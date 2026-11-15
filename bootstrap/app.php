<?php

use App\Http\Middleware\CheckMaintenanceMode;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsureActiveHostingSubscription;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SecurityHeaders;
use App\Support\ErrorPages;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Hanya percayai proxy dari Cloudflare (jangan '*' supaya X-Forwarded-For tidak bisa dipalsukan)
        $middleware->trustProxies(at: [
            '127.0.0.1', '::1',
            '173.245.48.0/20', '103.21.244.0/22', '103.22.200.0/22', '103.31.4.0/22',
            '141.101.64.0/18', '108.162.192.0/18', '190.93.240.0/20', '188.114.96.0/20',
            '197.234.240.0/22', '198.41.128.0/17', '162.158.0.0/15', '104.16.0.0/13',
            '104.24.0.0/14', '172.64.0.0/13', '131.0.72.0/22',
            '2400:cb00::/32', '2606:4700::/32', '2803:f800::/32', '2405:b500::/32',
            '2405:8100::/32', '2a06:98c0::/29', '2c0f:f248::/32',
        ]);

        // Security headers (CSP, X-Frame-Options, etc.)
        $middleware->append(SecurityHeaders::class);

        // Maintenance Mode
        $middleware->append(CheckMaintenanceMode::class);

        // Inertia.js
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        // Role-based authorization alias
        $middleware->alias([
            'role' => CheckRole::class,
            'active_hosting' => EnsureActiveHostingSubscription::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') || ($request->expectsJson() && ! $request->header('X-Inertia'))) {
                return null;
            }

            if ($e instanceof AuthenticationException || $e instanceof ValidationException) {
                return null;
            }

            $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;

            if ($status === 500 && config('app.debug')) {
                return null;
            }

            $page = ErrorPages::forStatus($status);
            if (! $page) {
                return null;
            }

            try {
                $user = $request->user();
                Inertia::setRootView('inertia');

                return Inertia::render('Errors/Show', [
                    ...$page,
                    'seoTitle' => $page['code'].' — '.$page['title'].' · Ryaze',
                    'seoDescription' => $page['description'],
                    'canonical' => $request->url(),
                    'ogImage' => asset('og-image.png'),
                    'robots' => 'noindex, nofollow',
                    'navLinks' => config('nav.links'),
                    'footerLinks' => config('nav.footer_links'),
                    'csrf' => csrf_token(),
                    'auth' => [
                        'user' => $user ? [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $user->role,
                        ] : null,
                    ],
                    'flash' => ['success' => null, 'error' => null],
                    'requestPath' => '/'.ltrim($request->path(), '/'),
                    'requestMethod' => $request->method(),
                    'requestTime' => now()->format('d M Y · H:i'),
                ])->toResponse($request)->setStatusCode($status);
            } catch (\Throwable $renderError) {
                report($renderError);

                return null;
            }
        });
    })->create();
