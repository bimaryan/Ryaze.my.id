/**
 * Ryaze Node Agent
 * -----------------------------------------------------------------------------
 * Agent mini (tanpa dependency) yang berjalan di dalam container runtime Node
 * (`ryaze-node`). Panel Laravel bertindak sebagai control plane dan hanya
 * berkomunikasi dengan proses Node user MELALUI agent ini — PHP tidak pernah
 * men-spawn proses Node secara langsung.
 *
 * Endpoint (semuanya wajib header `X-Agent-Token`):
 *   GET  /health                 — info agent (versi, mode pm2/spawn)
 *   POST /build                  — jalankan perintah build (npm install, dll)
 *   POST /start                  — start aplikasi user (PM2 atau detached spawn)
 *   POST /stop                   — stop aplikasi user
 *   POST /restart                — restart memakai parameter start terakhir
 *   GET  /status?project=        — status aplikasi (running, port, uptime, RAM)
 *   GET  /logs?project=&lines=   — tail log aplikasi (.app.log)
 *
 * File marker yang ditulis ke folder project (volume bersama dengan panel):
 *   .port          — port aplikasi, dibaca nginx Lua untuk routing
 *   .proxy-host    — IP container ini, dibaca nginx Lua untuk proxy_pass
 *   .app.log       — stdout/stderr aplikasi
 *   .app.json      — parameter start terakhir (untuk /restart)
 *   .node.pid      — PID (hanya mode spawn fallback)
 *   .ryaze-app.config.js — ecosystem PM2 (hanya mode pm2)
 */

'use strict';

const http = require('http');
const fs = require('fs');
const path = require('path');
const os = require('os');
const { spawn, spawnSync } = require('child_process');

// ── Konfigurasi ──────────────────────────────────────────────────────────────
const AGENT_VERSION = '1.0.0';
const AGENT_TOKEN = process.env.AGENT_TOKEN || '';
const AGENT_PORT = parseInt(process.env.AGENT_PORT || '4700', 10);
const CLIENTS_DIR = path.resolve(process.env.CLIENTS_DIR || '/www/sites/hosting_clients');
const PROXY_HOST_OVERRIDE = process.env.NODE_AGENT_PROXY_HOST || '';
const PORT_RANGES = (process.env.AGENT_PORT_RANGES || '3000-3400,8000-8100')
  .split(',')
  .map((r) => {
    const [a, b] = r.trim().split('-').map((n) => parseInt(n, 10));
    return [a, b];
  })
  .filter((r) => Number.isFinite(r[0]) && Number.isFinite(r[1]));

const PORT_FILE = '.port';
const PROXY_HOST_FILE = '.proxy-host';
const APP_LOG_FILE = '.app.log';
const APP_META_FILE = '.app.json';
const APP_PID_FILE = '.node.pid';
const PM2_FILE = '.ryaze-app.config.js';
const BUILD_LOG_FILE = '.build.log';
const PM2_NAME_PREFIX = 'app_';
const BUILD_TIMEOUT_MS = 15 * 60 * 1000; // 15 menit
const START_WAIT_MS = 30 * 1000;          // tunggu port listen maksimal 30 detik
const LOG_TAIL_MAX = 1000;

const HAS_PM2 = spawnSync('pm2', ['-v'], { timeout: 5000 }).status === 0;

// ── Utilitas ─────────────────────────────────────────────────────────────────
function send(res, status, obj) {
  const body = JSON.stringify(obj);
  res.writeHead(status, { 'Content-Type': 'application/json; charset=utf-8' });
  res.end(body);
}

function readBody(req) {
  return new Promise((resolve, reject) => {
    let data = '';
    let size = 0;
    req.on('data', (chunk) => {
      size += chunk.length;
      if (size > 1024 * 1024) { // 1MB
        reject(new Error('Body terlalu besar'));
        req.destroy();
        return;
      }
      data += chunk;
    });
    req.on('end', () => {
      if (!data) return resolve({});
      try { resolve(JSON.parse(data)); } catch (e) { reject(new Error('Body bukan JSON valid')); }
    });
    req.on('error', reject);
  });
}

