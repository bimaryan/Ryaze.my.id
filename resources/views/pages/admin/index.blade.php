@extends('index')

@section('content')
    <x-ui.page-layout>

        <!-- Header -->
        <x-ui.page-header title="Dashboard" />

        <div class="mt-6 space-y-6">

            <!-- Main KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- Total Users -->
                <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4 transition-colors">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20">
                        <i class="fa-solid fa-users text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5 truncate">Total Pengguna</p>
                        <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($totalUsers) }}</h3>
                        <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1 mt-1 truncate">
                            <i class="fa-solid fa-arrow-trend-up text-[9px]"></i> Akun aktif terdaftar
                        </p>
                    </div>
                </div>

                <!-- Joki Orders -->
                <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4 transition-colors">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-purple-50 text-purple-600 border border-purple-100 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20">
                        <i class="fa-solid fa-code-branch text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5 truncate">Pesanan Joki Aktif</p>
                        <div class="flex items-baseline gap-1.5">
                            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($activeJokiOrders) }}</h3>
                            <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">/ {{ number_format($totalJokiOrders) }} total</span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1 mt-1 truncate">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-purple-500"></span> Dalam pengerjaan
                        </p>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4 transition-colors">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-sky-50 text-sky-600 border border-sky-100 dark:bg-sky-500/10 dark:text-sky-400 dark:border-sky-500/20">
                        <i class="fa-solid fa-wallet text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5 truncate">Pendapatan Bulan Ini</p>
                            <a href="{{ route('superadmin.finance') }}" class="text-[11px] font-semibold text-sky-600 dark:text-sky-400 hover:underline inline-flex items-center gap-1 shrink-0" title="Lihat Laporan Keuangan">
                                Detail <i class="fa-solid fa-arrow-right text-[9px]"></i>
                            </a>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight truncate">
                            Rp {{ number_format($totalRevenueMonth, 0, ',', '.') }}
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-1">
                            Joki: Rp {{ number_format($jokiRevenueMonth, 0, ',', '.') }} • Hosting: Rp {{ number_format($hostingRevenueMonth, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <!-- Hosting Projects -->
                <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4 transition-colors">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20">
                        <i class="fa-solid fa-server text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5 truncate">Hosting Aktif</p>
                        <div class="flex items-baseline gap-1.5">
                            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($activeHosting) }}</h3>
                            <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">/ {{ number_format($totalHosting) }} total</span>
                        </div>
                        <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1 mt-1 truncate">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Layanan berjalan normal
                        </p>
                    </div>
                </div>

                <!-- Databases -->
                <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-amber-50 text-amber-600 border border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20">
                        <i class="fa-solid fa-database text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5 truncate">Total Database</p>
                        <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($totalDatabases) }}</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1 mt-1 truncate">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500"></span> MySQL & NoSQL aktif
                        </p>
                    </div>
                </div>

                <!-- Storage -->
                <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-teal-50 text-teal-600 border border-teal-100 dark:bg-teal-500/10 dark:text-teal-400 dark:border-teal-500/20">
                        <i class="fa-solid fa-hard-drive text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5 truncate">Penyimpanan Teralokasi</p>
                        <div class="flex items-baseline gap-1">
                            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                                {{ $totalStorageMB >= 1024 ? number_format($totalStorageMB / 1024, 1) : number_format($totalStorageMB) }}
                            </h3>
                            <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">{{ $totalStorageMB >= 1024 ? 'GB' : 'MB' }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1 mt-1 truncate">
                            <span>Limit alokasi penyimpanan</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Server Health Status -->
            <div x-data="serverHealth()" x-init="startMonitoring()">
                <div class="flex justify-between items-center mb-6 p-4 bg-white rounded-lg shadow-sm flex flex-wrap items-center justify-between border border-slate-200/80 dark:border-slate-800 mb-6 gap-4 dark:bg-slate-900">
                    <p class="text-lg font-bold text-indigo-600 dark:text-slate-100 flex items-center gap-2">
                        Status Kesehatan Server
                    </p>
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-3 w-3">
                            <span :class="{'bg-emerald-400': !loading && !error, 'bg-rose-400': error, 'bg-slate-400': loading}" class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"></span>
                            <span :class="{'bg-emerald-500': !loading && !error, 'bg-rose-500': error, 'bg-slate-500': loading}" class="relative inline-flex rounded-full h-3 w-3"></span>
                        </span>
                        <span class="text-sm font-bold text-slate-600 dark:text-slate-300" x-text="error ? 'Terputus' : (loading ? 'Menghubungkan...' : 'Online')"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                    <!-- CPU -->
                    <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 relative overflow-hidden">
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-microchip"></i> CPU Load (1m)
                        </p>
                        <div class="flex items-end gap-2">
                            <h4 class="text-2xl font-black" :class="{'text-rose-600 dark:text-rose-300': data.cpu.load_1m > 80, 'text-amber-500 dark:text-amber-400': data.cpu.load_1m > 50, 'text-slate-800 dark:text-slate-100': data.cpu.load_1m <= 50}" x-text="data.cpu.load_1m + '%'"></h4>
                        </div>
                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 mt-3">
                            <div class="h-1.5 rounded-full transition-all duration-1000" :class="{'bg-rose-500': data.cpu.load_1m > 80, 'bg-amber-500': data.cpu.load_1m > 50, 'bg-emerald-500': data.cpu.load_1m <= 50}" :style="`width: ${Math.min(data.cpu.load_1m, 100)}%`"></div>
                        </div>
                    </div>

                    <!-- RAM -->
                    <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 relative overflow-hidden">
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-memory"></i> RAM Usage
                        </p>
                        <div class="flex items-end gap-2">
                            <h4 class="text-2xl font-black text-slate-800 dark:text-slate-100" x-text="data.ram.percentage + '%'"></h4>
                            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 mb-1" x-text="`${Math.round(data.ram.used_mb/1024)}GB / ${Math.round(data.ram.total_mb/1024)}GB`"></span>
                        </div>
                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 mt-3">
                            <div class="h-1.5 rounded-full transition-all duration-1000" :class="{'bg-rose-500': data.ram.percentage > 85, 'bg-amber-500': data.ram.percentage > 70, 'bg-emerald-500': data.ram.percentage <= 70}" :style="`width: ${data.ram.percentage}%`"></div>
                        </div>
                    </div>

                    <!-- DISK (OS) -->
                    <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 relative overflow-hidden">
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-hard-drive"></i> Disk (OS)
                        </p>
                        <div class="flex items-end gap-2">
                            <h4 class="text-2xl font-black text-slate-800 dark:text-slate-100" x-text="data.disk.percentage + '%'"></h4>
                            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 mb-1" x-text="`${data.disk.free_gb}GB Free`"></span>
                        </div>
                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 mt-3 mb-2">
                            <div class="h-1.5 rounded-full transition-all duration-1000" :class="{'bg-rose-500': data.disk.percentage > 90, 'bg-amber-500': data.disk.percentage > 75, 'bg-blue-500': data.disk.percentage <= 75}" :style="`width: ${data.disk.percentage}%`"></div>
                        </div>
                        
                        <!-- Health & Speed -->
                        <div class="flex justify-between items-center text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-2 border-t border-slate-200 dark:border-slate-700 pt-2">
                            <span :class="{'text-emerald-500': data.disk.health === 'Healthy', 'text-rose-500': data.disk.health === 'Critical', 'text-amber-500': data.disk.health === 'Warning'}">
                                <i class="fa-solid" :class="{'fa-heart-circle-check': data.disk.health === 'Healthy', 'fa-triangle-exclamation': data.disk.health !== 'Healthy'}"></i> <span x-text="data.disk.health"></span>
                            </span>
                            <span title="Read / Write Speed" class="flex gap-2">
                                <span><i class="fa-solid fa-arrow-down text-sky-500"></i> <span x-text="data.disk.read_speed"></span>MB/s</span>
                                <span><i class="fa-solid fa-arrow-up text-indigo-500"></i> <span x-text="data.disk.write_speed"></span>MB/s</span>
                            </span>
                        </div>
                    </div>

                    <!-- DISK (HDD/Hosting) -->
                    <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 relative overflow-hidden">
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-database"></i> Disk (Hosting)
                        </p>
                        <div class="flex items-end gap-2">
                            <h4 class="text-2xl font-black text-slate-800 dark:text-slate-100" x-text="data.disk_hdd.percentage + '%'"></h4>
                            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 mb-1" x-text="`${data.disk_hdd.free_gb}GB Free`"></span>
                        </div>
                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 mt-3 mb-2">
                            <div class="h-1.5 rounded-full transition-all duration-1000" :class="{'bg-rose-500': data.disk_hdd.percentage > 90, 'bg-amber-500': data.disk_hdd.percentage > 75, 'bg-blue-500': data.disk_hdd.percentage <= 75}" :style="`width: ${data.disk_hdd.percentage}%`"></div>
                        </div>

                        <!-- Health & Speed -->
                        <div class="flex justify-between items-center text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-2 border-t border-slate-200 dark:border-slate-700 pt-2">
                            <span :class="{'text-emerald-500': data.disk_hdd.health === 'Healthy', 'text-rose-500': data.disk_hdd.health === 'Critical', 'text-amber-500': data.disk_hdd.health === 'Warning'}">
                                <i class="fa-solid" :class="{'fa-heart-circle-check': data.disk_hdd.health === 'Healthy', 'fa-triangle-exclamation': data.disk_hdd.health !== 'Healthy'}"></i> <span x-text="data.disk_hdd.health"></span>
                            </span>
                            <span title="Read / Write Speed" class="flex gap-2">
                                <span><i class="fa-solid fa-arrow-down text-sky-500"></i> <span x-text="data.disk_hdd.read_speed"></span>MB/s</span>
                                <span><i class="fa-solid fa-arrow-up text-indigo-500"></i> <span x-text="data.disk_hdd.write_speed"></span>MB/s</span>
                            </span>
                        </div>
                    </div>

                    <!-- UPTIME -->
                    <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col justify-center items-center text-center">
                        <i class="fa-solid fa-clock text-slate-300 dark:text-slate-400 text-2xl mb-2"></i>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Server Uptime</p>
                        <h4 class="text-sm font-black text-slate-800 dark:text-slate-100" x-text="data.uptime"></h4>
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
                <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-4">Tren Pendaftaran Pengguna</h3>
                    <div id="chart-user-registrations"></div>
                </div>

                <!-- Pie Chart: Tipe Pengguna -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-4">Distribusi Peran Pengguna</h3>
                    <div id="chart-user-roles" class="flex justify-center"></div>
                </div>

                <!-- Bar Chart: Pendapatan -->
                <div class="lg:col-span-3 bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-4">Perbandingan Pendapatan Joki vs Hosting (6 Bulan Terakhir)</h3>
                    <div id="chart-revenue"></div>
                </div>
            </div>

            <!-- Detailed Revenue Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Joki Revenue -->
                <div>
                    <div class="flex items-center justify-between mb-4 bg-white dark:bg-slate-900 p-4 rounded-lg shadow-sm">
                        <div class="flex items-center gap-3">
                            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Pendapatan Joki Code</h3>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200/80 dark:border-slate-800">
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Bulan Ini</p>
                            <p class="text-xl font-bold text-indigo-600">Rp
                                {{ number_format($jokiRevenueMonth, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200/80 dark:border-slate-800">
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Keseluruhan
                            </p>
                            <p class="text-xl font-bold text-indigo-600">Rp
                                {{ number_format($jokiRevenueTotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Hosting Revenue -->
                <div>
                    <div class="flex items-center justify-between mb-4 bg-white dark:bg-slate-900 p-4 rounded-lg shadow-sm">
                        <div class="flex items-center gap-3">
                            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Pendapatan Hosting</h3>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200/80 dark:border-slate-800">
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Bulan Ini</p>
                            <p class="text-xl font-bold text-indigo-600">Rp
                                {{ number_format($hostingRevenueMonth, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200/80 dark:border-slate-800">
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Keseluruhan
                            </p>
                            <p class="text-xl font-bold text-indigo-600">Rp
                                {{ number_format($hostingRevenueTotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Recent Joki Orders -->
                <div>
                    <div class="p-4 border-b mb-4 bg-white dark:bg-slate-900 rounded-lg shadow-sm border border-slate-200/80 dark:border-slate-800 flex justify-between items-center">
                        <h2 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            Pesanan Joki Terbaru
                        </h2>
                        <a href="{{ route('admin_joki.orders') ?? '#' }}"
                            class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">Lihat
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
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50">
                                    <td class="px-6 py-3">
                                        <div class="font-semibold text-slate-800 dark:text-slate-100 truncate max-w-[150px]">
                                             {{ $order->project_name ?? 'Tanpa Nama' }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 truncate max-w-[150px]">
                                            {{ $order->client->name ?? 'Unknown' }}</div>
                                    </td>
                                    <td class="px-6 py-3">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-amber-50 text-amber-700 border-amber-200/80 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
                                                'progress' => 'bg-blue-50 text-blue-700 border-blue-200/80 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20',
                                                'review' => 'bg-purple-50 text-purple-700 border-purple-200/80 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20',
                                                'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
                                                'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200/80 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20',
                                            ];
                                            $colorClass =
                                                $statusColors[$order->status] ??
                                                'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200/80 dark:border-slate-700';
                                        @endphp
                                        <span
                                            class="px-2.5 py-1 rounded-md text-xs font-medium border {{ $colorClass }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right font-medium text-slate-800 dark:text-slate-100">
                                        Rp {{ number_format($order->price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">Belum ada pesanan
                                        joki.</td>
                                </tr>
                            @endforelse
                        </x-ui.table>
                    </div>
                </div>

                <!-- Recent Hosting Projects -->
                <div class="">
                    <div class="p-4 border-b mb-4 bg-white dark:bg-slate-900 rounded-lg shadow-sm border border-slate-200/80 dark:border-slate-800 flex justify-between items-center">
                        <h2 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-server text-emerald-500 dark:text-emerald-400"></i>
                            Proyek Hosting Terbaru
                        </h2>
                        <a href="{{ route('admin_hosting.projects') ?? '#' }}"
                            class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300">Lihat
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
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50">
                                    <td class="px-6 py-3">
                                        <div class="font-semibold text-slate-800 dark:text-slate-100 truncate max-w-[150px]">
                                            {{ $project->project_name }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 truncate max-w-[150px]">
                                            {{ $project->client->name ?? 'Unknown' }}</div>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200/80 dark:border-slate-700">
                                            {{ ucfirst($project->framework) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        @php
                                            $hostingStatusColors = [
                                                'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
                                                'suspended' => 'bg-rose-50 text-rose-700 border-rose-200/80 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20',
                                                'building' => 'bg-blue-50 text-blue-700 border-blue-200/80 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20',
                                            ];
                                            $hColorClass =
                                                $hostingStatusColors[$project->status] ??
                                                'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200/80 dark:border-slate-700';
                                        @endphp
                                        <span
                                            class="px-2.5 py-1 rounded-md text-xs font-medium border {{ $hColorClass }}">
                                            {{ ucfirst($project->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">Belum ada proyek
                                        hosting.</td>
                                </tr>
                            @endforelse
                        </x-ui.table>
                    </div>
                </div>

            </div>

            <!-- Recent Users Table -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                    <h2 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-user-plus text-blue-500 dark:text-blue-400"></i>
                        Pendaftar Klien Terbaru
                    </h2>
                    <a href="{{ route('superadmin.users.index') }}"
                        class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">Kelola Pengguna
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
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50">
                                <td class="px-6 py-4 font-medium text-slate-800 dark:text-slate-100 flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20 flex items-center justify-center font-bold text-sm uppercase shadow-sm">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    {{ $user->name }}
                                </td>
                                <td class="px-6 py-4">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    @if ($user->role == 'user_joki')
                                        <span
                                            class="px-3 py-1.5 rounded-md text-xs font-semibold bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/30">
                                            Jasa Joki Code
                                        </span>
                                    @elseif($user->role == 'user_hosting')
                                        <span
                                            class="px-3 py-1.5 rounded-md text-xs font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-500/30">
                                            App Deployment
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1.5 rounded-md text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700">
                                            {{ ucfirst(str_replace('_', ' ', $user->role ?? 'User')) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-slate-500 dark:text-slate-400">{{ $user->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('superadmin.users.show', $user->hashid) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 text-slate-400 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-200 dark:hover:border-indigo-500/40 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 shadow-sm"
                                        title="Detail Profil">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i class="fa-regular fa-folder-open text-3xl text-slate-300 dark:text-slate-400"></i>
                                        <p>Belum ada pengguna yang mendaftar.</p>
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
                        chart: { type: 'area', height: 320, fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
                        series: [{ name: 'Pendaftar Baru', data: @json($chartUserRegistrations['series']) }],
                        xaxis: { categories: @json($chartUserRegistrations['labels']) },
                        colors: ['#3b82f6'],
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
                        chart: { type: 'donut', height: 320, fontFamily: 'Inter, sans-serif' },
                        series: @json($chartUserRoles['series']),
                        labels: @json($chartUserRoles['labels']),
                        colors: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#64748b'],
                        dataLabels: { enabled: true },
                        legend: { position: 'bottom' }
                    };
                    var chartRolesEl = document.querySelector("#chart-user-roles");
                    if(chartRolesEl) {
                        new ApexCharts(chartRolesEl, optionsRoles).render();
                    }

                    // 3. Chart: Revenue (Bar)
                    var optionsRevenue = {
                        chart: { type: 'bar', height: 320, fontFamily: 'Inter, sans-serif', stacked: false, toolbar: { show: false } },
                        series: [
                            { name: 'Pendapatan Joki', data: @json($chartRevenue['joki']) },
                            { name: 'Pendapatan Hosting', data: @json($chartRevenue['hosting']) }
                        ],
                        xaxis: { categories: @json($chartRevenue['labels']) },
                        colors: ['#6366f1', '#10b981'],
                        dataLabels: { enabled: false },
                        stroke: { show: true, width: 2, colors: ['transparent'] },
                        plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 4 } },
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