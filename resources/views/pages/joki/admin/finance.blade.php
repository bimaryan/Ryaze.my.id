@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- Header --}}
        <x-ui.page-header
            title="Keuangan Joki"
            eyebrow="Admin Joki · Laporan"
            subtitle="Laporan pendapatan dan riwayat pembayaran lunas."
            icon="fa-solid fa-wallet"
        >
            <x-slot:actions>
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-[#F0F6FC] dark:bg-[#0D1828] text-[#1D5B96] dark:text-[#6BA8DC] border border-[#C9D5E2] dark:border-[#1B2C44] font-mono text-xs font-bold uppercase tracking-[0.15em]">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Total Pendapatan: Rp{{ number_format($totalRevenue, 0, ',', '.') }}
                </div>
            </x-slot:actions>
        </x-ui.page-header>

        {{-- Table --}}
        <div class="mt-6">
            <x-ui.table>
                <x-slot:head>
                    <th class="px-6 py-4">Waktu Lunas</th>
                    <th class="px-6 py-4">Pesanan</th>
                    <th class="px-6 py-4">Klien</th>
                    <th class="px-6 py-4">Layanan</th>
                    <th class="px-6 py-4 text-right">Jumlah Pendapatan</th>
                </x-slot:head>
                @forelse ($payments as $payment)
                    <tr class="hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828]">
                        <td class="px-6 py-4 text-[#737373] dark:text-white/40 text-xs font-mono">
                            {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 font-bold">
                            <a href="{{ route('admin_joki.orders.edit', $payment->order->hashid) }}"
                                class="font-mono text-[#1D5B96] dark:text-[#6BA8DC] hover:underline">
                                {{ $payment->order->order_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 font-medium text-[#0F1A2A] dark:text-white">
                            {{ $payment->order->client->name ?? 'Unknown' }}
                        </td>
                        <td class="px-6 py-4 text-xs font-semibold text-[#737373] dark:text-white/40 uppercase tracking-wide">
                            {{ $payment->order->service->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-right font-mono font-medium text-emerald-600 dark:text-emerald-400">
                            + Rp{{ number_format($payment->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-[#94A3B4] dark:text-white/35 font-mono text-[11px] uppercase tracking-[0.2em]">Belum ada pendapatan yang tercatat.</td>
                    </tr>
                @endforelse
            </x-ui.table>
        </div>

    </x-ui.page-layout>
@endsection