/**
 * Validasi + resolve folder project. Menerima:
 *   - nama subdomain (mis. "toko")
 *   - path relatif ("toko/app")
 *   - path absolut SEBABAK di dalam CLIENTS_DIR (mis. "/www/sites/hosting_clients/toko"
 *     di Linux, atau "D:\...\hosting_clients\toko" di Windows dev) — panel memang
 *     mengirim path lengkap. Path di luar CLIENTS_DIR maupun ".." ditolak.
 */
function resolveProjectDir(input) {
  if (typeof input !== 'string' || !input.trim()) {
    throw new Error('Parameter project wajib diisi');
  }
  const raw = input.trim();
  const unified = raw.replace(/\\/g, '/'); // separator seragam untuk validasi segmen

  if (unified.includes('..')) {
    throw new Error('Path project tidak valid');
  }

  const isAbsolute = path.isAbsolute(raw) || /^[A-Za-z]:[\\/]/.test(raw);
  if (isAbsolute) {
    // Absolute: hapus leading slash duplikat lalu resolve apa adanya
    const resolved = path.resolve(raw);
    if (!isInsideClientsDir(resolved)) {
      throw new Error('Path project di luar direktori klien');
    }
    return requireExists(resolved);
  }

  const clean = unified.replace(/^\/+|\/+$/g, '');
  if (!clean) {
    throw new Error('Path project tidak valid');
  }
  const resolved = path.resolve(path.join(CLIENTS_DIR, clean));
  if (!isInsideClientsDir(resolved)) {
    throw new Error('Path project di luar direktori klien');
  }
  return requireExists(resolved);
}

/** Perbandingan prefix yang case-insensitive di Windows (filesystem NTFS-nya case-insensitive). */
function isInsideClientsDir(resolved) {
  const base = path.resolve(CLIENTS_DIR);
  const a = process.platform === 'win32' ? resolved.toLowerCase() : resolved;
  const b = process.platform === 'win32' ? base.toLowerCase() : base;
  return a !== b && a.startsWith(b + path.sep);
}

function requireExists(resolved) {
  if (!fs.existsSync(resolved) || !fs.statSync(resolved).isDirectory()) {
    throw new Error(`Folder project tidak ditemukan: ${path.basename(resolved)}`);
  }
  return resolved;
}

/** Tail file (baris terakhir) tanpa memuat seluruh file ke memori berlebihan. */
function tailFile(file, lines) {
  try {
    const content = fs.readFileSync(file, 'utf8');
    const parts = content.split('\n');
    return parts.slice(Math.max(0, parts.length - lines)).join('\n');
  } catch (e) {
    return '';
  }
}

function writeMarker(projectDir, name, value) {
  const file = path.join(projectDir, name);
  fs.writeFileSync(file, String(value), { mode: 0o644 });
}

function removeMarker(projectDir, name) {
  try { fs.unlinkSync(path.join(projectDir, name)); } catch (e) { /* abaikan */ }
}

function readMarker(projectDir, name) {
  try {
    return fs.readFileSync(path.join(projectDir, name), 'utf8').trim();
  } catch (e) {
    return '';
  }
}

function probePort(port) {
  const net = require('net');
  return new Promise((resolve) => {
    const socket = new net.Socket();
    socket.setTimeout(1500);
    socket.once('connect', () => { socket.destroy(); resolve(true); });
    socket.once('timeout', () => { socket.destroy(); resolve(false); });
    socket.once('error', () => { socket.destroy(); resolve(false); });
    socket.connect(port, '127.0.0.1');
  });
}

