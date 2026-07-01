@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50 relative">

        {{-- Header --}}
        <div class="p-5 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="shrink-0 w-11 h-11 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg">
                    <i class="fa-solid fa-wallet text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Keuangan Joki</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Laporan pendapatan dan riwayat pembayaran lunas.</p>
                </div>
            </div>
            <div class="inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-lg font-bold">
                Total Pendapatan: Rp{{ number_format($totalRevenue, 0, ',', '.') }}
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">Waktu Lunas</th>
                            <th class="px-6 py-4">Pesanan</th>
                            <th class="px-6 py-4">Klien</th>
                            <th class="px-6 py-4">Layanan</th>
                            <th class="px-6 py-4 text-right">Jumlah Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($payments as $payment)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-slate-500 text-xs font-mono">
                                    {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800">
                                    <a href="{{ route('admin_joki.orders.edit', $payment->order->hashid) }}" class="text-indigo-600 hover:text-indigo-800">
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
                                <td colspan="5" class="px-6 py-10 text-center text-slate-400">Belum ada pendapatan yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
