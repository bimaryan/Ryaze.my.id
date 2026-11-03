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
                popup: 'shadow-none border border-[#E3EAF1] dark:border-[#1B2C44]',
                title: 'text-xl font-bold text-[#0F1A2A] dark:text-white',
                htmlContainer: 'text-sm text-[#525252] dark:text-white/60'
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

<body class="bg-[#F4F8FC] dark:bg-[#0A1322] font-sans antialiased text-[#0F1A2A] dark:text-white min-h-screen flex flex-col">
    @php
        $isWhoisPage = request()->routeIs('whois.*');
        $publicLogo = asset('icon/logo_ryaze.png');
    @endphp

    {{-- Navbar public — cermin PublicLayout.jsx: mono tab, border 1px, flat --}}
    <nav class="fixed top-0 inset-x-0 z-50 bg-white/85 dark:bg-[#0A1322]/85 backdrop-blur-xl border-b border-[#E3EAF1] dark:border-[#1B2C44]">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3 group shrink-0">
                <img src="{{ $publicLogo }}" alt="Ryaze" width="32" height="32"
                    class="h-8 w-8 object-contain ring-1 ring-[#E3EAF1] dark:ring-[#24384F] group-hover:ring-[#1D5B96] dark:group-hover:ring-[#6BA8DC] transition-all duration-300">
                <span class="font-black text-[#0F1A2A] dark:text-white text-[15px] tracking-[-0.02em] leading-none">RYAZE</span>
            </a>

            <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                <a href="{{ route('whois.index') }}"
                    class="relative hidden sm:inline-flex items-center h-16 px-4 font-mono text-[11px] uppercase tracking-[0.15em] transition-colors duration-200 {{ $isWhoisPage ? 'text-[#1D5B96] dark:text-[#6BA8DC]' : 'text-[#525252] dark:text-white/45 hover:text-[#0F1A2A] dark:hover:text-white' }}">
                    Cek Domain
                    <span class="absolute inset-x-4 bottom-0 h-[2px] bg-[#1D5B96] dark:bg-[#6BA8DC] transition-transform duration-300 origin-left {{ $isWhoisPage ? 'scale-x-100' : 'scale-x-0' }}"></span>
                </a>

                <button type="button" onclick="ryazeToggleTheme(event)" aria-label="Ganti tema"
                    class="w-9 h-9 flex items-center justify-center text-[#737373] dark:text-white/40 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] hover:bg-[#E8F0F9] dark:hover:bg-[#6BA8DC]/10 transition-colors">
                    <i class="fa-solid fa-moon text-[13px] dark:hidden"></i>
                    <i class="fa-solid fa-sun text-[13px] hidden dark:inline"></i>
                </button>

                @auth
                    <a href="{{ route('user_hosting.dashboard') }}"
                        class="ml-1 inline-flex items-center gap-2 px-4 py-2 bg-[#1D5B96] hover:bg-[#2A6FAD] text-white text-[12px] font-semibold uppercase tracking-[0.08em] transition-colors">
                        Dashboard <i class="fa-solid fa-arrow-right text-[9px] opacity-70"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="hidden sm:inline-flex items-center px-4 py-2 font-mono text-[11px] uppercase tracking-[0.15em] text-[#525252] dark:text-white/50 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors">Masuk</a>
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center px-4 py-2 bg-[#1D5B96] hover:bg-[#2A6FAD] text-white text-[12px] font-semibold uppercase tracking-[0.08em] transition-colors">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="pt-16 flex-1">
        @yield('content')
    </main>

    <footer class="mt-auto border-t border-[#E3EAF1] dark:border-[#1B2C44] bg-white dark:bg-[#0A1322]">
        <div class="max-w-6xl mx-auto px-6 py-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2.5">
                    <img src="{{ $publicLogo }}" alt="Ryaze" width="24" height="24" class="h-6 w-6 object-contain ring-1 ring-[#E3EAF1] dark:ring-[#24384F]">
                    <span class="font-black text-[#0F1A2A] dark:text-white text-sm tracking-tight">RYAZE</span>
                    <span class="font-mono text-[10px] text-[#94A3B4] dark:text-white/30 hidden sm:inline tracking-[0.1em]">&copy; {{ date('Y') }}</span>
                </div>
                <div class="flex items-center gap-6">
                    <a href="{{ url('/') }}" class="font-mono text-[10px] uppercase tracking-[0.15em] text-[#737373] dark:text-white/40 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors">Beranda</a>
                    <a href="{{ route('whois.index') }}" class="font-mono text-[10px] uppercase tracking-[0.15em] text-[#737373] dark:text-white/40 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors">Cek Domain</a>
                    <a href="{{ route('user_hosting.docs') }}" class="font-mono text-[10px] uppercase tracking-[0.15em] text-[#737373] dark:text-white/40 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors">Dokumentasi</a>
                </div>
            </div>
        </div>
    </footer>

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