function pickFreePort() {
  const net = require('net');
  const ranges = [...PORT_RANGES].sort(() => Math.random() - 0.5);
  return new Promise((resolve, reject) => {
    const tryRange = (i) => {
      if (i >= ranges.length) return reject(new Error('Tidak ada port kosong tersedia'));
      const [min, max] = ranges[i];
      const port = Math.floor(Math.random() * (max - min + 1)) + min;
      const server = net.createServer();
      server.once('error', () => tryRange(i + 1));
      server.listen(port, '127.0.0.1', () => {
        server.close(() => resolve(port));
      });
    };
    tryRange(0);
  });
}

/** IP pertama (non-internal, IPv4) milik container ini — dipakai nginx untuk proxy. */
function detectProxyHost() {
  if (PROXY_HOST_OVERRIDE) return PROXY_HOST_OVERRIDE;
  const ifaces = os.networkInterfaces();
  for (const name of Object.keys(ifaces)) {
    for (const iface of ifaces[name]) {
      if (iface.family === 'IPv4' && !iface.internal && iface.address.startsWith('192.168.')) {
        return iface.address;
      }
    }
  }
  for (const name of Object.keys(ifaces)) {
    for (const iface of ifaces[name]) {
      if (iface.family === 'IPv4' && !iface.internal) {
        return iface.address;
      }
    }
  }
  return '127.0.0.1';
}

function execCommand(command, cwd, timeoutMs) {
  return new Promise((resolve) => {
    const child = spawn(command, { cwd, shell: true, timeout: timeoutMs });
    let output = '';
    child.stdout.on('data', (d) => { output += d.toString(); });
    child.stderr.on('data', (d) => { output += d.toString(); });
    child.on('error', (err) => { output += `\n${err.message}`; });
    child.on('close', (code) => resolve({ code, output }));
  });
}

/**
 * Deteksi entry file aplikasi, mengikuti pola yang sama dengan panel:
 * entry eksplisit → package.json main → scripts.start → auto-detect.
 * Mengembalikan { script, args } — script path file .js, args argumen tambahan.
 */
function resolveEntry(projectDir, explicitEntry) {
  const pkgPath = path.join(projectDir, 'package.json');

  const validRelative = (p) => {
    if (!p || p.includes('..')) return null;
    const full = path.resolve(projectDir, p);
    return fs.existsSync(full) && fs.statSync(full).isFile() ? p : null;
  };

  // 1. Entry eksplisit dari panel/user
  if (explicitEntry) {
    const found = validRelative(explicitEntry);
    if (!found) throw new Error(`Entry file tidak ditemukan: ${explicitEntry}`);
    return { script: found, args: [] };
  }

  // 2. package.json: scripts.start / main
  try {
    const pkg = JSON.parse(fs.readFileSync(pkgPath, 'utf8'));
    const startCmd = pkg.scripts && pkg.scripts.start;
    if (startCmd) {
      const tokens = startCmd.trim().split(/\s+/);
      // Pola "node [--flags...] entry.js" → jalankan langsung entry-nya
      if (tokens[0] === 'node' || tokens[0] === 'nodejs') {
        let idx = 1;
        while (idx < tokens.length && tokens[idx].startsWith('-')) idx++;
        const candidate = tokens[idx];
        const found = candidate && validRelative(candidate);
        if (found) return { script: found, args: tokens.slice(idx + 1) };
      }
      // Pola lain (npm run, cross-env, next start, dst) → PM2 bisa
      // menjalankannya via interpreter npm; mode spawn tidak bisa.
      if (HAS_PM2) {
        return { npmScript: 'start' };
      }
      throw new Error(
        `scripts.start ("${startCmd}") hanya bisa dijalankan dengan PM2. ` +
        'Install PM2 di container atau isi Entry File secara eksplisit di panel.',
      );
    }
    if (pkg.main) {
      const found = validRelative(pkg.main);
      if (found) return { script: found, args: [] };
    }
  } catch (err) {
    if (err.message.includes('PM2') || err.message.includes('Entry file')) throw err;
    // package.json tidak ada / tidak valid → lanjut auto-detect
  }

  // 3. Auto-detect
  for (const candidate of ['server.js', 'index.js', 'app.js', 'dist/server.js', 'src/index.js', 'src/server.js']) {
    const found = validRelative(candidate);
    if (found) return { script: found, args: [] };
  }

  throw new Error('Tidak menemukan entry file. Isi Entry File secara eksplisit di panel.');
}

