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

    /** Jumlah retry jika job gagal. */
    public int $tries = 1;

    /** Timeout maksimum job (detik). Build NPM bisa lama. */
    public int $timeout = 600;

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

            $isRepo = is_dir("{$projectDir}/.git");

            if ($isRepo) {
                $this->log($deploy, '> Repository found. Pulling latest changes...');
                $this->exec("chown -R root:root {$projectDir}", $deploy);
                $this->exec(
                    "cd {$projectDir} && git fetch --all 2>&1 && git reset --hard origin/{$this->project->branch} 2>&1",
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
                    "git clone -b {$this->project->branch} {$this->project->repo_source} {$projectDir} 2>&1",
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

            if (in_array($framework, ['react', 'node', 'nextjs', 'vue'])) {
                $this->setupNodeFramework($deploy, $projectDir, $framework);
            } elseif ($framework === 'laravel') {
                $this->setupLaravel($deploy, $projectDir);
            } elseif ($framework === 'python') {
                $this->setupPython($deploy, $projectDir);
            } elseif ($framework === 'html') {
                $this->log($deploy, '> Static HTML project. No build step required.');
            } else {
                $this->log($deploy, "> [WARNING] Framework '{$framework}' tidak dikenali. Melewati build step.");
            }

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

    private function setupNodeFramework($deploy, string $projectDir, string $framework): void
    {
        // Deteksi apakah ini project Laravel + Inertia (Vue/React frontend)
        $isLaravel = file_exists("{$projectDir}/artisan") && file_exists("{$projectDir}/composer.json");

        if ($isLaravel) {
            $this->log($deploy, '> Laravel project detected. Running composer install first...');
            $this->runComposerInstall($deploy, $projectDir);

            // Buat .env jika belum ada
            if (! file_exists("{$projectDir}/.env") && file_exists("{$projectDir}/.env.example")) {
                $this->exec("cp {$projectDir}/.env.example {$projectDir}/.env", $deploy);
                $this->exec("cd {$projectDir} && php artisan key:generate 2>&1", $deploy);
            }
        }

        // Install NPM dependencies
        $this->log($deploy, '> Installing NPM dependencies...');
        $this->exec(
            "cd {$projectDir} && /usr/bin/npm install --legacy-peer-deps 2>&1",
            $deploy,
            true
        );

        // Build (Node.js murni tidak punya build step)
        if (in_array($framework, ['react', 'nextjs', 'vue'])) {
            $this->log($deploy, '> Running build script...');
            $this->exec(
                "cd {$projectDir} && /usr/bin/npm run build 2>&1",
                $deploy,
                true
            );

            if ($isLaravel) {
                // Laravel/Inertia: hasil build ada di public/build — tidak perlu dipindah
                $this->log($deploy, '> Laravel+Inertia: build output at public/build. No move needed.');
            } else {
                // SPA murni: pindahkan dist/build ke root
                $this->log($deploy, '> Organizing build output...');
                $this->moveBuiltOutput($deploy, $projectDir);
            }
        }
    }

    private function setupLaravel($deploy, string $projectDir): void
    {
        $this->runComposerInstall($deploy, $projectDir);

        if (! file_exists("{$projectDir}/.env") && file_exists("{$projectDir}/.env.example")) {
            $this->exec("cp {$projectDir}/.env.example {$projectDir}/.env", $deploy);
            $this->exec("cd {$projectDir} && php artisan key:generate 2>&1", $deploy);
        }

        $this->exec("cd {$projectDir} && php artisan config:cache 2>&1", $deploy);
        $this->exec("cd {$projectDir} && php artisan route:cache 2>&1", $deploy);
        $this->log($deploy, '> Laravel setup complete.');
    }

    private function runComposerInstall($deploy, string $projectDir): void
    {
        // Cari binary composer
        $composer = '/usr/local/bin/composer';
        if (! file_exists($composer)) {
            $composer = trim(shell_exec('which composer 2>/dev/null') ?? '');
        }
        if (! $composer) {
            throw new \RuntimeException('composer binary tidak ditemukan di server.');
        }

        $this->exec(
            "cd {$projectDir} && {$composer} install --no-dev --optimize-autoloader --no-interaction 2>&1",
            $deploy,
            true
        );
    }

    private function setupPython($deploy, string $projectDir): void
    {
        $this->log($deploy, '> Setting up Python virtual environment...');

        $this->exec(
            "cd {$projectDir} && /usr/bin/python3 -m venv venv 2>&1",
            $deploy,
            true
        );

        $this->exec(
            "cd {$projectDir} && venv/bin/pip install --upgrade pip 2>&1",
            $deploy
        );

        $this->exec(
            "cd {$projectDir} && venv/bin/pip install -r requirements.txt 2>&1",
            $deploy,
            true
        );

        $this->log($deploy, '> Python dependencies installed.');
    }

    private function moveBuiltOutput($deploy, string $projectDir): void
    {
        $outputDirName = null;
        foreach (['dist', 'build'] as $candidate) {
            if (is_dir("{$projectDir}/{$candidate}")) {
                $outputDirName = $candidate;
                break;
            }
        }

        if (! $outputDirName) {
            // Build gagal menghasilkan output — ini error, bukan warning
            throw new \RuntimeException(
                "Build output folder (dist/build) tidak ditemukan di {$projectDir}. " .
                "Build kemungkinan gagal — periksa log npm di atas."
            );
        }

        $outputDir = "{$projectDir}/{$outputDirName}";
        $this->log($deploy, "> Output folder found: {$outputDir}");

        // cp -a menyalin semua file termasuk hidden files, preserve permission
        $this->exec("cp -a {$outputDir}/. {$projectDir}/", $deploy, true);
        $this->exec("rm -rf {$outputDir}", $deploy);

        // Validasi index.html harus ada
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
     * Jalankan shell command menggunakan exit code sebagai penentu sukses/gagal.
     * Output tetap ditulis ke log.
     *
     * @throws \RuntimeException jika $throwOnError = true dan exit code != 0
     */
    private function exec(string $command, $deploy, bool $throwOnError = false): string
    {
        // Jalankan command, tangkap output + exit code
        $fullCommand = "({$command}) 2>&1; echo \"__EXIT__:$?\"";
        $raw = shell_exec($fullCommand) ?? '';

        // Pisahkan output dari exit code
        $exitCode = 0;
        if (preg_match('/\n?__EXIT__:(\d+)\s*$/', $raw, $matches)) {
            $exitCode = (int) $matches[1];
            $output   = trim(substr($raw, 0, strrpos($raw, $matches[0])));
        } else {
            $output = trim($raw);
        }

        if ($output !== '') {
            $this->log($deploy, $output);
        }

        if ($throwOnError && $exitCode !== 0) {
            // Ambil 3 baris terakhir sebagai ringkasan error
            $lines   = array_filter(array_map('trim', explode("\n", $output)));
            $summary = implode(' | ', array_slice(array_values($lines), -3));
            throw new \RuntimeException("Command exited with code {$exitCode}: {$summary}");
        }

        return $output;
    }

    /**
     * Append log ke deployment. Refresh model dulu agar tidak menimpa log sebelumnya.
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
