@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Manajemen Portofolio" 
            subtitle="Kelola mahakarya terbaru yang akan ditampilkan di halaman depan." 
            icon="fa-solid fa-briefcase">
            <x-slot:actions>
                <a href="{{ route('superadmin.portfolios.create') }}"
                    class="inline-flex justify-center items-center bg-[#1D5B96] hover:bg-[#2A6FAD] text-white px-5 py-2.5  text-sm font-medium transition  border border-transparent">
                    <i class="fa-solid fa-plus mr-2"></i> Tambah Portofolio
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div>
            <div class="flex flex-col sm:flex-row justify-between items-center mb-4 px-1 gap-4">
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    {{-- Status Filter --}}
                    <div class="flex bg-[#F0F6FC] dark:bg-[#0D1828]  p-0.5">
                        <a href="{{ route('superadmin.portfolios.index', request()->except('status')) }}" 
                            class="px-3 py-1.5 text-xs font-medium  transition {{ !request()->has('status') ? 'bg-white dark:bg-[#0F1D30]  text-[#0F1A2A] dark:text-white' : 'text-[#737373] dark:text-white/40 hover:text-[#525252] dark:hover:text-white/70' }}">
                            Semua
                        </a>
                        <a href="{{ route('superadmin.portfolios.index', array_merge(request()->except('status'), ['status' => '1'])) }}" 
                            class="px-3 py-1.5 text-xs font-medium  transition {{ request('status') === '1' ? 'bg-white dark:bg-[#0F1D30]  text-[#0F1A2A] dark:text-white' : 'text-[#737373] dark:text-white/40 hover:text-[#525252] dark:hover:text-white/70' }}">
                            Aktif
                        </a>
                        <a href="{{ route('superadmin.portfolios.index', array_merge(request()->except('status'), ['status' => '0'])) }}" 
                            class="px-3 py-1.5 text-xs font-medium  transition {{ request('status') === '0' ? 'bg-white dark:bg-[#0F1D30]  text-[#0F1A2A] dark:text-white' : 'text-[#737373] dark:text-white/40 hover:text-[#525252] dark:hover:text-white/70' }}">
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
                            <i class="fa-solid fa-search text-[#94A3B4] dark:text-white/35"></i>
                        </div>
                        <input type="text" name="search" class="text-[#0F1A2A] dark:text-white block ps-9 p-2 w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="Cari judul portofolio..." value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="p-2 ms-2 text-sm font-medium text-white bg-[#1D5B96]  hover:bg-[#2A6FAD]  transition">
                        Cari
                    </button>
                    @if(request()->has('search') && request()->search != '')
                        <a href="{{ route('superadmin.portfolios.index') }}" class="p-2 ms-2 text-sm font-medium text-[#525252] dark:text-white/60 bg-[#F0F6FC] dark:bg-[#0D1828]  hover:bg-[#E8F0F9] dark:hover:bg-[#1D5B96] transition">
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
                    <tr class="hover:bg-[#F4F8FC] dark:bg-[#0D1828] dark:hover:bg-[#0D1828] transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($portfolio->image_path)
                                    <img src="{{ Storage::url($portfolio->image_path) }}" alt="{{ $portfolio->title }}" class="w-12 h-12 object-cover  border border-[#C9D5E2] dark:border-[#1B2C44]">
                                @else
                                    <div class="w-12 h-12  bg-[#E8F0F9] dark:bg-[#1D5B96]/10 flex items-center justify-center text-[#6BA8DC] border border-[#C9D5E2] dark:border-[#1D5B96]/40">
                                        <i class="fa-solid fa-image"></i>
                                    </div>
                                @endif
                                <div class="flex flex-col min-w-0">
                                    <span class="font-medium text-[#0F1A2A] dark:text-white truncate max-w-[250px]">{{ $portfolio->title }}</span>
                                    @if($portfolio->link_preview)
                                        <a href="{{ $portfolio->link_preview }}" target="_blank" class="text-[10px] text-[#1D5B96] dark:text-[#6BA8DC] hover:underline mt-0.5 font-bold"><i class="fa-solid fa-link mr-1"></i>Live Preview</a>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($portfolio->tags)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($portfolio->tags as $tag)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-[#F0F6FC] dark:bg-[#0D1828] text-[#525252] dark:text-white/60 border border-[#C9D5E2] dark:border-[#1B2C44]">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-[#94A3B4] dark:text-white/35 italic text-xs">Tanpa tag</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if ($portfolio->is_active)
                                <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold uppercase rounded">Aktif</span>
                            @else
                                <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 text-[10px] font-bold uppercase rounded">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-[#737373] dark:text-white/40">
                            {{ \Carbon\Carbon::parse($portfolio->created_at)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ route('superadmin.portfolios.status.toggle', $portfolio->hashid) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="{{ $portfolio->is_active ? 'Jadikan Draft' : 'Aktifkan' }}" class="ui-icon-btn {{ $portfolio->is_active ? 'ui-icon-btn-success' : 'ui-icon-btn-neutral' }}">
                                        <i class="fa-solid {{ $portfolio->is_active ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                    </button>
                                </form>
                                <a href="{{ route('superadmin.portfolios.edit', $portfolio->hashid) }}" class="ui-icon-btn ui-icon-btn-primary">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('superadmin.portfolios.destroy', $portfolio->hashid) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this)" class="ui-icon-btn ui-icon-btn-danger">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-[#737373] dark:text-white/40">
                            <i class="fa-solid fa-briefcase text-3xl mb-3 text-[#C9D5E2] dark:text-white/25"></i>
                            <p>Belum ada data portofolio.</p>
                        </td>
                    </tr>
                @endforelse
            </x-ui.table>

            <div class="mt-4">{{ $portfolios->links() }}</div>
        </div>

        <script>
            function confirmDelete(button) {
                Swal.fire({
                    title: 'Hapus Portofolio?',
                    text: 'Data portofolio ini akan dihapus permanen dan tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#94a3b8',
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