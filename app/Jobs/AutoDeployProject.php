<?php

namespace App\Jobs;

use App\Models\HostingProject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

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

        $baseDir = '/www/sites/hosting_clients';
        $subdomain = str_replace('.ryaze.my.id', '', $this->project->ryaze_domain);
        $projectDir = $baseDir.'/'.$subdomain;

        // 1. Persiapan Direktori
        $this->appendLog($deploy, '> Preparing deployment directory...');
        $this->executeShellCommand("mkdir -p {$baseDir}", $deploy);

        // 2. TAHAP 1: Git Clone atau Pull
        $this->appendLog($deploy, "\n> Cloning repository from " . $this->project->repo_source . '...');

        if (file_exists($projectDir)) {
            $this->appendLog($deploy, '> Directory exists. Marking as safe and pulling latest changes...');

            $this->executeShellCommand("cd {$projectDir} && git config --add safe.directory {$projectDir} 2>&1", $deploy);

            $command = "cd {$projectDir} && git pull origin {$this->project->branch} 2>&1";
        } else {
            $command = "git clone -b {$this->project->branch} {$this->project->repo_source} {$projectDir} 2>&1";
        }

        $cloneResult = $this->executeShellCommand($command, $deploy);

        if (! $cloneResult['success']) {
            $this->appendLog($deploy, "\n> [ERROR] Git Clone/Pull failed. Aborting deployment.");
            $this->markAsFailed($deploy);

            return;
        }

        // 3. FIX PERMISSIONS (Penting agar Nginx bisa akses file)
        $this->appendLog($deploy, '> Fixing file permissions...');
        $this->executeShellCommand("chown -R www-data:www-data {$projectDir} && chmod -R 775 {$projectDir} 2>&1", $deploy);

        // 4. TAHAP 2: Setup & Install Framework
        $this->appendLog($deploy, "\n> Setting up ".strtoupper($this->project->framework).' environment...');

        // LOGIKA NPM BASED (React, Node, NextJS, Vue)
        if (in_array($this->project->framework, ['react', 'node', 'nextjs', 'vue'])) {
            $this->appendLog($deploy, '> Installing NPM dependencies...');
            $npmCommand = "cd {$projectDir} && /usr/bin/npm install 2>&1";
            $this->executeShellCommand($npmCommand, $deploy);

            if (in_array($this->project->framework, ['react', 'nextjs', 'vue'])) {
                $this->appendLog($deploy, '> Running build script...');
                $buildCommand = "cd {$projectDir} && /usr/bin/npm run build 2>&1";
                $this->executeShellCommand($buildCommand, $deploy);
            }
        }
        // LOGIKA PYTHON (Flask)
        elseif ($this->project->framework == 'python') {
            $this->appendLog($deploy, '> Setting up Python Virtual Environment...');
            $pyCommand = "cd {$projectDir} && /usr/bin/python3 -m venv venv && source venv/bin/activate && /usr/bin/pip install -r requirements.txt 2>&1";
            $this->executeShellCommand($pyCommand, $deploy);
            $this->appendLog($deploy, '> Python dependencies installed.');
        }
        // LOGIKA HTML
        elseif ($this->project->framework == 'html') {
            $this->appendLog($deploy, '> HTML project detected. No build step required.');
        }

        // 5. Proses Otomatis Cloudflare DNS
        $this->appendLog($deploy, "\n> Configuring Cloudflare DNS for ".$this->project->ryaze_domain.'...');
        $this->createCloudflareDNS($deploy);

        // 6. TAHAP AKHIR: Sukses
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

    private function executeShellCommand($command, $deploy)
    {
        $output = shell_exec($command);
        if ($output) {
            $this->appendLog($deploy, trim($output));
        }

        if (strpos(strtolower($output), 'fatal:') !== false || strpos(strtolower($output), 'error:') !== false) {
            return ['success' => false, 'output' => $output];
        }

        return ['success' => true, 'output' => $output];
    }

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
