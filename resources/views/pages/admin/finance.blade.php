@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Laporan Keuangan"
            subtitle="Laporan detail pendapatan, diskon, dan breakdown per layanan. Hanya transaksi berstatus Lunas (paid) sesuai rentang tanggal."
            icon="fa-solid fa-chart-pie">
            <x-slot:actions>
                <div class="inline-flex items-center px-4 py-2 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-500/30 rounded-lg font-bold text-sm">
                    <i class="fa-solid fa-wallet me-2"></i>
                    Net: Rp{{ number_format($totalRevenue, 0, ',', '.') }}
                </div>
            </x-slot:actions>
        </x-ui.page-header>

        @php
            $presets = [
                'bulan_ini'  => ['label' => 'Bulan Ini',   'from' => now()->startOfMonth()->format('Y-m-d'), 'to' => now()->format('Y-m-d')],
                'bulan_lalu' => ['label' => 'Bulan Lalu',  'from' => now()->startOfMonth()->subMonth()->format('Y-m-d'), 'to' => now()->startOfMonth()->subDay()->format('Y-m-d')],
                'tahun_ini'  => ['label' => 'Tahun Ini',   'from' => now()->startOfYear()->format('Y-m-d'),  'to' => now()->format('Y-m-d')],
                'semua'      => ['label' => 'Semua Waktu', 'from' => '', 'to' => ''],
            ];
            $currentFrom = $start->format('Y-m-d');
            $currentTo   = $end->format('Y-m-d');
        @endphp

        <div class="mt-6 space-y-6">

            {{-- FILTER --}}
            <div class="bg-white dark:bg-slate-800/60 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                <form method="GET" action="{{ route('superadmin.finance') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-2">Dari Tanggal</label>
                        <input type="date" name="from" value="{{ $currentFrom }}" class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-2">Sampai Tanggal</label>
                        <input type="date" name="to" value="{{ $currentTo }}" class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-2">Layanan</label>
                        <select name="service" class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            <option value="all"     @selected($service==='all')>Joki + Hosting</option>
                            <option value="joki"    @selected($service==='joki')>Joki Code</option>
                            <option value="hosting" @selected($service==='hosting')>Hosting</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-2">Metode Pembayaran</label>
                        <select name="method" class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            <option value="">Semua Metode</option>
                            @foreach ($availableMethods as $m)
                                <option value="{{ $m }}" @selected($method===$m)>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-4 flex flex-wrap items-center gap-2">
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition">
                            <i class="fa-solid fa-filter me-2"></i> Terapkan Filter
                        </button>
                        <a href="{{ route('superadmin.finance') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-bold rounded-xl transition">
                            <i class="fa-solid fa-rotate-left me-2"></i> Reset
                        </a>
                        <span class="mx-1 text-slate-300 dark:text-slate-400">|</span>
                        @foreach ($presets as $preset)
                            @php $active = ($preset['from'] ?? '') === $currentFrom && ($preset['to'] ?? '') === $currentTo; @endphp
                            <a href="{{ route('superadmin.finance', array_filter(['from' => $preset['from'], 'to' => $preset['to']])) }}"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold border transition {{ $active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-indigo-300 hover:text-indigo-600' }}">
                                {{ $preset['label'] }}
                            </a>
                        @endforeach
                    </div>
                </form>
            </div>

            {{-- INFO BAR --}}
            <div class="bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/30 rounded-xl px-5 py-3 text-sm text-indigo-800 dark:text-indigo-300 flex items-start gap-3">
                <i class="fa-solid fa-circle-info mt-0.5"></i>
                <div>
                    <span class="font-bold">Periode:</span>
                    {{ $start->translatedFormat('d M Y') }} — {{ $end->translatedFormat('d M Y') }} &middot;
                    <span class="font-bold">{{ number_format($totalCount) }}</span> transaksi lunas &middot;
                    Gross Rp{{ number_format($totalGross, 0, ',', '.') }} &minus; Diskon Rp{{ number_format($totalDiscount, 0, ',', '.') }} = <span class="font-bold">Net Rp{{ number_format($totalRevenue, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- STATS CARDS ROW 1 --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Total Net --}}
                <div class="bg-white dark:bg-slate-800/60 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 relative overflow-hidden">
                    <div class="absolute -right-3 -top-3 w-20 h-20 bg-sky-50 dark:bg-sky-500/10 rounded-full"></div>
                    <div class="relative z-10">
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Total Net</p>
                        <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                        <p class="text-xs text-slate-400 mt-1">{{ $totalCount }} transaksi</p>
                    </div>
                </div>
                {{-- Gross sebelum diskon --}}
                <div class="bg-white dark:bg-slate-800/60 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 relative overflow-hidden">
                    <div class="absolute -right-3 -top-3 w-20 h-20 bg-violet-50 dark:bg-violet-500/10 rounded-full"></div>
                    <div class="relative z-10">
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Gross (Sebelum Diskon)</p>
                        <h3 class="text-xl font-bold text-violet-700 dark:text-violet-300">Rp {{ number_format($totalGross, 0, ',', '.') }}</h3>
                        <p class="text-xs text-slate-400 mt-1">Harga normal tanpa potongan</p>
                    </div>
                </div>
                {{-- Total Diskon --}}
                <div class="bg-white dark:bg-slate-800/60 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 relative overflow-hidden">
                    <div class="absolute -right-3 -top-3 w-20 h-20 bg-rose-50 dark:bg-rose-500/10 rounded-full"></div>
                    <div class="relative z-10">
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Total Diskon</p>
                        <h3 class="text-xl font-bold text-rose-600 dark:text-rose-400">- Rp {{ number_format($totalDiscount, 0, ',', '.') }}</h3>
                        <p class="text-xs text-slate-400 mt-1">
                            <span class="text-amber-600 dark:text-amber-400 font-semibold">{{ $voucherCount }}x voucher</span>
                            &middot; <span class="text-teal-600 dark:text-teal-400 font-semibold">{{ $freeCount }}x gratis</span>
                        </p>
                    </div>
                </div>
                {{-- Diskon Voucher --}}
                <div class="bg-white dark:bg-slate-800/60 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 relative overflow-hidden">
                    <div class="absolute -right-3 -top-3 w-20 h-20 bg-amber-50 dark:bg-amber-500/10 rounded-full"></div>
                    <div class="relative z-10">
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Diskon Voucher</p>
                        <h3 class="text-xl font-bold text-amber-600 dark:text-amber-400">Rp {{ number_format($totalVoucherDiscount, 0, ',', '.') }}</h3>
                        <p class="text-xs text-slate-400 mt-1">{{ $voucherCount }} transaksi pakai voucher</p>
                    </div>
                </div>
            </div>

            {{-- STATS CARDS ROW 2 --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-800/60 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 relative overflow-hidden">
                    <div class="absolute -right-3 -top-3 w-20 h-20 bg-indigo-50 dark:bg-indigo-500/10 rounded-full"></div>
                    <div class="relative z-10">
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Pendapatan Joki</p>
                        <h3 class="text-xl font-bold text-indigo-700 dark:text-indigo-300">Rp {{ number_format($jokiRevenue, 0, ',', '.') }}</h3>
                        <p class="text-xs text-slate-400 mt-1">{{ $jokiCount }} transaksi</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800/60 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 relative overflow-hidden">
                    <div class="absolute -right-3 -top-3 w-20 h-20 bg-emerald-50 dark:bg-emerald-500/10 rounded-full"></div>
                    <div class="relative z-10">
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Pendapatan Hosting</p>
                        <h3 class="text-xl font-bold text-emerald-700 dark:text-emerald-300">Rp {{ number_format($hostingRevenue, 0, ',', '.') }}</h3>
                        <p class="text-xs text-slate-400 mt-1">{{ $hostingCount }} transaksi</p>
                    </div>
                </div>
                {{-- Breakdown per plan --}}
                @foreach($planBreakdown->take(2) as $planName => $pd)
                <div class="bg-white dark:bg-slate-800/60 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Paket {{ ucfirst($planName) }}</p>
                    <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100">Rp {{ number_format($pd['revenue'], 0, ',', '.') }}</h3>
                    <p class="text-xs text-slate-400 mt-1">
                        {{ $pd['count'] }}x &middot;
                        @if($pd['discount'] > 0)
                            <span class="text-rose-500">-Rp{{ number_format($pd['discount'], 0, ',', '.') }} diskon</span>
                        @else
                            tanpa diskon
                        @endif
                    </p>
                </div>
                @endforeach
            </div>

            {{-- CHART + DISTRIBUSI --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white dark:bg-slate-800/60 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-4">Perbandingan Pendapatan Joki vs Hosting (12 Bulan Terakhir)</h3>
                    <div id="chart-finance-monthly"></div>
                </div>
                <div class="space-y-4">
                    {{-- Metode pembayaran --}}
                    <div class="bg-white dark:bg-slate-800/60 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-3">Metode Pembayaran</h3>
                        <div id="chart-finance-methods" class="flex justify-center mb-3"></div>
                        <div class="space-y-2">
                            @forelse($methods as $m => $data)
                                <div class="flex justify-between text-xs border-b border-slate-100 dark:border-slate-700 pb-1.5">
                                    <span class="font-semibold text-slate-600 dark:text-slate-300 truncate max-w-[110px]">{{ $m }}</span>
                                    <div class="text-right shrink-0">
                                        <span class="font-bold text-slate-800 dark:text-slate-100">Rp {{ number_format($data['total'], 0, ',', '.') }}</span>
                                        <span class="text-slate-400 ml-1">({{ $data['count'] }}x)</span>
                                        @if($data['discount'] > 0)
                                            <br><span class="text-rose-400 text-[10px]">diskon -Rp{{ number_format($data['discount'], 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 text-center">Belum ada data.</p>
                            @endforelse
                        </div>
                    </div>
                    {{-- Breakdown paket hosting --}}
                    @if($planBreakdown->isNotEmpty())
                    <div class="bg-white dark:bg-slate-800/60 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-3">Breakdown Paket Hosting</h3>
                        <div class="space-y-2">
                            @foreach($planBreakdown as $planName => $pd)
                            <div class="flex items-start justify-between text-xs border-b border-slate-100 dark:border-slate-700 pb-2">
                                <div>
                                    <span class="font-bold text-slate-700 dark:text-slate-200">Paket {{ ucfirst($planName) }}</span>
                                    <br><span class="text-slate-400">{{ $pd['count'] }}x &middot; Gross Rp{{ number_format($pd['gross'], 0, ',', '.') }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($pd['revenue'], 0, ',', '.') }}</span>
                                    @if($pd['discount'] > 0)
                                        <br><span class="text-rose-400">-Rp{{ number_format($pd['discount'], 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- TABEL TRANSAKSI --}}
            <div>
                <x-ui.table>
                    <x-slot:head>
                        <th class="px-4 py-4 text-xs">Waktu Lunas</th>
                        <th class="px-4 py-4 text-xs">Invoice</th>
                        <th class="px-4 py-4 text-xs">Klien</th>
                        <th class="px-4 py-4 text-xs">Layanan / Paket</th>
                        <th class="px-4 py-4 text-xs">Metode</th>
                        <th class="px-4 py-4 text-xs text-right">Harga Normal</th>
                        <th class="px-4 py-4 text-xs text-right">Diskon</th>
                        <th class="px-4 py-4 text-xs text-right">Dibayar</th>
                    </x-slot:head>
                    @forelse ($transactions as $row)
                        <tr class="hover:bg-slate-50 dark:bg-slate-800/50 dark:hover:bg-slate-700/40 transition-colors">
                            {{-- Waktu --}}
                            <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400 font-mono whitespace-nowrap">
                                {{ $row['paid_at'] ? \Carbon\Carbon::parse($row['paid_at'])->format('d M Y') : '-' }}<br>
                                <span class="text-slate-400">{{ $row['paid_at'] ? \Carbon\Carbon::parse($row['paid_at'])->format('H:i') : '' }}</span>
                            </td>
                            {{-- Invoice --}}
                            <td class="px-4 py-3">
                                <span class="text-xs font-mono font-semibold text-slate-700 dark:text-slate-200 block">{{ $row['invoice'] }}</span>
                                @if(!empty($row['invoice_type']))
                                    <span class="text-[10px] text-slate-400">{{ $row['invoice_type'] }}</span>
                                @endif
                            </td>
                            {{-- Klien --}}
                            <td class="px-4 py-3">
                                <span class="text-sm font-medium text-slate-800 dark:text-slate-100 block">{{ $row['client'] }}</span>
                                <span class="text-[10px] text-slate-400">{{ $row['client_email'] }}</span>
                            </td>
                            {{-- Layanan / Paket + badge --}}
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center gap-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold whitespace-nowrap {{ $row['source'] === 'joki' ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-500/30' : 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30' }}">
                                        {{ $row['source'] === 'joki' ? 'Joki' : 'Hosting' }}
                                    </span>
                                    @if($row['discount_type'] === 'voucher')
                                        <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-[10px] font-bold whitespace-nowrap bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">
                                            <i class="fa-solid fa-tag text-[9px]"></i> Voucher
                                        </span>
                                    @elseif($row['discount_type'] === 'free')
                                        <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-[10px] font-bold whitespace-nowrap bg-teal-50 dark:bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-500/30">
                                            <i class="fa-solid fa-gift text-[9px]"></i> Gratis
                                        </span>
                                    @elseif($row['discount_type'] === 'wallet')
                                        <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-[10px] font-bold whitespace-nowrap bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30">
                                            <i class="fa-solid fa-wallet text-[9px]"></i> Wallet
                                        </span>
                                    @endif
                                </div>
                                <span class="text-xs text-slate-600 dark:text-slate-300 mt-1 block">{{ $row['detail'] }}</span>
                            </td>
                            {{-- Metode --}}
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium bg-slate-100 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200 px-2 py-1 rounded whitespace-nowrap border border-slate-200 dark:border-slate-700">
                                    {{ $row['method'] }}
                                </span>
                            </td>
                            {{-- Harga Normal --}}
                            <td class="px-4 py-3 text-right font-mono text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                Rp{{ number_format($row['original_price'], 0, ',', '.') }}
                            </td>
                            {{-- Diskon --}}
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                @if($row['discount_amount'] > 0)
                                    <span class="font-mono text-xs font-bold text-rose-600 dark:text-rose-400 block">
                                        -Rp{{ number_format($row['discount_amount'], 0, ',', '.') }}
                                    </span>
                                    <span class="text-[10px] text-rose-400">
                                        @if(!empty($row['discount_label'])){{ $row['discount_label'] }}@else-{{ $row['discount_pct'] }}%@endif
                                    </span>
                                @else
                                    <span class="text-xs text-slate-300 dark:text-slate-600">—</span>
                                @endif
                            </td>
                            {{-- Dibayar --}}
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                @if($row['amount'] > 0)
                                    <span class="font-mono text-sm font-bold text-emerald-600 dark:text-emerald-300">
                                        Rp{{ number_format($row['amount'], 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-xs italic text-slate-400 dark:text-slate-500">Rp 0</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-slate-400 dark:text-slate-500">
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
                        chart: { type: 'bar', height: 300, fontFamily: 'Inter, sans-serif', stacked: false, toolbar: { show: false } },
                        series: [
                            { name: 'Pendapatan Joki',    data: @json($chartJoki) },
                            { name: 'Pendapatan Hosting', data: @json($chartHosting) }
                        ],
                        xaxis: { categories: @json($chartMonths), labels: { style: { fontSize: '11px' } } },
                        colors: ['#6366f1', '#10b981'],
                        dataLabels: { enabled: false },
                        stroke: { show: true, width: 2, colors: ['transparent'] },
                        plotOptions: { bar: { horizontal: false, columnWidth: '50%', borderRadius: 4 } },
                        yaxis: { labels: { formatter: val => 'Rp ' + val.toLocaleString('id-ID') } },
                        tooltip: { y: { formatter: val => 'Rp ' + val.toLocaleString('id-ID') } }
                    }).render();
                }

                var chartMethodsEl = document.querySelector("#chart-finance-methods");
                @if($methods->isNotEmpty())
                if (chartMethodsEl) {
                    new ApexCharts(chartMethodsEl, {
                        chart: { type: 'donut', height: 200, fontFamily: 'Inter, sans-serif' },
                        series: @json($methods->pluck('total')->values()),
                        labels: @json($methods->keys()->values()),
                        dataLabels: { enabled: false },
                        legend: { position: 'bottom', fontSize: '11px' },
                        tooltip: { y: { formatter: val => 'Rp ' + val.toLocaleString('id-ID') } }
                    }).render();
                }
                @endif
            })();
            </script>
        </div>
    </x-ui.page-layout>
@endsection
