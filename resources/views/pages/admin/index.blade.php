@extends('index')

@section('content')
    <x-ui.page-layout>

        <!-- Header -->
        <x-ui.page-header title="Dashboard" />


        <div class="mt-6 space-y-6">

            <!-- Main KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Total Users -->
                <div
                    class="bg-white dark:bg-slate-800/60 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col justify-between group transition-all duration-300 hover:shadow-md hover:border-blue-200 dark:hover:border-blue-500/40 hover:-translate-y-1 relative overflow-hidden">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 dark:bg-blue-500/10 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0">
                    </div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Total Pengguna</p>
                            <h3 class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ number_format($totalUsers) }}</h3>
                        </div>
                        <div
                            class="w-12 h-12 flex items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-300 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                            <i class="fa-solid fa-users text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Joki Orders -->
                <div
                    class="bg-white dark:bg-slate-800/60 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col justify-between group transition-all duration-300 hover:shadow-md hover:border-indigo-200 dark:hover:border-indigo-500/40 hover:-translate-y-1 relative overflow-hidden">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50 dark:bg-indigo-500/10 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0">
                    </div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Pesanan Joki Aktif</p>
                            <div class="flex items-baseline gap-2">
                                <h3 class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ number_format($activeJokiOrders) }}</h3>
                                <span class="text-sm text-slate-400 dark:text-slate-500 font-medium">/ {{ number_format($totalJokiOrders) }}
                                    Total</span>
                            </div>
                        </div>
                        <div
                            class="w-12 h-12 flex items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                            <i class="fa-solid fa-code-branch text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div
                    class="bg-white dark:bg-slate-800/60 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col justify-between group transition-all duration-300 hover:shadow-md hover:border-sky-200 dark:hover:border-sky-500/40 hover:-translate-y-1 relative overflow-hidden">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-sky-50 dark:bg-sky-500/10 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0">
                    </div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Pendapatan (Bulan Ini)</p>
                            <h3 class="text-2xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">Rp
                                {{ number_format($totalRevenueMonth, 0, ',', '.') }}</h3>
                            <div class="text-xs text-slate-400 dark:text-slate-500 mt-1.5 space-y-0.5">
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-block w-2 h-2 rounded-full bg-indigo-500"></span>
                                    Joki: Rp {{ number_format($jokiRevenueMonth, 0, ',', '.') }}
                                    ({{ number_format($jokiRevenueMonthCount) }} transaksi)
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                                    Hosting: Rp {{ number_format($hostingRevenueMonth, 0, ',', '.') }}
                                    ({{ number_format($hostingRevenueMonthCount) }} transaksi)
                                </div>
                            </div>
                            <a href="{{ route('superadmin.finance') }}"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-sky-600 dark:text-sky-300 hover:text-sky-800 dark:hover:text-sky-300 mt-2 transition-colors">
                                <i class="fa-solid fa-chart-pie"></i> Lihat Laporan Keuangan
                            </a>
                        </div>
                        <div
                            class="w-12 h-12 flex items-center justify-center rounded-xl bg-sky-100 dark:bg-sky-500/20 text-sky-600 dark:text-sky-300 group-hover:bg-sky-600 group-hover:text-white transition-colors duration-300">
                            <i class="fa-solid fa-wallet text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Hosting Projects -->
                <div
                    class="bg-white dark:bg-slate-800/60 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col justify-between group transition-all duration-300 hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-500/40 hover:-translate-y-1 relative overflow-hidden">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 dark:bg-emerald-500/10 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0">
                    </div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Hosting Aktif</p>
                            <div class="flex items-baseline gap-2">
                                <h3 class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ number_format($activeHosting) }}</h3>
                                <span class="text-sm text-slate-400 dark:text-slate-500 font-medium">/ {{ number_format($totalHosting) }}
                                    Total</span>
                            </div>
                        </div>
                        <div
                            class="w-12 h-12 flex items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-300 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                            <i class="fa-solid fa-server text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Databases -->
                <a href="{{ route('admin_hosting.databases') }}"
                    class="bg-white dark:bg-slate-800/60 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col justify-between group transition-all duration-300 hover:shadow-md hover:border-orange-200 dark:hover:border-orange-500/40 hover:-translate-y-1 relative overflow-hidden block">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-orange-50 dark:bg-orange-500/10 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0">
                    </div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Total Database</p>
                            <h3 class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ number_format($totalDatabases) }}</h3>
                        </div>
                        <div
                            class="w-12 h-12 flex items-center justify-center rounded-xl bg-orange-100 dark:bg-orange-500/20 text-orange-600 dark:text-orange-300 group-hover:bg-orange-600 group-hover:text-white transition-colors duration-300">
                            <i class="fa-solid fa-database text-xl"></i>
                        </div>
                    </div>
                </a>

                <!-- Storage -->
                <a href="{{ route('admin_hosting.storage') }}"
                    class="bg-white dark:bg-slate-800/60 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col justify-between group transition-all duration-300 hover:shadow-md hover:border-teal-200 dark:hover:border-teal-500/40 hover:-translate-y-1 relative overflow-hidden block">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-teal-50 dark:bg-teal-500/10 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0">
                    </div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Penyimpanan Teralokasi</p>
                            <div class="flex items-baseline gap-2">
                                <h3 class="text-3xl font-bold text-slate-800 dark:text-slate-100">
                                    {{ $totalStorageMB >= 1024 ? number_format($totalStorageMB / 1024, 1) : number_format($totalStorageMB) }}
                                </h3>
                                <span
                                    class="text-sm text-slate-400 dark:text-slate-500 font-medium">{{ $totalStorageMB >= 1024 ? 'GB' : 'MB' }}</span>
                            </div>
                        </div>
                        <div
                            class="w-12 h-12 flex items-center justify-center rounded-xl bg-teal-100 dark:bg-teal-500/20 text-teal-600 dark:text-teal-300 group-hover:bg-teal-600 group-hover:text-white transition-colors duration-300">
                            <i class="fa-solid fa-hard-drive text-xl"></i>
                        </div>
                    </div>
                </a>
            </div>

            </div>

            <!-- Server Health Status -->
            <div x-data="serverHealth()" x-init="startMonitoring()" class="bg-white dark:bg-slate-800/60 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-server text-indigo-500 dark:text-indigo-400"></i> Status Kesehatan Server
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-3 w-3">
                            <span :class="{'bg-emerald-400': !loading && !error, 'bg-rose-400': error, 'bg-slate-400': loading}" class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"></span>
                            <span :class="{'bg-emerald-500': !loading && !error, 'bg-rose-500': error, 'bg-slate-500': loading}" class="relative inline-flex rounded-full h-3 w-3"></span>
                        </span>
                        <span class="text-sm font-bold text-slate-600 dark:text-slate-300" x-text="error ? 'Terputus' : (loading ? 'Menghubungkan...' : 'Online')"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- CPU -->
                    <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-100 dark:border-slate-700 relative overflow-hidden">
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
                    <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-100 dark:border-slate-700 relative overflow-hidden">
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

                    <!-- DISK -->
                    <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-100 dark:border-slate-700 relative overflow-hidden">
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-hard-drive"></i> Disk Space
                        </p>
                        <div class="flex items-end gap-2">
                            <h4 class="text-2xl font-black text-slate-800 dark:text-slate-100" x-text="data.disk.percentage + '%'"></h4>
                            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 mb-1" x-text="`${data.disk.free_gb}GB Free`"></span>
                        </div>
                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 mt-3">
                            <div class="h-1.5 rounded-full transition-all duration-1000" :class="{'bg-rose-500': data.disk.percentage > 90, 'bg-amber-500': data.disk.percentage > 75, 'bg-blue-500': data.disk.percentage <= 75}" :style="`width: ${data.disk.percentage}%`"></div>
                        </div>
                    </div>

                    <!-- UPTIME -->
                    <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-100 dark:border-slate-700 flex flex-col justify-center items-center text-center">
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
                            disk: { percentage: 0, free_gb: 0 },
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
                <div class="lg:col-span-2 bg-white dark:bg-slate-800/60 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4">Tren Pendaftaran Pengguna</h3>
                    <div id="chart-user-registrations"></div>
                </div>

                <!-- Pie Chart: Tipe Pengguna -->
                <div class="bg-white dark:bg-slate-800/60 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4">Distribusi Peran Pengguna</h3>
                    <div id="chart-user-roles" class="flex justify-center"></div>
                </div>

                <!-- Bar Chart: Pendapatan -->
                <div class="lg:col-span-3 bg-white dark:bg-slate-800/60 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4">Perbandingan Pendapatan Joki vs Hosting (6 Bulan Terakhir)</h3>
                    <div id="chart-revenue"></div>
                </div>
            </div>

            <!-- Detailed Revenue Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Joki Revenue -->
                <div class="bg-white dark:bg-slate-800/60 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                <i class="fa-solid fa-laptop-code"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Pendapatan Joki Code</h3>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-700">
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Bulan Ini</p>
                            <p class="text-xl font-bold text-indigo-700 dark:text-indigo-300">Rp
                                {{ number_format($jokiRevenueMonth, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-700">
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Keseluruhan
                            </p>
                            <p class="text-xl font-bold text-slate-700 dark:text-slate-200">Rp
                                {{ number_format($jokiRevenueTotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Hosting Revenue -->
                <div class="bg-white dark:bg-slate-800/60 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-300 flex items-center justify-center">
                                <i class="fa-solid fa-cloud"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Pendapatan Hosting</h3>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-700">
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Bulan Ini</p>
                            <p class="text-xl font-bold text-emerald-700 dark:text-emerald-300">Rp
                                {{ number_format($hostingRevenueMonth, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-700">
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Keseluruhan
                            </p>
                            <p class="text-xl font-bold text-slate-700 dark:text-slate-200">Rp
                                {{ number_format($hostingRevenueTotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Recent Joki Orders -->
                <div class="bg-white dark:bg-slate-800/60 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-white dark:bg-slate-800/60">
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-code-pull-request text-indigo-500 dark:text-indigo-400"></i>
                            Pesanan Joki Terbaru
                        </h2>
                        <a href="{{ route('admin_joki.orders') ?? '#' }}"
                            class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-400 dark:hover:text-indigo-300 transition-colors">Lihat
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
                                <tr class="hover:bg-slate-50 dark:bg-slate-800/50 dark:hover:bg-slate-700/40 transition-colors">
                                    <td class="px-6 py-3">
                                        <div class="font-semibold text-slate-800 dark:text-slate-100 truncate max-w-[150px]">
                                            {{ $order->project_name ?? 'Tanpa Nama' }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 truncate max-w-[150px]">
                                            {{ $order->client->name ?? 'Unknown' }}</div>
                                    </td>
                                    <td class="px-6 py-3">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-500/40',
                                                'progress' => 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-500/40',
                                                'review' => 'bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-500/40',
                                                'completed' => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/40',
                                                'cancelled' => 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300 border-red-200 dark:border-red-500/40',
                                            ];
                                            $colorClass =
                                                $statusColors[$order->status] ??
                                                'bg-slate-100 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700';
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
                <div class="bg-white dark:bg-slate-800/60 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-white dark:bg-slate-800/60">
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-server text-emerald-500 dark:text-emerald-400"></i>
                            Proyek Hosting Terbaru
                        </h2>
                        <a href="{{ route('admin_hosting.projects') ?? '#' }}"
                            class="text-sm font-semibold text-emerald-600 dark:text-emerald-300 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors">Lihat
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
                                <tr class="hover:bg-slate-50 dark:bg-slate-800/50 dark:hover:bg-slate-700/40 transition-colors">
                                    <td class="px-6 py-3">
                                        <div class="font-semibold text-slate-800 dark:text-slate-100 truncate max-w-[150px]">
                                            {{ $project->project_name }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 truncate max-w-[150px]">
                                            {{ $project->client->name ?? 'Unknown' }}</div>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                                            {{ ucfirst($project->framework) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        @php
                                            $hostingStatusColors = [
                                                'active' => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/40',
                                                'suspended' => 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300 border-red-200 dark:border-red-500/40',
                                                'building' => 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-500/40',
                                            ];
                                            $hColorClass =
                                                $hostingStatusColors[$project->status] ??
                                                'bg-slate-100 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700';
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
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-white dark:bg-slate-800/60">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-user-plus text-blue-500 dark:text-blue-400"></i>
                        Pendaftar Klien Terbaru
                    </h2>
                    <a href="{{ route('superadmin.users.index') }}"
                        class="text-sm font-semibold text-blue-600 dark:text-blue-300 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">Kelola Pengguna
                        &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <x-ui.table>
                        <x-slot:head>
                            <th scope="col" class="px-6 py-4">Nama Klien</th>
                            <th scope="col" class="px-6 py-4">Email</th>
                            <th scope="col" class="px-6 py-4">Minat Layanan / Role</th>
                            <th scope="col" class="px-6 py-4 text-center">Tanggal Daftar</th>
                            <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                        </x-slot:head>
                        @forelse($recentUsers as $user)
                            <tr class="hover:bg-slate-50 dark:bg-slate-800/50 dark:hover:bg-slate-700/40 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-800 dark:text-slate-100 flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-200 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-bold text-sm uppercase shadow-sm">
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
                                            class="px-3 py-1.5 rounded-md text-xs font-semibold bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                            {{ ucfirst(str_replace('_', ' ', $user->role ?? 'User')) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-slate-500 dark:text-slate-400">{{ $user->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('superadmin.users.show', $user->hashid) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 dark:hover:text-indigo-400 hover:border-indigo-200 dark:hover:border-indigo-500/40 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 dark:hover:bg-indigo-500/10 transition-all shadow-sm"
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