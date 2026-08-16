<x-public-layout
    title="Jasa Pembuatan Website & Shared Hosting Indonesia"
    description="Jasa pembuatan website, aplikasi, dan joki Tugas Akhir. Shared hosting murah dengan auto-deploy, SSL gratis, database MySQL, web terminal, dan panel kontrol lengkap. Mulai dari Rp 10.000/bulan."
    body-class="antialiased selection:bg-indigo-600 selection:text-white relative"
    og-image="{{ url('/og-image.png') }}"
    :links="[
        ['label' => 'Tentang', 'href' => '#about'],
        ['label' => 'Layanan', 'href' => '#services'],
        ['label' => 'Harga', 'href' => '#pricing'],
        ['label' => 'Portofolio', 'href' => '#portfolio'],
        ['label' => 'Blog', 'href' => route('blog.index')],
    ]">

    @push('head')
        <meta name="theme-color" content="#4f46e5">
        <meta name="robots" content="index, follow, max-image-preview:large">
        <meta name="keywords" content="jasa pembuatan website, web hosting indonesia, hosting murah, shared hosting, jasa joki skripsi, pembuatan aplikasi web, auto deploy website, hosting laravel, jasa website polindra">
        <meta name="author" content="{{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze Portal' }}">
        <meta property="og:site_name" content="{{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze' }}">

        {{-- JSON-LD: Organization --}}
        <script type="application/ld+json" nonce="{{ csp_nonce() }}">
        {
            "@@context": "https://schema.org",
            "@type": "Organization",
            "@id": "{{ url('/') }}#organization",
            "name": "{{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze' }}",
            "url": "{{ url('/') }}",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ url('/og-image.png') }}",
                "width": 1200,
                "height": 630
            },
            "description": "Platform jasa pembuatan website, aplikasi, dan shared hosting Indonesia dengan auto-deploy, SSL gratis, dan database MySQL.",
            "sameAs": [
                @if(\App\Models\Setting::val('social_github')){{ '"' . \App\Models\Setting::val('social_github') . '",' }}@endif
                @if(\App\Models\Setting::val('social_instagram')){{ '"' . \App\Models\Setting::val('social_instagram') . '",' }}@endif
                @if(\App\Models\Setting::val('social_linkedin')){{ '"' . \App\Models\Setting::val('social_linkedin') . '"' }}@endif
            ]
        }
        </script>

        {{-- JSON-LD: WebSite --}}
        <script type="application/ld+json" nonce="{{ csp_nonce() }}">
        {
            "@@context": "https://schema.org",
            "@type": "WebSite",
            "@id": "{{ url('/') }}#website",
            "url": "{{ url('/') }}",
            "name": "{{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze' }}",
            "inLanguage": "id-ID",
            "publisher": { "@id": "{{ url('/') }}#organization" }
        }
        </script>

        {{-- JSON-LD: FAQPage --}}
        <script type="application/ld+json" nonce="{{ csp_nonce() }}">
        {
            "@@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [{
                "@type": "Question",
                "name": "Apa itu Ryaze?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Ryaze adalah platform layanan jasa pembuatan website dan aplikasi sekaligus penyedia shared hosting Indonesia dengan auto-deploy dari repositori Git, SSL gratis, database MySQL, web terminal, dan panel kontrol lengkap."
                }
            }, {
                "@type": "Question",
                "name": "Teknologi apa saja yang didukung hosting Ryaze?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Hosting Ryaze mendukung Node.js, PHP (termasuk Laravel), Python, React, Vue.js, dan website statis HTML. Setiap project di-deploy otomatis dari repositori Git Anda."
                }
            }, {
                "@type": "Question",
                "name": "Apakah SSL gratis tersedia?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Ya, setiap project hosting di Ryaze otomatis mendapatkan sertifikat SSL gratis sehingga website Anda aman dan diakses melalui HTTPS."
                }
            }, {
                "@type": "Question",
                "name": "Apakah tersedia database untuk project saya?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Ya, setiap project mendapatkan database MySQL bawaan yang dapat dikelola melalui panel, mini phpMyAdmin, dan API key untuk koneksi aplikasi."
                }
            }, {
                "@type": "Question",
                "name": "Apakah bisa request jasa pembuatan website atau aplikasi?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Bisa. Ryaze menerima pengerjaan sistem informasi, aplikasi SaaS, hingga prototipe fungsional Tugas Akhir atau Skripsi dengan arsitektur modern yang bersih dan terdokumentasi."
                }
            }, {
                "@type": "Question",
                "name": "Bagaimana cara mulai menggunakan Ryaze?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Cukup daftar akun secara gratis, pilih paket hosting yang sesuai, lalu deploy project Anda langsung dari repositori Git dalam hitungan menit."
                }
            }]
        }
        </script>

        <style nonce="{{ csp_nonce() }}">
            body {
                font-family: 'Inter', sans-serif;
                background-color: #ffffff;
                color: #111827;
                scroll-behavior: smooth;
            }

            .dark body {
                background-color: #0f172a;
                color: #f1f5f9;
            }

            /* Subtle grid pattern background */
            .bg-grid {
                background-image: linear-gradient(to right, #f1f5f9 1px, transparent 1px),
                    linear-gradient(to bottom, #f1f5f9 1px, transparent 1px);
                background-size: 40px 40px;
                background-position: center top;
            }

            .dark .bg-grid {
                background-image: linear-gradient(to right, #1e293b 1px, transparent 1px),
                    linear-gradient(to bottom, #1e293b 1px, transparent 1px);
            }

            /* Aurora glow blobs untuk hero */
            .hero-blob {
                position: absolute;
                border-radius: 9999px;
                filter: blur(80px);
                opacity: .45;
                pointer-events: none;
            }

            /* Strict borders for cards */
            .card-brutal {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                transition: all 0.25s ease;
            }

            .dark .card-brutal {
                background: rgba(30, 41, 59, 0.6);
                border-color: #334155;
            }

            .card-brutal:hover {
                border-color: #4f46e5;
                box-shadow: 0 12px 28px rgba(79, 70, 229, 0.08);
                transform: translateY(-3px);
            }

            .dark .card-brutal:hover {
                border-color: #6366f1;
                box-shadow: 0 12px 28px rgba(99, 102, 241, 0.15);
            }

            /* Gradient text but strictly monochrome/subtle */
            .text-gradient-mono {
                background: linear-gradient(to right, #4f46e5, #818cf8);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            /* Scroll reveal */
            .reveal {
                opacity: 0;
                transform: translateY(24px);
                transition: opacity .7s ease, transform .7s ease;
            }
            .reveal.reveal-visible {
                opacity: 1;
                transform: translateY(0);
            }
            @media (prefers-reduced-motion: reduce) {
                .reveal { opacity: 1; transform: none; transition: none; }
            }

            /* Hero stack logos */
            .stack-logo {
                transition: transform .2s ease, color .2s ease;
            }
            .stack-logo:hover {
                transform: translateY(-3px) scale(1.12);
            }

            /* Popular plan glow */
            .plan-popular {
                box-shadow: 0 0 0 2px #8b5cf6, 0 20px 45px -15px rgba(139, 92, 246, 0.35);
            }
        </style>
    @endpush

    <!-- HERO SECTION -->
    <section
        class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 bg-grid dark:bg-slate-950 min-h-[90vh] flex items-center border-b border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="hero-blob w-96 h-96 bg-indigo-300 -top-20 -left-20"></div>
        <div class="hero-blob w-80 h-80 bg-violet-300 top-40 right-0"></div>
        <div class="hero-blob w-72 h-72 bg-sky-300 -bottom-16 left-1/3"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-white dark:to-slate-950 pointer-events-none"></div>
        <div class="max-w-4xl mx-auto px-6 relative z-10 text-center">

            @php
                $activePromo = \App\Models\PromoEvent::where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->latest()
                    ->first();
            @endphp

            @if($activePromo)
                <a href="{{ $activePromo->target_url ?? '#' }}" class="block mb-8 transition-transform hover:scale-[1.02]">
                    @if($activePromo->banner_image)
                        <img src="{{ $activePromo->banner_url }}" class="w-full max-w-2xl mx-auto rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 h-auto object-cover max-h-48" alt="{{ $activePromo->title }}">
                    @else
                        <div class="inline-flex flex-col items-center justify-center p-6 w-full max-w-2xl mx-auto rounded-2xl shadow-lg bg-gradient-to-r from-indigo-500 to-purple-600 border border-indigo-400 dark:border-indigo-500/50">
                            <h3 class="text-xl md:text-2xl font-bold text-white">{{ $activePromo->title }}</h3>
                            @if($activePromo->description)
                                <p class="text-indigo-100 mt-2 text-sm">{{ $activePromo->description }}</p>
                            @endif
                        </div>
                    @endif
                </a>
            @else
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-slate-200 dark:border-slate-700 bg-white/80 dark:bg-slate-900/80 backdrop-blur text-slate-600 dark:text-slate-300 text-xs font-semibold mb-8 shadow-sm">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Sistem Deployment Tersedia
                </div>
            @endif

            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50 leading-[1.1] mb-6">
                Bangun Produk Digital Anda <br class="hidden md:block" />
                <span class="text-gradient-mono">Lebih Cepat & Kuat.</span>
            </h1>

            <p class="text-lg md:text-xl text-slate-500 dark:text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed font-medium">
                Jasa pembuatan website & aplikasi terpercaya, plus shared hosting Indonesia dengan auto-deploy, SSL
                gratis, dan database MySQL. Tim development profesional siap mengeksekusi visi teknologi Anda tanpa
                kompromi.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#services"
                    class="px-8 py-3.5 text-sm font-semibold rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-all hover:shadow-lg hover:shadow-indigo-500/30 flex items-center justify-center gap-2">
                    Jelajahi Layanan
                </a>
                <a href="#portfolio"
                    class="px-8 py-3.5 text-sm font-semibold rounded-md text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:border-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors flex items-center justify-center gap-2">
                    Lihat Portofolio
                </a>
            </div>

            <div class="mt-16 grid grid-cols-3 gap-4 max-w-2xl mx-auto">
                <div class="bg-white/70 dark:bg-slate-900/70 backdrop-blur border border-slate-200 dark:border-slate-700 rounded-lg py-5 px-3">
                    <p class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-slate-50">99.9%</p>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">Uptime Server</p>
                </div>
                <div class="bg-white/70 dark:bg-slate-900/70 backdrop-blur border border-slate-200 dark:border-slate-700 rounded-lg py-5 px-3">
                    <p class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-slate-50">100+</p>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">Project Selesai</p>
                </div>
                <div class="bg-white/70 dark:bg-slate-900/70 backdrop-blur border border-slate-200 dark:border-slate-700 rounded-lg py-5 px-3">
                    <p class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-slate-50">&lt;5 mnt</p>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">Auto-Deploy</p>
                </div>
            </div>

            <div class="mt-16 pt-8 border-t border-slate-200 dark:border-slate-700 max-w-3xl mx-auto">
                <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-6">Stack Teknologi Kami</p>
                <div class="flex flex-wrap justify-center gap-8 text-slate-300 dark:text-slate-400">
                    <i class="fa-brands fa-laravel text-3xl stack-logo text-red-500 dark:text-red-400"></i>
                    <i class="fa-brands fa-react text-3xl stack-logo text-cyan-500 dark:text-cyan-400"></i>
                    <i class="fa-brands fa-node-js text-3xl stack-logo text-green-500 dark:text-green-400"></i>
                    <i class="fa-brands fa-python text-3xl stack-logo text-yellow-500 dark:text-yellow-400"></i>
                    <i class="fa-brands fa-vuejs text-3xl stack-logo text-emerald-500 dark:text-emerald-400"></i>
                    <i class="fa-brands fa-aws text-3xl stack-logo text-orange-500 dark:text-orange-400"></i>
                    <i class="fa-brands fa-docker text-3xl stack-logo text-sky-500 dark:text-sky-400"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section id="about" class="py-24 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
                <!-- Text Content -->
                <div class="pt-4 reveal">
                    <h2 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mb-6">Di Balik Layar</h2>
                    <div class="w-12 h-1 bg-indigo-600 mb-8"></div>

                    <p class="text-base text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">
                        Saya <strong>Bima Ryan Alfarizi</strong>, mahasiswa D4 Rekayasa Perangkat Lunak di Politeknik
                        Negeri Indramayu. Visi utama saya adalah menciptakan standar rekayasa perangkat lunak yang
                        bersih, skalabel, dan fungsional.
                    </p>
                    <p class="text-base text-slate-600 dark:text-slate-300 mb-8 leading-relaxed">
                        Ryaze dikembangkan bukan hanya sebagai penyedia layanan, tetapi sebagai ekosistem di mana kode
                        dan infrastruktur berpadu dengan sempurna. Fokus kami ada pada efisiensi teknis dan keandalan
                        sistem.
                    </p>

                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1.5 bg-slate-100 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-700 rounded text-xs font-semibold text-slate-700 dark:text-slate-200">Fullstack Web</span>
                        <span class="px-3 py-1.5 bg-slate-100 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-700 rounded text-xs font-semibold text-slate-700 dark:text-slate-200">Shared Server</span>
                        <span class="px-3 py-1.5 bg-slate-100 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-700 rounded text-xs font-semibold text-slate-700 dark:text-slate-200">CI/CD Pipeline</span>
                        <span class="px-3 py-1.5 bg-slate-100 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-700 rounded text-xs font-semibold text-slate-700 dark:text-slate-200">Game Engine</span>
                    </div>
                </div>

                <!-- Clean Profile Card -->
                <div class="flex justify-center lg:justify-end">
                    <div class="w-full max-w-sm">
                        <div class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                            <div class="aspect-[3/4] bg-slate-100 dark:bg-slate-700/50 relative">
                                <img src="{{ asset('profil/bima.jpeg') }}"
                                    alt="Bima Ryan Alfarizi - Founder dan Lead Developer Ryaze"
                                    class="w-full h-full object-cover object-top">
                            </div>
                            <div class="p-6 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60">
                                <h3 class="font-bold text-slate-900 dark:text-slate-50 text-lg">Bima Ryan Alfarizi, S.Tr.Kom</h3>
                                <p class="text-indigo-600 dark:text-indigo-400 font-medium text-sm mb-4">Sarjana Terapan RPL Polindra</p>
                                
                                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
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
    <section id="services" class="py-24 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="mb-16 max-w-2xl reveal">
                <h2 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mb-4">Infrastruktur & Layanan</h2>
                <p class="text-slate-500 dark:text-slate-400 text-base">Kami merancang arsitektur web dan infrastruktur shared hosting yang
                    andal untuk melayani project Anda kapan saja.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 reveal">
                <!-- Web Dev Box -->
                <div class="card-brutal p-8 flex flex-col h-full group hover:bg-slate-50 dark:bg-slate-800/50 dark:hover:bg-slate-700/40">
                    <div class="w-12 h-12 bg-indigo-600 text-white rounded flex items-center justify-center mb-6">
                        <i class="fa-solid fa-laptop-code text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-slate-50 mb-3">Joki Pembuatan Web & Aplikasi</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-8 flex-1">
                        Layanan Joki untuk pembuatan sistem informasi, aplikasi SaaS, hingga prototipe fungsional Tugas
                        Akhir (Skripsi). Berbasis arsitektur modern yang bersih, efisien, dan terdokumentasi dengan
                        baik.
                    </p>
                    <ul class="space-y-3 mb-8 text-sm font-medium text-slate-600 dark:text-slate-300">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-indigo-500 dark:text-indigo-400"></i> Backend
                            & API Design</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-indigo-500 dark:text-indigo-400"></i> Frontend
                            Modern (React/Vue)</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-indigo-500 dark:text-indigo-400"></i> Keamanan
                            & Skalabilitas Tinggi</li>
                    </ul>
                    <a href="{{ route('register') }}"
                        class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 inline-flex items-center gap-2 group-hover:underline">
                        Mulai Konsultasi <i
                            class="fa-solid fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                    </a>
                </div>

                <!-- Hosting Box -->
                <div class="card-brutal p-8 flex flex-col h-full group hover:bg-slate-50 dark:bg-slate-800/50 dark:hover:bg-slate-700/40">
                    <div class="w-12 h-12 bg-indigo-600 text-white rounded flex items-center justify-center mb-6">
                        <i class="fa-solid fa-server text-xl"></i>
                    </div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-slate-50">Shared Application Hosting</h3>
                        @php
                            $normalPrice = (int) \App\Models\Setting::val('hosting_price', 10000);
                            $promoPrice = (int) \App\Models\Setting::val('hosting_promo_price', 0);
                        @endphp
                        <div class="text-right flex flex-col items-end leading-tight">
                            @if ($promoPrice > 0)
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 line-through">Rp
                                    {{ number_format($normalPrice, 0, ',', '.') }}</span>
                                <span class="text-sm font-bold text-emerald-600 dark:text-emerald-300">Rp
                                    {{ number_format($promoPrice, 0, ',', '.') }}<span
                                        class="text-[10px] text-slate-500 dark:text-slate-400 font-normal">/bln</span></span>
                            @else
                                <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">Rp
                                    {{ number_format($normalPrice, 0, ',', '.') }}<span
                                        class="text-[10px] text-slate-500 dark:text-slate-400 font-normal">/bln</span></span>
                            @endif
                        </div>
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6 flex-1">
                        Hosting murah dengan deployment otomatis tanpa pusing. Eksekusi repositori kode langsung ke
                        server publik dengan dukungan Web-Terminal, proses manager, dan database bawaan.
                    </p>
                    <ul class="space-y-2 mb-8 text-sm font-medium text-slate-600 dark:text-slate-300">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-indigo-500 dark:text-indigo-400"></i> Auto
                            Deploy (Node, PHP, Python, React, Vue)</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-indigo-500 dark:text-indigo-400"></i> Database
                            (MySQL) & SSL Gratis</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-indigo-500 dark:text-indigo-400"></i> File
                            Manager, Web Terminal</li>
                    </ul>
                    <a href="{{ route('register') }}"
                        class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 inline-flex items-center gap-2 group-hover:underline">
                        Deploy Sekarang <i
                            class="fa-solid fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- PRICING SECTION -->
    <section id="pricing" class="py-24 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="mb-14 text-center reveal">
                <span class="text-xs font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400 mb-3 block">Harga Transparan</span>
                <h2 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mb-4">Pilih Paket Hosting</h2>
                <p class="text-slate-500 dark:text-slate-400 text-base max-w-xl mx-auto">Deploy project Anda sekarang. Mulai dari harga terjangkau dengan fitur lengkap, siap scale sesuai kebutuhan.</p>
            </div>
            @php
                $homePlans = \App\Models\User::hostingPlans();
                $homeColorMap = [
                    'slate'  => ['accent' => 'border-t-slate-500', 'icon' => 'bg-slate-600', 'price' => 'text-slate-600 dark:text-slate-300', 'btn' => 'bg-slate-600 hover:bg-slate-700 dark:hover:bg-slate-600 text-white', 'check' => 'text-slate-500 dark:text-slate-400'],
                    'indigo' => ['accent' => 'border-t-indigo-500', 'icon' => 'bg-indigo-600', 'price' => 'text-indigo-600 dark:text-indigo-400', 'btn' => 'bg-indigo-600 hover:bg-indigo-700 text-white', 'check' => 'text-indigo-500 dark:text-indigo-400'],
                    'violet' => ['accent' => 'border-t-violet-500', 'icon' => 'bg-violet-600', 'price' => 'text-violet-600 dark:text-violet-300', 'btn' => 'bg-violet-600 hover:bg-violet-700 text-white', 'check' => 'text-violet-500 dark:text-violet-400'],
                    'amber'  => ['accent' => 'border-t-amber-400',  'icon' => 'bg-amber-500',  'price' => 'text-amber-600 dark:text-amber-300',  'btn' => 'bg-amber-500 hover:bg-amber-600 text-white',   'check' => 'text-amber-500 dark:text-amber-400'],
                ];
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 reveal">
                @foreach ($homePlans as $slug => $plan)
                    @php
                        $pricing = \App\Models\User::getPlanPricing($slug);
                        $hc = $homeColorMap[$plan['color']];
                        $isPopular = $slug === 'pro';
                    @endphp
                    <div class="relative flex flex-col bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all {{ $isPopular ? 'ring-2 ring-violet-500 scale-105 z-10' : '' }}">
                        <div class="h-1 w-full {{ $hc['accent'] }} border-t-4"></div>
                        @if ($isPopular)
                            <div class="absolute top-4 right-4">
                                <span class="text-[10px] font-bold bg-violet-100 dark:bg-violet-500/20 text-violet-700 dark:text-violet-300 border border-violet-200 dark:border-violet-500/40 px-2.5 py-1 rounded-full">⭐ POPULER</span>
                            </div>
                        @endif
                        <div class="p-8 flex-1">
                            <div class="w-11 h-11 {{ $hc['icon'] }} rounded-xl flex items-center justify-center mb-5">
                                <i class="fa-solid fa-server text-white"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-slate-50 mb-1">{{ $plan['label'] }}</h3>
                            <div class="flex flex-col mb-6">
                                @if($pricing['promo'] !== null)
                                    <span class="text-sm font-semibold text-slate-400 dark:text-slate-500 line-through decoration-rose-500 decoration-2">Rp {{ number_format($pricing['normal'], 0, ',', '.') }}</span>
                                @endif
                                <div class="flex items-baseline gap-1">
                                    <span class="text-4xl font-extrabold {{ $hc['price'] }}">Rp {{ number_format($pricing['active'], 0, ',', '.') }}</span>
                                    <span class="text-slate-400 dark:text-slate-500 text-sm">/bulan</span>
                                </div>
                            </div>
                            <ul class="space-y-3">
                                @foreach ($plan['features'] as $feat)
                                    <li class="flex items-center gap-2.5 text-sm text-slate-600 dark:text-slate-300">
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

            <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-4 reveal">
                <div class="flex items-center gap-3 justify-center md:justify-start">
                    <i class="fa-solid fa-shield-halved text-indigo-600 dark:text-indigo-400 text-lg"></i>
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">SSL Gratis</span>
                </div>
                <div class="flex items-center gap-3 justify-center md:justify-start">
                    <i class="fa-solid fa-database text-indigo-600 dark:text-indigo-400 text-lg"></i>
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Database MySQL</span>
                </div>
                <div class="flex items-center gap-3 justify-center md:justify-start">
                    <i class="fa-solid fa-rotate text-indigo-600 dark:text-indigo-400 text-lg"></i>
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Auto-Deploy Git</span>
                </div>
                <div class="flex items-center gap-3 justify-center md:justify-start">
                    <i class="fa-solid fa-headset text-indigo-600 dark:text-indigo-400 text-lg"></i>
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Support 1-on-1</span>
                </div>
            </div>
        </div>
    </section>

        <!-- PORTFOLIO SECTION -->
    <section id="portfolio" class="py-24 bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="mb-12 flex justify-between items-end reveal">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mb-2">Arsip Karya</h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm">Beberapa entitas digital yang telah kami kembangkan.</p>
                </div>
                <a href="https://github.com/bimaryan" target="_blank" rel="noopener noreferrer"
                    class="hidden md:flex text-sm font-semibold text-indigo-600 dark:text-indigo-400 items-center gap-2 hover:underline">
                    Lihat Repositori <i class="fa-brands fa-github text-lg"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 reveal">
                @forelse($portfolios as $portfolio)
                    <div class="card-brutal flex flex-col overflow-hidden bg-white dark:bg-slate-800/60 group">
                        @if ($portfolio->link_preview)
                            <a href="{{ $portfolio->link_preview }}" target="_blank" rel="noopener noreferrer"
                                class="block h-48 border-b border-slate-100 dark:border-slate-700 bg-slate-100 dark:bg-slate-700/50 overflow-hidden relative">
                            @else
                                <div
                                    class="block h-48 border-b border-slate-100 dark:border-slate-700 bg-slate-100 dark:bg-slate-700/50 overflow-hidden relative">
                        @endif

                        @if ($portfolio->image_path)
                            <img src="{{ Storage::url($portfolio->image_path) }}" alt="{{ $portfolio->title }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60">
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
                                    class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-[10px] uppercase font-bold rounded">{{ $tag }}</span>
                            @endforeach
                        @endif
                    </div>

                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-50 mb-2">{{ $portfolio->title }}</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm line-clamp-3 mb-4 flex-1 leading-relaxed">
                        {{ $portfolio->description }}</p>

                    <div class="flex items-center gap-3 flex-wrap mt-auto pt-4 border-t border-slate-100 dark:border-slate-700">
                        @if ($portfolio->link_github)
                            <a href="{{ $portfolio->link_github }}" target="_blank" rel="noopener noreferrer"
                                class="text-xs font-semibold text-slate-900 dark:text-slate-50 hover:text-indigo-600 dark:hover:text-indigo-400 dark:hover:text-indigo-400 transition-colors flex items-center gap-1.5">
                                <i class="fa-brands fa-github text-sm"></i> Code
                            </a>
                        @endif
                        @if ($portfolio->link_journal)
                            <a href="{{ $portfolio->link_journal }}" target="_blank" rel="noopener noreferrer"
                                class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-400 dark:hover:text-indigo-300 transition-colors flex items-center gap-1.5">
                                <i class="fa-solid fa-book-open text-sm"></i> Jurnal
                            </a>
                        @endif
                        @if ($portfolio->certificate_path)
                            <a href="{{ Storage::url($portfolio->certificate_path) }}" target="_blank" rel="noopener noreferrer"
                                class="text-xs font-semibold text-amber-600 dark:text-amber-300 hover:text-amber-700 dark:hover:text-amber-300 transition-colors flex items-center gap-1.5">
                                <i class="fa-solid fa-certificate text-sm"></i> Sertifikat
                            </a>
                        @endif
                        @if ($portfolio->link_copyright)
                            <a href="{{ $portfolio->link_copyright }}" target="_blank" rel="noopener noreferrer"
                                class="text-xs font-semibold text-amber-600 dark:text-amber-300 hover:text-amber-700 dark:hover:text-amber-300 transition-colors flex items-center gap-1.5">
                                <i class="fa-solid fa-shield-halved text-sm"></i> Hak Cipta
                            </a>
                        @endif
                        @if ($portfolio->link_preview)
                            <a href="{{ $portfolio->link_preview }}" target="_blank" rel="noopener noreferrer"
                                class="text-xs font-semibold text-slate-900 dark:text-slate-50 hover:text-indigo-600 dark:hover:text-indigo-400 dark:hover:text-indigo-400 transition-colors flex items-center gap-1.5 ml-auto">
                                Visit <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 border border-dashed border-slate-300 dark:border-slate-600 rounded-lg text-center bg-white dark:bg-slate-800/60">
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Data arsip belum tersedia.</p>
            </div>
            @endforelse
        </div>

        <div class="mt-8 md:hidden">
            <a href="https://github.com/bimaryan" target="_blank" rel="noopener noreferrer"
                class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 inline-flex items-center gap-2 hover:underline">
                Lihat Repositori <i class="fa-brands fa-github text-lg"></i>
            </a>
        </div>
        </div>
    </section>

    <!-- BLOG SECTION -->
    <section class="py-24 bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-700" id="blog">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-12 reveal">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50 mb-2">Artikel Terbaru</h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm max-w-2xl">Tulisan seputar web development, tips hosting, dan
                        wawasan teknologi lainnya dari tim Ryaze.</p>
                </div>
                <a href="{{ route('blog.index') }}"
                    class="hidden md:inline-flex text-sm font-semibold text-indigo-600 dark:text-indigo-400 items-center gap-2 hover:underline">
                    Lihat Semua Artikel <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 reveal">
                @forelse($articles as $article)
                    <a href="{{ route('blog.show', $article->slug) }}"
                        class="group card-brutal overflow-hidden flex flex-col">
                        @if ($article->cover_image)
                            <div class="h-48 overflow-hidden bg-slate-100 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700">
                                <img src="{{ Storage::url($article->cover_image) }}" alt="{{ $article->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                        @else
                            <div
                                class="h-48 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-300 dark:text-slate-400">
                                <i class="fa-solid fa-newspaper text-5xl"></i>
                            </div>
                        @endif
                        <div class="p-6 flex flex-col flex-1">
                            @if ($article->category)
                                <span
                                    class="text-[10px] font-bold uppercase text-indigo-600 dark:text-indigo-400 mb-2">{{ $article->category->name }}</span>
                            @endif
                            <h3
                                class="text-lg font-bold text-slate-900 dark:text-slate-50 mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 dark:group-hover:text-indigo-400 transition-colors line-clamp-2">
                                {{ $article->title }}</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm line-clamp-2 mb-4 flex-1">
                                {{ $article->excerpt ?: Str::limit(strip_tags($article->body), 100) }}</p>
                            <div
                                class="flex items-center gap-3 text-xs text-slate-400 dark:text-slate-500 mt-auto pt-4 border-t border-slate-100 dark:border-slate-700">
                                <span>{{ $article->published_at?->format('d M Y') }}</span>
                                <span>&middot;</span>
                                <span>{{ $article->reading_time }} min</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div
                        class="col-span-full py-12 border border-dashed border-slate-300 dark:border-slate-600 rounded-lg text-center bg-white dark:bg-slate-800/60">
                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Belum ada artikel.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8 md:hidden text-center">
                <a href="{{ route('blog.index') }}"
                    class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 inline-flex items-center gap-2 hover:underline">
                    Lihat Semua Artikel <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- FAQ SECTION -->
    <section id="faq" class="py-24 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700">
        <div class="max-w-3xl mx-auto px-6 lg:px-8">
            <div class="mb-14 text-center reveal">
                <span class="text-xs font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400 mb-3 block">Pertanyaan Umum</span>
                <h2 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mb-4">Yang Sering Ditanyakan</h2>
                <p class="text-slate-500 dark:text-slate-400 text-base">Semua yang perlu Anda ketahui sebelum deploy atau memesan jasa pembuatan website.</p>
            </div>

            <div class="space-y-4 reveal">
                <details class="card-brutal p-6 group" open>
                    <summary class="cursor-pointer font-bold text-slate-900 dark:text-slate-50 flex items-center justify-between gap-4">
                        Apa itu Ryaze?
                        <i class="fa-solid fa-chevron-down text-xs text-slate-400 dark:text-slate-500 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mt-4">
                        Ryaze adalah platform layanan jasa pembuatan website dan aplikasi sekaligus penyedia shared
                        hosting Indonesia dengan auto-deploy dari repositori Git, SSL gratis, database MySQL, web
                        terminal, dan panel kontrol lengkap.
                    </p>
                </details>

                <details class="card-brutal p-6 group">
                    <summary class="cursor-pointer font-bold text-slate-900 dark:text-slate-50 flex items-center justify-between gap-4">
                        Teknologi apa saja yang didukung hosting Ryaze?
                        <i class="fa-solid fa-chevron-down text-xs text-slate-400 dark:text-slate-500 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mt-4">
                        Hosting Ryaze mendukung Node.js, PHP (termasuk Laravel), Python, React, Vue.js, dan website
                        statis HTML. Setiap project di-deploy otomatis dari repositori Git Anda.
                    </p>
                </details>

                <details class="card-brutal p-6 group">
                    <summary class="cursor-pointer font-bold text-slate-900 dark:text-slate-50 flex items-center justify-between gap-4">
                        Apakah SSL gratis tersedia?
                        <i class="fa-solid fa-chevron-down text-xs text-slate-400 dark:text-slate-500 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mt-4">
                        Ya, setiap project hosting di Ryaze otomatis mendapatkan sertifikat SSL gratis sehingga website
                        Anda aman dan diakses melalui HTTPS.
                    </p>
                </details>

                <details class="card-brutal p-6 group">
                    <summary class="cursor-pointer font-bold text-slate-900 dark:text-slate-50 flex items-center justify-between gap-4">
                        Apakah tersedia database untuk project saya?
                        <i class="fa-solid fa-chevron-down text-xs text-slate-400 dark:text-slate-500 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mt-4">
                        Ya, setiap project mendapatkan database MySQL bawaan yang dapat dikelola melalui panel, mini
                        phpMyAdmin, dan API key untuk koneksi aplikasi.
                    </p>
                </details>

                <details class="card-brutal p-6 group">
                    <summary class="cursor-pointer font-bold text-slate-900 dark:text-slate-50 flex items-center justify-between gap-4">
                        Apakah bisa request jasa pembuatan website atau aplikasi?
                        <i class="fa-solid fa-chevron-down text-xs text-slate-400 dark:text-slate-500 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mt-4">
                        Bisa. Ryaze menerima pengerjaan sistem informasi, aplikasi SaaS, hingga prototipe fungsional
                        Tugas Akhir atau Skripsi dengan arsitektur modern yang bersih dan terdokumentasi.
                    </p>
                </details>

                <details class="card-brutal p-6 group">
                    <summary class="cursor-pointer font-bold text-slate-900 dark:text-slate-50 flex items-center justify-between gap-4">
                        Bagaimana cara mulai menggunakan Ryaze?
                        <i class="fa-solid fa-chevron-down text-xs text-slate-400 dark:text-slate-500 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mt-4">
                        Cukup daftar akun secara gratis, pilih paket hosting yang sesuai, lalu deploy project Anda
                        langsung dari repositori Git dalam hitungan menit.
                    </p>
                </details>
            </div>
        </div>
    </section>

    <!-- CALL TO ACTION -->
    <section class="py-24 bg-indigo-600 text-white text-center px-6 relative overflow-hidden">
        <div class="hero-blob w-80 h-80 bg-indigo-400 -top-24 -right-24" style="opacity:.3"></div>
        <div class="hero-blob w-80 h-80 bg-violet-500 -bottom-24 -left-24" style="opacity:.25"></div>
        <div class="max-w-3xl mx-auto relative z-10 reveal">
            <h2 class="text-3xl md:text-5xl font-bold tracking-tight mb-6">Siap Mengeksekusi Ide?</h2>
            <p class="text-indigo-200 text-lg mb-10 max-w-xl mx-auto">Daftar sekarang untuk mengakses lingkungan
                deployment yang kuat atau hubungi kami untuk pengerjaan perangkat lunak Anda.</p>
            <a href="{{ route('register') }}"
                class="inline-block px-8 py-3 bg-white dark:bg-slate-800/60 text-indigo-700 dark:text-indigo-300 text-sm font-bold rounded-md hover:bg-indigo-50 dark:hover:bg-indigo-500/10 dark:hover:bg-indigo-500/10 transition-colors shadow-lg shadow-indigo-900/20">
                Mulai Secara Gratis
            </a>
        </div>
    </section>

    <!-- Chatbot Widget -->
    <div id="ryaze-chatbot-widget" class="fixed bottom-6 right-6 z-50 font-sans">
        <!-- Chat Window -->
        <div id="ryaze-chat-window" class="hidden flex-col bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 shadow-2xl rounded-2xl w-80 h-96 mb-4 overflow-hidden transition-all duration-300 transform origin-bottom-right">
            <div class="bg-indigo-600 px-4 py-3 text-white flex justify-between items-center shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                    <span class="font-bold text-sm">Ryaze Assistant</span>
                </div>
                <button id="ryaze-chat-close" class="text-indigo-100 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div id="ryaze-chat-messages" class="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50 dark:bg-slate-800/60 text-sm flex flex-col">
                <!-- Welcome Message -->
                <div class="flex items-start gap-2">
                    <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center shrink-0 mt-0.5">
                        <i class="fa-solid fa-robot text-[10px] text-indigo-600 dark:text-indigo-400"></i>
                    </div>
                    <div class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 px-3 py-2 rounded-2xl rounded-tl-sm text-slate-700 dark:text-slate-200 shadow-sm max-w-[85%]">
                        Halo! Saya asisten AI Ryaze. Ada yang bisa saya bantu hari ini?
                    </div>
                </div>
            </div>
            <div class="p-3 bg-white dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-700">
                <form id="ryaze-chat-form" class="flex items-center gap-2">
                    <input type="text" id="ryaze-chat-input" placeholder="Ketik pesan..." required class="flex-1 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-sm rounded-full px-4 py-2 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    <button type="submit" class="w-9 h-9 rounded-full bg-indigo-600 text-white flex items-center justify-center hover:bg-indigo-700 transition-colors shrink-0 shadow-sm disabled:opacity-50">
                        <i class="fa-solid fa-paper-plane text-[10px] -ml-0.5"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Toggle Button -->
        <button id="ryaze-chat-toggle" class="w-14 h-14 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full flex items-center justify-center shadow-lg shadow-indigo-200 transition-all hover:scale-105 ml-auto relative">
            <i class="fa-solid fa-message text-xl"></i>
            <span class="absolute top-0 right-0 w-3.5 h-3.5 bg-rose-500 border-2 border-white dark:border-slate-900 rounded-full"></span>
        </button>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const revealEls = document.querySelectorAll('.reveal');
                if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    const io = new IntersectionObserver((entries) => {
                        entries.forEach((entry) => {
                            if (entry.isIntersecting) {
                                entry.target.classList.add('reveal-visible');
                                io.unobserve(entry.target);
                            }
                        });
                    }, { threshold: 0.08 });
                    revealEls.forEach((el) => io.observe(el));
                } else {
                    revealEls.forEach((el) => el.classList.add('reveal-visible'));
                }
            });
        </script>
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
                    if (!chatWindow.classList.contains('hidden')) {
                        chatInput.focus();
                        if (notifDot) notifDot.style.display = 'none';
                    }
                };

                toggleBtn.addEventListener('click', toggleChat);
                closeBtn.addEventListener('click', toggleChat);

                function addMessage(text, isUser = false) {
                    const wrapper = document.createElement('div');
                    wrapper.className = `flex items-start gap-2 ${isUser ? 'flex-row-reverse' : ''}`;

                    let avatar = '';
                    if (isUser) {
                        avatar = `<div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="fa-solid fa-user text-[10px] text-slate-500 dark:text-slate-400"></i>
                                  </div>`;
                    } else {
                        avatar = `<div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="fa-solid fa-robot text-[10px] text-indigo-600 dark:text-indigo-400"></i>
                                  </div>`;
                    }

                    const msgBubble = document.createElement('div');
                    msgBubble.className = isUser
                        ? 'bg-indigo-600 text-white px-3 py-2 rounded-2xl rounded-tr-sm shadow-sm max-w-[85%] break-words'
                        : 'bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 px-3 py-2 rounded-2xl rounded-tl-sm text-slate-700 dark:text-slate-200 shadow-sm max-w-[85%] break-words';

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
                        <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fa-solid fa-robot text-[10px] text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                        <div class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 px-4 py-3 rounded-2xl rounded-tl-sm shadow-sm flex gap-1 items-center">
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
                    if (!text) return;

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

                        if (response.ok) {
                            const data = await response.json();
                            if (data.reply) {
                                addMessage(data.reply, false);

                                chatHistory.push({ role: 'user', content: text });
                                chatHistory.push({ role: 'assistant', content: data.reply });

                                if (chatHistory.length > 10) chatHistory = chatHistory.slice(-10);
                            } else {
                                addMessage("Maaf, terjadi kesalahan.", false);
                            }
                        } else {
                            addMessage("Maaf, gagal terhubung ke server.", false);
                        }
                    } catch (err) {
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
    @endpush
</x-public-layout>
