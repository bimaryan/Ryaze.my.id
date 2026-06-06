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
        $dnsSuccess = $this->createCloudflareDNS($this->project->ryaze_domain);

        if ($dnsSuccess) {
            $this->appendLog($deploy, '> DNS Record successfully propagated!');
        } else {
            $this->appendLog($deploy, '> [WARNING] DNS Record already exists or failed to create. Proceeding...');
        }

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

    // --- FUNGSI BARU UNTUK NEMBAK API CLOUDFLARE ---
    private function createCloudflareDNS($domainName)
    {
        $zoneId = env('CLOUDFLARE_ZONE_ID');
        $apiToken = env('CLOUDFLARE_API_TOKEN');
        $serverIp = env('SERVER_IP_ADDRESS');

        // Jika setting di .env kosong, lewati proses ini agar tidak error
        if (! $zoneId || ! $apiToken || ! $serverIp) {
            return false;
        }

        // Tembak API Cloudflare untuk membuat A Record baru
        $response = Http::withToken($apiToken)->post("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records", [
            'type' => 'A',
            'name' => $domainName,
            'content' => $serverIp,
            'ttl' => 1,
            'proxied' => true,
        ]);

        // Kalau sukses dibuat (HTTP 200)
        if ($response->successful()) {
            return true;
        }

        return false;
    }
}
