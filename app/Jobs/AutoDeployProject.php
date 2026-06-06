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

    public $project;

    public function __construct(HostingProject $project)
    {
        $this->project = $project;
    }

    public function handle(): void
    {
        $deploy = $this->project->deployments()->latest()->first();
        $deploy->update(['status' => 'building']);

        // --- KONFIGURASI PATH SERVER ---
        // Ganti dengan path direktori tempat mas ingin menyimpan file klien di 1Panel
        $baseDir = '/www/sites/hosting_clients';

        $subdomain = str_replace('.ryaze.my.id', '', $this->project->ryaze_domain);

        // Buat nama folder berdasarkan hash ID atau nama proyek agar unik
        $projectDir = $baseDir . '/' . $subdomain;

        // 1. Persiapan Direktori
        $this->appendLog($deploy, '> Preparing deployment directory...');
        $this->executeShellCommand("mkdir -p {$baseDir}", $deploy);

        // 2. TAHAP 1: Git Clone Asli
        $this->appendLog($deploy, "\n> Cloning repository from ".$this->project->repo_source.'...');

        // Cek apakah folder sudah ada (kalau re-deploy)
        if (file_exists($projectDir)) {
            $this->appendLog($deploy, '> Directory exists. Pulling latest changes...');
            $command = "cd {$projectDir} && git pull origin {$this->project->branch} 2>&1";
        } else {
            $command = "git clone -b {$this->project->branch} {$this->project->repo_source} {$projectDir} 2>&1";
        }

        $cloneResult = $this->executeShellCommand($command, $deploy);

        if (! $cloneResult['success']) {
            $this->appendLog($deploy, "\n> [ERROR] Git Clone/Pull failed. Aborting deployment.");
            $this->markAsFailed($deploy);

            return; // Hentikan proses jika clone gagal
        }

        // 3. TAHAP 2: Setup & Install (Contoh untuk HTML/Node/React)
        $this->appendLog($deploy, "\n> Setting up ".strtoupper($this->project->framework).' environment...');

        if (in_array($this->project->framework, ['react', 'node', 'nextjs'])) {
            $this->appendLog($deploy, '> Installing NPM dependencies...');
            // Asumsi NPM terinstal di VPS mas
            $npmCommand = "cd {$projectDir} && npm install 2>&1";
            $this->executeShellCommand($npmCommand, $deploy);

            if (in_array($this->project->framework, ['react', 'nextjs'])) {
                $this->appendLog($deploy, '> Running build script...');
                $buildCommand = "cd {$projectDir} && npm run build 2>&1";
                $this->executeShellCommand($buildCommand, $deploy);
            }
        } elseif ($this->project->framework == 'html') {
            $this->appendLog($deploy, '> HTML project detected. No build step required.');
        }

        // 4. Proses Otomatis Cloudflare DNS
        $this->appendLog($deploy, "\n> Configuring Cloudflare DNS for ".$this->project->ryaze_domain.'...');
        $this->createCloudflareDNS($deploy);

        // 5. TAHAP AKHIR: Sukses
        $this->appendLog($deploy, "\n> [SUCCESS] Deployment Finished!\n> Application is live at: https://".$this->project->ryaze_domain);

        $deploy->update([
            'status' => 'ready',
            'deployed_at' => now(),
        ]);

        $this->project->update(['status' => 'active']);
    }

    private function appendLog($deploy, $text)
    {
        $deploy->update([
            'build_logs' => $deploy->build_logs."\n".$text,
        ]);
    }

    private function markAsFailed($deploy)
    {
        $deploy->update(['status' => 'failed']);
        $this->project->update(['status' => 'error']);
    }

    // --- FUNGSI BARU: Eksekutor Shell Command ---
    private function executeShellCommand($command, $deploy)
    {
        // Menggunakan shell_exec untuk menjalankan perintah Linux
        $output = shell_exec($command);

        // Log output aslinya ke layar terminal web
        if ($output) {
            $this->appendLog($deploy, trim($output));
        }

        // Cek secara sederhana apakah ada error fatal (Ini bisa disesuaikan lagi logika errornya)
        if (strpos(strtolower($output), 'fatal:') !== false || strpos(strtolower($output), 'error:') !== false) {
            return ['success' => false, 'output' => $output];
        }

        return ['success' => true, 'output' => $output];
    }

    // --- FUNGSI CLOUDFLARE ---
    private function createCloudflareDNS($deploy)
    {
        $domainName = $this->project->ryaze_domain;
        $zoneId = env('CLOUDFLARE_ZONE_ID');
        $apiToken = env('CLOUDFLARE_API_TOKEN');

        $tunnelUrl = preg_replace('#^https?://#', '', rtrim(env('CLOUDFLARE_TUNNEL_URL'), '/'));

        if (! $zoneId || ! $apiToken || ! $tunnelUrl) {
            $this->appendLog($deploy, '> [ERROR] Kredensial Cloudflare di .env belum lengkap!');

            return false;
        }

        $response = Http::withToken($apiToken)->post("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records", [
            'type' => 'CNAME',
            'name' => $domainName,
            'content' => $tunnelUrl,
            'ttl' => 1,
            'proxied' => true,
        ]);

        if ($response->successful()) {
            $this->appendLog($deploy, '> DNS Record successfully propagated!');

            return true;
        } else {
            $errorData = $response->json();
            $errorMessage = $errorData['errors'][0]['message'] ?? 'Unknown Cloudflare API Error';
            $this->appendLog($deploy, '> [API ERROR] Cloudflare menolak: '.$errorMessage);

            return false;
        }
    }
}
