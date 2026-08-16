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
        
        <x-ui.card class="mt-6 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Judul Promo</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Masa Berlaku</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-900 divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($promos as $promo)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($promo->banner_image)
                                            <div class="flex-shrink-0 h-10 w-16 mr-4 bg-slate-100 rounded flex items-center justify-center overflow-hidden">
                                                <img class="max-h-full max-w-full object-cover" src="{{ $promo->banner_url }}" alt="">
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $promo->title }}</div>
                                            <div class="text-xs text-slate-500">{{ Str::limit($promo->description, 50) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-slate-900 dark:text-white">{{ $promo->start_date->format('d M Y, H:i') }}</div>
                                    <div class="text-xs text-slate-500">s.d. {{ $promo->end_date->format('d M Y, H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($promo->is_active && $promo->end_date >= now())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.promo_events.edit', $promo->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">Edit</a>
                                    <form action="{{ route('admin.promo_events.destroy', $promo->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus promo ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-900 dark:text-rose-400 dark:hover:text-rose-300">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="text-slate-500 dark:text-slate-400 mb-2"><i class="fa-solid fa-box-open text-4xl"></i></div>
                                    <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Belum ada promo event</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($promos->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $promos->links() }}
                </div>
            @endif
        </x-ui.card>
    </x-ui.page-layout>
@endsection
