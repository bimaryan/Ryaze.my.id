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
    <title>Sistem Dalam Pemeliharaan - Ryaze</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" nonce="{{ csp_nonce() }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}" crossorigin="anonymous" referrerpolicy="no-referrer" nonce="{{ csp_nonce() }}">
    @vite(['resources/css/app.css'])
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
        .dark body { background-color: #030712; color: #f8fafc; }
        .bg-dot-pattern {
            position: fixed; inset: 0;
            background-image: radial-gradient(rgba(15, 23, 42, 0.1) 1px, transparent 1px);
            background-size: 24px 24px; z-index: 0; opacity: 0.4;
        }
        .dark .bg-dot-pattern {
            background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
        }
        .hero-glow {
            position: fixed; width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.15) 0%, rgba(0,0,0,0) 70%);
            top: -200px; left: 50%; transform: translateX(-50%);
            pointer-events: none; z-index: 0;
        }
        .gradient-text {
            background: linear-gradient(to bottom, #0f172a, #94a3b8);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .dark .gradient-text {
            background: linear-gradient(to bottom, #ffffff, #64748b);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        ::-moz-selection { background: #4f46e5; color: #fff; }
        ::selection { background: #4f46e5; color: #fff; }
        @keyframes spin-slow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @keyframes spin-reverse { from { transform: rotate(360deg); } to { transform: rotate(0deg); } }
        @keyframes pulse-soft { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
        .animate-spin-slow { animation: spin-slow 4s linear infinite; }
        .animate-spin-reverse { animation: spin-reverse 3s linear infinite; }
        .animate-pulse-soft { animation: pulse-soft 2s ease-in-out infinite; }
    </style>
</head>
<body>
    <div class="bg-dot-pattern"></div>
    <div class="hero-glow dark:block hidden"></div>

    <main class="relative z-10 w-full max-w-2xl mx-auto px-6 text-center">
        {{-- Decorative Icon --}}
        <div class="mb-8 inline-flex">
            <div class="w-20 h-20 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-200/50 dark:border-indigo-500/20 flex items-center justify-center rotate-3 shadow-sm relative">
                <i class="fa-solid fa-wrench text-4xl text-indigo-500 dark:text-indigo-400 -rotate-3"></i>
                <i class="fa-solid fa-gear text-indigo-400/60 dark:text-indigo-400/40 text-sm absolute -top-2 -right-2 animate-spin-slow"></i>
                <i class="fa-solid fa-gear text-slate-300 dark:text-slate-500 text-[10px] absolute -bottom-1 -left-1 animate-spin-reverse"></i>
            </div>
        </div>

        {{-- Title --}}
        <h1 class="text-4xl md:text-5xl font-black gradient-text tracking-tighter mb-4 leading-none">
            Sistem Dalam Pemeliharaan
        </h1>

        {{-- Description --}}
        <p class="text-base md:text-lg text-slate-500 dark:text-slate-400 mb-10 max-w-lg mx-auto leading-relaxed">
            Ryaze sedang melakukan pemeliharaan rutin dan pembaruan sistem untuk memberikan layanan yang lebih baik. Kami akan segera kembali!
        </p>

        {{-- Info Box --}}
        <div class="max-w-lg mx-auto p-4 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm text-sm text-slate-600 dark:text-slate-300 text-left flex items-start gap-3 backdrop-blur-sm">
            <i class="fa-solid fa-circle-info text-indigo-500 dark:text-indigo-400 mt-0.5"></i>
            <div>
                <strong class="text-slate-700 dark:text-slate-200 block mb-1">Apa yang terjadi?</strong>
                Fitur klien sementara ditangguhkan selama proses update berlangsung. Administrator kami sedang bekerja keras menyelesaikannya secepat mungkin.
            </div>
        </div>

        {{-- Auto-refresh notice --}}
        <div class="mt-6 inline-flex items-center gap-2 text-xs text-slate-400 dark:text-slate-600">
            <i class="fa-solid fa-rotate text-[10px]"></i>
            Halaman ini akan memuat ulang secara otomatis setiap 60 detik.
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
        setTimeout(function() { location.reload(); }, 60000);
    </script>
</body>
</html>
