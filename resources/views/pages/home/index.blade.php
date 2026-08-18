<x-public-layout
    title="Jasa Pembuatan Website & Shared Hosting Indonesia"
    description="Jasa pembuatan website, aplikasi, dan joki Tugas Akhir. Shared hosting murah dengan auto-deploy, SSL gratis, database MySQL, web terminal, dan panel kontrol lengkap. Mulai dari Rp 10.000/bulan."
    body-class="bg-slate-50 font-sans antialiased text-slate-900 dark:bg-slate-950 dark:text-slate-100 selection:bg-indigo-600 selection:text-white relative"
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
                color: #0f172a;
                scroll-behavior: smooth;
            }

            .dark body {
                background-color: #09090b; /* Zinc 950 */
                color: #f8fafc;
            }

            /* Smooth Scroll Reveal */
            .reveal {
                opacity: 0;
                transform: translateY(20px);
                transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            }
            .reveal.reveal-visible {
                opacity: 1;
                transform: translateY(0);
            }
            @media (prefers-reduced-motion: reduce) {
                .reveal { opacity: 1; transform: none; transition: none; }
            }

            /* Stack Logos Hover */
            .stack-logo {
                transition: all 0.3s ease;
                opacity: 0.6;
            }
            .stack-logo:hover {
                opacity: 1;
            }

            /* Dotted Grid Background - Clean SaaS style */
            .bg-dot-pattern {
                background-image: radial-gradient(rgba(15, 23, 42, 0.1) 1px, transparent 1px);
                background-size: 24px 24px;
            }
            .dark .bg-dot-pattern {
                background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            }
            .hero-glow {
                position: absolute;
                width: 600px;
                height: 600px;
                background: radial-gradient(circle, rgba(79, 70, 229, 0.15) 0%, rgba(0,0,0,0) 70%);
                top: -200px;
                left: 50%;
                transform: translateX(-50%);
                pointer-events: none;
                z-index: 0;
            }
        </style>
    @endpush

    <!-- HERO SECTION -->
    <section class="relative pt-32 pb-24 lg:pt-48 lg:pb-32 bg-white dark:bg-[#030712] min-h-[90vh] flex items-center overflow-hidden border-b border-slate-200 dark:border-white/5">
        <!-- Minimal Background Pattern -->
        <div class="absolute inset-0 bg-dot-pattern z-0 opacity-40"></div>
        <div class="hero-glow hidden dark:block"></div>
        <div class="absolute top-0 inset-x-0 h-40 bg-gradient-to-b from-white dark:from-[#030712] to-transparent z-0"></div>
        <div class="absolute bottom-0 inset-x-0 h-40 bg-gradient-to-t from-white dark:from-[#030712] to-transparent z-0"></div>
        
        <div class="max-w-5xl mx-auto px-6 relative z-10 text-center">
            
            <!-- Sleek Badge -->
            <x-ui.promo-banner class="mb-10 max-w-4xl mx-auto">
                <x-slot name="fallback">
                    <div class="inline-flex items-center gap-2.5 px-3 py-1 rounded-full border border-slate-200 dark:border-white/10 bg-white/50 dark:bg-white/5 backdrop-blur-md shadow-sm text-slate-600 dark:text-slate-300 text-xs sm:text-sm font-medium mb-8 transition-colors hover:border-slate-300 dark:hover:border-white/20">
                        <span class="flex h-1.5 w-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></span>
                        Sistem Deployment Tersedia
                        <i class="fa-solid fa-arrow-right text-[10px] ml-1 opacity-50"></i>
                    </div>
                </x-slot>
            </x-ui.promo-banner>

            <h1 class="text-5xl md:text-7xl font-bold tracking-tighter text-slate-900 dark:text-transparent dark:bg-clip-text dark:bg-gradient-to-b dark:from-white dark:to-slate-400 leading-tight mb-6">
                Bangun Produk Digital Anda <br class="hidden md:block" />
                Lebih Cepat & Kuat.
            </h1>

            <p class="text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed font-normal">
                Jasa pembuatan website & aplikasi terpercaya, plus shared hosting Indonesia dengan auto-deploy, SSL gratis, dan database MySQL. Tim development profesional siap mengeksekusi visi teknologi Anda tanpa kompromi.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4 mb-16">
                <a href="#services"
                    class="px-8 py-3 text-sm font-semibold rounded-full text-white bg-slate-900 dark:bg-white dark:text-slate-900 hover:bg-slate-800 dark:hover:bg-slate-200 shadow-[0_0_20px_rgba(255,255,255,0.1)] dark:shadow-[0_0_20px_rgba(255,255,255,0.2)] transition-all flex items-center justify-center gap-2">
                    Jelajahi Layanan
                </a>
                <a href="#portfolio"
                    class="px-8 py-3 text-sm font-semibold rounded-full text-slate-700 dark:text-slate-300 bg-transparent border border-slate-200 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors flex items-center justify-center gap-2">
                    Lihat Portofolio
                </a>
            </div>

            <!-- Sleek Metrics -->
            <div class="grid grid-cols-3 gap-6 max-w-3xl mx-auto mb-16">
                <div class="flex flex-col items-center justify-center">
                    <p class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">99.9%</p>
                    <p class="text-[10px] font-bold text-slate-500 dark:text-slate-500 mt-2 uppercase tracking-[0.2em]">Uptime Server</p>
                </div>
                <div class="flex flex-col items-center justify-center border-x border-slate-200/50 dark:border-white/10">
                    <p class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">100+</p>
                    <p class="text-[10px] font-bold text-slate-500 dark:text-slate-500 mt-2 uppercase tracking-[0.2em]">Project Selesai</p>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <p class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">&lt;5 mnt</p>
                    <p class="text-[10px] font-bold text-slate-500 dark:text-slate-500 mt-2 uppercase tracking-[0.2em]">Auto-Deploy</p>
                </div>
            </div>

            <!-- Minimalist Tech Stack -->
            <div class="pt-10 border-t border-slate-200/50 dark:border-white/5 max-w-4xl mx-auto">
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-600 uppercase tracking-[0.2em] mb-8">Didukung oleh Teknologi Modern</p>
                <div class="flex flex-wrap justify-center gap-10 text-slate-400 dark:text-slate-500">
                    <i class="fa-brands fa-laravel text-3xl md:text-4xl stack-logo"></i>
                    <i class="fa-brands fa-react text-3xl md:text-4xl stack-logo"></i>
                    <i class="fa-brands fa-node-js text-3xl md:text-4xl stack-logo"></i>
                    <i class="fa-brands fa-python text-3xl md:text-4xl stack-logo"></i>
                    <i class="fa-brands fa-vuejs text-3xl md:text-4xl stack-logo"></i>
                    <i class="fa-brands fa-aws text-3xl md:text-4xl stack-logo"></i>
                    <i class="fa-brands fa-docker text-3xl md:text-4xl stack-logo"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section id="about" class="py-24 bg-slate-50 dark:bg-[#030712] border-b border-slate-200 dark:border-white/5">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <!-- Text Content -->
                <div class="reveal">
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400 mb-3 block">Di Balik Layar</span>
                    <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-slate-900 dark:text-white mb-6">Dedikasi Terhadap Kode yang Bersih.</h2>
                    
                    <p class="text-base text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                        Saya <strong>Bima Ryan Alfarizi</strong>, mahasiswa D4 Rekayasa Perangkat Lunak di Politeknik
                        Negeri Indramayu. Visi utama saya adalah menciptakan standar rekayasa perangkat lunak yang
                        bersih, skalabel, dan fungsional.
                    </p>
                    <p class="text-base text-slate-600 dark:text-slate-400 mb-8 leading-relaxed">
                        Ryaze dikembangkan bukan hanya sebagai penyedia layanan, tetapi sebagai ekosistem di mana kode
                        dan infrastruktur berpadu dengan sempurna. Fokus kami ada pada efisiensi teknis dan keandalan
                        sistem.
                    </p>

                    <div class="flex flex-wrap gap-2">
                        <span class="px-4 py-1.5 bg-indigo-50 dark:bg-white/5 backdrop-blur-sm border border-indigo-200 dark:border-white/10 rounded-full text-[11px] font-bold tracking-[0.1em] text-indigo-600 dark:text-slate-300">FULLSTACK WEB</span>
                        <span class="px-4 py-1.5 bg-indigo-50 dark:bg-white/5 backdrop-blur-sm border border-indigo-200 dark:border-white/10 rounded-full text-[11px] font-bold tracking-[0.1em] text-indigo-600 dark:text-slate-300">SHARED SERVER</span>
                        <span class="px-4 py-1.5 bg-indigo-50 dark:bg-white/5 backdrop-blur-sm border border-indigo-200 dark:border-white/10 rounded-full text-[11px] font-bold tracking-[0.1em] text-indigo-600 dark:text-slate-300">CI/CD PIPELINE</span>
                        <span class="px-4 py-1.5 bg-indigo-50 dark:bg-white/5 backdrop-blur-sm border border-indigo-200 dark:border-white/10 rounded-full text-[11px] font-bold tracking-[0.1em] text-indigo-600 dark:text-slate-300">GAME ENGINE</span>
                    </div>
                </div>

                <!-- Clean Profile Card -->
                <div class="flex justify-center lg:justify-end reveal">
                    <div class="w-full max-w-sm">
                        <div class="rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden bg-white dark:bg-white/5 dark:backdrop-blur-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)] relative group">
                            <!-- Subtle card glow -->
                            <div class="absolute -inset-0.5 bg-gradient-to-br from-white/20 to-transparent opacity-0 dark:opacity-100 pointer-events-none rounded-2xl"></div>
                            
                            <div class="aspect-[4/5] bg-slate-100 dark:bg-transparent relative overflow-hidden">
                                <img src="{{ asset('profil/bima.jpeg') }}"
                                    alt="Bima Ryan Alfarizi - Founder dan Lead Developer Ryaze"
                                    class="w-full h-full object-cover object-top transition-all duration-700 group-hover:scale-105">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                                
                                <div class="absolute bottom-0 left-0 right-0 p-6 z-10">
                                    <h3 class="font-bold text-white text-xl tracking-tight">Bima Ryan Alfarizi, S.Tr.Kom</h3>
                                    <p class="text-slate-300 font-medium text-sm mt-1">Sarjana Terapan RPL Polindra</p>
                                </div>
                            </div>
                            <div class="px-6 py-4 border-t border-slate-200 dark:border-white/10 flex items-center justify-between text-xs font-medium text-slate-500 dark:text-slate-400 relative z-10 bg-white dark:bg-transparent">
                                <span class="flex items-center gap-2"><i class="fa-solid fa-location-dot"></i> Indramayu, ID</span>
                                <span class="flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></div> Available for Hire</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SERVICES SECTION -->
    <section id="services" class="py-24 bg-white dark:bg-[#030712] border-b border-slate-200 dark:border-white/5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-indigo-50/50 dark:from-white/[0.02] to-transparent pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
            <div class="mb-16 max-w-2xl reveal">
                <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400 mb-3 block">Layanan</span>
                <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-slate-900 dark:text-white mb-4">Infrastruktur & Layanan.</h2>
                <p class="text-slate-500 dark:text-slate-400 text-lg leading-relaxed">Kami merancang arsitektur web dan infrastruktur shared hosting yang andal untuk melayani project Anda kapan saja.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 reveal">
                <!-- Web Dev Box -->
                <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 dark:backdrop-blur-xl p-8 lg:p-10 flex flex-col h-full group shadow-sm transition-all hover:border-slate-300 dark:hover:border-white/20 hover:-translate-y-1 relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl pointer-events-none"></div>
                    <div class="relative z-10">
                        <div class="w-12 h-12 rounded-lg bg-white dark:bg-white/10 border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white flex items-center justify-center mb-8 shadow-sm">
                            <i class="fa-solid fa-laptop-code text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight mb-4">Jasa Pembuatan Sistem</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-base leading-relaxed mb-8">
                            Pengerjaan sistem informasi, aplikasi SaaS, hingga prototipe fungsional Tugas Akhir (Skripsi). Berbasis arsitektur modern yang bersih, efisien, dan terdokumentasi.
                        </p>
                        <ul class="space-y-4 mb-10 text-sm font-medium text-slate-600 dark:text-slate-300 flex-1">
                            <li class="flex items-center gap-3">
                                <div class="flex-shrink-0 text-slate-400 dark:text-slate-500"><i class="fa-solid fa-check"></i></div>
                                Backend & API Design
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="flex-shrink-0 text-slate-400 dark:text-slate-500"><i class="fa-solid fa-check"></i></div>
                                Frontend Modern (React/Vue)
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="flex-shrink-0 text-slate-400 dark:text-slate-500"><i class="fa-solid fa-check"></i></div>
                                Keamanan & Skalabilitas Tinggi
                            </li>
                        </ul>
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-2.5 text-sm font-semibold text-slate-900 dark:text-white bg-white dark:bg-white/10 border border-slate-300 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-white/20 rounded-full transition-colors shadow-sm">
                            Mulai Konsultasi <i class="fa-solid fa-arrow-right ml-2 text-[10px]"></i>
                        </a>
                    </div>
                </div>

                <!-- Hosting Box -->
                <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 dark:backdrop-blur-xl p-8 lg:p-10 flex flex-col h-full group shadow-sm transition-all hover:border-slate-300 dark:hover:border-white/20 hover:-translate-y-1 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl pointer-events-none"></div>
                    <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none transition-transform duration-700 group-hover:scale-110">
                        <i class="fa-solid fa-server text-9xl text-slate-900 dark:text-white"></i>
                    </div>
                    <div class="relative z-10 flex-1 flex flex-col">
                        <div class="w-12 h-12 rounded-lg bg-white dark:bg-white/10 border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white flex items-center justify-center mb-8 shadow-sm">
                            <i class="fa-solid fa-server text-xl"></i>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-4 gap-4">
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Shared App Hosting</h3>
                            @php
                                $starterPricing = \App\Models\User::getPlanPricing('starter');
                                $normalPrice = $starterPricing['normal'];
                                $promoPrice = (int) ($starterPricing['promo'] ?? 0);
                            @endphp
                            <div class="flex flex-col sm:items-end">
                                @if ($promoPrice > 0)
                                    <span class="text-[11px] font-medium text-slate-400 dark:text-slate-500 line-through mb-1">Rp {{ number_format($normalPrice, 0, ',', '.') }}</span>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Rp {{ number_format($promoPrice, 0, ',', '.') }}</span>
                                        <span class="text-xs text-slate-500">/bln</span>
                                    </div>
                                @else
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Rp {{ number_format($normalPrice, 0, ',', '.') }}</span>
                                        <span class="text-xs text-slate-500">/bln</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <p class="text-slate-500 dark:text-slate-400 text-base leading-relaxed mb-8">
                            Hosting murah dengan deployment otomatis tanpa pusing. Eksekusi repositori kode langsung ke server publik dengan dukungan Web-Terminal, proses manager, dan database bawaan.
                        </p>

                        <ul class="space-y-4 mb-10 text-sm font-medium text-slate-600 dark:text-slate-300 flex-1">
                            <li class="flex items-center gap-3">
                                <div class="flex-shrink-0 text-slate-400 dark:text-slate-500"><i class="fa-solid fa-check"></i></div>
                                Auto Deploy (Node, PHP, Python, dsb)
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="flex-shrink-0 text-slate-400 dark:text-slate-500"><i class="fa-solid fa-check"></i></div>
                                Database (MySQL) & SSL Gratis
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="flex-shrink-0 text-slate-400 dark:text-slate-500"><i class="fa-solid fa-check"></i></div>
                                File Manager & Web Terminal Lengkap
                            </li>
                        </ul>

                        <a href="{{ route('register') }}"
                            class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-2.5 text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200 rounded-full transition-colors shadow-sm">
                            Deploy Sekarang <i class="fa-solid fa-arrow-right ml-2 text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PRICING SECTION -->
    <section id="pricing" class="py-24 bg-slate-50 dark:bg-[#030712] border-b border-slate-200 dark:border-white/5 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
            <div class="mb-16 text-center reveal">
                <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400 mb-3 block">Harga Transparan</span>
                <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-slate-900 dark:text-white mb-4">Pilih Paket Hosting</h2>
                <p class="text-slate-500 dark:text-slate-400 text-lg max-w-2xl mx-auto">Deploy project Anda sekarang. Mulai dari harga terjangkau dengan fitur lengkap, siap scale sesuai kebutuhan.</p>
            </div>
            
            @php
                $homePlans = \App\Models\User::hostingPlans();
                $homeColorMap = [
                    'slate'  => ['bg' => 'bg-white dark:bg-white/5 dark:backdrop-blur-xl', 'text' => 'text-slate-900 dark:text-white', 'btn' => 'bg-slate-100 dark:bg-white/10 text-slate-900 dark:text-white hover:bg-slate-200 dark:hover:bg-white/20 border border-transparent dark:border-white/10', 'check' => 'text-slate-400 dark:text-slate-500'],
                    'indigo' => ['bg' => 'bg-white dark:bg-white/5 dark:backdrop-blur-xl', 'text' => 'text-slate-900 dark:text-white', 'btn' => 'bg-slate-100 dark:bg-white/10 text-slate-900 dark:text-white hover:bg-slate-200 dark:hover:bg-white/20 border border-transparent dark:border-white/10', 'check' => 'text-slate-400 dark:text-slate-500'],
                    'violet' => ['bg' => 'bg-slate-900 dark:bg-indigo-500/10 dark:backdrop-blur-xl', 'text' => 'text-white dark:text-white', 'btn' => 'bg-white dark:bg-white text-slate-900 dark:text-slate-900 hover:bg-slate-100 dark:hover:bg-slate-200 border border-slate-700 dark:border-transparent', 'check' => 'text-slate-400 dark:text-indigo-300'],
                    'amber'  => ['bg' => 'bg-white dark:bg-white/5 dark:backdrop-blur-xl', 'text' => 'text-slate-900 dark:text-white', 'btn' => 'bg-slate-100 dark:bg-white/10 text-slate-900 dark:text-white hover:bg-slate-200 dark:hover:bg-white/20 border border-transparent dark:border-white/10', 'check' => 'text-slate-400 dark:text-slate-500'],
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 reveal">
                @foreach ($homePlans as $slug => $plan)
                    @php
                        $pricing = \App\Models\User::getPlanPricing($slug);
                        $hc = $homeColorMap[$plan['color']];
                        $isPopular = $slug === 'pro';
                    @endphp
                    
                    <div class="relative flex flex-col {{ $hc['bg'] }} border {{ $isPopular ? 'border-slate-900 dark:border-indigo-500 shadow-md z-10 scale-100 lg:scale-[1.02]' : 'border-slate-200 dark:border-white/10 shadow-sm' }} rounded-2xl overflow-hidden transition-all hover:border-slate-300 dark:hover:border-white/20">
                        
                        @if ($isPopular)
                            <div class="absolute top-0 inset-x-0 bg-slate-900 dark:bg-indigo-500 text-white dark:text-white text-[10px] font-bold text-center py-1 uppercase tracking-wider">
                                Paling Populer
                            </div>
                        @endif

                        <div class="p-8 flex-1 mt-4">
                            <h3 class="text-lg font-semibold {{ $hc['text'] }} mb-2">{{ $plan['label'] }}</h3>
                            
                            <div class="mb-6">
                                @if($pricing['promo'] !== null)
                                    <span class="text-xs font-medium text-slate-400 dark:text-slate-500 line-through block mb-1">Rp {{ number_format($pricing['normal'], 0, ',', '.') }}</span>
                                @else
                                    <div class="h-4 mb-1"></div> <!-- Spacer -->
                                @endif
                                <div class="flex items-baseline gap-1">
                                    <span class="text-3xl font-bold tracking-tight {{ $hc['text'] }}">Rp {{ number_format($pricing['active'], 0, ',', '.') }}</span>
                                    <span class="{{ $isPopular ? 'text-slate-300 dark:text-indigo-200' : 'text-slate-500 dark:text-slate-400' }} text-sm">/bln</span>
                                </div>
                            </div>

                            <ul class="space-y-3">
                                @foreach ($plan['features'] as $feat)
                                    <li class="flex items-start gap-3 text-sm {{ $isPopular ? 'text-slate-600 dark:text-indigo-100' : 'text-slate-600 dark:text-slate-400' }}">
                                        <div class="mt-1 flex-shrink-0">
                                            <i class="fa-solid fa-check {{ $hc['check'] }} text-[10px]"></i>
                                        </div>
                                        <span>{{ $feat }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        
                        <div class="p-8 pt-0">
                            <a href="{{ route('register') }}" class="flex items-center justify-center w-full {{ $hc['btn'] }} font-semibold py-2.5 rounded-full transition-colors text-sm">
                                Pilih Paket
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Features Bottom Line -->
            <div class="mt-16 pt-8 border-t border-slate-200/60 dark:border-white/10 grid grid-cols-2 md:grid-cols-4 gap-6 reveal">
                <div class="flex items-center justify-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-700 dark:text-slate-300 border border-transparent dark:border-white/10">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">SSL Gratis</span>
                </div>
                <div class="flex items-center justify-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-700 dark:text-slate-300 border border-transparent dark:border-white/10">
                        <i class="fa-solid fa-database"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Database MySQL</span>
                </div>
                <div class="flex items-center justify-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-700 dark:text-slate-300 border border-transparent dark:border-white/10">
                        <i class="fa-solid fa-rotate"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Auto-Deploy Git</span>
                </div>
                <div class="flex items-center justify-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-700 dark:text-slate-300 border border-transparent dark:border-white/10">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Support 1-on-1</span>
                </div>
            </div>
        </div>
    </section>

    <!-- PORTFOLIO SECTION -->
    <section id="portfolio" class="py-24 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-white/5 relative">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
            <div class="mb-16 flex flex-col md:flex-row justify-between items-end gap-6 reveal">
                <div class="max-w-2xl">
                    <span class="text-xs font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400 mb-3 block">Showcase</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-4">Arsip Karya Digital.</h2>
                    <p class="text-slate-500 dark:text-slate-400 text-lg">Eksplorasi beberapa sistem informasi, aplikasi, dan platform digital yang telah kami kembangkan.</p>
                </div>
                <a href="https://github.com/bimaryan" target="_blank" rel="noopener noreferrer"
                    class="hidden md:flex text-sm font-semibold text-slate-700 dark:text-slate-300 items-center gap-2 px-5 py-2.5 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 shadow-sm transition-all hover:shadow-md">
                    <i class="fa-brands fa-github text-lg"></i> Kunjungi Repositori
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 reveal">
                @forelse($portfolios as $portfolio)
                    <div class="card-premium flex flex-col overflow-hidden bg-white/80 dark:bg-slate-800/60 group border border-slate-200/60 dark:border-white/10">
                        @if ($portfolio->link_preview)
                            <a href="{{ $portfolio->link_preview }}" target="_blank" rel="noopener noreferrer"
                                class="block aspect-[16/10] overflow-hidden relative">
                        @else
                            <div class="block aspect-[16/10] overflow-hidden relative">
                        @endif

                        @if ($portfolio->image_path)
                            <img src="{{ Storage::url($portfolio->image_path) }}" alt="{{ $portfolio->title }}"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <!-- Premium Overlay -->
                            <div class="absolute inset-0 bg-slate-900/10 dark:bg-slate-900/40 group-hover:bg-transparent transition-colors duration-500"></div>
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300 dark:text-slate-500 bg-slate-100 dark:bg-slate-800/80">
                                <i class="fa-solid fa-image text-4xl"></i>
                            </div>
                        @endif

                        @if ($portfolio->link_preview)
                            </a>
                        @else
                            </div>
                        @endif

                        <div class="p-6 lg:p-8 flex flex-col flex-1">
                            <div class="flex gap-2 mb-4 flex-wrap">
                                @if ($portfolio->tags)
                                    @foreach ($portfolio->tags as $tag)
                                        <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300 text-[10px] uppercase font-bold tracking-wider rounded-md border border-slate-200/50 dark:border-white/5">{{ $tag }}</span>
                                    @endforeach
                                @endif
                            </div>

                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $portfolio->title }}</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm line-clamp-3 mb-6 flex-1 leading-relaxed">
                                {{ $portfolio->description }}
                            </p>

                            <div class="flex items-center gap-4 flex-wrap mt-auto pt-5 border-t border-slate-100 dark:border-white/5">
                                @if ($portfolio->link_github)
                                    <a href="{{ $portfolio->link_github }}" target="_blank" rel="noopener noreferrer"
                                        class="text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors flex items-center gap-1.5">
                                        <i class="fa-brands fa-github text-sm"></i> Code
                                    </a>
                                @endif
                                @if ($portfolio->link_journal)
                                    <a href="{{ $portfolio->link_journal }}" target="_blank" rel="noopener noreferrer"
                                        class="text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors flex items-center gap-1.5">
                                        <i class="fa-solid fa-book-open text-sm"></i> Jurnal
                                    </a>
                                @endif
                                @if ($portfolio->certificate_path)
                                    <a href="{{ Storage::url($portfolio->certificate_path) }}" target="_blank" rel="noopener noreferrer"
                                        class="text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors flex items-center gap-1.5">
                                        <i class="fa-solid fa-certificate text-sm"></i> Sertifikat
                                    </a>
                                @endif
                                @if ($portfolio->link_copyright)
                                    <a href="{{ $portfolio->link_copyright }}" target="_blank" rel="noopener noreferrer"
                                        class="text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors flex items-center gap-1.5">
                                        <i class="fa-solid fa-shield-halved text-sm"></i> Hak Cipta
                                    </a>
                                @endif
                                @if ($portfolio->link_preview)
                                    <a href="{{ $portfolio->link_preview }}" target="_blank" rel="noopener noreferrer"
                                        class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors flex items-center gap-1.5 ml-auto">
                                        Visit <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 border border-dashed border-slate-300 dark:border-slate-700 rounded-2xl text-center bg-white/50 dark:bg-slate-800/30">
                        <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                            <i class="fa-solid fa-folder-open text-2xl"></i>
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Data arsip belum tersedia.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-10 md:hidden flex justify-center">
                <a href="https://github.com/bimaryan" target="_blank" rel="noopener noreferrer"
                    class="text-sm font-semibold text-slate-700 dark:text-slate-300 items-center gap-2 px-6 py-3 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 shadow-sm transition-all flex">
                    <i class="fa-brands fa-github text-lg"></i> Kunjungi Repositori
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

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 reveal">
                @forelse($articles as $article)
                    <a href="{{ route('blog.show', $article->slug) }}"
                        class="group bg-white dark:bg-slate-800/60 border border-slate-200/60 dark:border-white/10 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 rounded-2xl overflow-hidden flex flex-col">
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
                <details class="bg-white dark:bg-slate-800/60 border border-slate-200/60 dark:border-white/10 shadow-sm transition-all duration-300 rounded-2xl p-6 lg:p-8 group" open>
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

                <details class="bg-white dark:bg-white/5 dark:backdrop-blur-xl border border-slate-200 dark:border-white/10 shadow-sm transition-all duration-300 rounded-2xl p-6 lg:p-8 group hover:border-slate-300 dark:hover:border-white/20">
                    <summary class="cursor-pointer font-bold text-slate-900 dark:text-slate-50 flex items-center justify-between gap-4">
                        Teknologi apa saja yang didukung hosting Ryaze?
                        <i class="fa-solid fa-chevron-down text-xs text-slate-400 dark:text-slate-500 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mt-4">
                        Hosting Ryaze mendukung Node.js, PHP (termasuk Laravel), Python, React, Vue.js, dan website
                        statis HTML. Setiap project di-deploy otomatis dari repositori Git Anda.
                    </p>
                </details>

                <details class="bg-white dark:bg-white/5 dark:backdrop-blur-xl border border-slate-200 dark:border-white/10 shadow-sm transition-all duration-300 rounded-2xl p-6 lg:p-8 group hover:border-slate-300 dark:hover:border-white/20">
                    <summary class="cursor-pointer font-bold text-slate-900 dark:text-slate-50 flex items-center justify-between gap-4">
                        Apakah SSL gratis tersedia?
                        <i class="fa-solid fa-chevron-down text-xs text-slate-400 dark:text-slate-500 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mt-4">
                        Ya, setiap project hosting di Ryaze otomatis mendapatkan sertifikat SSL gratis sehingga website
                        Anda aman dan diakses melalui HTTPS.
                    </p>
                </details>

                <details class="bg-white dark:bg-white/5 dark:backdrop-blur-xl border border-slate-200 dark:border-white/10 shadow-sm transition-all duration-300 rounded-2xl p-6 lg:p-8 group hover:border-slate-300 dark:hover:border-white/20">
                    <summary class="cursor-pointer font-bold text-slate-900 dark:text-slate-50 flex items-center justify-between gap-4">
                        Apakah tersedia database untuk project saya?
                        <i class="fa-solid fa-chevron-down text-xs text-slate-400 dark:text-slate-500 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mt-4">
                        Ya, setiap project mendapatkan database MySQL bawaan yang dapat dikelola melalui panel, mini
                        phpMyAdmin, dan API key untuk koneksi aplikasi.
                    </p>
                </details>

                <details class="bg-white dark:bg-white/5 dark:backdrop-blur-xl border border-slate-200 dark:border-white/10 shadow-sm transition-all duration-300 rounded-2xl p-6 lg:p-8 group hover:border-slate-300 dark:hover:border-white/20">
                    <summary class="cursor-pointer font-bold text-slate-900 dark:text-slate-50 flex items-center justify-between gap-4">
                        Apakah bisa request jasa pembuatan website atau aplikasi?
                        <i class="fa-solid fa-chevron-down text-xs text-slate-400 dark:text-slate-500 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mt-4">
                        Bisa. Ryaze menerima pengerjaan sistem informasi, aplikasi SaaS, hingga prototipe fungsional
                        Tugas Akhir atau Skripsi dengan arsitektur modern yang bersih dan terdokumentasi.
                    </p>
                </details>

                <details class="bg-white dark:bg-white/5 dark:backdrop-blur-xl border border-slate-200 dark:border-white/10 shadow-sm transition-all duration-300 rounded-2xl p-6 lg:p-8 group hover:border-slate-300 dark:hover:border-white/20">
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
    <section class="py-24 bg-slate-50 dark:bg-[#030712] text-center px-6 relative overflow-hidden border-t border-slate-200 dark:border-white/5">
        <!-- Subtle radial spotlight -->
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-indigo-900/10 via-[#030712] to-[#030712] pointer-events-none hidden dark:block"></div>
        <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-white/[0.02] to-transparent pointer-events-none"></div>
        
        <div class="max-w-3xl mx-auto relative z-10 reveal">
            <h2 class="text-3xl md:text-5xl font-bold tracking-tight mb-6 text-slate-900 dark:text-transparent dark:bg-clip-text dark:bg-gradient-to-b dark:from-white dark:to-slate-400">Siap Mengeksekusi Ide?</h2>
            <p class="text-slate-500 dark:text-slate-400 text-lg mb-10 max-w-xl mx-auto">Daftar sekarang untuk mengakses lingkungan
                deployment yang kuat atau hubungi kami untuk pengerjaan perangkat lunak Anda.</p>
            <a href="{{ route('register') }}"
                class="inline-flex px-8 py-3 bg-slate-900 text-white dark:bg-white dark:text-slate-900 text-sm font-semibold rounded-full hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors shadow-sm dark:shadow-[0_0_20px_rgba(255,255,255,0.15)]">
                Mulai Secara Gratis
            </a>
        </div>
    </section>

    <!-- Chatbot Widget -->
    <div id="ryaze-chatbot-widget" class="fixed bottom-6 right-6 z-50 font-sans">
        <!-- Chat Window -->
        <div id="ryaze-chat-window" class="hidden flex-col bg-white dark:bg-[#0B0F19] border border-slate-200 dark:border-white/10 shadow-2xl rounded-2xl w-80 h-96 mb-4 overflow-hidden transition-all duration-300 transform origin-bottom-right">
            <div class="bg-slate-900 dark:bg-indigo-600 px-4 py-3 text-white flex justify-between items-center shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                    <span class="font-bold text-sm">Ryaze Assistant</span>
                </div>
                <button id="ryaze-chat-close" class="text-indigo-100 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div id="ryaze-chat-messages" class="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50 dark:bg-[#030712] text-sm flex flex-col">
                <!-- Welcome Message -->
                <div class="flex items-start gap-2">
                    <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center shrink-0 mt-0.5">
                        <i class="fa-solid fa-robot text-[10px] text-indigo-600 dark:text-indigo-400"></i>
                    </div>
                    <div class="bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 px-3 py-2 rounded-2xl rounded-tl-sm text-slate-700 dark:text-slate-200 shadow-sm max-w-[85%]">
                        Halo! Saya asisten AI Ryaze. Ada yang bisa saya bantu hari ini?
                    </div>
                </div>
            </div>
            <div class="p-3 bg-white dark:bg-[#0B0F19] border-t border-slate-100 dark:border-white/10">
                <form id="ryaze-chat-form" class="flex items-center gap-2">
                    <input type="text" id="ryaze-chat-input" placeholder="Ketik pesan..." required class="flex-1 bg-slate-50 dark:bg-[#030712] border border-slate-200 dark:border-white/10 text-sm rounded-full px-4 py-2 focus:outline-none focus:border-slate-400 dark:focus:border-white/20 transition-all text-slate-900 dark:text-white">
                    <button type="submit" class="w-9 h-9 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 flex items-center justify-center hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors shrink-0 shadow-sm disabled:opacity-50">
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
                        avatar = `<div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-white/10 flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="fa-solid fa-user text-[10px] text-slate-500 dark:text-slate-400"></i>
                                  </div>`;
                    } else {
                        avatar = `<div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="fa-solid fa-robot text-[10px] text-indigo-600 dark:text-indigo-400"></i>
                                  </div>`;
                    }

                    const msgBubble = document.createElement('div');
                    msgBubble.className = isUser
                        ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-3 py-2 rounded-2xl rounded-tr-sm shadow-sm max-w-[85%] break-words'
                        : 'bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 px-3 py-2 rounded-2xl rounded-tl-sm text-slate-700 dark:text-slate-200 shadow-sm max-w-[85%] break-words';

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
                        <div class="bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 px-4 py-3 rounded-2xl rounded-tl-sm shadow-sm flex gap-1 items-center">
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
