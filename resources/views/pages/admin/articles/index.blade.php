@extends('index')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header 
        title="Manajemen Artikel" 
        subtitle="Kelola semua artikel dan konten blog yang dipublikasikan." 
        icon="fa-solid fa-newspaper">
        <x-slot:actions>
            <a href="{{ route('superadmin.article_categories.index') }}"
                class="inline-flex items-center bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-4 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fa-solid fa-folder mr-2"></i> Kategori
            </a>
            <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')"
                class="inline-flex items-center bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fa-solid fa-file-excel mr-2"></i> Import Excel
            </button>
            <a href="{{ route('superadmin.articles.create') }}"
                class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fa-solid fa-plus mr-2"></i> Tulis Artikel
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <div>
        <div class="flex flex-col sm:flex-row justify-between items-center mb-4 px-1 gap-4">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                {{-- Status Filter --}}
                <div class="flex bg-slate-100 rounded-lg p-0.5">
                    <a href="{{ route('superadmin.articles.index', request()->except('status')) }}" 
                        class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ !request('status') ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700' }}">
                        Semua
                    </a>
                    <a href="{{ route('superadmin.articles.index', array_merge(request()->except('status'), ['status' => 'draft'])) }}" 
                        class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ request('status') == 'draft' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700' }}">
                        Draft
                    </a>
                    <a href="{{ route('superadmin.articles.index', array_merge(request()->except('status'), ['status' => 'published'])) }}" 
                        class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ request('status') == 'published' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700' }}">
                        Published
                    </a>
                </div>
            </div>
            
            <form action="{{ route('superadmin.articles.index') }}" method="GET" class="flex items-center w-full sm:w-auto">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <i class="fa-solid fa-search text-slate-400"></i>
                    </div>
                    <input type="text" name="search" class="text-slate-800 block ps-9 p-2 w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="Cari judul artikel..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="p-2 ms-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                    Cari
                </button>
                @if(request()->has('search') && request()->search != '')
                    <a href="{{ route('superadmin.articles.index') }}" class="p-2 ms-2 text-sm font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <x-ui.table>
            <x-slot:head>
                <th scope="col" class="px-6 py-4">Artikel</th>
                <th scope="col" class="px-6 py-4">Kategori</th>
                <th scope="col" class="px-6 py-4">Status</th>
                <th scope="col" class="px-6 py-4">Views</th>
                <th scope="col" class="px-6 py-4">Tanggal</th>
                <th scope="col" class="px-6 py-4 text-center">Aksi</th>
            </x-slot:head>

            @forelse($articles as $article)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($article->cover_image)
                                <img src="{{ Storage::url($article->cover_image) }}" alt="{{ $article->title }}" class="w-12 h-12 object-cover rounded-lg border border-slate-200">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-400 border border-indigo-100">
                                    <i class="fa-solid fa-file-lines"></i>
                                </div>
                            @endif
                            <div class="flex flex-col min-w-0">
                                <span class="font-medium text-slate-800 truncate max-w-[250px]">{{ $article->title }}</span>
                                <span class="text-xs text-slate-400">{{ $article->user->name ?? '-' }}</span>
                                @if($article->is_featured)
                                    <span class="inline-flex items-center gap-1 text-[10px] text-amber-600 font-bold mt-0.5"><i class="fa-solid fa-star"></i> Sorotan</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($article->category)
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-xs font-medium rounded">{{ $article->category->name }}</span>
                        @else
                            <span class="text-xs text-slate-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($article->status == 'published')
                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase rounded">Published</span>
                        @elseif($article->status == 'draft')
                            <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold uppercase rounded">Draft</span>
                        @else
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-bold uppercase rounded">Archived</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        <i class="fa-solid fa-eye text-slate-400 mr-1"></i>{{ number_format($article->views_count) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500">
                        {{ $article->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <form action="{{ route('superadmin.articles.featured', $article->hashid) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" title="{{ $article->is_featured ? 'Hapus Sorotan' : 'Jadikan Sorotan' }}" class="p-1.5 rounded-lg transition {{ $article->is_featured ? 'text-amber-500 bg-amber-50 hover:bg-amber-100' : 'text-slate-400 hover:text-amber-500 hover:bg-amber-50' }}">
                                    <i class="fa-solid fa-star"></i>
                                </button>
                            </form>
                            <form action="{{ route('superadmin.articles.status', $article->hashid) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" title="{{ $article->status == 'published' ? 'Draft' : 'Publish' }}" class="p-1.5 rounded-lg transition {{ $article->status == 'published' ? 'text-emerald-500 bg-emerald-50 hover:bg-emerald-100' : 'text-slate-400 hover:text-emerald-500 hover:bg-emerald-50' }}">
                                    <i class="fa-solid {{ $article->status == 'published' ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                </button>
                            </form>
                            <a href="{{ route('superadmin.articles.edit', $article->hashid) }}" class="p-1.5 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('superadmin.articles.destroy', $article->hashid) }}" method="POST" class="inline delete-form">
                                @csrf @method('DELETE')
                                <button type="button" class="p-1.5 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition delete-btn">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                        <i class="fa-solid fa-newspaper text-4xl text-slate-300 mb-3"></i>
                        <p class="font-medium">Belum ada artikel.</p>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">{{ $articles->links() }}</div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full flex bg-slate-900/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-md max-h-full m-auto">
            <div class="relative bg-white rounded-xl shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b border-slate-100 rounded-t">
                    <h3 class="text-lg font-semibold text-slate-900">
                        Import Artikel (Excel/CSV)
                    </h3>
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="text-slate-400 bg-transparent hover:bg-slate-200 hover:text-slate-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <form action="{{ route('superadmin.articles.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-4 md:p-5 space-y-4">
                        <div class="bg-indigo-50 text-indigo-700 p-3 rounded-lg text-xs font-medium border border-indigo-100 mb-4">
                            Unduh template Excel untuk memastikan format kolom sudah benar sebelum mengunggah.
                            <a href="{{ route('superadmin.articles.template') }}" class="inline-block mt-2 underline font-bold"><i class="fa-solid fa-download"></i> Download Template</a>
                        </div>
                        
                        <div>
                            <label class="block mb-2 text-sm font-medium text-slate-900" for="file">Upload File Excel/CSV</label>
                            <input class="block w-full text-sm text-slate-900 border border-slate-300 rounded-lg cursor-pointer bg-slate-50 focus:outline-none p-2.5" id="file" type="file" name="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                        </div>
                    </div>
                    <div class="flex items-center p-4 md:p-5 border-t border-slate-100 rounded-b">
                        <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition">Import Data</button>
                        <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="py-2.5 px-5 ms-3 text-sm font-medium text-slate-900 focus:outline-none bg-white rounded-lg border border-slate-200 hover:bg-slate-100 hover:text-indigo-700 transition">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-ui.page-layout>
@endsection

@push('scripts')
<script nonce="{{ app('csp_nonce') ?? '' }}">
    document.addEventListener('DOMContentLoaded', function() {
        const deleteBtns = document.querySelectorAll('.delete-btn');
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                
                Swal.fire({
                    title: 'Hapus Artikel?',
                    text: "Artikel yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
