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
                    class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between group transition-all duration-300 hover:shadow-md hover:border-blue-200 hover:-translate-y-1 relative overflow-hidden">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0">
                    </div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">Total Pengguna</p>
                            <h3 class="text-3xl font-bold text-slate-800">{{ number_format($totalUsers) }}</h3>
                        </div>
                        <div
                            class="w-12 h-12 flex items-center justify-center rounded-xl bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                            <i class="fa-solid fa-users text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Joki Orders -->
                <div
                    class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between group transition-all duration-300 hover:shadow-md hover:border-indigo-200 hover:-translate-y-1 relative overflow-hidden">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0">
                    </div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">Pesanan Joki Aktif</p>
                            <div class="flex items-baseline gap-2">
                                <h3 class="text-3xl font-bold text-slate-800">{{ number_format($activeJokiOrders) }}</h3>
                                <span class="text-sm text-slate-400 font-medium">/ {{ number_format($totalJokiOrders) }}
                                    Total</span>
                            </div>
                        </div>
                        <div
                            class="w-12 h-12 flex items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                            <i class="fa-solid fa-code-branch text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div
                    class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between group transition-all duration-300 hover:shadow-md hover:border-sky-200 hover:-translate-y-1 relative overflow-hidden">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-sky-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0">
                    </div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">Pendapatan (Bulan Ini)</p>
                            <h3 class="text-2xl font-bold text-slate-800 tracking-tight">Rp
                                {{ number_format($totalRevenueMonth, 0, ',', '.') }}</h3>
                        </div>
                        <div
                            class="w-12 h-12 flex items-center justify-center rounded-xl bg-sky-100 text-sky-600 group-hover:bg-sky-600 group-hover:text-white transition-colors duration-300">
                            <i class="fa-solid fa-wallet text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Hosting Projects -->
                <div
                    class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between group transition-all duration-300 hover:shadow-md hover:border-emerald-200 hover:-translate-y-1 relative overflow-hidden">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0">
                    </div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">Hosting Aktif</p>
                            <div class="flex items-baseline gap-2">
                                <h3 class="text-3xl font-bold text-slate-800">{{ number_format($activeHosting) }}</h3>
                                <span class="text-sm text-slate-400 font-medium">/ {{ number_format($totalHosting) }}
                                    Total</span>
                            </div>
                        </div>
                        <div
                            class="w-12 h-12 flex items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                            <i class="fa-solid fa-server text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Databases -->
                <a href="{{ route('admin_hosting.databases') }}"
                    class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between group transition-all duration-300 hover:shadow-md hover:border-orange-200 hover:-translate-y-1 relative overflow-hidden block">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-orange-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0">
                    </div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">Total Database</p>
                            <h3 class="text-3xl font-bold text-slate-800">{{ number_format($totalDatabases) }}</h3>
                        </div>
                        <div
                            class="w-12 h-12 flex items-center justify-center rounded-xl bg-orange-100 text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors duration-300">
                            <i class="fa-solid fa-database text-xl"></i>
                        </div>
                    </div>
                </a>

                <!-- Storage -->
                <a href="{{ route('admin_hosting.storage') }}"
                    class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between group transition-all duration-300 hover:shadow-md hover:border-teal-200 hover:-translate-y-1 relative overflow-hidden block">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-teal-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0">
                    </div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">Penyimpanan Teralokasi</p>
                            <div class="flex items-baseline gap-2">
                                <h3 class="text-3xl font-bold text-slate-800">
                                    {{ $totalStorageMB >= 1024 ? number_format($totalStorageMB / 1024, 1) : number_format($totalStorageMB) }}
                                </h3>
                                <span
                                    class="text-sm text-slate-400 font-medium">{{ $totalStorageMB >= 1024 ? 'GB' : 'MB' }}</span>
                            </div>
                        </div>
                        <div
                            class="w-12 h-12 flex items-center justify-center rounded-xl bg-teal-100 text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition-colors duration-300">
                            <i class="fa-solid fa-hard-drive text-xl"></i>
                        </div>
                    </div>
                </a>
            </div>

            </div>

            <!-- Server Health Status -->
            <div x-data="serverHealth()" x-init="startMonitoring()" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-server text-indigo-500"></i> Status Kesehatan Server
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-3 w-3">
                            <span :class="{'bg-emerald-400': !loading && !error, 'bg-rose-400': error, 'bg-slate-400': loading}" class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"></span>
                            <span :class="{'bg-emerald-500': !loading && !error, 'bg-rose-500': error, 'bg-slate-500': loading}" class="relative inline-flex rounded-full h-3 w-3"></span>
                        </span>
                        <span class="text-sm font-bold text-slate-600" x-text="error ? 'Terputus' : (loading ? 'Menghubungkan...' : 'Online')"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- CPU -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 relative overflow-hidden">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-microchip"></i> CPU Load (1m)
                        </p>
                        <div class="flex items-end gap-2">
                            <h4 class="text-2xl font-black" :class="{'text-rose-600': data.cpu.load_1m > 80, 'text-amber-500': data.cpu.load_1m > 50, 'text-slate-800': data.cpu.load_1m <= 50}" x-text="data.cpu.load_1m + '%'"></h4>
                        </div>
                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-200 rounded-full h-1.5 mt-3">
                            <div class="h-1.5 rounded-full transition-all duration-1000" :class="{'bg-rose-500': data.cpu.load_1m > 80, 'bg-amber-500': data.cpu.load_1m > 50, 'bg-emerald-500': data.cpu.load_1m <= 50}" :style="`width: ${Math.min(data.cpu.load_1m, 100)}%`"></div>
                        </div>
                    </div>

                    <!-- RAM -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 relative overflow-hidden">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-memory"></i> RAM Usage
                        </p>
                        <div class="flex items-end gap-2">
                            <h4 class="text-2xl font-black text-slate-800" x-text="data.ram.percentage + '%'"></h4>
                            <span class="text-xs font-semibold text-slate-400 mb-1" x-text="`${Math.round(data.ram.used_mb/1024)}GB / ${Math.round(data.ram.total_mb/1024)}GB`"></span>
                        </div>
                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-200 rounded-full h-1.5 mt-3">
                            <div class="h-1.5 rounded-full transition-all duration-1000" :class="{'bg-rose-500': data.ram.percentage > 85, 'bg-amber-500': data.ram.percentage > 70, 'bg-emerald-500': data.ram.percentage <= 70}" :style="`width: ${data.ram.percentage}%`"></div>
                        </div>
                    </div>

                    <!-- DISK -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 relative overflow-hidden">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-hard-drive"></i> Disk Space
                        </p>
                        <div class="flex items-end gap-2">
                            <h4 class="text-2xl font-black text-slate-800" x-text="data.disk.percentage + '%'"></h4>
                            <span class="text-xs font-semibold text-slate-400 mb-1" x-text="`${data.disk.free_gb}GB Free`"></span>
                        </div>
                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-200 rounded-full h-1.5 mt-3">
                            <div class="h-1.5 rounded-full transition-all duration-1000" :class="{'bg-rose-500': data.disk.percentage > 90, 'bg-amber-500': data.disk.percentage > 75, 'bg-blue-500': data.disk.percentage <= 75}" :style="`width: ${data.disk.percentage}%`"></div>
                        </div>
                    </div>

                    <!-- UPTIME -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 flex flex-col justify-center items-center text-center">
                        <i class="fa-solid fa-clock text-slate-300 text-2xl mb-2"></i>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Server Uptime</p>
                        <h4 class="text-sm font-black text-slate-800" x-text="data.uptime"></h4>
                    </div>
                </div>
            </div>

            <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('serverHealth', () => ({
                    loading: true,
                    error: false,
                    data: {
                        cpu: { load_1m: 0 },
                        ram: { percentage: 0, used_mb: 0, total_mb: 0 },
                        disk: { percentage: 0, free_gb: 0 },
                        uptime: '...'
                    },
                    startMonitoring() {
                        this.fetchData();
                        setInterval(() => {
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
                    }
                }));
            });
            </script>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Line Chart: Pendaftaran Pengguna -->
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Tren Pendaftaran Pengguna</h3>
                    <div id="chart-user-registrations"></div>
                </div>

                <!-- Pie Chart: Tipe Pengguna -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Distribusi Peran Pengguna</h3>
                    <div id="chart-user-roles" class="flex justify-center"></div>
                </div>

                <!-- Bar Chart: Pendapatan -->
                <div class="lg:col-span-3 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Perbandingan Pendapatan Joki vs Hosting (6 Bulan Terakhir)</h3>
                    <div id="chart-revenue"></div>
                </div>
            </div>

            <!-- Detailed Revenue Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Joki Revenue -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <i class="fa-solid fa-laptop-code"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">Pendapatan Joki Code</h3>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Bulan Ini</p>
                            <p class="text-xl font-bold text-indigo-700">Rp
                                {{ number_format($jokiRevenueMonth, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Total Keseluruhan
                            </p>
                            <p class="text-xl font-bold text-slate-700">Rp
                                {{ number_format($jokiRevenueTotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Hosting Revenue -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i class="fa-solid fa-cloud"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">Pendapatan Hosting</h3>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Bulan Ini</p>
                            <p class="text-xl font-bold text-emerald-700">Rp
                                {{ number_format($hostingRevenueMonth, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Total Keseluruhan
                            </p>
                            <p class="text-xl font-bold text-slate-700">Rp
                                {{ number_format($hostingRevenueTotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Recent Joki Orders -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                    <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
                        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-code-pull-request text-indigo-500"></i>
                            Pesanan Joki Terbaru
                        </h2>
                        <a href="{{ route('admin_joki.orders') ?? '#' }}"
                            class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">Lihat
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
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-3">
                                        <div class="font-semibold text-slate-800 truncate max-w-[150px]">
                                            {{ $order->project_name ?? 'Tanpa Nama' }}</div>
                                        <div class="text-xs text-slate-500 truncate max-w-[150px]">
                                            {{ $order->client->name ?? 'Unknown' }}</div>
                                    </td>
                                    <td class="px-6 py-3">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                                                'progress' => 'bg-blue-100 text-blue-700 border-blue-200',
                                                'review' => 'bg-purple-100 text-purple-700 border-purple-200',
                                                'completed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                                'cancelled' => 'bg-red-100 text-red-700 border-red-200',
                                            ];
                                            $colorClass =
                                                $statusColors[$order->status] ??
                                                'bg-slate-100 text-slate-700 border-slate-200';
                                        @endphp
                                        <span
                                            class="px-2.5 py-1 rounded-md text-xs font-medium border {{ $colorClass }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right font-medium text-slate-800">
                                        Rp {{ number_format($order->price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-slate-500">Belum ada pesanan
                                        joki.</td>
                                </tr>
                            @endforelse
                        </x-ui.table>
                    </div>
                </div>

                <!-- Recent Hosting Projects -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                    <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
                        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-server text-emerald-500"></i>
                            Proyek Hosting Terbaru
                        </h2>
                        <a href="{{ route('admin_hosting.projects') ?? '#' }}"
                            class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">Lihat
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
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-3">
                                        <div class="font-semibold text-slate-800 truncate max-w-[150px]">
                                            {{ $project->project_name }}</div>
                                        <div class="text-xs text-slate-500 truncate max-w-[150px]">
                                            {{ $project->client->name ?? 'Unknown' }}</div>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ ucfirst($project->framework) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        @php
                                            $hostingStatusColors = [
                                                'active' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                                'suspended' => 'bg-red-100 text-red-700 border-red-200',
                                                'building' => 'bg-blue-100 text-blue-700 border-blue-200',
                                            ];
                                            $hColorClass =
                                                $hostingStatusColors[$project->status] ??
                                                'bg-slate-100 text-slate-700 border-slate-200';
                                        @endphp
                                        <span
                                            class="px-2.5 py-1 rounded-md text-xs font-medium border {{ $hColorClass }}">
                                            {{ ucfirst($project->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-slate-500">Belum ada proyek
                                        hosting.</td>
                                </tr>
                            @endforelse
                        </x-ui.table>
                    </div>
                </div>

            </div>

            <!-- Recent Users Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-user-plus text-blue-500"></i>
                        Pendaftar Klien Terbaru
                    </h2>
                    <a href="{{ route('superadmin.users.index') }}"
                        class="text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors">Kelola Pengguna
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
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-800 flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-200 text-indigo-700 flex items-center justify-center font-bold text-sm uppercase shadow-sm">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    {{ $user->name }}
                                </td>
                                <td class="px-6 py-4">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    @if ($user->role == 'user_joki')
                                        <span
                                            class="px-3 py-1.5 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-600 border border-indigo-100">
                                            Jasa Joki Code
                                        </span>
                                    @elseif($user->role == 'user_hosting')
                                        <span
                                            class="px-3 py-1.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                            App Deployment
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                            {{ ucfirst(str_replace('_', ' ', $user->role ?? 'User')) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-slate-500">{{ $user->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('superadmin.users.show', $user->hashid) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 transition-all shadow-sm"
                                        title="Detail Profil">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i class="fa-regular fa-folder-open text-3xl text-slate-300"></i>
                                        <p>Belum ada pengguna yang mendaftar.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </x-ui.table>
                </div>
            </div>

        </div>
    </x-ui.page-layout>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Line Chart: User Registrations
        var optionsRegistrations = {
            series: [{
                name: 'Pengguna Baru',
                data: @json($chartUserRegistrations['series'])
            }],
            chart: {
                type: 'line',
                height: 300,
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            colors: ['#3b82f6'],
            stroke: { curve: 'smooth', width: 3 },
            xaxis: {
                categories: @json($chartUserRegistrations['labels']),
                tooltip: { enabled: false }
            },
            yaxis: {
                labels: { formatter: function(val) { return Math.round(val); } }
            },
            dataLabels: { enabled: false },
            tooltip: {
                theme: 'light',
                y: { formatter: function (val) { return val + " Pengguna" } }
            }
        };
        new ApexCharts(document.querySelector("#chart-user-registrations"), optionsRegistrations).render();

        // 2. Pie Chart: User Roles
        var optionsRoles = {
            series: @json($chartUserRoles['series']),
            chart: {
                type: 'donut',
                height: 300,
                fontFamily: 'inherit'
            },
            labels: @json($chartUserRoles['labels']),
            colors: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
            dataLabels: { enabled: false },
            plotOptions: {
                pie: {
                    donut: { size: '70%' },
                    expandOnClick: false
                }
            },
            legend: {
                position: 'bottom'
            }
        };
        new ApexCharts(document.querySelector("#chart-user-roles"), optionsRoles).render();

        // 3. Bar Chart: Revenue
        var optionsRevenue = {
            series: [{
                name: 'Jasa Joki Code',
                data: @json($chartRevenue['joki'])
            }, {
                name: 'Hosting & Deployment',
                data: @json($chartRevenue['hosting'])
            }],
            chart: {
                type: 'bar',
                height: 350,
                stacked: true,
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            colors: ['#4f46e5', '#10b981'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 4,
                    columnWidth: '40%'
                },
            },
            xaxis: {
                categories: @json($chartRevenue['labels']),
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return "Rp " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    }
                }
            },
            dataLabels: { enabled: false },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "Rp " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left'
            }
        };
        new ApexCharts(document.querySelector("#chart-revenue"), optionsRevenue).render();
    });
</script>
@endpush
