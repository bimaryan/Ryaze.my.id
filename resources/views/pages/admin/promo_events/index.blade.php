@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Event Promo" 
            subtitle="Kelola event promo dan diskon untuk ditampilkan kepada user." 
            icon="fa-solid fa-bullhorn">
            <x-slot:actions>
                <a href="{{ route('admin.promo_events.create') }}"
                    class="inline-flex justify-center items-center bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm border border-transparent">
                    <i class="fa-solid fa-plus mr-2"></i> Tambah Promo
                </a>
            </x-slot:actions>
        </x-ui.page-header>
        
        <div>
            <div class="flex flex-col sm:flex-row justify-between items-center mb-4 px-1 gap-4">
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    {{-- Status Filter --}}
                    <div class="flex bg-slate-100 dark:bg-slate-700/50 rounded-lg p-0.5">
                        <a href="{{ route('admin.promo_events.index', request()->except('status')) }}" 
                            class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ !request()->has('status') ? 'bg-white dark:bg-slate-800/60 shadow-sm text-slate-800 dark:text-slate-100' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                            Semua
                        </a>
                        <a href="{{ route('admin.promo_events.index', array_merge(request()->except('status'), ['status' => '1'])) }}" 
                            class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ request('status') === '1' ? 'bg-white dark:bg-slate-800/60 shadow-sm text-slate-800 dark:text-slate-100' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                            Aktif
                        </a>
                        <a href="{{ route('admin.promo_events.index', array_merge(request()->except('status'), ['status' => '0'])) }}" 
                            class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ request('status') === '0' ? 'bg-white dark:bg-slate-800/60 shadow-sm text-slate-800 dark:text-slate-100' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                            Tidak Aktif
                        </a>
                    </div>
                </div>
                
                <form action="{{ route('admin.promo_events.index') }}" method="GET" class="flex items-center w-full sm:w-auto">
                    @if(request()->has('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <i class="fa-solid fa-search text-slate-400 dark:text-slate-500"></i>
                        </div>
                        <input type="text" name="search" class="text-slate-800 dark:text-slate-100 block ps-9 p-2 w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="Cari judul promo..." value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="p-2 ms-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                        Cari
                    </button>
                    @if(request()->has('search') && request()->search != '')
                        <a href="{{ route('admin.promo_events.index') }}" class="p-2 ms-2 text-sm font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700/50 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <x-ui.table>
                <x-slot:head>
                    <th scope="col" class="px-6 py-4">Judul Promo</th>
                    <th scope="col" class="px-6 py-4">Masa Berlaku</th>
                    <th scope="col" class="px-6 py-4">Status</th>
                    <th scope="col" class="px-6 py-4 text-center">Aksi</th>
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
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ route('admin.promo_events.status', $promo->id) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="{{ $promo->is_active ? 'Nonaktifkan' : 'Aktifkan' }}" class="p-1.5 rounded-lg transition {{ $promo->is_active ? 'text-emerald-500 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20' : 'text-slate-400 dark:text-slate-500 hover:text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-500/10' }}">
                                        <i class="fa-solid {{ $promo->is_active ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                    </button>
                                </form>
                                <a href="{{ route('admin.promo_events.edit', $promo->id) }}" class="p-1.5 text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 rounded-lg transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.promo_events.destroy', $promo->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this)" class="p-1.5 text-rose-600 dark:text-rose-300 bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-100 dark:hover:bg-rose-500/20 rounded-lg transition">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-bullhorn text-3xl mb-3 text-slate-300 dark:text-slate-400"></i>
                            <p>Belum ada data promo event.</p>
                        </td>
                    </tr>
                @endforelse
            </x-ui.table>

            @if($promos->hasPages())
                <div class="mt-4">
                    {{ $promos->links() }}
                </div>
            @endif
        </div>

        <script>
            function confirmDelete(button) {
                Swal.fire({
                    title: 'Hapus Promo?',
                    text: 'Promo event ini akan dihapus permanen dan tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.closest('form').submit();
                    }
                });
            }
        </script>
    </x-ui.page-layout>
@endsection
