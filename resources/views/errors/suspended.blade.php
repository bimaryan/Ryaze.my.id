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
    <title>Layanan Disuspend - Ryaze</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" nonce="{{ csp_nonce() }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}" crossorigin="anonymous" referrerpolicy="no-referrer" nonce="{{ csp_nonce() }}">
    @php
        try {
            $siteFavicon = \App\Models\Setting::where('key', 'site_favicon')->value('value');
            $siteName = \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze';
        } catch (\Throwable $e) {
            $siteFavicon = null;
            $siteName = 'Ryaze';
        }
    @endphp
    @if ($siteFavicon)
        <link rel="icon" href="{{ asset('storage/' . $siteFavicon) }}">
    @endif
    <style nonce="{{ csp_nonce() }}">
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #ffffff;
            --panel: #ffffff;
            --grid-gap: #e5e5e5;
            --border: #e5e5e5;
            --text: #0a0a14;
            --primary: #7c3aed;
            --primary-dark: #6d28d9;
            --muted: #666666;
            --faint: #999999;
            --ghost: #bbbbbb;
        }
        .dark {
            --bg: #0a0a14;
            --panel: #0d0d18;
            --grid-gap: #1a1a2e;
            --border: #2d1f42;
            --text: #ffffff;
            --muted: rgba(255, 255, 255, 0.7);
            --faint: rgba(255, 255, 255, 0.5);
            --ghost: rgba(255, 255, 255, 0.4);
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
        }
        a { text-decoration: none; color: inherit; }

        .container { width: 100%; max-width: 72rem; margin: 0 auto; padding-left: 1.5rem; padding-right: 1.5rem; }

        {{-- NAV --}}
        .topbar { position: fixed; top: 0; left: 0; right: 0; z-index: 50; border-bottom: 1px solid var(--border); background: var(--bg); }
        .topbar-inner { height: 3.5rem; display: flex; align-items: center; justify-content: space-between; }
        .brand { display: inline-flex; align-items: center; gap: 0.625rem; }
        .brand-mark {
            width: 1.75rem; height: 1.75rem;
            background: var(--primary);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 0.75rem;
        }
        .brand-name { font-weight: 900; color: var(--primary); font-size: 0.875rem; letter-spacing: -0.02em; }
        .dark .brand-name { color: var(--text); }
        .btn-nav {
            padding: 0.375rem 1rem;
            background: var(--primary);
            color: #fff;
            font-size: 13px; font-weight: 600;
            transition: background 0.15s ease;
        }
        .btn-nav:hover { background: var(--primary-dark); }

        {{-- HERO --}}
        .hero { flex: 1; display: flex; flex-direction: column; justify-content: center; padding-top: 8rem; padding-bottom: 7rem; width: 100%; }
        @media (min-width: 1024px) { .hero { padding-top: 11rem; padding-bottom: 9rem; } }

        .badge {
            display: inline-block;
            width: max-content;
            padding: 4px 12px;
            background: var(--primary);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 2rem;
        }

        h1 {
            font-size: clamp(2.5rem, 6vw, 4.25rem);
            font-weight: 900;
            color: var(--primary);
            line-height: 1.05;
            letter-spacing: -0.03em;
            margin-bottom: 1.5rem;
        }
        h1 .accent { color: var(--primary-dark); }
        .dark h1 { color: var(--text); }
        .dark h1 .accent { color: var(--primary); }

        .lead {
            color: var(--muted);
            font-size: 1.05rem;
            line-height: 1.7;
            max-width: 36rem;
            margin-bottom: 2.5rem;
        }

        .actions { display: flex; flex-direction: column; gap: 0.75rem; }
        @media (min-width: 640px) { .actions { flex-direction: row; } }
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.625rem;
            padding: 0.75rem 1.75rem;
            font-size: 0.875rem; font-weight: 600;
            transition: background 0.15s ease, color 0.15s ease;
            text-align: center;
        }
        .btn i { font-size: 11px; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-outline { border: 1px solid var(--primary); color: var(--primary); }
        @media (min-width: 640px) { .btn-outline { color: var(--primary); background: transparent; } }
        .dark .btn-outline { color: var(--text); border-color: var(--primary); }
        .btn-outline:hover { background: var(--primary); color: #fff; }

        {{-- CAUSE GRID (gaya grid gap-px di landing) --}}
        .cause-grid {
            margin-top: 5rem;
            display: grid;
            grid-template-columns: 1fr;
            gap: 1px;
            background: var(--grid-gap);
            border: 1px solid var(--grid-gap);
        }
        @media (min-width: 640px) { .cause-grid { grid-template-columns: repeat(3, 1fr); } }
        .cause {
            background: var(--panel);
            padding: 1.75rem 1.5rem;
        }
        .cause i { font-size: 1rem; color: var(--primary); display: block; margin-bottom: 0.75rem; }
        .cause-title { font-size: 13px; font-weight: 700; color: var(--text); letter-spacing: -0.01em; }
        .cause-desc { font-size: 12.5px; line-height: 1.6; color: var(--faint); margin-top: 4px; }

        {{-- FOOTER --}}
        .footer { border-top: 1px solid var(--border); background: var(--bg); }
        .footer-inner { padding-top: 1.5rem; padding-bottom: 1.5rem; display: flex; flex-direction: column; align-items: center; justify-content: space-between; gap: 1rem; }
        @media (min-width: 768px) { .footer-inner { flex-direction: row; } }
        .footer-brand { display: flex; align-items: center; gap: 0.625rem; }
        .footer-mark {
            width: 1.5rem; height: 1.5rem;
            background: var(--primary);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 10px;
        }
        .footer-name { font-weight: 900; color: var(--primary); font-size: 0.875rem; letter-spacing: -0.02em; }
        .dark .footer-name { color: var(--text); }
        .footer-copy { font-size: 11px; color: var(--faint); }
        .footer-links { display: flex; align-items: center; gap: 1.25rem; font-size: 13px; color: var(--faint); }
        .footer-links a { transition: color 0.15s ease; }
        .footer-links a:hover { color: var(--primary); }
        .dark .footer-links a:hover { color: #fff; }

        ::-moz-selection { background: #7c3aed; color: #fff; }
        ::selection { background: #7c3aed; color: #fff; }

        @media (max-width: 640px) { .hide-sm { display: none; } }
    </style>
</head>
<body>
    {{-- NAV --}}
    <header class="topbar">
        <div class="container topbar-inner">
            <a href="{{ url('/') }}" class="brand">
                <span class="brand-mark">R</span>
                <span class="brand-name">{{ strtoupper($siteName) }}</span>
            </a>
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <a href="{{ url('/login') }}" style="font-size:13px; font-weight:500; color:var(--faint);" class="hide-sm">Masuk</a>
                <a href="{{ url('/') }}" class="btn-nav">Dashboard</a>
            </div>
        </div>
    </header>

    {{-- HERO --}}
    <main class="hero">
        <div class="container">
            <div class="badge">Akses Ditangguhkan</div>

            <h1>
                Layanan hosting<br>
                <span class="accent">sedang ditangguhkan.</span>
            </h1>

            <p class="lead">
                Website ini tidak dapat diakses karena layanan hostingnya telah ditangguhkan.
                Kemungkinan karena tagihan yang belum lunas atau masa aktif paket yang telah berakhir.
            </p>

            <div class="actions">
                <a href="{{ url('/') }}" class="btn btn-primary"><i class="fa-solid fa-home"></i> Kembali ke Beranda</a>
                <a href="{{ url('/login') }}" class="btn btn-outline"><i class="fa-solid fa-right-to-bracket"></i> Masuk ke Panel</a>
            </div>

            {{-- Penyebab umum --}}
            <div class="cause-grid">
                <div class="cause">
                    <i class="fa-solid fa-file-invoice"></i>
                    <p class="cause-title">Tagihan Belum Lunas</p>
                    <p class="cause-desc">Langganan otomatis disuspend saat jatuh tempo terlewat.</p>
                </div>
                <div class="cause">
                    <i class="fa-solid fa-calendar-xmark"></i>
                    <p class="cause-title">Masa Aktif Berakhir</p>
                    <p class="cause-desc">Paket hosting perlu diperpanjang untuk melanjutkan layanan.</p>
                </div>
                <div class="cause">
                    <i class="fa-solid fa-shield-halved"></i>
                    <p class="cause-title">Pelanggaran Ketentuan</p>
                    <p class="cause-desc">Konten atau aktivitas yang melanggar Syarat &amp; Ketentuan.</p>
                </div>
            </div>
        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="footer">
        <div class="container footer-inner">
            <div class="footer-brand">
                <span class="footer-mark">R</span>
                <span class="footer-name">{{ strtoupper($siteName) }}</span>
                <span class="footer-copy">&copy; {{ date('Y') }}</span>
            </div>
            <div class="footer-links">
                <a href="{{ url('/') }}">Beranda</a>
                <a href="{{ url('/login') }}">Masuk</a>
                <a href="{{ url('/register') }}">Daftar</a>
            </div>
        </div>
    </footer>
</body>
</html>
