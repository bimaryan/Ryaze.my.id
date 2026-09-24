@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Transaksi Produk" 
            subtitle="Riwayat pembelian produk digital." 
            icon="fa-solid fa-receipt">
            <x-slot:actions>
                <div class="flex gap-2">
                    <a href="{{ route('superadmin.digital_products.index') }}"
                        class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold py-2.5 px-4 rounded-lg shadow-md transition-colors text-sm">
                        <i class="fa-solid fa-box-open mr-1.5"></i> Produk
                    </a>
                    <a href="{{ route('superadmin.digital_purchases.index') }}{{ request()->status ? '?status=' . request()->status : '' }}"
                        class="px-3 py-2.5 rounded-lg text-sm font-medium border transition {{ !request('status') ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300' }}">Semua</a>
                    <a href="{{ route('superadmin.digital_purchases.index', ['status' => 'paid']) }}"
                        class="px-3 py-2.5 rounded-lg text-sm font-medium border transition {{ request('status') === 'paid' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300' }}">Lunas</a>
                    <a href="{{ route('superadmin.digital_purchases.index', ['status' => 'pending']) }}"
                        class="px-3 py-2.5 rounded-lg text-sm font-medium border transition {{ request('status') === 'pending' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300' }}">Pending</a>
                </div>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card class="mt-6">
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Jumlah</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Dibayar</th>
                    </tr>
                </x-slot:head>

                @forelse($purchases as $p)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors">
                        <td class="px-6 py-4 text-xs font-mono text-slate-500 dark:text-slate-400">{{ $p->order_id }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $p->product?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-slate-700 dark:text-slate-200">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusClass = match($p->status) {
                                    'paid' => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300',
                                    'pending' => 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300',
                                    default => 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300',
                                };
                            @endphp
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded {{ $statusClass }}">{{ $p->status }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $p->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $p->paid_at?->format('d M Y H:i') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-receipt text-3xl mb-3 text-slate-300 dark:text-slate-500"></i>
                            <p>Belum ada transaksi.</p>
                        </td>
                    </tr>
                @endforelse
            </x-ui.table>

            <div class="p-4">
                {{ $purchases->links() }}
            </div>
        </x-ui.card>
    </x-ui.page-layout>
@endsection
