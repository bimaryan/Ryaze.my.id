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

        // Status enum hosting_billings: active | past_due | canceled.
        // 'unpaid' BUKAN nilai yang valid di kolom ini, jadi cron lama tidak pernah jalan.
        $expiredBillings = HostingBilling::where('next_due_date', '<', Carbon::now())
            ->whereIn('status', ['active', 'past_due'])
            ->get();

        $count = 0;

        foreach ($expiredBillings as $billing) {
            $user = $billing->user;
            if (!$user) continue;

            // Tandai tagihan yang jatuh tempo sebagai past_due
            if ($billing->status !== 'past_due') {
                $billing->update(['status' => 'past_due']);

                // Generate tagihan baru jika bukan paket free
                if (strtolower($billing->plan) !== 'free') {
                    $planPrice = \App\Models\User::getPlanPrice($billing->plan);
                    if ($planPrice > 0) {
                        $existingUnpaid = \App\Models\HostingPayment::where('user_id', $user->id)
                            ->where('status', 'unpaid')
                            ->exists();
                            
                        if (!$existingUnpaid) {
                            \App\Models\HostingPayment::create([
                                'user_id' => $user->id,
                                'hosting_project_id' => null,
                                'invoice_number' => 'HST-INV-' . strtoupper(uniqid()),
                                'amount' => $planPrice,
                                'status' => 'unpaid',
                                'notes' => $billing->plan,
                            ]);
                        }
                    }
                }
            }

            $projects = \App\Models\HostingProject::where('user_id', $user->id)
                ->where('status', 'active')
                ->get();

            foreach ($projects as $project) {
                // Ubah status ke suspended
                $project->status = 'suspended';
                $project->save();

                // Buat file .suspended di root directory (Nginx akan mendeteksinya)
                $subdomain = explode('.', $project->ryaze_domain)[0];
                $projectDir = hosting_clients_dir() . "/{$subdomain}";
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $projectDir = substr(base_path(), 0, 2) . str_replace('/', '\\', $projectDir);
                }
                $suspendFile = "{$projectDir}/.suspended";

                if (is_dir($projectDir)) {
                    @touch($suspendFile);
                    @chmod($suspendFile, 0660);
                }

                // Catat log
                $project->deployments()->create([
                    'status' => 'failed',
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
