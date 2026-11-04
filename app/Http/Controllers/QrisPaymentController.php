<?php

namespace App\Http\Controllers;

use App\Models\PaymentConfirmation;
use App\Services\PaymentConfirmationService;
use App\Services\PaymentFulfillmentService;
use App\Services\QrisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class QrisPaymentController extends Controller
{
    public function __construct(
        protected QrisService $qrisService,
        protected PaymentFulfillmentService $fulfillmentService,
        protected PaymentConfirmationService $confirmationService
    ) {}

    /**
     * Tampilkan halaman pembayaran QRIS Dinamis.
     */
    public function show(Request $request, string $orderId): InertiaResponse|\Illuminate\Http\RedirectResponse
    {
        $cleanOrderId = trim($orderId);

        $details = $this->fulfillmentService->getOrderDetails($cleanOrderId);
        if (!$details) {
            abort(404, 'Invoice atau tagihan tidak ditemukan.');
        }

        // Jika sudah lunas, langsung redirect ke halaman hasil / riwayat
        if ($this->fulfillmentService->isPaid($cleanOrderId)) {
            return redirect($details['redirect_url'])->with('success', 'Tagihan ' . $cleanOrderId . ' sudah lunas.');
        }

        $amount = (int) $details['amount'];

        // Generate dynamic payload & QR Code
        $dynamicPayload = $this->qrisService->generateDynamicPayload($amount, $cleanOrderId);
        $qrDataUri = $this->qrisService->generateQrDataUri($dynamicPayload, 300);
        $merchant = $this->qrisService->getMerchantInfo();

        // Ambil nomor WA dari settings untuk prop merchant
        $merchant['contact_whatsapp'] = \App\Models\Setting::val('contact_whatsapp', '');

        $pendingConfirmation = PaymentConfirmation::pending()
            ->where('order_number', $cleanOrderId)
            ->latest()
            ->first();

        $details['flash_info'] = session('info') ?? session('success');
        $details['confirmed_at'] = $pendingConfirmation?->created_at?->toIso8601String();

        return Inertia::render('Payment/Qris', [
            'order'          => $details,
            'amount'         => $amount,
            'dynamicPayload' => $dynamicPayload,
            'qrDataUri'      => $qrDataUri,
            'merchant'       => $merchant,
            'seoTitle'       => 'Pembayaran QRIS | Ryaze',
            'seoDescription' => 'Halaman pembayaran QRIS Dinamis untuk tagihan pesanan Anda di Ryaze.',
            'robots'         => 'noindex, nofollow',
        ]);
    }

    /**
     * AJAX endpoint untuk polling status pembayaran apakah sudah lunas.
     */
    public function checkStatus(string $orderId): JsonResponse
    {
        $cleanOrderId = trim($orderId);
        $details = $this->fulfillmentService->getOrderDetails($cleanOrderId);

        if (!$details) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        $isPaid = $this->fulfillmentService->isPaid($cleanOrderId);

        return response()->json([
            'success'      => true,
            'order_id'     => $cleanOrderId,
            'is_paid'      => $isPaid,
            'status'       => $isPaid ? 'paid' : ($details['status'] ?? 'unpaid'),
            'redirect_url' => $details['redirect_url'] ?? route('home'),
        ]);
    }

    /**
     * Konfirmasi pembayaran oleh pengguna / kirim notifikasi manual ke admin.
     */
    public function confirm(Request $request, string $orderId): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $cleanOrderId = trim($orderId);
        $details = $this->fulfillmentService->getOrderDetails($cleanOrderId);

        if (!$details) {
            abort(404);
        }

        if ($this->fulfillmentService->isPaid($cleanOrderId)) {
            return $this->respond($request, ['success' => true, 'already_paid' => true],
                'Tagihan ini sudah lunas, tidak perlu dikonfirmasi lagi.');
        }

        $validated = $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $confirmation = $this->confirmationService->register($details, $validated['note'] ?? null);

        return $this->respond($request, [
            'success'         => true,
            'confirmation_id' => $confirmation->hashid,
            'is_new'          => $confirmation->wasRecentlyCreated,
            'created_at'      => $confirmation->created_at->toIso8601String(),
        ], 'Konfirmasi pembayaran Anda telah dicatat. Admin sedang memverifikasi transaksi.');
    }

    private function respond(Request $request, array $payload, string $message): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json($payload + ['message' => $message]);
        }

        return back()->with('info', $message);
    }
}

