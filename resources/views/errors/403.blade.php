@extends('index')
@section('title', 'Forbidden')
@section('content')
    <main class="flex-grow flex items-center justify-center pt-24 pb-16 px-6">
        <div class="w-full max-w-2xl text-center">
            <div class="w-20 h-20 bg-indigo-50 dark:bg-indigo-500/10 rounded-2xl flex items-center justify-center mx-auto mb-8 rotate-3">
                <i class="fa-solid fa-triangle-exclamation text-4xl text-indigo-500 dark:text-indigo-400 -rotate-3"></i>
            </div>
            <h1 class="text-7xl md:text-9xl font-black text-slate-900 dark:text-slate-50 tracking-tighter mb-4">403</h1>
            <h2 class="text-2xl md:text-3xl font-bold text-slate-800 dark:text-slate-100 mb-4">Forbidden</h2>
            <p class="text-base md:text-lg text-slate-500 dark:text-slate-400 mb-10 max-w-lg mx-auto leading-relaxed">Akses ditolak! Anda tidak memiliki izin untuk membuka halaman atau direktori ini.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ url('/') }}" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                    <i class="fa-solid fa-home"></i> Kembali ke Beranda
                </a>
                <button onclick="window.history.back()" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 bg-white dark:bg-slate-800/60 hover:bg-slate-50 dark:hover:bg-slate-700/40 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-bold py-3 px-6 rounded-lg transition-colors">
                    <i class="fa-solid fa-arrow-left"></i> Kembali Sebelumnya
                </button>
            </div>
        </div>
    </main>
@endsection
