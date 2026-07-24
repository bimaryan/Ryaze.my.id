<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Ryaze</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #ffffff; color: #111827; }
        .bg-grid {
            background-image: linear-gradient(to right, #f1f5f9 1px, transparent 1px),
                linear-gradient(to bottom, #f1f5f9 1px, transparent 1px);
            background-size: 40px 40px;
            background-position: center top;
        }
    </style>
</head>
<body class="antialiased selection:bg-indigo-600 selection:text-white bg-grid min-h-screen flex flex-col relative overflow-hidden">
    
    <!-- NAVBAR -->
    <nav x-data="{ mobileMenuOpen: false }" class="fixed top-0 z-50 w-full bg-white/90 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                    <div class="bg-indigo-600 text-white rounded-md w-8 h-8 flex items-center justify-center">
                        <i class="fa-solid fa-code text-sm"></i>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-indigo-600">Ryaze Portal</span>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                    <a href="{{ url('/#about') }}" class="hover:text-indigo-600 transition-colors">Tentang</a>
                    <a href="{{ url('/#services') }}" class="hover:text-indigo-600 transition-colors">Layanan</a>
                    <a href="{{ url('/#portfolio') }}" class="hover:text-indigo-600 transition-colors">Portofolio</a>
                    <a href="{{ route('blog.index') }}" class="hover:text-indigo-600 transition-colors">Blog</a>
                </div>

                <!-- Auth Buttons & Mobile Toggle -->
                <div class="flex items-center gap-4">
                    <a href="{{ url('/') }}" class="hidden sm:inline-flex text-sm font-semibold bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                        Kembali
                    </a>
                    
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
                <a href="{{ url('/#about') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-indigo-600 hover:bg-slate-50">Tentang</a>
                <a href="{{ url('/#services') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-indigo-600 hover:bg-slate-50">Layanan</a>
                <a href="{{ url('/#portfolio') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-indigo-600 hover:bg-slate-50">Portofolio</a>
                <a href="{{ route('blog.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-indigo-600 hover:bg-slate-50">Blog</a>
                <a href="{{ url('/') }}" class="block w-full text-center mt-4 text-sm font-semibold bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                    Kembali
                </a>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <main class="flex-grow flex items-center justify-center pt-24 pb-16 px-6">
        <div class="w-full max-w-2xl text-center">
            <!-- Decorative Icon -->
            <div class="w-20 h-20 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-8 rotate-3">
                <i class="fa-solid fa-triangle-exclamation text-4xl text-indigo-500 -rotate-3"></i>
            </div>
            
            <h1 class="text-7xl md:text-9xl font-black text-slate-900 tracking-tighter mb-4">
                @yield('code')
            </h1>
            
            <h2 class="text-2xl md:text-3xl font-bold text-slate-800 mb-4">
                @yield('message')
            </h2>
            
            <p class="text-base md:text-lg text-slate-500 mb-10 max-w-lg mx-auto leading-relaxed">
                @yield('description')
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ url('/') }}" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                    <i class="fa-solid fa-home"></i> Kembali ke Beranda
                </a>
                <button onclick="window.history.back()" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold py-3 px-6 rounded-lg transition-colors">
                    <i class="fa-solid fa-arrow-left"></i> Kembali Sebelumnya
                </button>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-slate-200 py-8">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-medium text-slate-500">
            <div class="flex items-center gap-2 text-indigo-600">
                <i class="fa-solid fa-code text-sm"></i>
                <span class="text-sm font-bold tracking-tight">Ryaze Portal</span>
            </div>
            <p>&copy; {{ date('Y') }} Ryaze Ecosystem. All rights reserved.</p>
            <p>Engineered by Bima Ryan.</p>
        </div>
    </footer>

</body>
</html>
