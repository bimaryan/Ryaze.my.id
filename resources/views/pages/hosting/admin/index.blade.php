@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- ── 1. ADMIN HOSTING – Dashboard Manajemen ────────────────────── --}}
        <x-ui.page-header 
            title="Manajemen Hosting" 
            icon="fa-solid fa-server">
            <x-slot:subtitle>
                Halo Admin <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ Auth::user()->name ?? '' }}</span>. Berikut status server hari ini.
            </x-slot:subtitle>
            <x-slot:actions>
                <span class="text-sm text-slate-400 dark:text-slate-500 sm:text-right shrink-0">{{ now()->format('d M Y, H:i') }} WIB</span>
            </x-slot:actions>
        </x-ui.page-header>

        {{-- ══ FLASH MESSAGE ══════════════════════════════════════════ --}}
        @if (session('error'))
            <div class="mt-4 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/40 text-red-800 dark:text-red-200 rounded-xl text-sm flex items-center gap-3">
                <i class="fa-solid fa-circle-xmark text-red-500 dark:text-red-400"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- ══ KARTU STATISTIK ════════════════════════════════════════ --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <!-- Total Project -->
            <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4 transition-colors hover:border-indigo-300 dark:hover:border-indigo-500/40">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-indigo-50 text-indigo-600 border border-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20">
                    <i class="fa-solid fa-layer-group text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5 truncate">Total Project</p>
                    <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($stats['total_projects']) }}</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1 mt-1 truncate">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-indigo-500"></span> Keseluruhan website terdaftar
                    </p>
                </div>
            </div>

            <!-- Aktif -->
            <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4 transition-colors hover:border-emerald-300 dark:hover:border-emerald-500/40">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5 truncate">Hosting Aktif</p>
                    <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($stats['active_projects']) }}</h3>
                    <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1 mt-1 truncate">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Layanan berjalan normal
                    </p>
                </div>
            </div>

            <!-- Total Klien -->
            <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4 transition-colors hover:border-blue-300 dark:hover:border-blue-500/40">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20">
                    <i class="fa-solid fa-users text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5 truncate">Total Klien</p>
                    <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($stats['total_clients']) }}</h3>
                    <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1 mt-1 truncate">
                        <i class="fa-solid fa-arrow-trend-up text-[9px]"></i> Klien aktif hosting
                    </p>
                </div>
            </div>

            <!-- Database -->
            <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4 transition-colors hover:border-purple-300 dark:hover:border-purple-500/40">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-purple-50 text-purple-600 border border-purple-100 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20">
                    <i class="fa-solid fa-database text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5 truncate">Total Database</p>
                    <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($stats['total_databases']) }}</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1 mt-1 truncate">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-purple-500"></span> MySQL & NoSQL database
                    </p>
                </div>
            </div>

            <!-- Tagihan Pending -->
            <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4 transition-colors hover:border-amber-300 dark:hover:border-amber-500/40">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-amber-50 text-amber-600 border border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20">
                    <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5 truncate">Tagihan Pending</p>
                    <h3 class="text-xl sm:text-2xl font-bold {{ $stats['pending_billing'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-900 dark:text-white' }} tracking-tight">{{ number_format($stats['pending_billing']) }}</h3>
                    <p class="text-[11px] font-medium flex items-center gap-1 mt-1 truncate {{ $stats['pending_billing'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-500 dark:text-slate-400' }}">
                        <span class="inline-block w-1.5 h-1.5 rounded-full {{ $stats['pending_billing'] > 0 ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                        {{ $stats['pending_billing'] > 0 ? 'Menunggu verifikasi pembayaran' : 'Semua tagihan lunas' }}
                    </p>
                </div>
            </div>

            <!-- Sedang Build -->
            <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4 transition-colors hover:border-sky-300 dark:hover:border-sky-500/40">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-sky-50 text-sky-600 border border-sky-100 dark:bg-sky-500/10 dark:text-sky-400 dark:border-sky-500/20">
                    <i class="fa-solid fa-gears text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5 truncate">Sedang Build</p>
                    <h3 class="text-xl sm:text-2xl font-bold {{ $stats['building_now'] > 0 ? 'text-sky-600 dark:text-sky-400' : 'text-slate-900 dark:text-white' }} tracking-tight">{{ number_format($stats['building_now']) }}</h3>
                    <p class="text-[11px] font-medium flex items-center gap-1 mt-1 truncate {{ $stats['building_now'] > 0 ? 'text-sky-600 dark:text-sky-400' : 'text-slate-500 dark:text-slate-400' }}">
                        <span class="inline-block w-1.5 h-1.5 rounded-full {{ $stats['building_now'] > 0 ? 'bg-sky-500 animate-pulse' : 'bg-slate-400' }}"></span>
                        {{ $stats['building_now'] > 0 ? 'Deployment aktif dalam proses' : 'Tidak ada antrean build' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ══ CHARTS SECTION ══════════════════════════════════════════ --}}
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Line Chart: Tren Tagihan -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-4">Tren Pendapatan Hosting (6 Bulan Terakhir)</h3>
                <div id="chart-hosting-billings"></div>
            </div>

            <!-- Pie Chart: Status Proyek -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-4">Distribusi Status Proyek</h3>
                <div id="chart-project-status" class="flex justify-center"></div>
            </div>

            <!-- Bar Chart: Proyek Baru -->
            <div class="lg:col-span-3 bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-4">Proyek Hosting Baru (6 Bulan Terakhir)</h3>
                <div id="chart-new-projects"></div>
            </div>
        </div>

        {{-- ══ NAVIGASI MENU (PENGGANTI TABEL) ════════════════════════════ --}}
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Card 1: Membutuhkan Tindakan --}}
            <a href="{{ route('admin_hosting.pending') }}"
                class="group bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800 p-6 hover:shadow-md hover:border-amber-300 dark:hover:border-amber-500/40 transition-all block relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-triangle-exclamation text-6xl text-amber-500 dark:text-amber-400"></i>
                </div>
                <div class="relative z-10">
                    <div
                        class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-500 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20 flex items-center justify-center text-xl mb-4">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-1">Membutuhkan Tindakan</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Project yang butuh aktivasi, suspend, atau perbaikan error.</p>
                    <span
                        class="inline-flex items-center gap-1.5 text-sm font-semibold text-amber-600 dark:text-amber-300 group-hover:text-amber-700">
                        Kelola {{ $stats['action_required'] }} Antrean <i class="fa-solid fa-arrow-right text-xs"></i>
                    </span>
                </div>
            </a>

            {{-- Card 2: Deploy Terbaru --}}
            <a href="{{ route('admin_hosting.deployments') }}"
                class="group bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800 p-6 hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-500/40 transition-all block relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-rocket text-6xl text-indigo-500 dark:text-indigo-400"></i>
                </div>
                <div class="relative z-10">
                    <div
                        class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-500 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20 flex items-center justify-center text-xl mb-4">
                        <i class="fa-solid fa-rocket"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-1">Riwayat Deployment</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Pantau log dan status build dari project klien secara real-time.
                    </p>
                    <span
                        class="inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 dark:text-indigo-400 group-hover:text-indigo-700">
                        Lihat Log Build <i class="fa-solid fa-arrow-right text-xs"></i>
                    </span>
                </div>
            </a>

            {{-- Card 3: Semua Project --}}
            <a href="{{ route('admin_hosting.projects') }}"
                class="group bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800 p-6 hover:shadow-md hover:border-emerald-300 dark:hover:border-emerald-500/40 transition-all block relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-server text-6xl text-emerald-500 dark:text-emerald-400"></i>
                </div>
                <div class="relative z-10">
                    <div
                        class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-500 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20 flex items-center justify-center text-xl mb-4">
                        <i class="fa-solid fa-server"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-1">Semua Project Hosting</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Akses tabel master seluruh data website klien, domain, dan paket.
                    </p>
                    <span
                        class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-600 dark:text-emerald-300 group-hover:text-emerald-700">
                        Kelola {{ $stats['total_projects'] }} Project <i class="fa-solid fa-arrow-right text-xs"></i>
                    </span>
                </div>
            </a>

        </div>

