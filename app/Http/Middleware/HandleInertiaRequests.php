<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'inertia';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $navLinks = config('nav.links');
        $footerLinks = config('nav.footer_links');
        $isPrivate = $request->routeIs(
            'two-factor.*', 'portfolio.products.status', 'portfolio.tip.success'
        ) || collect($request->route()?->gatherMiddleware() ?? [])->contains(
            fn ($middleware) => is_string($middleware)
                && ($middleware === 'auth' || str_starts_with($middleware, 'auth:'))
        );

        return [
            ...parent::share($request),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'auth' => [
                'user' => fn () => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'role' => $request->user()->role,
                ] : null,
            ],
            'robots' => $isPrivate ? 'noindex, nofollow' : 'index, follow, max-image-preview:large',
            'csrf' => fn () => csrf_token(),
            'turnstileSiteKey' => config('services.turnstile.site_key'),
            'navLinks' => $navLinks,
            'footerLinks' => $footerLinks,
        ];
    }
}
