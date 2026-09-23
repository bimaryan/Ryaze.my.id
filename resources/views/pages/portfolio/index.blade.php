<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="keywords" content="{{ $profile['name'] }}, portfolio, developer, full-stack, laravel, react, Indonesia">
    <meta name="description" content="Full-Stack Developer & Software Engineer. Lihat bio, riwayat pendidikan, pengalaman kerja, skill, dan project yang telah dikerjakan.">
    <title>Portfolio — {{ $profile['name'] }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300..900;1,14..32,300..900&display=swap" rel="stylesheet" nonce="{{ csp_nonce() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" nonce="{{ csp_nonce() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script nonce="{{ csp_nonce() }}">
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    <style nonce="{{ csp_nonce() }}">
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; margin: 0; overflow-x: hidden; }
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; scroll-padding-top: 5rem; }

        /* ─── Background ─── */
        .bg-mesh {
            background-color: #f8fafc;
            background-image:
                radial-gradient(at 20% 20%, rgba(99,102,241,.08) 0px, transparent 50%),
                radial-gradient(at 80% 10%, rgba(139,92,246,.06) 0px, transparent 50%),
                radial-gradient(at 50% 80%, rgba(236,72,153,.05) 0px, transparent 50%),
                radial-gradient(at 10% 70%, rgba(14,165,233,.04) 0px, transparent 50%);
        }
        .dark .bg-mesh {
            background-color: #030712;
            background-image:
                radial-gradient(at 20% 20%, rgba(99,102,241,.12) 0px, transparent 50%),
                radial-gradient(at 80% 10%, rgba(139,92,246,.08) 0px, transparent 50%),
                radial-gradient(at 50% 80%, rgba(236,72,153,.06) 0px, transparent 50%),
                radial-gradient(at 10% 70%, rgba(14,165,233,.05) 0px, transparent 50%);
        }

        .bg-dot {
            background-image: radial-gradient(rgba(15, 23, 42, 0.04) 1px, transparent 1px);
            background-size: 28px 28px;
        }
        .dark .bg-dot {
            background-image: radial-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px);
        }

        /* ─── Section label ─── */
        .section-label {
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .2em;
            text-transform: uppercase;
            color: #6366f1;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: .75rem;
        }
        .section-label::before {
            content: '';
            width: 2rem;
            height: 2px;
            background: linear-gradient(90deg, transparent, #6366f1);
            border-radius: 99px;
        }

        /* ─── Avatar ─── */
        .avatar-ring {
            padding: 4px;
            border-radius: 9999px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6, #ec4899);
        }
        .avatar-inner {
            border-radius: 9999px;
            overflow: hidden;
            background: #e0e7ff;
        }
        .dark .avatar-inner { background: #1e1b4b; }
        .avatar-inner img {
            width: 100%; height: 100%;
            object-fit: cover;
            border-radius: 9999px;
            display: block;
        }

        /* ─── Status ─── */
        .status-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 10px rgba(34,197,94,.7);
            animation: pulse-dot 2s ease-in-out infinite;
            display: inline-block;
        }
        @keyframes pulse-dot {
            0%, 100% { box-shadow: 0 0 6px rgba(34,197,94,.5); }
            50% { box-shadow: 0 0 16px rgba(34,197,94,.9); }
        }

        /* ─── Glass card ─── */
        .glass-card {
            background: rgba(255,255,255,.55);
            border: 1px solid rgba(255,255,255,.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        .dark .glass-card { background: rgba(255,255,255,.03); border-color: rgba(255,255,255,.06); }

        /* ─── Section card ─── */
        .section-card {
            background: rgba(255,255,255,.45);
            border: 1px solid rgba(255,255,255,.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .dark .section-card { background: rgba(255,255,255,.02); border-color: rgba(255,255,255,.04); }

        /* ─── Timeline ─── */
        .timeline-line {
            position: absolute;
            left: 1.15rem;
            top: 0; bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, rgba(99,102,241,.4) 0%, rgba(99,102,241,.03) 100%);
        }
        .timeline-dot {
            position: absolute;
            left: 0;
            width: 2.3rem; height: 2.3rem;
            border-radius: 50%;
            background: rgba(255,255,255,.8);
            border: 2px solid rgba(99,102,241,.15);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
            transition: all .4s cubic-bezier(.16,1,.3,1);
        }
        .dark .timeline-dot { background: rgba(15,23,42,.8); border-color: rgba(99,102,241,.2); }
        .timeline-item:hover .timeline-dot {
            border-color: #6366f1;
            box-shadow: 0 0 0 8px rgba(99,102,241,.08);
            transform: scale(1.15);
        }
        .timeline-body { margin-left: 3.6rem; padding-bottom: 2.5rem; }

        /* ─── Skill bar ─── */
        .skill-bar-bg {
            height: 6px;
            border-radius: 99px;
            background: rgba(99,102,241,.08);
            overflow: hidden;
        }
        .dark .skill-bar-bg { background: rgba(99,102,241,.12); }
        .skill-bar-fill {
            height: 100%;
            border-radius: 99px;
            transform-origin: left;
            transform: scaleX(0);
            transition: transform 1.4s cubic-bezier(.16,1,.3,1);
        }

        /* ─── Portfolio card ─── */
        .pf-card {
            background: rgba(255,255,255,.5);
            border: 1px solid rgba(255,255,255,.6);
            backdrop-filter: blur(8px);
            border-radius: 20px;
            overflow: hidden;
            transition: all .5s cubic-bezier(.16,1,.3,1);
            display: flex;
            flex-direction: column;
        }
        .dark .pf-card { background: rgba(255,255,255,.02); border-color: rgba(255,255,255,.05); }
        .pf-card:hover {
            transform: translateY(-8px) scale(1.01);
            box-shadow: 0 30px 60px rgba(79,70,229,.1), 0 10px 20px rgba(0,0,0,.04);
            border-color: rgba(99,102,241,.2);
        }
        .dark .pf-card:hover {
            box-shadow: 0 30px 60px rgba(79,70,229,.15), 0 10px 20px rgba(0,0,0,.3);
            border-color: rgba(99,102,241,.3);
        }
        .pf-img-wrap {
            aspect-ratio: 16/9;
            overflow: hidden;
            background: linear-gradient(135deg, #e0e7ff, #ede9fe);
            position: relative;
        }
        .dark .pf-img-wrap { background: linear-gradient(135deg, #1e1b4b, #2e1065); }
        .pf-img-wrap img {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform .7s cubic-bezier(.16,1,.3,1);
        }
        .pf-card:hover .pf-img-wrap img { transform: scale(1.08); }
        .pf-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(10,10,20,.8) 0%, rgba(10,10,20,.2) 50%, transparent 100%);
            opacity: 0;
            transition: opacity .4s;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding: 1.2rem;
            gap: .6rem;
        }
        .pf-card:hover .pf-overlay { opacity: 1; }
        .ov-btn {
            padding: .45rem 1rem;
            border-radius: 99px;
            font-size: .73rem;
            font-weight: 600;
            transition: transform .25s;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }
        .ov-btn:hover { transform: scale(1.06); }
        .ov-primary { background: #fff; color: #0f172a; }
        .ov-ghost { background: rgba(255,255,255,.12); color: #fff; border: 1px solid rgba(255,255,255,.25); backdrop-filter: blur(4px); }

        /* ─── Tag pill ─── */
        .tpill {
            display: inline-flex;
            align-items: center;
            padding: .2rem .6rem;
            border-radius: 99px;
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: .03em;
            font-family: ui-monospace, monospace;
        }

        /* ─── Filter btn ─── */
        .fb {
            padding: .4rem 1rem;
            border-radius: 99px;
            font-size: .78rem;
            font-weight: 600;
            cursor: pointer;
            border: 1.5px solid transparent;
            transition: all .3s;
            white-space: nowrap;
        }
        .fb-off { background: rgba(255,255,255,.4); color: #64748b; border-color: rgba(100,116,139,.12); backdrop-filter: blur(8px); }
        .dark .fb-off { color: #94a3b8; border-color: rgba(148,163,184,.1); background: rgba(255,255,255,.03); }
        .fb-off:hover { border-color: rgba(99,102,241,.4); color: #6366f1; background: rgba(99,102,241,.06); }
        .fb-on { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; border-color: transparent; box-shadow: 0 4px 20px rgba(99,102,241,.35); }

        /* ─── Navbar ─── */
        .nav-link {
            font-size: .82rem;
            font-weight: 600;
            color: #64748b;
            transition: color .25s;
            position: relative;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 50%; right: 50%;
            height: 2px;
            background: linear-gradient(90deg, #6366f1, #8b5cf6);
            border-radius: 99px;
            transition: left .3s cubic-bezier(.16,1,.3,1), right .3s cubic-bezier(.16,1,.3,1);
        }
        .nav-link:hover::after { left: 0; right: 0; }
        .nav-link:hover { color: #6366f1; }
        .nav-link.active { color: #6366f1; }
        .nav-link.active::after { left: 0; right: 0; }
        .dark .nav-link { color: #94a3b8; }
        .dark .nav-link:hover { color: #a5b4fc; }
        .dark .nav-link.active { color: #a5b4fc; }

        /* ─── Icon toggle ─── */
        #dark-toggle .icon-sun { display: none; }
        #dark-toggle .icon-moon { display: block; }
        html.dark #dark-toggle .icon-sun { display: block; }
        html.dark #dark-toggle .icon-moon { display: none; }
        #mobile-toggle .icon-bars { display: block; }
        #mobile-toggle .icon-xmark { display: none; }
        #mobile-toggle.is-open .icon-bars { display: none; }
        #mobile-toggle.is-open .icon-xmark { display: block; }

        /* ─── Glow blob ─── */
        .glow-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            pointer-events: none;
        }

        /* ─── Stat ─── */
        .stat-value { font-variant-numeric: tabular-nums; }

        /* ─── GSAP animation classes (GPU composited only) ─── */
        .gs-fade-up {
            opacity: 0;
            transform: translateY(60px) translateZ(0);
            will-change: transform, opacity;
        }
        .gs-fade-left {
            opacity: 0;
            transform: translateX(-60px) translateZ(0);
            will-change: transform, opacity;
        }
        .gs-fade-right {
            opacity: 0;
            transform: translateX(60px) translateZ(0);
            will-change: transform, opacity;
        }
        .gs-scale {
            opacity: 0;
            transform: scale(0.85) translateZ(0);
            will-change: transform, opacity;
        }
        .gs-fade-in {
            opacity: 0;
            will-change: opacity;
        }
    </style>
</head>

<body class="bg-mesh font-sans antialiased text-slate-900 dark:text-slate-100 selection:bg-indigo-600 selection:text-white">

    @php
        $navLinks = [
            ['label' => 'Tentang', 'href' => '#about'],
            ['label' => 'Pendidikan', 'href' => '#education'],
            ['label' => 'Pengalaman', 'href' => '#experience'],
            ['label' => 'Skill', 'href' => '#skills'],
            ['label' => 'Project', 'href' => '#projects'],
        ];
    @endphp

    {{-- NAVBAR --}}
    <header id="main-nav" class="fixed top-0 inset-x-0 z-50 backdrop-blur-xl bg-white/60 dark:bg-[#030712]/60 border-b border-slate-200/50 dark:border-white/5">
        <div class="max-w-6xl mx-auto px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-sm font-black tracking-tight text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                {{ $profile['name'] }}
            </a>
            <nav class="hidden md:flex items-center gap-8" id="desktop-nav">
                @foreach($navLinks as $link)
                    <a href="{{ $link['href'] }}" class="nav-link" data-section="{{ ltrim($link['href'], '#') }}">{{ $link['label'] }}</a>
                @endforeach
            </nav>
            <div class="flex items-center gap-2">
                <button id="dark-toggle" type="button" class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all" aria-label="Toggle dark mode">
                    <i class="fa-solid fa-sun text-sm icon-sun"></i>
                    <i class="fa-solid fa-moon text-sm icon-moon"></i>
                </button>
                <button id="mobile-toggle" type="button" class="md:hidden w-9 h-9 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300" aria-label="Toggle menu">
                    <i class="fa-solid fa-bars text-sm icon-bars"></i>
                    <i class="fa-solid fa-xmark text-sm icon-xmark"></i>
                </button>
            </div>
        </div>
        <nav id="mobile-nav" class="hidden md:hidden border-t border-slate-200/50 dark:border-white/5 px-6 py-4 flex flex-col gap-4 bg-white/80 dark:bg-[#030712]/80 backdrop-blur-xl">
            @foreach($navLinks as $link)
                <a href="{{ $link['href'] }}" class="nav-link mobile-nav-link" data-section="{{ ltrim($link['href'], '#') }}">{{ $link['label'] }}</a>
            @endforeach
        </nav>
    </header>

    <main class="pt-16">

        {{-- ════════════════════════════════════════════════════════
        HERO / BIO
        ════════════════════════════════════════════════════════ --}}
        <section class="relative min-h-screen flex items-center pt-24 pb-20 overflow-hidden" id="about">
            <div class="absolute inset-0 bg-dot opacity-30 pointer-events-none"></div>
            <div class="glow-blob w-[600px] h-[600px] bg-indigo-400/15 dark:bg-indigo-500/10 top-[-200px] left-1/2 -translate-x-1/2"></div>
            <div class="glow-blob w-[400px] h-[400px] bg-violet-400/10 dark:bg-violet-500/8 bottom-[-100px] right-[10%]"></div>
            <div class="glow-blob w-[300px] h-[300px] bg-pink-400/8 dark:bg-pink-500/5 top-[40%] left-[-5%]"></div>

            <div class="max-w-6xl mx-auto px-6 lg:px-8 w-full relative z-10">
                <div class="flex flex-col lg:flex-row items-center lg:items-start gap-16">

                    {{-- Avatar --}}
                    <div class="gs-fade-left flex-shrink-0 flex flex-col items-center gap-5">
                        <div class="avatar-ring w-44 h-44 lg:w-56 lg:h-56">
                            <div class="avatar-inner w-full h-full" style="padding:4px;">
                                @if($profile['avatar'])
                                    <img src="{{ asset('storage/' . $profile['avatar']) }}" alt="{{ $profile['name'] }}" class="w-full h-full object-cover">
                                @else
                                    <img src="{{ asset('profil/bima.jpeg') }}" alt="{{ $profile['name'] }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5 px-4 py-2 rounded-full glass-card text-xs font-semibold text-slate-600 dark:text-slate-300">
                            <span class="status-dot"></span>
                            Open to work
                        </div>

                        <div class="flex items-center gap-3 mt-1">
                            @if($profile['github'])
                                <a href="{{ $profile['github'] }}" target="_blank" rel="noopener" class="w-10 h-10 flex items-center justify-center rounded-full glass-card text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all hover:scale-110">
                                    <i class="fa-brands fa-github"></i>
                                </a>
                            @endif
                            @if($profile['linkedin'])
                                <a href="{{ $profile['linkedin'] }}" target="_blank" rel="noopener" class="w-10 h-10 flex items-center justify-center rounded-full glass-card text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-all hover:scale-110">
                                    <i class="fa-brands fa-linkedin"></i>
                                </a>
                            @endif
                            @if($profile['instagram'])
                                <a href="{{ $profile['instagram'] }}" target="_blank" rel="noopener" class="w-10 h-10 flex items-center justify-center rounded-full glass-card text-slate-500 dark:text-slate-400 hover:text-pink-600 dark:hover:text-pink-400 transition-all hover:scale-110">
                                    <i class="fa-brands fa-instagram"></i>
                                </a>
                            @endif
                            @if($profile['email'])
                                <a href="mailto:{{ $profile['email'] }}" class="w-10 h-10 flex items-center justify-center rounded-full glass-card text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all hover:scale-110">
                                    <i class="fa-solid fa-envelope"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Bio --}}
                    <div class="flex-1 text-center lg:text-left">
                        <span class="gs-fade-up section-label rd1">Portfolio</span>

                        <h1 class="gs-fade-up text-5xl md:text-6xl lg:text-7xl font-black tracking-tighter text-slate-900 dark:text-transparent dark:bg-clip-text dark:bg-gradient-to-br dark:from-white dark:to-slate-400 leading-[1.05] mb-6">
                            {{ $profile['name'] }}<span class="text-2xl md:text-3xl lg:text-4xl font-bold text-indigo-500 dark:text-indigo-400 ml-2">S.Tr.Kom</span>
                        </h1>

                        <p class="gs-fade-up text-lg md:text-xl font-semibold bg-gradient-to-r from-indigo-600 to-violet-600 dark:from-indigo-400 dark:to-violet-400 bg-clip-text text-transparent mb-6 tracking-tight">
                            {{ $profile['title'] }}
                        </p>

                        <p class="gs-fade-up text-base md:text-lg text-slate-500 dark:text-slate-400 leading-relaxed max-w-2xl {{ !request()->is('/*') ? 'mx-auto lg:mx-0' : '' }} mb-10">
                            {{ $profile['bio'] }}
                        </p>

                        {{-- Quick stats --}}
                        <div class="gs-fade-up flex flex-wrap justify-center lg:justify-start gap-8 mb-12">
                            @php
                                $stats = [
                                    ['val' => $portfolios->count(), 'suffix' => '+', 'label' => 'Project Selesai'],
                                    ['val' => 3, 'suffix' => '+', 'label' => 'Tahun Pengalaman'],
                                    ['val' => 100, 'suffix' => '%', 'label' => 'Komitmen Kualitas'],
                                ];
                            @endphp
                            @foreach($stats as $stat)
                                <div class="flex flex-col items-center lg:items-start px-5 py-3 rounded-2xl glass-card">
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-3xl font-black text-slate-900 dark:text-white tracking-tight stat-value" data-count="{{ $stat['val'] }}">0</span>
                                        <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-violet-600 dark:from-indigo-400 dark:to-violet-400 bg-clip-text text-transparent">{{ $stat['suffix'] }}</span>
                                    </div>
                                    <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-1">{{ $stat['label'] }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- CTA --}}
                        <div class="gs-fade-up flex flex-wrap justify-center lg:justify-start gap-3">
                            <a href="#projects" class="px-7 py-3.5 rounded-full bg-gradient-to-r from-slate-900 to-slate-800 dark:from-white dark:to-slate-100 text-white dark:text-slate-900 text-sm font-bold hover:shadow-xl hover:shadow-slate-900/20 dark:hover:shadow-white/10 transition-all duration-300 flex items-center gap-2">
                                <i class="fa-solid fa-folder-open text-xs"></i> Lihat Project
                            </a>
                            @if($profile['whatsapp'])
                                <a href="https://wa.me/62{{ ltrim($profile['whatsapp'], '0') }}" target="_blank" rel="noopener" class="px-7 py-3.5 rounded-full glass-card text-slate-700 dark:text-slate-300 text-sm font-bold hover:border-emerald-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all duration-300 flex items-center gap-2">
                                    <i class="fa-brands fa-whatsapp text-emerald-500 text-sm"></i> Hubungi via WA
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ════════════════════════════════════════════════════════
        EDUCATION
        ════════════════════════════════════════════════════════ --}}
        @if($educations->count() > 0)
        <section id="education" class="py-28 relative">
            <div class="max-w-4xl mx-auto px-6 lg:px-8">
                <div class="mb-16 text-center gs-scale">
                    <span class="section-label justify-center">Riwayat Pendidikan</span>
                    <h2 class="text-3xl md:text-4xl font-black tracking-tight text-slate-900 dark:text-white">Pendidikan</h2>
                </div>

                <div class="relative">
                    <div class="timeline-line"></div>
                    @foreach($educations as $i => $edu)
                        <div class="timeline-item relative flex gap-0 gs-fade-left">
                            <div class="timeline-dot top-0" style="border-color: {{ $edu->color }}30;">
                                <i class="fa-solid {{ $edu->icon }} text-xs" style="color: {{ $edu->color }};"></i>
                            </div>
                            <div class="timeline-body flex-1">
                                <div class="section-card rounded-2xl p-6 hover:shadow-lg hover:shadow-indigo-500/5 transition-all duration-500">
                                    <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                                        <div>
                                            <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $edu->degree }}</h3>
                                            <p class="text-sm font-semibold bg-gradient-to-r from-indigo-600 to-violet-600 dark:from-indigo-400 dark:to-violet-400 bg-clip-text text-transparent">{{ $edu->institution }}</p>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ $edu->period }}</span>
                                            @if($edu->location)
                                                <p class="text-xs text-slate-400 dark:text-slate-600 mt-0.5">
                                                    <i class="fa-solid fa-location-dot text-[10px] mr-1"></i>{{ $edu->location }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    @if($edu->description)
                                        <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-3">{{ $edu->description }}</p>
                                    @endif
                                    @if(!empty($edu->tags))
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach($edu->tags as $tag)
                                                <span class="tpill" style="background:{{ $edu->color }}12;color:{{ $edu->color }};">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        {{-- ════════════════════════════════════════════════════════
        EXPERIENCE
        ════════════════════════════════════════════════════════ --}}
        @if($experiences->count() > 0)
        <section id="experience" class="py-28 relative">
            <div class="max-w-4xl mx-auto px-6 lg:px-8">
                <div class="mb-16 text-center gs-scale">
                    <span class="section-label justify-center">Karir & Pengalaman</span>
                    <h2 class="text-3xl md:text-4xl font-black tracking-tight text-slate-900 dark:text-white">Pengalaman</h2>
                </div>

                <div class="relative">
                    <div class="timeline-line"></div>
                    @foreach($experiences as $i => $exp)
                        <div class="timeline-item relative flex gap-0 gs-fade-right">
                            <div class="timeline-dot top-0" style="border-color:{{ $exp->color }}30;">
                                <i class="fa-solid {{ $exp->icon }} text-xs" style="color:{{ $exp->color }};"></i>
                            </div>
                            <div class="timeline-body flex-1">
                                <div class="section-card rounded-2xl p-6 hover:shadow-lg hover:shadow-indigo-500/5 transition-all duration-500">
                                    <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                                        <div>
                                            <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $exp->role }}</h3>
                                            <p class="text-sm font-semibold bg-gradient-to-r from-indigo-600 to-violet-600 dark:from-indigo-400 dark:to-violet-400 bg-clip-text text-transparent">{{ $exp->company }}</p>
                                        </div>
                                        <div class="text-right flex-shrink-0 flex flex-col items-end gap-1">
                                            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ $exp->period }}</span>
                                            <div class="flex items-center gap-1.5">
                                                <span class="tpill text-[10px]" style="background:{{ $exp->color }}12;color:{{ $exp->color }};">{{ $exp->type }}</span>
                                                @if($exp->location)
                                                    <span class="text-[10px] text-slate-400">
                                                        <i class="fa-solid fa-location-dot text-[9px] mr-0.5"></i>{{ $exp->location }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if($exp->description)
                                        <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-3">{{ $exp->description }}</p>
                                    @endif
                                    @if(!empty($exp->tags))
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach($exp->tags as $tag)
                                                <span class="tpill" style="background:{{ $exp->color }}12;color:{{ $exp->color }};">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        {{-- ════════════════════════════════════════════════════════
        SKILLS
        ════════════════════════════════════════════════════════ --}}
        @if($skillGroups->count() > 0)
        <section id="skills" class="py-28 relative">
            <div class="max-w-5xl mx-auto px-6 lg:px-8">
                <div class="mb-16 text-center gs-scale">
                    <span class="section-label justify-center">Kemampuan Teknis</span>
                    <h2 class="text-3xl md:text-4xl font-black tracking-tight text-slate-900 dark:text-white">Tech Stack & Skill</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($skillGroups as $i => $group)
                        <div class="gs-scale section-card rounded-2xl p-6">
                            <div class="flex items-center gap-3 mb-7">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:{{ $group->color }}12;">
                                    <i class="fa-solid {{ $group->icon }} text-sm" style="color:{{ $group->color }};"></i>
                                </div>
                                <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm tracking-wide">{{ $group->label }}</h3>
                            </div>
                            <div class="space-y-5">
                                @foreach($group->skills as $sk)
                                    <div>
                                        <div class="flex justify-between items-center mb-1.5">
                                            <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">{{ $sk->name }}</span>
                                            <span class="text-xs font-bold tabular-nums" style="color:{{ $group->color }};">{{ $sk->percentage }}%</span>
                                        </div>
                                        <div class="skill-bar-bg">
                                            <div class="skill-bar-fill"
                                                :style="visible ? 'transform:scaleX({{ $sk->percentage / 100 }});background:linear-gradient(90deg,{{ $group->color }},{{ $group->color }}aa);' : ''"
                                                style="background:linear-gradient(90deg,{{ $group->color }},{{ $group->color }}aa);">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($techBadges->count() > 0)
                    <div class="mt-12 gs-scale">
                        <p class="text-center text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-600 mb-6">Tools & Lainnya</p>
                        <div class="flex flex-wrap justify-center gap-2.5">
                            @foreach($techBadges as $badge)
                                <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl glass-card text-xs font-semibold text-slate-600 dark:text-slate-300 hover:scale-105 hover:shadow-md transition-all duration-300 cursor-default">
                                    <i class="{{ $badge->icon }}" style="color:{{ $badge->color }};font-size:1rem;"></i>
                                    {{ $badge->label }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>
        @endif

        {{-- ════════════════════════════════════════════════════════
        PROJECTS
        ════════════════════════════════════════════════════════ --}}
        <section id="projects" class="py-28 relative" x-data="pfFilter()">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="mb-14 text-center gs-scale">
                    <span class="section-label justify-center">Karya Terpilih</span>
                    <h2 class="text-3xl md:text-4xl font-black tracking-tight text-slate-900 dark:text-white mb-4">Project</h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm max-w-lg mx-auto">
                        Kumpulan project yang telah diselesaikan — dari project personal hingga produk yang digunakan pengguna nyata.
                    </p>
                </div>

                @if($allTags->count() > 0)
                    <div class="flex flex-wrap justify-center gap-2 mb-12 gs-fade-up">
                        <button class="fb" :class="tag==='__all__'?'fb-on':'fb-off'" @click="tag='__all__'">Semua</button>
                        @foreach($allTags as $t)
                            <button class="fb" :class="tag==='{{ $t }}'?'fb-on':'fb-off'" @click="tag='{{ $t }}'">{{ $t }}</button>
                        @endforeach
                    </div>
                @endif

                @if($portfolios->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="pf-grid">
                        @php
                            $colors = [
                                ['bg' => 'rgba(99,102,241,0.08)', 'text' => '#6366f1'],
                                ['bg' => 'rgba(16,185,129,0.08)', 'text' => '#059669'],
                                ['bg' => 'rgba(245,158,11,0.08)', 'text' => '#d97706'],
                                ['bg' => 'rgba(239,68,68,0.08)', 'text' => '#dc2626'],
                                ['bg' => 'rgba(139,92,246,0.08)', 'text' => '#7c3aed'],
                                ['bg' => 'rgba(14,165,233,0.08)', 'text' => '#0284c7'],
                                ['bg' => 'rgba(236,72,153,0.08)', 'text' => '#be185d'],
                                ['bg' => 'rgba(34,197,94,0.08)', 'text' => '#16a34a'],
                            ];
                        @endphp
                        @foreach($portfolios as $i => $p)
                            <div class="pf-card gs-scale"
                                x-show="tag==='__all__' || {{ json_encode($p->tags ?? []) }}.includes(tag)"
                                x-transition:enter="transition ease-out duration-400"
                                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0 scale-95" data-tags="{{ implode(',', $p->tags ?? []) }}">

                                <div class="pf-img-wrap">
                                    @if($p->image_path)
                                        <img src="{{ asset('storage/' . $p->image_path) }}" alt="{{ $p->title }}" loading="lazy">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fa-solid fa-code text-4xl opacity-10 text-indigo-400 dark:text-indigo-600"></i>
                                        </div>
                                    @endif
                                    <div class="pf-overlay">
                                        @if($p->link_preview)
                                            <a href="{{ $p->link_preview }}" target="_blank" rel="noopener" class="ov-btn ov-primary">
                                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Demo
                                            </a>
                                        @endif
                                        @if($p->link_github)
                                            <a href="{{ $p->link_github }}" target="_blank" rel="noopener" class="ov-btn ov-ghost">
                                                <i class="fa-brands fa-github text-[11px]"></i> GitHub
                                            </a>
                                        @endif
                                        @if($p->link_journal)
                                            <a href="{{ $p->link_journal }}" target="_blank" rel="noopener" class="ov-btn ov-ghost">
                                                <i class="fa-solid fa-file-lines text-[11px]"></i> Jurnal
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-col flex-1 p-5">
                                    @if(!empty($p->tags))
                                        <div class="flex flex-wrap gap-1.5 mb-3">
                                            @foreach($p->tags as $t)
                                                @php $ci = abs(crc32($t)) % count($colors); $c = $colors[$ci]; @endphp
                                                <span class="tpill" style="background:{{ $c['bg'] }};color:{{ $c['text'] }};">{{ $t }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white leading-snug mb-2">{{ $p->title }}</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed flex-1 line-clamp-3">{{ $p->description }}</p>

                                    @if($p->link_preview || $p->link_github || $p->link_journal || $p->link_copyright)
                                        <div class="flex flex-wrap items-center gap-3 mt-4 pt-4 border-t border-slate-200/50 dark:border-white/5">
                                            @if($p->link_preview)
                                                <a href="{{ $p->link_preview }}" target="_blank" rel="noopener" class="flex items-center gap-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>Demo
                                                </a>
                                            @endif
                                            @if($p->link_github)
                                                <a href="{{ $p->link_github }}" target="_blank" rel="noopener" class="flex items-center gap-1.5 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                                                    <i class="fa-brands fa-github text-[11px]"></i>GitHub
                                                </a>
                                            @endif
                                            @if($p->link_copyright)
                                                <a href="{{ $p->link_copyright }}" target="_blank" rel="noopener" class="flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-slate-700 dark:hover:text-white transition-colors ml-auto">
                                                    <i class="fa-solid fa-copyright text-[9px]"></i>HKI
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div x-show="noResult" x-cloak class="text-center py-16">
                        <p class="text-slate-500 dark:text-slate-400 text-sm">Tidak ada project dengan tag ini.</p>
                        <button @click="tag='__all__'" class="mt-3 text-sm font-bold bg-gradient-to-r from-indigo-600 to-violet-600 dark:from-indigo-400 dark:to-violet-400 bg-clip-text text-transparent hover:underline">Tampilkan semua</button>
                    </div>
                @else
                    <div class="text-center py-24 gs-scale">
                        <div class="w-16 h-16 rounded-2xl glass-card flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-briefcase text-slate-300 dark:text-slate-600 text-2xl"></i>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm">Project akan segera hadir.</p>
                    </div>
                @endif
            </div>
        </section>

        {{-- ════════════════════════════════════════════════════════
        CONTACT CTA
        ════════════════════════════════════════════════════════ --}}
        <section class="py-28 relative">
            <div class="max-w-3xl mx-auto px-6 text-center gs-scale">
                <span class="section-label justify-center">Kontak</span>
                <h2 class="text-3xl md:text-4xl font-black tracking-tight text-slate-900 dark:text-white mb-5">
                    Ada project yang ingin dikerjakan?
                </h2>
                <p class="text-slate-500 dark:text-slate-400 text-base leading-relaxed mb-10 max-w-xl mx-auto">
                    Terbuka untuk kolaborasi, freelance, dan diskusi ide. Ceritakan visi Anda dan mari kita wujudkan bersama.
                </p>

                <div class="flex flex-wrap justify-center gap-4">
                    @if($profile['email'])
                        <a href="mailto:{{ $profile['email'] }}" class="px-8 py-3.5 rounded-full bg-gradient-to-r from-slate-900 to-slate-800 dark:from-white dark:to-slate-100 text-white dark:text-slate-900 text-sm font-bold hover:shadow-xl hover:shadow-slate-900/20 dark:hover:shadow-white/10 transition-all duration-300 flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-xs"></i> {{ $profile['email'] }}
                        </a>
                    @endif
                    @if($profile['whatsapp'])
                        <a href="https://wa.me/62{{ ltrim($profile['whatsapp'], '0') }}" target="_blank" rel="noopener" class="px-8 py-3.5 rounded-full glass-card text-slate-700 dark:text-slate-300 text-sm font-bold hover:border-emerald-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all duration-300 flex items-center gap-2">
                            <i class="fa-brands fa-whatsapp text-emerald-500"></i> WhatsApp
                        </a>
                    @endif
                    @if($profile['linkedin'])
                        <a href="{{ $profile['linkedin'] }}" target="_blank" rel="noopener" class="px-8 py-3.5 rounded-full glass-card text-slate-700 dark:text-slate-300 text-sm font-bold hover:border-blue-400 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-300 flex items-center gap-2">
                            <i class="fa-brands fa-linkedin text-blue-500"></i> LinkedIn
                        </a>
                    @endif
                </div>
            </div>
        </section>

    </main>

    {{-- FOOTER --}}
    <footer class="py-10 relative">
        <div class="max-w-6xl mx-auto px-6 lg:px-8 text-center">
            <p class="text-xs text-slate-400 dark:text-slate-600">
                &copy; {{ date('Y') }} {{ $profile['name'] ?? 'Bima Ryan Alfarizi' }}. Dibuat dengan Laravel &amp; Tailwind CSS.
            </p>
        </div>
    </footer>

    {{-- GSAP --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" nonce="{{ csp_nonce() }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js" nonce="{{ csp_nonce() }}"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js" nonce="{{ csp_nonce() }}"></script>

    <script nonce="{{ csp_nonce() }}">
        // ── Dark Mode Toggle (pure JS) ──────────────────────
        (function () {
            const toggle = document.getElementById('dark-toggle');
            if (!toggle) return;
            toggle.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            });
        })();

        // ── Mobile Menu Toggle (pure JS) ────────────────────
        (function () {
            const btn = document.getElementById('mobile-toggle');
            const nav = document.getElementById('mobile-nav');
            if (!btn || !nav) return;
            btn.addEventListener('click', () => {
                const isOpen = nav.classList.toggle('hidden') === false;
                btn.classList.toggle('is-open', isOpen);
            });
            nav.querySelectorAll('.mobile-nav-link').forEach(link => {
                link.addEventListener('click', () => {
                    nav.classList.add('hidden');
                    btn.classList.remove('is-open');
                });
            });
        })();

        // ── Scroll Spy (active nav link) ────────────────────
        (function () {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-link[data-section]');
            function updateActive() {
                let current = '';
                const scrollY = window.scrollY + 120;
                sections.forEach(sec => {
                    if (sec.offsetTop <= scrollY) current = sec.id;
                });
                navLinks.forEach(link => {
                    link.classList.toggle('active', link.dataset.section === current);
                });
            }
            window.addEventListener('scroll', updateActive, { passive: true });
            updateActive();
        })();
    </script>

    <script nonce="{{ csp_nonce() }}">
        // ── GSAP 60fps Animations ──────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            gsap.registerPlugin(ScrollTrigger);

            // Global defaults — 60fps composited
            gsap.defaults({
                force3D: true,
                overwrite: 'auto'
            });

            const SPRING = 'elastic.out(1, 0.75)';
            const SMOOTH = 'power4.out';
            const BOUNCE = 'back.out(1.7)';

            // ── Hero entrance (immediate, staggered) ──
            const heroTl = gsap.timeline({ delay: 0.3 });
            heroTl
                .to('.gs-fade-left', {
                    opacity: 1, x: 0, z: 0,
                    duration: 1.2,
                    ease: SPRING,
                    stagger: 0.15
                })
                .to('.gs-fade-up', {
                    opacity: 1, y: 0, z: 0,
                    duration: 1,
                    ease: SMOOTH,
                    stagger: 0.1
                }, '-=0.8');

            // ── Scroll-triggered: fade up ──
            gsap.utils.toArray('.gs-fade-up').forEach((el, i) => {
                if (el.closest('section#about')) return; // skip hero
                ScrollTrigger.create({
                    trigger: el,
                    start: 'top 90%',
                    once: true,
                    onEnter: () => {
                        gsap.to(el, {
                            opacity: 1, y: 0, z: 0,
                            duration: 0.9,
                            ease: SMOOTH,
                            delay: (i % 4) * 0.08
                        });
                    }
                });
            });

            // ── Scroll-triggered: fade left ──
            gsap.utils.toArray('.gs-fade-left').forEach((el, i) => {
                if (el.closest('section#about')) return;
                ScrollTrigger.create({
                    trigger: el,
                    start: 'top 90%',
                    once: true,
                    onEnter: () => {
                        gsap.to(el, {
                            opacity: 1, x: 0, z: 0,
                            duration: 1,
                            ease: SPRING,
                            delay: (i % 5) * 0.1
                        });
                    }
                });
            });

            // ── Scroll-triggered: fade right ──
            gsap.utils.toArray('.gs-fade-right').forEach((el, i) => {
                ScrollTrigger.create({
                    trigger: el,
                    start: 'top 90%',
                    once: true,
                    onEnter: () => {
                        gsap.to(el, {
                            opacity: 1, x: 0, z: 0,
                            duration: 1,
                            ease: SPRING,
                            delay: (i % 5) * 0.1
                        });
                    }
                });
            });

            // ── Scroll-triggered: scale (staggered grid) ──
            const scaleEls = gsap.utils.toArray('.gs-scale');
            const sectionGroups = {};
            scaleEls.forEach(el => {
                const section = el.closest('section') || el.parentElement;
                const key = section ? section.id || 'other' : 'other';
                if (!sectionGroups[key]) sectionGroups[key] = [];
                sectionGroups[key].push(el);
            });

            Object.values(sectionGroups).forEach(group => {
                group.forEach((el, i) => {
                    ScrollTrigger.create({
                        trigger: el,
                        start: 'top 92%',
                        once: true,
                        onEnter: () => {
                            gsap.to(el, {
                                opacity: 1, scale: 1, z: 0,
                                duration: 0.8,
                                ease: BOUNCE,
                                delay: i * 0.1
                            });
                        }
                    });
                });
            });

            // ── Refresh on load for accurate positions ──
            window.addEventListener('load', () => ScrollTrigger.refresh());
        });

        // ── Counter (requestAnimationFrame, 60fps) ─────────
        (function () {
            const counters = document.querySelectorAll('.stat-value[data-count]');
            const io = new IntersectionObserver((entries) => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        const el = e.target;
                        const target = parseInt(el.dataset.count);
                        const duration = 2000;
                        const start = performance.now();
                        const step = (now) => {
                            const t = Math.min((now - start) / duration, 1);
                            // Framer Motion style easeOutExpo
                            const eased = t === 1 ? 1 : 1 - Math.pow(2, -10 * t);
                            el.textContent = Math.round(target * eased);
                            if (t < 1) requestAnimationFrame(step);
                        };
                        requestAnimationFrame(step);
                        io.unobserve(el);
                    }
                });
            }, { threshold: 0.5 });
            counters.forEach(c => io.observe(c));
        })();

        // ── AlpineJS Portfolio Filter ───────────────────────
        function pfFilter() {
            return {
                tag: '__all__',
                get noResult() {
                    if (this.tag === '__all__') return false;
                    const cards = document.querySelectorAll('#pf-grid > [data-tags]');
                    let any = false;
                    cards.forEach(c => {
                        const tags = c.dataset.tags ? c.dataset.tags.split(',') : [];
                        if (tags.includes(this.tag)) any = true;
                    });
                    return !any;
                }
            };
        }
    </script>
</body>

</html>
