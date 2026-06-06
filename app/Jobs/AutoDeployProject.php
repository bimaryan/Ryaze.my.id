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

    // Menerima data project yang mau di-deploy
    public function __construct(HostingProject $project)
    {
        $this->project = $project;
    }

    public function handle(): void
    {
        $deploy = $this->project->deployments()->latest()->first();

        // 1. Ubah status jadi Building
        $deploy->update(['status' => 'building']);

        // 2. Tahap 1: Git Clone (Jeda 2 Detik)
        sleep(2);
        $this->appendLog($deploy, '> Cloning repository from '.$this->project->repo_source."...\n> Receiving objects: 100% (24/24), done.\n> Resolving deltas: 100% (8/8), done.");

        // 3. Tahap 2: Setup Environment & Dependencies (Jeda 3 Detik)
        sleep(3);
        $this->appendLog($deploy, '> Setting up '.strtoupper($this->project->framework)." environment...\n> Installing dependencies...\n> added 142 packages, and audited 143 packages in 3s");

        // 4. Tahap 3: Build & Start (Jeda 3 Detik)
        sleep(3);
        $this->appendLog($deploy, "> Running build script...\n> Build completed successfully in 1.2s.\n> Starting application instances...");

        // 5. Tahap Akhir: SUKSES!
        sleep(2);
        $this->appendLog($deploy, "\n> [SUCCESS] Deployment Finished!\n> Application is live at: https://".$this->project->ryaze_domain);

        // Ubah semua status menjadi sukses (Hijau)
        $deploy->update([
            'status' => 'ready',
            'deployed_at' => now(),
        ]);

        $this->project->update(['status' => 'active']);
    }

    // Fungsi kecil untuk menambah teks ke terminal secara bertahap
    private function appendLog($deploy, $text)
    {
        $deploy->update([
            'build_logs' => $deploy->build_logs."\n".$text,
        ]);
    }
}
