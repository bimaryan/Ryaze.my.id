@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Riwayat Pesanan Joki" 
            subtitle="Lihat kembali riwayat dan daftar proyek joki Anda yang sudah lalu." 
            icon="fa-solid fa-history">
            <x-slot:actions>
                <a href="{{ route('user_joki.dashboard') }}" class="inline-flex justify-center items-center bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-200 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="mt-6">
            <div>
                <x-ui.table>
                    <x-slot:head>
                        <th class="px-6 py-4">Nama Proyek</th>
                        <th class="px-6 py-4">Tech Stack</th>
                        <th class="px-6 py-4 text-center">Status Akhir</th>
                        <th class="px-6 py-4 text-center">Tanggal Selesai</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </x-slot:head>
                            @forelse($historyOrders as $order)
                                <tr class="hover:bg-slate-50 dark:bg-slate-800/50 dark:hover:bg-slate-700/40 transition-colors">
                                    <td class="px-6 py-4 font-medium text-slate-800 dark:text-slate-100">{{ $order->project_name }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-xs px-2 py-1 rounded font-medium">
                                            {{ $order->tech_stack ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($order->status == 'completed')
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/40">Selesai</span>
                                        @elseif($order->status == 'canceled')
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-300 border border-rose-200 dark:border-rose-500/40">Dibatalkan</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        {{ \Carbon\Carbon::parse($order->updated_at)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('user_joki.detail', $order->hashid) }}"
                                            class="w-8 h-8 mx-auto rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-600 hover:text-white transition-all duration-200 shadow-sm tooltip" title="Lihat Detail">
                                            <i class="fa-solid fa-file-lines"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">Anda belum memiliki
                                        riwayat proyek yang selesai.</td>
                                </tr>
                            @endforelse
                </x-ui.table>
            </div>
        </div>
    </x-ui.page-layout>
@endsection
