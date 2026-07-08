<?php

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
