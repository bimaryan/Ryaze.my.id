@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Tambah Promo Event" 
            subtitle="Buat promo event baru untuk ditampilkan di dashboard." 
            icon="fa-solid fa-plus">
            <x-slot:actions>
                <a href="{{ route('admin.promo_events.index') }}"
                    class="inline-flex justify-center items-center bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463] hover:bg-[#E8F0F9] dark:hover:bg-[#0D1828] text-[#0F1A2A] dark:text-white px-5 py-2.5  text-sm font-medium transition ">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>
        
        <x-ui.card class="w-full mt-6">
            <form action="{{ route('admin.promo_events.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Judul Promo <span class="text-rose-500 dark:text-rose-400">*</span></label>
                            <input type="text" name="title" id="title" class="transition-all @error('title') border-rose-500 @enderror w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" value="{{ old('title') }}" required>
                            @error('title') <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="target_url" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Target URL</label>
                            <input type="url" name="target_url" id="target_url" class="transition-all @error('target_url') border-rose-500 @enderror w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" value="{{ old('target_url') }}" placeholder="https://...">
                            <p class="text-xs text-[#737373] dark:text-white/40 mt-1">Opsional. Link saat banner diklik.</p>
                            @error('target_url') <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Deskripsi Promo</label>
                        @include('pages.admin.promo_events.partials.ai-description')
                        <textarea name="description" id="description" rows="3" class="transition-all @error('description') border-rose-500 @enderror w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition">{{ old('description') }}</textarea>
                        @error('description') <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Mulai Berlaku <span class="text-rose-500 dark:text-rose-400">*</span></label>
                            <input type="datetime-local" name="start_date" id="start_date" class="transition-all @error('start_date') border-rose-500 @enderror w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" value="{{ old('start_date', now()->format('Y-m-d\TH:i')) }}" required>
                            @error('start_date') <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Selesai Berlaku <span class="text-rose-500 dark:text-rose-400">*</span></label>
                            <input type="datetime-local" name="end_date" id="end_date" class="transition-all @error('end_date') border-rose-500 @enderror w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" value="{{ old('end_date', now()->addDays(7)->format('Y-m-d\TH:i')) }}" required>
                            @error('end_date') <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="banner_image" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Banner Image</label>
                        <input type="file" name="banner_image" id="banner_image" accept="image/*" class="block w-full text-sm text-[#737373] dark:text-white/40 file:mr-4 file:py-2.5 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-[#E8F0F9] file:text-[#1D5B96] hover:file:bg-[#C9D5E2] border border-[#C9D5E2] dark:border-[#1B2C44] ">
                        <p class="text-xs text-[#737373] dark:text-white/40 mt-2">Opsional. Rekomendasi rasio gambar landscape (misal: 1200x400).</p>
                        @error('banner_image') <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-[#E3EAF1] dark:bg-[#1B2C44] peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#1D5B96]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[#C9D5E2] after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#1D5B96]"></div>
                            <span class="ml-3 text-sm font-medium text-[#0F1A2A] dark:text-white">Aktifkan promo ini sekarang</span>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-[#E3EAF1] dark:border-[#1B2C44] flex justify-end">
                        <button type="submit" class="bg-[#1D5B96] hover:bg-[#2A6FAD] text-white font-bold py-2.5 px-6   transition-colors">
                            <i class="fa-solid fa-save mr-2"></i> Simpan Promo
                        </button>
                    </div>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page-layout>
@endsection
