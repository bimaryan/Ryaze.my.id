<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ryaze Portal - Hosting & Joki Terbaik</title>

    <!-- SEO Meta Tags -->
    <meta name="description"
        content="Ryaze Portal menawarkan layanan web hosting instan dan joki tugas IT terpercaya. Proses serba otomatis, aman, dan harga terjangkau.">
    <meta name="keywords"
        content="hosting murah, joki tugas IT, web hosting otomatis, Ryaze, panel hosting, joki koding">
    <meta name="author" content="Ryaze">

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="Ryaze Portal - Hosting & Joki IT Terpercaya">
    <meta property="og:description" content="Layanan hosting instan & joki coding profesional dengan sistem otomatis.">
    <meta property="og:image" content="{{ asset('assets/ryaze-og-image.jpg') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url('/') }}">
    <meta property="twitter:title" content="Ryaze Portal - Hosting & Joki IT Terpercaya">
    <meta property="twitter:description"
        content="Layanan hosting instan & joki coding profesional dengan sistem otomatis.">
    <meta property="twitter:image" content="{{ asset('assets/ryaze-og-image.jpg') }}">

    <!-- Vite Config -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Outfit:wght@400;500;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fafafa;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .font-outfit {
            font-family: 'Outfit', sans-serif;
        }

        /* Glassmorphism Navbar */
        .glass-nav {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
        }

        /* Mesh Gradient Background */
        .mesh-bg {
            background-color: #f8fafc;
            background-image:
                radial-gradient(at 0% 0%, hsla(253, 16%, 7%, 0) 0, transparent 50%),
                radial-gradient(at 50% 0%, hsla(225, 39%, 30%, 0) 0, transparent 50%),
                radial-gradient(at 100% 0%, hsla(339, 49%, 30%, 0) 0, transparent 50%);
            position: relative;
            overflow: hidden;
        }

        .blob {
            position: absolute;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.6;
            animation: float 10s infinite ease-in-out alternate;
        }

        .blob-1 {
            top: -10%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: rgba(99, 102, 241, 0.4);
        }

        .blob-2 {
            bottom: -10%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: rgba(236, 72, 153, 0.3);
            animation-delay: -5s;
        }

        .blob-3 {
            top: 40%;
            left: 50%;
            width: 400px;
            height: 400px;
            background: rgba(56, 189, 248, 0.4);
            animation-delay: -2s;
        }

        @keyframes float {
            0% {
                transform: translate(0, 0) scale(1);
            }

            100% {
                transform: translate(30px, 50px) scale(1.1);
            }
        }

        /* Glowing Button */
        .btn-glow {
            position: relative;
            z-index: 1;
        }

        .btn-glow::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
            background: inherit;
            filter: blur(12px);
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .btn-glow:hover::before {
            opacity: 1;
            filter: blur(16px);
        }

        /* Gradient Text */
        .text-gradient {
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-image: linear-gradient(135deg, #4f46e5 0%, #db2777 100%);
        }

        /* Premium Card Hover */
        .card-premium {
            background: white;
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-premium:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -10px rgba(79, 70, 229, 0.15);
            border-color: rgba(99, 102, 241, 0.3);
        }

        /* Image Zoom */
        .img-zoom-container {
            overflow: hidden;
        }

        .img-zoom {
            transition: transform 0.7s ease;
        }

        .group:hover .img-zoom {
            transform: scale(1.08);
        }
    </style>
</head>

<body class="text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">

    <!-- NAVBAR -->
    <nav class="fixed top-0 z-50 w-full glass-nav transition-all duration-300 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div
                        class="bg-gradient-to-br from-indigo-600 to-purple-600 text-white rounded-xl p-2.5 shadow-lg shadow-indigo-200 transition-transform duration-300 group-hover:rotate-12 group-hover:scale-110">
                        <i class="fa-solid fa-code text-xl"></i>
                    </div>
                    <span class="text-2xl font-outfit font-black tracking-tight text-slate-900">Ryaze<span
                            class="text-indigo-600">.</span></span>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-8 font-medium text-slate-600">
                    <a href="#about"
                        class="hover:text-indigo-600 transition-colors relative after:content-[''] after:absolute after:-bottom-1 after:left-0 after:w-0 after:h-0.5 after:bg-indigo-600 after:transition-all hover:after:w-full">Tentang</a>
                    <a href="#services"
                        class="hover:text-indigo-600 transition-colors relative after:content-[''] after:absolute after:-bottom-1 after:left-0 after:w-0 after:h-0.5 after:bg-indigo-600 after:transition-all hover:after:w-full">Layanan</a>
                    <a href="#portfolio"
                        class="hover:text-indigo-600 transition-colors relative after:content-[''] after:absolute after:-bottom-1 after:left-0 after:w-0 after:h-0.5 after:bg-indigo-600 after:transition-all hover:after:w-full">Portofolio</a>
                </div>

                <!-- Auth Buttons -->
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
                            class="btn-glow text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-2.5 rounded-full shadow-lg transition-transform hover:-translate-y-0.5">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-sm font-bold text-slate-700 hover:text-indigo-600 transition-colors hidden sm:block">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}"
                            class="btn-glow text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-2.5 rounded-full shadow-lg transition-transform hover:-translate-y-0.5">
                            Daftar Gratis
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="mesh-bg relative pt-36 pb-24 lg:pt-48 lg:pb-40 min-h-[90vh] flex items-center">
        <!-- Floating Blobs -->
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center z-10" data-aos="fade-up"
            data-aos-duration="1000">
            <div
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/60 backdrop-blur-sm border border-white/50 text-indigo-700 text-sm font-bold mb-8 shadow-sm">
                <span class="relative flex h-3 w-3">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                </span>
                Fullstack Developer & Deployment Specialist
            </div>

            <h1 class="text-5xl md:text-7xl lg:text-[5rem] font-black leading-tight tracking-tight mb-6">
                Wujudkan Website Impian <br class="hidden md:block" />
                <span class="text-gradient">Tanpa Hambatan.</span>
            </h1>

            <p class="mt-6 text-lg md:text-xl text-slate-600 max-w-2xl mx-auto mb-10 leading-relaxed font-medium">
                Dari pembuatan *code* hingga mengudara di internet. Kami melayani Jasa Joki Tugas Akhir, Web Custom,
                hingga platform Hosting canggih untuk developer.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-5">
                <a href="#services"
                    class="btn-glow px-8 py-4 text-lg font-bold rounded-full text-white bg-slate-900 hover:bg-black shadow-xl transition-transform hover:-translate-y-1 flex items-center justify-center gap-2">
                    Mulai Sekarang <i class="fa-solid fa-arrow-right"></i>
                </a>
                <a href="#portfolio"
                    class="px-8 py-4 text-lg font-bold rounded-full text-slate-700 bg-white/80 backdrop-blur-sm border border-slate-200 hover:bg-white hover:border-slate-300 hover:shadow-lg transition-all flex items-center justify-center gap-2">
                    Lihat Karya <i class="fa-solid fa-play text-indigo-500"></i>
                </a>
            </div>

            <!-- Tech Stack Marquee (Static Preview) -->
            <div class="mt-20 pt-10 border-t border-slate-200/60 max-w-4xl mx-auto">
                <p class="text-sm font-semibold text-slate-400 uppercase tracking-widest mb-6">Dipercaya dengan
                    Teknologi Terbaik</p>
                <div
                    class="flex flex-wrap justify-center gap-8 opacity-60 grayscale hover:grayscale-0 transition-all duration-500">
                    <i class="fa-brands fa-laravel text-4xl hover:text-[#FF2D20] transition-colors"></i>
                    <i class="fa-brands fa-react text-4xl hover:text-[#61DAFB] transition-colors"></i>
                    <i class="fa-brands fa-node-js text-4xl hover:text-[#339933] transition-colors"></i>
                    <i class="fa-brands fa-python text-4xl hover:text-[#3776AB] transition-colors"></i>
                    <i class="fa-brands fa-vuejs text-4xl hover:text-[#4FC08D] transition-colors"></i>
                    <i class="fa-brands fa-aws text-4xl hover:text-[#232F3E] transition-colors"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section id="about" class="py-24 bg-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-16 lg:gap-24">

                <!-- Avatar / Photo -->
                <div class="w-full lg:w-1/2 flex justify-center" data-aos="fade-right">
                    <div class="relative group">
                        <!-- Decorative Elements -->
                        <div
                            class="absolute -inset-4 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-[2rem] opacity-20 group-hover:opacity-40 blur-xl transition-opacity duration-500">
                        </div>
                        <div
                            class="absolute inset-0 bg-gradient-to-tr from-indigo-600 to-fuchsia-500 rounded-[2rem] rotate-6 group-hover:rotate-12 transition-transform duration-500">
                        </div>

                        <img src="https://ui-avatars.com/api/?name=Bima+Ryan&size=600&background=1e293b&color=fff&bold=true"
                            alt="Bima Ryan Alfarizi"
                            class="relative rounded-[2rem] shadow-2xl w-80 lg:w-[400px] border-8 border-white object-cover aspect-square transition-transform duration-500 group-hover:-translate-y-2 group-hover:scale-[1.02]">

                        <!-- Floating Badge -->
                        <div
                            class="absolute -bottom-6 -right-6 bg-white p-4 rounded-2xl shadow-xl border border-slate-100 animate-bounce">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                                    <i class="fa-solid fa-check-double"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 font-bold uppercase">Experience</p>
                                    <p class="text-lg font-black text-slate-900">3+ Years</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Text Content -->
                <div class="w-full lg:w-1/2" data-aos="fade-left">
                    <h2 class="text-sm font-black text-indigo-600 uppercase tracking-widest mb-3">Tentang Kreator</h2>
                    <h3 class="text-4xl md:text-5xl font-black text-slate-900 mb-6 leading-tight">Halo, kenali saya
                        lebih dekat!</h3>

                    <p class="text-lg text-slate-600 mb-6 leading-relaxed">
                        Saya <strong>Bima Ryan Alfarizi</strong>, mahasiswa D4 Rekayasa Perangkat Lunak di Politeknik
                        Negeri Indramayu (POLINDRA). Saya terobsesi dengan menciptakan antarmuka digital yang memukau
                        dan arsitektur backend yang kokoh.
                    </p>

                    <p class="text-lg text-slate-600 mb-8 leading-relaxed">
                        Keahlian saya mencakup Web Development dan Game Development. Misi saya adalah membantu klien
                        mengubah ide rumit menjadi produk digital yang mulus dan siap pakai.
                    </p>

                    <!-- Modern Skill Badges -->
                    <div class="flex flex-wrap gap-3">
                        @php
                            $skills = [
                                ['name' => 'Laravel', 'icon' => 'fa-brands fa-laravel text-red-500'],
                                ['name' => 'React', 'icon' => 'fa-brands fa-react text-sky-400'],
                                ['name' => 'Python', 'icon' => 'fa-brands fa-python text-yellow-500'],
                                ['name' => 'Node.js', 'icon' => 'fa-brands fa-node-js text-green-600'],
                                ['name' => 'Tailwind', 'icon' => 'fa-brands fa-css3-alt text-teal-400'],
                                ['name' => 'Linux/VPS', 'icon' => 'fa-brands fa-linux text-slate-800'],
                                ['name' => 'Unity 3D', 'icon' => 'fa-brands fa-unity text-slate-700'],
                            ];
                        @endphp

                        @foreach ($skills as $skill)
                            <div
                                class="px-5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-700 shadow-sm hover:shadow-md hover:-translate-y-1 hover:border-indigo-300 transition-all cursor-default flex items-center gap-2">
                                <i class="{{ $skill['icon'] }} text-lg"></i> {{ $skill['name'] }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SERVICES SECTION -->
    <section id="services" class="py-24 bg-[#f8fafc] relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20" data-aos="fade-up">
                <h2 class="text-sm font-black text-indigo-600 uppercase tracking-widest mb-3">Layanan Kami</h2>
                <h3 class="text-4xl md:text-5xl font-black text-slate-900">Solusi Digital Lengkap</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">

                <!-- Service 1: Joki Code -->
                <div class="card-premium rounded-3xl p-10 relative overflow-hidden group" data-aos="fade-up"
                    data-aos-delay="100">
                    <div
                        class="absolute top-0 right-0 w-64 h-64 bg-indigo-50 rounded-full blur-3xl -mr-20 -mt-20 transition-all group-hover:bg-indigo-100">
                    </div>

                    <div class="relative z-10">
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mb-8 shadow-lg shadow-indigo-200 text-white group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">
                            <i class="fa-solid fa-code text-2xl"></i>
                        </div>

                        <h3 class="text-2xl font-black text-slate-900 mb-4 font-outfit">Jasa Web Development</h3>
                        <p class="text-slate-600 mb-8 text-lg leading-relaxed">
                            Bantuan pembuatan Website Custom, Sistem Informasi, ERP, hingga penyelesaian Tugas Akhir /
                            Skripsi IT Anda dengan kode yang bersih dan terstruktur.
                        </p>

                        <ul class="space-y-4 mb-10 text-slate-700 font-medium">
                            <li class="flex items-start gap-3">
                                <i class="fa-solid fa-circle-check text-indigo-500 mt-1 text-lg"></i>
                                <span>Pengerjaan cepat & profesional</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fa-solid fa-circle-check text-indigo-500 mt-1 text-lg"></i>
                                <span>Tech stack modern (Laravel, React, Vue)</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fa-solid fa-circle-check text-indigo-500 mt-1 text-lg"></i>
                                <span>Free revisi & konsultasi desain arsitektur</span>
                            </li>
                        </ul>

                        <a href="{{ route('register') }}"
                            class="inline-flex items-center font-bold text-indigo-600 hover:text-indigo-800 text-lg group/link">
                            Mulai Konsultasi <i
                                class="fa-solid fa-arrow-right ml-2 transition-transform group-hover/link:translate-x-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Service 2: Hosting -->
                <div class="card-premium rounded-3xl p-10 relative overflow-hidden group" data-aos="fade-up"
                    data-aos-delay="200">
                    <div
                        class="absolute top-0 right-0 w-64 h-64 bg-emerald-50 rounded-full blur-3xl -mr-20 -mt-20 transition-all group-hover:bg-emerald-100">
                    </div>

                    <div class="relative z-10">
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl flex items-center justify-center mb-8 shadow-lg shadow-emerald-200 text-white group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">
                            <i class="fa-solid fa-server text-2xl"></i>
                        </div>

                        <h3 class="text-2xl font-black text-slate-900 mb-4 font-outfit">Developer Cloud Hosting</h3>
                        <p class="text-slate-600 mb-8 text-lg leading-relaxed">
                            Deploy aplikasi Node.js, Next.js, React, hingga Python Flask di infrastruktur kami semudah
                            klik tombol. Berbasis kontrol panel modern.
                        </p>

                        <ul class="space-y-4 mb-10 text-slate-700 font-medium">
                            <li class="flex items-start gap-3">
                                <i class="fa-solid fa-circle-check text-emerald-500 mt-1 text-lg"></i>
                                <span>Auto-Deploy dari GitHub / GitLab</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fa-solid fa-circle-check text-emerald-500 mt-1 text-lg"></i>
                                <span>Akses Web-Terminal & File Manager</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fa-solid fa-circle-check text-emerald-500 mt-1 text-lg"></i>
                                <span>Support PM2, Composer, NPM, & SSL Gratis</span>
                            </li>
                        </ul>

                        <a href="{{ route('register') }}"
                            class="inline-flex items-center font-bold text-emerald-600 hover:text-emerald-800 text-lg group/link">
                            Deploy Aplikasi <i
                                class="fa-solid fa-arrow-right ml-2 transition-transform group-hover/link:translate-x-2"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- PORTFOLIO SECTION -->
    <section id="portfolio" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-sm font-black text-indigo-600 uppercase tracking-widest mb-3">Portofolio</h2>
                <h3 class="text-4xl md:text-5xl font-black text-slate-900 mb-6">Mahakarya Terbaru</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Portofolio Item 1 -->
                <div class="group rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl hover:shadow-indigo-500/20 transition-all duration-500"
                    data-aos="fade-up" data-aos-delay="100">
                    <a href="https://silk.ryaze.my.id/login" target="_blank" class="block">
                        <div class="img-zoom-container h-60 bg-slate-100 relative">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                                <span
                                    class="text-white font-bold text-lg translate-y-4 group-hover:translate-y-0 transition-transform duration-300">Live Preview →</span>
                            </div>
                            <div class="w-full h-full bg-slate-800 flex items-center justify-center img-zoom relative">
                                <i class="fa-solid fa-microscope text-7xl text-white/20 absolute"></i>
                                <div class="w-full h-full bg-gradient-to-br from-indigo-500/30 to-purple-600/30"></div>
                            </div>
                        </div>
                    </a>
                    <div class="p-8 border border-t-0 border-slate-100 bg-white rounded-b-3xl">
                        <div class="flex gap-2 mb-4">
                            <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-full">Laravel</span>
                            <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-full">Tailwind</span>
                        </div>
                        <a href="https://silk.ryaze.my.id/login" target="_blank">
                            <h4 class="text-xl font-black text-slate-900 mb-2 font-outfit group-hover:text-indigo-600 transition-colors">
                                Sistem Peminjaman Lab Kesehatan
                            </h4>
                        </a>
                        <p class="text-slate-500 line-clamp-2 mb-4">Aplikasi manajemen jadwal & inventaris untuk Universitas ternama.</p>
                        
                        <a href="https://github.com/bimaryan/silk" target="_blank" class="inline-flex items-center text-sm font-semibold text-slate-600 hover:text-indigo-600 transition-colors">
                            <i class="fa-brands fa-github text-lg mr-2"></i> Repository
                        </a>
                    </div>
                </div>

            </div>

            <div class="text-center mt-16" data-aos="fade-up">
                <a href="https://github.com/bimaryan" target="_blank"
                    class="inline-flex items-center justify-center px-8 py-4 font-bold text-slate-700 bg-white border-2 border-slate-200 rounded-full hover:border-indigo-500 hover:text-indigo-600 transition-all">
                    Jelajahi GitHub Saya <i class="fa-brands fa-github ml-3 text-xl"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- CALL TO ACTION -->
    <section class="py-24 relative overflow-hidden">
        <div class="absolute inset-0 bg-slate-900"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-slate-900 to-purple-900 opacity-80"></div>

        <!-- Animated Background Pattern -->
        <div class="absolute inset-0"
            style="background-image: radial-gradient(rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 30px 30px;">
        </div>

        <div class="max-w-4xl mx-auto px-4 text-center relative z-10" data-aos="zoom-in">
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-white mb-6 font-outfit tracking-tight">Siap
                Memulai Proyek Anda?</h2>
            <p class="text-indigo-200 text-xl mb-12 font-medium max-w-2xl mx-auto">
                Bergabunglah dengan platform kami hari ini. Pesan jasa pembuatan web atau kelola hosting aplikasi Anda
                dalam satu dashboard canggih.
            </p>
            <a href="{{ route('register') }}"
                class="btn-glow inline-block px-10 py-5 text-xl font-black rounded-full text-indigo-900 bg-white shadow-2xl hover:scale-105 transition-transform duration-300">
                Buat Akun Gratis Sekarang
            </a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-slate-950 pt-20 pb-10 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center gap-3 mb-6 md:mb-0">
                    <div class="bg-gradient-to-br from-indigo-600 to-purple-600 text-white rounded-lg p-2">
                        <i class="fa-solid fa-code"></i>
                    </div>
                    <span class="text-2xl font-black font-outfit text-white">Ryaze<span
                            class="text-indigo-500">.</span></span>
                </div>
                <div class="flex gap-6">
                    <a href="https://github.com/bimaryan" target="_blank"
                        class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-indigo-600 hover:text-white transition-all">
                        <i class="fa-brands fa-github text-lg"></i>
                    </a>
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-pink-600 hover:text-white transition-all">
                        <i class="fa-brands fa-instagram text-lg"></i>
                    </a>
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition-all">
                        <i class="fa-brands fa-linkedin text-lg"></i>
                    </a>
                </div>
            </div>
            <div
                class="mt-12 pt-8 border-t border-slate-800/50 flex flex-col md:flex-row justify-between items-center gap-4 text-slate-500 font-medium">
                <p>&copy; {{ date('Y') }} Ryaze Portal. All rights reserved.</p>
                <p>Designed with <i class="fa-solid fa-heart text-rose-500 mx-1"></i> by Bima Ryan</p>
            </div>
        </div>
    </footer>

    <!-- AOS Script -->
    <script nonce="{{ app('csp_nonce') ?? '' }}"></script>
    <script nonce="{{ app('csp_nonce') ?? '' }}">
        AOS.init({
            once: true,
            offset: 100,
            duration: 800,
            easing: 'ease-out-cubic',
        });
    </script>
    @include('components.hot-toast')
</body>

</html>
