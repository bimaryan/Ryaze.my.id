@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Edit Produk Digital" 
            subtitle="Ubah data produk digital." 
            icon="fa-solid fa-pen-to-square">
            <x-slot:actions>
                <a href="{{ route('superadmin.digital_products.index') }}"
                    class="inline-flex items-center bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463] hover:bg-[#E8F0F9] dark:hover:bg-[#0D1828] text-[#0F1A2A] dark:text-white px-5 py-2.5  text-sm font-medium transition ">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card class="w-full mt-6">
            <form action="{{ route('superadmin.digital_products.update', $product->hashid) }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf @method('PUT')
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Nama Produk <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}"
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none @error('name') border-rose-500 @enderror" required>
                            @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="slug" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Slug</label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $product->slug) }}"
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none @error('slug') border-rose-500 @enderror">
                            @error('slug') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Deskripsi</label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="price" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Harga (Rp) <span class="text-rose-500">*</span></label>
                            <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" min="1000" step="1000"
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none @error('price') border-rose-500 @enderror" required>
                            @error('price') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="cover" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Cover / Gambar</label>
                            @if($product->cover_path)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $product->cover_path) }}" alt="{{ $product->name }}" class="w-20 h-20  object-cover border border-[#C9D5E2] dark:border-[#1B2C44]">
                                </div>
                            @endif
                            <input type="file" name="cover" id="cover" accept="image/*"
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none file:mr-3 file:py-1 file:px-3 file:border-0 file:bg-[#E8F0F9] file:text-[#1D5B96] file:text-sm file:font-medium hover:file:bg-[#C9D5E2]">
                            @error('cover') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="file" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Ganti File Produk <span class="text-[#94A3B4] font-normal">(kosongkan jika tidak diganti)</span></label>
                        <p class="text-xs text-[#737373] dark:text-white/40 mb-2">
                            File saat ini: <span class="font-semibold">{{ $product->file_name }}</span> ({{ $product->download_count }} download)
                        </p>
                        <input type="file" name="file" id="file"
                            class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none file:mr-3 file:py-1 file:px-3 file:border-0 file:bg-[#E8F0F9] file:text-[#1D5B96] file:text-sm file:font-medium hover:file:bg-[#C9D5E2]">
                        @error('file') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-[#E3EAF1] dark:bg-[#1B2C44] peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#1D5B96]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[#C9D5E2] after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#1D5B96]"></div>
                            <span class="ml-3 text-sm font-medium text-[#0F1A2A] dark:text-white">Tampilkan di portfolio</span>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-[#E3EAF1] dark:border-[#1B2C44] flex justify-end">
                        <button type="submit" class="bg-[#1D5B96] hover:bg-[#2A6FAD] text-white font-bold py-2.5 px-6   transition-colors">
                            <i class="fa-solid fa-save mr-2"></i> Perbarui
                        </button>
                    </div>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page-layout>
@endsection
