@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Tambah Promo Event" 
            subtitle="Buat promo event baru untuk ditampilkan di dashboard." 
            icon="fa-solid fa-plus">
            <x-slot:actions>
                <a href="{{ route('admin.promo_events.index') }}"
                    class="inline-flex justify-center items-center bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-200 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
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
                            <label for="title" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Judul Promo <span class="text-red-500 dark:text-red-400">*</span></label>
                            <input type="text" name="title" id="title" class="transition-all @error('title') border-red-500 @enderror w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" value="{{ old('title') }}" required>
                            @error('title') <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="target_url" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Target URL</label>
                            <input type="url" name="target_url" id="target_url" class="transition-all @error('target_url') border-red-500 @enderror w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" value="{{ old('target_url') }}" placeholder="https://...">
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Opsional. Link saat banner diklik.</p>
                            @error('target_url') <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Deskripsi Promo</label>
                        <textarea name="description" id="description" rows="3" class="transition-all @error('description') border-red-500 @enderror w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">{{ old('description') }}</textarea>
                        @error('description') <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Mulai Berlaku <span class="text-red-500 dark:text-red-400">*</span></label>
                            <input type="datetime-local" name="start_date" id="start_date" class="transition-all @error('start_date') border-red-500 @enderror w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" value="{{ old('start_date', now()->format('Y-m-d\TH:i')) }}" required>
                            @error('start_date') <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Selesai Berlaku <span class="text-red-500 dark:text-red-400">*</span></label>
                            <input type="datetime-local" name="end_date" id="end_date" class="transition-all @error('end_date') border-red-500 @enderror w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" value="{{ old('end_date', now()->addDays(7)->format('Y-m-d\TH:i')) }}" required>
                            @error('end_date') <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="banner_image" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Banner Image</label>
                        <input type="file" name="banner_image" id="banner_image" accept="image/*" class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-200 dark:border-slate-700 rounded-lg">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Opsional. Rekomendasi rasio gambar landscape (misal: 1200x400).</p>
                        @error('banner_image') <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            <span class="ml-3 text-sm font-medium text-slate-700 dark:text-slate-200">Aktifkan promo ini sekarang</span>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition-colors">
                            <i class="fa-solid fa-save mr-2"></i> Simpan Promo
                        </button>
                    </div>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page-layout>
@endsection
