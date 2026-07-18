<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HostingBilling;
use App\Models\HostingProject;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SuspendExpiredHosting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hosting:suspend-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Suspend hosting projects that have passed their due date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Memulai pengecekan hosting expired...");

        $expiredBillings = HostingBilling::where('next_due_date', '<', Carbon::now())
            ->where('status', 'unpaid')
            ->get();

        $count = 0;

        foreach ($expiredBillings as $billing) {
            $user = $billing->user;
            if (!$user) continue;

            $projects = \App\Models\HostingProject::where('user_id', $user->id)
                ->where('status', 'active')
                ->get();

            foreach ($projects as $project) {
                // Ubah status ke suspended
                $project->status = 'suspended';
                $project->save();

                // Buat file .suspended di root directory (Nginx akan mendeteksinya)
                $subdomain = explode('.', $project->ryaze_domain)[0];
                $projectDir = "/www/sites/hosting_clients/{$subdomain}";
                $suspendFile = "{$projectDir}/.suspended";

                if (is_dir($projectDir)) {
                    touch($suspendFile);
                    @chmod($suspendFile, 0666);
                }

                // Catat log
                $project->deployments()->create([
                    'status' => 'error',
                    'build_logs' => "> SISTEM: Hosting disuspend otomatis karena tagihan langganan akun melewati batas waktu pembayaran.",
                ]);

                // Todo: Send email notification
                
                $count++;
                $this->info("Project {$project->project_name} disuspend.");
            }
        }

        $this->info("Selesai. Total {$count} project disuspend.");
    }
}
