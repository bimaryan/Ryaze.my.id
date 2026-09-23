@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Tambah Pendidikan" 
            subtitle="Tambahkan data riwayat pendidikan baru." 
            icon="fa-solid fa-plus">
            <x-slot:actions>
                <a href="{{ route('superadmin.educations.index') }}"
                    class="inline-flex items-center bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-200 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card class="w-full mt-6">
            <form action="{{ route('superadmin.educations.store') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="period" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Periode <span class="text-red-500">*</span></label>
                            <input type="text" name="period" id="period" value="{{ old('period') }}" placeholder="Contoh: 2022 — Sekarang"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none @error('period') border-red-500 @enderror" required>
                            @error('period') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="degree" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Gelar / Jurusan <span class="text-red-500">*</span></label>
                            <input type="text" name="degree" id="degree" value="{{ old('degree') }}" placeholder="Contoh: S1 Teknik Informatika"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none @error('degree') border-red-500 @enderror" required>
                            @error('degree') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="institution" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Institusi <span class="text-red-500">*</span></label>
                            <input type="text" name="institution" id="institution" value="{{ old('institution') }}" placeholder="Contoh: Universitas Bina Darma"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none @error('institution') border-red-500 @enderror" required>
                            @error('institution') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="location" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Lokasi</label>
                            <input type="text" name="location" id="location" value="{{ old('location') }}" placeholder="Contoh: Palembang, Sumatera Selatan"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Deskripsi</label>
                        <textarea name="description" id="description" rows="3" placeholder="Deskripsi singkat tentang pendidikan ini..."
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="relative">
                            <x-ui.icon-picker name="icon" value="{{ old('icon', 'fa-graduation-cap') }}" label="Icon" />
                        </div>
                        <div>
                            <label for="color" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Warna</label>
                            <input type="color" name="color" id="color" value="{{ old('color', '#6366f1') }}"
                                class="w-full h-10 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-2 py-1 cursor-pointer">
                        </div>
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Urutan</label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                        </div>
                    </div>

                    <div>
                        <label for="tags" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Tags (Pisahkan dengan koma)</label>
                        <input type="text" name="tags" id="tags" value="{{ old('tags') }}" placeholder="Contoh: Informatika, Programming, Software Engineering"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                    </div>

                    <div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', 1) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            <span class="ml-3 text-sm font-medium text-slate-700 dark:text-slate-200">Tampilkan di portfolio</span>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition-colors">
                            <i class="fa-solid fa-save mr-2"></i> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page-layout>
@endsection
