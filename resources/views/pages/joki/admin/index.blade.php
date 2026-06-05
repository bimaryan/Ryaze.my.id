@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50">
        <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg">
                    <i class="fa-solid fa-code-branch text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Manajemen Joki Code</h1>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Semangat ngoding, <span
                            class="font-semibold text-indigo-600">{{ Auth::user()->name ?? 'Dev' }}</span>! Berikut antrean
                        pekerjaanmu.
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 border-t-4 border-t-amber-500">
                    <p class="text-sm font-medium text-slate-500">Pesanan Baru (Pending)</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $pendingOrders }}</h3>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 border-t-4 border-t-blue-500">
                    <p class="text-sm font-medium text-slate-500">Sedang Dikerjakan</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $progressOrders }}</h3>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 border-t-4 border-t-purple-500">
                    <p class="text-sm font-medium text-slate-500">Menunggu Review Klien</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $reviewOrders }}</h3>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 border-t-4 border-t-emerald-500">
                    <p class="text-sm font-medium text-slate-500">Proyek Selesai (Bulan ini)</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $completedOrders }}</h3>
                </div>
            </div>
        </div>
    </div>
@endsection
