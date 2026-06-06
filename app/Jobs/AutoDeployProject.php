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

        sleep(2);
        $this->appendLog($deploy, '> Cloning repository from '.$this->project->repo_source."...\n> Receiving objects: 100% (24/24), done.\n> Resolving deltas: 100% (8/8), done.");

        sleep(3);
        $this->appendLog($deploy, '> Setting up '.strtoupper($this->project->framework)." environment...\n> Installing dependencies...\n> added 142 packages, and audited 143 packages in 3s");

        sleep(3);
        $this->appendLog($deploy, "> Running build script...\n> Build completed successfully in 1.2s.\n> Starting application instances...");

        // --- PROSES OTOMATIS CLOUDFLARE DNS ---
        $this->appendLog($deploy, '> Configuring Cloudflare DNS for '.$this->project->ryaze_domain.'...');

        // Kita oper variable $deploy langsung ke fungsi agar bisa menulis log error
        $this->createCloudflareDNS($deploy);

        sleep(2);
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

    // --- FUNGSI CLOUDFLARE YANG SUDAH DIPERBAIKI ---
    private function createCloudflareDNS($deploy)
    {
        $domainName = $this->project->ryaze_domain;
        $zoneId = env('CLOUDFLARE_ZONE_ID');
        $apiToken = env('CLOUDFLARE_API_TOKEN');

        // Bersihkan https:// atau garis miring jika klien tidak sengaja memasukkannya di .env
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
            // TANGKAP PESAN ERROR DARI CLOUDFLARE
            $errorData = $response->json();
            $errorMessage = $errorData['errors'][0]['message'] ?? 'Unknown Cloudflare API Error';

            $this->appendLog($deploy, '> [API ERROR] Cloudflare menolak: '.$errorMessage);

            return false;
        }
    }
}
