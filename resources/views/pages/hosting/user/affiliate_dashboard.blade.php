@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- Header --}}
        <x-ui.page-header 
            title="Affiliate Dashboard" 
            eyebrow="Program Referral"
            status="AKTIF"
            subtitle="Pantau performa program afiliasi dan komisi Anda." 
            icon="fa-users" 
            iconColor="emerald">
            <x-slot:actions>
                <a href="{{ route('user.wallet.history') }}"
                    class="ui-btn ui-btn-secondary px-5 py-2.5 text-sm font-bold">
                    <i class="fa-solid fa-wallet mr-2"></i> Cek Wallet
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        {{-- Statistik --}}
        <div class="ui-grid grid-cols-1 md:grid-cols-2 mb-6">
            {{-- Total Referrals --}}
            <div class="ui-cell p-6 relative overflow-hidden">
                <div class="absolute right-0 top-0 p-3 opacity-10 text-[#0F1A2A] dark:text-white pointer-events-none">
                    <i class="fa-solid fa-user-plus text-7xl"></i>
                </div>
                <div class="relative z-10 flex items-center justify-between gap-4">
                    <div>
                        <p class="ui-eyebrow-muted mb-1.5">Total Undangan</p>
                        <div class="ui-stat-value">{{ number_format($totalReferrals, 0, ',', '.') }} <span class="text-sm font-medium text-[#8A9BAE] dark:text-white/45">Pengguna</span></div>
                    </div>
                    <div class="ui-icon-box w-12 h-12 text-xl shrink-0">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                </div>
            </div>

            {{-- Total Commission --}}
            <div class="ui-cell-alt p-6 relative overflow-hidden">
                <div class="absolute right-0 top-0 p-3 opacity-10 text-[#0F1A2A] dark:text-white pointer-events-none">
                    <i class="fa-solid fa-money-bill-trend-up text-7xl"></i>
                </div>
                <div class="relative z-10 flex items-center justify-between gap-4">
                    <div>
                        <p class="ui-eyebrow-muted mb-1.5">Total Pendapatan Komisi</p>
                        <div class="ui-stat-value text-emerald-600 dark:text-emerald-400">Rp {{ number_format($totalCommission, 0, ',', '.') }}</div>
                    </div>
                    <div class="ui-icon-box w-12 h-12 text-xl shrink-0">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Referral Link --}}
        <div class="ui-panel mb-6 overflow-hidden">
            <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                <h2 class="font-mono text-[11px] font-semibold uppercase tracking-[0.2em] text-[#0F1A2A] dark:text-white">Link Referral Anda</h2>
                <span class="ui-status"><span class="ui-status-dot"></span>AKTIF</span>
            </div>
            <div class="p-6">
                <p class="text-sm text-[#525252] dark:text-[#8A9BAE] mb-4">Bagikan link ini ke teman Anda. Dapatkan komisi sebesar 10% setiap kali mereka melakukan transaksi pertama maupun tagihan bulanan!</p>

                <div class="flex items-center gap-3">
                    <code class="bg-[#F4F8FC] dark:bg-[#0A1322] border border-[#E3EAF1] dark:border-[#2A4463] px-4 py-3 text-[#1D5B96] dark:text-[#6BA8DC] flex-1 break-all select-all text-sm font-mono font-medium">
                        {{ url('/register?ref=' . ($user->referral_code ?? 'RYZ-'.$user->id)) }}
                    </code>
                    <button onclick="navigator.clipboard.writeText('{{ url('/register?ref=' . ($user->referral_code ?? 'RYZ-'.$user->id)) }}'); hotToast('Link referral disalin ke clipboard!', 'success')"
                        class="ui-btn ui-btn-primary px-5 py-3 flex-shrink-0 text-sm font-bold">
                        <i class="fa-regular fa-copy mr-1"></i> Copy
                    </button>
                </div>
            </div>
        </div>

        {{-- Commission History Table --}}
        <x-ui.table>
            <x-slot:header>
                <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                    <h2 class="font-mono text-[11px] font-semibold uppercase tracking-[0.2em] text-[#0F1A2A] dark:text-white">Riwayat Komisi</h2>
                    <a href="{{ route('user.wallet.history') }}" class="ui-arrow-link">
                        Cek Wallet <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </x-slot:header>
            <x-slot:head>
                <th class="px-6 py-4">Tanggal</th>
                <th class="px-6 py-4">Pengguna (Ref)</th>
                <th class="px-6 py-4">Keterangan</th>
                <th class="px-6 py-4">Komisi</th>
                <th class="px-6 py-4 text-center">Status</th>
            </x-slot:head>
            @forelse ($commissions as $comm)
                <tr class="hover:bg-[#E8F0F9] dark:bg-[#0F1D30] dark:hover:bg-[#1A2F49] transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-[#0F1A2A] dark:text-white">{{ $comm->created_at->format('d M Y') }}</div>
                        <div class="text-xs text-[#525252] dark:text-[#8A9BAE] font-mono">{{ $comm->created_at->format('H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 font-semibold text-[#0F1A2A] dark:text-white/85">
                        {{ $comm->referredUser->name ?? 'User Terhapus' }}
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-[#525252] dark:text-white/70">
                        {{ $comm->description }}
                    </td>
                    <td class="px-6 py-4 font-mono font-bold text-emerald-600 dark:text-emerald-300">
                        +Rp{{ number_format($comm->amount, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $statusClass = match ($comm->status) {
                                'paid' => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300',
                                'pending' => 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300',
                                'cancelled' => 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300',
                                default => 'bg-[#F0F6FC] dark:bg-[#0F1D30] text-[#0F1A2A] dark:text-white/85',
                            };
                            $statusLabel = strtoupper($comm->status);
                        @endphp
                        <span class="text-xs font-bold px-2 py-1 {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-[#8A9BAE] dark:text-white/45">Belum ada komisi yang masuk. Yuk mulai ajak teman!</td>
                </tr>
            @endforelse
            <x-slot:pagination>
                {{ $commissions->links() }}
            </x-slot:pagination>
        </x-ui.table>

    </x-ui.page-layout>
@endsection
