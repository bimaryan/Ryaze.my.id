@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Laporan Keuangan"
            eyebrow="Superadmin · Keuangan"
            subtitle="Laporan detail pendapatan, diskon, dan breakdown per layanan. Hanya transaksi berstatus Lunas (paid) sesuai rentang tanggal."
            icon="fa-solid fa-chart-pie">
            <x-slot:actions>
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-[#F0F6FC] dark:bg-[#0D1828] text-[#1D5B96] dark:text-[#6BA8DC] border border-[#C9D5E2] dark:border-[#1B2C44] font-mono text-xs font-bold uppercase tracking-[0.15em]">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
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
            <div class="ui-panel">
                <div class="ui-panel-head">
                    <h3 class="ui-modal-title">Filter Laporan</h3>
                    <span class="ui-status">Rentang & Layanan</span>
                </div>
                <form method="GET" action="{{ route('superadmin.finance') }}" class="p-6 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="ui-eyebrow-muted block mb-2">Dari Tanggal</label>
                        <input type="date" name="from" value="{{ $currentFrom }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm font-mono focus:border-[#1D5B96] outline-none transition">
                    </div>
                    <div>
                        <label class="ui-eyebrow-muted block mb-2">Sampai Tanggal</label>
                        <input type="date" name="to" value="{{ $currentTo }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm font-mono focus:border-[#1D5B96] outline-none transition">
                    </div>
                    <div>
                        <label class="ui-eyebrow-muted block mb-2">Layanan</label>
                        <select name="service" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition">
                            <option value="all"     @selected($service==='all')>Joki + Hosting</option>
                            <option value="joki"    @selected($service==='joki')>Joki Code</option>
                            <option value="hosting" @selected($service==='hosting')>Hosting</option>
                        </select>
                    </div>
                    <div>
                        <label class="ui-eyebrow-muted block mb-2">Metode Pembayaran</label>
                        <select name="method" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition">
                            <option value="">Semua Metode</option>
                            @foreach ($availableMethods as $m)
                                <option value="{{ $m }}" @selected($method===$m)>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-4 flex flex-wrap items-center gap-2">
                        <button type="submit" class="ui-btn-primary">
                            <i class="fa-solid fa-filter me-2 text-xs"></i> Terapkan Filter
                        </button>
                        <a href="{{ route('superadmin.finance') }}" class="ui-btn-secondary">
                            <i class="fa-solid fa-rotate-left me-2 text-xs"></i> Reset
                        </a>
                        <span class="mx-1 text-[#C9D5E2] dark:text-white/20">|</span>
                        @foreach ($presets as $preset)
                            @php $active = ($preset['from'] ?? '') === $currentFrom && ($preset['to'] ?? '') === $currentTo; @endphp
                            <a href="{{ route('superadmin.finance', array_filter(['from' => $preset['from'], 'to' => $preset['to']])) }}"
                                class="px-3 py-1.5 text-xs font-bold font-mono uppercase tracking-[0.15em] border transition {{ $active ? 'bg-[#1D5B96] text-white border-[#1D5B96]' : 'bg-white dark:bg-[#0F1D30] text-[#525252] dark:text-white/60 border-[#C9D5E2] dark:border-[#2A4463] hover:border-[#1D5B96] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC]' }}">
                                {{ $preset['label'] }}
                            </a>
                        @endforeach
                    </div>
                </form>
            </div>

            {{-- INFO BAR --}}
            <div class="bg-[#F0F6FC] dark:bg-[#0D1828] border border-[#C9D5E2] dark:border-[#1B2C44] px-5 py-3 text-sm text-[#0F1A2A] dark:text-white/70 flex items-start gap-3">
                <i class="fa-solid fa-circle-info mt-0.5 text-[#1D5B96] dark:text-[#6BA8DC]"></i>
                <div>
                    <span class="font-bold">Periode:</span>
                    {{ $start->translatedFormat('d M Y') }} — {{ $end->translatedFormat('d M Y') }} &middot;
                    <span class="font-bold">{{ number_format($totalCount) }}</span> transaksi lunas &middot;
                    Gross Rp{{ number_format($totalGross, 0, ',', '.') }} &minus; Diskon Rp{{ number_format($totalDiscount, 0, ',', '.') }} = <span class="font-bold text-[#1D5B96] dark:text-[#6BA8DC]">Net Rp{{ number_format($totalRevenue, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- STATS CARDS ROW 1 --}}
            <div class="ui-grid grid-cols-2 lg:grid-cols-4">
                {{-- Total Net --}}
                <div class="ui-cell p-5">
                    <p class="ui-stat-label mb-1">Total Net</p>
                    <h3 class="ui-stat-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                    <p class="text-xs text-[#94A3B4] dark:text-white/35 font-mono mt-1.5">{{ $totalCount }} transaksi</p>
                </div>
                {{-- Gross sebelum diskon --}}
                <div class="ui-cell p-5">
                    <p class="ui-stat-label mb-1">Gross (Sebelum Diskon)</p>
                    <h3 class="ui-stat-value">Rp {{ number_format($totalGross, 0, ',', '.') }}</h3>
                    <p class="text-xs text-[#94A3B4] dark:text-white/35 mt-1.5">Harga normal tanpa potongan</p>
                </div>
                {{-- Total Diskon --}}
                <div class="ui-cell p-5">
                    <p class="ui-stat-label mb-1">Total Diskon</p>
                    <h3 class="ui-stat-value text-rose-600 dark:text-rose-400">- Rp {{ number_format($totalDiscount, 0, ',', '.') }}</h3>
                    <p class="text-xs text-[#94A3B4] dark:text-white/35 font-mono mt-1.5">
                        <span class="text-amber-600 dark:text-amber-400 font-semibold">{{ $voucherCount }}x voucher</span>
                        &middot; <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $freeCount }}x gratis</span>
                    </p>
                </div>
                {{-- Diskon Voucher --}}
                <div class="ui-cell p-5">
                    <p class="ui-stat-label mb-1">Diskon Voucher</p>
                    <h3 class="ui-stat-value text-amber-600 dark:text-amber-400">Rp {{ number_format($totalVoucherDiscount, 0, ',', '.') }}</h3>
                    <p class="text-xs text-[#94A3B4] dark:text-white/35 font-mono mt-1.5">{{ $voucherCount }} transaksi pakai voucher</p>
                </div>
            </div>

            {{-- STATS CARDS ROW 2 --}}
            <div class="ui-grid grid-cols-2 lg:grid-cols-4">
                <div class="ui-cell p-5">
                    <p class="ui-stat-label mb-1">Pendapatan Joki</p>
                    <h3 class="ui-stat-value text-[#1D5B96] dark:text-[#6BA8DC]">Rp {{ number_format($jokiRevenue, 0, ',', '.') }}</h3>
                    <p class="text-xs text-[#94A3B4] dark:text-white/35 font-mono mt-1.5">{{ $jokiCount }} transaksi</p>
                </div>
                <div class="ui-cell p-5">
                    <p class="ui-stat-label mb-1">Pendapatan Hosting</p>
                    <h3 class="ui-stat-value text-emerald-600 dark:text-emerald-400">Rp {{ number_format($hostingRevenue, 0, ',', '.') }}</h3>
                    <p class="text-xs text-[#94A3B4] dark:text-white/35 font-mono mt-1.5">{{ $hostingCount }} transaksi</p>
                </div>
                {{-- Breakdown per plan --}}
                @foreach($planBreakdown->take(2) as $planName => $pd)
                <div class="ui-cell p-5">
                    <p class="ui-stat-label mb-1">Paket {{ ucfirst($planName) }}</p>
                    <h3 class="ui-stat-value">Rp {{ number_format($pd['revenue'], 0, ',', '.') }}</h3>
                    <p class="text-xs text-[#94A3B4] dark:text-white/35 font-mono mt-1.5">
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
                <div class="ui-panel lg:col-span-2">
                    <div class="ui-panel-head">
                        <h3 class="ui-modal-title">Perbandingan Pendapatan Joki vs Hosting (12 Bulan Terakhir)</h3>
                        <span class="ui-status">12 Bulan</span>
                    </div>
                    <div class="p-6">
                        <div id="chart-finance-monthly"></div>
                    </div>
                </div>
                <div class="space-y-6">
                    {{-- Metode pembayaran --}}
                    <div class="ui-panel">
                        <div class="ui-panel-head">
                            <h3 class="ui-modal-title">Metode Pembayaran</h3>
                        </div>
                        <div class="p-5">
                            <div id="chart-finance-methods" class="flex justify-center mb-3"></div>
                            <div class="space-y-2">
                                @forelse($methods as $m => $data)
                                    <div class="flex justify-between text-xs border-b border-[#E3EAF1] dark:border-[#1B2C44] pb-1.5">
                                        <span class="font-semibold text-[#525252] dark:text-white/60 truncate max-w-[110px] font-mono">{{ $m }}</span>
                                        <div class="text-right shrink-0 font-mono">
                                            <span class="font-bold text-[#0F1A2A] dark:text-white">Rp {{ number_format($data['total'], 0, ',', '.') }}</span>
                                            <span class="text-[#94A3B4] dark:text-white/35 ml-1">({{ $data['count'] }}x)</span>
                                            @if($data['discount'] > 0)
                                                <br><span class="text-rose-400 text-[10px]">diskon -Rp{{ number_format($data['discount'], 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-[#94A3B4] dark:text-white/35 text-center font-mono uppercase tracking-[0.2em]">Belum ada data.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    {{-- Breakdown paket hosting --}}
                    @if($planBreakdown->isNotEmpty())
                    <div class="ui-panel">
                        <div class="ui-panel-head">
                            <h3 class="ui-modal-title">Breakdown Paket Hosting</h3>
                        </div>
                        <div class="p-5">
                            <div class="space-y-2">
                                @foreach($planBreakdown as $planName => $pd)
                                <div class="flex items-start justify-between text-xs border-b border-[#E3EAF1] dark:border-[#1B2C44] pb-2">
                                    <div>
                                        <span class="font-bold text-[#0F1A2A] dark:text-white">Paket {{ ucfirst($planName) }}</span>
                                        <br><span class="text-[#94A3B4] dark:text-white/35 font-mono">{{ $pd['count'] }}x &middot; Gross Rp{{ number_format($pd['gross'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="text-right font-mono">
                                        <span class="font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($pd['revenue'], 0, ',', '.') }}</span>
                                        @if($pd['discount'] > 0)
                                            <br><span class="text-rose-400">-Rp{{ number_format($pd['discount'], 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
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
                        <tr class="hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828]">
                            {{-- Waktu --}}
                            <td class="px-4 py-3 text-xs text-[#737373] dark:text-white/40 font-mono whitespace-nowrap">
                                {{ $row['paid_at'] ? \Carbon\Carbon::parse($row['paid_at'])->format('d M Y') : '-' }}<br>
                                <span class="text-[#94A3B4] dark:text-white/30">{{ $row['paid_at'] ? \Carbon\Carbon::parse($row['paid_at'])->format('H:i') : '' }}</span>
                            </td>
                            {{-- Invoice --}}
                            <td class="px-4 py-3">
                                <span class="text-xs font-mono font-semibold text-[#1D5B96] dark:text-[#6BA8DC] block">{{ $row['invoice'] }}</span>
                                @if(!empty($row['invoice_type']))
                                    <span class="text-[10px] text-[#94A3B4] dark:text-white/35 font-mono uppercase tracking-wider">{{ $row['invoice_type'] }}</span>
                                @endif
                            </td>
                            {{-- Klien --}}
                            <td class="px-4 py-3">
                                <span class="text-sm font-medium text-[#0F1A2A] dark:text-white block">{{ $row['client'] }}</span>
                                <span class="text-[10px] text-[#94A3B4] dark:text-white/35">{{ $row['client_email'] }}</span>
                            </td>
                            {{-- Layanan / Paket + badge --}}
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center gap-1">
                                    <span class="ui-badge {{ $row['source'] === 'joki' ? 'ui-badge-primary' : 'ui-badge-success' }}">
                                        {{ $row['source'] === 'joki' ? 'Joki' : 'Hosting' }}
                                    </span>
                                    @if($row['discount_type'] === 'voucher')
                                        <span class="ui-badge ui-badge-warning">
                                            <i class="fa-solid fa-tag text-[9px]"></i> Voucher
                                        </span>
                                    @elseif($row['discount_type'] === 'free')
                                        <span class="ui-badge ui-badge-success">
                                            <i class="fa-solid fa-gift text-[9px]"></i> Gratis
                                        </span>
                                    @elseif($row['discount_type'] === 'wallet')
                                        <span class="ui-badge ui-badge-info">
                                            <i class="fa-solid fa-wallet text-[9px]"></i> Wallet
                                        </span>
                                    @endif
                                    @if(isset($row['discount_type']) && $row['discount_type'] === 'promo')
                                        <span class="ui-badge ui-badge-primary">
                                            <i class="fa-solid fa-percent text-[9px]"></i> Promo
                                        </span>
                                    @endif
                                </div>
                                <span class="text-xs text-[#525252] dark:text-white/60 mt-1 block">{{ $row['detail'] }}</span>
                            </td>
                            {{-- Metode --}}
                            <td class="px-4 py-3">
                                <span class="ui-badge font-mono whitespace-nowrap">
                                    {{ $row['method'] }}
                                </span>
                            </td>
                            {{-- Harga Normal --}}
                            <td class="px-4 py-3 text-right font-mono text-xs text-[#737373] dark:text-white/40 whitespace-nowrap">
                                Rp{{ number_format($row['original_price'], 0, ',', '.') }}
                            </td>
                            {{-- Diskon --}}
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                @if($row['discount_amount'] > 0)
                                    <span class="font-mono text-xs font-bold text-rose-600 dark:text-rose-400 block">
                                        -Rp{{ number_format($row['discount_amount'], 0, ',', '.') }}
                                    </span>
                                    <span class="text-[10px] text-rose-400 font-mono">
                                        @if(!empty($row['discount_label'])){{ $row['discount_label'] }}@else-{{ $row['discount_pct'] }}%@endif
                                    </span>
                                @else
                                    <span class="text-xs text-[#C9D5E2] dark:text-white/20">—</span>
                                @endif
                            </td>
                            {{-- Dibayar --}}
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                @if($row['amount'] > 0)
                                    <span class="font-mono text-sm font-bold text-emerald-600 dark:text-emerald-400">
                                        Rp{{ number_format($row['amount'], 0, ',', '.') }}
                                    </span>
                                    @if(!empty($row['admin_fee']) && $row['admin_fee'] > 0)
                                        <span class="text-[10px] text-[#94A3B4] dark:text-white/35 font-mono block">incl. fee Rp{{ number_format($row['admin_fee'], 0, ',', '.') }}</span>
                                    @endif
                                @else
                                    <span class="text-xs italic text-[#94A3B4] dark:text-white/35">Rp 0</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-[#94A3B4] dark:text-white/35 font-mono text-[11px] uppercase tracking-[0.2em]">
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
                        chart: { type: 'bar', height: 300, fontFamily: 'inherit', stacked: false, toolbar: { show: false } },
                        series: [
                            { name: 'Pendapatan Joki',    data: @json($chartJoki) },
                            { name: 'Pendapatan Hosting', data: @json($chartHosting) }
                        ],
                        xaxis: { categories: @json($chartMonths), labels: { style: { fontSize: '11px' } } },
                        colors: ['#1D5B96', '#10b981'],
                        dataLabels: { enabled: false },
                        stroke: { show: true, width: 2, colors: ['transparent'] },
                        plotOptions: { bar: { horizontal: false, columnWidth: '50%', borderRadius: 0 } },
                        yaxis: { labels: { formatter: val => 'Rp ' + val.toLocaleString('id-ID') } },
                        tooltip: { y: { formatter: val => 'Rp ' + val.toLocaleString('id-ID') } }
                    }).render();
                }

                var chartMethodsEl = document.querySelector("#chart-finance-methods");
                @if($methods->isNotEmpty())
                if (chartMethodsEl) {
                    new ApexCharts(chartMethodsEl, {
                        chart: { type: 'donut', height: 200, fontFamily: 'inherit' },
                        series: @json($methods->pluck('total')->values()),
                        labels: @json($methods->keys()->values()),
                        colors: ['#1D5B96', '#7FB4E2', '#10b981', '#f59e0b', '#94A3B4'],
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
