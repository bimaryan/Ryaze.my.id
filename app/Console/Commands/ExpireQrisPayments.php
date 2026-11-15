<?php

namespace App\Console\Commands;

use App\Models\QrisPaymentSession;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Services\PaymentFulfillmentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireQrisPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:expire-qris';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tandai invoice QRIS Dinamis yang melewati batas waktu pembayaran sebagai failed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fulfillment = app(PaymentFulfillmentService::class);

        $sessions = QrisPaymentSession::query()
            ->where('expires_at', '<', now())
            ->get();

        $expired = 0;

        foreach ($sessions as $session) {
            $orderId = $session->order_number;

            // Sesi kedaluwarsa selalu dibersihkan; halaman QRIS akan membuat
            // sesi (dan timer) baru ketika user membuka halamannya lagi.
            $session->delete();

            // Order yang ternyata sudah lunas tidak perlu apa-apa.
            if ($fulfillment->isPaid($orderId)) {
                continue;
            }

            if ($fulfillment->markExpired($orderId)) {
                $expired++;

                $details = $fulfillment->getOrderDetails($orderId);
                $user = isset($details['user_id']) && $details['user_id']
                    ? User::find($details['user_id'])
                    : null;

                $user?->notify(new SystemNotification(
                    'Invoice ' . $orderId . ' kedaluwarsa karena melewati batas waktu pembayaran QRIS. Silakan buka QRIS lagi untuk memperbarui kode pembayaran.',
                    'error',
                ));

                $this->info("Expired: {$orderId}");
            }
        }

        $this->info("Selesai. {$expired} invoice ditandai failed.");

        if ($expired > 0) {
            Log::info('payments:expire-qris menandai ' . $expired . ' invoice kedaluwarsa sebagai failed.');
        }

        return self::SUCCESS;
    }
}
