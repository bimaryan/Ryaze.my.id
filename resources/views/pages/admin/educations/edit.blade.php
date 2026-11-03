@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Edit Pendidikan" 
            subtitle="Ubah data riwayat pendidikan." 
            icon="fa-solid fa-pen-to-square">
            <x-slot:actions>
                <a href="{{ route('superadmin.educations.index') }}"
                    class="inline-flex items-center bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463] hover:bg-[#E8F0F9] dark:hover:bg-[#0D1828] text-[#0F1A2A] dark:text-white px-5 py-2.5  text-sm font-medium transition ">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card class="w-full mt-6">
            <form action="{{ route('superadmin.educations.update', $education->hashid) }}" method="POST" class="p-6">
                @csrf @method('PUT')
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="period" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Periode <span class="text-rose-500">*</span></label>
                            <input type="text" name="period" id="period" value="{{ old('period', $education->period) }}" placeholder="Contoh: 2022 — Sekarang"
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none @error('period') border-rose-500 @enderror" required>
                            @error('period') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="degree" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Gelar / Jurusan <span class="text-rose-500">*</span></label>
                            <input type="text" name="degree" id="degree" value="{{ old('degree', $education->degree) }}" placeholder="Contoh: S1 Teknik Informatika"
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none @error('degree') border-rose-500 @enderror" required>
                            @error('degree') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="institution" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Institusi <span class="text-rose-500">*</span></label>
                            <input type="text" name="institution" id="institution" value="{{ old('institution', $education->institution) }}" placeholder="Contoh: Universitas Bina Darma"
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none @error('institution') border-rose-500 @enderror" required>
                            @error('institution') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="location" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Lokasi</label>
                            <input type="text" name="location" id="location" value="{{ old('location', $education->location) }}" placeholder="Contoh: Palembang, Sumatera Selatan"
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none">
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Deskripsi</label>
                        <textarea name="description" id="description" rows="3" placeholder="Deskripsi singkat tentang pendidikan ini..."
                            class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none">{{ old('description', $education->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="relative">
                            <x-ui.icon-picker name="icon" value="{{ old('icon', $education->icon) }}" label="Icon" />
                        </div>
                        <div>
                            <label for="color" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Warna</label>
                            <input type="color" name="color" id="color" value="{{ old('color', $education->color) }}"
                                class="w-full h-10 bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44]  px-2 py-1 cursor-pointer">
                        </div>
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Urutan</label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $education->sort_order) }}"
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none">
                        </div>
                    </div>

                    <div>
                        <label for="tags" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Tags (Pisahkan dengan koma)</label>
                        <input type="text" name="tags" id="tags" value="{{ old('tags', is_array($education->tags) ? implode(', ', $education->tags) : $education->tags) }}" placeholder="Contoh: Informatika, Programming"
                            class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none">
                    </div>

                    <div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $education->is_active) ? 'checked' : '' }}>
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
