@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50 relative">
        <div class="p-5 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="shrink-0 w-11 h-11 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg">
                    <i class="fa-solid fa-gauge text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Dashboard Joki Code</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Pantau status pesanan dan proyek terbaru Anda di sini.</p>
                </div>
            </div>
            <a href="{{ route('user_joki.create') }}" class="inline-flex justify-center items-center bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                + Pesan Joki Baru
            </a>
        </div>

        <div class="mt-6">
        </div>
    </div>
@endsection
