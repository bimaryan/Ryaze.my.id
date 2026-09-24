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
                        class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold py-2.5 px-4 rounded-lg shadow-md transition-colors text-sm">
                        <i class="fa-solid fa-receipt mr-1.5"></i> Transaksi
                    </a>
                    <a href="{{ route('superadmin.tips.index') }}"
                        class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold py-2.5 px-4 rounded-lg shadow-md transition-colors text-sm">
                        <i class="fa-solid fa-coins mr-1.5"></i> Tips
                    </a>
                    <a href="{{ route('superadmin.digital_products.create') }}"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-lg shadow-md transition-colors text-sm">
                        <i class="fa-solid fa-plus mr-2"></i> Tambah
                    </a>
                </div>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card class="mt-6">
            <div class="p-4">
                <form action="{{ route('superadmin.digital_products.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="flex-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none"
                        placeholder="Cari produk...">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        <i class="fa-solid fa-search mr-1"></i> Cari
                    </button>
                </form>
            </div>

            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Harga</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Terjual/Download</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </x-slot:head>

                @forelse($products as $p)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($p->cover_path)
                                    <img src="{{ asset('storage/' . $p->cover_path) }}" alt="{{ $p->name }}" class="w-10 h-10 rounded-lg object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                        <i class="fa-solid fa-box-open text-sm"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $p->name }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $p->file_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-slate-700 dark:text-slate-200">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center text-sm text-slate-500">{{ $p->download_count }} download</td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('superadmin.digital_products.status.toggle', $p->hashid) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-2 py-0.5 text-[10px] font-bold uppercase rounded {{ $p->is_active ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }}">
                                    {{ $p->is_active ? 'Aktif' : 'Draft' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('superadmin.digital_products.edit', $p->hashid) }}"
                                    class="p-1.5 text-indigo-600 bg-indigo-50 dark:bg-indigo-500/10 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition-colors">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <form action="{{ route('superadmin.digital_products.destroy', $p->hashid) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this)"
                                        class="p-1.5 text-rose-600 bg-rose-50 dark:bg-rose-500/10 rounded-lg hover:bg-rose-100 dark:hover:bg-rose-500/20 transition-colors">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-box-open text-3xl mb-3 text-slate-300 dark:text-slate-500"></i>
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
