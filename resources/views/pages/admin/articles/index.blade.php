@extends('index')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header 
        title="Manajemen Artikel" 
        subtitle="Kelola semua artikel dan konten blog yang dipublikasikan." 
        icon="fa-solid fa-newspaper">
        <x-slot:actions>
            <a href="{{ route('superadmin.article_categories.index') }}"
                class="inline-flex items-center bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463] text-[#0F1A2A] dark:text-white hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828] px-4 py-2.5  text-sm font-medium transition ">
                <i class="fa-solid fa-folder mr-2"></i> Kategori
            </a>
            <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')"
                class="inline-flex items-center bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5  text-sm font-medium transition ">
                <i class="fa-solid fa-file-excel mr-2"></i> Import Excel
            </button>
            <button type="button" onclick="document.getElementById('aiGenerateModal').classList.remove('hidden')"
                class="inline-flex items-center bg-[#1D5B96] hover:bg-[#2A6FAD] text-white px-4 py-2.5  text-sm font-medium transition ">
                <i class="fa-solid fa-wand-magic-sparkles mr-2"></i> Buat dengan AI
            </button>
            <a href="{{ route('superadmin.articles.create') }}"
                class="inline-flex items-center bg-[#1D5B96] hover:bg-[#2A6FAD] text-white px-5 py-2.5  text-sm font-medium transition ">
                <i class="fa-solid fa-plus mr-2"></i> Tulis Artikel
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <div>
        <div class="flex flex-col sm:flex-row justify-between items-center mb-4 px-1 gap-4">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                {{-- Status Filter --}}
                <div class="flex bg-[#F0F6FC] dark:bg-[#0D1828]  p-0.5">
                    <a href="{{ route('superadmin.articles.index', request()->except('status')) }}" 
                        class="px-3 py-1.5 text-xs font-medium  transition {{ !request('status') ? 'bg-white dark:bg-[#0F1D30]  text-[#0F1A2A] dark:text-white' : 'text-[#737373] dark:text-white/40 hover:text-[#525252] dark:hover:text-white/70' }}">
                        Semua
                    </a>
                    <a href="{{ route('superadmin.articles.index', array_merge(request()->except('status'), ['status' => 'draft'])) }}" 
                        class="px-3 py-1.5 text-xs font-medium  transition {{ request('status') == 'draft' ? 'bg-white dark:bg-[#0F1D30]  text-[#0F1A2A] dark:text-white' : 'text-[#737373] dark:text-white/40 hover:text-[#525252] dark:hover:text-white/70' }}">
                        Draft
                    </a>
                    <a href="{{ route('superadmin.articles.index', array_merge(request()->except('status'), ['status' => 'published'])) }}" 
                        class="px-3 py-1.5 text-xs font-medium  transition {{ request('status') == 'published' ? 'bg-white dark:bg-[#0F1D30]  text-[#0F1A2A] dark:text-white' : 'text-[#737373] dark:text-white/40 hover:text-[#525252] dark:hover:text-white/70' }}">
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
                        <i class="fa-solid fa-search text-[#94A3B4] dark:text-white/35"></i>
                    </div>
                    <input type="text" name="search" class="text-[#0F1A2A] dark:text-white block ps-9 p-2 w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="Cari judul artikel..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="p-2 ms-2 text-sm font-medium text-white bg-[#1D5B96]  hover:bg-[#2A6FAD]  transition">
                    Cari
                </button>
                @if(request()->has('search') && request()->search != '')
                    <a href="{{ route('superadmin.articles.index') }}" class="p-2 ms-2 text-sm font-medium text-[#525252] dark:text-white/60 bg-[#F0F6FC] dark:bg-[#0D1828]  hover:bg-[#E8F0F9] dark:hover:bg-[#1D5B96] transition">
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
                <tr class="hover:bg-[#F4F8FC] dark:bg-[#0D1828] dark:hover:bg-[#0D1828] transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($article->cover_image)
                                <img src="{{ Storage::url($article->cover_image) }}" alt="{{ $article->title }}" class="w-12 h-12 object-cover  border border-[#C9D5E2] dark:border-[#1B2C44]">
                            @else
                                <div class="w-12 h-12  bg-[#E8F0F9] dark:bg-[#1D5B96]/10 flex items-center justify-center text-[#6BA8DC] border border-[#C9D5E2] dark:border-[#1D5B96]/40">
                                    <i class="fa-solid fa-file-lines"></i>
                                </div>
                            @endif
                            <div class="flex flex-col min-w-0">
                                <span class="font-medium text-[#0F1A2A] dark:text-white truncate max-w-[250px]">{{ $article->title }}</span>
                                <span class="text-xs text-[#94A3B4] dark:text-white/35">{{ $article->user->name ?? '-' }}</span>
                                @if($article->is_featured)
                                    <span class="inline-flex items-center gap-1 text-[10px] text-amber-600 dark:text-amber-300 font-bold mt-0.5"><i class="fa-solid fa-star"></i> Sorotan</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($article->category)
                            <span class="px-2 py-0.5 bg-[#F0F6FC] dark:bg-[#0D1828] text-[#525252] dark:text-white/60 text-xs font-medium rounded">{{ $article->category->name }}</span>
                        @else
                            <span class="text-xs text-[#94A3B4] dark:text-white/35">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($article->status == 'published')
                            <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold uppercase rounded">Published</span>
                        @elseif($article->status == 'draft')
                            <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 text-[10px] font-bold uppercase rounded">Draft</span>
                        @else
                            <span class="px-2 py-0.5 bg-[#F0F6FC] dark:bg-[#0D1828] text-[#525252] dark:text-white/60 text-[10px] font-bold uppercase rounded">Archived</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-[#525252] dark:text-white/60">
                        <i class="fa-solid fa-eye text-[#94A3B4] dark:text-white/35 mr-1"></i>{{ number_format($article->views_count) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-[#737373] dark:text-white/40">
                        {{ $article->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <form action="{{ route('superadmin.articles.featured', $article->hashid) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" title="{{ $article->is_featured ? 'Hapus Sorotan' : 'Jadikan Sorotan' }}" class="ui-icon-btn ui-icon-btn-warning">
                                    <i class="fa-solid fa-star"></i>
                                </button>
                            </form>
                            <form action="{{ route('superadmin.articles.status', $article->hashid) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" title="{{ $article->status == 'published' ? 'Draft' : 'Publish' }}" class="ui-icon-btn {{ $article->status == 'published' ? 'ui-icon-btn-success' : 'ui-icon-btn-neutral' }}">
                                    <i class="fa-solid {{ $article->status == 'published' ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                </button>
                            </form>
                            <a href="{{ route('superadmin.articles.edit', $article->hashid) }}" class="ui-icon-btn ui-icon-btn-primary">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('superadmin.articles.destroy', $article->hashid) }}" method="POST" class="inline delete-form">
                                @csrf @method('DELETE')
                                <button type="button" class="ui-icon-btn ui-icon-btn-danger delete-btn">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-[#737373] dark:text-white/40">
                        <i class="fa-solid fa-newspaper text-4xl text-[#C9D5E2] dark:text-white/25 mb-3"></i>
                        <p class="font-medium">Belum ada artikel.</p>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">{{ $articles->links() }}</div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full flex bg-[#0A1322]/60">
        <div class="relative p-4 w-full max-w-md max-h-full m-auto">
            <div class="relative bg-white dark:bg-[#0F1D30]  shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b border-[#E3EAF1] dark:border-[#1B2C44] rounded-t">
                    <h3 class="text-lg font-semibold text-[#0F1A2A] dark:text-white">
                        Import Artikel (Excel/CSV)
                    </h3>
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="ui-icon-btn ui-icon-btn-primary ms-auto">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <form action="{{ route('superadmin.articles.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-4 md:p-5 space-y-4">
                        <div class="bg-[#E8F0F9] dark:bg-[#1D5B96]/10 text-[#1D5B96] dark:text-[#6BA8DC] p-3  text-xs font-medium border border-[#C9D5E2] dark:border-[#1D5B96]/40 mb-4">
                            Unduh template Excel untuk memastikan format kolom sudah benar sebelum mengunggah.
                            <a href="{{ route('superadmin.articles.template') }}" class="inline-block mt-2 underline font-bold"><i class="fa-solid fa-download"></i> Download Template</a>
                        </div>
                        
                        <div>
                            <label class="block mb-2 text-sm font-medium text-[#0F1A2A] dark:text-white" for="file">Upload File Excel/CSV</label>
                            <input class="block w-full text-sm text-[#0F1A2A] dark:text-white border border-[#C9D5E2] dark:border-[#1B2C44]  cursor-pointer bg-[#F4F8FC] dark:bg-[#0D1828] focus:outline-none p-2.5" id="file" type="file" name="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                        </div>
                    </div>
                    <div class="flex items-center p-4 md:p-5 border-t border-[#E3EAF1] dark:border-[#1B2C44] rounded-b">
                        <button type="submit" class="text-white bg-[#1D5B96] hover:bg-[#2A6FAD] font-medium  text-sm px-5 py-2.5 text-center transition">Import Data</button>
                        <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="py-2.5 px-5 ms-3 text-sm font-medium text-[#525252] dark:text-white/60 hover:text-[#0F1A2A] dark:hover:text-white focus:outline-none bg-white dark:bg-[#0D1828]  border border-[#C9D5E2] dark:border-[#1B2C44] hover:bg-[#E8F0F9] dark:hover:bg-[#12233A] transition-colors">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- AI Generate Modal -->
    <div id="aiGenerateModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full flex bg-[#0A1322]/60">
        <div class="relative p-4 w-full max-w-lg max-h-full m-auto">
            <div class="relative bg-white dark:bg-[#0F1D30]  shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b border-[#E3EAF1] dark:border-[#1B2C44] rounded-t">
                    <div>
                        <h3 class="text-lg font-semibold text-[#0F1A2A] dark:text-white">Buat Artikel dengan AI</h3>
                        <p class="text-xs text-[#737373] dark:text-white/40 mt-1">AI akan membuat isi, SEO, tag, dan gambar sampul.</p>
                    </div>
                    <button type="button" onclick="document.getElementById('aiGenerateModal').classList.add('hidden')" class="ui-icon-btn ui-icon-btn-neutral">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <form action="{{ route('superadmin.articles.generate_ai') }}" method="POST">
                    @csrf
                    <div class="p-4 md:p-5 space-y-4">
                        <div>
                            <label for="ai-topic" class="block mb-2 text-sm font-medium text-[#0F1A2A] dark:text-white">Topik</label>
                            <textarea id="ai-topic" name="topic" rows="3" required minlength="5" maxlength="500" class="block w-full text-sm text-[#0F1A2A] dark:text-white bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44]  p-3 focus:border-[#1D5B96] outline-none" placeholder="Contoh: Panduan memilih hosting untuk website UMKM"></textarea>
                        </div>
                        <div>
                            <label for="ai-category" class="block mb-2 text-sm font-medium text-[#0F1A2A] dark:text-white">Kategori</label>
                            <select id="ai-category" name="category_id" class="block w-full text-sm text-[#0F1A2A] dark:text-white bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44]  p-3 focus:border-[#1D5B96] outline-none">
                                <option value="">Tanpa kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label class="flex items-start gap-3 text-sm text-[#0F1A2A] dark:text-white">
                            <input type="checkbox" name="publish" value="1" class="mt-0.5 rounded border-[#C9D5E2] dark:border-[#1B2C44] accent-[#1D5B96]">
                            <span><span class="font-medium">Langsung publikasikan</span><br><span class="text-xs text-[#737373] dark:text-white/40">Jika tidak dicentang, artikel dibuat sebagai draft untuk direview.</span></span>
                        </label>
                    </div>
                    <div class="flex items-center p-4 md:p-5 border-t border-[#E3EAF1] dark:border-[#1B2C44] rounded-b">
                        <button type="submit" class="text-white bg-[#1D5B96] hover:bg-[#2A6FAD] font-medium  text-sm px-5 py-2.5 transition"><i class="fa-solid fa-wand-magic-sparkles mr-2"></i>Masukkan ke Antrean</button>
                        <button type="button" onclick="document.getElementById('aiGenerateModal').classList.add('hidden')" class="py-2.5 px-5 ms-3 text-sm font-medium text-[#525252] dark:text-white/60 hover:text-[#0F1A2A] dark:hover:text-white focus:outline-none bg-white dark:bg-[#0D1828]  border border-[#C9D5E2] dark:border-[#1B2C44] hover:bg-[#E8F0F9] dark:hover:bg-[#12233A] transition-colors">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-ui.page-layout>
