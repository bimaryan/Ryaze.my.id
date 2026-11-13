<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DokuService
{
    /**
     * Path endpoint DOKU Checkout (non-SNAP API).
     * Dokumentasi: https://developers.doku.com/accept-payments/doku-checkout
     */
    public const CHECKOUT_PATH = '/checkout/v1/payment';

    /**
     * Apakah kredensial DOKU sudah tersedia di .env?
     */
    public static function configured(): bool
    {
        return (bool) config('services.doku.client_id') && (bool) config('services.doku.secret_key');
    }

    /**
     * Buat sesi checkout DOKU (hosted payment page) dan kembalikan URL pembayaran.
     *
     * @param string $invoiceNumber Nomor invoice (order.invoice_number), maks 64 karakter
     * @param int    $amount        Nominal IDR tanpa desimal
     * @param array  $customer      Opsional: ['name' => ..., 'email' => ..., 'phone' => ...]
     * @param string|null $callbackUrl URL untuk user kembali setelah selesai bayar
     * @param int    $dueMinutes    Batas waktu pembayaran dalam menit (default 1440 = 24 jam)
     *
     * @return array|null ['url' => string, 'token_id' => string, 'expired_date' => string] atau null saat gagal
     */
    public static function createCheckout(
        string $invoiceNumber,
        int $amount,
        array $customer = [],
        ?string $callbackUrl = null,
        int $dueMinutes = 1440,
    ): ?array {
        if (!self::configured()) {
            Log::error('DOKU Client ID / Secret Key belum diisi di .env');
            return null;
        }

        $body = [
            'order' => [
                'amount' => $amount,
                'invoice_number' => $invoiceNumber,
                'currency' => 'IDR',
                'language' => 'id',
            ],
            'payment' => [
                'payment_due_date' => $dueMinutes,
            ],
        ];

        // Opsional: paksa daftar metode tertentu via DOKU_PAYMENT_METHODS
        // (pisahkan dengan koma, mis. "QRIS,CREDIT_CARD,EMONEY_DANA").
        // Kosong = DOKU menampilkan semua kanal yang aktif di akun merchant.
        $methods = config('services.doku.payment_methods');
        if (!empty($methods)) {
            $body['payment']['payment_method_types'] = $methods;
        }

        if ($callbackUrl) {
            $body['order']['callback_url'] = $callbackUrl;
        }

        if (!empty($customer)) {
            $body['customer'] = array_filter([
                'id' => $customer['email'] ?? null,
                'name' => $customer['name'] ?? null,
                'email' => $customer['email'] ?? null,
                'phone' => $customer['phone'] ?? null,
            ]);
        }

        $rawBody = json_encode($body, JSON_UNESCAPED_SLASHES);

        $headers = self::requestHeaders($rawBody, self::CHECKOUT_PATH);
        if ($headers === null) {
            return null;
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout(20)
                ->withBody($rawBody, 'application/json')
                ->post(rtrim(config('services.doku.base_url'), '/') . self::CHECKOUT_PATH);
        } catch (\Throwable $e) {
            Log::error('DOKU checkout error: ' . $e->getMessage(), ['order_id' => $invoiceNumber]);
            return null;
        }

        $data = $response->json();
        $payUrl = $data['response']['payment']['url'] ?? null;

        if (!$response->successful() || !$payUrl) {
            Log::error('DOKU create checkout failed', [
                'order_id' => $invoiceNumber,
                'status_code' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        // expired_date format yyyyMMddHHmmss dengan zona UTC+7 (WIB)
        $expiredAt = null;
        if (!empty($data['response']['payment']['expired_date'])) {
            try {
                $expiredAt = \Illuminate\Support\Carbon::createFromFormat(
                    'YmdHis',
                    (string) $data['response']['payment']['expired_date'],
                    'Asia/Jakarta',
                );
            } catch (\Throwable $e) {
                $expiredAt = null;
            }
        }

        return [
            'url' => $payUrl,
            'token_id' => $data['response']['payment']['token_id'] ?? null,
            'expired_date' => $data['response']['payment']['expired_date'] ?? null,
            'expired_at' => $expiredAt ?? now()->addMinutes($dueMinutes),
        ];
    }

    /**
     * Verifikasi HTTP Notification dari DOKU.
     *
     * Signature di header dibangun ulang dari komponen:
     * Client-Id, Request-Id, Request-Timestamp, Request-Target (path URL notifikasi kita),
     * dan Digest (SHA-256 base64 dari raw body) — lalu HMAC-SHA256 base64 dengan Secret Key.
     */
    public static function verifyNotification(string $rawBody, string $targetPath, string $clientId, string $requestId, string $requestTimestamp, string $signatureHeader): bool
    {
        $secret = config('services.doku.secret_key');
        $expectedClientId = config('services.doku.client_id');

        if (!$secret || !$expectedClientId) {
            return false;
        }

        // Client-Id pada notifikasi harus milik merchant kita
        if (!hash_equals($expectedClientId, $clientId)) {
            return false;
        }

        // Buang prefix "HMACSHA256=" bila ada
        $signature = str_starts_with($signatureHeader, 'HMACSHA256=')
            ? substr($signatureHeader, strlen('HMACSHA256='))
            : $signatureHeader;

        $digest = base64_encode(hash('sha256', $rawBody, true));

        $component = "Client-Id:{$clientId}"
            . "\nRequest-Id:{$requestId}"
            . "\nRequest-Timestamp:{$requestTimestamp}"
            . "\nRequest-Target:{$targetPath}"
            . "\nDigest:{$digest}";

        $expected = base64_encode(hash_hmac('sha256', $component, $secret, true));

        return hash_equals($expected, (string) $signature);
    }

    /**
     * Bangun header request yang ditandatangani untuk endpoint DOKU.
     *
     * @return array|null Header siap pakai, atau null saat kredensial belum ada
     */
    private static function requestHeaders(string $rawBody, string $requestTarget): ?array
    {
        $clientId = config('services.doku.client_id');
        $secret = config('services.doku.secret_key');

        if (!$clientId || !$secret) {
            return null;
        }

        $requestId = (string) Str::uuid();
        // ISO8601 UTC+0 (format yang diwajibkan DOKU)
        $requestTimestamp = now()->utc()->format('Y-m-d\TH:i:s\Z');

        $digest = base64_encode(hash('sha256', $rawBody, true));

        $component = "Client-Id:{$clientId}"
            . "\nRequest-Id:{$requestId}"
            . "\nRequest-Timestamp:{$requestTimestamp}"
            . "\nRequest-Target:{$requestTarget}"
            . "\nDigest:{$digest}";

        return [
            'Client-Id' => $clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $requestTimestamp,
            'Signature' => 'HMACSHA256=' . base64_encode(hash_hmac('sha256', $component, $secret, true)),
        ];
    }
}
