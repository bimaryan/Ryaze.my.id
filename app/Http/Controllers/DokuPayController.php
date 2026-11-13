<?php

namespace App\Http\Controllers;

use App\Services\PaymentFulfillmentService;
use Illuminate\Http\Request;

class DokuPayController extends Controller
{
    /**
     * Titik masuk pembayaran DOKU Checkout.
     * Sesi checkout dibuat di sisi server, lalu user diteruskan ke
     * hosted payment page DOKU (semua metode: VA, e-Wallet, QRIS, kartu).
     */
    public function pay(Request $request)
    {
        $orderId = (string) $request->query('order_id');

        if ($orderId === '' || strlen($orderId) > 64) {
            abort(400);
        }

        $fulfillment = app(PaymentFulfillmentService::class);

        if (!$fulfillment->isPaid($orderId)) {
            // Invoice yang sempat ditandai failed (kedaluwarsa) diaktifkan
            // kembali karena user sedang mencoba membayar ulang.
            $this->reviveOrder($orderId);

            // Lanjutkan sesi checkout yang masih aktif, jangan buat transaksi
            // baru setiap kali user menekan tombol DOKU lagi.
            $activeSession = \App\Models\DokuCheckoutSession::query()
                ->where('order_id', $orderId)
                ->where('expired_at', '>', now())
                ->first();

            if ($activeSession && $activeSession->payment_url) {
                return redirect()->away($activeSession->payment_url);
            }

            $amount = $this->resolveAmount($orderId);
            if ($amount === null) {
                abort(404);
            }

            $checkout = \App\Services\DokuService::createCheckout(
                invoiceNumber: $orderId,
                amount: $amount,
                customer: array_filter([
                    'name' => $fulfillment->getOrderDetails($orderId)['user_name'] ?? null,
                    'email' => $fulfillment->getOrderDetails($orderId)['user_email'] ?? null,
                ]),
                callbackUrl: route('payment.doku.done', ['order_id' => $orderId]),
            );

            if (!$checkout) {
                return back()->with('error', 'Gagal membuat transaksi pembayaran DOKU. Silakan coba lagi.');
            }

            // Catat sesi checkout beserta batas waktunya, untuk penandaan
            // invoice sebagai failed oleh command payments:expire-doku.
            \App\Models\DokuCheckoutSession::updateOrCreate(
                ['order_id' => $orderId],
                [
                    'token_id' => $checkout['token_id'],
                    'payment_url' => $checkout['url'],
                    'expired_at' => $checkout['expired_at'],
                ],
            );

            return redirect()->away($checkout['url']);
        }

        return redirect($fulfillment->getOrderDetails($orderId)['redirect_url'])
            ->with('info', 'Invoice ' . $orderId . ' sudah lunas, tidak perlu dibayar lagi.');
    }

    /**
     * Halaman yang dibuka user setelah checkout DOKU selesai (callback_url).
     * Status pembayaran tetap ditentukan oleh HTTP Notification dari DOKU,
     * jadi di sini cukup mengarahkan user ke halaman terkait order-nya.
     */
    public function done(Request $request, string $order_id)
    {
        if (strlen($order_id) > 64) {
            abort(400);
        }

        $fulfillment = app(PaymentFulfillmentService::class);
        $details = $fulfillment->getOrderDetails($order_id);

        if (!$details) {
            abort(404);
        }

        $paid = $fulfillment->isPaid($order_id);

        return redirect($details['redirect_url'])->with(
            $paid ? 'success' : 'info',
            $paid
                ? 'Pembayaran untuk invoice ' . $order_id . ' berhasil diterima. Terima kasih!'
                : 'Pembayaran sedang diproses. Status akan diperbarui otomatis setelah DOKU mengonfirmasi.',
        );
    }

    /**
     * Kembalikan invoice yang ditandai failed (kedaluwarsa) ke status awal
     * karena user mencoba membuang sesi lama dan memulai pembayaran baru.
     */
    private function reviveOrder(string $orderId): void
    {
        if (str_starts_with($orderId, 'HST-INV-') || str_starts_with($orderId, 'HST-UPG-')) {
            \App\Models\HostingPayment::where('invoice_number', $orderId)
                ->where('status', 'failed')
                ->update(['status' => 'unpaid']);

            return;
        }

        if (str_starts_with($orderId, 'WLT-TOPUP-')) {
            \App\Models\WalletTransaction::where('reference_id', $orderId)
                ->where('status', 'failed')
                ->update(['status' => 'pending']);

            return;
        }

        if (str_starts_with($orderId, 'PRD-')) {
            \App\Models\DigitalPurchase::where('order_id', $orderId)
                ->where('status', 'failed')
                ->update(['status' => 'pending']);

            return;
        }

        if (str_starts_with($orderId, 'TIP-')) {
            \App\Models\Tip::where('order_id', $orderId)
                ->where('status', 'failed')
                ->update(['status' => 'pending']);

            return;
        }

        \App\Models\JokiPayment::where('invoice_number', $orderId)
            ->where('status', 'failed')
            ->update(['status' => 'unpaid']);
    }

    /**
     * Nominal tagihan selalu diambil dari database, bukan dari query string,
     * agar tidak bisa dikirim ulang dengan nominal yang berbeda.
     */
    private function resolveAmount(string $orderId): ?int
    {
        if (str_starts_with($orderId, 'HST-INV-') || str_starts_with($orderId, 'HST-UPG-')) {
            $payment = \App\Models\HostingPayment::where('invoice_number', $orderId)->first();
            if ($payment) {
                return (int) $payment->amount;
            }

            $billing = \App\Models\HostingBilling::where('invoice_number', $orderId)->first();

            return $billing ? (int) $billing->amount : null;
        }

        if (str_starts_with($orderId, 'WLT-TOPUP-')) {
            $tx = \App\Models\WalletTransaction::where('reference_id', $orderId)->first();

            return $tx ? (int) $tx->amount : null;
        }

        if (str_starts_with($orderId, 'PRD-')) {
            $purchase = \App\Models\DigitalPurchase::where('order_id', $orderId)->first();

            return $purchase ? (int) $purchase->amount : null;
        }

        if (str_starts_with($orderId, 'TIP-')) {
            $tip = \App\Models\Tip::where('order_id', $orderId)->first();

            return $tip ? (int) $tip->amount : null;
        }

        $joki = \App\Models\JokiPayment::where('invoice_number', $orderId)->first();

        return $joki ? (int) $joki->amount : null;
    }
}
