@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Event Promo" 
            subtitle="Kelola event promo dan diskon untuk ditampilkan kepada user." 
            icon="fa-solid fa-bullhorn">
            <x-slot name="actions">
                <a href="{{ route('admin.promo_events.create') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                    <i class="fa-solid fa-plus mr-2"></i> Tambah Promo
                </a>
            </x-slot>
        </x-ui.page-header>
        
        <x-ui.table>
            <x-slot:head>
                <th scope="col" class="px-6 py-4">Judul Promo</th>
                <th scope="col" class="px-6 py-4">Masa Berlaku</th>
                <th scope="col" class="px-6 py-4">Status</th>
                <th scope="col" class="px-6 py-4 text-right">Aksi</th>
            </x-slot:head>

            @forelse($promos as $promo)
                <tr class="hover:bg-slate-50 dark:bg-slate-800/50 dark:hover:bg-slate-700/40 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($promo->banner_image)
                                <img src="{{ $promo->banner_url }}" alt="{{ $promo->title }}" class="w-16 h-10 object-cover rounded-lg border border-slate-200 dark:border-slate-700">
                            @else
                                <div class="w-16 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-400 border border-indigo-100 dark:border-indigo-500/30">
                                    <i class="fa-solid fa-bullhorn"></i>
                                </div>
                            @endif
                            <div class="flex flex-col min-w-0">
                                <span class="font-medium text-slate-800 dark:text-slate-100 truncate max-w-[250px]">{{ $promo->title }}</span>
                                <span class="text-xs text-slate-400 dark:text-slate-500">{{ Str::limit($promo->description, 50) }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-slate-900 dark:text-slate-100">{{ $promo->start_date->format('d M Y, H:i') }}</div>
                        <div class="text-xs text-slate-500">s.d. {{ $promo->end_date->format('d M Y, H:i') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($promo->is_active && $promo->end_date >= now())
                            <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold uppercase rounded">Aktif</span>
                        @else
                            <span class="px-2 py-0.5 bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 text-[10px] font-bold uppercase rounded">Tidak Aktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <a href="{{ route('admin.promo_events.edit', $promo->id) }}" class="p-1.5 inline-flex items-center justify-center text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 rounded-lg transition mr-2" title="Edit">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <form action="{{ route('admin.promo_events.destroy', $promo->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus promo ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 inline-flex items-center justify-center text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-100 dark:hover:bg-rose-500/20 rounded-lg transition" title="Hapus">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="text-slate-400 dark:text-slate-500 mb-2"><i class="fa-solid fa-folder-open text-4xl"></i></div>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Belum ada promo event.</p>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>

        @if($promos->hasPages())
            <div class="mt-4">
                {{ $promos->links() }}
            </div>
        @endif
    </x-ui.page-layout>
@endsection
