<?php

namespace App\Services;

use App\Models\PaymentConfirmation;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Log;

class PaymentConfirmationService
{
    /** Jeda antar konfirmasi untuk order yang sama, supaya admin tidak dibanjiri notifikasi. */
    private const REPEAT_WINDOW_MINUTES = 10;

    /**
     * Catat konfirmasi pembayaran dari user. Kalau dalam 10 menit terakhir sudah ada
     * konfirmasi pending untuk order yang sama, pakai ulang record itu (tanpa notifikasi lagi).
     *
     * @param array{order_id: string, type: string, title: string, amount: int, user_id: int|null, user_name: string|null} $details
     */
    public function register(array $details, ?string $note, string $channel = 'qris_dynamic'): PaymentConfirmation
    {
        $recent = PaymentConfirmation::pending()
            ->where('order_number', $details['order_id'])
            ->latest()
            ->first();

        if ($recent && $recent->created_at->gt(now()->subMinutes(self::REPEAT_WINDOW_MINUTES))) {
            return $recent;
        }

        $confirmation = PaymentConfirmation::create([
            'order_number' => $details['order_id'],
            'order_type'   => $details['type'],
            'amount'       => (int) $details['amount'],
            'user_id'      => $details['user_id'] ?? null,
            'note'         => $note,
            'channel'      => $channel,
            'status'       => PaymentConfirmation::STATUS_PENDING,
        ]);

        $this->notifyAdmins($confirmation, $details);
        $confirmation->update(['admin_notified_at' => now()]);

        return $confirmation;
    }

    /**
     * @param array{user_name?: string|null} $details
     */
    private function notifyAdmins(PaymentConfirmation $confirmation, array $details): void
    {
        $payersName = $details['user_name'] ?? ($confirmation->user?->name ?? 'Pengguna');
        $message = 'Konfirmasi pembayaran QRIS ' . $confirmation->order_number
            . ' sebesar Rp ' . number_format($confirmation->amount, 0, ',', '.')
            . ' dari ' . $payersName . '. Mohon diverifikasi.';

        $admins = User::whereIn('role', ['superadmin', $confirmation->adminRole()])->get();

        foreach ($admins as $admin) {
            try {
                $admin->notify(new SystemNotification($message, 'info'));

                if ($admin->phone) {
                    WhatsAppService::send($admin->phone, "*RYAZE KONFIRMASI PEMBAYARAN*\n\n" . $message);
                }
            } catch (\Throwable $e) {
                Log::warning('Gagal mengirim notifikasi konfirmasi pembayaran ke admin ' . $admin->id . ': ' . $e->getMessage());
            }
        }
    }

    /**
     * Setujui konfirmasi: tagihan dilunasi lewat jalur manual admin.
     */
    public function approve(PaymentConfirmation $confirmation, User $admin): bool
    {
        $fulfilled = app(PaymentFulfillmentService::class)->fulfill(
            $confirmation->order_number,
            'QRIS Dinamis',
            'Konfirmasi user #' . $confirmation->id
        );

        if (!$fulfilled) {
            return false;
        }

        $confirmation->update([
            'status'      => PaymentConfirmation::STATUS_VERIFIED,
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        $confirmation->user?->notify(new SystemNotification(
            'Pembayaran ' . $confirmation->order_number . ' berhasil diverifikasi admin. Terima kasih!',
            'success'
        ));

        return true;
    }

    /**
     * Tolak konfirmasi. Tagihan tetap unpaid sampai pembayaran benar diterima.
     */
    public function reject(PaymentConfirmation $confirmation, User $admin): void
    {
        $confirmation->update([
            'status'      => PaymentConfirmation::STATUS_REJECTED,
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        $confirmation->user?->notify(new SystemNotification(
            'Konfirmasi pembayaran ' . $confirmation->order_number . ' ditolak. Periksa kembali nominal dan invoice Anda.',
            'error'
        ));
    }
}
