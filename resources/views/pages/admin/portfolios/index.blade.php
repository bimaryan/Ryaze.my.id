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
                <h2 class="text-lg font-bold text-slate-800">Daftar Portofolio</h2>
                
                <form action="{{ route('superadmin.portfolios.index') }}" method="GET" class="flex items-center w-full sm:w-auto">
                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <i class="fa-solid fa-search text-slate-400"></i>
                        </div>
                        <input type="text" name="search" class="bg-white border border-slate-200 text-slate-800 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full ps-9 p-2 shadow-sm" placeholder="Cari judul portofolio..." value="{{ request('search') }}">
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
                        <td class="px-6 py-4 font-medium text-slate-800 flex items-center gap-3">
                            @if($portfolio->image_path)
                                <img src="{{ Storage::url($portfolio->image_path) }}" alt="{{ $portfolio->title }}" class="w-10 h-10 object-cover rounded-lg border border-slate-200">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-400 border border-indigo-100">
                                    <i class="fa-solid fa-image"></i>
                                </div>
                            @endif
                            <div class="flex flex-col">
                                <span>{{ $portfolio->title }}</span>
                                @if($portfolio->link_preview)
                                    <a href="{{ $portfolio->link_preview }}" target="_blank" class="text-xs text-indigo-500 hover:underline"><i class="fa-solid fa-link mr-1"></i>Live Preview</a>
                                @endif
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
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-200">Aktif</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-600 border border-rose-200">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($portfolio->created_at)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('superadmin.portfolios.edit', $portfolio->hashid) }}"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-amber-600 bg-amber-50 hover:bg-amber-600 hover:text-white transition-all duration-200 shadow-sm tooltip"
                                    title="Edit">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                                
                                <form action="{{ route('superadmin.portfolios.status.toggle', $portfolio->hashid) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center {{ $portfolio->is_active ? 'text-rose-600 bg-rose-50 hover:bg-rose-600' : 'text-emerald-600 bg-emerald-50 hover:bg-emerald-600' }} hover:text-white transition-all duration-200 shadow-sm tooltip"
                                        title="{{ $portfolio->is_active ? 'Jadikan Draft' : 'Aktifkan' }}">
                                        <i class="fa-solid {{ $portfolio->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                    </button>
                                </form>

                                <form action="{{ route('superadmin.portfolios.destroy', $portfolio->hashid) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this)"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-red-600 bg-red-50 hover:bg-red-600 hover:text-white transition-all duration-200 shadow-sm tooltip"
                                        title="Hapus">
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
                <x-slot:pagination>
                    @if ($portfolios->hasPages())
                        {{ $portfolios->links() }}
                    @endif
                </x-slot:pagination>
            </x-ui.table>
        </div>
    </x-ui.page-layout>
@endsection

@section('scripts')
<script>
    function confirmDelete(button) {
        Swal.fire({
            title: 'Hapus Portofolio?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('form').submit();
            }
        })
    }
</script>
@endsection
