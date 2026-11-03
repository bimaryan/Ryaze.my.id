@extends('index')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header 
        title="Tulis Artikel Baru" 
        subtitle="Buat konten artikel untuk blog Ryaze." 
        icon="fa-solid fa-pen-fancy">
        <x-slot:actions>
            <a href="{{ route('superadmin.articles.index') }}" class="bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463] text-[#0F1A2A] dark:text-white hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828] px-4 py-2  font-medium transition text-sm flex items-center gap-2 ">
                Kembali
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <form id="article-form" action="{{ route('superadmin.articles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463]   p-6">
                    <h3 class="text-sm font-bold text-[#0F1A2A] dark:text-white mb-4 border-b border-[#E3EAF1] dark:border-[#1B2C44] pb-2">Konten Artikel</h3>
                    
                    <div class="mb-5">
                        <label for="title" class="block mb-1.5 text-sm font-medium text-[#0F1A2A] dark:text-white">Judul <span class="text-rose-500 dark:text-rose-400">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="text-[#0F1A2A] dark:text-white block p-3 w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="Judul artikel yang menarik...">
                        @error('title') <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-5">
                        <label for="excerpt" class="block mb-1.5 text-sm font-medium text-[#0F1A2A] dark:text-white">Ringkasan</label>
                        <textarea name="excerpt" id="excerpt" rows="3"
                            class="text-[#0F1A2A] dark:text-white block p-3 w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="Ringkasan singkat artikel (opsional, maks 500 karakter)...">{{ old('excerpt') }}</textarea>
                        @error('excerpt') <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-5">
                        <label for="body" class="block mb-1.5 text-sm font-medium text-[#0F1A2A] dark:text-white">Konten <span class="text-rose-500 dark:text-rose-400">*</span></label>
                        <div class="border border-[#C9D5E2] dark:border-[#1B2C44] rounded-t-lg bg-[#F4F8FC] dark:bg-[#0D1828] quill-toolbar-container"></div>
                        <div id="editor-container" class="bg-white dark:bg-[#0A1322] rounded-b-lg border-x border-b border-[#C9D5E2] dark:border-[#1B2C44] prose prose-sm max-w-none" style="height: 500px;">{!! old('body') !!}</div>
                        <input type="hidden" name="body" id="body" value="{{ old('body') }}">
                        @error('body') <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- SEO Settings --}}
                <div class="bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463]   p-6">
                    <h3 class="text-sm font-bold text-[#0F1A2A] dark:text-white mb-4 border-b border-[#E3EAF1] dark:border-[#1B2C44] pb-2">
                        <i class="fa-solid fa-magnifying-glass-chart text-[#1D5B96] dark:text-[#6BA8DC] mr-2"></i>Pengaturan SEO
                    </h3>
                    <div class="mb-5">
                        <label for="meta_title" class="block mb-1.5 text-sm font-medium text-[#0F1A2A] dark:text-white">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" maxlength="70"
                            class="text-[#0F1A2A] dark:text-white block p-3 w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="Override judul untuk mesin pencari (maks 70 karakter)">
                    </div>
                    <div>
                        <label for="meta_description" class="block mb-1.5 text-sm font-medium text-[#0F1A2A] dark:text-white">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" rows="2" maxlength="160"
                            class="text-[#0F1A2A] dark:text-white block p-3 w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="Deskripsi singkat untuk hasil pencarian Google (maks 160 karakter)">{{ old('meta_description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Publish Settings --}}
                <div class="bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463]   p-6">
                    <h3 class="text-sm font-bold text-[#0F1A2A] dark:text-white mb-4 border-b border-[#E3EAF1] dark:border-[#1B2C44] pb-2">Publikasi</h3>
                    
                    <div class="mb-5">
                        <label for="status" class="block mb-1.5 text-sm font-medium text-[#0F1A2A] dark:text-white">Status</label>
                        <select name="status" id="status" class="text-[#0F1A2A] dark:text-white block p-3 w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>

                    <div class="mb-5">
                        <label for="category_id" class="block mb-1.5 text-sm font-medium text-[#0F1A2A] dark:text-white">Kategori</label>
                        <select name="category_id" id="category_id" class="text-[#0F1A2A] dark:text-white block p-3 w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition">
                            <option value="">Tanpa Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-3 mb-5">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                            class="w-4 h-4 text-[#1D5B96] dark:text-[#6BA8DC] bg-white dark:bg-[#0A1322] border-[#C9D5E2] dark:border-[#1B2C44] accent-[#1D5B96]">
                        <label for="is_featured" class="text-sm font-medium text-[#0F1A2A] dark:text-white">Jadikan Sorotan</label>
                    </div>

                    <button type="submit" class="w-full bg-[#1D5B96] hover:bg-[#2A6FAD] text-white font-semibold text-sm py-3  transition ">
                        <i class="fa-solid fa-paper-plane mr-2"></i>Simpan Artikel
                    </button>
                </div>

                {{-- Cover Image --}}
                <div class="bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463]   p-6">
                    <h3 class="text-sm font-bold text-[#0F1A2A] dark:text-white mb-4 border-b border-[#E3EAF1] dark:border-[#1B2C44] pb-2">Gambar Sampul</h3>
                    <input type="file" name="cover_image" id="cover_image" accept="image/*"
                        class="block w-full text-sm text-[#737373] dark:text-white/40 file:mr-3 file:py-2 file:px-4 file:border-0 file:text-sm file:font-medium file:bg-[#E8F0F9] file:text-[#1D5B96] hover:file:bg-[#C9D5E2] cursor-pointer">
                    <p class="text-xs text-[#94A3B4] dark:text-white/35 mt-2">Format: JPG, PNG, WEBP. Maks: 3MB.</p>
                    @error('cover_image') <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tags --}}
                <div class="bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463]   p-6">
                    <h3 class="text-sm font-bold text-[#0F1A2A] dark:text-white mb-4 border-b border-[#E3EAF1] dark:border-[#1B2C44] pb-2">Tags</h3>
                    <input type="text" name="tags" id="tags" value="{{ old('tags') }}"
                        class="text-[#0F1A2A] dark:text-white block p-3 w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="laravel, php, tutorial">
                    <p class="text-xs text-[#94A3B4] dark:text-white/35 mt-2">Pisahkan dengan koma.</p>
                </div>
            </div>
        </div>
    </form>

    <!-- Quill Editor Initialization -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <style>
        .ql-toolbar.ql-snow { border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; background-color: #f8fafc; border-color: #e2e8f0; }
        .ql-container.ql-snow { border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem; border-color: #e2e8f0; font-family: inherit; font-size: 0.875rem; }
        .ql-editor { min-height: 400px; }
    </style>
    <script nonce="{{ csp_nonce() }}">
        (function() {
            var editor = document.getElementById('editor-container');
            if (!editor) return;
            
            var customToolbar = document.querySelector('.quill-toolbar-container');
            if (customToolbar) customToolbar.remove();

            var quill = new Quill('#editor-container', {
                theme: 'snow',
                placeholder: 'Tulis konten artikel di sini...',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, 4, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image', 'video'],
                        ['clean']
                    ]
                }
            });

            var form = document.getElementById('article-form');
            var bodyInput = document.getElementById('body');

            form.addEventListener('submit', function() {
                bodyInput.value = quill.root.innerHTML;
            });
        })();
    </script>
</x-ui.page-layout>