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
            // TAHAP 2: Git Clone atau Pull
            // ----------------------------------------------------------------
            $this->log($deploy, "\n> Checking repository status...");

            $this->exec("git config --global --add safe.directory '*'", $deploy);

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
                    "git clone -b {$this->project->branch} {$this->project->repo_source} {$projectDir}",
                    $deploy,
                    true
                );
            }

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
                'html' => $this->log($deploy, '> Static HTML project. No build step required.'),
                default => $this->log($deploy, "> [WARNING] Framework '{$framework}' tidak dikenali. Melewati build step."),
            };

            // ----------------------------------------------------------------
            // TAHAP 4: Cloudflare DNS
            // ----------------------------------------------------------------
            $this->log($deploy, "\n> Configuring Cloudflare DNS for {$this->project->ryaze_domain}...");
            $this->createCloudflareDNS($deploy);

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
                $this->exec("chown -R www-data:www-data {$projectDir} && chmod -R 755 {$projectDir}", $deploy);
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
     * APP_KEY di-generate via random_bytes — tidak pakai artisan key:generate
     * karena bisa crash akibat broken PHP extension (php_gmp.dll dll).
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

            // Hapus semua baris APP_KEY lama (apapun bentuknya)
            $this->exec("sed -i '/APP_KEY/d' {$envPath}", $deploy);

            // Generate via PHP random_bytes — tidak butuh artisan, tidak terpengaruh broken extension
            $appKey = 'base64:'.base64_encode(random_bytes(32));
            $escapedKey = escapeshellarg("APP_KEY={$appKey}");
            $this->exec("echo {$escapedKey} >> {$envPath}", $deploy, true);

            // Verifikasi
            $keyAfter = trim(shell_exec("grep '^APP_KEY=base64:' {$envPath} 2>/dev/null") ?? '');
            if (empty($keyAfter)) {
                $envDump = trim(shell_exec("head -20 {$envPath} 2>/dev/null") ?? '');
                $this->log($deploy, "> [DEBUG] .env:\n{$envDump}");
                throw new \RuntimeException('APP_KEY gagal ditulis ke .env. Periksa permission file.');
            }
            $this->log($deploy, '> APP_KEY set successfully.');
        }

        // ── 3. Permission .env ────────────────────────────────────────────────
        // chown www-data agar webserver bisa baca/tulis .env (file_put_contents dari Laravel)
        $this->exec("chown www-data:www-data {$envPath} && chmod 644 {$envPath}", $deploy);

        // ── 4. Storage & cache — chmod 777 agar www-data bisa tulis apapun user webserver-nya ──
        $this->exec("chmod -R 777 {$projectDir}/storage {$projectDir}/bootstrap/cache 2>/dev/null || true", $deploy);
        $this->exec("chown -R www-data:www-data {$projectDir}/storage {$projectDir}/bootstrap/cache 2>/dev/null || true", $deploy);

        $this->log($deploy, '> Laravel environment ready.');
    }

    /**
     * Jalankan migrate + optimize setelah .env siap.
     * Dipanggil dari setupLaravel dan setupNodeFramework (Inertia).
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
            "cd {$projectDir} && {$composer} install --no-dev --optimize-autoloader --no-interaction",
            $deploy,
            true
        );
    }

    private function setupPython($deploy, string $projectDir): void
    {
        $this->log($deploy, '> Setting up Python virtual environment...');
        $this->exec("cd {$projectDir} && /usr/bin/python3 -m venv venv", $deploy, true);
        $this->exec("cd {$projectDir} && venv/bin/pip install --upgrade pip", $deploy);
        $this->exec("cd {$projectDir} && venv/bin/pip install -r requirements.txt", $deploy, true);
        $this->log($deploy, '> Python dependencies installed.');
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

        $this->exec("chown -R www-data:www-data {$projectDir} && chmod -R 755 {$projectDir}", $deploy);
        $this->log($deploy, '> Build output moved and permissions reset.');
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
}
