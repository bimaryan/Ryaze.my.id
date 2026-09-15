@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Tambah Informasi" 
            subtitle="Buat informasi atau pengumuman baru untuk pengguna." 
            icon="fa-solid fa-plus">
            <x-slot:actions>
                <a href="{{ route('superadmin.announcements.index') }}"
                    class="inline-flex justify-center items-center bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-200 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card class="w-full mt-6">
            <form action="{{ route('superadmin.announcements.store') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Judul Informasi <span class="text-red-500 dark:text-red-400">*</span></label>
                            <input type="text" name="title" id="title" class="transition-all @error('title') border-red-500 @enderror w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" value="{{ old('title') }}" required placeholder="Contoh: Pembaruan Sistem v2.5">
                            @error('title') <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="content" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Isi Informasi</label>
                        <textarea name="content" id="content" rows="5" class="transition-all @error('content') border-red-500 @enderror w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="Jelaskan detail informasi atau pembaruan...">{{ old('content') }}</textarea>
                        @error('content') <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="type" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Tipe Informasi <span class="text-red-500 dark:text-red-400">*</span></label>
                            <select name="type" id="type" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                                <option value="info" {{ old('type') === 'info' ? 'selected' : '' }}>ℹ️ Info</option>
                                <option value="update" {{ old('type') === 'update' ? 'selected' : '' }}>🔄 Update</option>
                                <option value="maintenance" {{ old('type') === 'maintenance' ? 'selected' : '' }}>🔧 Maintenance</option>
                                <option value="warning" {{ old('type') === 'warning' ? 'selected' : '' }}>⚠️ Warning</option>
                            </select>
                            @error('type') <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="audience" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Target Audience <span class="text-red-500 dark:text-red-400">*</span></label>
                            <select name="audience" id="audience" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                                <option value="all" {{ old('audience') === 'all' ? 'selected' : '' }}>Semua Pengguna</option>
                                <option value="hosting" {{ old('audience') === 'hosting' ? 'selected' : '' }}>Hosting Saja</option>
                                <option value="joki" {{ old('audience') === 'joki' ? 'selected' : '' }}>Joki Saja</option>
                            </select>
                            @error('audience') <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="starts_at" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Mulai Ditampilkan</label>
                            <input type="datetime-local" name="starts_at" id="starts_at" class="transition-all @error('starts_at') border-red-500 @enderror w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" value="{{ old('starts_at') }}">
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Kosongkan jika ingin langsung ditampilkan.</p>
                            @error('starts_at') <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="expires_at" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Berakhir Ditampilkan</label>
                            <input type="datetime-local" name="expires_at" id="expires_at" class="transition-all @error('expires_at') border-red-500 @enderror w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" value="{{ old('expires_at') }}">
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Kosongkan jika tidak ada batas waktu.</p>
                            @error('expires_at') <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            <span class="ml-3 text-sm font-medium text-slate-700 dark:text-slate-200">Aktifkan sekarang</span>
                        </label>

                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_pinned" value="1" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                            <span class="ml-3 text-sm font-medium text-slate-700 dark:text-slate-200">Sematkan (Pinned)</span>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition-colors">
                            <i class="fa-solid fa-save mr-2"></i> Simpan Informasi
                        </button>
                    </div>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page-layout>
@endsection
