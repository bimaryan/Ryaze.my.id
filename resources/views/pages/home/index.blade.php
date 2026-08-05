<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="theme-color" content="#ffffff">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">
    @php
        $siteName = \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze Portal';
        $siteDescription =
            \App\Models\Setting::where('key', 'site_description')->value('value') ??
            'Layanan web hosting canggih dan jasa development profesional.';
        $siteFavicon = \App\Models\Setting::where('key', 'site_favicon')->value('value');
        $gaId = \App\Models\Setting::where('key', 'google_analytics_id')->value('value');
    @endphp

    <title>{{ $siteName }} - Cloud Hosting & Web Development</title>

    @if ($siteFavicon)
        <link rel="icon" href="{{ asset('storage/' . $siteFavicon) }}">
    @endif

    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ $siteDescription }}">
    <meta name="keywords" content="hosting, web development, ryaze, server, cloud">
    <meta name="author" content="{{ $siteName }}">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="{{ $siteName }} - Cloud Hosting & Web Development">
    <meta property="og:description" content="{{ $siteDescription }}">
    <meta property="og:image"
        content="https://ui-avatars.com/api/?name={{ urlencode($siteName) }}&size=600&background=4f46e5&color=fff">

    @if ($gaId)
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', '{{ $gaId }}');
        </script>
    @endif

    <!-- Vite Config -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"
        nonce="{{ app('csp_nonce') ?? '' }}">

    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js" nonce="{{ app('csp_nonce') ?? '' }}"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" nonce="{{ app('csp_nonce') ?? '' }}">

    <style nonce="{{ app('csp_nonce') ?? '' }}">
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            color: #111827;
        }

        /* Subtle grid pattern background */
        .bg-grid {
            background-image: linear-gradient(to right, #f1f5f9 1px, transparent 1px),
                linear-gradient(to bottom, #f1f5f9 1px, transparent 1px);
            background-size: 40px 40px;
            background-position: center top;
        }

        /* Strict borders for cards */
        .card-brutal {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .card-brutal:hover {
            border-color: #4f46e5;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Gradient text but strictly monochrome/subtle */
        .text-gradient-mono {
            background: linear-gradient(to right, #4f46e5, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>

<body class="antialiased selection:bg-indigo-600 selection:text-white relative">

    <!-- NAVBAR -->
    <nav x-data="{ mobileMenuOpen: false }"
        class="fixed top-0 z-50 w-full bg-white/90 backdrop-blur-md border-b border-slate-200 transition-all duration-200">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                    <div class="bg-indigo-600 text-white rounded-md w-8 h-8 flex items-center justify-center">
                        <i class="fa-solid fa-code text-sm"></i>
                    </div>
                    <span
                        class="text-xl font-bold tracking-tight text-indigo-600">{{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze Portal' }}</span>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                    <a href="#about" class="hover:text-indigo-600 transition-colors">Tentang</a>
                    <a href="#services" class="hover:text-indigo-600 transition-colors">Layanan</a>
                    <a href="#portfolio" class="hover:text-indigo-600 transition-colors">Portofolio</a>
                    <a href="{{ route('blog.index') }}" class="hover:text-indigo-600 transition-colors">Blog</a>
                </div>

                <!-- Auth Buttons & Mobile Toggle -->
                <div class="flex items-center gap-4">
                    @auth
                        @php
                            $dashboardUrl = match (Auth::user()->role ?? '') {
                                'superadmin' => route('superadmin.dashboard'),
                                'admin_joki' => route('admin_joki.dashboard'),
                                'admin_hosting' => route('admin_hosting.dashboard'),
                                'user_joki' => route('user_joki.dashboard'),
                                'user_hosting' => route('user_hosting.dashboard'),
                                default => url('/'),
                            };
                        @endphp
                        <a href="{{ $dashboardUrl }}"
                            class="hidden sm:inline-flex text-sm font-semibold bg-indigo-600 text-white px-5 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors hidden sm:block">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}"
                            class="hidden sm:inline-flex text-sm font-semibold bg-indigo-600 text-white px-5 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                            Daftar
                        </a>
                    @endauth

                    <!-- Hamburger Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none">
                        <i class="fa-solid fa-bars text-xl" x-show="!mobileMenuOpen"></i>
                        <i class="fa-solid fa-xmark text-xl" x-show="mobileMenuOpen" style="display: none;"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" style="display: none;" class="md:hidden bg-white border-t border-slate-200" x-transition>
            <div class="px-4 pt-2 pb-6 space-y-1">
                <a href="#about" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-indigo-600 hover:bg-slate-50">Tentang</a>
                <a href="#services" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-indigo-600 hover:bg-slate-50">Layanan</a>
                <a href="#portfolio" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-indigo-600 hover:bg-slate-50">Portofolio</a>
                <a href="{{ route('blog.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-indigo-600 hover:bg-slate-50">Blog</a>
                
                <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col gap-2">
                    @auth
                        <a href="{{ $dashboardUrl ?? url('/') }}" class="block w-full text-center text-sm font-semibold bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="block w-full text-center text-sm font-semibold border border-slate-200 text-slate-700 px-4 py-2 rounded-md hover:bg-slate-50 transition-colors">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="block w-full text-center text-sm font-semibold bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                            Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section
        class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 bg-grid min-h-[90vh] flex items-center border-b border-slate-200">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-white pointer-events-none"></div>
        <div class="max-w-4xl mx-auto px-6 relative z-10 text-center">

            <div
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-slate-200 bg-slate-50 text-slate-600 text-xs font-semibold mb-8">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Sistem Deployment Tersedia
            </div>

            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-slate-900 leading-[1.1] mb-6">
                Bangun Produk Digital Anda <br class="hidden md:block" />
                <span class="text-gradient-mono">Lebih Cepat & Kuat.</span>
            </h1>

            <p class="text-lg md:text-xl text-slate-500 max-w-2xl mx-auto mb-10 leading-relaxed font-medium">
                Infrastruktur hosting tangguh berbasis cloud dan tim development profesional siap mengeksekusi visi
                teknologi Anda tanpa kompromi.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#services"
                    class="px-8 py-3.5 text-sm font-semibold rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2">
                    Jelajahi Layanan
                </a>
                <a href="#portfolio"
                    class="px-8 py-3.5 text-sm font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition-colors flex items-center justify-center gap-2">
                    Lihat Portofolio
                </a>
            </div>

            <div class="mt-24 pt-8 border-t border-slate-200 max-w-3xl mx-auto">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-6">Stack Teknologi Kami</p>
                <div class="flex flex-wrap justify-center gap-8 text-slate-300 hover:text-slate-400 transition-colors">
                    <i class="fa-brands fa-laravel text-3xl"></i>
                    <i class="fa-brands fa-react text-3xl"></i>
                    <i class="fa-brands fa-node-js text-3xl"></i>
                    <i class="fa-brands fa-python text-3xl"></i>
                    <i class="fa-brands fa-vuejs text-3xl"></i>
                    <i class="fa-brands fa-aws text-3xl"></i>
                    <i class="fa-brands fa-docker text-3xl"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section id="about" class="py-24 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
                <!-- Text Content -->
                <div class="pt-4">
                    <h2 class="text-3xl font-bold tracking-tight text-slate-900 mb-6">Di Balik Layar</h2>
                    <div class="w-12 h-1 bg-indigo-600 mb-8"></div>

                    <p class="text-base text-slate-600 mb-6 leading-relaxed">
                        Saya <strong>Bima Ryan Alfarizi</strong>, mahasiswa D4 Rekayasa Perangkat Lunak di Politeknik
                        Negeri Indramayu. Visi utama saya adalah menciptakan standar rekayasa perangkat lunak yang
                        bersih, skalabel, dan fungsional.
                    </p>
                    <p class="text-base text-slate-600 mb-8 leading-relaxed">
                        Ryaze dikembangkan bukan hanya sebagai penyedia layanan, tetapi sebagai ekosistem di mana kode
                        dan infrastruktur berpadu dengan sempurna. Fokus kami ada pada efisiensi teknis dan keandalan
                        sistem.
                    </p>

                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1.5 bg-slate-100 border border-slate-200 rounded text-xs font-semibold text-slate-700">Fullstack Web</span>
                        <span class="px-3 py-1.5 bg-slate-100 border border-slate-200 rounded text-xs font-semibold text-slate-700">Cloud Server</span>
                        <span class="px-3 py-1.5 bg-slate-100 border border-slate-200 rounded text-xs font-semibold text-slate-700">CI/CD Pipeline</span>
                        <span class="px-3 py-1.5 bg-slate-100 border border-slate-200 rounded text-xs font-semibold text-slate-700">Game Engine</span>
                    </div>
                </div>

                <!-- Clean Profile Card -->
                <div class="flex justify-center lg:justify-end">
                    <div class="w-full max-w-sm">
                        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                            <div class="aspect-[3/4] bg-slate-100 relative">
                                <img src="{{ asset('profil/bima.jpeg') }}"
                                    alt="Bima Ryan"
                                    class="w-full h-full object-cover object-top">
                            </div>
                            <div class="p-6 border-t border-slate-100 bg-slate-50">
                                <h3 class="font-bold text-slate-900 text-lg">Bima Ryan Alfarizi, S.Tr.Kom</h3>
                                <p class="text-indigo-600 font-medium text-sm mb-4">Sarjana Terapan RPL Polindra</p>
                                
                                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                                    <i class="fa-solid fa-location-dot"></i> Indramayu, Indonesia
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SERVICES SECTION -->
    <section id="services" class="py-24 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="mb-16 max-w-2xl">
                <h2 class="text-3xl font-bold tracking-tight text-slate-900 mb-4">Infrastruktur & Layanan</h2>
                <p class="text-slate-500 text-base">Kami merancang arsitektur web dan infrastruktur cloud kelas pekerja
                    yang bisa diandalkan kapan saja.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Web Dev Box -->
                <div class="card-brutal p-8 flex flex-col h-full group hover:bg-slate-50">
                    <div class="w-12 h-12 bg-indigo-600 text-white rounded flex items-center justify-center mb-6">
                        <i class="fa-solid fa-laptop-code text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Joki Pembuatan Web & Aplikasi</h3>
                    <p class="text-slate-500 text-sm leading-relaxed mb-8 flex-1">
                        Layanan Joki untuk pembuatan sistem informasi, aplikasi SaaS, hingga prototipe fungsional Tugas
                        Akhir (Skripsi). Berbasis arsitektur modern yang bersih, efisien, dan terdokumentasi dengan
                        baik.
                    </p>
                    <ul class="space-y-3 mb-8 text-sm font-medium text-slate-600">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-indigo-500"></i> Backend
                            & API Design</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-indigo-500"></i> Frontend
                            Modern (React/Vue)</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-indigo-500"></i> Keamanan
                            & Skalabilitas Tinggi</li>
                    </ul>
                    <a href="{{ route('register') }}"
                        class="text-sm font-semibold text-indigo-600 inline-flex items-center gap-2 group-hover:underline">
                        Mulai Konsultasi <i
                            class="fa-solid fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                    </a>
                </div>

                <!-- Hosting Box -->
                <div class="card-brutal p-8 flex flex-col h-full group hover:bg-slate-50">
                    <div class="w-12 h-12 bg-indigo-600 text-white rounded flex items-center justify-center mb-6">
                        <i class="fa-solid fa-server text-xl"></i>
                    </div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xl font-bold text-slate-900">Cloud Application Hosting</h3>
                        @php
                            $normalPrice = (int) \App\Models\Setting::val('hosting_price', 10000);
                            $promoPrice = (int) \App\Models\Setting::val('hosting_promo_price', 0);
                        @endphp
                        <div class="text-right flex flex-col items-end leading-tight">
                            @if ($promoPrice > 0)
                                <span class="text-[10px] text-slate-400 line-through">Rp
                                    {{ number_format($normalPrice, 0, ',', '.') }}</span>
                                <span class="text-sm font-bold text-emerald-600">Rp
                                    {{ number_format($promoPrice, 0, ',', '.') }}<span
                                        class="text-[10px] text-slate-500 font-normal">/bln</span></span>
                            @else
                                <span class="text-sm font-bold text-indigo-600">Rp
                                    {{ number_format($normalPrice, 0, ',', '.') }}<span
                                        class="text-[10px] text-slate-500 font-normal">/bln</span></span>
                            @endif
                        </div>
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed mb-6 flex-1">
                        Deployment otomatis tanpa pusing. Eksekusi repositori kode langsung ke server publik dengan
                        dukungan Web-Terminal, proses manager, dan database bawaan.
                    </p>
                    <ul class="space-y-2 mb-8 text-sm font-medium text-slate-600">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-indigo-500"></i> Auto
                            Deploy (Node, PHP, Python, React, Vue)</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-indigo-500"></i> Database
                            (MySQL) & SSL Gratis</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-indigo-500"></i> File
                            Manager, Web Terminal</li>
                    </ul>
                    <a href="{{ route('register') }}"
                        class="text-sm font-semibold text-indigo-600 inline-flex items-center gap-2 group-hover:underline">
                        Deploy Sekarang <i
                            class="fa-solid fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- PRICING SECTION -->
    <section id="pricing" class="py-24 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="mb-14 text-center">
                <span class="text-xs font-bold uppercase tracking-widest text-indigo-600 mb-3 block">Harga Transparan</span>
                <h2 class="text-3xl font-bold tracking-tight text-slate-900 mb-4">Pilih Paket Hosting</h2>
                <p class="text-slate-500 text-base max-w-xl mx-auto">Deploy project Anda sekarang. Mulai dari harga terjangkau dengan fitur lengkap, siap scale sesuai kebutuhan.</p>
            </div>
            @php
                $homePlans = \App\Models\User::hostingPlans();
                $homeColorMap = [
                    'slate'  => ['accent' => 'border-t-slate-500', 'icon' => 'bg-slate-600', 'price' => 'text-slate-600', 'btn' => 'bg-slate-600 hover:bg-slate-700 text-white', 'check' => 'text-slate-500'],
                    'indigo' => ['accent' => 'border-t-indigo-500', 'icon' => 'bg-indigo-600', 'price' => 'text-indigo-600', 'btn' => 'bg-indigo-600 hover:bg-indigo-700 text-white', 'check' => 'text-indigo-500'],
                    'violet' => ['accent' => 'border-t-violet-500', 'icon' => 'bg-violet-600', 'price' => 'text-violet-600', 'btn' => 'bg-violet-600 hover:bg-violet-700 text-white', 'check' => 'text-violet-500'],
                    'amber'  => ['accent' => 'border-t-amber-400',  'icon' => 'bg-amber-500',  'price' => 'text-amber-600',  'btn' => 'bg-amber-500 hover:bg-amber-600 text-white',   'check' => 'text-amber-500'],
                ];
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($homePlans as $slug => $plan)
                    @php
                        $pricing = \App\Models\User::getPlanPricing($slug);
                        $hc = $homeColorMap[$plan['color']];
                        $isPopular = $slug === 'pro';
                    @endphp
                    <div class="relative flex flex-col bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all {{ $isPopular ? 'ring-2 ring-violet-500 scale-105 z-10' : '' }}">
                        <div class="h-1 w-full {{ $hc['accent'] }} border-t-4"></div>
                        @if ($isPopular)
                            <div class="absolute top-4 right-4">
                                <span class="text-[10px] font-bold bg-violet-100 text-violet-700 border border-violet-200 px-2.5 py-1 rounded-full">⭐ POPULER</span>
                            </div>
                        @endif
                        <div class="p-8 flex-1">
                            <div class="w-11 h-11 {{ $hc['icon'] }} rounded-xl flex items-center justify-center mb-5">
                                <i class="fa-solid fa-server text-white"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-1">{{ $plan['label'] }}</h3>
                            <div class="flex flex-col mb-6">
                                @if($pricing['promo'] !== null)
                                    <span class="text-sm font-semibold text-slate-400 line-through decoration-rose-500 decoration-2">Rp {{ number_format($pricing['normal'], 0, ',', '.') }}</span>
                                @endif
                                <div class="flex items-baseline gap-1">
                                    <span class="text-4xl font-extrabold {{ $hc['price'] }}">Rp {{ number_format($pricing['active'], 0, ',', '.') }}</span>
                                    <span class="text-slate-400 text-sm">/bulan</span>
                                </div>
                            </div>
                            <ul class="space-y-3">
                                @foreach ($plan['features'] as $feat)
                                    <li class="flex items-center gap-2.5 text-sm text-slate-600">
                                        <i class="fa-solid fa-check {{ $hc['check'] }} text-xs flex-shrink-0"></i>
                                        {{ $feat }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="px-8 pb-8">
                            <a href="{{ route('register') }}" class="block w-full text-center {{ $hc['btn'] }} font-bold py-3 rounded-xl transition-all shadow-sm text-sm">
                                Mulai Sekarang <i class="fa-solid fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

        <!-- PORTFOLIO SECTION -->
    <section id="portfolio" class="py-24 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="mb-12 flex justify-between items-end">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-slate-900 mb-2">Arsip Karya</h2>
                    <p class="text-slate-500 text-sm">Beberapa entitas digital yang telah kami kembangkan.</p>
                </div>
                <a href="https://github.com/bimaryan" target="_blank" rel="noopener noreferrer"
                    class="hidden md:flex text-sm font-semibold text-indigo-600 items-center gap-2 hover:underline">
                    Lihat Repositori <i class="fa-brands fa-github text-lg"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($portfolios as $portfolio)
                    <div class="card-brutal flex flex-col overflow-hidden bg-white group">
                        @if ($portfolio->link_preview)
                            <a href="{{ $portfolio->link_preview }}" target="_blank" rel="noopener noreferrer"
                                class="block h-48 border-b border-slate-100 bg-slate-100 overflow-hidden relative">
                            @else
                                <div
                                    class="block h-48 border-b border-slate-100 bg-slate-100 overflow-hidden relative">
                        @endif

                        @if ($portfolio->image_path)
                            <img src="{{ Storage::url($portfolio->image_path) }}" alt="{{ $portfolio->title }}"
                                class="w-full h-full object-cover filter grayscale group-hover:grayscale-0 transition-all duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300 bg-slate-50">
                                <i class="fa-solid fa-image text-3xl"></i>
                            </div>
                        @endif

                        @if ($portfolio->link_preview)
                            </a>
                        @else
                    </div>
                @endif

                <div class="p-6 flex flex-col flex-1">
                    <div class="flex gap-2 mb-3 flex-wrap">
                        @if ($portfolio->tags)
                            @foreach ($portfolio->tags as $tag)
                                <span
                                    class="px-2 py-0.5 bg-slate-100 border border-slate-200 text-slate-600 text-[10px] uppercase font-bold rounded">{{ $tag }}</span>
                            @endforeach
                        @endif
                    </div>

                    <h3 class="text-base font-bold text-slate-900 mb-2">{{ $portfolio->title }}</h3>
                    <p class="text-slate-500 text-sm line-clamp-3 mb-4 flex-1 leading-relaxed">
                        {{ $portfolio->description }}</p>

                    <div class="flex items-center gap-3 flex-wrap mt-auto pt-4 border-t border-slate-100">
                        @if ($portfolio->link_github)
                            <a href="{{ $portfolio->link_github }}" target="_blank" rel="noopener noreferrer"
                                class="text-xs font-semibold text-slate-600 hover:text-indigo-600 transition-colors flex items-center gap-1.5">
                                <i class="fa-brands fa-github text-sm"></i> Code
                            </a>
                        @endif
                        @if ($portfolio->link_journal)
                            <a href="{{ $portfolio->link_journal }}" target="_blank" rel="noopener noreferrer"
                                class="text-xs font-semibold text-slate-600 hover:text-indigo-600 transition-colors flex items-center gap-1.5">
                                <i class="fa-solid fa-book-open text-sm"></i> Jurnal
                            </a>
                        @endif
                        @if ($portfolio->certificate_path)
                            <a href="{{ Storage::url($portfolio->certificate_path) }}" target="_blank" rel="noopener noreferrer"
                                class="text-xs font-semibold text-amber-600 hover:text-amber-700 transition-colors flex items-center gap-1.5">
                                <i class="fa-solid fa-certificate text-sm"></i> Sertifikat
                            </a>
                        @endif
                        @if ($portfolio->link_preview)
                            <a href="{{ $portfolio->link_preview }}" target="_blank" rel="noopener noreferrer"
                                class="text-xs font-semibold text-slate-600 hover:text-indigo-600 transition-colors flex items-center gap-1.5 ml-auto">
                                Visit <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 border border-dashed border-slate-300 rounded-lg text-center bg-white">
                <p class="text-sm text-slate-500 font-medium">Data arsip belum tersedia.</p>
            </div>
            @endforelse
        </div>

        <div class="mt-8 md:hidden">
            <a href="https://github.com/bimaryan" target="_blank" rel="noopener noreferrer"
                class="text-sm font-semibold text-indigo-600 inline-flex items-center gap-2 hover:underline">
                Lihat Repositori <i class="fa-brands fa-github text-lg"></i>
            </a>
        </div>
        </div>
    </section>

    <!-- BLOG SECTION -->
    <section class="py-24 bg-slate-50 border-t border-slate-200" id="blog">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-12">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 mb-2">Artikel Terbaru</h2>
                    <p class="text-slate-500 text-sm max-w-2xl">Tulisan seputar web development, tips hosting, dan
                        wawasan teknologi lainnya dari tim Ryaze.</p>
                </div>
                <a href="{{ route('blog.index') }}"
                    class="hidden md:inline-flex text-sm font-semibold text-indigo-600 items-center gap-2 hover:underline">
                    Lihat Semua Artikel <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($articles as $article)
                    <a href="{{ route('blog.show', $article->slug) }}"
                        class="group card-brutal overflow-hidden flex flex-col">
                        @if ($article->cover_image)
                            <div class="h-48 overflow-hidden bg-slate-100 border-b border-slate-200">
                                <img src="{{ Storage::url($article->cover_image) }}" alt="{{ $article->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                        @else
                            <div
                                class="h-48 bg-slate-50 border-b border-slate-200 flex items-center justify-center text-slate-300">
                                <i class="fa-solid fa-newspaper text-5xl"></i>
                            </div>
                        @endif
                        <div class="p-6 flex flex-col flex-1">
                            @if ($article->category)
                                <span
                                    class="text-[10px] font-bold uppercase text-indigo-600 mb-2">{{ $article->category->name }}</span>
                            @endif
                            <h3
                                class="text-lg font-bold text-slate-900 mb-2 group-hover:text-indigo-600 transition-colors line-clamp-2">
                                {{ $article->title }}</h3>
                            <p class="text-slate-500 text-sm line-clamp-2 mb-4 flex-1">
                                {{ $article->excerpt ?: Str::limit(strip_tags($article->body), 100) }}</p>
                            <div
                                class="flex items-center gap-3 text-xs text-slate-400 mt-auto pt-4 border-t border-slate-100">
                                <span>{{ $article->published_at?->format('d M Y') }}</span>
                                <span>&middot;</span>
                                <span>{{ $article->reading_time }} min</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div
                        class="col-span-full py-12 border border-dashed border-slate-300 rounded-lg text-center bg-white">
                        <p class="text-sm text-slate-500 font-medium">Belum ada artikel.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8 md:hidden text-center">
                <a href="{{ route('blog.index') }}"
                    class="text-sm font-semibold text-indigo-600 inline-flex items-center gap-2 hover:underline">
                    Lihat Semua Artikel <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- CALL TO ACTION -->
    <section class="py-24 bg-indigo-600 text-white text-center px-6">
        <div class="max-w-3xl mx-auto">
            <h2 class="text-3xl md:text-5xl font-bold tracking-tight mb-6">Siap Mengeksekusi Ide?</h2>
            <p class="text-indigo-200 text-lg mb-10 max-w-xl mx-auto">Daftar sekarang untuk mengakses lingkungan
                deployment yang kuat atau hubungi kami untuk pengerjaan perangkat lunak Anda.</p>
            <a href="{{ route('register') }}"
                class="inline-block px-8 py-3 bg-white text-indigo-700 text-sm font-bold rounded-md hover:bg-indigo-50 transition-colors">
                Mulai Secara Gratis
            </a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-slate-200 py-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-2 text-indigo-600">
                    <i class="fa-solid fa-code text-lg"></i>
                    <span
                        class="text-xl font-bold tracking-tight">{{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze Portal' }}</span>
                </div>
                <div class="flex gap-6 items-center">
                    @php
                        $socialGithub = \App\Models\Setting::where('key', 'social_github')->value('value');
                        $socialInstagram = \App\Models\Setting::where('key', 'social_instagram')->value('value');
                        $socialLinkedin = \App\Models\Setting::where('key', 'social_linkedin')->value('value');
                        $contactEmail = \App\Models\Setting::where('key', 'contact_email')->value('value');
                        $contactWhatsapp = \App\Models\Setting::where('key', 'contact_whatsapp')->value('value');
                    @endphp
                    @if ($contactEmail)
                        <a href="mailto:{{ $contactEmail }}"
                            class="text-slate-400 hover:text-indigo-600 transition-colors">
                            <i class="fa-solid fa-envelope text-xl"></i>
                        </a>
                    @endif
                    @if ($contactWhatsapp)
                        <a href="https://wa.me/62{{ ltrim($contactWhatsapp, '0') }}" target="_blank"
                            rel="noopener noreferrer" class="text-slate-400 hover:text-emerald-500 transition-colors">
                            <i class="fa-brands fa-whatsapp text-xl"></i>
                        </a>
                    @endif
                    @if ($socialGithub)
                        <a href="{{ $socialGithub }}" target="_blank" rel="noopener noreferrer"
                            class="text-slate-400 hover:text-indigo-600 transition-colors">
                            <i class="fa-brands fa-github text-xl"></i>
                        </a>
                    @endif
                    @if ($socialInstagram)
                        <a href="{{ $socialInstagram }}" target="_blank" rel="noopener noreferrer"
                            class="text-slate-400 hover:text-pink-600 transition-colors">
                            <i class="fa-brands fa-instagram text-xl"></i>
                        </a>
                    @endif
                    @if ($socialLinkedin)
                        <a href="{{ $socialLinkedin }}" target="_blank" rel="noopener noreferrer"
                            class="text-slate-400 hover:text-blue-600 transition-colors">
                            <i class="fa-brands fa-linkedin text-xl"></i>
                        </a>
                    @endif
                </div>
            </div>
            <div
                class="mt-8 pt-8 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-medium text-slate-500">
                <p>&copy; {{ date('Y') }}
                    {{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze Portal' }}. All rights
                    reserved.</p>
                <p>Engineered by Bima Ryan.</p>
            </div>
        </div>
    </footer>

    <!-- Chatbot Widget -->
    <div id="ryaze-chatbot-widget" class="fixed bottom-6 right-6 z-50 font-sans">
        <!-- Chat Window -->
        <div id="ryaze-chat-window" class="hidden flex-col bg-white border border-slate-200 shadow-2xl rounded-2xl w-80 h-96 mb-4 overflow-hidden transition-all duration-300 transform origin-bottom-right">
            <div class="bg-indigo-600 px-4 py-3 text-white flex justify-between items-center shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                    <span class="font-bold text-sm">Ryaze Assistant</span>
                </div>
                <button id="ryaze-chat-close" class="text-indigo-100 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div id="ryaze-chat-messages" class="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50 text-sm flex flex-col">
                <!-- Welcome Message -->
                <div class="flex items-start gap-2">
                    <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center shrink-0 mt-0.5">
                        <i class="fa-solid fa-robot text-[10px] text-indigo-600"></i>
                    </div>
                    <div class="bg-white border border-slate-200 px-3 py-2 rounded-2xl rounded-tl-sm text-slate-700 shadow-sm max-w-[85%]">
                        Halo! Saya asisten AI Ryaze. Ada yang bisa saya bantu hari ini?
                    </div>
                </div>
            </div>
            <div class="p-3 bg-white border-t border-slate-100">
                <form id="ryaze-chat-form" class="flex items-center gap-2">
                    <input type="text" id="ryaze-chat-input" placeholder="Ketik pesan..." required class="flex-1 bg-slate-50 border border-slate-200 text-sm rounded-full px-4 py-2 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    <button type="submit" class="w-9 h-9 rounded-full bg-indigo-600 text-white flex items-center justify-center hover:bg-indigo-700 transition-colors shrink-0 shadow-sm disabled:opacity-50">
                        <i class="fa-solid fa-paper-plane text-[10px] -ml-0.5"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Toggle Button -->
        <button id="ryaze-chat-toggle" class="w-14 h-14 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full flex items-center justify-center shadow-lg shadow-indigo-200 transition-all hover:scale-105 ml-auto relative">
            <i class="fa-solid fa-message text-xl"></i>
            <span class="absolute top-0 right-0 w-3.5 h-3.5 bg-rose-500 border-2 border-white rounded-full"></span>
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('ryaze-chat-toggle');
            const closeBtn = document.getElementById('ryaze-chat-close');
            const chatWindow = document.getElementById('ryaze-chat-window');
            const chatForm = document.getElementById('ryaze-chat-form');
            const chatInput = document.getElementById('ryaze-chat-input');
            const messagesContainer = document.getElementById('ryaze-chat-messages');
            
            // Notification dot
            const notifDot = toggleBtn.querySelector('span');

            let chatHistory = [];

            // Toggle window
            const toggleChat = () => {
                chatWindow.classList.toggle('hidden');
                chatWindow.classList.toggle('flex');
                if(!chatWindow.classList.contains('hidden')) {
                    chatInput.focus();
                    if(notifDot) notifDot.style.display = 'none';
                }
            };

            toggleBtn.addEventListener('click', toggleChat);
            closeBtn.addEventListener('click', toggleChat);

            function addMessage(text, isUser = false) {
                const wrapper = document.createElement('div');
                wrapper.className = `flex items-start gap-2 ${isUser ? 'flex-row-reverse' : ''}`;
                
                let avatar = '';
                if(isUser) {
                    avatar = `<div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fa-solid fa-user text-[10px] text-slate-500"></i>
                              </div>`;
                } else {
                    avatar = `<div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fa-solid fa-robot text-[10px] text-indigo-600"></i>
                              </div>`;
                }

                const msgBubble = document.createElement('div');
                msgBubble.className = isUser 
                    ? 'bg-indigo-600 text-white px-3 py-2 rounded-2xl rounded-tr-sm shadow-sm max-w-[85%] break-words'
                    : 'bg-white border border-slate-200 px-3 py-2 rounded-2xl rounded-tl-sm text-slate-700 shadow-sm max-w-[85%] break-words';
                
                const formattedText = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                                          .replace(/\n/g, '<br>');
                msgBubble.innerHTML = formattedText;

                wrapper.innerHTML = avatar;
                wrapper.appendChild(msgBubble);
                messagesContainer.appendChild(wrapper);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }

            function addTypingIndicator() {
                const id = 'typing-' + Date.now();
                const wrapper = document.createElement('div');
                wrapper.id = id;
                wrapper.className = 'flex items-start gap-2';
                wrapper.innerHTML = `
                    <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center shrink-0 mt-0.5">
                        <i class="fa-solid fa-robot text-[10px] text-indigo-600"></i>
                    </div>
                    <div class="bg-white border border-slate-200 px-4 py-3 rounded-2xl rounded-tl-sm shadow-sm flex gap-1 items-center">
                        <div class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"></div>
                        <div class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                `;
                messagesContainer.appendChild(wrapper);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                return id;
            }

            function removeElement(id) {
                const el = document.getElementById(id);
                if (el) el.remove();
            }

            chatForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const text = chatInput.value.trim();
                if(!text) return;

                const submitBtn = chatForm.querySelector('button');
                
                // Add user message to UI
                addMessage(text, true);
                chatInput.value = '';
                chatInput.disabled = true;
                submitBtn.disabled = true;

                // Add loading
                const typingId = addTypingIndicator();

                try {
                    const response = await fetch('/chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            message: text,
                            history: chatHistory
                        })
                    });

                    removeElement(typingId);
                    
                    if(response.ok) {
                        const data = await response.json();
                        if(data.reply) {
                            addMessage(data.reply, false);
                            
                            chatHistory.push({ role: 'user', content: text });
                            chatHistory.push({ role: 'assistant', content: data.reply });
                            
                            if(chatHistory.length > 10) chatHistory = chatHistory.slice(-10);
                        } else {
                            addMessage("Maaf, terjadi kesalahan.", false);
                        }
                    } else {
                        addMessage("Maaf, gagal terhubung ke server.", false);
                    }
                } catch(err) {
                    removeElement(typingId);
                    addMessage("Terjadi kesalahan jaringan.", false);
                } finally {
                    chatInput.disabled = false;
                    submitBtn.disabled = false;
                    chatInput.focus();
                }
            });
        });
    </script>

    @include('components.hot-toast')
</body>

</html>
