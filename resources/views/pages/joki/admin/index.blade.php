@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Manajemen Joki Code"
            eyebrow="Admin Joki · Command Center"
            status="ONLINE"
            icon="fa-solid fa-gauge">
            <x-slot:subtitle>
                Semangat ngoding, <span class="font-semibold text-[#1D5B96] dark:text-[#6BA8DC]">{{ Auth::user()->name ?? 'Dev' }}</span>! Berikut antrean pekerjaanmu.
            </x-slot:subtitle>
        </x-ui.page-header>

        <div class="mt-6">
            <div class="ui-grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                <div class="ui-cell p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="ui-eyebrow-muted">Pesanan Baru · Pending</p>
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    </div>
                    <h3 class="ui-stat-value text-2xl sm:text-3xl">{{ $pendingOrders }}</h3>
                    <p class="ui-stat-label">Menunggu diambil</p>
                </div>
                <div class="ui-cell p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="ui-eyebrow-muted">Sedang Dikerjakan</p>
                        <span class="w-1.5 h-1.5 rounded-full bg-[#1D5B96] dark:bg-[#6BA8DC]"></span>
                    </div>
                    <h3 class="ui-stat-value text-2xl sm:text-3xl">{{ $progressOrders }}</h3>
                    <p class="ui-stat-label">Aktif dalam pengerjaan</p>
                </div>
                <div class="ui-cell p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="ui-eyebrow-muted">Menunggu Review Klien</p>
                        <span class="w-1.5 h-1.5 rounded-full bg-[#7FB4E2]"></span>
                    </div>
                    <h3 class="ui-stat-value text-2xl sm:text-3xl">{{ $reviewOrders }}</h3>
                    <p class="ui-stat-label">Dikonfirmasi klien</p>
                </div>
                <div class="ui-cell p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="ui-eyebrow-muted">Proyek Selesai · Bulan Ini</p>
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    </div>
                    <h3 class="ui-stat-value text-2xl sm:text-3xl">{{ $completedOrders }}</h3>
                    <p class="ui-stat-label"><i class="fa-solid fa-arrow-trend-up text-[9px]"></i> Bulan berjalan</p>
                </div>
            </div>

            {{-- ══ CHARTS SECTION ══════════════════════════════════════════ --}}
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Line Chart: Selesai -->
                <div class="lg:col-span-2 ui-panel">
                    <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#0F1A2A]">
                        <h3 class="ui-eyebrow text-[11px]">Tren Pesanan Selesai</h3>
                        <span class="ui-eyebrow-muted">6 Bulan Terakhir</span>
                    </div>
                    <div class="p-6">
                        <div id="chart-completed-orders"></div>
                    </div>
                </div>

                <!-- Pie Chart: Status -->
                <div class="ui-panel">
                    <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#0F1A2A]">
                        <h3 class="ui-eyebrow text-[11px]">Distribusi Status</h3>
                    </div>
                    <div class="p-6">
                        <div id="chart-order-status" class="flex justify-center"></div>
                    </div>
                </div>

                <!-- Bar Chart: Baru -->
                <div class="lg:col-span-3 ui-panel">
                    <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#0F1A2A]">
                        <h3 class="ui-eyebrow text-[11px]">Pesanan Joki Baru</h3>
                        <span class="ui-eyebrow-muted">6 Bulan Terakhir</span>
                    </div>
                    <div class="p-6">
                        <div id="chart-new-orders"></div>
                    </div>
                </div>
            </div>

            {{-- Tabel Antrean --}}
            <div class="mt-6 ui-panel">
                <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#0F1A2A]">
                    <h2 class="ui-eyebrow text-[11px]">Antrean Pekerjaan Aktif</h2>
                    <span class="ui-status"><span class="ui-status-dot"></span> {{ $queueOrders->count() }} Item</span>
                </div>
                <x-ui.table class="border-0 shadow-none">
                    <x-slot:head>
                        <th class="px-6 py-4">Klien & Order ID</th>
                        <th class="px-6 py-4">Proyek</th>
                        <th class="px-6 py-4">Status & Progres</th>
                        <th class="px-6 py-4 text-center">Tenggat Waktu</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </x-slot:head>
                            @forelse ($queueOrders as $order)
                                <tr class="hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828]">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-[#0F1A2A] dark:text-white">{{ $order->client->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-[#737373] dark:text-white/45 font-mono mt-0.5">{{ $order->order_number }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-[#0F1A2A] dark:text-white">{{ $order->project_name }}</div>
                                        <div class="text-xs text-[#737373] dark:text-white/45 mt-0.5">{{ $order->tech_stack }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusColors = [
                                                'pending' => 'text-amber-700 border-amber-200 dark:text-amber-400 dark:border-amber-500/40',
                                                'progress' => 'text-[#1D5B96] border-[#C9D5E2] dark:text-[#6BA8DC] dark:border-[#1D5B96]/40',
                                                'review' => 'text-[#2A6FAD] border-[#C9D5E2] dark:text-[#7FB4E2] dark:border-[#1D5B96]/40'
                                            ];
                                            $color = $statusColors[$order->status] ?? 'text-[#525252] border-[#E3EAF1] dark:text-white/60 dark:border-[#1B2C44]';
                                        @endphp
                                        <span class="ui-badge {{ $color }}">
                                            {{ $order->status }}
                                        </span>
                                        @if($order->status == 'progress')
                                            <div class="mt-2 w-full bg-[#E3EAF1] dark:bg-[#1B2C44] h-1.5">
                                                <div class="bg-[#1D5B96] dark:bg-[#6BA8DC] h-1.5" style="width: {{ $order->progress }}%"></div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="ui-badge {{ \Carbon\Carbon::parse($order->deadline)->isPast() ? 'text-rose-700 border-rose-200 dark:text-rose-400 dark:border-rose-500/40' : '' }}">
                                            {{ \Carbon\Carbon::parse($order->deadline)->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('admin_joki.orders.edit', $order->hashid) }}" class="ui-icon-btn ui-icon-btn-primary">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-[#94A3B4] dark:text-white/35 font-mono text-[11px] uppercase tracking-[0.2em]">Tidak ada antrean aktif saat ini.</td>
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
        colors: ['#f59e0b', '#1D5B96', '#7FB4E2', '#10b981', '#e11d48'],
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
        colors: ['#1D5B96'],
        plotOptions: {
            bar: {
                borderRadius: 0,
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
