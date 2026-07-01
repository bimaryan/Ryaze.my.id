@extends('index')

@section('content')
    <x-ui.page-layout>
<div class="p-5 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="shrink-0 w-11 h-11 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg">
                    <i class="fa-solid fa-gauge text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Manajemen Joki Code</h1>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Semangat ngoding, <span class="font-semibold text-indigo-600">{{ Auth::user()->name ?? 'Dev' }}</span>! Berikut antrean pekerjaanmu.
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 border-t-4 border-t-amber-500">
                    <p class="text-sm font-medium text-slate-500">Pesanan Baru (Pending)</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $pendingOrders }}</h3>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 border-t-4 border-t-blue-500">
                    <p class="text-sm font-medium text-slate-500">Sedang Dikerjakan</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $progressOrders }}</h3>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 border-t-4 border-t-purple-500">
                    <p class="text-sm font-medium text-slate-500">Menunggu Review Klien</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $reviewOrders }}</h3>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 border-t-4 border-t-emerald-500">
                    <p class="text-sm font-medium text-slate-500">Proyek Selesai (Bulan ini)</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $completedOrders }}</h3>
                </div>
            </div>

            {{-- Tabel Antrean --}}
            <div class="mt-8 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-200 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-800">Antrean Pekerjaan Aktif</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Klien & Order ID</th>
                                <th class="px-6 py-4">Proyek</th>
                                <th class="px-6 py-4">Status & Progres</th>
                                <th class="px-6 py-4 text-center">Tenggat Waktu</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                                </x-slot:head>
                            @forelse ($queueOrders as $order)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-800">{{ $order->client->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-slate-500 font-mono mt-0.5">{{ $order->order_number }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-slate-800">{{ $order->project_name }}</div>
                                        <div class="text-xs text-slate-500 mt-0.5">{{ $order->tech_stack }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-amber-100 text-amber-700',
                                                'progress' => 'bg-blue-100 text-blue-700',
                                                'review' => 'bg-purple-100 text-purple-700'
                                            ];
                                            $color = $statusColors[$order->status] ?? 'bg-slate-100 text-slate-700';
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $color }} uppercase">
                                            {{ $order->status }}
                                        </span>
                                        @if($order->status == 'progress')
                                            <div class="mt-2 w-full bg-slate-200 rounded-full h-1.5">
                                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $order->progress }}%"></div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-xs font-medium px-2 py-1 rounded-lg {{ \Carbon\Carbon::parse($order->deadline)->isPast() ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700' }}">
                                            {{ \Carbon\Carbon::parse($order->deadline)->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('admin_joki.orders.edit', $order->hashid) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-slate-400">Tidak ada antrean aktif saat ini.</td>
                                </tr>
                            @endforelse
                        </x-ui.table>
        </div>
    </x-ui.page-layout>
@endsection