<script nonce="{{ csp_nonce() }}">
(function() {
        // 1. Line Chart: Hosting Revenue (Billings)
        var optionsBillings = {
            series: [{
                name: 'Pendapatan',
                data: @json($chartBillings['series'])
            }],
            chart: {
                type: 'line',
                height: 300,
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            colors: ['#10b981'],
            stroke: { curve: 'smooth', width: 3 },
            xaxis: {
                categories: @json($chartBillings['labels']),
                tooltip: { enabled: false }
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
                theme: 'light',
                y: {
                    formatter: function (val) {
                        return "Rp " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    }
                }
            }
        };
        new ApexCharts(document.querySelector("#chart-hosting-billings"), optionsBillings).render();

        // 2. Pie Chart: Project Status
        var optionsStatus = {
            series: @json($chartProjectStatus['series']),
            chart: {
                type: 'pie',
                height: 300,
                fontFamily: 'inherit'
            },
            labels: @json($chartProjectStatus['labels']),
            colors: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#64748b'],
            dataLabels: { enabled: false },
            legend: {
                position: 'bottom'
            }
        };
        new ApexCharts(document.querySelector("#chart-project-status"), optionsStatus).render();

        // 3. Bar Chart: New Projects
        var optionsProjects = {
            series: [{
                name: 'Proyek Baru',
                data: @json($chartNewProjects['series'])
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            colors: ['#3b82f6'],
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '40%'
                },
            },
            xaxis: {
                categories: @json($chartNewProjects['labels']),
            },
            yaxis: {
                labels: { formatter: function(val) { return Math.round(val); } }
            },
            dataLabels: { enabled: false },
            tooltip: {
                y: { formatter: function (val) { return val + " Proyek" } }
            }
        };
        new ApexCharts(document.querySelector("#chart-new-projects"), optionsProjects).render();
})();
</script>

    </x-ui.page-layout>
@endsection
