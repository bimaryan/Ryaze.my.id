<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tunnel Offline | Ryaze</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 font-sans">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100 text-center">
        <div class="bg-slate-900 px-6 py-8 relative overflow-hidden">
            <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.2)_0,transparent_100%)]"></div>
            <i class="fa-solid fa-plug-circle-xmark text-5xl text-rose-400 mb-4 drop-shadow-md"></i>
            <h1 class="text-2xl font-bold text-white mb-1">Tunnel Offline</h1>
            <p class="text-slate-300 text-sm">ryaze.my.id</p>
        </div>
        
        <div class="p-8 space-y-6">
            <div>
                <p class="text-slate-600 mb-2">Koneksi tunnel ke <span class="font-semibold text-slate-800">{{ $subdomain }}.ryaze.my.id</span> saat ini terputus.</p>
                <div class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-rose-50 text-rose-600 text-xs font-medium border border-rose-100">
                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse mr-2"></span> Tidak Terhubung
                </div>
            </div>

            <div class="bg-slate-50 rounded-xl p-4 text-left border border-slate-100">
                <h3 class="text-sm font-bold text-slate-800 mb-2"><i class="fa-solid fa-lightbulb text-amber-500 mr-2"></i>Cara Mengaktifkan:</h3>
                <ol class="text-xs text-slate-600 space-y-2 list-decimal list-inside">
                    <li>Buka terminal komputer lokal Anda.</li>
                    <li>Jalankan ulang script PHP Tunnel Client.</li>
                    <li>Pastikan komputer Anda terhubung ke internet.</li>
                    <li>Refresh halaman ini.</li>
                </ol>
            </div>

            <button onclick="window.location.reload()" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                <i class="fa-solid fa-rotate-right"></i>
                Coba Lagi
            </button>
        </div>
    </div>
</body>
</html>
