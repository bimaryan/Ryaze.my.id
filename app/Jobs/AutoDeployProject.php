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

    /**
     * Jumlah retry jika job gagal.
     */
    public int $tries = 1;

    /**
     * Timeout maksimum job (detik). Build NPM bisa lama.
     */
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
                // Repo sudah ada → pull
                $this->log($deploy, '> Repository found. Pulling latest changes...');
                $this->exec("chown -R root:root {$projectDir}", $deploy);
                $this->exec(
                    "cd {$projectDir} && git fetch --all 2>&1 && git reset --hard origin/{$this->project->branch} 2>&1",
                    $deploy,
                    true // throw on error
                );
            } else {
                // Folder ada tapi bukan git → bersihkan
                if (is_dir($projectDir)) {
                    $this->log($deploy, '> Found stale directory (not a git repo). Cleaning up...');
                    $this->exec("rm -rf {$projectDir}", $deploy);
                }

                // Clone baru
                $this->log($deploy, '> Cloning repository...');
                $this->exec(
                    "git clone -b {$this->project->branch} {$this->project->repo_source} {$projectDir} 2>&1",
                    $deploy,
                    true // throw on error
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
        // Install dependencies
        $this->log($deploy, '> Installing NPM dependencies...');
        $this->exec(
            "cd {$projectDir} && /usr/bin/npm install --legacy-peer-deps 2>&1",
            $deploy,
            true
        );

        // Build (bukan Node.js murni — Node tidak punya build step)
        if (in_array($framework, ['react', 'nextjs', 'vue'])) {
            $this->log($deploy, '> Running build script...');
            $this->exec(
                "cd {$projectDir} && /usr/bin/npm run build 2>&1",
                $deploy,
                true
            );

            $this->log($deploy, '> Organizing build output...');
            $this->moveBuiltOutput($deploy, $projectDir);
        }
    }

    private function setupPython($deploy, string $projectDir): void
    {
        $this->log($deploy, '> Setting up Python virtual environment...');

        // source tidak bisa di shell_exec; jalankan pip langsung dari venv
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
        // Tentukan folder output (dist atau build)
        $outputDirName = null;
        foreach (['dist', 'build'] as $candidate) {
            if (is_dir("{$projectDir}/{$candidate}")) {
                $outputDirName = $candidate;
                break;
            }
        }

        if (! $outputDirName) {
            $this->log($deploy, '> [WARNING] Build output folder (dist/build) not found! Skipping move.');
            return;
        }

        $outputDir = "{$projectDir}/{$outputDirName}";
        $this->log($deploy, "> Output folder found: {$outputDir}");

        // Salin semua isi ke project root (termasuk hidden files via dot glob)
        // Jalankan sebagai root sehingga tidak ada permission issue saat copy
        $this->exec("cp -a {$outputDir}/. {$projectDir}/", $deploy, true);

        // Hapus folder output agar tidak berantakan
        $this->exec("rm -rf {$outputDir}", $deploy);

        // Pastikan index.html ada — kalau tidak ada, deployment percuma
        if (! file_exists("{$projectDir}/index.html")) {
            throw new \RuntimeException("index.html tidak ditemukan di {$projectDir} setelah build. Periksa output build project.");
        }

        // Reset ownership ke www-data agar webserver bisa baca
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

        // Jangan throw — DNS error tidak harus menggagalkan seluruh deploy
        return false;
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    /**
     * Jalankan shell command, tulis output ke log.
     *
     * @param  bool  $throwOnError  Jika true, lempar exception saat output mengandung fatal/error.
     *
     * @throws \RuntimeException
     */
    private function exec(string $command, $deploy, bool $throwOnError = false): string
    {
        // Pastikan stderr selalu digabung ke stdout
        if (! str_ends_with(trim($command), '2>&1')) {
            $command .= ' 2>&1';
        }

        $output = shell_exec($command) ?? '';
        $output = trim($output);

        if ($output !== '') {
            $this->log($deploy, $output);
        }

        if ($throwOnError) {
            $lower = strtolower($output);

            // "npm warn" dan "warning" bukan error — filter dulu
            $lines = explode("\n", $lower);
            $errorLines = array_filter($lines, function ($line) {
                $line = trim($line);
                // Abaikan baris warning/hint/info
                if (str_starts_with($line, 'npm warn')
                    || str_starts_with($line, 'warning')
                    || str_starts_with($line, 'browserslist:')
                    || str_starts_with($line, 'one of your')
                    || str_starts_with($line, 'find out more')
                    || str_starts_with($line, 'you can control')
                    || $line === ''
                ) {
                    return false;
                }

                // Tandai sebagai error hanya jika mengandung keyword fatal
                $fatals = ['fatal:', 'error:', 'npm err!', 'could not read', 'permission denied', 'command failed'];
                foreach ($fatals as $keyword) {
                    if (str_contains($line, $keyword)) {
                        return true;
                    }
                }

                return false;
            });

            if (! empty($errorLines)) {
                $errorDetail = implode(' | ', array_slice(array_values($errorLines), 0, 3));
                throw new \RuntimeException("Command failed: {$errorDetail}");
            }
        }

        return $output;
    }

    /**
     * Append satu baris ke build_logs. Refresh model sebelum update
     * supaya tidak menimpa log sebelumnya.
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
