<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HostingBilling;
use App\Models\User;

class FixDuplicateActiveBillings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hosting:fix-duplicate-billings {--dry-run : Preview changes without actually modifying data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix users yang punya lebih dari 1 billing aktif akibat upgrade paket. Hanya billing terbaru yang dipertahankan, sisanya dicancel.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('[DRY RUN] Tidak ada perubahan yang disimpan.');
        }

        // Cari user_id yang punya lebih dari 1 billing aktif
        $duplicateUserIds = HostingBilling::where('status', 'active')
            ->selectRaw('user_id, COUNT(*) as cnt')
            ->groupBy('user_id')
            ->having('cnt', '>', 1)
            ->pluck('user_id');

        if ($duplicateUserIds->isEmpty()) {
            $this->info('Tidak ada duplikasi billing aktif ditemukan. Semua data sudah bersih!');
            return 0;
        }

        $this->info("Ditemukan {$duplicateUserIds->count()} user dengan duplikasi billing aktif.");
        $this->newLine();

        $fixedCount = 0;

        foreach ($duplicateUserIds as $userId) {
            $user = User::find($userId);
            if (!$user) continue;

            // Ambil semua billing aktif, urut dari yang terbaru
            $activeBillings = HostingBilling::where('user_id', $userId)
                ->where('status', 'active')
                ->latest()
                ->get();

            $latestBilling = $activeBillings->first();
            $oldBillings   = $activeBillings->slice(1);

            $this->line("User: {$user->email} (ID: {$userId})");
            $this->line("  Billing aktif dipertahankan: ID #{$latestBilling->id} — Plan: {$latestBilling->plan} (next_due: {$latestBilling->next_due_date})");

            foreach ($oldBillings as $old) {
                $this->line("  → Cancel billing ID #{$old->id} — Plan: {$old->plan} (next_due: {$old->next_due_date})");
                if (!$isDryRun) {
                    $old->update(['status' => 'canceled']);
                }
            }

            // Pastikan storage limit user sudah sesuai dengan plan aktif terbaru
            $correctStorageMb = User::getPlanLimits($latestBilling->plan)['storage_mb'];
            $currentStorageMb = $user->hosting_storage_limit_mb ?? 0;

            // Hitung extra storage yang sudah dibeli user (di luar base plan)
            // Ambil base storage dari plan aktif itu sendiri
            $extraStorage = max(0, $currentStorageMb - $correctStorageMb);

            // Jika current storage lebih kecil dari base plan, berarti belum pernah diupdate
            if ($currentStorageMb < $correctStorageMb) {
                $this->line("  → Update storage: {$currentStorageMb} MB → {$correctStorageMb} MB (sesuai plan {$latestBilling->plan})");
                if (!$isDryRun) {
                    $user->update(['hosting_storage_limit_mb' => $correctStorageMb]);
                }
            } else {
                $this->line("  → Storage OK: {$currentStorageMb} MB (base: {$correctStorageMb} MB + extra: {$extraStorage} MB)");
            }

            $this->newLine();
            $fixedCount++;
        }

        if ($isDryRun) {
            $this->warn("[DRY RUN] {$fixedCount} user akan diperbaiki jika dijalankan tanpa --dry-run.");
        } else {
            $this->info("Selesai! {$fixedCount} user berhasil diperbaiki.");
        }

        return 0;
    }
}
