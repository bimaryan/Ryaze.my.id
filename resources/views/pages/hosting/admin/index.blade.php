@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- ── 1. ADMIN HOSTING – Dashboard Manajemen ────────────────────── --}}
        <x-ui.page-header 
            title="Manajemen Hosting" 
            icon="fa-solid fa-server">
            <x-slot:subtitle>
                Halo Admin <span class="font-semibold text-indigo-600">{{ Auth::user()->name ?? '' }}</span>. Berikut status server hari ini.
            </x-slot:subtitle>
            <x-slot:actions>
                <span class="text-sm text-slate-400 sm:text-right shrink-0">{{ now()->format('d M Y, H:i') }} WIB</span>
            </x-slot:actions>
        </x-ui.page-header>

        {{-- ══ FLASH MESSAGE ══════════════════════════════════════════ --}}
        @if (session('error'))
            <div class="mt-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm flex items-center gap-3">
                <i class="fa-solid fa-circle-xmark text-red-500"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- ══ KARTU STATISTIK ════════════════════════════════════════ --}}
        <div class="mt-6 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <x-ui.card class="p-5">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500">Total Project</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['total_projects'] }}</h3>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card class="p-5">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500">Aktif</p>
                        <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['active_projects'] }}</h3>
                    </div>
                    <div
                        class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-check"></i>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card class="p-5">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500">Total Klien</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['total_clients'] }}</h3>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card class="p-5">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500">Database</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['total_databases'] }}</h3>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-database"></i>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card class="p-5">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500">Tagihan Pending</p>
                        <h3
                            class="text-2xl font-bold {{ $stats['pending_billing'] > 0 ? 'text-amber-600' : 'text-slate-800' }} mt-1">
                            {{ $stats['pending_billing'] }}
                        </h3>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card class="p-5">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500">Sedang Build</p>
                        <h3
                            class="text-2xl font-bold {{ $stats['building_now'] > 0 ? 'text-blue-600' : 'text-slate-800' }} mt-1">
                            {{ $stats['building_now'] }}
                        </h3>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-sky-50 text-sky-500 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-gears"></i>
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- ══ CHARTS SECTION ══════════════════════════════════════════ --}}
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Line Chart: Tren Tagihan -->
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Tren Pendapatan Hosting (6 Bulan Terakhir)</h3>
                <div id="chart-hosting-billings"></div>
            </div>

            <!-- Pie Chart: Status Proyek -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Distribusi Status Proyek</h3>
                <div id="chart-project-status" class="flex justify-center"></div>
            </div>

            <!-- Bar Chart: Proyek Baru -->
            <div class="lg:col-span-3 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Proyek Hosting Baru (6 Bulan Terakhir)</h3>
                <div id="chart-new-projects"></div>
            </div>
        </div>

        {{-- ══ NAVIGASI MENU (PENGGANTI TABEL) ════════════════════════════ --}}
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Card 1: Membutuhkan Tindakan --}}
            <a href="{{ route('admin_hosting.pending') }}"
                class="group bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:shadow-md hover:border-amber-300 transition-all block relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-triangle-exclamation text-6xl text-amber-500"></i>
                </div>
                <div class="relative z-10">
                    <div
                        class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center text-xl mb-4">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1">Membutuhkan Tindakan</h3>
                    <p class="text-sm text-slate-500 mb-4">Project yang butuh aktivasi, suspend, atau perbaikan error.</p>
                    <span
                        class="inline-flex items-center gap-1.5 text-sm font-semibold text-amber-600 group-hover:text-amber-700">
                        Kelola {{ $stats['action_required'] }} Antrean <i class="fa-solid fa-arrow-right text-xs"></i>
                    </span>
                </div>
            </a>

            {{-- Card 2: Deploy Terbaru --}}
            <a href="{{ route('admin_hosting.deployments') }}"
                class="group bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:shadow-md hover:border-indigo-300 transition-all block relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-rocket text-6xl text-indigo-500"></i>
                </div>
                <div class="relative z-10">
                    <div
                        class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center text-xl mb-4">
                        <i class="fa-solid fa-rocket"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1">Riwayat Deployment</h3>
                    <p class="text-sm text-slate-500 mb-4">Pantau log dan status build dari project klien secara real-time.
                    </p>
                    <span
                        class="inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 group-hover:text-indigo-700">
                        Lihat Log Build <i class="fa-solid fa-arrow-right text-xs"></i>
                    </span>
                </div>
            </a>

            {{-- Card 3: Semua Project --}}
            <a href="{{ route('admin_hosting.projects') }}"
                class="group bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:shadow-md hover:border-emerald-300 transition-all block relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-server text-6xl text-emerald-500"></i>
                </div>
                <div class="relative z-10">
                    <div
                        class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl mb-4">
                        <i class="fa-solid fa-server"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1">Semua Project Hosting</h3>
                    <p class="text-sm text-slate-500 mb-4">Akses tabel master seluruh data website klien, domain, dan paket.
                    </p>
                    <span
                        class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-600 group-hover:text-emerald-700">
                        Kelola {{ $stats['total_projects'] }} Project <i class="fa-solid fa-arrow-right text-xs"></i>
                    </span>
                </div>
            </a>

        </div>
    </x-ui.page-layout>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
    });
</script>
@endpush
