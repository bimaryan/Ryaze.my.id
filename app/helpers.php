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
