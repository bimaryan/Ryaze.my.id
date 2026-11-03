@extends('index')

@section('content')
    <x-ui.page-layout>

        <!-- Header -->
        <x-ui.page-header title="Dashboard" eyebrow="Superadmin · Ringkasan" />

        <div class="mt-6 space-y-6">

            <!-- Main KPI Cards -->
            <div class="ui-grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Total Users -->
                <div class="ui-cell p-5 flex items-center gap-4">
                    <div class="w-12 h-12 flex items-center justify-center shrink-0 bg-[#E8F0F9] text-[#1D5B96] border border-[#C9D5E2] dark:bg-[#1D5B96]/10 dark:text-[#6BA8DC] dark:border-[#1B2C44]">
                        <i class="fa-solid fa-users text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="ui-stat-label mb-1 truncate">Total Pengguna</p>
                        <h3 class="ui-stat-value">{{ number_format($totalUsers) }}</h3>
                        <p class="text-[11px] text-[#737373] dark:text-white/40 font-medium flex items-center gap-1.5 mt-1.5 truncate">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{ number_format($activeUsers) }} aktif
                            <span class="text-[#C9D5E2] dark:text-white/20">•</span>
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-rose-500"></span> {{ number_format($inactiveUsers) }} nonaktif
                        </p>
                    </div>
                </div>

                <!-- Joki Orders -->
                <div class="ui-cell p-5 flex items-center gap-4">
                    <div class="w-12 h-12 flex items-center justify-center shrink-0 bg-[#E8F0F9] text-[#1D5B96] border border-[#C9D5E2] dark:bg-[#1D5B96]/10 dark:text-[#6BA8DC] dark:border-[#1B2C44]">
                        <i class="fa-solid fa-code-branch text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="ui-stat-label mb-1 truncate">Pesanan Joki Aktif</p>
                        <div class="flex items-baseline gap-1.5">
                            <h3 class="ui-stat-value">{{ number_format($activeJokiOrders) }}</h3>
                            <span class="text-xs text-[#94A3B4] dark:text-white/35 font-mono">/ {{ number_format($totalJokiOrders) }} total</span>
                        </div>
                        <p class="text-[11px] text-[#737373] dark:text-white/40 font-medium flex items-center gap-1.5 mt-1.5 truncate">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#1D5B96]"></span> Dalam pengerjaan
                        </p>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="ui-cell p-5 flex items-center gap-4">
                    <div class="w-12 h-12 flex items-center justify-center shrink-0 bg-[#E8F0F9] text-[#1D5B96] border border-[#C9D5E2] dark:bg-[#1D5B96]/10 dark:text-[#6BA8DC] dark:border-[#1B2C44]">
                        <i class="fa-solid fa-wallet text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2">
                            <p class="ui-stat-label mb-1 truncate">Pendapatan Bulan Ini</p>
                            <a href="{{ route('superadmin.finance') }}" class="ui-arrow-link shrink-0" title="Lihat Laporan Keuangan">
                                Detail <i class="fa-solid fa-arrow-right text-[9px]"></i>
                            </a>
                        </div>
                        <h3 class="ui-stat-value truncate">
                            Rp {{ number_format($totalRevenueMonth, 0, ',', '.') }}
                        </h3>
                        <p class="text-[11px] text-[#737373] dark:text-white/40 font-mono truncate mt-1.5">
                            Joki: Rp {{ number_format($jokiRevenueMonth, 0, ',', '.') }} • Hosting: Rp {{ number_format($hostingRevenueMonth, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <!-- Hosting Projects -->
                <div class="ui-cell p-5 flex items-center gap-4">
                    <div class="w-12 h-12 flex items-center justify-center shrink-0 bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20">
                        <i class="fa-solid fa-server text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="ui-stat-label mb-1 truncate">Hosting Aktif</p>
                        <div class="flex items-baseline gap-1.5">
                            <h3 class="ui-stat-value">{{ number_format($activeHosting) }}</h3>
                            <span class="text-xs text-[#94A3B4] dark:text-white/35 font-mono">/ {{ number_format($totalHosting) }} total</span>
                        </div>
                        <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1.5 mt-1.5 truncate">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Layanan berjalan normal
                        </p>
                    </div>
                </div>

                <!-- Databases -->
                <div class="ui-cell p-5 flex items-center gap-4">
                    <div class="w-12 h-12 flex items-center justify-center shrink-0 bg-amber-50 text-amber-600 border border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20">
                        <i class="fa-solid fa-database text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="ui-stat-label mb-1 truncate">Total Database</p>
                        <h3 class="ui-stat-value">{{ number_format($totalDatabases) }}</h3>
                        <p class="text-[11px] text-[#737373] dark:text-white/40 font-medium flex items-center gap-1.5 mt-1.5 truncate">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500"></span> MySQL & NoSQL aktif
                        </p>
                    </div>
                </div>

                <!-- Storage -->
                <div class="ui-cell p-5 flex items-center gap-4">
                    <div class="w-12 h-12 flex items-center justify-center shrink-0 bg-[#E8F0F9] text-[#1D5B96] border border-[#C9D5E2] dark:bg-[#1D5B96]/10 dark:text-[#6BA8DC] dark:border-[#1B2C44]">
                        <i class="fa-solid fa-hard-drive text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="ui-stat-label mb-1 truncate">Penyimpanan Teralokasi</p>
                        <div class="flex items-baseline gap-1.5">
                            <h3 class="ui-stat-value">
                                {{ $totalStorageMB >= 1024 ? number_format($totalStorageMB / 1024, 1) : number_format($totalStorageMB) }}
                            </h3>
                            <span class="text-xs text-[#94A3B4] dark:text-white/35 font-mono font-medium">{{ $totalStorageMB >= 1024 ? 'GB' : 'MB' }}</span>
                        </div>
                        <p class="text-[11px] text-[#737373] dark:text-white/40 font-medium flex items-center gap-1.5 mt-1.5 truncate">
                            <span>Limit alokasi penyimpanan</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Server Health Status -->
            <div x-data="serverHealth()" x-init="startMonitoring()">
                <div class="ui-panel-head flex-wrap gap-4">
                    <p class="ui-modal-title">
                        Status Kesehatan Server
                    </p>
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-3 w-3">
                            <span :class="{'bg-emerald-400': !loading && !error, 'bg-rose-400': error, 'bg-[#94A3B4]': loading}" class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"></span>
                            <span :class="{'bg-emerald-500': !loading && !error, 'bg-rose-500': error, 'bg-[#94A3B4]': loading}" class="relative inline-flex rounded-full h-3 w-3"></span>
                        </span>
                        <span class="text-xs font-bold font-mono uppercase tracking-[0.15em] text-[#525252] dark:text-white/60" x-text="error ? 'Terputus' : (loading ? 'Menghubungkan...' : 'Online')"></span>
                    </div>
                </div>

                <div class="ui-grid grid-cols-1 md:grid-cols-5">
                    <!-- CPU -->
                    <div class="ui-cell p-4">
                        <p class="ui-eyebrow-muted mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-microchip"></i> CPU Load (1m)
                        </p>
                        <div class="flex items-end gap-2">
                            <h4 class="text-2xl font-black tracking-[-0.03em]" :class="{'text-rose-600 dark:text-rose-400': data.cpu.load_1m > 80, 'text-amber-600 dark:text-amber-400': data.cpu.load_1m > 50, 'text-[#0F1A2A] dark:text-white': data.cpu.load_1m <= 50}" x-text="data.cpu.load_1m + '%'"></h4>
                        </div>
                        <!-- Progress Bar -->
                        <div class="w-full bg-[#E3EAF1] dark:bg-[#1B2C44] rounded-full h-1.5 mt-3">
                            <div class="h-1.5 rounded-full transition-all duration-1000" :class="{'bg-rose-500': data.cpu.load_1m > 80, 'bg-amber-500': data.cpu.load_1m > 50, 'bg-emerald-500': data.cpu.load_1m <= 50}" :style="`width: ${Math.min(data.cpu.load_1m, 100)}%`"></div>
                        </div>
                    </div>

                    <!-- RAM -->
                    <div class="ui-cell p-4">
                        <p class="ui-eyebrow-muted mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-memory"></i> RAM Usage
                        </p>
                        <div class="flex items-end gap-2">
                            <h4 class="text-2xl font-black tracking-[-0.03em] text-[#0F1A2A] dark:text-white" x-text="data.ram.percentage + '%'"></h4>
                            <span class="text-xs font-semibold font-mono text-[#94A3B4] dark:text-white/35 mb-1" x-text="`${Math.round(data.ram.used_mb/1024)}GB / ${Math.round(data.ram.total_mb/1024)}GB`"></span>
                        </div>
                        <!-- Progress Bar -->
                        <div class="w-full bg-[#E3EAF1] dark:bg-[#1B2C44] rounded-full h-1.5 mt-3">
                            <div class="h-1.5 rounded-full transition-all duration-1000" :class="{'bg-rose-500': data.ram.percentage > 85, 'bg-amber-500': data.ram.percentage > 70, 'bg-emerald-500': data.ram.percentage <= 70}" :style="`width: ${data.ram.percentage}%`"></div>
                        </div>
                    </div>

                    <!-- DISK (OS) -->
                    <div class="ui-cell p-4">
                        <p class="ui-eyebrow-muted mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-hard-drive"></i> Disk (OS)
                        </p>
                        <div class="flex items-end gap-2">
                            <h4 class="text-2xl font-black tracking-[-0.03em] text-[#0F1A2A] dark:text-white" x-text="data.disk.percentage + '%'"></h4>
                            <span class="text-xs font-semibold font-mono text-[#94A3B4] dark:text-white/35 mb-1" x-text="`${data.disk.free_gb}GB Free`"></span>
                        </div>
                        <!-- Progress Bar -->
                        <div class="w-full bg-[#E3EAF1] dark:bg-[#1B2C44] rounded-full h-1.5 mt-3 mb-2">
                            <div class="h-1.5 rounded-full transition-all duration-1000" :class="{'bg-rose-500': data.disk.percentage > 90, 'bg-amber-500': data.disk.percentage > 75, 'bg-[#1D5B96]': data.disk.percentage <= 75}" :style="`width: ${data.disk.percentage}%`"></div>
                        </div>

                        <!-- Health & Speed -->
                        <div class="flex justify-between items-center text-[10px] text-[#737373] dark:text-white/40 font-bold font-mono uppercase tracking-[0.15em] mt-2 border-t border-[#E3EAF1] dark:border-[#1B2C44] pt-2">
                            <span :class="{'text-emerald-500': data.disk.health === 'Healthy', 'text-rose-500': data.disk.health === 'Critical', 'text-amber-500': data.disk.health === 'Warning'}">
                                <i class="fa-solid" :class="{'fa-heart-circle-check': data.disk.health === 'Healthy', 'fa-triangle-exclamation': data.disk.health !== 'Healthy'}"></i> <span x-text="data.disk.health"></span>
                            </span>
                            <span title="Read / Write Speed" class="flex gap-2">
                                <span><i class="fa-solid fa-arrow-down text-[#1D5B96] dark:text-[#6BA8DC]"></i> <span x-text="data.disk.read_speed"></span>MB/s</span>
                                <span><i class="fa-solid fa-arrow-up text-[#1D5B96] dark:text-[#6BA8DC]"></i> <span x-text="data.disk.write_speed"></span>MB/s</span>
                            </span>
                        </div>
                    </div>

                    <!-- DISK (HDD/Hosting) -->
                    <div class="ui-cell p-4">
                        <p class="ui-eyebrow-muted mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-database"></i> Disk (Hosting)
                        </p>
                        <div class="flex items-end gap-2">
                            <h4 class="text-2xl font-black tracking-[-0.03em] text-[#0F1A2A] dark:text-white" x-text="data.disk_hdd.percentage + '%'"></h4>
                            <span class="text-xs font-semibold font-mono text-[#94A3B4] dark:text-white/35 mb-1" x-text="`${data.disk_hdd.free_gb}GB Free`"></span>
                        </div>
                        <!-- Progress Bar -->
                        <div class="w-full bg-[#E3EAF1] dark:bg-[#1B2C44] rounded-full h-1.5 mt-3 mb-2">
                            <div class="h-1.5 rounded-full transition-all duration-1000" :class="{'bg-rose-500': data.disk_hdd.percentage > 90, 'bg-amber-500': data.disk_hdd.percentage > 75, 'bg-[#1D5B96]': data.disk_hdd.percentage <= 75}" :style="`width: ${data.disk_hdd.percentage}%`"></div>
                        </div>

                        <!-- Health & Speed -->
                        <div class="flex justify-between items-center text-[10px] text-[#737373] dark:text-white/40 font-bold font-mono uppercase tracking-[0.15em] mt-2 border-t border-[#E3EAF1] dark:border-[#1B2C44] pt-2">
                            <span :class="{'text-emerald-500': data.disk_hdd.health === 'Healthy', 'text-rose-500': data.disk_hdd.health === 'Critical', 'text-amber-500': data.disk_hdd.health === 'Warning'}">
                                <i class="fa-solid" :class="{'fa-heart-circle-check': data.disk_hdd.health === 'Healthy', 'fa-triangle-exclamation': data.disk_hdd.health !== 'Healthy'}"></i> <span x-text="data.disk_hdd.health"></span>
                            </span>
                            <span title="Read / Write Speed" class="flex gap-2">
                                <span><i class="fa-solid fa-arrow-down text-[#1D5B96] dark:text-[#6BA8DC]"></i> <span x-text="data.disk_hdd.read_speed"></span>MB/s</span>
                                <span><i class="fa-solid fa-arrow-up text-[#1D5B96] dark:text-[#6BA8DC]"></i> <span x-text="data.disk_hdd.write_speed"></span>MB/s</span>
                            </span>
                        </div>
                    </div>

                    <!-- UPTIME -->
                    <div class="ui-cell p-4 flex flex-col justify-center items-center text-center">
                        <i class="fa-solid fa-clock text-[#94A3B4] dark:text-white/30 text-2xl mb-2"></i>
                        <p class="ui-eyebrow-muted mb-1">Server Uptime</p>
                        <h4 class="text-sm font-black font-mono text-[#0F1A2A] dark:text-white" x-text="data.uptime"></h4>
                    </div>
                </div>
            </div>

            <script nonce="{{ csp_nonce() }}">
            (function() {
                const initServerHealth = () => {
                    Alpine.data('serverHealth', () => ({
                        loading: true,
                        error: false,
                        intervalId: null,
                        data: {
                            cpu: { load_1m: 0 },
                            ram: { percentage: 0, used_mb: 0, total_mb: 0 },
                            disk: { percentage: 0, free_gb: 0, health: '...', read_speed: 0, write_speed: 0 },
                            disk_hdd: { percentage: 0, free_gb: 0, health: '...', read_speed: 0, write_speed: 0 },
                            uptime: '...'
                        },
                        startMonitoring() {
                            this.fetchData();
                            this.intervalId = setInterval(() => {
                                this.fetchData();
                            }, 5000); // Poll setiap 5 detik
                        },
                        fetchData() {
                            fetch('{{ route("superadmin.server_status") }}')
                                .then(res => {
                                    if(!res.ok) throw new Error('Network error');
                                    return res.json();
                                })
                                .then(data => {
                                    this.data = data;
                                    this.loading = false;
                                    this.error = false;
                                })
                                .catch(err => {
                                    console.error('Error fetching server status:', err);
                                    this.error = true;
                                });
                        },
                        destroy() {
                            if (this.intervalId) {
                                clearInterval(this.intervalId);
                            }
                        }
                    }));
                };

                if (window.Alpine) {
                    initServerHealth();
                } else {
                    document.addEventListener('alpine:init', initServerHealth);
                }
            })();
            </script>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Line Chart: Pendaftaran Pengguna -->
                <div class="ui-panel lg:col-span-2">
                    <div class="ui-panel-head">
                        <h3 class="ui-modal-title">Tren Pendaftaran Pengguna</h3>
                        <span class="ui-status">6 Bulan</span>
                    </div>
                    <div class="p-6">
                        <div id="chart-user-registrations"></div>
                    </div>
                </div>

                <!-- Pie Chart: Tipe Pengguna -->
                <div class="ui-panel">
                    <div class="ui-panel-head">
                        <h3 class="ui-modal-title">Distribusi Peran Pengguna</h3>
                    </div>
                    <div class="p-6">
                        <div id="chart-user-roles" class="flex justify-center"></div>
                    </div>
                </div>

                <!-- Bar Chart: Pendapatan -->
                <div class="ui-panel lg:col-span-3">
                    <div class="ui-panel-head">
                        <h3 class="ui-modal-title">Perbandingan Pendapatan Joki vs Hosting (6 Bulan Terakhir)</h3>
                        <span class="ui-status">Pendapatan</span>
                    </div>
                    <div class="p-6">
                        <div id="chart-revenue"></div>
                    </div>
                </div>
            </div>

            <!-- Detailed Revenue Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Joki Revenue -->
                <div class="ui-panel">
                    <div class="ui-panel-head">
                        <h3 class="ui-modal-title">Pendapatan Joki Code</h3>
                        <span class="ui-badge ui-badge-primary">Joki</span>
                    </div>
                    <div class="ui-grid grid-cols-2">
                        <div class="ui-cell p-5">
                            <p class="ui-stat-label mb-1">Bulan Ini</p>
                            <p class="ui-stat-value text-[#1D5B96] dark:text-[#6BA8DC]">Rp
                                {{ number_format($jokiRevenueMonth, 0, ',', '.') }}</p>
                        </div>
                        <div class="ui-cell p-5">
                            <p class="ui-stat-label mb-1">Total Keseluruhan</p>
                            <p class="ui-stat-value text-[#1D5B96] dark:text-[#6BA8DC]">Rp
                                {{ number_format($jokiRevenueTotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Hosting Revenue -->
                <div class="ui-panel">
                    <div class="ui-panel-head">
                        <h3 class="ui-modal-title">Pendapatan Hosting</h3>
                        <span class="ui-badge ui-badge-success">Hosting</span>
                    </div>
                    <div class="ui-grid grid-cols-2">
                        <div class="ui-cell p-5">
                            <p class="ui-stat-label mb-1">Bulan Ini</p>
                            <p class="ui-stat-value text-[#1D5B96] dark:text-[#6BA8DC]">Rp
                                {{ number_format($hostingRevenueMonth, 0, ',', '.') }}</p>
                        </div>
                        <div class="ui-cell p-5">
                            <p class="ui-stat-label mb-1">Total Keseluruhan</p>
                            <p class="ui-stat-value text-[#1D5B96] dark:text-[#6BA8DC]">Rp
                                {{ number_format($hostingRevenueTotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Recent Joki Orders -->
                <div class="ui-panel overflow-hidden">
                    <div class="ui-panel-head">
                        <h2 class="ui-modal-title">
                            Pesanan Joki Terbaru
                        </h2>
                        <a href="{{ route('admin_joki.orders') ?? '#' }}"
                            class="ui-arrow-link">Lihat
                            Semua</a>
                    </div>
                    <div class="overflow-x-auto flex-1">
                        <x-ui.table>
                            <x-slot:head>
                                <th scope="col" class="px-6 py-3">Proyek & Klien</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3 text-right">Harga</th>
                            </x-slot:head>
                            @forelse($recentJokiOrders as $order)
                                <tr class="hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828]">
                                    <td class="px-6 py-3">
                                        <div class="font-semibold text-[#0F1A2A] dark:text-white truncate max-w-[150px]">
                                             {{ $order->project_name ?? 'Tanpa Nama' }}</div>
                                        <div class="text-xs text-[#94A3B4] dark:text-white/35 truncate max-w-[150px]">
                                            {{ $order->client->name ?? 'Unknown' }}</div>
                                    </td>
                                    <td class="px-6 py-3">
                                        @php
                                            $statusColors = [
                                                'pending' => 'ui-badge-warning',
                                                'progress' => 'ui-badge-primary',
                                                'review' => 'ui-badge-info',
                                                'completed' => 'ui-badge-success',
                                                'cancelled' => 'ui-badge-danger',
                                            ];
                                            $colorClass =
                                                $statusColors[$order->status] ?? '';
                                        @endphp
                                        <span class="ui-badge {{ $colorClass }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right font-mono font-medium text-[#0F1A2A] dark:text-white">
                                        Rp {{ number_format($order->price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-[#94A3B4] dark:text-white/35 font-mono text-[11px] uppercase tracking-[0.2em]">Belum ada pesanan
                                        joki.</td>
                                </tr>
                            @endforelse
                        </x-ui.table>
                    </div>
                </div>

                <!-- Recent Hosting Projects -->
                <div class="ui-panel overflow-hidden">
                    <div class="ui-panel-head">
                        <h2 class="ui-modal-title">
                            <i class="text-emerald-500 dark:text-emerald-400 text-sm"></i>
                            Proyek Hosting Terbaru
                        </h2>
                        <a href="{{ route('admin_hosting.projects') ?? '#' }}"
                            class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">Lihat
                            Semua</a>
                    </div>
                    <div class="overflow-x-auto flex-1">
                        <x-ui.table>
                            <x-slot:head>
                                <th scope="col" class="px-6 py-3">Proyek & Klien</th>
                                <th scope="col" class="px-6 py-3">Framework</th>
                                <th scope="col" class="px-6 py-3 text-right">Status</th>
                            </x-slot:head>
                            @forelse($recentHostingProjects as $project)
                                <tr class="hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828]">
                                    <td class="px-6 py-3">
                                        <div class="font-semibold text-[#0F1A2A] dark:text-white truncate max-w-[150px]">
                                            {{ $project->project_name }}</div>
                                        <div class="text-xs text-[#94A3B4] dark:text-white/35 truncate max-w-[150px]">
                                            {{ $project->client->name ?? 'Unknown' }}</div>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span
                                            class="ui-badge font-mono">
                                            {{ ucfirst($project->framework) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        @php
                                            $hostingStatusColors = [
                                                'active' => 'ui-badge-success',
                                                'suspended' => 'ui-badge-danger',
                                                'building' => 'ui-badge-primary',
                                            ];
                                            $hColorClass =
                                                $hostingStatusColors[$project->status] ?? '';
                                        @endphp
                                        <span class="ui-badge {{ $hColorClass }}">
                                            {{ ucfirst($project->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-[#94A3B4] dark:text-white/35 font-mono text-[11px] uppercase tracking-[0.2em]">Belum ada proyek
                                        hosting.</td>
                                </tr>
                            @endforelse
                        </x-ui.table>
                    </div>
                </div>

            </div>

            <!-- Recent Users Table -->
            <div class="ui-panel overflow-hidden">
                <div class="ui-panel-head">
                    <h2 class="ui-modal-title">
                        <i class="fa-solid fa-user-plus text-[#1D5B96] dark:text-[#6BA8DC] text-sm"></i>
                        Pendaftar Klien Terbaru
                    </h2>
                    <a href="{{ route('superadmin.users.index') }}"
                        class="ui-arrow-link">Kelola Pengguna
                        &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <x-ui.table class="border-0 shadow-none rounded-none">
                        <x-slot:head>
                            <th scope="col" class="px-6 py-4">Nama Klien</th>
                            <th scope="col" class="px-6 py-4">Email</th>
                            <th scope="col" class="px-6 py-4">Minat Layanan / Role</th>
                            <th scope="col" class="px-6 py-4 text-center">Tanggal Daftar</th>
                            <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                        </x-slot:head>
                        @forelse($recentUsers as $user)
                            <tr class="hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828]">
                                <td class="px-6 py-4 font-medium text-[#0F1A2A] dark:text-white flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 bg-[#F0F6FC] dark:bg-[#0D1828] text-[#525252] dark:text-white/60 border border-[#E3EAF1] dark:border-[#1B2C44] flex items-center justify-center font-mono font-bold text-sm uppercase">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    {{ $user->name }}
                                </td>
                                <td class="px-6 py-4 text-[#525252] dark:text-white/60">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    @if ($user->role == 'user_joki')
                                        <span class="ui-badge ui-badge-primary">
                                            Jasa Joki Code
                                        </span>
                                    @elseif($user->role == 'user_hosting')
                                        <span class="ui-badge ui-badge-success">
                                            App Deployment
                                        </span>
                                    @else
                                        <span class="ui-badge">
                                            {{ ucfirst(str_replace('_', ' ', $user->role ?? 'User')) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-[#737373] dark:text-white/40 font-mono text-xs">{{ $user->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('superadmin.users.show', $user->hashid) }}"
                                        class="ui-icon-btn ui-icon-btn-primary"
                                        title="Detail Profil">
                                        <i class="fa-solid fa-arrow-right text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-[#94A3B4] dark:text-white/35">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i class="fa-regular fa-folder-open text-3xl text-[#C9D5E2] dark:text-white/20"></i>
                                        <p class="font-mono text-[11px] uppercase tracking-[0.2em]">Belum ada pengguna yang mendaftar.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </x-ui.table>
                </div>
            </div>

            <!-- Chart Scripts (Inside layout for PJAX support) -->
            <script nonce="{{ csp_nonce() }}">
                (function() {
                    // 1. Chart: User Registrations (Line)
                    var optionsRegistrations = {
                        chart: { type: 'area', height: 320, fontFamily: 'inherit', toolbar: { show: false } },
                        series: [{ name: 'Pendaftar Baru', data: @json($chartUserRegistrations['series']) }],
                        xaxis: { categories: @json($chartUserRegistrations['labels']) },
                        colors: ['#1D5B96'],
                        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] } },
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: 2 }
                    };
                    var chartRegEl = document.querySelector("#chart-user-registrations");
                    if(chartRegEl) {
                        new ApexCharts(chartRegEl, optionsRegistrations).render();
                    }

                    // 2. Chart: User Roles (Pie/Donut)
                    var optionsRoles = {
                        chart: { type: 'donut', height: 320, fontFamily: 'inherit' },
                        series: @json($chartUserRoles['series']),
                        labels: @json($chartUserRoles['labels']),
                        colors: ['#1D5B96', '#10b981', '#f59e0b', '#7FB4E2', '#94A3B4'],
                        dataLabels: { enabled: true },
                        legend: { position: 'bottom' }
                    };
                    var chartRolesEl = document.querySelector("#chart-user-roles");
                    if(chartRolesEl) {
                        new ApexCharts(chartRolesEl, optionsRoles).render();
                    }

                    // 3. Chart: Revenue (Bar)
                    var optionsRevenue = {
                        chart: { type: 'bar', height: 320, fontFamily: 'inherit', stacked: false, toolbar: { show: false } },
                        series: [
                            { name: 'Pendapatan Joki', data: @json($chartRevenue['joki']) },
                            { name: 'Pendapatan Hosting', data: @json($chartRevenue['hosting']) }
                        ],
                        xaxis: { categories: @json($chartRevenue['labels']) },
                        colors: ['#1D5B96', '#10b981'],
                        dataLabels: { enabled: false },
                        stroke: { show: true, width: 2, colors: ['transparent'] },
                        plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 0 } },
                        yaxis: {
                            labels: {
                                formatter: function (val) {
                                    return "Rp " + val.toLocaleString('id-ID');
                                }
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: function (val) {
                                    return "Rp " + val.toLocaleString('id-ID');
                                }
                            }
                        }
                    };
                    var chartRevEl = document.querySelector("#chart-revenue");
                    if(chartRevEl) {
                        new ApexCharts(chartRevEl, optionsRevenue).render();
                    }
                })();
            </script>
        </div>
    </x-ui.page-layout>
@endsection
