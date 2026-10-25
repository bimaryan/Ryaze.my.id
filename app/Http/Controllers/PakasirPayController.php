<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PakasirPayController extends Controller
{
    /**
     * Titik masuk pembayaran Pakasir API v2.
     * Transaksi dibuat di sisi server (find or create), lalu user
     * diteruskan ke payment link: https://app.pakasir.com/pay-v2/{txn_id}
     */
    public function pay(Request $request)
    {
        $orderId = (string) $request->query('order_id');

        if ($orderId === '' || strlen($orderId) > 64) {
            abort(400);
        }

        $amount = $this->resolveAmount($orderId);
        if ($amount === null) {
            abort(404);
        }

        $payUrl = pakasir_pay_url($amount, $orderId);

        if (!$payUrl) {
            return back()->with('error', 'Gagal membuat transaksi pembayaran. Silakan coba lagi.');
        }

        return redirect()->away($payUrl);
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
