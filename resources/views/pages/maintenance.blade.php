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
    @php
        $siteFavicon = \App\Models\Setting::where('key', 'site_favicon')->value('value');
    @endphp
    @if ($siteFavicon)
        <link rel="icon" href="{{ asset('storage/' . $siteFavicon) }}">
    @endif
    <style nonce="{{ csp_nonce() }}">
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #f8fafc;
            --text: #0f172a;
            --muted: #64748b;
            --soft: #475569;
            --card-bg: #ffffff;
            --card-border: rgba(15, 23, 42, 0.08);
            --icon-bg: #eef2ff;
            --icon-border: rgba(79, 70, 229, 0.15);
            --accent: #6366f1;
            --divider: rgba(15, 23, 42, 0.08);
            --title-grad-a: #0f172a;
            --title-grad-b: #64748b;
        }
        .dark {
            --bg: #030712;
            --text: #f8fafc;
            --muted: #94a3b8;
            --soft: #cbd5e1;
            --card-bg: rgba(255, 255, 255, 0.04);
            --card-border: rgba(255, 255, 255, 0.08);
            --icon-bg: rgba(99, 102, 241, 0.12);
            --icon-border: rgba(99, 102, 241, 0.25);
            --accent: #818cf8;
            --divider: rgba(255, 255, 255, 0.08);
            --title-grad-a: #ffffff;
            --title-grad-b: #64748b;
        }

        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: var(--bg);
            color: var(--text);
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0;
            padding: 3rem 1.5rem 2.5rem;
        }

        .backdrop {
            position: fixed;
            inset: 0;
            pointer-events: none;
        }
        .backdrop::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(currentColor 1px, transparent 1px);
            background-size: 26px 26px;
            color: rgba(15, 23, 42, 0.10);
            opacity: 0.5;
        }
        .dark .backdrop::before {
            color: rgba(148, 163, 184, 0.14);
        }
        .backdrop::after {
            content: '';
            position: absolute;
            top: -260px;
            left: 50%;
            transform: translateX(-50%);
            width: 720px;
            height: 480px;
            background: radial-gradient(ellipse, color-mix(in srgb, var(--accent) 22%, transparent) 0%, transparent 70%);
            filter: blur(10px);
        }

        .shell {
            width: 100%;
            max-width: 640px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 999px;
            border: 1px solid var(--icon-border);
            background: var(--icon-bg);
            color: var(--accent);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 1.75rem;
        }
        .badge .dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: var(--accent);
            animation: pulse-soft 1.8s ease-in-out infinite;
        }

        .hero-icon {
            position: relative;
            width: 76px;
            height: 76px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            background: var(--icon-bg);
            border: 1px solid var(--icon-border);
            transform: rotate(3deg);
            margin-bottom: 2rem;
        }
        .hero-icon .main-icon {
            font-size: 30px;
            color: var(--accent);
            transform: rotate(-3deg);
        }
        .hero-icon .gear-1,
        .hero-icon .gear-2 {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
        }
        .hero-icon .gear-1 {
            top: -11px; right: -11px;
            width: 26px; height: 26px;
            font-size: 12px;
            color: var(--accent);
            background: var(--bg);
            border: 1px solid var(--icon-border);
            animation: spin-slow 4s linear infinite;
        }
        .hero-icon .gear-2 {
            bottom: -9px; left: -9px;
            width: 22px; height: 22px;
            font-size: 10px;
            color: var(--muted);
            background: var(--bg);
            border: 1px solid var(--card-border);
            animation: spin-reverse 3s linear infinite;
        }

        h1 {
            font-size: clamp(2rem, 6vw, 2.9rem);
            font-weight: 900;
            letter-spacing: -0.03em;
            line-height: 1.1;
            background-image: linear-gradient(to bottom, var(--title-grad-a), var(--title-grad-b));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }

        .lead {
            max-width: 30rem;
            font-size: 0.975rem;
            line-height: 1.7;
            color: var(--muted);
            margin-bottom: 2.25rem;
        }

        .info-card {
            width: 100%;
            max-width: 30rem;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            text-align: left;
            padding: 16px 18px;
            border-radius: 16px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            backdrop-filter: blur(8px);
            margin: 0 auto 2.5rem;
        }
        .info-card > i {
            font-size: 14px;
            color: var(--accent);
            margin-top: 2px;
        }
        .info-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
        }
        .info-body {
            font-size: 13.5px;
            line-height: 1.65;
            color: var(--muted);
        }

        .refresh-note {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 12px;
            color: var(--muted);
        }
        .refresh-note i { font-size: 10px; opacity: 0.7; }

        .footer {
            margin-top: 4rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--divider);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .footer a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: -0.01em;
            transition: color 0.15s ease;
        }
        .footer a:hover { color: var(--accent); }
        .footer img { height: 18px; opacity: 0.75; }
        .footer .logo-fallback {
            width: 20px; height: 20px;
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            background: var(--icon-bg);
            color: var(--accent);
            font-size: 9px;
        }

        ::-moz-selection { background: #4f46e5; color: #fff; }
        ::selection { background: #4f46e5; color: #fff; }

        @keyframes spin-slow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @keyframes spin-reverse { from { transform: rotate(360deg); } to { transform: rotate(0deg); } }
        @keyframes pulse-soft { 0%, 100% { opacity: 1; } 50% { opacity: 0.45; } }
    </style>
</head>
<body>
    <div class="backdrop"></div>

    <div class="shell">
        {{-- Status badge --}}
        <div class="badge">
            <span class="dot"></span>
            503 — Service Unavailable
        </div>

        {{-- Decorative Icon --}}
        <div class="hero-icon">
            <i class="fa-solid fa-wrench main-icon"></i>
            <span class="gear-1"><i class="fa-solid fa-gear"></i></span>
            <span class="gear-2"><i class="fa-solid fa-gear"></i></span>
        </div>

        {{-- Title --}}
        <h1>Sistem Dalam Pemeliharaan</h1>

        {{-- Description --}}
        <p class="lead">
            Ryaze sedang melakukan pemeliharaan rutin dan pembaruan sistem untuk memberikan layanan yang lebih baik. Kami akan segera kembali!
        </p>

        {{-- Info Box --}}
        <div class="info-card">
            <i class="fa-solid fa-circle-info"></i>
            <div>
                <p class="info-title">Apa yang terjadi?</p>
                <p class="info-body">
                    Fitur klien sementara ditangguhkan selama proses update berlangsung. Administrator kami sedang bekerja keras menyelesaikannya secepat mungkin.
                </p>
            </div>
        </div>

        {{-- Auto-refresh notice --}}
        <div class="refresh-note">
            <i class="fa-solid fa-rotate"></i>
            Halaman ini akan memuat ulang secara otomatis setiap 60 detik.
        </div>
    </div>

    {{-- Branding --}}
    <div class="footer">
        <a href="{{ url('/') }}">
            @if ($siteFavicon)
                <img src="{{ asset('storage/' . $siteFavicon) }}" alt="Logo">
            @else
                <span class="logo-fallback"><i class="fa-solid fa-code"></i></span>
            @endif
            <span>{{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze' }}</span>
        </a>
    </div>

    <script nonce="{{ csp_nonce() }}">
        setTimeout(function() { location.reload(); }, 60000);
    </script>
</body>
</html>
