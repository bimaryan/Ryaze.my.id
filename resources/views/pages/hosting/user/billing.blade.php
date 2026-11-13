@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- Header --}}
        <x-ui.page-header 
            title="Riwayat Tagihan" 
            eyebrow="Transaksi & Pembayaran"
            subtitle="Daftar lengkap transaksi dan status pembayaran hosting Anda." 
            icon="fa-file-invoice-dollar" 
            iconColor="emerald">
            <x-slot:actions>
                <a href="{{ route('user_hosting.dashboard') }}"
                    class="ui-btn ui-btn-md ui-btn-icon ui-btn-secondary" title="Kembali" aria-label="Kembali">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        {{-- Ringkasan Angka --}}
        @php
            $paidCount = $billings->where('status', 'paid')->count();
            $unpaidCount = $billings->where('status', 'unpaid')->count();
            $failedCount = $billings->where('status', 'failed')->count();
        @endphp
        <div class="ui-panel">
            <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">Ringkasan Tagihan</span>
                <span class="ui-status"><span class="ui-status-dot"></span> Halaman {{ $billings->currentPage() }}</span>
            </div>
            <div class="ui-stat-strip grid-cols-2 md:grid-cols-4">
                <div class="px-6 py-6">
                    <p class="ui-stat-value">{{ number_format($billings->total()) }}</p>
                    <p class="ui-stat-label">Total Tagihan</p>
                </div>
                <div class="px-6 py-6">
                    <p class="ui-stat-value text-emerald-600 dark:text-emerald-400">{{ $paidCount }}</p>
                    <p class="ui-stat-label">Lunas</p>
                </div>
                <div class="px-6 py-6">
                    <p class="ui-stat-value text-amber-600 dark:text-amber-400">{{ $unpaidCount }}</p>
                    <p class="ui-stat-label">Belum Lunas</p>
                </div>
                <div class="px-6 py-6">
                    <p class="ui-stat-value text-rose-600 dark:text-rose-400">{{ $failedCount }}</p>
                    <p class="ui-stat-label">Gagal</p>
                </div>
            </div>
        </div>

        <x-ui.table class="mt-6">
            <x-slot:header>
                <div class="ui-panel-head px-6 py-5 bg-[#F4F8FC] dark:bg-[#12233A]">
                    <h2 class="font-mono text-sm font-bold uppercase tracking-[0.2em] text-[#0F1A2A] dark:text-white">Riwayat Tagihan</h2>
                    <a href="{{ route('user_hosting.dashboard') }}" class="ui-arrow-link">
                        Dashboard <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </x-slot:header>
            <x-slot:head>
                <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-wider">Invoice / Tanggal</th>
                <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-wider">Keterangan</th>
                <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-wider">Jumlah</th>
                <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-wider">Metode</th>
                <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-wider text-center">Status</th>
                <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-wider text-center">Aksi</th>
            </x-slot:head>
            @forelse ($billings as $bill)
                <tr class="hover:bg-[#E8F0F9] dark:hover:bg-[#1A2F49] transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-[#0F1A2A] dark:text-white">{{ $bill->invoice_number }}</div>
                        <div class="text-xs text-[#8A9BAE] dark:text-white/40 font-mono">{{ $bill->created_at->format('d M Y, H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 font-semibold text-[#0F1A2A] dark:text-white">{{ $bill->project->project_name ?? 'Langganan Akun' }}</td>
                    <td class="px-6 py-4 font-mono font-semibold text-[#0F1A2A] dark:text-white">Rp{{ number_format($bill->amount, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-xs font-mono font-semibold uppercase text-[#525252] dark:text-white/50">{{ $bill->payment_method ?? '-' }}</td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $statusClass = match ($bill->status) {
                                'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/50',
                                'unpaid' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/50',
                                'failed' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800/50',
                                default => 'bg-[#F0F6FC] text-[#525252] border-[#E3EAF1] dark:bg-[#12233A] dark:text-white/60 dark:border-[#2A4463]',
                            };
                            $statusLabel = strtoupper($bill->status);
                        @endphp
                        <span class="px-2.5 py-1 text-[10px] font-mono font-bold uppercase tracking-wider border {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if ($bill->status === 'unpaid')
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('payment.qris', ['order_id' => $bill->invoice_number]) }}"
                                   class="ui-btn ui-btn-sm ui-btn-success"
                                   title="Bayar dengan QRIS Dinamis (Semua Bank & e-Wallet)">
                                    <i class="fa-solid fa-qrcode"></i> QRIS
                                </a>
                                <a href="{{ route('payment.doku', ['order_id' => $bill->invoice_number]) }}"
                                   class="ui-btn ui-btn-sm ui-btn-secondary"
                                   title="Bayar via DOKU (VA, e-Wallet, QRIS, Kartu Kredit)">
                                    <i class="fa-solid fa-credit-card"></i> DOKU
                                </a>
                                <form action="{{ route('user_hosting.billing.pay_wallet') }}" method="POST" class="m-0 p-0 inline-block" onsubmit="event.preventDefault(); let f = this; swConfirm('Bayar dengan Saldo?', 'Tagihan ini akan dibayar menggunakan saldo wallet Anda.').then(res => { if(res.isConfirmed) f.submit(); }); return false;">
                                    @csrf
                                    <input type="hidden" name="invoice_number" value="{{ $bill->invoice_number }}">
                                    <button type="submit" class="ui-btn ui-btn-sm ui-btn-primary" title="Bayar dari Saldo Ryaze Wallet">
                                        <i class="fa-solid fa-wallet"></i> Saldo
                                    </button>
                                </form>
                            </div>
                        @else
                            <span class="font-mono text-xs text-[#C9D5E2] dark:text-white/30">&mdash;</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center font-mono text-xs text-[#8A9BAE] dark:text-white/45">Belum ada riwayat tagihan.</td>
                </tr>
            @endforelse
            <x-slot:pagination>
                {{ $billings->links() }}
            </x-slot:pagination>
        </x-ui.table>
    </x-ui.page-layout>
@endsection
