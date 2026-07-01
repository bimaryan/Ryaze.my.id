@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- Header --}}
        <x-ui.page-header 
            title="Riwayat Tagihan" 
            subtitle="Daftar lengkap transaksi dan status pembayaran pesanan joki Anda." 
            icon="fa-solid fa-file-invoice-dollar">
            <x-slot:actions>
                <a href="{{ route('user_joki.dashboard') }}"
                    class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.table>
            <x-slot:head>
                <th class="px-6 py-4">Nomor Pesanan</th>
                <th class="px-6 py-4">Pembayaran</th>
                <th class="px-6 py-4">Jumlah</th>
                <th class="px-6 py-4 text-center">Status</th>
            </x-slot:head>
            @forelse ($payments as $payment)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-semibold text-slate-800">
                        {{ $payment->order->order_number ?? '-' }}
                    </td>
                    <td class="px-6 py-4">{{ $payment->payment_name }}</td>
                    <td class="px-6 py-4 font-mono">Rp{{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $statusClass = match ($payment->status) {
                                'paid', 'verified' => 'bg-emerald-100 text-emerald-700',
                                'pending', 'pending_verification' => 'bg-amber-100 text-amber-700',
                                'rejected', 'failed' => 'bg-rose-100 text-rose-700',
                                default => 'bg-slate-100 text-slate-700',
                            };

                            $statusLabel = match ($payment->status) {
                                'paid', 'verified' => 'LUNAS',
                                'pending' => 'PENDING',
                                'pending_verification' => 'MENUNGGU VERIFIKASI',
                                'rejected', 'failed' => 'GAGAL / DITOLAK',
                                default => strtoupper($payment->status),
                            };
                        @endphp
                        <span class="text-xs font-bold px-2 py-1 rounded-full {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-slate-400">Belum ada riwayat tagihan.</td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.page-layout>
@endsection
