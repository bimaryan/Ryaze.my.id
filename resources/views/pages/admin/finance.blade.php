@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Laporan Keuangan"
            subtitle="Pendapatan dihitung dari seluruh pembayaran berstatus Lunas (paid) sesuai rentang tanggal terpilih."
            icon="fa-solid fa-chart-pie">
            <x-slot:actions>
                <div class="inline-flex items-center px-4 py-2 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-500/30 rounded-lg font-bold">
                    <i class="fa-solid fa-wallet me-2"></i>
                    Total Terpilih: Rp{{ number_format($totalRevenue, 0, ',', '.') }}
                </div>
            </x-slot:actions>
        </x-ui.page-header>

        @php
            $presets = [
                'bulan_ini' => ['label' => 'Bulan Ini', 'from' => now()->startOfMonth()->format('Y-m-d'), 'to' => now()->format('Y-m-d')],
                'bulan_lalu' => ['label' => 'Bulan Lalu', 'from' => now()->startOfMonth()->subMonth()->format('Y-m-d'), 'to' => now()->startOfMonth()->subDay()->format('Y-m-d')],
                'tahun_ini' => ['label' => 'Tahun Ini', 'from' => now()->startOfYear()->format('Y-m-d'), 'to' => now()->format('Y-m-d')],
                'semua' => ['label' => 'Semua Waktu', 'from' => '', 'to' => ''],
            ];
            $currentFrom = $start->format('Y-m-d');
            $currentTo = $end->format('Y-m-d');
        @endphp

        <div class="mt-6 space-y-6">
            <div class="bg-white dark:bg-slate-800/60 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                <form method="GET" action="{{ route('superadmin.finance') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-2">Dari Tanggal</label>
                        <input type="date" name="from" value="{{ $currentFrom }}"
                            class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-2">Sampai Tanggal</label>
                        <input type="date" name="to" value="{{ $currentTo }}"
                            class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-2">Layanan</label>
                        <select name="service"
                            class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            <option value="all" @selected($service === 'all')>Joki + Hosting</option>
                            <option value="joki" @selected($service === 'joki')>Joki Code</option>
                            <option value="hosting" @selected($service === 'hosting')>Hosting</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-2">Metode Pembayaran</label>
                        <select name="method"
                            class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            <option value="">Semua Metode</option>
                            @foreach ($availableMethods as $m)
                                <option value="{{ $m }}" @selected($method === $m)>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-4 flex flex-wrap items-center gap-2">
                        <button type="submit"
                            class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition">
                            <i class="fa-solid fa-filter me-2"></i> Terapkan Filter
                        </button>
                        <a href="{{ route('superadmin.finance') }}"
                            class="inline-flex items-center px-4 py-2.5 bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-bold rounded-xl transition">
                            <i class="fa-solid fa-rotate-left me-2"></i> Reset
                        </a>
                        <span class="mx-1 text-slate-300 dark:text-slate-400">|</span>
                        @foreach ($presets as $key => $preset)
                            @php
                                $active = ($preset['from'] ?? '') === $currentFrom && ($preset['to'] ?? '') === $currentTo;
                            @endphp
                            <a href="{{ route('superadmin.finance', array_filter(['from' => $preset['from'], 'to' => $preset['to']])) }}"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold border transition {{ $active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-indigo-300 hover:text-indigo-600 dark:hover:text-indigo-400 dark:hover:text-indigo-400' }}">
                                {{ $preset['label'] }}
                            </a>
                        @endforeach
                    </div>
                </form>
            </div>

            <div class="bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/30 rounded-xl px-5 py-3 text-sm text-indigo-800 dark:text-indigo-300 flex items-start gap-3">
                <i class="fa-solid fa-circle-info mt-0.5"></i>
                <div>
                    <span class="font-bold">Transparansi perhitungan:</span> angka di bawah dihitung dari daftar transaksi pada tabel di halaman ini (pembayaran berstatus <span class="font-bold">Lunas</span> antara
                    {{ $start->translatedFormat('d M Y') }} — {{ $end->translatedFormat('d M Y') }}). Total keseluruhan = jumlah nominal semua baris tabel.
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-slate-800/60 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-sky-50 dark:bg-sky-500/10 rounded-full"></div>
                    <div class="relative z-10">
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Total Pendapatan</p>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ number_format($totalCount) }} transaksi lunas</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800/60 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50 dark:bg-indigo-500/10 rounded-full"></div>
                    <div class="relative z-10">
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Pendapatan Joki Code</p>
                        <h3 class="text-2xl font-bold text-indigo-700 dark:text-indigo-300">Rp {{ number_format($jokiRevenue, 0, ',', '.') }}</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ number_format($jokiCount) }} transaksi lunas</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800/60 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 dark:bg-emerald-500/10 rounded-full"></div>
                    <div class="relative z-10">
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Pendapatan Hosting</p>
                        <h3 class="text-2xl font-bold text-emerald-700 dark:text-emerald-300">Rp {{ number_format($hostingRevenue, 0, ',', '.') }}</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ number_format($hostingCount) }} transaksi lunas</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800/60 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 dark:bg-amber-500/10 rounded-full"></div>
                    <div class="relative z-10">
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Transaksi Per Metode</p>
                        <div class="space-y-1.5 mt-1">
                            @forelse($methods->take(3) as $m => $data)
                                <div class="flex justify-between text-sm">
                                    <span class="font-medium text-slate-600 dark:text-slate-300">{{ $m }}</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-100">Rp {{ number_format($data['total'], 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <p class="text-sm text-slate-400 dark:text-slate-500">Belum ada data.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white dark:bg-slate-800/60 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4">Perbandingan Pendapatan Joki vs Hosting (12 Bulan Terakhir)</h3>
                    <div id="chart-finance-monthly"></div>
                </div>
                <div class="bg-white dark:bg-slate-800/60 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4">Distribusi Metode Pembayaran</h3>
                    <div id="chart-finance-methods" class="flex justify-center"></div>
                    <div class="mt-4 space-y-2">
                        @forelse($methods as $m => $data)
                            <div class="flex justify-between text-sm border-b border-slate-50 pb-1.5">
                                <span class="font-medium text-slate-600 dark:text-slate-300">{{ $m }}</span>
                                <span class="text-slate-500 dark:text-slate-400">{{ number_format($data['count']) }}x &middot; <span class="font-bold text-slate-800 dark:text-slate-100">Rp {{ number_format($data['total'], 0, ',', '.') }}</span></span>
                            </div>
                        @empty
                            <p class="text-sm text-slate-400 dark:text-slate-500 text-center">Belum ada data.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div>
                <x-ui.table>
                    <x-slot:head>
                        <th class="px-6 py-4">Waktu Lunas</th>
                        <th class="px-6 py-4">Invoice</th>
                        <th class="px-6 py-4">Klien / User</th>
                        <th class="px-6 py-4">Layanan</th>
                        <th class="px-6 py-4">Metode</th>
                        <th class="px-6 py-4 text-right">Nominal</th>
                    </x-slot:head>
                    @forelse ($transactions as $row)
                        <tr class="hover:bg-slate-50 dark:bg-slate-800/50 dark:hover:bg-slate-700/40 transition-colors">
                            <td class="px-6 py-4 text-xs text-slate-500 dark:text-slate-400 font-mono">
                                {{ $row['paid_at'] ? \Carbon\Carbon::parse($row['paid_at'])->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-xs font-mono font-semibold text-slate-700 dark:text-slate-200">
                                {{ $row['invoice'] }}
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800 dark:text-slate-100">
                                {{ $row['client'] }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold {{ $row['source'] === 'joki' ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-500/30' : 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-500/30' }}">
                                    {{ $row['source'] === 'joki' ? 'Joki' : 'Hosting' }}
                                </span>
                                <span class="text-xs text-slate-500 dark:text-slate-400 ms-2">{{ $row['detail'] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-medium bg-slate-100 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200 px-2.5 py-1 rounded-md border border-slate-200 dark:border-slate-700">
                                    {{ $row['method'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-mono font-medium text-emerald-600 dark:text-emerald-300">
                                + Rp{{ number_format($row['amount'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-400 dark:text-slate-500">
                                Tidak ada transaksi lunas pada rentang tanggal tersebut.
                            </td>
                        </tr>
                    @endforelse
                    <x-slot:pagination>
                        {{ $transactions->links() }}
                    </x-slot:pagination>
                </x-ui.table>
            </div>

            <script nonce="{{ csp_nonce() }}">
            (function() {
                var chartMonthlyEl = document.querySelector("#chart-finance-monthly");
                if (chartMonthlyEl) {
                    new ApexCharts(chartMonthlyEl, {
                        chart: { type: 'bar', height: 320, fontFamily: 'Inter, sans-serif', stacked: false, toolbar: { show: false } },
                        series: [
                            { name: 'Pendapatan Joki', data: @json($chartJoki) },
                            { name: 'Pendapatan Hosting', data: @json($chartHosting) }
                        ],
                        xaxis: { categories: @json($chartMonths) },
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
                    }).render();
                }

                var chartMethodsEl = document.querySelector("#chart-finance-methods");
                @if($methods->isNotEmpty())
                if (chartMethodsEl) {
                    new ApexCharts(chartMethodsEl, {
                        chart: { type: 'donut', height: 300, fontFamily: 'Inter, sans-serif' },
                        series: @json($methods->pluck('total')->values()),
                        labels: @json($methods->keys()->values()),
                        dataLabels: { enabled: false },
                        legend: { position: 'bottom', fontSize: '12px' },
                        tooltip: {
                            y: {
                                formatter: function (val) {
                                    return "Rp " + val.toLocaleString('id-ID');
                                }
                            }
                        }
                    }).render();
                }
                @endif
            })();
            </script>
        </div>
    </x-ui.page-layout>
@endsection
