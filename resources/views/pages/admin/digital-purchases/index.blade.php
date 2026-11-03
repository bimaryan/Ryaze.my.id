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
                        class="bg-[#F0F6FC] dark:bg-[#0D1828] hover:bg-[#E8F0F9] dark:hover:bg-[#1D5B96] text-[#0F1A2A] dark:text-white font-bold py-2.5 px-4   transition-colors text-sm">
                        <i class="fa-solid fa-box-open mr-1.5"></i> Produk
                    </a>
                    <a href="{{ route('superadmin.digital_purchases.index') }}{{ request()->status ? '?status=' . request()->status : '' }}"
                        class="px-3 py-2.5  text-sm font-medium border transition {{ !request('status') ? 'bg-[#1D5B96] text-white border-[#1D5B96]' : 'bg-[#F4F8FC] dark:bg-[#0D1828] border-[#C9D5E2] dark:border-[#1B2C44] text-[#525252] dark:text-white/60' }}">Semua</a>
                    <a href="{{ route('superadmin.digital_purchases.index', ['status' => 'paid']) }}"
                        class="px-3 py-2.5  text-sm font-medium border transition {{ request('status') === 'paid' ? 'bg-[#1D5B96] text-white border-[#1D5B96]' : 'bg-[#F4F8FC] dark:bg-[#0D1828] border-[#C9D5E2] dark:border-[#1B2C44] text-[#525252] dark:text-white/60' }}">Lunas</a>
                    <a href="{{ route('superadmin.digital_purchases.index', ['status' => 'pending']) }}"
                        class="px-3 py-2.5  text-sm font-medium border transition {{ request('status') === 'pending' ? 'bg-[#1D5B96] text-white border-[#1D5B96]' : 'bg-[#F4F8FC] dark:bg-[#0D1828] border-[#C9D5E2] dark:border-[#1B2C44] text-[#525252] dark:text-white/60' }}">Pending</a>
                </div>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card class="mt-6">
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Jumlah</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-[#737373] uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Dibayar</th>
                    </tr>
                </x-slot:head>

                @forelse($purchases as $p)
                    <tr class="hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828] transition-colors">
                        <td class="px-6 py-4 text-xs font-mono text-[#737373] dark:text-white/40">{{ $p->order_id }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-[#0F1A2A] dark:text-white">{{ $p->product?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-[#0F1A2A] dark:text-white">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
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
                        <td class="px-6 py-4 text-sm text-[#737373]">{{ $p->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm text-[#737373]">{{ $p->paid_at?->format('d M Y H:i') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-[#737373] dark:text-white/40">
                            <i class="fa-solid fa-receipt text-3xl mb-3 text-[#C9D5E2] dark:text-white/25"></i>
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