function pm2AppName(projectDir) {
  const base = path.basename(projectDir);
  return PM2_NAME_PREFIX + base.replace(/[^a-zA-Z0-9_-]/g, '_');
}

async function waitPortListening(port) {
  const deadline = Date.now() + START_WAIT_MS;
  while (Date.now() < deadline) {
    if (await probePort(port)) return true;
    await new Promise((r) => setTimeout(r, 1000));
  }
  return false;
}

// ── Aksi: start / stop / status ──────────────────────────────────────────────
async function startApp(projectDir, params) {
  // Stop dulu kalau masih ada instance jalan (idempoten)
  await stopApp(projectDir, true);

  const entry = resolveEntry(projectDir, params.entry);
  const port = params.port || await pickFreePort();
  const env = {
    ...process.env,
    PORT: String(port),
    HOST: '0.0.0.0',
    NODE_ENV: params.node_env || 'production',
    ...(params.env_vars || {}),
  };
  // Env panel tidak boleh menimpa identitas agent
  delete env.AGENT_TOKEN;
  delete env.CLIENTS_DIR;

  const meta = {
    entry: entry.script || null,
    npm_script: entry.npmScript || null,
    args: entry.args || [],
    port,
    node_env: env.NODE_ENV,
    env_vars: params.env_vars || {},
    started_at: new Date().toISOString(),
  };
  writeMarker(projectDir, APP_META_FILE, JSON.stringify(meta, null, 2));

  let mode;
  if (HAS_PM2 && (entry.script || entry.npmScript)) {
    const name = pm2AppName(projectDir);
    const appConfig = {
      name,
      cwd: projectDir,
      max_memory_restart: process.env.AGENT_MAX_MEMORY || '512M',
      autorestart: true,
      out_file: path.join(projectDir, APP_LOG_FILE),
      error_file: path.join(projectDir, APP_LOG_FILE),
      merge_logs: true,
      env,
    };
    if (entry.npmScript) {
      appConfig.script = 'npm';
      appConfig.args = ['run', entry.npmScript];
    } else {
      appConfig.script = path.join(projectDir, entry.script);
      appConfig.args = entry.args;
    }

    fs.writeFileSync(
      path.join(projectDir, PM2_FILE),
      'module.exports = {\n  apps: ' + JSON.stringify([appConfig], null, 2) + '\n};\n',
    );

    // Bersihkan instance lama dengan nama sama agar tidak menumpuk
    await execCommand(`pm2 delete ${name} 2>/dev/null || true`, projectDir, 15000);
    const { code, output } = await execCommand(`pm2 start ${PM2_FILE}`, projectDir, 30000);
    if (code !== 0) {
      return { ok: false, error: `PM2 start gagal: ${output.slice(-1500)}` };
    }
    mode = 'pm2';
  } else {
    // Fallback tanpa PM2: detached spawn dalam process group sendiri
    const logFd = fs.openSync(path.join(projectDir, APP_LOG_FILE), 'a');
    let cmd, args;
    if (entry.npmScript) {
      cmd = process.platform === 'win32' ? 'npm.cmd' : 'npm';
      args = ['run', entry.npmScript];
    } else {
      cmd = process.execPath;
      args = [path.join(projectDir, entry.script), ...(entry.args || [])];
    }
    const child = spawn(cmd, args, {
      cwd: projectDir,
      env,
      detached: true,
      stdio: ['ignore', logFd, logFd],
    });
    child.unref();
    fs.closeSync(logFd);
    writeMarker(projectDir, APP_PID_FILE, String(child.pid));
    mode = 'spawn';
  }

  const listening = await waitPortListening(port);
  writeMarker(projectDir, PORT_FILE, String(port));
  writeMarker(projectDir, PROXY_HOST_FILE, detectProxyHost());

  return {
    ok: true,
    mode,
    entry: entry.script || entry.npmScript,
    port,
    proxy_host: detectProxyHost(),
    listening,
    ...(listening ? {} : { warning: `Aplikasi ter-start tapi port ${port} belum merespons dalam ${START_WAIT_MS / 1000}s — cek log aplikasi.` }),
  };
}

