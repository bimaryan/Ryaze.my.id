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

        $baseDir    = '/www/sites/hosting_clients';
        $subdomain  = str_replace('.ryaze.my.id', '', $this->project->ryaze_domain);
        $projectDir = $baseDir . '/' . $subdomain;

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

            // Tandai semua direktori sebagai safe agar git tidak complain ownership
            $this->exec("git config --global --add safe.directory '*'", $deploy);

            $isRepo = is_dir("{$projectDir}/.git");

            if ($isRepo) {
                $this->log($deploy, '> Repository found. Pulling latest changes...');
                // Pastikan root punya akses untuk git pull
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

            // Kembalikan ownership ke www-data setelah git
            $this->exec("chown -R www-data:www-data {$projectDir} && chmod -R 775 {$projectDir}", $deploy);

            // ----------------------------------------------------------------
            // TAHAP 3: Setup Framework
            // ----------------------------------------------------------------
            $framework = strtolower($this->project->framework);
            $this->log($deploy, "\n> Setting up " . strtoupper($framework) . ' environment...');

            match ($framework) {
                'react', 'nextjs', 'vue', 'node' => $this->setupNodeFramework($deploy, $projectDir, $framework),
                'laravel'                          => $this->setupLaravel($deploy, $projectDir),
                'python'                           => $this->setupPython($deploy, $projectDir),
                'html'                             => $this->log($deploy, '> Static HTML project. No build step required.'),
                default                            => $this->log($deploy, "> [WARNING] Framework '{$framework}' tidak dikenali. Melewati build step."),
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
                'status'      => 'ready',
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

    /**
     * Handler untuk React / Vue / Next.js / Node.js.
     * Otomatis deteksi jika project adalah Laravel + Inertia.
     */
    private function setupNodeFramework($deploy, string $projectDir, string $framework): void
    {
        // Deteksi Laravel + Inertia (ada artisan + composer.json + package.json)
        $isLaravelInertia = file_exists("{$projectDir}/artisan")
            && file_exists("{$projectDir}/composer.json")
            && file_exists("{$projectDir}/package.json");

        if ($isLaravelInertia) {
            $this->log($deploy, '> Laravel+Inertia detected. Running composer install first...');
            $this->runComposerInstall($deploy, $projectDir);
            $this->setupLaravelEnv($deploy, $projectDir);
        }

        // Install NPM dependencies
        $this->log($deploy, '> Installing NPM dependencies...');
        $this->exec(
            "cd {$projectDir} && /usr/bin/npm install --legacy-peer-deps",
            $deploy,
            true
        );

        // Build — Node.js Express murni tidak perlu build
        if (in_array($framework, ['react', 'nextjs', 'vue'])) {
            $this->log($deploy, '> Running build script...');
            $this->exec(
                "cd {$projectDir} && /usr/bin/npm run build",
                $deploy,
                true
            );

            if ($isLaravelInertia) {
                // Laravel/Inertia: Vite output sudah masuk ke public/build — tidak perlu dipindah
                $this->log($deploy, '> Laravel+Inertia: Vite output is at public/build. No file move needed.');
                // Set ulang permission setelah build
                $this->exec("chown -R www-data:www-data {$projectDir} && chmod -R 755 {$projectDir}", $deploy);
            } else {
                $this->log($deploy, '> Organizing build output...');
                $this->moveBuiltOutput($deploy, $projectDir);
            }
        }
    }

    /**
     * Handler untuk framework Laravel murni (tanpa Inertia/NPM build).
     */
    private function setupLaravel($deploy, string $projectDir): void
    {
        $this->runComposerInstall($deploy, $projectDir);
        $this->setupLaravelEnv($deploy, $projectDir);

        // Jalankan artisan optimize
        $this->exec("cd {$projectDir} && php artisan optimize", $deploy);

        $this->log($deploy, '> Laravel setup complete.');
    }

    /**
     * Setup .env dan APP_KEY untuk project Laravel.
     * Urutan penting: copy .env dulu, baru key:generate.
     */
    private function setupLaravelEnv($deploy, string $projectDir): void
    {
        $envPath        = "{$projectDir}/.env";
        $envExamplePath = "{$projectDir}/.env.example";

        // ── 1. Buat .env jika belum ada ──────────────────────────────────────
        if (! file_exists($envPath)) {
            if (file_exists($envExamplePath)) {
                $this->log($deploy, '> Creating .env from .env.example...');
                $this->exec("cp {$envExamplePath} {$envPath}", $deploy, true);
            } else {
                $this->log($deploy, '> [WARNING] .env.example tidak ditemukan! Membuat .env minimal...');
                file_put_contents($envPath, "APP_NAME=Laravel\nAPP_ENV=production\nAPP_KEY=\nAPP_DEBUG=false\n");
            }
        }

        // ── 2. Pastikan baris APP_KEY= ada — key:generate WAJIB ada placeholder ──
        clearstatcache(true, $envPath);
        $envContent = file_get_contents($envPath) ?: '';

        if (preg_match('/^APP_KEY=base64:.+/m', $envContent)) {
            $this->log($deploy, '> APP_KEY already set. Skipping key:generate.');
        } else {
            // Inject placeholder APP_KEY= jika baris tidak ada sama sekali
            if (! preg_match('/^APP_KEY=/m', $envContent)) {
                $this->log($deploy, '> APP_KEY line missing. Injecting placeholder...');
                file_put_contents($envPath, $envContent . "\nAPP_KEY=\n");
                clearstatcache(true, $envPath);
            }

            $this->log($deploy, '> Generating APP_KEY...');
            $this->exec("cd {$projectDir} && php artisan key:generate --force", $deploy, true);

            // Verifikasi key benar-benar terset
            clearstatcache(true, $envPath);
            $envAfter = file_get_contents($envPath) ?: '';
            if (! preg_match('/^APP_KEY=base64:.+/m', $envAfter)) {
                throw new \RuntimeException('key:generate sukses tapi APP_KEY masih kosong. Periksa permission .env.');
            }
            $this->log($deploy, '> APP_KEY generated successfully.');
        }

        // ── 3. Storage & cache permission ────────────────────────────────────
        $this->exec("chmod -R 775 {$projectDir}/storage {$projectDir}/bootstrap/cache 2>/dev/null || true", $deploy);
        $this->exec("chown -R www-data:www-data {$projectDir}/storage {$projectDir}/bootstrap/cache 2>/dev/null || true", $deploy);

        $this->log($deploy, '> Laravel environment ready.');
    }


    private function runComposerInstall($deploy, string $projectDir): void
    {
        // Cari binary composer
        $candidates = ['/usr/local/bin/composer', '/usr/bin/composer'];
        $composer   = null;

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
            throw new \RuntimeException('composer binary tidak ditemukan di server. Install composer terlebih dahulu.');
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

    /**
     * Pindahkan isi folder dist/ atau build/ ke root project.
     * Hanya untuk SPA murni (React/Vue/Next tanpa Laravel).
     */
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
                "Build output folder (dist/build/out) tidak ditemukan di {$projectDir}. " .
                "Build kemungkinan gagal — periksa log npm di atas."
            );
        }

        $outputDir = "{$projectDir}/{$outputDirName}";
        $this->log($deploy, "> Output folder found: {$outputDir}");

        // cp -a: preserve semua attribute + salin hidden files via trailing slash
        $this->exec("cp -a {$outputDir}/. {$projectDir}/", $deploy, true);
        $this->exec("rm -rf {$outputDir}", $deploy);

        // Validasi index.html wajib ada
        if (! file_exists("{$projectDir}/index.html")) {
            throw new \RuntimeException(
                "index.html tidak ditemukan setelah move. " .
                "Pastikan build project menghasilkan index.html di folder {$outputDirName}/."
            );
        }

        // Reset ownership ke www-data
        $this->exec(
            "chown -R www-data:www-data {$projectDir} && chmod -R 755 {$projectDir}",
            $deploy
        );

        $this->log($deploy, '> Build output moved, cleaned, and permissions reset to www-data.');
    }

    // =========================================================================
    // CLOUDFLARE
    // =========================================================================

    private function createCloudflareDNS($deploy): bool
    {
        $domainName = $this->project->ryaze_domain;
        $zoneId     = config('services.cloudflare.zone_id',   env('CLOUDFLARE_ZONE_ID'));
        $apiToken   = config('services.cloudflare.api_token', env('CLOUDFLARE_API_TOKEN'));
        $tunnelUrl  = preg_replace('#^https?://#', '', rtrim(
            config('services.cloudflare.tunnel_url', env('CLOUDFLARE_TUNNEL_URL')), '/'
        ));

        if (! $zoneId || ! $apiToken || ! $tunnelUrl) {
            $this->log($deploy, '> [ERROR] Cloudflare credentials (.env) are incomplete!');
            return false;
        }

        // Cek apakah DNS record sudah ada
        $existing = Http::withToken($apiToken)
            ->get("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records", [
                'type' => 'CNAME',
                'name' => $domainName,
            ]);

        if ($existing->successful() && count($existing->json('result', [])) > 0) {
            $this->log($deploy, '> DNS record already exists. Skipping creation.');
            return true;
        }

        $response = Http::withToken($apiToken)
            ->post("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records", [
                'type'    => 'CNAME',
                'name'    => $domainName,
                'content' => $tunnelUrl,
                'ttl'     => 1,
                'proxied' => true,
            ]);

        if ($response->successful()) {
            $this->log($deploy, '> DNS record created and propagated successfully!');
            return true;
        }

        $errorMessage = $response->json('errors.0.message', 'Unknown Cloudflare API Error');
        $this->log($deploy, "> [API ERROR] Cloudflare rejected the request: {$errorMessage}");

        return false;
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    /**
     * Jalankan shell command.
     * Gunakan exit code ($?) sebagai penentu sukses/gagal — bukan parsing teks.
     *
     * @throws \RuntimeException jika $throwOnError = true dan exit code != 0
     */
    private function exec(string $command, $deploy, bool $throwOnError = false): string
    {
        // Inject exit code capture — jangan ubah command aslinya agar exit code benar
        $fullCommand = "({$command}) 2>&1; echo \"__EXIT_CODE__:$?\"";
        $raw         = shell_exec($fullCommand) ?? '';

        $exitCode = 0;
        $output   = $raw;

        if (preg_match('/\n?__EXIT_CODE__:(\d+)\s*$/', $raw, $matches)) {
            $exitCode = (int) $matches[1];
            $output   = trim(substr($raw, 0, strrpos($raw, "\n__EXIT_CODE__:{$exitCode}")));
            if ($output === false) {
                $output = trim(str_replace($matches[0], '', $raw));
            }
        }

        $output = trim($output);

        if ($output !== '') {
            $this->log($deploy, $output);
        }

        if ($throwOnError && $exitCode !== 0) {
            $lines   = array_filter(array_map('trim', explode("\n", $output)));
            $summary = implode(' | ', array_slice(array_values($lines), -3));
            throw new \RuntimeException("Command exited with code {$exitCode}: {$summary}");
        }

        return $output;
    }

    /**
     * Append teks ke build_logs.
     * Refresh model dulu agar tidak menimpa log yang sudah ada.
     */
    private function log($deploy, string $text): void
    {
        $deploy->refresh();
        $deploy->update([
            'build_logs' => $deploy->build_logs . "\n" . $text,
        ]);
    }

    private function markAsFailed($deploy): void
    {
        $deploy->update(['status' => 'failed']);
        $this->project->update(['status' => 'error']);
    }
}
