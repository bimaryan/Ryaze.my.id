@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Manajemen Portofolio" 
            subtitle="Kelola mahakarya terbaru yang akan ditampilkan di halaman depan." 
            icon="fa-solid fa-briefcase">
            <x-slot:actions>
                <a href="{{ route('superadmin.portfolios.create') }}"
                    class="inline-flex justify-center items-center bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm border border-transparent">
                    <i class="fa-solid fa-plus mr-2"></i> Tambah Portofolio
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div>
            <div class="flex flex-col sm:flex-row justify-between items-center mb-4 px-1 gap-4">
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    {{-- Status Filter --}}
                    <div class="flex bg-slate-100 rounded-lg p-0.5">
                        <a href="{{ route('superadmin.portfolios.index', request()->except('status')) }}" 
                            class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ !request()->has('status') ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700' }}">
                            Semua
                        </a>
                        <a href="{{ route('superadmin.portfolios.index', array_merge(request()->except('status'), ['status' => '1'])) }}" 
                            class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ request('status') === '1' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700' }}">
                            Aktif
                        </a>
                        <a href="{{ route('superadmin.portfolios.index', array_merge(request()->except('status'), ['status' => '0'])) }}" 
                            class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ request('status') === '0' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700' }}">
                            Draft
                        </a>
                    </div>
                </div>
                
                <form action="{{ route('superadmin.portfolios.index') }}" method="GET" class="flex items-center w-full sm:w-auto">
                    @if(request()->has('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <i class="fa-solid fa-search text-slate-400"></i>
                        </div>
                        <input type="text" name="search" class="text-slate-800 block ps-9 p-2 w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="Cari judul portofolio..." value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="p-2 ms-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                        Cari
                    </button>
                    @if(request()->has('search') && request()->search != '')
                        <a href="{{ route('superadmin.portfolios.index') }}" class="p-2 ms-2 text-sm font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
            <x-ui.table>
                <x-slot:head>
                    <th scope="col" class="px-6 py-4">Judul</th>
                    <th scope="col" class="px-6 py-4">Tags</th>
                    <th scope="col" class="px-6 py-4">Status</th>
                    <th scope="col" class="px-6 py-4">Tgl Dibuat</th>
                    <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                </x-slot:head>
                @forelse($portfolios as $portfolio)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($portfolio->image_path)
                                    <img src="{{ Storage::url($portfolio->image_path) }}" alt="{{ $portfolio->title }}" class="w-12 h-12 object-cover rounded-lg border border-slate-200">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-400 border border-indigo-100">
                                        <i class="fa-solid fa-image"></i>
                                    </div>
                                @endif
                                <div class="flex flex-col min-w-0">
                                    <span class="font-medium text-slate-800 truncate max-w-[250px]">{{ $portfolio->title }}</span>
                                    @if($portfolio->link_preview)
                                        <a href="{{ $portfolio->link_preview }}" target="_blank" class="text-[10px] text-indigo-500 hover:underline mt-0.5 font-bold"><i class="fa-solid fa-link mr-1"></i>Live Preview</a>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($portfolio->tags)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($portfolio->tags as $tag)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-600 border border-slate-200">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-slate-400 italic text-xs">Tanpa tag</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if ($portfolio->is_active)
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase rounded">Aktif</span>
                            @else
                                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold uppercase rounded">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">
                            {{ \Carbon\Carbon::parse($portfolio->created_at)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ route('superadmin.portfolios.status.toggle', $portfolio->hashid) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="{{ $portfolio->is_active ? 'Jadikan Draft' : 'Aktifkan' }}" class="p-1.5 rounded-lg transition {{ $portfolio->is_active ? 'text-emerald-500 bg-emerald-50 hover:bg-emerald-100' : 'text-slate-400 hover:text-emerald-500 hover:bg-emerald-50' }}">
                                        <i class="fa-solid {{ $portfolio->is_active ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                    </button>
                                </form>
                                <a href="{{ route('superadmin.portfolios.edit', $portfolio->hashid) }}" class="p-1.5 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('superadmin.portfolios.destroy', $portfolio->hashid) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this)" class="p-1.5 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            <i class="fa-solid fa-briefcase text-3xl mb-3 text-slate-300"></i>
                            <p>Belum ada data portofolio.</p>
                        </td>
                    </tr>
                @endforelse
            </x-ui.table>

            <div class="mt-4">{{ $portfolios->links() }}</div>
        </div>
    </x-ui.page-layout>