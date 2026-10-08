<!DOCTYPE html>
<html lang="id">

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
        $siteName = \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'RYAZE PORTAL';
        $siteDescription = \App\Models\Setting::where('key', 'site_description')->value('value') ?? 'Platform Layanan Joki dan Web Hosting Profesional Terpercaya.';
        $siteFavicon = \App\Models\Setting::where('key', 'site_favicon')->value('value');
        $gaId = \App\Models\Setting::where('key', 'google_analytics_id')->value('value');
    @endphp

    <title>@hasSection('title')@yield('title') - {{ $siteName }}@else{{ $siteName }}@endif</title>
    <meta name="description" content="@hasSection('seo_description')@yield('seo_description')@else{{ $siteDescription }}@endif">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@hasSection('title')@yield('title') - {{ $siteName }}@else{{ $siteName }}@endif">
    <meta property="og:description" content="@hasSection('seo_description')@yield('seo_description')@else{{ $siteDescription }}@endif">
    @if($siteFavicon)<meta property="og:image" content="{{ asset('storage/' . $siteFavicon) }}">@endif

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@hasSection('title')@yield('title') - {{ $siteName }}@else{{ $siteName }}@endif">
    <meta property="twitter:description" content="@hasSection('seo_description')@yield('seo_description')@else{{ $siteDescription }}@endif">
    @if($siteFavicon)<meta property="twitter:image" content="{{ asset('storage/' . $siteFavicon) }}">@endif

    <link rel="canonical" href="{{ url()->current() }}">
    @if($siteFavicon)
        <link rel="icon" href="{{ asset('storage/' . $siteFavicon) }}">
    @endif

    @if($gaId)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', '{{ $gaId }}');
        </script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}" crossorigin="anonymous" referrerpolicy="no-referrer" nonce="{{ csp_nonce() }}">

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" nonce="{{ csp_nonce() }}"></script>
    <script nonce="{{ csp_nonce() }}">
        window.Swal = Swal.mixin({
            customClass: {
                popup: 'rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700',
                title: 'text-xl font-bold text-slate-800 dark:text-slate-100',
                htmlContainer: 'text-sm text-slate-500 dark:text-slate-400'
            }
        });
    </script>

    {{-- AlpineJS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js" nonce="{{ csp_nonce() }}"></script>

    @stack('head')

    <style>
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

<body class="bg-white font-sans antialiased text-slate-900 dark:bg-[#0a0a14] dark:text-white">

    {{-- Public Navbar (standalone, tema menyatu dengan landing page) --}}
    <nav class="fixed top-0 inset-x-0 z-50 bg-white/90 dark:bg-[#1a1025]/90 backdrop-blur-md border-b border-[#e5e5e5] dark:border-[#2d1f42]">
        <div class="max-w-6xl mx-auto px-6 h-14 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <div class="w-7 h-7 bg-[#7c3aed] flex items-center justify-center">
                    <span class="text-white font-black text-xs">R</span>
                </div>
                <span class="font-black text-[#7c3aed] dark:text-white text-sm tracking-tight">{{ $siteName }}</span>
            </a>
            <div class="flex items-center gap-4">
                <a href="{{ route('whois.index') }}" class="text-[13px] font-medium text-[#666] dark:text-[#999] hover:text-[#7c3aed] dark:hover:text-white transition-colors hidden sm:inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i> Cek Domain
                </a>
                <a href="{{ route('speed-test.index') }}" class="text-[13px] font-medium text-[#666] dark:text-[#999] hover:text-[#7c3aed] dark:hover:text-white transition-colors hidden sm:inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-gauge-high text-xs"></i> Speed Test
                </a>
                <button type="button" onclick="ryazeToggleTheme(event)" aria-label="Ganti tema"
                    class="w-8 h-8 flex items-center justify-center text-[#999] dark:text-[#666] hover:text-[#7c3aed] dark:hover:text-white transition-colors">
                    <i class="fa-solid fa-moon text-sm"></i>
                </button>
            </div>
        </div>
    </nav>

    <main class="pt-14">
        @yield('content')
    </main>

    @stack('scripts')

    <script nonce="{{ csp_nonce() }}">
        window.ryazeToggleTheme = function (event) {
            const isDark = document.documentElement.classList.contains('dark');
            const toggleTheme = () => {
                var dark = document.documentElement.classList.toggle('dark');
                document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
                localStorage.setItem('ryaze-theme', dark ? 'dark' : 'light');
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
                    { clipPath: isDark ? [...clipPath].reverse() : clipPath },
                    {
                        duration: 500,
                        easing: 'ease-in-out',
                        pseudoElement: isDark ? '::view-transition-old(root)' : '::view-transition-new(root)'
                    }
                );
            });
        };
    </script>
</body>

</html>
