@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Progres Pesanan Joki"
            eyebrow="Layanan Joki · Pengerjaan Aktif"
            status="LIVE"
            subtitle="Pantau status pengerjaan proyek Anda yang sedang aktif."
            icon="fa-solid fa-spinner">
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
                        <th class="px-6 py-4 whitespace-nowrap">Nama Proyek</th>
                        <th class="px-6 py-4 whitespace-nowrap">Tech Stack</th>
                        <th class="px-6 py-4 whitespace-nowrap w-48">Progres</th>
                        <th class="px-6 py-4 whitespace-nowrap text-center">Status</th>
                        <th class="px-6 py-4 whitespace-nowrap text-center">Aksi</th>
                    </x-slot:head>
                            @forelse($activeOrders as $order)
                                <tr class="hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828]">
                                    <td class="px-6 py-4 font-medium text-[#0F1A2A] dark:text-white whitespace-nowrap">
                                        {{ $order->project_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="bg-[#F0F6FC] dark:bg-[#0D1828] text-[#525252] dark:text-white/60 border border-[#E3EAF1] dark:border-[#1B2C44] font-mono text-[11px] px-2.5 py-1">
                                            {{ $order->tech_stack ?? 'Menunggu Info' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 w-48 align-middle">
                                        <div class="flex justify-between text-[11px] mb-1.5">
                                            <span class="font-mono uppercase tracking-[0.15em] text-[#737373] dark:text-white/40">Berjalan</span>
                                            <span class="font-mono font-bold text-[#1D5B96] dark:text-[#6BA8DC] tabular-nums">{{ $order->progress }}%</span>
                                        </div>
                                        <div class="w-full bg-[#E3EAF1] dark:bg-[#1B2C44] h-1.5 overflow-hidden">
                                            <div class="bg-[#1D5B96] dark:bg-[#6BA8DC] h-1.5 transition-all duration-500"
                                                style="width: {{ $order->progress }}%"></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        @if ($order->status == 'pending')
                                            <span class="ui-badge ui-badge-warning">Pending</span>
                                        @elseif($order->status == 'progress')
                                            <span class="ui-badge ui-badge-primary">In Progress</span>
                                        @elseif($order->status == 'review')
                                            <span class="ui-badge ui-badge-info">Butuh Review</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <a href="{{ route('user_joki.detail', $order->hashid) }}"
                                            class="ui-icon-btn ui-icon-btn-primary tooltip" title="Detail Proyek">
                                            <i class="fa-solid fa-file-lines text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div
                                                class="w-16 h-16 mb-4 bg-[#F0F6FC] dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] text-[#C9D5E2] dark:text-white/40 flex items-center justify-center text-2xl">
                                                <i class="fa-solid fa-folder-open"></i>
                                            </div>
                                            <p class="ui-display text-sm">Anda belum memiliki proyek yang sedang berjalan.</p>
                                            <a href="{{ route('user_joki.create') }}"
                                                class="ui-arrow-link mt-3">
                                                Pesan Proyek Sekarang <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                </x-ui.table>
            </div>
        </div>
    </x-ui.page-layout>
@endsection
