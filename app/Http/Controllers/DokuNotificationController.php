<?php

namespace App\Http\Controllers;

use App\Services\DokuService;
use App\Services\PaymentFulfillmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DokuNotificationController extends Controller
{
    /**
     * HTTP Notification dari DOKU (diatur di DOKU Back Office sebagai Notification URL):
     * POST /api/doku/notification
     *
     * Verifikasi signature DOKU → cross-check nominal dengan database →
     * serahkan ke PaymentFulfillmentService (idempoten) → jawab 2xx.
     */
    public function handle(Request $request)
    {
        $rawBody = $request->getContent();

        // Path notifikasi ini yang dipakai DOKU sebagai Request-Target saat menandatangani
        $targetPath = '/api/doku/notification';

        $verified = DokuService::verifyNotification(
            $rawBody,
            $targetPath,
            (string) $request->header('Client-Id', ''),
            (string) $request->header('Request-Id', ''),
            (string) $request->header('Request-Timestamp', ''),
            (string) $request->header('Signature', ''),
        );

        if (!$verified) {
            Log::warning('DOKU notification ditolak: signature tidak valid', [
                'client_id' => $request->header('Client-Id'),
                'request_id' => $request->header('Request-Id'),
            ]);

            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $body = json_decode($rawBody, true);

        $invoiceNumber = $body['order']['invoice_number'] ?? null;
        $transactionStatus = $body['transaction']['status'] ?? null;
        $amount = isset($body['order']['amount']) ? (int) $body['order']['amount'] : null;

        if (!$invoiceNumber) {
            Log::warning('DOKU notification tanpa invoice_number', ['body' => $body]);

            return response()->json(['message' => 'Missing invoice_number'], 400);
        }

        // Sesuai best practice DOKU: pada Checkout, user boleh mencoba metode lain
        // setelah gagal, sehingga notifikasi dengan status FAILED harus diabaikan.
        if ($transactionStatus !== 'SUCCESS') {
            Log::info('DOKU notification diabaikan (status bukan SUCCESS)', [
                'invoice_number' => $invoiceNumber,
                'status' => $transactionStatus,
            ]);

            return response()->json(['message' => 'OK']);
        }

        // Cross-check: nominal pada notifikasi harus sama dengan tagihan di database
        $expectedAmount = $this->resolveAmount($invoiceNumber);

        if ($expectedAmount === null) {
            Log::warning('DOKU notification: order tidak ditemukan', ['invoice_number' => $invoiceNumber]);

            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($amount !== $expectedAmount) {
            Log::warning('DOKU notification: nominal tidak cocok', [
                'invoice_number' => $invoiceNumber,
                'doku_amount' => $amount,
                'db_amount' => $expectedAmount,
            ]);

            return response()->json(['message' => 'Amount mismatch'], 400);
        }

        // Referensi eksternal untuk catatan (boleh kosong)
        $reference = $body['transaction']['doku_reference_number']
            ?? $body['transaction']['reference_number']
            ?? null;

        try {
            $fulfilled = app(PaymentFulfillmentService::class)->fulfill($invoiceNumber, 'DOKU', $reference);
        } catch (\Throwable $e) {
            Log::error('DOKU notification: fulfillment gagal', [
                'invoice_number' => $invoiceNumber,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Fulfillment error'], 500);
        }

        if (!$fulfilled) {
            Log::warning('DOKU notification: fulfillment gagal', ['invoice_number' => $invoiceNumber]);

            return response()->json(['message' => 'Order not found'], 404);
        }

        Log::info('DOKU notification: pembayaran diproses', [
            'invoice_number' => $invoiceNumber,
            'amount' => $amount,
        ]);

        return response()->json(['message' => 'OK']);
    }

    /**
     * Nominal tagihan dari database (pola sama dengan PakasirPayController).
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
