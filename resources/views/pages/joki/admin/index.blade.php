@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Manajemen Joki Code" 
            icon="fa-solid fa-gauge">
            <x-slot:subtitle>
                Semangat ngoding, <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ Auth::user()->name ?? 'Dev' }}</span>! Berikut antrean pekerjaanmu.
            </x-slot:subtitle>
        </x-ui.page-header>

        <div class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-amber-50 text-amber-600 border border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20">
                        <i class="fa-solid fa-inbox text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5 truncate">Pesanan Baru (Pending)</p>
                        <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ $pendingOrders }}</h3>
                        <p class="text-[11px] text-amber-600 dark:text-amber-400 font-medium flex items-center gap-1 mt-1 truncate">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu diambil
                        </p>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20">
                        <i class="fa-solid fa-code text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5 truncate">Sedang Dikerjakan</p>
                        <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ $progressOrders }}</h3>
                        <p class="text-[11px] text-blue-600 dark:text-blue-400 font-medium flex items-center gap-1 mt-1 truncate">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-500"></span> Aktif dalam pengerjaan
                        </p>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-purple-50 text-purple-600 border border-purple-100 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20">
                        <i class="fa-solid fa-eye text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5 truncate">Menunggu Review Klien</p>
                        <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ $reviewOrders }}</h3>
                        <p class="text-[11px] text-purple-600 dark:text-purple-400 font-medium flex items-center gap-1 mt-1 truncate">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-purple-500"></span> Dikonfirmasi klien
                        </p>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20">
                        <i class="fa-solid fa-circle-check text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5 truncate">Proyek Selesai (Bulan ini)</p>
                        <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ $completedOrders }}</h3>
                        <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1 mt-1 truncate">
                            <i class="fa-solid fa-arrow-trend-up text-[9px]"></i> Bulan berjalan
                        </p>
                    </div>
                </div>
            </div>

            {{-- ══ CHARTS SECTION ══════════════════════════════════════════ --}}
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Line Chart: Selesai -->
                <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-4">Tren Pesanan Selesai (6 Bulan Terakhir)</h3>
                    <div id="chart-completed-orders"></div>
                </div>

                <!-- Pie Chart: Status -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-4">Distribusi Status Pesanan</h3>
                    <div id="chart-order-status" class="flex justify-center"></div>
                </div>

                <!-- Bar Chart: Baru -->
                <div class="lg:col-span-3 bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-4">Pesanan Joki Baru (6 Bulan Terakhir)</h3>
                    <div id="chart-new-orders"></div>
                </div>
            </div>

            {{-- Tabel Antrean --}}
            <div class="mt-8 bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                    <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Antrean Pekerjaan Aktif</h2>
                </div>
                <x-ui.table class="border-0 shadow-none rounded-none">
                    <x-slot:head>
                        <th class="px-6 py-4">Klien & Order ID</th>
                        <th class="px-6 py-4">Proyek</th>
                        <th class="px-6 py-4">Status & Progres</th>
                        <th class="px-6 py-4 text-center">Tenggat Waktu</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </x-slot:head>
                            @forelse ($queueOrders as $order)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-800 dark:text-slate-100">{{ $order->client->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-0.5">{{ $order->order_number }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-slate-800 dark:text-slate-100">{{ $order->project_name }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $order->tech_stack }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-amber-50 text-amber-700 border border-amber-200/80 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
                                                'progress' => 'bg-blue-50 text-blue-700 border border-blue-200/80 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20',
                                                'review' => 'bg-purple-50 text-purple-700 border border-purple-200/80 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20'
                                            ];
                                            $color = $statusColors[$order->status] ?? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700';
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $color }} uppercase">
                                            {{ $order->status }}
                                        </span>
                                        @if($order->status == 'progress')
                                            <div class="mt-2 w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5">
                                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $order->progress }}%"></div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-xs font-medium px-2 py-1 rounded-lg {{ \Carbon\Carbon::parse($order->deadline)->isPast() ? 'bg-rose-50 text-rose-700 border border-rose-200/80 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700' }}">
                                            {{ \Carbon\Carbon::parse($order->deadline)->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('admin_joki.orders.edit', $order->hashid) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-500/20">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-slate-400 dark:text-slate-500">Tidak ada antrean aktif saat ini.</td>
                                </tr>
                            @endforelse
                </x-ui.table>
            </div>
        </div>

<script nonce="{{ csp_nonce() }}">
(function() {
    // 1. Line Chart: Completed Orders
    var optionsCompleted = {
        series: [{
            name: 'Pesanan Selesai',
            data: @json($chartCompletedOrders['series'])
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
            categories: @json($chartCompletedOrders['labels']),
            tooltip: { enabled: false }
        },
        yaxis: {
            labels: { formatter: function(val) { return Math.round(val); } }
        },
        dataLabels: { enabled: false }
    };
    new ApexCharts(document.querySelector("#chart-completed-orders"), optionsCompleted).render();

    // 2. Pie Chart: Order Status
    var optionsStatus = {
        series: @json($chartOrderStatus['series']),
        chart: {
            type: 'pie',
            height: 300,
            fontFamily: 'inherit'
        },
        labels: @json($chartOrderStatus['labels']),
        colors: ['#f59e0b', '#3b82f6', '#8b5cf6', '#10b981', '#ef4444'],
        dataLabels: { enabled: false },
        legend: {
            position: 'bottom'
        }
    };
    new ApexCharts(document.querySelector("#chart-order-status"), optionsStatus).render();

    // 3. Bar Chart: New Orders
    var optionsNewOrders = {
        series: [{
            name: 'Pesanan Baru',
            data: @json($chartNewOrders['series'])
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        colors: ['#6366f1'],
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '40%'
            },
        },
        xaxis: {
            categories: @json($chartNewOrders['labels']),
        },
        yaxis: {
            labels: { formatter: function(val) { return Math.round(val); } }
        },
        dataLabels: { enabled: false }
    };
    new ApexCharts(document.querySelector("#chart-new-orders"), optionsNewOrders).render();
})();
</script>

    </x-ui.page-layout>
@endsection
