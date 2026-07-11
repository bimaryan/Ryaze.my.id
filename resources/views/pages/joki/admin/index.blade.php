@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Manajemen Joki Code" 
            icon="fa-solid fa-gauge">
            <x-slot:subtitle>
                Semangat ngoding, <span class="font-semibold text-indigo-600">{{ Auth::user()->name ?? 'Dev' }}</span>! Berikut antrean pekerjaanmu.
            </x-slot:subtitle>
        </x-ui.page-header>

        <div class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 border-t-4 border-t-amber-500">
                    <p class="text-sm font-medium text-slate-500">Pesanan Baru (Pending)</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $pendingOrders }}</h3>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 border-t-4 border-t-blue-500">
                    <p class="text-sm font-medium text-slate-500">Sedang Dikerjakan</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $progressOrders }}</h3>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 border-t-4 border-t-purple-500">
                    <p class="text-sm font-medium text-slate-500">Menunggu Review Klien</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $reviewOrders }}</h3>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 border-t-4 border-t-emerald-500">
                    <p class="text-sm font-medium text-slate-500">Proyek Selesai (Bulan ini)</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $completedOrders }}</h3>
                </div>
            </div>

            {{-- ══ CHARTS SECTION ══════════════════════════════════════════ --}}
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Line Chart: Selesai -->
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Tren Pesanan Selesai (6 Bulan Terakhir)</h3>
                    <div id="chart-completed-orders"></div>
                </div>

                <!-- Pie Chart: Status -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Distribusi Status Pesanan</h3>
                    <div id="chart-order-status" class="flex justify-center"></div>
                </div>

                <!-- Bar Chart: Baru -->
                <div class="lg:col-span-3 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Pesanan Joki Baru (6 Bulan Terakhir)</h3>
                    <div id="chart-new-orders"></div>
                </div>
            </div>

            {{-- Tabel Antrean --}}
            <div class="mt-8">
                <h2 class="text-lg font-bold text-slate-800 mb-4 px-1">Antrean Pekerjaan Aktif</h2>
                <x-ui.table>
                    <x-slot:head>
                        <th class="px-6 py-4">Klien & Order ID</th>
                        <th class="px-6 py-4">Proyek</th>
                        <th class="px-6 py-4">Status & Progres</th>
                        <th class="px-6 py-4 text-center">Tenggat Waktu</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </x-slot:head>
                            @forelse ($queueOrders as $order)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-800">{{ $order->client->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-slate-500 font-mono mt-0.5">{{ $order->order_number }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-slate-800">{{ $order->project_name }}</div>
                                        <div class="text-xs text-slate-500 mt-0.5">{{ $order->tech_stack }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-amber-100 text-amber-700',
                                                'progress' => 'bg-blue-100 text-blue-700',
                                                'review' => 'bg-purple-100 text-purple-700'
                                            ];
                                            $color = $statusColors[$order->status] ?? 'bg-slate-100 text-slate-700';
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $color }} uppercase">
                                            {{ $order->status }}
                                        </span>
                                        @if($order->status == 'progress')
                                            <div class="mt-2 w-full bg-slate-200 rounded-full h-1.5">
                                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $order->progress }}%"></div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-xs font-medium px-2 py-1 rounded-lg {{ \Carbon\Carbon::parse($order->deadline)->isPast() ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700' }}">
                                            {{ \Carbon\Carbon::parse($order->deadline)->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('admin_joki.orders.edit', $order->hashid) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-slate-400">Tidak ada antrean aktif saat ini.</td>
                                </tr>
                            @endforelse
                </x-ui.table>
            </div>
        </div>
    </x-ui.page-layout>

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
