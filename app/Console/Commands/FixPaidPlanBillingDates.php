<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HostingBilling;
use Carbon\Carbon;

class FixPaidPlanBillingDates extends Command
{
    protected $signature = 'ryaze:fix-billing-dates {--force : Langsung fix tanpa konfirmasi}';

    protected $description = 'Perbaiki next_due_date billing paid plan yang salah (terlanjur +10 tahun, seharusnya +1 bulan dari dibuat).';

    public function handle()
    {
        $this->info("=== Mencari billing paid plan dengan next_due_date > 1 tahun ke depan ===");

        // Cari semua billing paid (bukan free) yang next_due_date-nya lebih dari 1 tahun ke depan
        // Ini adalah billing yang salah di-set addYears(10) padahal seharusnya addMonth()
        $wrongBillings = HostingBilling::where('plan', '!=', 'free')
            ->whereIn('status', ['active', 'past_due'])
            ->where('next_due_date', '>', now()->addYear())
            ->orderBy('user_id')
            ->get();

        if ($wrongBillings->isEmpty()) {
            $this->info("Tidak ada billing yang perlu diperbaiki.");
            return 0;
        }

        $this->warn("Ditemukan {$wrongBillings->count()} billing yang perlu diperbaiki:");
        $this->table(
            ['ID', 'User ID', 'Plan', 'Dibuat', 'Due Date Lama', 'Due Date Baru'],
            $wrongBillings->map(function ($b) {
                $correctDate = Carbon::parse($b->created_at)->addMonth();
                return [
                    $b->id,
                    $b->user_id,
                    strtoupper($b->plan),
                    Carbon::parse($b->created_at)->format('d M Y'),
                    Carbon::parse($b->next_due_date)->format('d M Y'),
                    $correctDate->format('d M Y'),
                ];
            })
        );

        if (!$this->option('force')) {
            if (!$this->confirm("Lanjutkan perbaikan?", true)) {
                $this->info("Dibatalkan.");
                return 0;
            }
        }

        $fixed = 0;
        foreach ($wrongBillings as $billing) {
            $correctDate = Carbon::parse($billing->created_at)->addMonth();
            $billing->update(['next_due_date' => $correctDate]);
            $this->line("  Fixed Billing #{$billing->id} User {$billing->user_id} [{$billing->plan}]: -> {$correctDate->format('d M Y')}");
            $fixed++;
        }

        $this->info("Selesai! {$fixed} billing berhasil diperbaiki.");
        $this->info("Jalankan 'php artisan hosting:suspend-expired' untuk suspend akun yang sudah kadaluarsa.");
        return 0;
    }
}