async function stopApp(projectDir, silent = false) {
  const name = pm2AppName(projectDir);
  if (HAS_PM2) {
    await execCommand(`pm2 delete ${name} 2>/dev/null || true`, projectDir, 15000);
  }
  const pid = readMarker(projectDir, APP_PID_FILE);
  if (pid && /^\d+$/.test(pid)) {
    try {
      // detached spawn → group kill mematikan proses + turunannya
      if (process.platform !== 'win32') {
        process.kill(-parseInt(pid, 10), 'SIGKILL');
      } else {
        process.kill(parseInt(pid, 10), 'SIGKILL');
      }
    } catch (e) { /* proses sudah mati */ }
  }
  removeMarker(projectDir, APP_PID_FILE);
  removeMarker(projectDir, PORT_FILE);
  removeMarker(projectDir, PROXY_HOST_FILE);
  removeMarker(projectDir, APP_META_FILE);
  return { ok: true, silent };
}

async function statusApp(projectDir) {
  const port = readMarker(projectDir, PORT_FILE);
  const proxyHost = readMarker(projectDir, PROXY_HOST_FILE) || detectProxyHost();
  const metaRaw = readMarker(projectDir, APP_META_FILE);
  let meta = {};
  try { meta = JSON.parse(metaRaw || '{}'); } catch (e) { /* abaikan */ }

  let running = false;
  let mode = HAS_PM2 ? 'pm2' : 'spawn';
  let memory = null;
  let uptime = null;

  if (port && /^\d+$/.test(port)) {
    running = await probePort(parseInt(port, 10));
  }

  if (HAS_PM2) {
    try {
      const { output } = await execCommand('pm2 jlist', projectDir, 15000);
      const list = JSON.parse(output || '[]');
      const app = list.find((p) => p.name === pm2AppName(projectDir));
      if (app) {
        mode = 'pm2';
        running = running || (app.pm2_env && app.pm2_env.status === 'online');
        memory = app.monit ? app.monit.memory : null;
        uptime = app.pm2_env && app.pm2_env.pm_uptime ? Date.now() - app.pm2_env.pm_uptime : null;
      }
    } catch (e) { /* abaikan */ }
  } else {
    const pid = readMarker(projectDir, APP_PID_FILE);
    if (pid && /^\d+$/.test(pid)) {
      try { process.kill(parseInt(pid, 10), 0); running = running || true; } catch (e) { /* mati */ }
    }
  }

  return {
    ok: true,
    running,
    mode,
    port: port ? parseInt(port, 10) : null,
    proxy_host: proxyHost,
    entry: meta.entry || null,
    node_env: meta.node_env || null,
    env_vars: meta.env_vars || {},
    memory,
    uptime,
  };
}

