@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Edit Portofolio" 
            subtitle="Ubah data portofolio/mahakarya yang sudah ada." 
            icon="fa-regular fa-pen-to-square">
            <x-slot:actions>
                <a href="{{ route('superadmin.portfolios.index') }}"
                    class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card class="max-w-4xl mx-auto mt-6">
            <form action="{{ route('superadmin.portfolios.update', $portfolio->hashid) }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-slate-700 mb-2">Judul Portofolio <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2.5 @error('title') border-red-500 @enderror" value="{{ old('title', $portfolio->title) }}" required>
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-2">Deskripsi <span class="text-red-500">*</span></label>
                        <textarea name="description" id="description" rows="5" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2.5 @error('description') border-red-500 @enderror" required>{{ old('description', $portfolio->description) }}</textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="tags" class="block text-sm font-medium text-slate-700 mb-2">Tags / Teknologi (Pisahkan dengan koma)</label>
                        <input type="text" name="tags" id="tags" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2.5 @error('tags') border-red-500 @enderror" value="{{ old('tags', implode(', ', $portfolio->tags ?? [])) }}" placeholder="Contoh: Laravel, Tailwind, React">
                        @error('tags') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="link_preview" class="block text-sm font-medium text-slate-700 mb-2">Link Live Preview</label>
                            <input type="url" name="link_preview" id="link_preview" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2.5 @error('link_preview') border-red-500 @enderror" value="{{ old('link_preview', $portfolio->link_preview) }}" placeholder="https://...">
                            @error('link_preview') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="link_github" class="block text-sm font-medium text-slate-700 mb-2">Link GitHub / Repo</label>
                            <input type="url" name="link_github" id="link_github" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2.5 @error('link_github') border-red-500 @enderror" value="{{ old('link_github', $portfolio->link_github) }}" placeholder="https://github.com/...">
                            @error('link_github') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="image" class="block text-sm font-medium text-slate-700 mb-2">Gambar / Thumbnail</label>
                        @if($portfolio->image_path)
                            <div class="mb-3">
                                <p class="text-xs text-slate-500 mb-2">Gambar saat ini:</p>
                                <img src="{{ Storage::url($portfolio->image_path) }}" alt="{{ $portfolio->title }}" class="w-48 rounded-lg border border-slate-200 shadow-sm">
                            </div>
                        @endif
                        <input type="file" name="image" id="image" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-200 rounded-lg">
                        <p class="text-xs text-slate-500 mt-2">Format: JPG, PNG, WEBP (Max 2MB). Kosongkan jika tidak ingin mengubah gambar.</p>
                        @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $portfolio->is_active) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            <span class="ml-3 text-sm font-medium text-slate-700">Publikasikan secara langsung (Aktif)</span>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition-colors">
                            <i class="fa-solid fa-save mr-2"></i> Perbarui Portofolio
                        </button>
                    </div>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page-layout>
@endsection
