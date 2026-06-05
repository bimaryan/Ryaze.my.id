@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50">
        <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg">
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

            <div class="mt-8 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-800">Antrean Pengerjaan (Queue)</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Klien</th>
                                <th class="px-6 py-4">Nama Proyek</th>
                                <th class="px-6 py-4">Deadline</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($queueOrders as $order)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4 font-medium text-slate-800">{{ $order->client->name }}</td>
                                    <td class="px-6 py-4">{{ $order->project_name }}</td>
                                    <td
                                        class="px-6 py-4 {{ \Carbon\Carbon::parse($order->deadline)->isPast() ? 'text-red-600 font-bold' : '' }}">
                                        {{ \Carbon\Carbon::parse($order->deadline)->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($order->status == 'pending')
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-600 border border-amber-200">Pending</span>
                                        @elseif($order->status == 'progress')
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600 border border-blue-200">Coding</span>
                                        @elseif($order->status == 'review')
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-600 border border-purple-200">Review</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button
                                            class="text-xs bg-indigo-600 text-white px-3 py-1.5 rounded hover:bg-indigo-700 transition-colors">Update
                                            Progres</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">Belum ada antrean proyek
                                        saat ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
