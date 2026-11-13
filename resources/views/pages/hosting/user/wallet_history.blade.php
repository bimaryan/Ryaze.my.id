@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- Header --}}
        <x-ui.page-header 
            title="Ryaze Wallet" 
            eyebrow="Saldo & Transaksi"
            status="AKTIF"
            subtitle="Kelola saldo dan riwayat transaksi Anda." 
            icon="fa-wallet" 
            iconColor="indigo">
            <x-slot:actions>
                <button type="button" onclick="document.getElementById('topup-modal').showModal()"
                    class="ui-btn ui-btn-primary px-5 py-2.5 text-sm font-bold">
                    <i class="fa-solid fa-plus mr-2"></i> Top Up
                </button>
            </x-slot:actions>
        </x-ui.page-header>

        {{-- Saldo & Ringkasan --}}
        <div class="ui-grid grid-cols-1 mb-6">
            <div class="ui-cell relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10 text-[#0F1A2A] dark:text-white pointer-events-none">
                    <i class="fa-solid fa-wallet text-8xl"></i>
                </div>
                <div class="relative z-10 px-6 pt-5 flex items-center justify-between gap-4">
                    <span class="ui-eyebrow">Ryaze Wallet</span>
                    <span class="ui-status"><span class="ui-status-dot"></span>AKTIF</span>
                </div>
                <div class="ui-stat-strip grid-cols-2 relative z-10 mt-1">
                    <div class="px-6 py-6">
                        <p class="ui-stat-value">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
                        <p class="ui-stat-label">Saldo Aktif</p>
                    </div>
                    <div class="px-6 py-6">
                        <p class="ui-stat-value">{{ number_format($transactions->total(), 0, ',', '.') }}</p>
                        <p class="ui-stat-label">Total Transaksi</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- History Table --}}
        <x-ui.table>
            <x-slot:header>
                <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                    <h2 class="font-mono text-[11px] font-semibold uppercase tracking-[0.2em] text-[#0F1A2A] dark:text-white">Riwayat Transaksi</h2>
                    <a href="{{ route('user.wallet.withdraw') }}" class="ui-arrow-link">
                        Tarik Saldo <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </x-slot:header>
            <x-slot:head>
                <th class="px-6 py-4">Tanggal / Waktu</th>
                <th class="px-6 py-4">Keterangan</th>
                <th class="px-6 py-4">Tipe</th>
                <th class="px-6 py-4">Jumlah</th>
                <th class="px-6 py-4 text-center">Status</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </x-slot:head>
            @forelse ($transactions as $tx)
                <tr class="hover:bg-[#E8F0F9] dark:bg-[#0F1D30] dark:hover:bg-[#1A2F49] transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-[#0F1A2A] dark:text-white">{{ $tx->created_at->format('d M Y') }}</div>
                        <div class="text-xs text-[#525252] dark:text-[#8A9BAE] font-mono">{{ $tx->created_at->format('H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-[#0F1A2A] dark:text-white/85">
                        {{ $tx->description }}
                        @if($tx->reference_id)
                            <div class="text-xs text-[#8A9BAE] dark:text-white/45 font-mono mt-1">{{ $tx->reference_id }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($tx->type === 'credit')
                            <span class="text-xs font-bold px-2 py-1 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300"><i class="fa-solid fa-arrow-down mr-1"></i> MASUK</span>
                        @else
                            <span class="text-xs font-bold px-2 py-1 bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300"><i class="fa-solid fa-arrow-up mr-1"></i> KELUAR</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-mono font-medium @if($tx->type === 'credit') text-emerald-600 dark:text-emerald-300 @else text-rose-600 dark:text-rose-300 @endif">
                        {{ $tx->type === 'credit' ? '+' : '-' }}Rp{{ number_format($tx->amount, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $statusClass = match ($tx->status) {
                                'completed' => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300',
                                'pending' => 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300',
                                'failed' => 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300',
                                default => 'bg-[#F0F6FC] dark:bg-[#0F1D30] text-[#0F1A2A] dark:text-white/85',
                            };
                            $statusLabel = strtoupper($tx->status);
                        @endphp
                        <span class="text-xs font-bold px-2 py-1 {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if ($tx->status === 'pending' && $tx->reference_id && str_starts_with($tx->reference_id, 'WLT-TOPUP-'))
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('payment.qris', ['order_id' => $tx->reference_id]) }}"
                                   class="ui-btn ui-btn-sm ui-btn-success"
                                   title="Bayar dengan QRIS Dinamis">
                                    <i class="fa-solid fa-qrcode"></i> QRIS
                                </a>
                                <a href="{{ route('payment.doku', ['order_id' => $tx->reference_id]) }}"
                                   class="ui-btn ui-btn-sm ui-btn-secondary"
                                   title="Bayar via DOKU (VA, e-Wallet, QRIS, Kartu Kredit)">
                                    <i class="fa-solid fa-credit-card"></i> DOKU
                                </a>
                            </div>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-[#8A9BAE] dark:text-white/45">Belum ada riwayat transaksi dompet.</td>
                </tr>
            @endforelse
            <x-slot:pagination>
                {{ $transactions->links() }}
            </x-slot:pagination>
        </x-ui.table>

        {{-- Modal Top Up --}}
        <dialog id="topup-modal" class="m-auto backdrop:bg-black/50 p-0 max-w-md w-full border border-[#E3EAF1] dark:border-[#2A4463] bg-white dark:bg-[#0F1D30] open:animate-in open:fade-in open:zoom-in-95 overflow-hidden">
            <form action="{{ route('user.wallet.topup') }}" method="POST">
                @csrf
                <div class="px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="ui-display text-xl leading-6" id="modal-title">
                            Top Up Ryaze Wallet
                        </h3>
                        <button type="button" onclick="document.getElementById('topup-modal').close()" class="text-[#8A9BAE] dark:text-white/45 hover:text-[#0F1A2A] dark:hover:text-[#C9D5E2] transition-colors">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                    <div class="mt-2">
                        <p class="text-sm text-[#525252] dark:text-[#8A9BAE] mb-4">Masukkan nominal top up yang Anda inginkan (Minimal Rp 10.000).</p>
                        
                        <div class="grid grid-cols-3 gap-2 mb-4">
                            @foreach([20000, 50000, 100000, 200000, 500000, 1000000] as $preset)
                                <button type="button" onclick="document.getElementById('amount_input').value = {{ $preset }}" class="ui-btn ui-btn-secondary">
                                    {{ number_format($preset/1000, 0, ',', '.') }}K
                                </button>
                            @endforeach
                        </div>
                        
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-[#525252] dark:text-[#8A9BAE] font-bold">Rp</span>
                            </div>
                            <input type="number" name="amount" id="amount_input" min="10000" step="1000" class="block w-full pl-12 pr-4 py-3 bg-[#F4F8FC] dark:bg-[#12233A] border border-[#E3EAF1] dark:border-[#2A4463] text-[#0F1A2A] dark:text-white font-bold text-lg focus:ring-2 focus:ring-[#1D5B96]/20 focus:border-[#1D5B96] transition-all outline-none" placeholder="10000" required>
                        </div>
                        <div class="mt-3 flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                            <i class="fa-solid fa-bolt"></i>
                            <span>Didukung QRIS Dinamis (Otomatis terisi nominal di m-Banking & e-Wallet).</span>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-[#F4F8FC] dark:bg-[#12233A] border-t border-[#EDF2F7] dark:border-[#2A4463] flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('topup-modal').close()" class="ui-btn ui-btn-secondary">
                        Batal
                    </button>
                    <button type="submit" class="ui-btn ui-btn-success">
                        <i class="fa-solid fa-qrcode"></i>
                        <span>Lanjut ke QRIS</span>
                    </button>
                </div>
            </form>
        </dialog>

    </x-ui.page-layout>
@endsection
