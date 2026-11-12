<?php

namespace App\Services;

use App\Models\HostingProject;
use Illuminate\Support\Facades\Log;

/**
 * Mengelola server Node.js milik klien di shared hosting (SSR/API).
 *
 * Prinsip: deploy HANYA build; server dijalankan klien lewat web terminal
 * dengan command helper ryaze-start / ryaze-stop / ryaze-status / ryaze-log.
 * Server dibuat detached (orphan → reparent ke init) dengan output ke .app.log
 * sehingga tetap hidup setelah perintah terminal selesai.
 *
 * Nginx melayani project ini lewat file .port (dibaca Lua worker) + .proxy-host
 * (IP container target proxy). Keduanya ditulis oleh service ini saat server hidup.
 */
class NodeServerService
{
    /**
     * Rentang port acak yang diizinkan (di luar port standar platform & web).
     */
    public const PORT_RANGES = [
        [3000, 3400],
        [8000, 8100],
    ];

    /**
     * Nama file penanda server berjalan (di root project klien).
     */
    public const PORT_FILE = '.port';

    public const PID_FILE = '.node.pid';

    public const LOG_FILE = '.app.log';

    public const PROXY_HOST_FILE = '.proxy-host';

    private bool $isLinux;

    public function __construct()
    {
        $this->isLinux = strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN';
    }

    /**
     * Pilih port acak yang bebas (acak urutan agar tiap start port berbeda,
     * membantu bypass DNS negative caching di beberapa resolver ISP).
     */
    public function pickFreePort(): ?int
    {
        $candidates = [];
        foreach (self::PORT_RANGES as [$min, $max]) {
            for ($p = $min; $p <= $max; $p++) {
                $candidates[] = $p;
            }
        }
        shuffle($candidates);

        foreach ($candidates as $p) {
            if (! $this->isPortListening('127.0.0.1', $p)) {
                return $p;
            }
        }

        return null;
    }

    public function isPortListening(string $host, int $port): bool
    {
        $conn = @fsockopen($host, $port, $errCode, $errStr, 0.2);
        if (is_resource($conn)) {
            fclose($conn);

            return true;
        }

        return false;
    }

    /**
     * Jalankan server Node project klien (detached) pada $port.
     *
     * @param  string  $projectDir  path absolut project klien
     * @param  string|null  $entryArg  argumen user (nama file entry, tanpa path)
     * @param  array  $log  output baris-baris perintah (by reference)
     * @param  array|null  $result  hasil: ['port'=>int,'pid'=>string|null]
     */
    public function start(HostingProject $project, string $projectDir, ?string $entryArg, int $port, array &$log, ?array &$result): bool
    {
        $command = $this->resolveStartCommand($projectDir, $entryArg, $log);
        if ($command === null) {
            return false;
        }

        $logPath = "{$projectDir}/".self::LOG_FILE;
        @file_put_contents($logPath, ''); // truncate log lama

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['file', $logPath, 'a'],
            2 => ['file', $logPath, 'a'],
        ];
        $env = $this->buildEnv($port);

        $proc = proc_open($command, $descriptors, $pipes, $projectDir, $env);
        if (! is_resource($proc)) {
            $log[] = '⛔ Gagal menjalankan proses server.';

            return false;
        }
        fclose($pipes[0]);

        $status = proc_get_status($proc);
        $pid = (string) ($status['pid'] ?? '');

        // Node startup bisa lama (SSR besar); tunggu port terbuka ±25 detik.
        $listening = false;
        for ($i = 0; $i < 50; $i++) {
            if ($this->isPortListening('127.0.0.1', $port)) {
                $listening = true;
                break;
            }
            usleep(500_000);
        }

        if (! $listening) {
            proc_terminate($proc, 9);
            proc_close($proc);
            $log[] = '⛔ Server gagal listen di port '.$port.' dalam 25 detik. Terminate otomatis.';
            $log[] = '─ Log (tail):';
            foreach ($this->tailLines($logPath, 30) as $line) {
                $log[] = '  '.$line;
            }

            return false;
        }

        // Detached: proses TIDAK di-'proc_close/wait' — proc_close akan
        // menunggu child selesai dan menggantung PHP-FPM karena server Node
        // jalan terus. Resource dilepas otomatis di akhir request; child
        // orphan → reparent ke init dan tetap hidup (semantik nohup,
        // stdout/stderr sudah file, stdin pipe ditutup).
        unset($proc);