// ── Routing ──────────────────────────────────────────────────────────────────
const server = http.createServer(async (req, res) => {
  const url = new URL(req.url, `http://127.0.0.1:${AGENT_PORT}`);
  const route = url.pathname.replace(/\/+$/, '') || '/';

  // Health check terbuka (untuk docker healthcheck, tanpa rahasia)
  if (route === '/health' && req.method === 'GET') {
    return send(res, 200, { ok: true, version: AGENT_VERSION, pm2: HAS_PM2, clients_dir: CLIENTS_DIR });
  }

  if (!AGENT_TOKEN) {
    return send(res, 503, { ok: false, error: 'AGENT_TOKEN belum di-set — agent menolak semua perintah' });
  }
  if (req.headers['x-agent-token'] !== AGENT_TOKEN) {
    return send(res, 401, { ok: false, error: 'Token agent tidak valid' });
  }

  try {
    // ── POST /build ──
    if (route === '/build' && req.method === 'POST') {
      const body = await readBody(req);
      const projectDir = resolveProjectDir(body.project);
      const commands = Array.isArray(body.commands) && body.commands.length
        ? body.commands
        : ['npm install --legacy-peer-deps'];
      const allowed = /^(npm|npx|node|yarn|pnpm)\b/;
      let output = '';
      let failed = false;
      for (const command of commands) {
        if (typeof command !== 'string' || !allowed.test(command.trim())) {
          return send(res, 400, { ok: false, error: `Perintah tidak diizinkan: ${command}` });
        }
        output += `\n$ ${command}\n`;
        const result = await execCommand(command, projectDir, BUILD_TIMEOUT_MS);
        output += result.output;
        if (result.code !== 0) { failed = true; break; }
      }
      fs.writeFileSync(path.join(projectDir, BUILD_LOG_FILE), output.slice(-200000), { mode: 0o644 });
      return send(res, failed ? 500 : 200, {
        ok: !failed,
        ...(failed ? { error: 'Build gagal' } : {}),
        output: output.slice(-8000),
      });
    }

    // ── POST /start ──
    if (route === '/start' && req.method === 'POST') {
      const body = await readBody(req);
      const projectDir = resolveProjectDir(body.project);
      const result = await startApp(projectDir, {
        entry: body.entry,
        port: body.port,
        env_vars: body.env_vars,
        node_env: body.node_env,
      });
      return send(res, result.ok ? 200 : 500, result);
    }

    // ── POST /stop ──
    if (route === '/stop' && req.method === 'POST') {
      const body = await readBody(req);
      const projectDir = resolveProjectDir(body.project);
      return send(res, 200, await stopApp(projectDir));
    }

    // ── POST /restart ──
    if (route === '/restart' && req.method === 'POST') {
      const body = await readBody(req);
      const projectDir = resolveProjectDir(body.project);
      let params = {};
      try { params = JSON.parse(readMarker(projectDir, APP_META_FILE) || '{}'); } catch (e) { /* kosong */ }
      const result = await startApp(projectDir, {
        entry: body.entry || params.entry,
        port: body.port || params.port,
        env_vars: body.env_vars || params.env_vars,
        node_env: body.node_env || params.node_env,
      });
      return send(res, result.ok ? 200 : 500, result);
    }

    // ── GET /status ──
    if (route === '/status' && req.method === 'GET') {
      const projectDir = resolveProjectDir(url.searchParams.get('project'));
      return send(res, 200, await statusApp(projectDir));
    }

    // ── GET /logs ──
    if (route === '/logs' && req.method === 'GET') {
      const projectDir = resolveProjectDir(url.searchParams.get('project'));
      const lines = Math.min(parseInt(url.searchParams.get('lines') || '200', 10) || 200, LOG_TAIL_MAX);
      return send(res, 200, { ok: true, logs: tailFile(path.join(projectDir, APP_LOG_FILE), lines) });
    }

    return send(res, 404, { ok: false, error: 'Endpoint tidak dikenal' });
  } catch (err) {
    return send(res, err.message.includes('tidak ditemukan') || err.message.includes('tidak valid') ? 404 : 500, {
      ok: false,
      error: err.message,
    });
  }
});

server.listen(AGENT_PORT, '0.0.0.0', () => {
  console.log(`[ryaze-node-agent] v${AGENT_VERSION} listening on :${AGENT_PORT}`);
  console.log(`[ryaze-node-agent] CLIENTS_DIR = ${CLIENTS_DIR}`);
  console.log(`[ryaze-node-agent] mode = ${HAS_PM2 ? 'pm2' : 'spawn (fallback)'}`);
});
