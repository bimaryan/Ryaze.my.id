@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- Header --}}
        <x-ui.page-header 
            title="Keuangan Joki" 
            subtitle="Laporan pendapatan dan riwayat pembayaran lunas." 
            icon="fa-solid fa-wallet">
            <x-slot:actions>
                <div class="inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-lg font-bold">
                    Total Pendapatan: Rp{{ number_format($totalRevenue, 0, ',', '.') }}
                </div>
            </x-slot:actions>
        </x-ui.page-header>

        {{-- Table --}}
        <x-ui.table>
            <x-slot:head>
                <th class="px-6 py-4">Waktu Lunas</th>
                <th class="px-6 py-4">Pesanan</th>
                <th class="px-6 py-4">Klien</th>
                <th class="px-6 py-4">Layanan</th>
                <th class="px-6 py-4 text-right">Jumlah Pendapatan</th>
            </x-slot:head>
            @forelse ($payments as $payment)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 text-slate-500 text-xs font-mono">
                        {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, H:i') : '-' }}
                    </td>
                    <td class="px-6 py-4 font-bold text-slate-800">
                        <a href="{{ route('admin_joki.orders.edit', $payment->order->hashid) }}"
                            class="text-indigo-600 hover:text-indigo-800">
                            {{ $payment->order->order_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        {{ $payment->order->client->name ?? 'Unknown' }}
                    </td>
                    <td class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">
                        {{ $payment->order->service->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-right font-mono font-medium text-emerald-600">
                        + Rp{{ number_format($payment->amount, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-slate-400">Belum ada pendapatan yang tercatat.
                    </td>
                </tr>
            @endforelse
        </x-ui.table>

    </x-ui.page-layout>
@endsection
