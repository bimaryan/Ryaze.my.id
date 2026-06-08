<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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

        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=()');
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; ".
            "script-src 'self' 'nonce-{$nonce}' 'unsafe-hashes' cdn.jsdelivr.net cdnjs.cloudflare.com kit.fontawesome.com static.cloudflareinsights.com; ".
            "style-src 'self' 'unsafe-inline' cdnjs.cloudflare.com; ".
            "font-src 'self' ka-f.fontawesome.com fonts.gstatic.com data:; ".
            "img-src 'self' data: blob:; ".
            "frame-src 'self' https://*.ryaze.my.id; ".
            "connect-src 'self' ka-f.fontawesome.com cloudflareinsights.com;"
        );

        return $response;
    }
}
