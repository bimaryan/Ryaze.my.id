@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Dashboard Hosting" 
            eyebrow="Panel Klien · Ringkasan Layanan"
            status="ONLINE"
            icon="fa-solid fa-gauge">
            <x-slot:subtitle>
                Halo, <span class="font-semibold text-[#1D5B96] dark:text-[#6BA8DC]">{{ Auth::user()->name ?? 'Klien' }}</span>! Selamat datang kembali di panel hosting Ryaze.
            </x-slot:subtitle>
            <x-slot:actions>
                <a href="{{ route('user_hosting.create') }}" class="ui-btn-primary">
                    <i class="fa-solid fa-plus text-xs"></i> Deploy Proyek
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        {{-- Promo Event Banner --}}
        <x-ui.promo-banner class="mt-6 max-w-4xl mx-auto shadow-none" />

        {{-- ⚠️ EXPIRED HOSTING BANNER --}}
        @if($expiredBilling)
        <div class="mt-6 border border-rose-300 dark:border-rose-800 bg-rose-50 dark:bg-rose-950/40 p-5 flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="w-12 h-12 flex-shrink-0 bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl border border-rose-200 dark:border-rose-800/60">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-bold text-rose-800 dark:text-rose-300 text-sm">Langganan Hosting Anda Telah Berakhir!</h3>
                <p class="text-xs text-rose-600 dark:text-rose-400 mt-0.5">
                    Paket <strong>{{ ucfirst($expiredBilling->plan) }}</strong> Anda kadaluarsa pada 
                    <strong>{{ \Carbon\Carbon::parse($expiredBilling->next_due_date)->translatedFormat('d F Y') }}</strong>.
                    Semua website yang ter-deploy telah disuspend. Perpanjang sekarang untuk mengaktifkan kembali.
                </p>
            </div>
            <a href="{{ route('user_hosting.subscription') }}" 
               class="ui-btn ui-btn-danger shrink-0 whitespace-nowrap">
                <i class="fa-solid fa-rotate-right"></i> Perpanjang Sekarang
            </a>
        </div>
        @elseif($activeBilling && $activeBilling->plan !== 'free')
            @php $daysLeft = now()->diffInDays(\Carbon\Carbon::parse($activeBilling->next_due_date), false); @endphp
            @if($daysLeft <= 7 && $daysLeft >= 0)
            <div class="mt-6 border border-amber-300 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/40 p-5 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="w-12 h-12 flex-shrink-0 bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl border border-amber-200 dark:border-amber-800/60">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-amber-800 dark:text-amber-300 text-sm">Langganan Segera Berakhir</h3>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">
                        Paket <strong>{{ ucfirst($activeBilling->plan) }}</strong> Anda akan berakhir dalam 
                        <strong>{{ $daysLeft }} hari</strong> 
                        ({{ \Carbon\Carbon::parse($activeBilling->next_due_date)->translatedFormat('d F Y') }}).
                        Perpanjang sekarang agar layanan tidak terganggu.
                    </p>
                </div>
                <a href="{{ route('user_hosting.subscription') }}" 
                   class="ui-btn shrink-0 whitespace-nowrap bg-amber-600 text-white hover:bg-amber-700">
                    <i class="fa-solid fa-rotate-right"></i> Perpanjang
                </a>
            </div>
            @endif
        @endif

        {{-- Wallet & Affiliate Summary --}}
        <div class="mt-6 ui-grid grid-cols-1 md:grid-cols-2">
            {{-- Ryaze Wallet --}}
            <div class="ui-cell p-5 sm:p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-[#12233A] text-white border border-[#1D5B96] flex items-center justify-center">
                            <i class="fa-solid fa-wallet text-sm text-[#6BA8DC]"></i>
                        </div>
                        <div>
                            <span class="font-mono text-[9px] uppercase tracking-[0.2em] text-[#8A9BAE] dark:text-white/40 block">Saldo Akun</span>
                            <h3 class="ui-eyebrow text-[11px] text-base">Ryaze Wallet</h3>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-[#0F1A2A] dark:text-white font-mono tracking-tight mb-1">
                        Rp {{ number_format(Auth::user()->wallet->balance ?? 0, 0, ',', '.') }}
                    </div>
                    <p class="text-xs text-[#525252] dark:text-white/50 mb-6">Saldo aktif yang dapat digunakan untuk transaksi otomatis, tagihan hosting & perpanjangan domain.</p>
                </div>
                <div class="flex items-center gap-2.5 mt-auto">
                    <a href="{{ route('user.wallet.history') }}" class="ui-btn ui-btn-primary flex-1">
                        <i class="fa-solid fa-plus text-xs"></i> Top Up
                    </a>
                    <a href="{{ route('user.wallet.withdraw') }}" class="ui-btn ui-btn-secondary flex-1">
                        <i class="fa-solid fa-money-bill-transfer text-xs"></i> Tarik
                    </a>
                    <a href="{{ route('user.wallet.history') }}" class="ui-btn ui-btn-secondary flex-1">
                        <i class="fa-solid fa-history text-xs"></i> Riwayat
                    </a>
                </div>
            </div>

            {{-- Affiliate --}}
            <div class="ui-cell p-5 sm:p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#12233A] text-white border border-[#1D5B96] flex items-center justify-center">
                                <i class="fa-solid fa-users text-sm text-[#6BA8DC]"></i>
                            </div>
                            <div>
                                <span class="font-mono text-[9px] uppercase tracking-[0.2em] text-[#8A9BAE] dark:text-white/40 block">Program Referral</span>
                                <h3 class="ui-eyebrow text-[11px] text-base">Affiliate Program</h3>
                            </div>
                        </div>
                        <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/50 text-[10px] font-mono uppercase tracking-wider px-2.5 py-1">Aktif</span>
                    </div>
                    <p class="text-xs text-[#525252] dark:text-white/50 mb-5">Ajak teman dan dapatkan komisi untuk setiap transaksi & langganan mereka.</p>
                </div>
                <div class="mt-auto">
                    <div class="font-mono text-[10px] uppercase tracking-wider text-[#8A9BAE] dark:text-white/40 mb-2">Link Referral Anda:</div>
                    <div class="flex items-center gap-2 mb-3">
                        <code class="bg-[#F4F8FC] dark:bg-[#0A1322] border border-[#E3EAF1] dark:border-[#2A4463] px-3.5 py-2 text-[#1D5B96] dark:text-[#6BA8DC] flex-1 break-all select-all text-xs font-mono font-medium">
                            {{ url('/register?ref=' . (Auth::user()->referral_code ?? 'RYZ-'.Auth::id())) }}
                        </code>
                        <button onclick="navigator.clipboard.writeText('{{ url('/register?ref=' . (Auth::user()->referral_code ?? 'RYZ-'.Auth::id())) }}'); hotToast('Link referral disalin ke clipboard!', 'success')" class="ui-btn ui-btn-secondary shrink-0" title="Copy Link">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                    <a href="{{ route('user.affiliate.dashboard') }}" class="ui-btn ui-btn-secondary w-full">
                        <i class="fa-solid fa-chart-line text-xs"></i> Buka Dashboard Affiliate
                    </a>
                </div>
            </div>
        </div>

        {{-- Statistik Dinamis --}}
        <div class="mt-6 ui-grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-card title="Hosting Aktif" value="{{ $stats['active'] }}" icon="fa-globe" color="emerald" subtitle="Layanan berjalan normal" />
            <x-stat-card title="Tagihan Belum Lunas" value="{{ $stats['unpaid'] }}" icon="fa-file-invoice-dollar" color="rose" subtitle="{{ $stats['unpaid'] > 0 ? 'Perlu diselesaikan' : 'Semua tagihan lunas' }}" />
            <x-stat-card title="Tiket Bantuan" value="{{ $stats['tickets'] }}" icon="fa-headset" color="sky" subtitle="{{ $stats['tickets'] > 0 ? 'Menunggu tanggapan' : 'Tidak ada tiket aktif' }}" />
            
            <!-- Server Health Node -->
            <div x-data="nodeHealth()" x-init="fetchStatus()" class="ui-cell p-5 flex items-center gap-4 transition-colors hover:bg-[#E8F0F9] dark:hover:bg-[#1A2F49]">
                <div class="w-12 h-12 flex items-center justify-center shrink-0 border transition-colors"
                    :class="{
                        'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/50': data.status === 'healthy',
                        'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/50': data.status === 'heavy_load',
                        'bg-[#F0F6FC] text-[#525252] border-[#E3EAF1] dark:bg-[#12233A] dark:text-white/40 dark:border-[#2A4463]': loading || error
                    }">
                    <i class="fa-solid fa-server text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-[#8A9BAE] dark:text-white/40 mb-1 truncate">Server Node</p>
                    <h3 class="text-xl sm:text-2xl font-black font-mono tracking-tight truncate"
                        :class="{
                            'text-emerald-600 dark:text-emerald-400': data.status === 'healthy',
                            'text-amber-600 dark:text-amber-400': data.status === 'heavy_load',
                            'text-rose-600 dark:text-rose-400': error,
                            'text-[#0F1A2A] dark:text-white': loading
                        }"
                        x-text="error ? 'Terputus' : (loading ? 'Memeriksa...' : (data.status === 'healthy' ? 'Sehat & Normal' : 'Beban Tinggi'))">
                    </h3>
                    <p class="font-mono text-[10px] text-[#8A9BAE] dark:text-white/40 truncate mt-1" x-show="!loading && !error">
                        CPU: <span x-text="data.cpu.load_1m + '%'"></span> | RAM: <span x-text="data.ram.percentage + '%'"></span>
                    </p>
                </div>
            </div>
        </div>

        <script nonce="{{ csp_nonce() }}">
        document.addEventListener('alpine:init', () => {
            Alpine.data('nodeHealth', () => ({
                loading: true,
                error: false,
                data: {
                    status: 'unknown',
                    cpu: { load_1m: 0 },
                    ram: { percentage: 0 }
                },
                fetchStatus() {
                    fetch('{{ route("user_hosting.server_status") }}')
                        .then(res => {
                            if(!res.ok) throw new Error('Network error');
                            return res.json();
                        })
                        .then(data => {
                            this.data = data;
                            this.loading = false;
                        })
                        .catch(err => {
                            console.error('Error fetching node status:', err);
                            this.error = true;
                            this.loading = false;
                        });
                }
            }));
        });
        </script>

        {{-- Tabel Layanan --}}
        <x-ui.table class="mt-8">
            <x-slot:header>
                <div class="ui-panel-head px-6 py-5 bg-[#F4F8FC] dark:bg-[#12233A]">
                    <h2 class="ui-eyebrow text-[11px] text-sm  font-mono uppercase tracking-[0.2em]">Layanan Terbaru</h2>
                    <a href="{{ route('user_hosting.projects') }}" class="ui-arrow-link">
                        Lihat Semua <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </x-slot:header>
            <x-slot:head>
                <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-wider">Domain / Project</th>
                <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-wider">Framework</th>
                <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-wider text-center">Aksi</th>
            </x-slot:head>
            @forelse ($projects as $project)
                <tr class="hover:bg-[#E8F0F9] dark:hover:bg-[#1A2F49] transition-colors">
                    <td class="px-6 py-4 font-medium text-[#0F1A2A] dark:text-white">
                        @php
                            $activeDomain = $project->domains()->where('ssl_status', 'active')->first();
                            $displayUrl = $activeDomain ? $activeDomain->domain_name : $project->ryaze_domain;
                        @endphp
                        <span class="font-bold text-sm block">{{ $project->name }}</span>
                        <a href="https://{{ $displayUrl }}" target="_blank"
                            class="text-xs font-mono text-[#8A9BAE] hover:text-[#1D5B96] dark:text-white/40 dark:hover:text-[#6BA8DC] inline-flex items-center gap-1.5 mt-0.5">
                            <i class="fa-solid fa-link text-[10px]"></i> {{ $displayUrl }}
                        </a>
                    </td>
                    <td class="px-6 py-4 uppercase font-mono text-xs">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-[#F0F6FC] dark:bg-[#12233A] text-[#1D5B96] dark:text-[#6BA8DC] border border-[#E3EAF1] dark:border-[#2A4463]">
                            {{ $project->framework }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $colors = [
                                'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/50',
                                'building' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/50',
                                'failed' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800/50'
                            ];
                            $badgeColor = $colors[$project->status] ?? 'bg-[#F0F6FC] text-[#525252] border-[#E3EAF1] dark:bg-[#12233A] dark:text-white/60 dark:border-[#2A4463]';
                        @endphp
                        <span class="px-2.5 py-1 text-[10px] font-mono uppercase tracking-wider font-semibold border {{ $badgeColor }}">
                            {{ ucfirst($project->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('user_hosting.show', $project->hashid) }}"
                            class="font-mono text-[10px] uppercase tracking-wider bg-[#12233A] hover:bg-[#1D5B96] text-white border border-[#2A4463] hover:border-[#1D5B96] px-3.5 py-1.5 inline-flex items-center gap-1 transition-colors">
                            Kelola <i class="fa-solid fa-arrow-right text-[9px]"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center font-mono text-xs text-[#8A9BAE] dark:text-white/40">
                        Belum ada project hosting aktif.
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.page-layout>
@endsection
