<?php

namespace App\Console\Commands;

use App\Models\DokuCheckoutSession;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Services\PaymentFulfillmentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireDokuPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:expire-doku';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tandai invoice DOKU yang melewati batas waktu pembayaran sebagai failed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fulfillment = app(PaymentFulfillmentService::class);

        $sessions = DokuCheckoutSession::query()
            ->where('expired_at', '<', now())
            ->get();

        $expired = 0;

        foreach ($sessions as $session) {
            $orderId = $session->order_id;

            // Sesi sudah lewat, hapus supaya order bisa memulai sesi baru.
            $session->delete();

            // Order yang ternyata sudah lunas tidak perlu apa-apa.
            if ($fulfillment->isPaid($orderId)) {
                continue;
            }

            if ($this->markExpired($orderId)) {
                $expired++;

                $details = $fulfillment->getOrderDetails($orderId);
                $user = isset($details['user_id']) && $details['user_id']
                    ? User::find($details['user_id'])
                    : null;

                $user?->notify(new SystemNotification(
                    'Invoice ' . $orderId . ' kedaluwarsa karena melewati batas waktu pembayaran. Silakan buat pembayaran ulang.',
                    'error',
                ));

                $this->info("Expired: {$orderId}");
            }
        }

        $this->info("Selesai. {$expired} invoice ditandai failed.");

        if ($expired > 0) {
            Log::info('payments:expire-doku menandai ' . $expired . ' invoice kedaluwarsa sebagai failed.');
        }

        return self::SUCCESS;
    }

    /**
     * Tandai order yang belum dibayar sebagai failed — logikanya ada di
     * PaymentFulfillmentService agar bisa dipakai bersama jalur QRIS Dinamis.
     */
    private function markExpired(string $orderId): bool
    {
        return app(PaymentFulfillmentService::class)->markExpired($orderId);
    }
}
