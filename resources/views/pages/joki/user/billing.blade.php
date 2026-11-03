@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- Header --}}
        <x-ui.page-header
            title="Riwayat Tagihan"
            eyebrow="Layanan Joki · Transaksi"
            subtitle="Daftar lengkap transaksi dan status pembayaran pesanan joki Anda."
            icon="fa-solid fa-file-invoice-dollar">
            <x-slot:actions>
                <a href="{{ route('user_joki.dashboard') }}" class="ui-btn-secondary font-mono text-[11px] uppercase tracking-[0.15em]">
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
                <tr class="hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828]">
                    <td class="px-6 py-4 font-mono font-semibold text-[#0F1A2A] dark:text-white">
                        {{ $payment->order->order_number ?? '-' }}
                    </td>
                    <td class="px-6 py-4">{{ $payment->payment_name }}</td>
                    <td class="px-6 py-4 font-mono tabular-nums">Rp{{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $statusClass = match ($payment->status) {
                                'paid', 'verified' => 'text-emerald-700 border-emerald-200 dark:text-emerald-400 dark:border-emerald-500/40',
                                'pending', 'pending_verification' => 'text-amber-700 border-amber-200 dark:text-amber-400 dark:border-amber-500/40',
                                'rejected', 'failed' => 'text-rose-700 border-rose-200 dark:text-rose-400 dark:border-rose-500/40',
                                default => 'text-[#525252] border-[#C9D5E2] dark:text-white/60 dark:border-[#24384F]',
                            };

                            $statusLabel = match ($payment->status) {
                                'paid', 'verified' => 'LUNAS',
                                'pending' => 'PENDING',
                                'pending_verification' => 'MENUNGGU VERIFIKASI',
                                'rejected', 'failed' => 'GAGAL / DITOLAK',
                                default => strtoupper($payment->status),
                            };
                        @endphp
                        <span class="ui-badge {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-[#94A3B4] dark:text-white/35 font-mono text-[11px] uppercase tracking-[0.2em]">Belum ada riwayat tagihan.</td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.page-layout>
@endsection
