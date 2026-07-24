<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Ryaze</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .animate-blob { animation: blob 7s infinite; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex items-center justify-center relative overflow-hidden">
    <!-- Decorative Blobs -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-indigo-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000"></div>
    <div class="absolute -bottom-32 left-20 w-96 h-96 bg-rose-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-4000"></div>

    <div class="relative z-10 w-full max-w-2xl p-6">
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-10 lg:p-16 text-center">
            
            <div class="mb-8">
                <h1 class="text-9xl font-black text-transparent bg-clip-text bg-gradient-to-br from-indigo-600 to-blue-500 tracking-tighter drop-shadow-sm">
                    @yield('code')
                </h1>
            </div>

            <h2 class="text-3xl font-bold mb-4 text-slate-800">
                @yield('message')
            </h2>
            
            <p class="text-slate-500 mb-10 text-lg leading-relaxed max-w-md mx-auto">
                @yield('description')
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ url('/') }}" class="inline-flex justify-center items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-indigo-200">
                    <i class="fa-solid fa-home"></i> Kembali ke Beranda
                </a>
                <button onclick="window.history.back()" class="inline-flex justify-center items-center gap-2 px-6 py-3 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-xl border border-slate-200 transition-all shadow-sm">
                    <i class="fa-solid fa-arrow-left"></i> Kembali Sebelumnya
                </button>
            </div>
        </div>

        <div class="text-center mt-12 text-slate-400 text-sm font-medium">
            &copy; {{ date('Y') }} Ryaze Ecosystem. All rights reserved.
        </div>
    </div>
</body>
</html>
