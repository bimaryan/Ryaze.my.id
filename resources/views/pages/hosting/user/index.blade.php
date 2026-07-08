@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Dashboard Hosting" 
            icon="fa-solid fa-gauge">
            <x-slot:subtitle>
                Halo, <span class="font-semibold text-indigo-600">{{ Auth::user()->name ?? 'Klien' }}</span>! Selamat datang kembali.
            </x-slot:subtitle>
            <x-slot:actions>
                <a href="{{ route('user_hosting.create') }}" class="inline-flex justify-center items-center bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    + Deploy Baru
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        {{-- Wallet & Affiliate Summary --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Ryaze Wallet --}}
            <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden flex flex-col justify-between">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <i class="fa-solid fa-wallet text-8xl"></i>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-2 text-slate-300 text-sm font-semibold mb-1">
                        <i class="fa-solid fa-wallet"></i> Ryaze Wallet
                    </div>
                    <div class="text-3xl font-black mb-4">
                        Rp {{ number_format(Auth::user()->wallet->balance ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="flex gap-3">
                        <button onclick="alert('Fitur Top Up sedang dalam pengembangan')" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold py-2 px-4 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-plus mr-1"></i> Top Up
                        </button>
                        <button onclick="alert('Fitur Riwayat sedang dalam pengembangan')" class="bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold py-2 px-4 rounded-lg transition shadow-sm">
                            Riwayat
                        </button>
                    </div>
                </div>
            </div>

            {{-- Affiliate --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-bold text-slate-800 flex items-center gap-2"><i class="fa-solid fa-users text-indigo-600"></i> Affiliate Program</h3>
                        <span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded">Aktif</span>
                    </div>
                    <p class="text-xs text-slate-500 mb-4">Ajak teman dan dapatkan komisi untuk setiap transaksi mereka.</p>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-700 mb-1">Link Referral Anda:</div>
                    <div class="flex items-center gap-2">
                        <code class="bg-slate-100 border border-slate-200 px-3 py-2 rounded-lg text-indigo-600 flex-1 break-all select-all text-xs font-mono">
                            {{ url('/register?ref=' . (Auth::user()->referral_code ?? 'RYZ-'.Auth::id())) }}
                        </code>
                        <button onclick="navigator.clipboard.writeText('{{ url('/register?ref=' . (Auth::user()->referral_code ?? 'RYZ-'.Auth::id())) }}'); alert('Link disalin!')" class="bg-slate-800 hover:bg-slate-700 text-white px-3 py-2 rounded-lg transition tooltip" title="Copy Link">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistik Dinamis --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-6">
            <x-stat-card title="Hosting Aktif" value="{{ $stats['active'] }}" icon="fa-globe" color="emerald" />
            <x-stat-card title="Tagihan Belum Lunas" value="{{ $stats['unpaid'] }}" icon="fa-file-invoice-dollar" color="rose" />
            <x-stat-card title="Tiket Bantuan" value="{{ $stats['tickets'] }}" icon="fa-headset" color="sky" />
            
            <!-- Server Health Node -->
            <div x-data="nodeHealth()" x-init="fetchStatus()" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Server Node</p>
                        <h3 class="text-xl font-bold" :class="{'text-emerald-600': data.status === 'healthy', 'text-amber-500': data.status === 'heavy_load', 'text-rose-500': error}" x-text="error ? 'Terputus' : (loading ? 'Memeriksa...' : (data.status === 'healthy' ? 'Sehat & Normal' : 'Beban Tinggi'))"></h3>
                        <p class="text-[10px] text-slate-400 mt-1" x-show="!loading && !error">CPU: <span x-text="data.cpu.load_1m + '%'"></span> | RAM: <span x-text="data.ram.percentage + '%'"></span></p>
                    </div>
                    <div class="w-12 h-12 flex items-center justify-center rounded-xl transition-colors duration-300" :class="{'bg-emerald-100 text-emerald-600': data.status === 'healthy', 'bg-amber-100 text-amber-600': data.status === 'heavy_load', 'bg-slate-100 text-slate-400': loading || error}">
                        <i class="fa-solid fa-server text-xl"></i>
                    </div>
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
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50 flex flex-wrap gap-3 justify-between items-center">
                    <h2 class="text-lg font-bold text-slate-800">Layanan Terbaru</h2>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('user_hosting.projects') }}"
                            class="text-sm text-slate-500 font-semibold hover:text-indigo-600 transition-colors">
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
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-800">
                                    <a href="https://{{ $project->ryaze_domain }}" target="_blank"
                                        class="text-indigo-600 hover:underline">
                                        {{ $project->ryaze_domain }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 uppercase">{{ $project->framework }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $colors = ['active' => 'emerald', 'building' => 'amber', 'failed' => 'rose'];
                                        $color = $colors[$project->status] ?? 'slate';
                                    @endphp
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $color }}-50 text-{{ $color }}-600 border border-{{ $color }}-200">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('user_hosting.show', $project->hashid) }}"
                                        class="text-xs bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded hover:bg-indigo-600 hover:text-white transition-colors">
                                        Kelola
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-slate-400">Belum ada project hosting.
                                </td>
                            </tr>
            @endforelse
        </x-ui.table>
    </x-ui.page-layout>
@endsection
