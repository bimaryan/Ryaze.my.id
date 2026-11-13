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
     * Tandai order yang belum dibayar sebagai failed sesuai tipe order-nya.
     */
    private function markExpired(string $orderId): bool
    {
        if (str_starts_with($orderId, 'HST-INV-') || str_starts_with($orderId, 'HST-UPG-')) {
            $payment = \App\Models\HostingPayment::where('invoice_number', $orderId)
                ->where('status', 'unpaid')
                ->first();

            if (!$payment) {
                return false;
            }

            $payment->update(['status' => 'failed']);

            return true;
        }

        if (str_starts_with($orderId, 'WLT-TOPUP-')) {
            $tx = \App\Models\WalletTransaction::where('reference_id', $orderId)
                ->where('status', 'pending')
                ->first();

            if (!$tx) {
                return false;
            }

            $tx->update(['status' => 'failed']);

            return true;
        }

        if (str_starts_with($orderId, 'PRD-')) {
            $purchase = \App\Models\DigitalPurchase::where('order_id', $orderId)
                ->where('status', 'pending')
                ->first();

            if (!$purchase) {
                return false;
            }

            $purchase->update(['status' => 'failed']);

            return true;
        }

        if (str_starts_with($orderId, 'TIP-')) {
            $tip = \App\Models\Tip::where('order_id', $orderId)
                ->where('status', 'pending')
                ->first();

            if (!$tip) {
                return false;
            }

            $tip->update(['status' => 'failed']);

            return true;
        }

        $joki = \App\Models\JokiPayment::where('invoice_number', $orderId)
            ->where('status', 'unpaid')
            ->first();

        if (!$joki) {
            return false;
        }

        $joki->update(['status' => 'failed']);

        return true;
    }
}
