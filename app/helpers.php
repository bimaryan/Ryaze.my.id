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

if (!function_exists('pakasir_pay_url')) {
    /**
     * Bangun URL pembayaran Pakasir.
     * Format: https://app.pakasir.com/pay/{slug}/{amount}?order_id={orderId}
     * Opsional: $redirect diarahkan setelah pembayaran selesai.
     */
    function pakasir_pay_url(int $amount, string $orderId, ?string $redirect = null): string
    {
        $slug = config('services.pakasir.slug', 'ryaze');

        $params = ['order_id' => $orderId];
        if ($redirect) {
            $params['redirect'] = $redirect;
        }

        return 'https://app.pakasir.com/pay/' . $slug . '/' . $amount . '?' . http_build_query($params);
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
            'python' => ['icon' => 'fa-brands fa-python', 'color' => 'text-yellow-500'],
            'node' => ['icon' => 'fa-brands fa-node', 'color' => 'text-emerald-500'],
            'vue' => ['icon' => 'fa-brands fa-vuejs', 'color' => 'text-emerald-500'],
        ];

        if (isset($icons[$framework])) {
            return $icons[$framework]['icon'] . ' ' . $icons[$framework]['color'];
        }

        return 'fa-solid fa-code text-slate-500';
    }
}
