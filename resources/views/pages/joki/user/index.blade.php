@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50">
        <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg">
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
            <button
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-md shadow-indigo-200">
                + Pesan Joki Baru
            </button>
        </div>

        <div class="mt-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-800">Proyek Sedang Berjalan</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Nama Proyek</th>
                                <th class="px-6 py-4">Tech Stack</th>
                                <th class="px-6 py-4">Progres</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($activeOrders as $order)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-slate-800">{{ $order->project_name }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="bg-slate-100 text-slate-600 border border-slate-200 text-xs px-2 py-1 rounded font-medium">{{ $order->tech_stack ?? 'Menunggu Info' }}</span>
                                    </td>
                                    <td class="px-6 py-4 w-48">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span>Progres Berjalan</span>
                                            <span class="font-bold text-indigo-600">{{ $order->progress }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-200 rounded-full h-2">
                                            <div class="bg-indigo-600 h-2 rounded-full"
                                                style="width: {{ $order->progress }}%"></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
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
                                    <td class="px-6 py-4 text-center">
                                        <button
                                            class="text-xs border border-slate-300 text-slate-600 px-3 py-1.5 rounded hover:bg-slate-100 transition-colors">Detail</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">Anda belum memiliki
                                        proyek yang sedang berjalan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
