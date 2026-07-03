<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Generate nonce sekali per request
        $nonce = base64_encode(random_bytes(16));

        // Simpan ke request agar bisa diakses di view via helper
        $request->attributes->set('csp_nonce', $nonce);
        app()->instance('csp_nonce', $nonce);

        // Beritahu Vite agar inject nonce ke HMR inline scripts (dev mode)
        Vite::useCspNonce($nonce);

        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=()');
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; ".
            "script-src 'self' 'unsafe-inline' 'unsafe-hashes' cdn.jsdelivr.net cdnjs.cloudflare.com kit.fontawesome.com unpkg.com static.cloudflareinsights.com challenges.cloudflare.com http://127.0.0.1:5173 http://localhost:5173; ".
            "script-src-attr 'unsafe-inline'; ".
            "style-src 'self' 'unsafe-inline' cdnjs.cloudflare.com fonts.googleapis.com unpkg.com http://127.0.0.1:5173 http://localhost:5173; ".
            "font-src 'self' ka-f.fontawesome.com fonts.gstatic.com data: cdnjs.cloudflare.com; ".
            "img-src 'self' data: blob: https://ui-avatars.com; ".
            "frame-src 'self' *.ryaze.my.id challenges.cloudflare.com; ".
            "worker-src 'self' data: blob:; ".
            "connect-src 'self' ka-f.fontawesome.com cloudflareinsights.com cdnjs.cloudflare.com cdn.jsdelivr.net unpkg.com ws: wss:;"
        );

        return $response;
    }
}

