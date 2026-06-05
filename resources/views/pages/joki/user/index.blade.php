@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50">
        <div
            class="p-6 bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 flex-shrink-0 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg">
                    <i class="fa-solid fa-laptop-code text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Dashboard Proyek Saya</h1>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Halo, <span class="font-semibold text-indigo-600">{{ Auth::user()->name ?? 'Klien' }}</span>! Pantau
                        pesanan joki Anda di sini.
                    </p>
                </div>
            </div>

            <a href="{{ route('user_joki.create') }}"
                class="inline-flex justify-center items-center flex-shrink-0 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-md shadow-indigo-200">
                + Pesan Joki Baru
            </a>
        </div>

        <div class="mt-6">
        </div>
    </div>
@endsection
