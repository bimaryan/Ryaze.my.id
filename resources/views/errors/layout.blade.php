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
    <title>@yield('code') — @yield('title') · Ryaze</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300..900;1,14..32,300..900&display=swap" rel="stylesheet" nonce="{{ csp_nonce() }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}" crossorigin="anonymous" referrerpolicy="no-referrer" nonce="{{ csp_nonce() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js" nonce="{{ csp_nonce() }}"></script>
    @php
        $siteFavicon = \App\Models\Setting::where('key', 'site_favicon')->value('value');
        $siteName    = \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze';
    @endphp
    @if ($siteFavicon)
        <link rel="icon" href="{{ asset('storage/' . $siteFavicon) }}">
    @endif

    <style nonce="{{ csp_nonce() }}">
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            background-color: #ffffff;
            color: #0f172a;
        }
        html.dark body {
            background-color: #030712;
            color: #f8fafc;
        }

        /* ── Layout ── */
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* ── Dot Grid ── */
        .dot-grid {
            position: fixed;
            inset: 0;
            background-image: radial-gradient(rgba(15,23,42,0.07) 1px, transparent 1px);
            background-size: 22px 22px;
            z-index: 0;
            pointer-events: none;
        }
        html.dark .dot-grid {
            background-image: radial-gradient(rgba(255,255,255,0.035) 1px, transparent 1px);
        }

        /* ── Glow blobs ── */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
            z-index: 0;
        }
        .blob-1 {
            width: 700px; height: 500px;
            background: radial-gradient(circle, rgba(99,102,241,0.13) 0%, transparent 70%);
            top: -200px; left: 50%;
            transform: translateX(-50%);
        }
        .blob-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(139,92,246,0.1) 0%, transparent 70%);
            bottom: -100px; right: -100px;
        }
        html.dark .blob-1 {
            background: radial-gradient(circle, rgba(99,102,241,0.18) 0%, transparent 70%);
        }
        html.dark .blob-2 {
            background: radial-gradient(circle, rgba(139,92,246,0.14) 0%, transparent 70%);
        }

        /* ── Main container ── */
        main {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 560px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
            text-align: center;
        }

        /* ── Glitch effect on error code ── */
        .error-code {
            font-size: clamp(5rem, 20vw, 9rem);
            font-weight: 900;
            letter-spacing: -0.04em;
            line-height: 1;
            position: relative;
            display: inline-block;
            background: linear-gradient(160deg, #0f172a 0%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            user-select: none;
        }
        html.dark .error-code {
            background: linear-gradient(160deg, #ffffff 0%, #475569 100%);
            -webkit-background-clip: text;
            background-clip: text;
        }
        /* Glitch layers */
        .error-code::before,
        .error-code::after {
            content: attr(data-text);
            position: absolute;
            inset: 0;
            background: inherit;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .error-code::before {
            left: 2px;
            background: linear-gradient(160deg, #6366f1 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            background-clip: text;
            opacity: 0;
            animation: glitch-a 5s infinite;
        }
        .error-code::after {
            left: -2px;
            background: linear-gradient(160deg, #ec4899 0%, #f43f5e 100%);
            -webkit-background-clip: text;
            background-clip: text;
            opacity: 0;
            animation: glitch-b 5s infinite 0.3s;
        }
        @keyframes glitch-a {
            0%,90%,100% { opacity:0; transform:translate(0); }
            91% { opacity:.5; transform:translate(-3px, 1px) skewX(-2deg); }
            93% { opacity:.5; transform:translate(3px,-1px) skewX(2deg); }
            95% { opacity:0; }
        }
        @keyframes glitch-b {
            0%,92%,100% { opacity:0; transform:translate(0); }
            93% { opacity:.4; transform:translate(3px, 2px); }
            95% { opacity:.4; transform:translate(-3px,-2px); }
            97% { opacity:0; }
        }

        /* ── Icon badge ── */
        .icon-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 72px; height: 72px;
            border-radius: 20px;
            background: rgba(99,102,241,0.08);
            border: 1px solid rgba(99,102,241,0.2);
            margin-bottom: 2rem;
            position: relative;
            transform: rotate(3deg);
            transition: transform 0.4s cubic-bezier(.16,1,.3,1);
        }
        .icon-badge:hover { transform: rotate(0deg) scale(1.05); }
        html.dark .icon-badge {
            background: rgba(99,102,241,0.12);
            border-color: rgba(99,102,241,0.25);
        }
        /* Subtle ring pulse */
        .icon-badge::before {
            content: '';
            position: absolute;
            inset: -6px;
            border-radius: 26px;
            border: 1.5px solid rgba(99,102,241,0.15);
            animation: ring-pulse 3s ease-in-out infinite;
        }
        @keyframes ring-pulse {
            0%,100% { opacity:0; transform:scale(.95); }
            50% { opacity:1; transform:scale(1); }
        }

        /* ── Message ── */
        .error-title {
            font-size: clamp(1.3rem, 4vw, 1.75rem);
            font-weight: 800;
            letter-spacing: -0.025em;
            color: #0f172a;
            margin-bottom: 0.75rem;
            line-height: 1.2;
        }
        html.dark .error-title { color: #f8fafc; }

        .error-desc {
            font-size: 1rem;
            line-height: 1.7;
            color: #64748b;
            max-width: 420px;
            margin: 0 auto 2.5rem;
        }
        html.dark .error-desc { color: #94a3b8; }

        /* ── Code pill (status code small) ── */
        .code-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.25rem 0.85rem;
            border-radius: 99px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            background: rgba(99,102,241,0.08);
            color: #6366f1;
            border: 1px solid rgba(99,102,241,0.2);
            margin-bottom: 1.25rem;
            font-family: ui-monospace, monospace;
        }
        html.dark .code-pill {
            background: rgba(99,102,241,0.12);
            border-color: rgba(99,102,241,0.25);
            color: #a5b4fc;
        }

        /* ── Buttons ── */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.75rem;
            border-radius: 99px;
            font-size: 0.85rem;
            font-weight: 700;
            background: #0f172a;
            color: #ffffff;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            white-space: nowrap;
        }
        .btn-primary:hover {
            background: #1e293b;
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }
        html.dark .btn-primary {
            background: #ffffff;
            color: #0f172a;
            box-shadow: 0 4px 16px rgba(255,255,255,0.1);
        }
        html.dark .btn-primary:hover {
            background: #e2e8f0;
            box-shadow: 0 8px 24px rgba(255,255,255,0.15);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.75rem;
            border-radius: 99px;
            font-size: 0.85rem;
            font-weight: 700;
            background: transparent;
            color: #475569;
            border: 1.5px solid rgba(100,116,139,0.25);
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .btn-secondary:hover {
            background: rgba(15,23,42,0.04);
            border-color: rgba(100,116,139,0.5);
            color: #0f172a;
            transform: translateY(-1px);
        }
        html.dark .btn-secondary {
            color: #94a3b8;
            border-color: rgba(255,255,255,0.1);
        }
        html.dark .btn-secondary:hover {
            background: rgba(255,255,255,0.05);
            border-color: rgba(255,255,255,0.2);
            color: #f8fafc;
        }

        .btn-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        /* ── Divider ── */
        .bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0; right: 0;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 10;
            border-top: 1px solid rgba(15,23,42,0.06);
        }
        html.dark .bottom-bar { border-color: rgba(255,255,255,0.05); }

        .brand-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: #94a3b8;
            font-size: 0.78rem;
            font-weight: 700;
            transition: color 0.2s;
        }
        .brand-link:hover { color: #6366f1; }
        html.dark .brand-link:hover { color: #a5b4fc; }
        .brand-icon {
            width: 24px; height: 24px;
            border-radius: 6px;
            background: rgba(99,102,241,0.1);
            border: 1px solid rgba(99,102,241,0.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.6rem;
            color: #6366f1;
        }
        html.dark .brand-icon {
            background: rgba(99,102,241,0.15);
            border-color: rgba(99,102,241,0.3);
            color: #a5b4fc;
        }

        /* ── Theme toggle ── */
        .theme-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px; height: 36px;
            border-radius: 10px;
            border: 1.5px solid rgba(100,116,139,0.2);
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.85rem;
        }
        .theme-toggle:hover {
            background: rgba(99,102,241,0.08);
            border-color: rgba(99,102,241,0.3);
            color: #6366f1;
        }
        html.dark .theme-toggle:hover {
            color: #a5b4fc;
        }

        ::-moz-selection { background: #4f46e5; color: #fff; }
        ::selection { background: #4f46e5; color: #fff; }

        /* ── View Transition (dark mode) ── */
        ::view-transition-old(root), ::view-transition-new(root) { animation:none; mix-blend-mode:normal; }
        ::view-transition-old(root) { z-index: 2147483646; }
        ::view-transition-new(root) { z-index: 1; }
        .dark::view-transition-old(root) { z-index: 1; }
        .dark::view-transition-new(root) { z-index: 2147483646; }
    </style>

    @stack('head')
</head>

<body>
    {{-- Background layers --}}
    <div class="dot-grid"></div>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <main>
        {{-- Icon badge --}}
        <div class="icon-badge">
            @yield('icon')
        </div>

        {{-- Status code pill --}}
        <div class="code-pill">
            <i class="fa-solid fa-circle-dot" style="font-size:.55rem;opacity:.7;"></i>
            HTTP @yield('code')
        </div>

        {{-- Big glitch code --}}
        <div class="error-code" data-text="@yield('code')">@yield('code')</div>

        {{-- Title + description --}}
        <h1 class="error-title">@yield('message')</h1>
        <p class="error-desc">@yield('description')</p>

        {{-- Action buttons --}}
        <div class="btn-group">
            <a href="{{ url('/') }}" class="btn-primary">
                <i class="fa-solid fa-house" style="font-size:.75rem;"></i>
                Beranda
            </a>
            <button onclick="window.history.back()" class="btn-secondary">
                <i class="fa-solid fa-arrow-left" style="font-size:.75rem;"></i>
                Kembali
            </button>
            @if(Request::is('*') && !str_contains(url()->current(), '/admin'))
            <a href="{{ url('/blog') }}" class="btn-secondary" style="display:none;" id="blog-btn">
                <i class="fa-solid fa-rss" style="font-size:.75rem;"></i>
                Blog
            </a>
            @endif
        </div>
    </main>

    {{-- Bottom bar --}}
    <div class="bottom-bar">
        <a href="{{ url('/') }}" class="brand-link">
            @if ($siteFavicon)
                <img src="{{ asset('storage/' . $siteFavicon) }}" alt="Logo" style="height:20px;opacity:.6;object-fit:contain;">
            @else
                <div class="brand-icon"><i class="fa-solid fa-code"></i></div>
            @endif
            {{ $siteName }}
        </a>

        <button
            onclick="ryazeToggleTheme(event)"
            class="theme-toggle"
            aria-label="Ganti tema">
            <i class="fa-solid fa-sun dark-hidden"></i>
            <i class="fa-solid fa-moon light-hidden" style="display:none;"></i>
        </button>
    </div>

    <script nonce="{{ csp_nonce() }}">
        // Sync theme icon
        (function(){
            const isDark = document.documentElement.classList.contains('dark');
            const sun = document.querySelector('.dark-hidden');
            const moon = document.querySelector('.light-hidden');
            if (isDark) { if(sun) sun.style.display='none'; if(moon) moon.style.display=''; }
            else { if(sun) sun.style.display=''; if(moon) moon.style.display='none'; }
        })();

        window.ryazeToggleTheme = function (event) {
            const isDark = document.documentElement.classList.contains('dark');
            const toggleTheme = () => {
                var dark = document.documentElement.classList.toggle('dark');
                document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
                localStorage.setItem('ryaze-theme', dark ? 'dark' : 'light');
                // sync icon
                const sun = document.querySelector('.dark-hidden');
                const moon = document.querySelector('.light-hidden');
                if (dark) { if(sun) sun.style.display='none'; if(moon) moon.style.display=''; }
                else { if(sun) sun.style.display=''; if(moon) moon.style.display='none'; }
            };
            if (!document.startViewTransition) { toggleTheme(); return; }
            const x = event?.clientX ?? window.innerWidth / 2;
            const y = event?.clientY ?? window.innerHeight / 2;
            const endRadius = Math.hypot(Math.max(x, innerWidth - x), Math.max(y, innerHeight - y));
            const transition = document.startViewTransition(toggleTheme);
            transition.ready.then(() => {
                const clipPath = [`circle(0px at ${x}px ${y}px)`, `circle(${endRadius}px at ${x}px ${y}px)`];
                document.documentElement.animate(
                    { clipPath: isDark ? [...clipPath].reverse() : clipPath },
                    { duration: 500, easing: 'ease-in-out', pseudoElement: isDark ? '::view-transition-old(root)' : '::view-transition-new(root)' }
                );
            });
        };
    </script>
</body>
</html>