        file_put_contents($projectDir.'/'.self::PID_FILE, $pid);
        $this->writeProxyFiles($projectDir, $port);

        $project->update([
            'dev_mode' => true,
            'dev_port' => $port,
            'dev_pid' => $pid,
        ]);

        $result = ['port' => $port, 'pid' => $pid];

        return true;
    }

    /**
     * Matikan semua proses server Node milik project klien + bersihkan file proxy.
     */
    public function stop(HostingProject $project, string $projectDir, array &$log, ?array &$result): bool
    {
        $result = ['killed' => [], 'port' => null];

        $portFile = $projectDir.'/'.self::PORT_FILE;
        if (file_exists($portFile)) {
            $result['port'] = (int) trim((string) @file_get_contents($portFile));
        }

        if (! $this->isLinux) {
            $log[] = '[SKIP-WIN] Stop server hanya tersedia di Linux.';
        } else {
            $pids = $this->findServerPids($projectDir, $log);
            foreach ($pids as $pid) {
                if (function_exists('posix_kill')) {
                    @posix_kill($pid, 9);
                } else {
                    shell_exec('kill -9 '.(int) $pid.' >/dev/null 2>&1');
                }
                $result['killed'][] = $pid;
            }
            if (! empty($result['killed'])) {
                usleep(300_000);
            }
        }

        foreach ([self::PORT_FILE, self::PID_FILE, self::PROXY_HOST_FILE, self::LOG_FILE] as $f) {
            if (file_exists($projectDir.'/'.$f)) {
                $this->safeUnlink($projectDir.'/'.$f);
            }
        }

        $project->update([
            'dev_mode' => false,
            'dev_port' => null,
            'dev_pid' => null,
        ]);

        if (empty($result['killed']) && ! $result['port']) {
            $log[] = 'Tidak ada server berjalan untuk project ini.';

            return false;
        }

        return true;
    }

    /**
     * Informasi status server milik project klien.
     */
    public function status(HostingProject $project, string $projectDir, array &$log, ?array &$result): bool
    {
        $result = ['port' => null, 'running' => false, 'pids' => []];

        $portFile = $projectDir.'/'.self::PORT_FILE;
        $port = file_exists($portFile) ? (int) trim((string) @file_get_contents($portFile)) : null;
        if ($port) {
            $result['port'] = $port;
        }

        if ($this->isLinux) {
            $result['pids'] = $this->findServerPids($projectDir, $log);
        }

        if ($port && $this->isPortListening('127.0.0.1', $port)) {
            $result['running'] = true;
        }

        return $result['running'];
    }

    /**
     * Tail N baris terakhir log server.
     */
    public function tailLines(string $logPath, int $lines): array
    {
        if (! file_exists($logPath)) {
            return ['(log kosong — server belum pernah dijalankan)'];
        }
        $content = (string) @file_get_contents($logPath);
        if (trim($content) === '') {
            return ['(log masih kosong — tunggu beberapa detik jika server baru saja start)'];
        }
        $all = explode("\n", $content);

        return array_slice($all, -$lines);
    }

    // ── Internal ──────────────────────────────────────────────────────────

    private function resolveStartCommand(string $projectDir, ?string $entryArg, array &$log): ?array
    {
        // 1. Ekosistem PM2 (config eksplisit milik user)
        if (file_exists("{$projectDir}/ecosystem.config.js")) {
            $log[] = '> Entry: ecosystem.config.js (via PM2)';

            return ['npx', '-y', 'pm2', 'start', 'ecosystem.config.js'];
        }

        // 2. Argumen eksplisit user — relative path di dalam project (boleh subfolder)
        if ($entryArg !== null && $entryArg !== '') {
            $target = $projectDir.'/'.$entryArg;
            $real = realpath($target);
            $real = $real === false ? false : str_replace('\\', '/', $real);
            $base = rtrim(str_replace('\\', '/', $projectDir), '/');
            if ($real === false || ! str_starts_with($real, $base.'/') || is_dir($target)) {
                $log[] = "⛔ File entry '{$entryArg}' tidak ditemukan / di luar root project.";

                return null;
            }

            return ['node', $entryArg];
        }

        // 3. package.json → scripts.start / main
        $pkgPath = "{$projectDir}/package.json";
        if (is_readable($pkgPath)) {
            $pkg = json_decode((string) @file_get_contents($pkgPath), true);
            if (is_array($pkg)) {
                if (! empty($pkg['scripts']['start'])) {
                    $log[] = "> Entry: npm start (package.json scripts.start = {$pkg['scripts']['start']})";

                    return ['npm', 'start'];
                }
                if (! empty($pkg['main']) && file_exists("{$projectDir}/{$pkg['main']}")) {
                    $log[] = "> Entry: node {$pkg['main']} (package.json main)";

                    return ['node', $pkg['main']];
                }
            }
        }

        // 4. Deteksi umum
        foreach (['server.js', 'index.js', 'app.js', 'server.mjs', 'index.mjs'] as $guess) {
            if (file_exists("{$projectDir}/{$guess}")) {
                $log[] = "> Entry: node {$guess} (auto-detect)";

                return ['node', $guess];
            }
        }

        $log[] = '⛔ Tidak ada entry point yang terdeteksi. Jalankan dengan argumen: ryaze-start namafile.js, atau pastikan package.json punya scripts.start.';

        return null;
    }

    /**
     * Environment bersih untuk proses Node klien:
     * - kredensial platform dihapus (client TIDAK boleh membaca env PHP platform)
     * - dibekali PORT/HOST agar server bind di 0.0.0.0
     *
     * Semantik sama dengan terminalEnv() di DashboardController (daftar identik).
     */
    private function buildEnv(int $port): array
    {
        $env = getenv();
        if (! is_array($env)) {
            $env = [];
        }

        $sensitive = [
            'APP_KEY', 'APP_ENV', 'APP_DEBUG',
            'DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE',
            'DB_USERNAME', 'DB_PASSWORD', 'DATABASE_URL',
            'REDIS_CLIENT', 'REDIS_PASSWORD', 'REDIS_HOST', 'REDIS_PORT',
            'BROADCAST_DRIVER', 'CACHE_DRIVER', 'QUEUE_CONNECTION',
            'SESSION_DRIVER', 'LOG_CHANNEL',
            'HASHIDS_SALT', 'PAYMENT_API_KEY', 'PAYMENT_MERCHANT_ID',
            'PAKASIR_API_KEY', 'TELEGRAM_BOT_TOKEN', 'CF_API_TOKEN',
        ];
        foreach ($sensitive as $key) {
            unset($env[$key]);
        }

        $env['PORT'] = (string) $port;
        $env['HOST'] = '0.0.0.0';
        $env['HOSTNAME'] = '0.0.0.0'; // Next.js membaca HOSTNAME

        // Buang variabel port/platform milik shell agar tidak menimpa
        // PORT yangkami tentukan (beberapa framework membaca ini juga).
        unset($env['VITE_PORT']);

        return $env;
    }

    /**
     * Tulis .port + .proxy-host untuk Lua/Jinja worker OpenResty.
     *
     * Logic deteksi host sama dengan writeProxyHostFile() versi AutoDeploy
     * (dipindah ke sini supaya bisa dipanggil saat start manual lewat terminal):
     * manual override > docker inspect <container> > hostname -i > /etc/hosts.
     */
    public function detectProxyHost(string $projectDir): string
    {
        $overrideFile = $projectDir.'/'.self::PROXY_HOST_FILE;
        if (file_exists($overrideFile)) { // note: nilai .proxy-host lama jadi override manual
            $override = trim((string) @file_get_contents($overrideFile));
            if ($override !== '' && $override !== '127.0.0.1') {
                return $override;
            }
        }

        $host = '127.0.0.1';
        if (! $this->isLinux) {
            return $host;
        }

        $container = config('services.php_container', '1Panel-php8-aJQI');
        $candidates = [];

        $out = trim((string) @shell_exec("getent hosts {$container} 2>/dev/null | awk '{print \$1}'"));
        if ($out !== '') {
            $candidates[] = $out;
        }

        $out = trim((string) @shell_exec('hostname -i 2>/dev/null'));
        if ($out !== '') {
            foreach (preg_split('/\s+/', $out) as $ip) {
                if ($ip !== '') {
                    $candidates[] = $ip;
                }
            }
        }

        if (file_exists('/etc/hosts')) {
            foreach (file('/etc/hosts', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if (str_contains($line, $container)) {
                    $parts = preg_split('/\s+/', trim($line));
                    if (! empty($parts[0])) {
                        $candidates[] = $parts[0];
                    }
                    break;
                }
            }
        }

        foreach (
            [
                "docker inspect --format '{{range .NetworkSettings.Networks}}{{.IPAddress}} {{end}}' {$container} 2>/dev/null",
                "docker inspect --format '{{.NetworkSettings.IPAddress}}' {$container} 2>/dev/null",
            ] as $cmd
        ) {
            $out = trim((string) @shell_exec($cmd));
            if ($out !== '') {
                foreach (preg_split('/\s+/', $out) as $ip) {
                    if ($ip !== '') {
                        $candidates[] = $ip;
                    }
                }
            }
        }

        // pilih IPv4 privat pertama (10.x / 172.16-31.x / 192.168.x)
        foreach ($candidates as $ip) {
            $ip = trim($ip);
            if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                continue;
            }
            if (str_starts_with($ip, '127.')) {
                continue;
            }
            $octets = explode('.', $ip);
            $isPrivate = (str_starts_with($ip, '10.'))
                || (str_starts_with($ip, '172.') && (int) $octets[1] >= 16 && (int) $octets[1] <= 31)
                || (str_starts_with($ip, '192.168.'));
            if ($isPrivate) {
                $host = $ip;
                break;
            }
        }

        return $host;
    }

    private function writeProxyFiles(string $projectDir, int $port): void
    {
        $host = $this->detectProxyHost($projectDir);

        @file_put_contents($projectDir.'/'.self::PORT_FILE, (string) $port);
        @file_put_contents($projectDir.'/'.self::PROXY_HOST_FILE, $host);

        if ($this->isLinux) {
            $user = config('services.php_user', 'www-data');
            foreach ([self::PORT_FILE, self::PROXY_HOST_FILE, self::PID_FILE, self::LOG_FILE] as $f) {
                shell_exec('chown '.$user.' '.escapeshellarg($projectDir.'/'.$f).' >/dev/null 2>&1');
                @chmod($projectDir.'/'.$f, 0644);
            }
        }

        if ($host === '127.0.0.1') {
            Log::warning("[NodeServer] Proxy host fallback 127.0.0.1 untuk {$projectDir} — kalau nginx di container terpisah, web akan 502. Set .proxy-host container PHP IP manual.");
        }
    }

    /**
     * Cari PID proses milik project klien.
     *
     * Heuristik aman: /proc/<pid>/cwd ada di dalam projectDir ATAU cmdline
     * memuat path projectDir. Proses PHP/Laravel sendiri selalu di-skip.
     */
    private function findServerPids(string $projectDir, array &$log): array
    {
        $procDir = '/proc';
        if (! is_dir($procDir)) {
            return [];
        }

        $self = getmypid();
        $pids = [];
        $base = rtrim($projectDir, '/');

        foreach (scandir($procDir) ?: [] as $entry) {
            if (! ctype_digit($entry) || (int) $entry === $self) {
                continue;
            }
            $dir = $procDir.'/'.$entry;

            $cwd = @readlink($dir.'/cwd');
            $cmdline = @file_get_contents($dir.'/cmdline');
            $cmdArgs = $cmdline ? explode("\0", trim($cmdline, "\0")) : [];
            $cmdStr = implode(' ', $cmdArgs);

            // PM2 daemon milik user (cwd beranda) — skip agar tidak mati semua
            // server lain; pm2 process_id di atasnya kita biarkan (child tetap
            // di-kill via cwd/cmdline matching).
            $inDir = $cwd !== '' && (str_starts_with($cwd, $base.'/') || $cwd === $base);
            if ($inDir) {
                $pids[] = (int) $entry;
                $log[] = '  PID '.$entry.': '.$cmdStr;
            }
        }

        return $pids;
    }

    private function safeUnlink(string $path): void
    {
        if ($this->isLinux) {
            shell_exec('rm -f '.escapeshellarg($path).' >/dev/null 2>&1');
        }
        @unlink($path);
    }
}
