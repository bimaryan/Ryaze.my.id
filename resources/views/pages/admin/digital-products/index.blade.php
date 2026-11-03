@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Produk Digital" 
            subtitle="Kelola produk digital (template, e-book, dll) untuk dijual di portfolio." 
            icon="fa-solid fa-box-open">
            <x-slot:actions>
                <div class="flex gap-2">
                    <a href="{{ route('superadmin.digital_purchases.index') }}"
                        class="bg-[#F0F6FC] dark:bg-[#0D1828] hover:bg-[#E8F0F9] dark:hover:bg-[#1D5B96] text-[#0F1A2A] dark:text-white font-bold py-2.5 px-4   transition-colors text-sm">
                        <i class="fa-solid fa-receipt mr-1.5"></i> Transaksi
                    </a>
                    <a href="{{ route('superadmin.tips.index') }}"
                        class="bg-[#F0F6FC] dark:bg-[#0D1828] hover:bg-[#E8F0F9] dark:hover:bg-[#1D5B96] text-[#0F1A2A] dark:text-white font-bold py-2.5 px-4   transition-colors text-sm">
                        <i class="fa-solid fa-coins mr-1.5"></i> Tips
                    </a>
                    <a href="{{ route('superadmin.digital_products.create') }}"
                        class="bg-[#1D5B96] hover:bg-[#2A6FAD] text-white font-bold py-2.5 px-5   transition-colors text-sm">
                        <i class="fa-solid fa-plus mr-2"></i> Tambah
                    </a>
                </div>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card class="mt-6">
            <div class="p-4">
                <form action="{{ route('superadmin.digital_products.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="flex-1 bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none"
                        placeholder="Cari produk...">
                    <button type="submit" class="bg-[#1D5B96] hover:bg-[#2A6FAD] text-white px-5 py-2.5  text-sm font-medium transition-colors">
                        <i class="fa-solid fa-search mr-1"></i> Cari
                    </button>
                </form>
            </div>

            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Harga</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-[#737373] uppercase">Terjual/Download</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-[#737373] uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-[#737373] uppercase">Aksi</th>
                    </tr>
                </x-slot:head>

                @forelse($products as $p)
                    <tr class="hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828] transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($p->cover_path)
                                    <img src="{{ asset('storage/' . $p->cover_path) }}" alt="{{ $p->name }}" class="w-10 h-10  object-cover">
                                @else
                                    <div class="w-10 h-10  bg-[#E8F0F9] dark:bg-[#1D5B96]/20 text-[#1D5B96] dark:text-[#6BA8DC] flex items-center justify-center">
                                        <i class="fa-solid fa-box-open text-sm"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-semibold text-[#0F1A2A] dark:text-white">{{ $p->name }}</div>
                                    <div class="text-xs text-[#737373] dark:text-white/40 mt-0.5">{{ $p->file_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-[#0F1A2A] dark:text-white">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center text-sm text-[#737373]">{{ $p->download_count }} download</td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('superadmin.digital_products.status.toggle', $p->hashid) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-2 py-0.5 text-[10px] font-bold uppercase rounded {{ $p->is_active ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-[#F0F6FC] dark:bg-[#0D1828] text-[#737373] dark:text-white/40' }}">
                                    {{ $p->is_active ? 'Aktif' : 'Draft' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('superadmin.digital_products.edit', $p->hashid) }}"
                                    class="ui-icon-btn ui-icon-btn-primary">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <form action="{{ route('superadmin.digital_products.destroy', $p->hashid) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this)"
                                        class="ui-icon-btn ui-icon-btn-danger">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-[#737373] dark:text-white/40">
                            <i class="fa-solid fa-box-open text-3xl mb-3 text-[#C9D5E2] dark:text-white/25"></i>
                            <p>Belum ada produk digital.</p>
                        </td>
                    </tr>
                @endforelse
            </x-ui.table>

            <div class="p-4">
                {{ $products->links() }}
            </div>
        </x-ui.card>
    </x-ui.page-layout>
@endsection
