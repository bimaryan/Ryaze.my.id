<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\HostingBilling;
use App\Models\HostingProject;

class GiveGracePeriod extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ryaze:give-grace-period';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Berikan masa tenggang 1 bulan gratis kepada user lama yang memiliki project aktif tanpa tagihan.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Memulai pemberian Grace Period untuk user lama...");

        // Cari user yang memiliki setidaknya 1 project aktif, tapi belum punya billing aktif
        $users = User::whereHas('hostingProjects')
            ->whereDoesntHave('hostingBillings', function ($query) {
                $query->where('status', 'active');
            })
            ->get();

        if ($users->isEmpty()) {
            $this->info("Tidak ada user yang memenuhi kriteria (semua sudah punya langganan atau tidak punya project).");
            return;
        }

        $count = 0;
        foreach ($users as $user) {
            HostingBilling::create([
                'user_id' => $user->id,
                'hosting_project_id' => null, // Berlaku untuk akun
                'plan_name' => 'Grace Period 1 Bulan (Legacy User)',
                'amount' => 0,
                'billing_cycle' => 'monthly',
                'status' => 'active',
                'payment_method' => 'Grace Period',
                'invoice_number' => 'INV-GRACE-' . strtoupper(uniqid()),
                'next_due_date' => now()->addMonth(),
            ]);
            $count++;
            $this->line("- Diberikan ke User: {$user->name} ({$user->email})");
        }

        $this->info("Selesai! {$count} user berhasil mendapatkan Grace Period 1 bulan.");
    }
}
