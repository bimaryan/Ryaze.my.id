@extends('index')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header 
        title="Tulis Artikel Baru" 
        subtitle="Buat konten artikel untuk blog Ryaze." 
        icon="fa-solid fa-pen-fancy">
        <x-slot:actions>
            <a href="{{ route('superadmin.articles.index') }}" class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg font-medium transition text-sm flex items-center gap-2 shadow-sm">
                Kembali
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <form id="article-form" action="{{ route('superadmin.articles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Konten Artikel</h3>
                    
                    <div class="mb-5">
                        <label for="title" class="block mb-1.5 text-sm font-medium text-slate-700">Judul <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="text-slate-800 block p-3 w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="Judul artikel yang menarik...">
                        @error('title') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-5">
                        <label for="excerpt" class="block mb-1.5 text-sm font-medium text-slate-700">Ringkasan</label>
                        <textarea name="excerpt" id="excerpt" rows="3"
                            class="text-slate-800 block p-3 w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="Ringkasan singkat artikel (opsional, maks 500 karakter)...">{{ old('excerpt') }}</textarea>
                        @error('excerpt') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-5">
                        <label for="body" class="block mb-1.5 text-sm font-medium text-slate-700">Konten <span class="text-rose-500">*</span></label>
                        <div class="border border-slate-200 rounded-t-lg bg-slate-50 quill-toolbar-container"></div>
                        <div id="editor-container" class="bg-white rounded-b-lg border-x border-b border-slate-200 prose prose-sm max-w-none" style="height: 500px;">{!! old('body') !!}</div>
                        <input type="hidden" name="body" id="body" value="{{ old('body') }}">
                        @error('body') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- SEO Settings --}}
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">
                        <i class="fa-solid fa-magnifying-glass-chart text-indigo-500 mr-2"></i>Pengaturan SEO
                    </h3>
                    <div class="mb-5">
                        <label for="meta_title" class="block mb-1.5 text-sm font-medium text-slate-700">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" maxlength="70"
                            class="text-slate-800 block p-3 w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="Override judul untuk mesin pencari (maks 70 karakter)">
                    </div>
                    <div>
                        <label for="meta_description" class="block mb-1.5 text-sm font-medium text-slate-700">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" rows="2" maxlength="160"
                            class="text-slate-800 block p-3 w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="Deskripsi singkat untuk hasil pencarian Google (maks 160 karakter)">{{ old('meta_description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Publish Settings --}}
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Publikasi</h3>
                    
                    <div class="mb-5">
                        <label for="status" class="block mb-1.5 text-sm font-medium text-slate-700">Status</label>
                        <select name="status" id="status" class="text-slate-800 block p-3 w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>

                    <div class="mb-5">
                        <label for="category_id" class="block mb-1.5 text-sm font-medium text-slate-700">Kategori</label>
                        <select name="category_id" id="category_id" class="text-slate-800 block p-3 w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            <option value="">Tanpa Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-3 mb-5">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 bg-white border-slate-300 rounded focus:ring-indigo-500">
                        <label for="is_featured" class="text-sm font-medium text-slate-700">Jadikan Sorotan</label>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm py-3 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-paper-plane mr-2"></i>Simpan Artikel
                    </button>
                </div>

                {{-- Cover Image --}}
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Gambar Sampul</h3>
                    <input type="file" name="cover_image" id="cover_image" accept="image/*"
                        class="block w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    <p class="text-xs text-slate-400 mt-2">Format: JPG, PNG, WEBP. Maks: 3MB.</p>
                    @error('cover_image') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tags --}}
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Tags</h3>
                    <input type="text" name="tags" id="tags" value="{{ old('tags') }}"
                        class="text-slate-800 block p-3 w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="laravel, php, tutorial">
                    <p class="text-xs text-slate-400 mt-2">Pisahkan dengan koma.</p>
                </div>
            </div>
        </div>
    </form>
</x-ui.page-layout>

@endsection

@push('scripts')
<script nonce="{{ app('csp_nonce') ?? '' }}">
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.Quill !== 'undefined') {
            const quill = new window.Quill('#editor-container', {
                theme: 'snow',
                placeholder: 'Tulis konten artikel di sini...',
                modules: {
                    toolbar: {
                        container: [
                            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            [{ 'align': [] }],
                            ['link', 'image', 'video'],
                            ['clean'],
                            ['code-block']
                        ],
                        handlers: {
                            image: function() {
                                const input = document.createElement('input');
                                input.setAttribute('type', 'file');
                                input.setAttribute('accept', 'image/*');
                                input.click();
                                input.onchange = async () => {
                                    const file = input.files[0];
                                    uploadQuillImage(file, quill);
                                };
                            }
                        }
                    }
                }
            });

            quill.root.addEventListener('paste', function(e) {
                if (e.clipboardData && e.clipboardData.items && e.clipboardData.items.length) {
                    const item = e.clipboardData.items[0];
                    if (item.type.indexOf('image/') !== -1) {
                        e.preventDefault();
                        const file = item.getAsFile();
                        uploadQuillImage(file, quill);
                    }
                }
            });

            async function uploadQuillImage(file, editor) {
                const formData = new FormData();
                formData.append('image', file);
                formData.append('_token', '{{ csrf_token() }}');
                
                try {
                    const response = await fetch('{{ route("superadmin.articles.uploadImage") }}', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    if (result.success) {
                        const range = editor.getSelection(true);
                        editor.insertEmbed(range.index, 'image', result.url);
                    } else {
                        alert(result.message || 'Gagal mengunggah gambar');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengunggah gambar');
                }
            }

            // Move the toolbar generated by quill into our custom container to style it better if needed, 
            // or just let Quill handle it (Quill inserts it before the editor-container).
            // Actually, we can specify the toolbar container in options, but let's just use the default.
            
            // Clean up the empty placeholder div we made for toolbar
            const toolbarPlaceholder = document.querySelector('.quill-toolbar-container');
            if(toolbarPlaceholder) toolbarPlaceholder.remove();

            const form = document.getElementById('article-form');
            form.addEventListener('submit', function() {
                // Remove empty paragraphs to avoid validation errors if actually empty
                let html = quill.root.innerHTML;
                if (html === '<p><br></p>') {
                    html = '';
                }
                document.querySelector('#body').value = html;
            });
        }
    });
</script>
@endpush
