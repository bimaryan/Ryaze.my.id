@extends('index')

@section('content')
    <x-ui.page-layout>
<div class="p-5 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="shrink-0 w-11 h-11 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg">
                    <i class="fa-solid fa-spinner text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Progres Pesanan Joki</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Pantau status pengerjaan proyek Anda yang sedang aktif.</p>
                </div>
            </div>
            <a href="{{ route('user_joki.dashboard') }}" class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                &larr; Kembali
            </a>
        </div>

        <div class="mt-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-800">Proyek Sedang Berjalan</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 whitespace-nowrap">Nama Proyek</th>
                                <th class="px-6 py-4 whitespace-nowrap">Tech Stack</th>
                                <th class="px-6 py-4 whitespace-nowrap w-48">Progres</th>
                                <th class="px-6 py-4 whitespace-nowrap text-center">Status</th>
                                <th class="px-6 py-4 whitespace-nowrap text-center">Aksi</th>
                                </x-slot:head>
                            @forelse($activeOrders as $order)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-slate-800 whitespace-nowrap">
                                        {{ $order->project_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="bg-slate-100 text-slate-600 border border-slate-200 text-xs px-2.5 py-1 rounded-md font-medium">
                                            {{ $order->tech_stack ?? 'Menunggu Info' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 w-48 align-middle">
                                        <div class="flex justify-between text-xs mb-1.5">
                                            <span class="font-medium text-slate-500">Berjalan</span>
                                            <span class="font-bold text-indigo-600">{{ $order->progress }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                            <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500"
                                                style="width: {{ $order->progress }}%"></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        @if ($order->status == 'pending')
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-600 border border-amber-200">Pending</span>
                                        @elseif($order->status == 'progress')
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600 border border-blue-200">In
                                                Progress</span>
                                        @elseif($order->status == 'review')
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-600 border border-purple-200">Butuh
                                                Review</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <a href="{{ route('user_joki.detail', $order->hashid) }}"
                                            class="inline-block text-xs border border-indigo-200 text-indigo-700 bg-indigo-50 px-4 py-2 rounded-lg hover:bg-indigo-600 hover:text-white transition-all duration-200 font-semibold shadow-sm">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div
                                                class="w-16 h-16 mb-4 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center text-2xl">
                                                <i class="fa-solid fa-folder-open"></i>
                                            </div>
                                            <p class="text-slate-500 font-medium">Anda belum memiliki proyek yang sedang
                                                berjalan.</p>
                                            <a href="{{ route('user_joki.create') }}"
                                                class="mt-2 text-indigo-600 hover:text-indigo-700 text-sm font-semibold hover:underline">
                                                Pesan Proyek Sekarang &rarr;
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </x-ui.table>
        </div>
    </x-ui.page-layout>
@endsection
