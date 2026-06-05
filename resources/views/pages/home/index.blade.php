<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ryaze - Portofolio, Joki Code & Jasa Deploy Web</title>

    <!-- Vite Config -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="font-sans antialiased text-slate-900 bg-slate-50">

    <!-- NAVBAR -->
    <nav
        class="fixed top-0 z-50 w-full bg-white/90 backdrop-blur-md border-b border-slate-200 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                    <div class="bg-indigo-600 text-white rounded-lg p-2 transition-transform group-hover:rotate-12">
                        <i class="fa-solid fa-code text-lg"></i>
                    </div>
                    <span class="text-2xl font-extrabold tracking-tight text-slate-800">Ryaze<span
                            class="text-indigo-600">.</span></span>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-8 font-medium text-slate-600">
                    <a href="#about" class="hover:text-indigo-600 transition-colors">Tentang Saya</a>
                    <a href="#services" class="hover:text-indigo-600 transition-colors">Layanan</a>
                    <a href="#portfolio" class="hover:text-indigo-600 transition-colors">Portofolio</a>
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
                            class="text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 px-5 py-2.5 rounded-lg shadow-md shadow-indigo-200 transition-all hover:-translate-y-0.5">
                            Masuk Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-sm font-semibold text-slate-700 hover:text-indigo-600 transition-colors hidden sm:block">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}"
                            class="text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 px-5 py-2.5 rounded-lg shadow-md shadow-indigo-200 transition-all hover:-translate-y-0.5">
                            Daftar Sekarang
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <!-- Background Decoration -->
        <div
            class="absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-indigo-100 via-slate-50 to-slate-50">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center">
            <span
                class="inline-block py-1 px-3 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-600 text-sm font-semibold mb-6">
                🚀 Fullstack Developer & Deployment Specialist
            </span>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-slate-900 tracking-tight mb-8">
                Bikin & Online-kan Web-mu <br class="hidden md:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-500">Tanpa
                    Pusing.</span>
            </h1>
            <p class="mt-4 text-lg md:text-xl text-slate-600 max-w-3xl mx-auto mb-10 leading-relaxed">
                Halo, saya Bima Ryan! Butuh bantuan bikin website dari nol (Jasa Joki) atau sekadar bingung cara
                <i>deploy/hosting</i> project (React, Laravel, Flask, dll) ke internet? Biar saya yang urus semuanya.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#services"
                    class="px-8 py-4 text-lg font-bold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all hover:-translate-y-1">
                    Lihat Layanan
                </a>
                <a href="#portfolio"
                    class="px-8 py-4 text-lg font-bold rounded-xl text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition-all">
                    Lihat Portofolio
                </a>
            </div>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section id="about" class="py-24 bg-slate-50 border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                <!-- Foto/Ilustrasi -->
                <div class="w-full lg:w-1/2 flex justify-center lg:justify-end">
                    <div class="relative">
                        <!-- Aksen kotak di belakang foto -->
                        <div
                            class="absolute inset-0 bg-indigo-600 rounded-3xl rotate-3 translate-x-4 translate-y-4 opacity-20">
                        </div>
                        <img src="https://ui-avatars.com/api/?name=Bima+Ryan&size=500&background=4f46e5&color=fff&bold=true"
                            alt="Bima Ryan Alfarizi"
                            class="relative rounded-3xl shadow-xl w-72 md:w-96 border-4 border-white object-cover aspect-square">
                    </div>
                </div>

                <!-- Teks Biografi -->
                <div class="w-full lg:w-1/2 text-center lg:text-left">
                    <span
                        class="inline-block py-1 px-3 rounded-full bg-slate-200 text-slate-700 text-sm font-semibold mb-4">
                        Tentang Saya
                    </span>
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-6">Mengenal Lebih Dekat</h2>
                    <p class="text-lg text-slate-600 mb-4 leading-relaxed">
                        Perkenalkan, saya <strong>Bima Ryan Alfarizi</strong>. Saat ini saya adalah mahasiswa semester 6
                        program studi D4 Rekayasa Perangkat Lunak di Politeknik Negeri Indramayu (POLINDRA).
                    </p>
                    <p class="text-lg text-slate-600 mb-8 leading-relaxed">
                        Saya memiliki minat dan spesialisasi yang kuat dalam <em>Web Development</em> dan <em>Game
                            Development</em>. Dengan pengalaman mengerjakan berbagai proyek <em>Fullstack</em>, saya
                        siap membantu mewujudkan website impian Anda sekaligus memastikannya online dengan infrastruktur
                        server yang andal.
                    </p>

                    <!-- Tech Stack / Skills -->
                    <div class="flex flex-wrap justify-center lg:justify-start gap-3">
                        <span
                            class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 shadow-sm">
                            <i class="fa-brands fa-laravel text-red-500 mr-2 text-lg"></i> Laravel
                        </span>
                        <span
                            class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 shadow-sm">
                            <i class="fa-brands fa-react text-blue-500 mr-2 text-lg"></i> Reactjs
                        </span>
                        <span
                            class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 shadow-sm">
                            <i class="fa-brands fa-php text-purple-500 mr-2 text-lg"></i> PHP
                        </span>
                        <span
                            class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 shadow-sm">
                            <i class="fa-brands fa-js text-yellow-500 mr-2 text-lg"></i> JS
                        </span>
                        <span
                            class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 shadow-sm">
                            <i class="fa-brands fa-python text-green-500 mr-2 text-lg"></i> Python
                        </span>
                        <span
                            class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 shadow-sm">
                            <i class="fa-brands fa-linux text-black-500 mr-2 text-lg"></i> Linux
                        </span>
                        <span
                            class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 shadow-sm">
                            <i class="fa-brands fa-css3-alt text-sky-500 mr-2 text-lg"></i> Tailwind CSS
                        </span>
                        <span
                            class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 shadow-sm">
                            <i class="fa-solid fa-database text-indigo-500 mr-2 text-lg"></i> MySQL / Postgre
                        </span>
                        <span
                            class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 shadow-sm">
                            <i class="fa-brands fa-unity text-slate-800 mr-2 text-lg"></i> Unity & C#
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SERVICES SECTION -->
    <section id="services" class="py-24 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Layanan Utama Kami</h2>
                <p class="text-lg text-slate-500">Satu platform untuk semua kebutuhan pengembangan digital Anda.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">

                <!-- Service 1: Jasa Joki Code -->
                <div
                    class="group bg-slate-50 rounded-3xl p-8 md:p-10 border border-slate-200 hover:border-indigo-300 hover:shadow-xl hover:shadow-indigo-100 transition-all duration-300">
                    <div
                        class="w-16 h-16 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-laptop-code text-3xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Jasa Pembuatan Web (Joki Code)</h3>
                    <p class="text-slate-600 mb-8 leading-relaxed">
                        Pusing mikirin kodingan tugas akhir atau butuh website bisnis yang kompleks? Serahkan pada saya.
                        Dibuat menggunakan tech stack modern (Laravel, React, dsb) yang aman dan responsif.
                    </p>
                    <ul class="space-y-3 mb-8 text-slate-600 font-medium">
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-indigo-500"></i> Tugas
                            Akhir & Skripsi</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-indigo-500"></i> Sistem
                            Informasi (ERP, SIAKAD, POS)</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-indigo-500"></i> Custom Web
                            Application</li>
                    </ul>
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center justify-center w-full py-3 px-4 font-semibold text-indigo-600 bg-indigo-50 rounded-xl group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                        Pesan Jasa Joki <i class="fa-solid fa-arrow-right ml-2"></i>
                    </a>
                </div>

                <!-- Service 2: Developer App Hosting -->
                <div
                    class="group bg-slate-50 rounded-3xl p-8 md:p-10 border border-slate-200 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-100 transition-all duration-300">
                    <div
                        class="w-16 h-16 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-terminal text-3xl text-emerald-500"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Developer App Hosting</h3>
                    <p class="text-slate-600 mb-8 leading-relaxed">
                        Bukan sekadar shared hosting biasa. Deploy project Node.js, React, Vue, Next.js, hingga Python
                        (FastAPI/Flask) langsung dari GitHub atau terminal web interaktif.
                    </p>
                    <ul class="space-y-3 mb-8 text-slate-600 font-medium">
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-emerald-500"></i> Git
                            Clone & Auto Deploy CI/CD</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-emerald-500"></i> Akses
                            Terminal Web / SSH</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-emerald-500"></i> Support
                            PM2, NPM, & Python Env</li>
                    </ul>
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center justify-center w-full py-3 px-4 font-semibold text-emerald-600 bg-emerald-50 rounded-xl group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        Mulai Deploy Project <i class="fa-solid fa-arrow-right ml-2"></i>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- PORTFOLIO SECTION (Sudah terisi dengan data CV sebelumnya) -->
    <section id="portfolio" class="py-24 bg-slate-50 border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-16">
                <span
                    class="inline-block py-1 px-3 rounded-full bg-indigo-50 text-indigo-600 text-sm font-semibold mb-4 border border-indigo-100">
                    Hasil Karya
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Portofolio Proyek</h2>
                <p class="text-lg text-slate-500">Beberapa sistem dan karya yang telah berhasil saya kembangkan.</p>
            </div>

            <!-- Grid Portofolio -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Proyek 1: Sistem Lab Kesehatan -->
                <div
                    class="bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-xl hover:shadow-indigo-100 transition-all duration-300 group">
                    <div class="h-48 bg-slate-200 overflow-hidden relative flex items-center justify-center">
                        <i
                            class="fa-solid fa-microscope text-6xl text-indigo-200 group-hover:scale-110 transition-transform duration-500"></i>
                        <div class="absolute inset-0 bg-indigo-900/10 group-hover:bg-transparent transition-colors">
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="text-xs font-bold px-2 py-1 bg-red-50 text-red-600 rounded">Laravel</span>
                            <span class="text-xs font-bold px-2 py-1 bg-sky-50 text-sky-600 rounded">Tailwind</span>
                            <span class="text-xs font-bold px-2 py-1 bg-slate-100 text-slate-600 rounded">VPS</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Sistem Peminjaman Lab Kesehatan</h3>
                        <p class="text-slate-600 text-sm mb-5 line-clamp-3">
                            Proyek Kampus Merdeka: Mengembangkan UI responsif, mengelola data peminjaman & jadwal
                            pengguna, hingga deployment ke VPS/Cloud.
                        </p>
                        <a href="#"
                            class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                            Lihat Detail <i class="fa-solid fa-arrow-right-long ml-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Proyek 2: Penyewaan Kost dan Mobil -->
                <div
                    class="bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-xl hover:shadow-indigo-100 transition-all duration-300 group">
                    <div class="h-48 bg-slate-200 overflow-hidden relative flex items-center justify-center">
                        <i
                            class="fa-solid fa-house-car text-6xl text-blue-200 group-hover:scale-110 transition-transform duration-500"></i>
                        <div class="absolute inset-0 bg-blue-900/10 group-hover:bg-transparent transition-colors">
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span
                                class="text-xs font-bold px-2 py-1 bg-indigo-50 text-indigo-600 rounded">Fullstack</span>
                            <span
                                class="text-xs font-bold px-2 py-1 bg-emerald-50 text-emerald-600 rounded">Midtrans</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Web Penyewaan Kost & Mobil</h3>
                        <p class="text-slate-600 text-sm mb-5 line-clamp-3">
                            Pembuatan halaman listing dan pemesanan yang dilengkapi autentikasi Role-Based Access, serta
                            integrasi payment gateway (Midtrans/Xendit).
                        </p>
                        <a href="#"
                            class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                            Lihat Detail <i class="fa-solid fa-arrow-right-long ml-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Proyek 3: Game Bajaj Jek -->
                <div
                    class="bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-xl hover:shadow-indigo-100 transition-all duration-300 group">
                    <div class="h-48 bg-slate-200 overflow-hidden relative flex items-center justify-center">
                        <i
                            class="fa-solid fa-gamepad text-6xl text-slate-300 group-hover:scale-110 transition-transform duration-500"></i>
                        <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-transparent transition-colors">
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="text-xs font-bold px-2 py-1 bg-slate-800 text-white rounded">Unity</span>
                            <span class="text-xs font-bold px-2 py-1 bg-purple-50 text-purple-600 rounded">C#</span>
                            <span class="text-xs font-bold px-2 py-1 bg-orange-50 text-orange-600 rounded">UI/UX</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Game Bajaj Jek (3D/2D)</h3>
                        <p class="text-slate-600 text-sm mb-5 line-clamp-3">
                            Merancang game menggunakan Unity & C#. Membuat sistem navigasi, AI penumpang, serta
                            mengintegrasikan aset dan UI yang responsif.
                        </p>
                        <a href="#"
                            class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                            Lihat Detail <i class="fa-solid fa-arrow-right-long ml-2"></i>
                        </a>
                    </div>
                </div>

            </div>

            <div class="text-center mt-12">
                <a href="https://github.com/bimaryan" target="_blank"
                    class="inline-flex items-center justify-center px-6 py-3 font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                    Lihat Proyek di Github <i class="fa-brands fa-github ml-2"></i>
                </a>
            </div>

        </div>
    </section>

    <!-- CALL TO ACTION -->
    <section class="py-20 bg-indigo-600">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Siap untuk memulai proyek & deploy aplikasi
                Anda?</h2>
            <p class="text-indigo-100 text-lg mb-10">
                Bergabunglah dengan puluhan klien dan developer lainnya. Buat akun sekarang untuk pesan Jasa Joki atau
                Deploy Web App Anda di server kami.
            </p>
            <a href="{{ route('register') }}"
                class="inline-block px-8 py-4 text-lg font-bold rounded-xl text-indigo-700 bg-white shadow-lg hover:bg-slate-50 transition-transform hover:-translate-y-1">
                Buat Akun Gratis
            </a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-slate-900 pt-16 pb-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center gap-2 mb-4 md:mb-0">
                    <div class="bg-indigo-600 text-white rounded p-1.5">
                        <i class="fa-solid fa-code text-sm"></i>
                    </div>
                    <span class="text-xl font-bold text-white">Ryaze Portal</span>
                </div>
                <div class="flex gap-6 text-slate-400">
                    <a href="https://github.com/bimaryan" target="_blank"
                        class="hover:text-white transition-colors"><i class="fa-brands fa-github text-xl"></i></a>
                    <a href="#" class="hover:text-white transition-colors"><i
                            class="fa-brands fa-instagram text-xl"></i></a>
                    <a href="#" class="hover:text-white transition-colors"><i
                            class="fa-brands fa-linkedin text-xl"></i></a>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-slate-800 text-center text-slate-500 text-sm">
                &copy; {{ date('Y') }} Ryaze Portal. All rights reserved. Built with Laravel & Tailwind CSS.
            </div>
        </div>
    </footer>

</body>

</html>
