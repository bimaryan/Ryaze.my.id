@extends('index')

@section('content')
    <x-ui.page-layout>
        <!-- Header -->
        <x-ui.page-header
            title="Daftar Pesanan Masuk"
            eyebrow="Admin Joki · Semua Pesanan"
            subtitle="Kelola dan pantau semua pesanan joki dari klien."
            icon="fa-solid fa-clipboard-list"
        />

        <div class="mt-6">
            <!-- Tabel Pesanan -->
            <x-ui.table>
                <x-slot:head>
                    <th scope="col" class="px-6 py-4">ID Pesanan</th>
                    <th scope="col" class="px-6 py-4">Klien</th>
                    <th scope="col" class="px-6 py-4">Nama Proyek</th>
                    <th scope="col" class="px-6 py-4 text-center">Status</th>
                    <th scope="col" class="px-6 py-4 text-center">Deadline</th>
                    <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                </x-slot:head>
                @forelse($orders as $order)
                    <tr class="hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828]">
                        <td class="px-6 py-4 font-mono font-semibold text-[#1D5B96] dark:text-[#6BA8DC]">{{ $order->order_number }}</td>
                        <td class="px-6 py-4 font-medium text-[#0F1A2A] dark:text-white flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-[#F0F6FC] dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] text-[#525252] dark:text-white/60 flex items-center justify-center font-mono font-bold text-xs uppercase">
                                {{ substr($order->client->name ?? 'U', 0, 1) }}
                            </div>
                            {{ $order->client->name ?? 'Klien Terhapus' }}
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-[#0F1A2A] dark:text-white">{{ $order->project_name }}</p>
                            <p class="text-xs text-[#94A3B4] dark:text-white/35 mt-0.5">{{ $order->tech_stack }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if ($order->status == 'pending')
                                <span class="ui-badge ui-badge-warning">Pending</span>
                            @elseif($order->status == 'progress')
                                <span class="ui-badge ui-badge-primary">Progress ({{ $order->progress }}%)</span>
                            @elseif($order->status == 'review')
                                <span class="ui-badge ui-badge-info">Review</span>
                            @elseif($order->status == 'completed')
                                <span class="ui-badge ui-badge-success">Selesai</span>
                            @endif
                        </td>
                        <td
                            class="px-6 py-4 text-center font-mono text-[13px] {{ \Carbon\Carbon::parse($order->deadline)->isPast() ? 'text-rose-600 dark:text-rose-400 font-bold' : '' }}">
                            {{ \Carbon\Carbon::parse($order->deadline)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin_joki.orders.edit', $order->hashid) }}"
                                class="ui-icon-btn ui-icon-btn-primary tooltip"
                                title="Kelola Pesanan">
                                <i class="fa-solid fa-gear text-xs"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-[#94A3B4] dark:text-white/35 font-mono text-[11px] uppercase tracking-[0.2em]">Belum ada data pesanan masuk.</td>
                    </tr>
                @endforelse
                @if ($orders->hasPages())
                    <x-slot:pagination>
                        {{ $orders->links() }}
                    </x-slot:pagination>
                @endif
            </x-ui.table>
        </div>
    </x-ui.page-layout>
@endsection
