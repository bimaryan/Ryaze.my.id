<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
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
    <title>@yield('title') - Ryaze</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" nonce="{{ csp_nonce() }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}" crossorigin="anonymous" referrerpolicy="no-referrer" nonce="{{ csp_nonce() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js" nonce="{{ csp_nonce() }}"></script>
    @php
        $siteFavicon = \App\Models\Setting::where('key', 'site_favicon')->value('value');
    @endphp
    @if ($siteFavicon)
        <link rel="icon" href="{{ asset('storage/' . $siteFavicon) }}">
    @endif
    <style nonce="{{ csp_nonce() }}">
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: #ffffff;
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .dark body {
            background-color: #030712;
            color: #f8fafc;
        }
        /* Dot grid background */
        .bg-dot-pattern {
            position: fixed;
            inset: 0;
            background-image: radial-gradient(rgba(15, 23, 42, 0.1) 1px, transparent 1px);
            background-size: 24px 24px;
            z-index: 0;
            opacity: 0.4;
        }
        .dark .bg-dot-pattern {
            background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
        }
        /* Hero glow */
        .hero-glow {
            position: fixed;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.15) 0%, rgba(0,0,0,0) 70%);
            top: -200px;
            left: 50%;
            transform: translateX(-50%);
            pointer-events: none;
            z-index: 0;
        }
        /* Gradient text */
        .gradient-text {
            background: linear-gradient(to bottom, #0f172a, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .dark .gradient-text {
            background: linear-gradient(to bottom, #ffffff, #64748b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        /* Selection */
        ::-moz-selection { background: #4f46e5; color: #fff; }
        ::selection { background: #4f46e5; color: #fff; }
    </style>
    @stack('head')
</head>
<body>
    <div class="bg-dot-pattern"></div>
    <div class="hero-glow dark:block hidden"></div>

    <main class="relative z-10 w-full max-w-2xl mx-auto px-6 text-center">
        {{-- Decorative Icon --}}
        <div class="mb-8 inline-flex">
            <div class="w-20 h-20 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-200/50 dark:border-indigo-500/20 flex items-center justify-center rotate-3 shadow-sm">
                @yield('icon')
            </div>
        </div>

        {{-- Error Code --}}
        <h1 class="text-7xl md:text-9xl font-black gradient-text tracking-tighter mb-4 leading-none">
            @yield('code')
        </h1>

        {{-- Message --}}
        <h2 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-4 tracking-tight">
            @yield('message')
        </h2>

        {{-- Description --}}
        <p class="text-base md:text-lg text-slate-500 dark:text-slate-400 mb-10 max-w-lg mx-auto leading-relaxed">
            @yield('description')
        </p>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ url('/') }}" class="w-full sm:w-auto inline-flex justify-center items-center gap-2.5 px-8 py-3 text-sm font-semibold rounded-full text-white bg-slate-900 dark:bg-white dark:text-slate-900 hover:bg-slate-800 dark:hover:bg-slate-200 shadow-[0_0_20px_rgba(255,255,255,0.1)] dark:shadow-[0_0_20px_rgba(255,255,255,0.2)] transition-all">
                <i class="fa-solid fa-home text-[11px]"></i> Kembali ke Beranda
            </a>
            <button onclick="window.history.back()" class="w-full sm:w-auto inline-flex justify-center items-center gap-2.5 px-8 py-3 text-sm font-semibold rounded-full text-slate-700 dark:text-slate-200 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-white/10 backdrop-blur-sm transition-all">
                <i class="fa-solid fa-arrow-left text-[11px]"></i> Kembali
            </button>
        </div>

        {{-- Branding --}}
        <div class="mt-16 pt-8 border-t border-slate-200/50 dark:border-white/5">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-slate-400 dark:text-slate-600 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                @if ($siteFavicon)
                    <img src="{{ asset('storage/' . $siteFavicon) }}" alt="Logo" class="h-5 object-contain opacity-50">
                @else
                    <div class="bg-slate-200 dark:bg-white/10 rounded w-5 h-5 flex items-center justify-center">
                        <i class="fa-solid fa-code text-[8px] text-slate-400 dark:text-slate-500"></i>
                    </div>
                @endif
                <span class="text-xs font-bold tracking-tight">{{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze' }}</span>
            </a>
        </div>
    </main>

    <script nonce="{{ csp_nonce() }}">
        window.ryazeToggleTheme = function (event) {
            const isDark = document.documentElement.classList.contains('dark');
            const toggleTheme = () => {
                var dark = document.documentElement.classList.toggle('dark');
                document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
                localStorage.setItem('ryaze-theme', dark ? 'dark' : 'light');
            };
            if (!document.startViewTransition) { toggleTheme(); return; }
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
                    { duration: 500, easing: 'ease-in-out', pseudoElement: isDark ? '::view-transition-old(root)' : '::view-transition-new(root)' }
                );
            });
        };
    </script>
</body>
</html>
