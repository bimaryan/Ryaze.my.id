@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Edit Portofolio" 
            subtitle="Ubah data portofolio/mahakarya yang sudah ada." 
            icon="fa-regular fa-pen-to-square">
            <x-slot:actions>
                <a href="{{ route('superadmin.portfolios.index') }}"
                    class="inline-flex justify-center items-center bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463] hover:bg-[#E8F0F9] dark:hover:bg-[#0D1828] text-[#0F1A2A] dark:text-white px-5 py-2.5  text-sm font-medium transition ">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card class="w-full mt-6">
            <form action="{{ route('superadmin.portfolios.update', $portfolio->hashid) }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Judul Portofolio <span class="text-rose-500 dark:text-rose-400">*</span></label>
                        <input type="text" name="title" id="title" class="transition-all @error('title') border-rose-500 @enderror w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" value="{{ old('title', $portfolio->title) }}" required>
                        @error('title') <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Deskripsi <span class="text-rose-500 dark:text-rose-400">*</span></label>
                        <textarea name="description" id="description" rows="5" class="transition-all @error('description') border-rose-500 @enderror w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" required>{{ old('description', $portfolio->description) }}</textarea>
                        @error('description') <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="tags" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Tags / Teknologi (Pisahkan dengan koma)</label>
                        <input type="text" name="tags" id="tags" class="transition-all @error('tags') border-rose-500 @enderror w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" value="{{ old('tags', implode(', ', $portfolio->tags ?? [])) }}" placeholder="Contoh: Laravel, Tailwind, React">
                        @error('tags') <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="link_preview" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Link Live Preview</label>
                            <input type="url" name="link_preview" id="link_preview" class="transition-all @error('link_preview') border-rose-500 @enderror w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" value="{{ old('link_preview', $portfolio->link_preview) }}" placeholder="https://...">
                            @error('link_preview') <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="link_github" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Link GitHub / Repo</label>
                            <input type="url" name="link_github" id="link_github" class="transition-all @error('link_github') border-rose-500 @enderror w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" value="{{ old('link_github', $portfolio->link_github) }}" placeholder="https://github.com/...">
                            @error('link_github') <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="link_journal" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">
                            <i class="fa-solid fa-book-open mr-1 text-[#1D5B96] dark:text-[#6BA8DC]"></i> Link Jurnal
                        </label>
                        <input type="url" name="link_journal" id="link_journal" class="transition-all @error('link_journal') border-rose-500 @enderror w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" value="{{ old('link_journal', $portfolio->link_journal) }}" placeholder="https://journal.example.com/...">
                        <p class="text-xs text-[#737373] dark:text-white/40 mt-1">Opsional. Isi jika portofolio ini dipublikasikan di jurnal ilmiah.</p>
                        @error('link_journal') <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="image" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Gambar / Thumbnail</label>
                        @if($portfolio->image_path)
                            <div class="mb-3">
                                <p class="text-xs text-[#737373] dark:text-white/40 mb-2">Gambar saat ini:</p>
                                <img src="{{ Storage::url($portfolio->image_path) }}" alt="{{ $portfolio->title }}" class="w-48  border border-[#C9D5E2] dark:border-[#1B2C44] ">
                            </div>
                        @endif
                        <input type="file" name="image" id="image" accept="image/*" class="block w-full text-sm text-[#737373] dark:text-white/40 file:mr-4 file:py-2.5 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-[#E8F0F9] file:text-[#1D5B96] hover:file:bg-[#C9D5E2] border border-[#C9D5E2] dark:border-[#1B2C44] ">
                        <p class="text-xs text-[#737373] dark:text-white/40 mt-2">Format: JPG, PNG, WEBP (Max 2MB). Kosongkan jika tidak ingin mengubah gambar.</p>
                        @error('image') <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="certificate" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">
                            <i class="fa-solid fa-certificate mr-1 text-amber-500 dark:text-amber-400"></i> Sertifikat Hak Cipta
                        </label>
                        @if($portfolio->certificate_path)
                            <div class="mb-3 flex items-center gap-3 p-3 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/40 ">
                                <i class="fa-solid fa-file-certificate text-amber-500 dark:text-amber-400 text-xl"></i>
                                <div>
                                    <p class="text-xs text-[#737373] dark:text-white/40 mb-1">Sertifikat saat ini:</p>
                                    <a href="{{ Storage::url($portfolio->certificate_path) }}" target="_blank" class="text-sm text-amber-700 dark:text-amber-300 font-medium hover:underline">
                                        <i class="fa-solid fa-external-link mr-1"></i> Lihat Sertifikat
                                    </a>
                                </div>
                            </div>
                        @endif
                        <input type="file" name="certificate" id="certificate" accept=".pdf,.jpg,.jpeg,.png" class="block w-full text-sm text-[#737373] dark:text-white/40 file:mr-4 file:py-2.5 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 border border-[#C9D5E2] dark:border-[#1B2C44] ">
                        <p class="text-xs text-[#737373] dark:text-white/40 mt-2">Format: PDF, JPG, PNG (Max 5MB). Kosongkan jika tidak ingin mengubah sertifikat.</p>
                        @error('certificate') <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="link_copyright" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">
                            <i class="fa-solid fa-shield-halved mr-1 text-amber-500 dark:text-amber-400"></i> Link Hak Cipta
                        </label>
                        <input type="url" name="link_copyright" id="link_copyright" class="transition-all @error('link_copyright') border-rose-500 @enderror w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" value="{{ old('link_copyright', $portfolio->link_copyright) }}" placeholder="https://pdki-indonesia.dgip.go.id/...">
                        <p class="text-xs text-[#737373] dark:text-white/40 mt-1">Opsional. Link ke halaman resmi pendaftaran hak cipta (DJKI, dll).</p>
                        @error('link_copyright') <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $portfolio->is_active) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-[#E3EAF1] dark:bg-[#1B2C44] peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#1D5B96]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[#C9D5E2] after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#1D5B96]"></div>
                            <span class="ml-3 text-sm font-medium text-[#0F1A2A] dark:text-white">Publikasikan secara langsung (Aktif)</span>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-[#E3EAF1] dark:border-[#1B2C44] flex justify-end">
                        <button type="submit" class="bg-[#1D5B96] hover:bg-[#2A6FAD] text-white font-bold py-2.5 px-6   transition-colors">
                            <i class="fa-solid fa-save mr-2"></i> Perbarui Portofolio
                        </button>
                    </div>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page-layout>
@endsection
