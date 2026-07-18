<?php

namespace App\Jobs;

use App\Models\HostingProject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class InstallPhpVersion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 1;
    public int $timeout = 600;

    public function __construct(
        public HostingProject $project,
        public string $phpVersion,
        public int $deploymentId
    ) {}

    public function handle(): void
    {
        $deployment = $this->project->deployments()->find($this->deploymentId);

        if (! $deployment) {
            Log::error("[InstallPhp] Deployment #{$this->deploymentId} tidak ditemukan.");
            return;
        }

        $this->log($deployment, "> Starting PHP {$this->phpVersion} installation...");

        try {
            // ── Cek apakah versi sudah terinstall ─────────────────────────────
            $checkCmd  = "php{$this->phpVersion} --version 2>/dev/null | head -1";
            $checkOut  = trim(shell_exec($checkCmd) ?? '');

            if (str_contains($checkOut, "PHP {$this->phpVersion}")) {
                $this->log($deployment, "> PHP {$this->phpVersion} already installed: {$checkOut}");
                $this->applyVersion($deployment);
                return;
            }

            // ── Deteksi OS ────────────────────────────────────────────────────
            $osRelease = file_get_contents('/etc/os-release') ?: '';
            $isUbuntu  = str_contains($osRelease, 'Ubuntu') || str_contains($osRelease, 'Debian');

            if ($isUbuntu) {
                $this->installOnUbuntu($deployment);
            } else {
                throw new \RuntimeException('OS tidak didukung untuk instalasi PHP otomatis. Install manual via 1Panel.');
            }

            $this->applyVersion($deployment);

        } catch (\Throwable $e) {
            $this->log($deployment, "\n> [FATAL ERROR] " . $e->getMessage());
            $deployment->update(['status' => 'failed', 'build_logs' => $deployment->build_logs . "\n> PHP installation failed."]);
            Log::error("[InstallPhp] Failed: " . $e->getMessage());
        }
    }

    private function installOnUbuntu($deployment): void
    {
        $v = $this->phpVersion;

        $this->log($deployment, "> Detected Ubuntu/Debian. Adding Ondřej Surý PPA...");

        // Add PPA jika belum ada
        $this->exec("add-apt-repository -y ppa:ondrej/php 2>&1 | tail -5", $deployment);
        $this->exec("apt-get update -qq 2>&1 | tail -3", $deployment);

        $this->log($deployment, "> Installing PHP {$v} and common extensions...");

        $extensions = [
            "php{$v}",
            "php{$v}-cli",
            "php{$v}-fpm",
            "php{$v}-common",
            "php{$v}-mysql",
            "php{$v}-mbstring",
            "php{$v}-xml",
            "php{$v}-curl",
            "php{$v}-zip",
            "php{$v}-bcmath",
            "php{$v}-gd",
            "php{$v}-intl",
            "php{$v}-redis",
            "php{$v}-opcache",
        ];

        $pkgList = implode(' ', $extensions);
        $result  = $this->exec("DEBIAN_FRONTEND=noninteractive apt-get install -y {$pkgList} 2>&1 | tail -10", $deployment, true);

        // Verifikasi
        $verify = trim(shell_exec("php{$v} --version 2>/dev/null | head -1") ?? '');
        if (! str_contains($verify, "PHP {$v}")) {
            throw new \RuntimeException("PHP {$v} installation failed. Output: {$verify}");
        }

        $this->log($deployment, "> PHP {$v} installed: {$verify}");
    }

    private function applyVersion($deployment): void
    {
        $v          = $this->phpVersion;
        $subdomain  = explode('.', $this->project->ryaze_domain)[0];
        $projectDir = "/www/sites/hosting_clients/{$subdomain}";

        $this->log($deployment, "> Applying PHP {$v} to project...");

        // Set CLI php default untuk project (via update-alternatives jika ada)
        $altCheck = trim(shell_exec("update-alternatives --list php 2>/dev/null") ?? '');
        if ($altCheck) {
            $phpBin = "/usr/bin/php{$v}";
            if (file_exists($phpBin)) {
                $this->exec("update-alternatives --set php {$phpBin} 2>&1", $deployment);
            }
        }

        // Kalau ada .user.ini atau config PHP di project dir, update
        $userIni = "{$projectDir}/.user.ini";
        if (file_exists($userIni)) {
            $this->log($deployment, "> Existing .user.ini found, keeping.");
        }

        // Update project record
        $this->project->update(['php_version' => $v]);

        $this->log($deployment, "> PHP version set to {$v} for this project.");
        $this->log($deployment, "> [SUCCESS] PHP {$v} is now active. Redeploy project to apply changes fully.");

        $deployment->update(['status' => 'ready']);
    }

    private function exec(string $command, $deployment, bool $throwOnError = false): string
    {
        $fullCommand = "({$command}) 2>&1; echo \"__EXIT__:\$?\"";
        $raw         = shell_exec($fullCommand) ?? '';
        $exitCode    = 0;
        $output      = $raw;

        if (preg_match('/\n?__EXIT__:(\d+)\s*$/', $raw, $m)) {
            $exitCode = (int) $m[1];
            $output   = trim(substr($raw, 0, strrpos($raw, $m[0])));
        }

        if ($output) $this->log($deployment, $output);

        if ($throwOnError && $exitCode !== 0) {
            $lines   = array_filter(array_map('trim', explode("\n", $output)));
            $summary = implode(' | ', array_slice(array_values($lines), -3));
            throw new \RuntimeException("Command failed (exit {$exitCode}): {$summary}");
        }

        return $output;
    }

    private function log($deployment, string $text): void
    {
        $deployment->refresh();
        $deployment->update([
            'build_logs' => $deployment->build_logs . "\n" . $text,
        ]);
    }
}
