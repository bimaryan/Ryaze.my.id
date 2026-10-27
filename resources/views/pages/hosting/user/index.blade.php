@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Dashboard Hosting" 
            icon="fa-solid fa-gauge">
            <x-slot:subtitle>
                Halo, <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">{{ Auth::user()->name ?? 'Klien' }}</span>! Selamat datang kembali.
            </x-slot:subtitle>
        </x-ui.page-header>

        {{-- Promo Event Banner --}}
        <x-ui.promo-banner class="mt-6 max-w-4xl mx-auto shadow-sm" />

        {{-- ⚠️ EXPIRED HOSTING BANNER --}}
        @if($expiredBilling)
        <div class="mt-6 rounded-2xl border border-red-200 dark:border-red-500/40 bg-red-50 dark:bg-red-500/10 p-5 flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="w-12 h-12 flex-shrink-0 rounded-xl bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 flex items-center justify-center text-xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-bold text-red-700 dark:text-red-300 text-sm">Langganan Hosting Anda Telah Berakhir!</h3>
                <p class="text-xs text-red-600 dark:text-red-400 mt-0.5">
                    Paket <strong>{{ ucfirst($expiredBilling->plan) }}</strong> Anda kadaluarsa pada 
                    <strong>{{ \Carbon\Carbon::parse($expiredBilling->next_due_date)->translatedFormat('d F Y') }}</strong>.
                    Semua website yang ter-deploy telah disuspend. Perpanjang sekarang untuk mengaktifkan kembali.
                </p>
            </div>
            <a href="{{ route('user_hosting.subscription') }}" 
               class="flex-shrink-0 inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition shadow-sm whitespace-nowrap">
                <i class="fa-solid fa-rotate-right"></i> Perpanjang Sekarang
            </a>
        </div>
        @elseif($activeBilling && $activeBilling->plan !== 'free')
            @php $daysLeft = now()->diffInDays(\Carbon\Carbon::parse($activeBilling->next_due_date), false); @endphp
            @if($daysLeft <= 7 && $daysLeft >= 0)
            <div class="mt-6 rounded-2xl border border-amber-200 dark:border-amber-500/40 bg-amber-50 dark:bg-amber-500/10 p-5 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="w-12 h-12 flex-shrink-0 rounded-xl bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-amber-700 dark:text-amber-300 text-sm">Langganan Segera Berakhir</h3>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">
                        Paket <strong>{{ ucfirst($activeBilling->plan) }}</strong> Anda akan berakhir dalam 
                        <strong>{{ $daysLeft }} hari</strong> 
                        ({{ \Carbon\Carbon::parse($activeBilling->next_due_date)->translatedFormat('d F Y') }}).
                        Perpanjang sekarang agar layanan tidak terganggu.
                    </p>
                </div>
                <a href="{{ route('user_hosting.subscription') }}" 
                   class="flex-shrink-0 inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition shadow-sm whitespace-nowrap">
                    <i class="fa-solid fa-rotate-right"></i> Perpanjang
                </a>
            </div>
            @endif
        @endif

        {{-- Wallet & Affiliate Summary --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Ryaze Wallet --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5 sm:p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20 flex items-center justify-center">
                            <i class="fa-solid fa-wallet text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Ryaze Wallet</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Saldo aktif akun Anda</p>
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight mb-1">
                        Rp {{ number_format(Auth::user()->wallet->balance ?? 0, 0, ',', '.') }}
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">Saldo aktif yang dapat digunakan untuk transaksi otomatis & pembayaran.</p>
                </div>
                <div class="flex items-center gap-2.5 mt-auto">
                    <a href="{{ route('user.wallet.history') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold py-2.5 px-3 rounded-xl shadow-sm flex-1 text-center flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-plus text-xs"></i> Top Up
                    </a>
                    <a href="{{ route('user.wallet.withdraw') }}" class="bg-amber-50 text-amber-700 border border-amber-200/80 hover:bg-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/30 dark:hover:bg-amber-500/20 text-xs font-semibold py-2.5 px-3 rounded-xl shadow-sm flex-1 text-center flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-money-bill-transfer text-xs"></i> Tarik
                    </a>
                    <a href="{{ route('user.wallet.history') }}" class="bg-slate-100 text-slate-700 border border-slate-200/80 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 dark:hover:bg-slate-700 text-xs font-semibold py-2.5 px-3 rounded-xl shadow-sm flex-1 text-center flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-history text-xs"></i> Riwayat
                    </a>
                </div>
            </div>

            {{-- Affiliate --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5 sm:p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 flex items-center justify-center">
                                <i class="fa-solid fa-users text-base"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Affiliate Program</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Komisi referral pengguna</p>
                            </div>
                        </div>
                        <span class="bg-emerald-50 text-emerald-700 border border-emerald-200/80 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30 text-xs font-semibold px-2.5 py-1 rounded-md">Aktif</span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-5">Ajak teman dan dapatkan komisi untuk setiap transaksi & langganan mereka.</p>
                </div>
                <div class="mt-auto">
                    <div class="text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Link Referral Anda:</div>
                    <div class="flex items-center gap-2 mb-3">
                        <code class="bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700 px-3.5 py-2.5 rounded-xl text-indigo-600 dark:text-indigo-400 flex-1 break-all select-all text-xs font-mono font-medium">
                            {{ url('/register?ref=' . (Auth::user()->referral_code ?? 'RYZ-'.Auth::id())) }}
                        </code>
                        <button onclick="navigator.clipboard.writeText('{{ url('/register?ref=' . (Auth::user()->referral_code ?? 'RYZ-'.Auth::id())) }}'); hotToast('Link referral disalin ke clipboard!', 'success')" class="bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white px-4 py-2.5 rounded-xl border border-transparent dark:border-slate-700 shadow-sm flex-shrink-0" title="Copy Link">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                    <a href="{{ route('user.affiliate.dashboard') }}" class="block w-full bg-emerald-50 text-emerald-700 border border-emerald-200/80 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30 dark:hover:bg-emerald-500/20 text-xs font-semibold py-2.5 px-5 rounded-xl shadow-sm text-center flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-chart-line text-xs"></i> Buka Dashboard Affiliate
                    </a>
                </div>
            </div>
        </div>

        {{-- Statistik Dinamis --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-6">
            <x-stat-card title="Hosting Aktif" value="{{ $stats['active'] }}" icon="fa-globe" color="emerald" subtitle="Layanan berjalan normal" />
            <x-stat-card title="Tagihan Belum Lunas" value="{{ $stats['unpaid'] }}" icon="fa-file-invoice-dollar" color="rose" subtitle="{{ $stats['unpaid'] > 0 ? 'Perlu diselesaikan' : 'Semua tagihan lunas' }}" />
            <x-stat-card title="Tiket Bantuan" value="{{ $stats['tickets'] }}" icon="fa-headset" color="sky" subtitle="{{ $stats['tickets'] > 0 ? 'Menunggu tanggapan' : 'Tidak ada tiket aktif' }}" />
            
            <!-- Server Health Node -->
            <div x-data="nodeHealth()" x-init="fetchStatus()" class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4 transition-colors hover:border-emerald-300 dark:hover:border-emerald-500/40">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 border transition-colors"
                    :class="{
                        'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20': data.status === 'healthy',
                        'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20': data.status === 'heavy_load',
                        'bg-slate-50 text-slate-500 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700': loading || error
                    }">
                    <i class="fa-solid fa-server text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5 truncate">Server Node</p>
                    <h3 class="text-xl sm:text-2xl font-bold tracking-tight truncate"
                        :class="{
                            'text-emerald-600 dark:text-emerald-400': data.status === 'healthy',
                            'text-amber-600 dark:text-amber-400': data.status === 'heavy_load',
                            'text-rose-600 dark:text-rose-400': error,
                            'text-slate-900 dark:text-white': loading
                        }"
                        x-text="error ? 'Terputus' : (loading ? 'Memeriksa...' : (data.status === 'healthy' ? 'Sehat & Normal' : 'Beban Tinggi'))">
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate mt-1" x-show="!loading && !error">
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
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex flex-wrap gap-3 justify-between items-center">
                    <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Layanan Terbaru</h2>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('user_hosting.projects') }}"
                            class="text-sm text-slate-500 dark:text-slate-400 font-semibold hover:text-indigo-600 dark:hover:text-indigo-400">
                            Lihat Semua <i class="fa-solid fa-arrow-right text-xs ml-1"></i>
                        </a>
                    </div>
                </div>
            </x-slot:header>
            <x-slot:head>
                <th class="px-6 py-4">Domain/Project</th>
                <th class="px-6 py-4">Framework</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </x-slot:head>
            @forelse ($projects as $project)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50">
                                <td class="px-6 py-4 font-medium text-slate-800 dark:text-slate-100">
                                    @php
                                        $activeDomain = $project->domains()->where('ssl_status', 'active')->first();
                                        $displayUrl = $activeDomain ? $activeDomain->domain_name : $project->ryaze_domain;
                                    @endphp
                                    <a href="https://{{ $displayUrl }}" target="_blank"
                                        class="text-xs font-medium text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1.5 mt-1">
                                        <i class="fa-solid fa-link text-[10px]"></i> {{ $displayUrl }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 uppercase">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200/80 dark:border-slate-700">
                                        {{ $project->framework }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $colors = [
                                            'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
                                            'building' => 'bg-amber-50 text-amber-700 border-amber-200/80 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
                                            'failed' => 'bg-rose-50 text-rose-700 border-rose-200/80 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20'
                                        ];
                                        $badgeColor = $colors[$project->status] ?? 'bg-slate-100 text-slate-700 border-slate-200/80 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700';
                                    @endphp
                                    <span
                                        class="px-2.5 py-1 rounded-md text-xs font-medium border {{ $badgeColor }}">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('user_hosting.show', $project->hashid) }}"
                                        class="text-xs bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 px-3 py-1.5 rounded-lg border border-indigo-100 dark:border-indigo-500/20 hover:bg-indigo-600 hover:text-white">
                                        Kelola
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-slate-400 dark:text-slate-500">Belum ada project hosting.
                                </td>
                            </tr>
            @endforelse
        </x-ui.table>
    </x-ui.page-layout>
@endsection
