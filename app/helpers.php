<?php

if (!function_exists('hosting_clients_dir')) {
    /**
     * Direktori utama penyimpanan project hosting user.
     * Baca dari config/hosting.php (env HOSTING_CLIENTS_DIR).
     * Di Windows (dev) fallback ke storage/app/hosting_clients.
     */
    function hosting_clients_dir(): string
    {
        $dir = rtrim((string) config('hosting.client_dir', '/www/sites/hosting_clients'), '/\\');

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return rtrim((string) storage_path('app/hosting_clients'), '/\\');
        }

        return $dir;
    }
}

if (!function_exists('csp_nonce')) {
    function csp_nonce(): string
    {
        try {
            return app('csp_nonce') ?? '';
        } catch (\Throwable $e) {
            return '';
        }
    }
}

if (!function_exists('pakasir_create_transaction')) {
    /**
     * Buat (atau ambil) transaksi Pakasir API v2.
     * Endpoint: POST /api/v2/create-transaction/{slug}/{order_id}
     * API ini bersifat "find or create" — pemanggilan berulang dengan
     * parameter & body yang sama selalu mengembalikan transaksi yang sama.
     * Rate limit: 2 request per detik.
     */
    function pakasir_create_transaction(int $amount, string $orderId, string $method = 'payment_link'): ?array
    {
        $apiKey = config('services.pakasir.api_key');
        $slug = config('services.pakasir.slug', 'ryaze');

        if (!$apiKey) {
            \Illuminate\Support\Facades\Log::error('Pakasir API Key is missing in .env');
            return null;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders(['X-Api-Key' => $apiKey])
                ->timeout(15)
                ->post("https://app.pakasir.com/api/v2/create-transaction/{$slug}/{$orderId}", [
                    'method' => $method,
                    'amount' => $amount,
                ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Pakasir create-transaction error: ' . $e->getMessage());
            return null;
        }

        if (!$response->successful()) {
            \Illuminate\Support\Facades\Log::error('Pakasir create-transaction failed', [
                'order_id' => $orderId,
                'status_code' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        return $response->json();
    }
}

if (!function_exists('pakasir_pay_url')) {
    /**
     * Bangun URL pembayaran Pakasir API v2 (payment link).
     * Format: https://app.pakasir.com/pay-v2/{txn_id}?redirect={url}
     * Opsional: $redirect diarahkan setelah pembayaran selesai.
     */
    function pakasir_pay_url(int $amount, string $orderId, ?string $redirect = null): ?string
    {
        $txn = pakasir_create_transaction($amount, $orderId);
        if (!$txn || empty($txn['txn_id'])) {
            return null;
        }

        $params = [];
        if ($redirect) {
            $params['redirect'] = $redirect;
        }

        return 'https://app.pakasir.com/pay-v2/' . $txn['txn_id'] . ($params ? '?' . http_build_query($params) : '');
    }
}

if (!function_exists('get_framework_icon')) {
    function get_framework_icon($framework)
    {
        $framework = strtolower($framework ?? '');
        $icons = [
            'html' => ['icon' => 'fa-brands fa-html5', 'color' => 'text-orange-500'],
            'php' => ['icon' => 'fa-brands fa-php', 'color' => 'text-indigo-500'],
            'laravel' => ['icon' => 'fa-brands fa-laravel', 'color' => 'text-red-500'],
            'react' => ['icon' => 'fa-brands fa-react', 'color' => 'text-sky-500'],
            'nextjs' => ['icon' => 'fa-brands fa-node-js', 'color' => 'text-slate-800'],
            'node' => ['icon' => 'fa-brands fa-node', 'color' => 'text-emerald-500'],
            'vue' => ['icon' => 'fa-brands fa-vuejs', 'color' => 'text-emerald-500'],
        ];

        if (isset($icons[$framework])) {
            return $icons[$framework]['icon'] . ' ' . $icons[$framework]['color'];
        }

        return 'fa-solid fa-code text-slate-500';
    }
}
