<?php

namespace App\Jobs;

use App\Models\HostingProject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoDeployProject implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $project;
    public $timeout = 600; // Timeout 10 menit untuk menghindari proses build yang macet

    /**
     * Create a new job instance.
     */
    public function __construct(HostingProject $project)
    {
        $this->project = $project;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Setup path dan inisialisasi status
        $subdomain = str_replace('.ryaze.my.id', '', $this->project->ryaze_domain);
        $projectDir = "/www/sites/hosting_clients/{$subdomain}";

        // Ambil deployment terbaru
        $deploy = $this->project->deployments()->latest()->first();
        if (!$deploy) {
            $deploy = $this->project->deployments()->create([
                'status' => 'building',
                'build_logs' => "> Menyiapkan environment deployment...\n",
            ]);
        } else {
            $deploy->update(['status' => 'building']);
        }

        $this->project->update(['status' => 'building']);

        try {
            // ══════════════════════════════════════════════════════════════════════
            // TAHAP 1: GIT CLONE / PULL
            // ══════════════════════════════════════════════════════════════════════
            $this->appendLog($deploy, "\n> TAHAP 1: Git Clone / Pull repository...");

            // Cek apakah .git sudah ada di dalam folder tersebut menggunakan bash
            $isRepo = shell_exec("ls -d {$projectDir}/.git 2>&1");

            if (trim($isRepo) !== "" && !str_contains($isRepo, 'No such file')) {
                // KASUS 1: Sudah ada repository Git (Pull saja)
                $this->appendLog($deploy, '> Directory exists and valid. Pulling changes...');
                $this->executeShellCommand("chown -R root:root {$projectDir}", $deploy);
                $command = "cd {$projectDir} && git pull origin {$this->project->branch} 2>&1";
            } else {
                // KASUS 2: Folder sudah ada tapi bukan Git (Sisa deploy gagal / Folder sampah)
                if (file_exists($projectDir)) {
                    $this->appendLog($deploy, '> Found existing directory but not a repo. Cleaning up...');
                    $this->executeShellCommand("rm -rf {$projectDir}", $deploy);
                }

                // KASUS 3: Folder belum ada (Clone baru)
                $this->appendLog($deploy, '> Cloning fresh repository...');
                $command = "git clone -b {$this->project->branch} {$this->project->repo_source} {$projectDir} 2>&1";
            }

            $cloneResult = $this->executeShellCommand($command, $deploy);

            if (!$cloneResult['success']) {
                $this->appendLog($deploy, "\n> [ERROR] Git Clone/Pull failed. Aborting deployment.");
                $this->markAsFailed($deploy);
                return;
            }

            // ══════════════════════════════════════════════════════════════════════
            // TAHAP 2: BUILD FRAMEWORK (NPM / COMPOSER / PIP)
            // ══════════════════════════════════════════════════════════════════════
            $this->appendLog($deploy, "\n> TAHAP 2: Menjalankan Build Pipeline (" . strtoupper($this->project->framework) . ")...");

            if ($this->project->framework == 'laravel') {

                $this->appendLog($deploy, '> Using composer to install dependencies...');
                $this->executeShellCommand("cd {$projectDir} && composer install --no-interaction --prefer-dist --optimize-autoloader 2>&1", $deploy);

                $this->appendLog($deploy, '> Configuring .env and APP_KEY...');
                $envSetupCommand = "cd {$projectDir} && " .
                                   "cp .env.example .env 2>/dev/null || touch .env && " .
                                   "sed -i '/^APP_KEY=/d' .env && " .
                                   "echo -e '\nAPP_KEY=' >> .env && " .
                                   "php artisan key:generate 2>&1";
                $this->executeShellCommand($envSetupCommand, $deploy);

                if (file_exists("{$projectDir}/package.json")) {
                    $this->appendLog($deploy, '> Installing NPM dependencies for Laravel Frontend...');
                    $this->executeShellCommand("cd {$projectDir} && npm install --legacy-peer-deps 2>&1", $deploy);
                    $this->executeShellCommand("cd {$projectDir} && npm run build 2>&1", $deploy);
                }

                $this->appendLog($deploy, '> Caching framework bootstrap, configuration, and metadata...');
                $this->executeShellCommand("cd {$projectDir} && php artisan optimize:clear 2>&1", $deploy);
                $this->executeShellCommand("cd {$projectDir} && php artisan optimize 2>&1", $deploy);

            } elseif (in_array($this->project->framework, ['react', 'nextjs', 'node'])) {

                if (file_exists("{$projectDir}/package.json")) {
                    $this->appendLog($deploy, '> Installing NPM dependencies...');
                    $this->executeShellCommand("cd {$projectDir} && npm install 2>&1", $deploy);

                    if ($this->project->framework !== 'node') {
                        $this->appendLog($deploy, '> Building production assets...');
                        $this->executeShellCommand("cd {$projectDir} && npm run build 2>&1", $deploy);
                    }
                }

            } elseif ($this->project->framework == 'python') {

                if (file_exists("{$projectDir}/requirements.txt")) {
                    $this->appendLog($deploy, '> Installing Python dependencies...');
                    $this->executeShellCommand("cd {$projectDir} && pip install -r requirements.txt 2>&1", $deploy);
                }

            }

            // ══════════════════════════════════════════════════════════════════════
            // TAHAP 3: PERMISSION FIX (SANGAT PENTING)
            // ══════════════════════════════════════════════════════════════════════
            $this->appendLog($deploy, "\n> TAHAP 3: Menyesuaikan Hak Akses (Permissions)...");

            // Kembalikan hak milik folder ke www-data agar bisa diakses Nginx/PHP-FPM
            $this->executeShellCommand("chown -R www-data:www-data {$projectDir}", $deploy);
            $this->executeShellCommand("chmod -R 775 {$projectDir}", $deploy);

            // Khusus Laravel, pastikan folder rentan error permission mendapatkan akses penuh
            if ($this->project->framework == 'laravel') {
                $this->executeShellCommand("chmod -R 777 {$projectDir}/storage 2>/dev/null", $deploy);
                $this->executeShellCommand("chmod -R 777 {$projectDir}/bootstrap/cache 2>/dev/null", $deploy);
            }

            // ══════════════════════════════════════════════════════════════════════
            // SELESAI
            // ══════════════════════════════════════════════════════════════════════
            $this->appendLog($deploy, "\n> [SUCCESS] Deployment finished!");
            $this->appendLog($deploy, "> Live at: https://{$this->project->ryaze_domain}");

            $deploy->update(['status' => 'success']);
            $this->project->update(['status' => 'active']);

        } catch (\Exception $e) {
            $this->appendLog($deploy, "\n> [FATAL ERROR] " . $e->getMessage());
            $this->markAsFailed($deploy);
        }
    }

    /**
     * Eksekusi perintah shell dan catat outputnya.
     */
    private function executeShellCommand($command, $deploy)
    {
        exec($command, $output, $exitCode);
        $outputString = implode("\n", $output);

        if (trim($outputString) !== '') {
            $this->appendLog($deploy, $outputString);
        }

        return [
            'success' => $exitCode === 0,
            'output' => $outputString,
            'code' => $exitCode
        ];
    }

    /**
     * Tambahkan teks ke log deployment.
     */
    private function appendLog($deploy, $text)
    {
        $currentLog = $deploy->build_logs;
        $newLog = $currentLog . "\n" . $text;
        $deploy->update(['build_logs' => $newLog]);
    }

    /**
     * Tandai deployment sebagai gagal.
     */
    private function markAsFailed($deploy)
    {
        $deploy->update(['status' => 'failed']);
        $this->project->update(['status' => 'failed']);
    }
}
