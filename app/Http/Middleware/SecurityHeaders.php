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
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=()');
        // Sesuaikan CSP dengan kebutuhan asset lu
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; ".
            "script-src 'self' cdn.jsdelivr.net cdnjs.cloudflare.com kit.fontawesome.com static.cloudflareinsights.com; ".
            "style-src 'self' 'unsafe-inline' cdnjs.cloudflare.com; ".
            "font-src 'self' ka-f.fontawesome.com fonts.gstatic.com data:; ".
            "img-src 'self' data: blob:; ".
            "connect-src 'self' ka-f.fontawesome.com cloudflareinsights.com;"
        );

        return $response;
    }
}
