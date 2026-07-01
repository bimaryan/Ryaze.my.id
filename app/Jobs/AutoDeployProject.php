<?php

namespace App\Jobs;

use App\Models\HostingProject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AutoDeployProject implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public HostingProject $project;

    /** Jangan retry — deploy ulang harus manual via redeploy button. */
    public int $tries = 1;

    /** Timeout maksimum job (detik). Build NPM + composer bisa lama. */
    public int $timeout = 900;

    public function __construct(HostingProject $project)
    {
        $this->project = $project;
    }

    public function handle(): void
    {
        $deploy = $this->project->deployments()->latest()->first();

        if (! $deploy) {
            Log::error("[AutoDeploy] Tidak ada deployment record untuk project #{$this->project->id}");
            $this->project->update(['status' => 'error']);

            return;
        }

        $deploy->update(['status' => 'building', 'build_logs' => '']);

        $baseDir = '/www/sites/hosting_clients';
        $subdomain = str_replace('.ryaze.my.id', '', $this->project->ryaze_domain);
        $projectDir = $baseDir.'/'.$subdomain;

        try {
            // ----------------------------------------------------------------
            // TAHAP 1: Persiapan Direktori
            // ----------------------------------------------------------------
            $this->log($deploy, '> Preparing deployment directory...');
            $this->exec("mkdir -p {$baseDir}", $deploy);

            // ----------------------------------------------------------------
            // TAHAP 2: Sumber File — Git Clone/Pull atau Template Scaffold
            // ----------------------------------------------------------------
            // Fallback check: jika repo_source mulai dengan 'template:', kita anggap sebagai template
            $isTemplate = ($this->project->source_type === 'template') || str_starts_with($this->project->repo_source, 'template:');
            if ($isTemplate) {
                // ── MODE TEMPLATE ────────────────────────────────────────────
                // Tidak ada git sama sekali. File dibuat langsung dari PHP.
                $templateKey    = str_replace('template:', '', $this->project->repo_source);
                $markerFile     = "{$projectDir}/.ryaze-template";
                $alreadyScaffolded = file_exists($markerFile);

                if ($alreadyScaffolded) {
                    // Redeploy: user sudah bisa edit file, preserve semua perubahan
                    $this->log($deploy, "\n> [TEMPLATE] Source: {$templateKey}");
                    $this->log($deploy, '> Template already scaffolded. Preserving user files...');
                    $this->log($deploy, '> Re-running build step only...');
                } else {
                    // Deploy pertama (atau reset): bersihkan lalu generate file
                    $this->log($deploy, "\n> [TEMPLATE] Scaffolding: {$templateKey}");
                    $this->log($deploy, '> Generating starter files directly on server (no git required)...');

                    // Bersihkan direktori lama jika ada (dari deploy gagal dll.)
                    if (is_dir($projectDir)) {
                        $this->exec("rm -rf {$projectDir}", $deploy);
                    }
                    $this->exec("mkdir -p {$projectDir}", $deploy);

                    // Generate file starter
                    $this->scaffoldTemplate($templateKey, $projectDir, $deploy);

                    // Tulis marker agar redeploy berikutnya tidak overwrite file user
                    file_put_contents($markerFile, json_encode([
                        'template'    => $templateKey,
                        'scaffolded'  => now()->toISOString(),
                        'project_id'  => $this->project->id,
                    ]));

                    $this->log($deploy, "> Template files ready in: {$projectDir}");
                }
            } else {
                // ── MODE REPOSITORY ──────────────────────────────────────────
                $this->log($deploy, "\n> Checking repository status...");

                // Paksa git selalu pakai HTTPS (queue worker tidak punya SSH/terminal)
                $this->exec("git config --global url.'https://github.com/'.insteadOf 'git@github.com:'", $deploy);
                $this->exec("git config --global url.'https://gitlab.com/'.insteadOf 'git@gitlab.com:'", $deploy);
                $this->exec("git config --global url.'https://bitbucket.org/'.insteadOf 'git@bitbucket.org:'", $deploy);
                $this->exec("git config --global core.askPass ''", $deploy);
                $this->exec("git config --global --add safe.directory '*'", $deploy);

                // Konversi SSH URL ke HTTPS jika user input format git@
                $repoUrl = $this->project->repo_source;
                if (preg_match('/^git@([^:]+):(.+?)(?:\.git)?$/', $repoUrl, $m)) {
                    $repoUrl = "https://{$m[1]}/{$m[2]}.git";
                    $this->log($deploy, "> [INFO] Converted SSH URL to HTTPS: {$repoUrl}");
                }

                $isRepo = is_dir("{$projectDir}/.git");

                if ($isRepo) {
                    $this->log($deploy, '> Repository found. Pulling latest changes...');
                    $this->exec("chown -R root:root {$projectDir}", $deploy);
                    $this->exec(
                        "cd {$projectDir} && git fetch --all && git reset --hard origin/{$this->project->branch}",
                        $deploy,
                        true
                    );
                } else {
                    if (is_dir($projectDir)) {
                        $this->log($deploy, '> Found stale directory (not a git repo). Cleaning up...');
                        $this->exec("rm -rf {$projectDir}", $deploy);
                    }

                    $this->log($deploy, '> Cloning repository...');
                    $this->exec(
                        "GIT_TERMINAL_PROMPT=0 git clone -b {$this->project->branch} {$repoUrl} {$projectDir}",
                        $deploy,
                        true
                    );
                }
            }

            // Kembalikan ke www-data sebelum setup framework
            $this->exec("chown -R www-data:www-data {$projectDir} && chmod -R 775 {$projectDir}", $deploy);

            // ----------------------------------------------------------------
            // TAHAP 3: Setup Framework
            // ----------------------------------------------------------------
            $framework = strtolower($this->project->framework);
            $this->log($deploy, "\n> Setting up ".strtoupper($framework).' environment...');

            match ($framework) {
                'react', 'nextjs', 'vue', 'node' => $this->setupNodeFramework($deploy, $projectDir, $framework),
                'laravel' => $this->setupLaravel($deploy, $projectDir),
                'python' => $this->setupPython($deploy, $projectDir),
                'html', 'php' => $this->log($deploy, "> Native/Static project ({$framework}). No build step required."),
                default => $this->log($deploy, "> [WARNING] Framework '{$framework}' tidak dikenali. Melewati build step."),
            };

            // ----------------------------------------------------------------
            // TAHAP 4: Final Permissions Fix (Sapu Bersih Permission)
            // ----------------------------------------------------------------
            $this->log($deploy, "\n> Applying final permissions to all generated files...");
            $this->exec("chown -R www-data:www-data {$projectDir} 2>/dev/null || true", $deploy);
            $this->exec("chmod -R 775 {$projectDir} 2>/dev/null || true", $deploy);

            if ($framework === 'laravel') {
                $this->exec("chmod -R 777 {$projectDir}/storage {$projectDir}/bootstrap/cache 2>/dev/null || true", $deploy);
            }

            // ----------------------------------------------------------------
            // TAHAP 5: Cloudflare DNS
            // ----------------------------------------------------------------
            $this->log($deploy, "\n> Configuring Cloudflare DNS for {$this->project->ryaze_domain}...");
            if (!$this->createCloudflareDNS($deploy)) {
                throw new \RuntimeException('Gagal mengkonfigurasi Cloudflare DNS. Periksa API Token atau pengaturan Zone ID.');
            }

            // ----------------------------------------------------------------
            // SELESAI
            // ----------------------------------------------------------------
            $this->log($deploy, "\n> [SUCCESS] Deployment finished!\n> Live at: https://{$this->project->ryaze_domain}");

            $deploy->update([
                'status' => 'ready',
                'deployed_at' => now(),
            ]);
            $this->project->update(['status' => 'active']);

        } catch (\Throwable $e) {
            $message = $e->getMessage();
            $this->log($deploy, "\n> [FATAL ERROR] {$message}");
            Log::error("[AutoDeploy] Project #{$this->project->id} failed: {$message}", [
                'exception' => $e,
            ]);
            $this->markAsFailed($deploy);
        }
    }

    // =========================================================================
    // FRAMEWORK HANDLERS
    // =========================================================================

    private function setupNodeFramework($deploy, string $projectDir, string $framework): void
    {
        $isLaravelInertia = file_exists("{$projectDir}/artisan")
            && file_exists("{$projectDir}/composer.json")
            && file_exists("{$projectDir}/package.json");

        if ($isLaravelInertia) {
            $this->log($deploy, '> Laravel+Inertia detected. Running composer install first...');
            $this->runComposerInstall($deploy, $projectDir);
            $this->setupLaravelEnv($deploy, $projectDir);
        }

        $this->log($deploy, '> Installing NPM dependencies...');
        $this->exec(
            "cd {$projectDir} && /usr/bin/npm install --legacy-peer-deps",
            $deploy,
            true
        );

        if (in_array($framework, ['react', 'nextjs', 'vue'])) {
            $this->log($deploy, '> Running build script...');
            $this->exec(
                "cd {$projectDir} && /usr/bin/npm run build",
                $deploy,
                true
            );

            if ($isLaravelInertia) {
                $this->log($deploy, '> Laravel+Inertia: Vite output is at public/build. No file move needed.');
                $this->runLaravelPostSetup($deploy, $projectDir);
            } else {
                $this->log($deploy, '> Organizing build output...');
                $this->moveBuiltOutput($deploy, $projectDir);
            }
        }
    }

    private function setupLaravel($deploy, string $projectDir): void
    {
        $this->runComposerInstall($deploy, $projectDir);
        $this->setupLaravelEnv($deploy, $projectDir);
        $this->runLaravelPostSetup($deploy, $projectDir);
        $this->log($deploy, '> Laravel setup complete.');
    }

    /**
     * Setup .env dan APP_KEY untuk project Laravel.
     */
    private function setupLaravelEnv($deploy, string $projectDir): void
    {
        $envPath = "{$projectDir}/.env";
        $envExamplePath = "{$projectDir}/.env.example";

        // ── 1. Pastikan .env ada ──────────────────────────────────────────────
        if (! file_exists($envPath)) {
            if (file_exists($envExamplePath)) {
                $this->log($deploy, '> Creating .env from .env.example...');
                $this->exec("cp {$envExamplePath} {$envPath} && chmod 666 {$envPath}", $deploy, true);
            } else {
                $this->log($deploy, '> [WARNING] .env.example tidak ada. Membuat .env minimal...');
                $this->exec(
                    "printf 'APP_NAME=Laravel\nAPP_ENV=production\nAPP_KEY=\nAPP_DEBUG=false\nLOG_CHANNEL=stack\n' > {$envPath} && chmod 666 {$envPath}",
                    $deploy, true
                );
            }
        } else {
            $this->exec("chmod 666 {$envPath}", $deploy);
        }

        // ── 2. Generate APP_KEY jika belum ada ────────────────────────────────
        $hasValidKey = (int) trim(shell_exec("grep -c '^APP_KEY=base64:' {$envPath} 2>/dev/null") ?? '0');

        if ($hasValidKey > 0) {
            $this->log($deploy, '> APP_KEY already valid. Skipping.');
        } else {
            $this->log($deploy, '> Generating APP_KEY...');

            $this->exec("sed -i '/APP_KEY/d' {$envPath}", $deploy);

            $appKey = 'base64:'.base64_encode(random_bytes(32));
            $escapedKey = escapeshellarg("APP_KEY={$appKey}");
            $this->exec("echo {$escapedKey} >> {$envPath}", $deploy, true);

            $keyAfter = trim(shell_exec("grep '^APP_KEY=base64:' {$envPath} 2>/dev/null") ?? '');
            if (empty($keyAfter)) {
                $envDump = trim(shell_exec("head -20 {$envPath} 2>/dev/null") ?? '');
                $this->log($deploy, "> [DEBUG] .env:\n{$envDump}");
                throw new \RuntimeException('APP_KEY gagal ditulis ke .env. Periksa permission file.');
            }
            $this->log($deploy, '> APP_KEY set successfully.');
        }

        // ── 3. Permission .env ────────────────────────────────────────────────
        $this->exec("chown www-data:www-data {$envPath} && chmod 666 {$envPath}", $deploy);
    }

    /**
     * Jalankan migrate + optimize setelah .env siap.
     */
    private function runLaravelPostSetup($deploy, string $projectDir): void
    {
        // Pastikan storage writable sebelum artisan apapun dijalankan
        $this->exec("chmod -R 777 {$projectDir}/storage {$projectDir}/bootstrap/cache 2>/dev/null || true", $deploy);
        $this->exec("chown -R www-data:www-data {$projectDir}/storage {$projectDir}/bootstrap/cache 2>/dev/null || true", $deploy);

        // ── Migrate jika DB dikonfigurasi ─────────────────────────────────────
        $dbConnection = trim(shell_exec("grep '^DB_CONNECTION=' {$projectDir}/.env 2>/dev/null | cut -d= -f2") ?? '');

        if (! empty($dbConnection) && $dbConnection !== 'sqlite') {
            $this->log($deploy, "> Running migrations (DB_CONNECTION={$dbConnection})...");

            $migrateOutput = trim(shell_exec(
                "cd {$projectDir} && php artisan migrate --force 2>&1"
            ) ?? '');

            if ($migrateOutput) {
                $this->log($deploy, $migrateOutput);
            }

            if (stripos($migrateOutput, 'SQLSTATE') !== false || stripos($migrateOutput, 'fatal') !== false) {
                $this->log($deploy, '> [WARNING] Migration error — pastikan DB credentials di .env sudah benar dan database sudah dibuat.');
            } else {
                $this->log($deploy, '> Migrations completed.');
            }
        } else {
            $this->log($deploy, '> Skipping migration (DB_CONNECTION tidak dikonfigurasi).');
        }

        // ── Optimize ─────────────────────────────────────────────────────────
        $this->exec("cd {$projectDir} && php artisan optimize 2>&1", $deploy);
    }

    private function runComposerInstall($deploy, string $projectDir): void
    {
        $candidates = ['/usr/local/bin/composer', '/usr/bin/composer'];
        $composer = null;

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                $composer = $path;
                break;
            }
        }

        if (! $composer) {
            $composer = trim(shell_exec('which composer 2>/dev/null') ?? '');
        }

        if (! $composer) {
            throw new \RuntimeException('composer binary tidak ditemukan di server.');
        }

        $this->log($deploy, "> Using composer: {$composer}");
        $this->exec(
            "cd {$projectDir} && {$composer} install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs",
            $deploy,
            true
        );
    }

    private function setupPython($deploy, string $projectDir): void
    {
        $this->log($deploy, '> Setting up Python virtual environment...');

        // Deteksi command python yang tersedia secara akurat
        $python = 'python3';
        $check = trim(shell_exec("python3 -c \"print('OK')\" 2>/dev/null") ?? '');
        if ($check !== 'OK') {
            $python = 'python';
            $check2 = trim(shell_exec("python -c \"print('OK')\" 2>/dev/null") ?? '');
            if ($check2 !== 'OK') {
                $python = 'py';
                $check3 = trim(shell_exec("py -c \"print('OK')\" 2>/dev/null") ?? '');
                if ($check3 !== 'OK') {
                    throw new \RuntimeException('Python (atau python3 / py) tidak terinstall di server/environment ini.');
                }
            }
        }

        $this->exec("cd {$projectDir} && {$python} -m venv venv", $deploy, true);

        // Deteksi path pip/uvicorn (Linux/Mac menggunakan bin/, Windows menggunakan Scripts/)
        $binDir = "venv/bin";
        if (is_dir("{$projectDir}/venv/Scripts")) {
            $binDir = "venv/Scripts";
        }
        $pipPath = "{$binDir}/pip";
        $uvicornPath = "{$binDir}/uvicorn";

        $this->exec("cd {$projectDir} && {$pipPath} install --upgrade pip", $deploy);

        if (file_exists("{$projectDir}/requirements.txt")) {
            $this->exec("cd {$projectDir} && {$pipPath} install -r requirements.txt", $deploy, true);
            $this->log($deploy, '> Python dependencies installed dari requirements.txt.');
        } else {
            $this->log($deploy, '> [WARNING] requirements.txt tidak ditemukan. Melewati instalasi dependencies.');
        }

        // --- MANAJEMEN PROSES & REVERSE PROXY ---
        $this->log($deploy, "\n> Configuring Python Background Process & Reverse Proxy...");

        $sockFile = "{$projectDir}/uvicorn.sock";
        $pidFile = "{$projectDir}/.python.pid";

        // Kill existing process if running
        $this->exec("if [ -f {$pidFile} ]; then kill -9 $(cat {$pidFile}) 2>/dev/null || true; fi", $deploy);
        $this->exec("rm -f {$sockFile}", $deploy);

        // Start Uvicorn in background via UNIX Socket
        $this->log($deploy, "> Starting Uvicorn on UNIX Socket...");
        $startCmd = "cd {$projectDir} && nohup {$uvicornPath} main:app --uds {$sockFile} > storage_fastapi.log 2>&1 & echo $! > {$pidFile}";
        $this->exec($startCmd, $deploy);

        // Wait briefly for Uvicorn to create the socket, then make it writable for PHP-FPM
        $this->exec("sleep 2 && chmod 777 {$sockFile} 2>/dev/null || true", $deploy);

        // Generate PHP Reverse Proxy using UNIX Socket
        $proxyCode = <<<PHP
<?php
/**
 * Auto-generated PHP Reverse Proxy by Ryaze
 * Forwards requests to the underlying Python application via UNIX Socket.
 * This bypasses Docker network isolation!
 */

\$sockFile = __DIR__ . '/uvicorn.sock';
\$method = \$_SERVER['REQUEST_METHOD'] ?? 'GET';
\$uri = \$_SERVER['REQUEST_URI'] ?? '/';
\$url = "http://localhost" . \$uri;

if (!file_exists(\$sockFile)) {
    http_response_code(502);
    echo "Ryaze Gateway Error: Uvicorn socket not found. App might still be starting or crashed.";
    exit;
}

\$ch = curl_init();
curl_setopt(\$ch, CURLOPT_UNIX_SOCKET_PATH, \$sockFile);
curl_setopt(\$ch, CURLOPT_URL, \$url);
curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt(\$ch, CURLOPT_HEADER, true);
curl_setopt(\$ch, CURLOPT_CUSTOMREQUEST, \$method);

// Forward headers
\$headers = [];
if (function_exists('getallheaders')) {
    foreach (getallheaders() as \$k => \$v) {
        if (strtolower(\$k) !== 'host') {
            \$headers[] = "\$k: \$v";
        }
    }
}
\$headers[] = "Host: localhost";
\$headers[] = "Connection: close";
curl_setopt(\$ch, CURLOPT_HTTPHEADER, \$headers);

if (\$method !== 'GET' && \$method !== 'HEAD') {
    \$input = file_get_contents('php://input');
    curl_setopt(\$ch, CURLOPT_POSTFIELDS, \$input);
}

\$response = curl_exec(\$ch);

if (\$response === false) {
    http_response_code(502);
    echo "Ryaze Gateway Error: Failed to connect to Uvicorn via socket.<br>";
    echo "cURL Error: " . curl_error(\$ch);
    exit;
}

\$headerSize = curl_getinfo(\$ch, CURLINFO_HEADER_SIZE);
\$responseHeaders = substr(\$response, 0, \$headerSize);
\$body = substr(\$response, \$headerSize);
\$httpCode = curl_getinfo(\$ch, CURLINFO_HTTP_CODE);

curl_close(\$ch);
http_response_code(\$httpCode);

// Forward Response Headers
\$headersArray = explode("\\r\\n", \$responseHeaders);
foreach (\$headersArray as \$header) {
    if (trim(\$header) && stripos(\$header, 'Transfer-Encoding') === false && stripos(\$header, 'Connection') === false) {
        header(\$header);
    }
}

echo \$body;
PHP;

        file_put_contents("{$projectDir}/index.php", $proxyCode);
        $this->exec("chown www-data:www-data {$projectDir}/index.php && chmod 644 {$projectDir}/index.php 2>/dev/null || true", $deploy);
        $this->log($deploy, "> Reverse Proxy created at index.php (Target: UNIX Socket).");
    }

    private function moveBuiltOutput($deploy, string $projectDir): void
    {
        $outputDirName = null;
        foreach (['dist', 'build', 'out'] as $candidate) {
            if (is_dir("{$projectDir}/{$candidate}")) {
                $outputDirName = $candidate;
                break;
            }
        }

        if (! $outputDirName) {
            throw new \RuntimeException(
                "Build output folder (dist/build/out) tidak ditemukan di {$projectDir}. Build kemungkinan gagal."
            );
        }

        $outputDir = "{$projectDir}/{$outputDirName}";
        $this->log($deploy, "> Output folder found: {$outputDir}");

        $this->exec("cp -a {$outputDir}/. {$projectDir}/", $deploy, true);
        $this->exec("rm -rf {$outputDir}", $deploy);

        if (! file_exists("{$projectDir}/index.html")) {
            throw new \RuntimeException(
                'index.html tidak ditemukan setelah move. Pastikan build menghasilkan index.html.'
            );
        }

        $this->log($deploy, '> Build output moved and ready.');
    }

    // =========================================================================
    // CLOUDFLARE
    // =========================================================================

    private function createCloudflareDNS($deploy): bool
    {
        $domainName = $this->project->ryaze_domain;
        $zoneId = config('services.cloudflare.zone_id', env('CLOUDFLARE_ZONE_ID'));
        $apiToken = config('services.cloudflare.api_token', env('CLOUDFLARE_API_TOKEN'));
        $tunnelUrl = preg_replace('#^https?://#', '', rtrim(
            config('services.cloudflare.tunnel_url', env('CLOUDFLARE_TUNNEL_URL')), '/'
        ));

        if (! $zoneId || ! $apiToken || ! $tunnelUrl) {
            $this->log($deploy, '> [ERROR] Cloudflare credentials (.env) incomplete!');

            return false;
        }

        $existing = Http::withToken($apiToken)
            ->get("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records", [
                'type' => 'CNAME',
                'name' => $domainName,
            ]);

        if ($existing->successful() && count($existing->json('result', [])) > 0) {
            $this->log($deploy, '> DNS record already exists. Skipping.');

            return true;
        }

        $response = Http::withToken($apiToken)
            ->post("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records", [
                'type' => 'CNAME',
                'name' => $domainName,
                'content' => $tunnelUrl,
                'ttl' => 1,
                'proxied' => true,
            ]);

        if ($response->successful()) {
            $this->log($deploy, '> DNS record created successfully!');

            return true;
        }

        $errorMessage = $response->json('errors.0.message', 'Unknown Cloudflare API Error');
        $this->log($deploy, "> [API ERROR] Cloudflare: {$errorMessage}");

        return false;
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    private function exec(string $command, $deploy, bool $throwOnError = false): string
    {
        // ════════ MANTRA ANTI-BLEEDING ════════
        $unsetEnv = 'unset APP_NAME APP_ENV APP_KEY APP_DEBUG APP_URL LOG_CHANNEL DB_CONNECTION DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD BROADCAST_DRIVER CACHE_DRIVER QUEUE_CONNECTION SESSION_DRIVER SESSION_LIFETIME REDIS_HOST REDIS_PASSWORD REDIS_PORT; ';

        $fullCommand = $unsetEnv."({$command}) 2>&1; echo \"__EXIT_CODE__:$?\"";
        // ══════════════════════════════════════

        $raw = shell_exec($fullCommand) ?? '';

        $exitCode = 0;
        $output = $raw;

        if (preg_match('/\n?__EXIT_CODE__:(\d+)\s*$/', $raw, $matches)) {
            $exitCode = (int) $matches[1];
            $output = trim(substr($raw, 0, strrpos($raw, "\n__EXIT_CODE__:{$exitCode}")));
            if ($output === false) {
                $output = trim(str_replace($matches[0], '', $raw));
            }
        }

        $output = trim($output);

        if ($output !== '') {
            $this->log($deploy, $output);
        }

        if ($throwOnError && $exitCode !== 0) {
            $lines = array_filter(array_map('trim', explode("\n", $output)));
            $summary = implode(' | ', array_slice(array_values($lines), -3));
            throw new \RuntimeException("Command exited with code {$exitCode}: {$summary}");
        }

        return $output;
    }

    private function log($deploy, string $text): void
    {
        $deploy->refresh();
        $deploy->update([
            'build_logs' => $deploy->build_logs."\n".$text,
        ]);
    }

    private function markAsFailed($deploy): void
    {
        $deploy->update(['status' => 'failed']);
        $this->project->update(['status' => 'error']);
    }

    /**
     * Cek apakah direktori kosong (tidak ada file/folder selain . dan ..)
     */
    private function isDirEmpty(string $dir): bool
    {
        if (!is_dir($dir)) {
            return true;
        }
        $items = array_diff(scandir($dir), ['.', '..']);
        return empty($items);
    }

    // =========================================================================
    // TEMPLATE SCAFFOLDER
    // =========================================================================

    /**
     * Generate file starter langsung di server berdasarkan template key.
     * Tidak perlu koneksi internet atau git clone sama sekali.
     */
    private function scaffoldTemplate(string $key, string $dir, $deploy): void
    {
        $projectName = $this->project->project_name;
        $domain      = $this->project->ryaze_domain;

        match ($key) {
            'html_landing'    => $this->scaffoldHtml($dir, $projectName, $domain),
            'php_basic'       => $this->scaffoldPhp($dir, $projectName),
            'laravel_starter' => $this->scaffoldLaravel($dir, $projectName, $deploy),
            'react_starter'   => $this->scaffoldReact($dir, $projectName),
            'nextjs_starter'  => $this->scaffoldNextjs($dir, $projectName),
            'node_express'    => $this->scaffoldNode($dir, $projectName),
            default           => throw new \RuntimeException("Unknown template key: {$key}"),
        };
    }

    private function scaffoldHtml(string $dir, string $name, string $domain): void
    {
        file_put_contents("{$dir}/index.html", <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$name}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: white; border-radius: 20px; padding: 60px 50px; text-align: center; max-width: 500px; box-shadow: 0 25px 50px rgba(0,0,0,0.15); }
        .badge { background: linear-gradient(135deg, #667eea, #764ba2); color: white; font-size: 12px; font-weight: 700; padding: 6px 16px; border-radius: 50px; display: inline-block; margin-bottom: 24px; letter-spacing: 1px; text-transform: uppercase; }
        h1 { font-size: 2.5rem; font-weight: 800; color: #1a1a2e; margin-bottom: 16px; }
        p { color: #6b7280; line-height: 1.8; margin-bottom: 32px; }
        .btn { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 14px 36px; border-radius: 12px; text-decoration: none; font-weight: 700; display: inline-block; transition: transform 0.2s, box-shadow 0.2s; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(102,126,234,0.4); }
        .domain { margin-top: 24px; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="card">
        <span class="badge">🚀 Ryaze Hosting</span>
        <h1>{$name}</h1>
        <p>Website Anda sudah live! Edit file <code>index.html</code> ini lewat <strong>File Manager</strong> untuk mulai kustomisasi tampilan.</p>
        <a href="#" class="btn">Mulai Edit →</a>
        <p class="domain">🌐 {$domain}</p>
    </div>
</body>
</html>
HTML);

        file_put_contents("{$dir}/style.css", "/* Tambahkan style kustom di sini */\n");
        file_put_contents("{$dir}/script.js", "// Tambahkan script kustom di sini\nconsole.log('Hello from {$name}!');\n");
        file_put_contents("{$dir}/.htaccess", "Options -Indexes\nRewriteEngine On\n");
    }

    private function scaffoldPhp(string $dir, string $name): void
    {
        @mkdir("{$dir}/app", 0775, true);
        @mkdir("{$dir}/public", 0775, true);

        file_put_contents("{$dir}/index.php", <<<'PHP'
<?php
// Router sederhana
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
require_once __DIR__ . '/app/router.php';
PHP);

        file_put_contents("{$dir}/app/router.php", <<<PHP
<?php
\$routes = [
    '/' => fn() => view('home'),
];

\$handler = \$routes[\$path] ?? fn() => view('404');
echo \$handler();

function view(string \$name): string {
    \$file = __DIR__ . "/../views/{\$name}.php";
    if (!file_exists(\$file)) return '<h1>404 Not Found</h1>';
    ob_start();
    include \$file;
    return ob_get_clean();
}
PHP);

        @mkdir("{$dir}/views", 0775, true);
        file_put_contents("{$dir}/views/home.php", <<<PHP
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>{$name}</title>
<style>body{font-family:sans-serif;max-width:700px;margin:60px auto;padding:20px;}</style>
</head>
<body>
<h1>🐘 {$name}</h1>
<p>PHP Native app Anda sudah berjalan! Edit <code>views/home.php</code> untuk memulai.</p>
</body></html>
PHP);

        file_put_contents("{$dir}/views/404.php", "<h1>404 — Halaman tidak ditemukan</h1>");
        file_put_contents("{$dir}/.htaccess", "Options -Indexes\nRewriteEngine On\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteRule ^ index.php [QSA,L]\n");
    }

    private function scaffoldLaravel(string $dir, string $name, $deploy): void
    {
        // Gunakan composer create-project untuk install Laravel fresh
        $this->log($deploy, '> Installing fresh Laravel project via Composer...');
        $this->log($deploy, '> (Ini mungkin membutuhkan waktu 1-2 menit)');

        $candidates = ['/usr/local/bin/composer', '/usr/bin/composer'];
        $composer   = null;
        foreach ($candidates as $path) {
            if (file_exists($path)) { $composer = $path; break; }
        }
        if (!$composer) {
            $composer = trim(shell_exec('which composer 2>/dev/null') ?? '');
        }
        if (!$composer) {
            throw new \RuntimeException('composer binary tidak ditemukan di server.');
        }

        $parentDir = dirname($dir);
        $baseName  = basename($dir);

        // Buat di temp dir lalu pindah
        $tmpDir = "{$parentDir}/.tmp_{$baseName}";
        shell_exec("rm -rf {$tmpDir} 2>/dev/null");

        $output = shell_exec(
            "{$composer} create-project laravel/laravel {$tmpDir} --no-interaction --prefer-dist --no-progress 2>&1"
        );

        if (!is_dir("{$tmpDir}/artisan")) {
            throw new \RuntimeException('Laravel create-project gagal. Output: ' . substr($output ?? '', 0, 200));
        }

        shell_exec("mv {$tmpDir} {$dir}");
    }

    private function scaffoldReact(string $dir, string $name): void
    {
        $safeName = preg_replace('/[^a-z0-9-]/', '-', strtolower($name));

        @mkdir($dir, 0775, true);

        file_put_contents("{$dir}/package.json", json_encode([
            'name'    => $safeName,
            'version' => '1.0.0',
            'private' => true,
            'scripts' => ['dev' => 'vite', 'build' => 'vite build', 'preview' => 'vite preview'],
            'dependencies'    => ['react' => '^18.3.1', 'react-dom' => '^18.3.1'],
            'devDependencies' => ['@vitejs/plugin-react' => '^4.3.1', 'vite' => '^5.4.2'],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        file_put_contents("{$dir}/vite.config.js", <<<JS
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  build: { outDir: 'dist' },
})
JS);

        file_put_contents("{$dir}/index.html", <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{$name}</title>
</head>
<body>
  <div id="root"></div>
  <script type="module" src="/src/main.jsx"></script>
</body>
</html>
HTML);

        @mkdir("{$dir}/src", 0775, true);

        file_put_contents("{$dir}/src/main.jsx", <<<'JSX'
import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App'
import './index.css'

ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode><App /></React.StrictMode>
)
JSX);

        file_put_contents("{$dir}/src/App.jsx", <<<PHP
import React from 'react'

export default function App() {
  return (
    <div style={{fontFamily:'sans-serif',maxWidth:'600px',margin:'60px auto',textAlign:'center'}}>
      <h1>⚛️ {$name}</h1>
      <p>React + Vite app Anda sudah siap! Edit <code>src/App.jsx</code> untuk memulai.</p>
    </div>
  )
}
PHP);

        file_put_contents("{$dir}/src/index.css", "body { margin: 0; font-family: 'Segoe UI', sans-serif; }\n");
        file_put_contents("{$dir}/.gitignore", "node_modules\ndist\n.env\n");
    }

    private function scaffoldNextjs(string $dir, string $name): void
    {
        $safeName = preg_replace('/[^a-z0-9-]/', '-', strtolower($name));

        @mkdir($dir, 0775, true);

        file_put_contents("{$dir}/package.json", json_encode([
            'name'    => $safeName,
            'version' => '1.0.0',
            'private' => true,
            'scripts' => ['dev' => 'next dev', 'build' => 'next build', 'start' => 'next start'],
            'dependencies'    => ['next' => '^14.2.5', 'react' => '^18.3.1', 'react-dom' => '^18.3.1'],
            'devDependencies' => [],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        file_put_contents("{$dir}/next.config.js", "/** @type {import('next').NextConfig} */\nconst nextConfig = { output: 'export' }\nmodule.exports = nextConfig\n");

        @mkdir("{$dir}/app", 0775, true);

        file_put_contents("{$dir}/app/page.js", <<<PHP
export default function Home() {
  return (
    <main style={{fontFamily:'sans-serif',maxWidth:'600px',margin:'60px auto',textAlign:'center'}}>
      <h1>▲ {$name}</h1>
      <p>Next.js app Anda sudah siap! Edit <code>app/page.js</code> untuk memulai.</p>
    </main>
  )
}
PHP);

        file_put_contents("{$dir}/app/layout.js", <<<PHP
export const metadata = { title: '{$name}' }
export default function RootLayout({ children }) {
  return <html lang="id"><body>{children}</body></html>
}
PHP);

        file_put_contents("{$dir}/.gitignore", "node_modules\n.next\nout\n.env\n");
    }

    private function scaffoldNode(string $dir, string $name): void
    {
        $safeName = preg_replace('/[^a-z0-9-]/', '-', strtolower($name));

        @mkdir($dir, 0775, true);

        file_put_contents("{$dir}/package.json", json_encode([
            'name'         => $safeName,
            'version'      => '1.0.0',
            'main'         => 'index.js',
            'scripts'      => ['start' => 'node index.js', 'dev' => 'node index.js'],
            'dependencies' => ['express' => '^4.19.2'],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        file_put_contents("{$dir}/index.js", <<<PHP
const express = require('express');
const app = express();
const PORT = process.env.PORT || 3000;

app.use(express.json());

app.get('/', (req, res) => {
    res.json({
        name: '{$name}',
        message: 'API berjalan! Edit index.js untuk menambah endpoint.',
        endpoints: ['GET /'],
    });
});

app.listen(PORT, () => {
    console.log(`{$name} listening on port \${PORT}`);
});
PHP);

        file_put_contents("{$dir}/.gitignore", "node_modules\n.env\n");
        file_put_contents("{$dir}/.env.example", "PORT=3000\n# Tambahkan env vars di sini\n");
    }
}
