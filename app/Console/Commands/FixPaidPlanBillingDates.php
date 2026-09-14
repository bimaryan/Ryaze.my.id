<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HostingBilling;
use Carbon\Carbon;

class FixPaidPlanBillingDates extends Command
{
    protected $signature = 'ryaze:fix-billing-dates {--dry-run : Preview saja tanpa mengubah data}';

    protected $description = 'Perbaiki next_due_date billing paid plan yang salah (terlanjur di-set +10 tahun, seharusnya +1 bulan dari tanggal dibuat).';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $this->info($isDryRun ? "=== DRY RUN (tidak ada perubahan) ===" : "=== Memulai perbaikan data billing ===");

        // Cari billing paid plan (bukan free) yang next_due_date-nya lebih dari 1 tahun ke depan
        // Ini adalah billings yang salah karena seharusnya monthly (1 bulan)
        $wrongBillings = HostingBilling::where('plan', '!=', 'free')
            ->whereIn('status', ['active', 'past_due'])
            ->where('next_due_date', '>', now()->addYear())
            ->get();

        $this->info("Ditemukan {$wrongBillings->count()} billing dengan tanggal yang perlu diperbaiki.");

        if ($wrongBillings->isEmpty()) {
            $this->info("Tidak ada billing yang perlu diperbaiki.");
            return 0;
        }

        $this->table(
            ['ID', 'User ID', 'Plan', 'Status', 'next_due_date (lama)', 'next_due_date (baru)'],
            $wrongBillings->map(function ($b) {
                $newDate = Carbon::parse($b->created_at)->addMonth();
                return [
                    $b->id,
                    $b->user_id,
                    $b->plan,
                    $b->status,
                    $b->next_due_date,
                    $newDate,
                ];
            })
        );

        if ($isDryRun) {
            $this->warn("DRY RUN selesai. Jalankan tanpa --dry-run untuk menerapkan perubahan.");
            return 0;
        }

        if (!$this->confirm("Apakah Anda yakin ingin memperbarui {$wrongBillings->count()} billing?")) {
            $this->info("Dibatalkan.");
            return 0;
        }

        $fixed = 0;
        foreach ($wrongBillings as $billing) {
            // Hitung next_due_date yang benar: 1 bulan dari tanggal billing dibuat
            $correctDueDate = Carbon::parse($billing->created_at)->addMonth();
            
            if (!$isDryRun) {
                $billing->update(['next_due_date' => $correctDueDate]);
            }

            $this->line("✓ Fixed billing ID {$billing->id} (User {$billing->user_id}, Plan {$billing->plan}): {$billing->next_due_date} → {$correctDueDate}");
            $fixed++;
        }

        $this->info("\nSelesai! {$fixed} billing berhasil diperbaiki.");
        $this->info("Jalankan 'php artisan hosting:suspend-expired' untuk suspend user yang sudah expired.");
        return 0;
    }
}
