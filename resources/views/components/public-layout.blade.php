@props([
    'title' => '',
    'description' => '',
    'withNav' => true,
    'withFooter' => true,
    'bodyClass' => 'bg-white font-sans antialiased text-slate-900 dark:bg-slate-950 dark:text-slate-100',
    'htmlClass' => 'scroll-smooth',
    'lang' => 'id',
    'links' => [],
    'active' => '',
    'ogImage' => '',
    'footerCompact' => false,
])

<!DOCTYPE html>
<html lang="{{ $lang }}" class="{{ $htmlClass }}">

<head>
    <meta charset="UTF-8">
    <!-- Dark mode: set class BEFORE CSS loads to prevent scrollbar FOUC -->
    <script>
        (function () {
            var stored = localStorage.getItem('ryaze-theme');
            if (stored === null) {
                stored = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            if (stored === 'dark') {
                document.documentElement.classList.add('dark');
                document.documentElement.style.colorScheme = 'dark';
            } else {
                document.documentElement.style.colorScheme = 'light';
            }
        })();
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @php
        $siteName = \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze Portal';
        $siteDescription = \App\Models\Setting::where('key', 'site_description')->value('value') ?? 'Layanan web hosting canggih dan jasa development profesional.';
        $siteFavicon = \App\Models\Setting::where('key', 'site_favicon')->value('value');
        $gaId = \App\Models\Setting::where('key', 'google_analytics_id')->value('value');
        $pageTitle = $title ? $title . ' - ' . $siteName : $siteName;
        $pageDescription = $description ?: $siteDescription;
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ url()->current() }}">

    @if ($siteFavicon)
        <link rel="icon" href="{{ asset('storage/' . $siteFavicon) }}">
        <link rel="apple-touch-icon" href="{{ asset('storage/' . $siteFavicon) }}">
    @endif

    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:locale" content="id_ID">
    @if ($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
        <meta property="twitter:image" content="{{ $ogImage }}">
    @elseif ($siteFavicon)
        <meta property="og:image" content="{{ asset('storage/' . $siteFavicon) }}">
        <meta property="twitter:image" content="{{ asset('storage/' . $siteFavicon) }}">
    @endif

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $pageTitle }}">
    <meta property="twitter:description" content="{{ $pageDescription }}">

    @if ($gaId)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}" nonce="{{ csp_nonce() }}"></script>
        <script nonce="{{ csp_nonce() }}">
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', '{{ $gaId }}');
        </script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script nonce="{{ csp_nonce() }}">
        window.ryazeToggleTheme = function (event) {
            const isDark = document.documentElement.classList.contains('dark');
            
            const toggleTheme = () => {
                var dark = document.documentElement.classList.toggle('dark');
                document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
                localStorage.setItem('ryaze-theme', dark ? 'dark' : 'light');
                document.dispatchEvent(new CustomEvent('theme:change', { detail: { dark: dark } }));
                document.querySelectorAll('[role="switch"][onclick*="ryazeToggleTheme"]').forEach(function(btn) {
                    btn.setAttribute('aria-checked', dark);
                });
            };

            if (!document.startViewTransition) {
                toggleTheme();
                return;
            }

            const x = event?.clientX ?? window.innerWidth / 2;
            const y = event?.clientY ?? window.innerHeight / 2;
            const endRadius = Math.hypot(Math.max(x, innerWidth - x), Math.max(y, innerHeight - y));

            const transition = document.startViewTransition(toggleTheme);

            transition.ready.then(() => {
                const clipPath = [
                    `circle(0px at ${x}px ${y}px)`,
                    `circle(${endRadius}px at ${x}px ${y}px)`
                ];
                
                document.documentElement.animate(
                    {
                        clipPath: isDark ? [...clipPath].reverse() : clipPath,
                    },
                    {
                        duration: 500,
                        easing: 'ease-in-out',
                        pseudoElement: isDark ? '::view-transition-old(root)' : '::view-transition-new(root)',
                    }
                );
            });
        };
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" nonce="{{ csp_nonce() }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}" crossorigin="anonymous" referrerpolicy="no-referrer" nonce="{{ csp_nonce() }}">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js" nonce="{{ csp_nonce() }}"></script>

    @stack('head')
    <style>
        /* View Transition API untuk animasi Dark Mode */
        ::view-transition-old(root),
        ::view-transition-new(root) {
            animation: none;
            mix-blend-mode: normal;
        }
        ::view-transition-old(root) {
            z-index: 2147483646;
        }
        ::view-transition-new(root) {
            z-index: 1;
        }
        .dark::view-transition-old(root) {
            z-index: 1;
        }
        .dark::view-transition-new(root) {
            z-index: 2147483646;
        }
    </style>
</head>

<body class="{{ $bodyClass }}">
    @if ($withNav)
        <x-public-nav :links="$links" :active="$active" />
    @endif

    {{ $slot }}

    @if ($withFooter)
        <x-public-footer :compact="$footerCompact" />
    @endif

    @include('components.hot-toast')
    @stack('scripts')
</body>

</html>
