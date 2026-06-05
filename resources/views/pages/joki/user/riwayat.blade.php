@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50">
        <!-- Header -->
        <div
            class="p-6 bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Riwayat Proyek</h1>
                <p class="text-sm text-slate-500 mt-0.5">
                    Arsip dan rekam jejak seluruh proyek Anda yang sudah selesai atau dibatalkan.
                </p>
            </div>
        </div>

        <div class="mt-6">
            <div class="mt-8 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-800">Riwayat Proyek</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Nama Proyek</th>
                                <th class="px-6 py-4">Tech Stack</th>
                                <th class="px-6 py-4 text-center">Status Akhir</th>
                                <th class="px-6 py-4 text-center">Tanggal Selesai</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($historyOrders as $order)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-slate-800">{{ $order->project_name }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="bg-slate-100 text-slate-600 border border-slate-200 text-xs px-2 py-1 rounded font-medium">
                                            {{ $order->tech_stack ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($order->status == 'completed')
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-200">Selesai</span>
                                        @elseif($order->status == 'canceled')
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-600 border border-rose-200">Dibatalkan</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        {{ \Carbon\Carbon::parse($order->updated_at)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('user_joki.detail', $order->hashid) }}"
                                            class="inline-block text-xs border border-indigo-200 text-indigo-700 bg-indigo-50 px-4 py-2 rounded-lg hover:bg-indigo-600 hover:text-white transition-all duration-200 font-semibold shadow-sm">Lihat
                                            Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">Anda belum memiliki
                                        riwayat proyek yang selesai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
