import { spawn } from 'node:child_process';
import { existsSync } from 'node:fs';
import net from 'node:net';

const HOST = '0.0.0.0';

function getFreePort() {
  return new Promise((resolve, reject) => {
    const srv = net.createServer();
    srv.on('error', reject);
    srv.listen(0, HOST, () => {
      const { port } = srv.address();
      srv.close(err => err ? reject(err) : resolve(port));
    });
  });
}

const PORT = process.env.PORT ? Number(process.env.PORT) : await getFreePort();

if (!existsSync('public/build/manifest.json')) {
  console.log('[start] public/build missing, running vite build...');
  const build = spawn(process.platform === 'win32' ? 'cmd.exe' : 'npm', process.platform === 'win32' ? ['/c', 'npm run build'] : ['run', 'build'], { stdio: 'inherit', shell: false });
  await new Promise((resolve, reject) => { build.on('exit', c => c === 0 ? resolve() : reject(new Error('vite build failed'))); });
}

console.log(`[start] php artisan serve --host=${HOST} --port=${PORT}`);
const php = spawn('php', ['artisan', 'serve', `--host=${HOST}`, `--port=${PORT}`], { stdio: 'inherit', env: process.env });
php.on('error', err => { console.error('Failed to spawn php:', err.message); process.exit(1); });
php.on('exit', c => process.exit(c ?? 0));
