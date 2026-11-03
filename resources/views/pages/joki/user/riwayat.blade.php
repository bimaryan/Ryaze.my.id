@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Riwayat Pesanan Joki"
            eyebrow="Layanan Joki · Arsip Proyek"
            subtitle="Lihat kembali riwayat dan daftar proyek joki Anda yang sudah lalu."
            icon="fa-solid fa-history">
            <x-slot:actions>
                <a href="{{ route('user_joki.dashboard') }}" class="ui-btn-secondary font-mono text-[11px] uppercase tracking-[0.15em]">
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
                                <tr class="hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828]">
                                    <td class="px-6 py-4 font-medium text-[#0F1A2A] dark:text-white">{{ $order->project_name }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="bg-[#F0F6FC] dark:bg-[#0D1828] text-[#525252] dark:text-white/60 border border-[#E3EAF1] dark:border-[#1B2C44] font-mono text-[11px] px-2 py-1">
                                            {{ $order->tech_stack ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($order->status == 'completed')
                                            <span
                                                class="ui-badge ui-badge-success">Selesai</span>
                                        @elseif($order->status == 'canceled')
                                            <span
                                                class="ui-badge ui-badge-danger">Dibatalkan</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono text-[13px]">
                                        {{ \Carbon\Carbon::parse($order->updated_at)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('user_joki.detail', $order->hashid) }}"
                                            class="ui-icon-btn ui-icon-btn-primary tooltip" title="Lihat Detail">
                                            <i class="fa-solid fa-file-lines text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-[#94A3B4] dark:text-white/35 font-mono text-[11px] uppercase tracking-[0.2em]">Anda belum memiliki
                                        riwayat proyek yang selesai.</td>
                                </tr>
                            @endforelse
                </x-ui.table>
            </div>
        </div>
    </x-ui.page-layout>
@endsection
