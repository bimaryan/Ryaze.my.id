@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Tambah Info Update"
            subtitle="Tulis info yang akan dibaca user di halaman /updates."
            icon="fa-solid fa-bullhorn">
            <x-slot:actions>
                <a href="{{ route('superadmin.info_updates.index') }}"
                    class="ui-btn ui-btn-md ui-btn-icon ui-btn-secondary" title="Kembali" aria-label="Kembali">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card class="w-full mt-6">
            <form action="{{ route('superadmin.info_updates.store') }}" method="POST" class="p-6">
                @csrf

                @php
                    $selectedCategory = old('category', \App\Models\InfoUpdate::CATEGORY_FEATURE);
                    $selectedPriority = old('priority', \App\Models\InfoUpdate::PRIORITY_NORMAL);
                    // published_at kosong = tayang langsung begitu disimpan.
                    $publishedAt = old('published_at');
                @endphp

                <div class="space-y-6">
                    {{-- Judul --}}
                    <div>
                        <label for="title" class="ui-label">
                            Judul <span class="text-red-500 dark:text-red-400">*</span>
                        </label>
                        <input type="text" name="title" id="title" required
                               placeholder="Contoh: Ryaze Sekarang Otomatis Deploy dari GitHub"
                               class="ui-field @error('title') border-red-500 @enderror"
                               value="{{ old('title') }}">
                        @error('title')
                            <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi + AI --}}
                    <div>
                        <label for="description" class="ui-label">
                            Deskripsi <span class="text-red-500 dark:text-red-400">*</span>
                        </label>
                        @include('pages.admin.info_updates.partials.ai-description')
                        <textarea name="description" id="description" rows="5" required
                                  placeholder="Jelaskan informasinya dengan bahasa yang mudah dipahami user."
                                  class="ui-field @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kategori / Prioritas / Versi --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="category" class="ui-label">Kategori <span class="text-red-500 dark:text-red-400">*</span></label>
                            <select name="category" id="category" required
                                    class="ui-field @error('category') border-red-500 @enderror">
                                @foreach(\App\Models\InfoUpdate::CATEGORY_META as $key => $meta)
                                    <option value="{{ $key }}" @selected($selectedCategory === $key)>{{ $meta['label'] }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Menentukan nada tulisan dan warna label.</p>
                            @error('category')
                                <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="priority" class="ui-label">Prioritas <span class="text-red-500 dark:text-red-400">*</span></label>
                            <select name="priority" id="priority" required
                                    class="ui-field @error('priority') border-red-500 @enderror">
                                @foreach(\App\Models\InfoUpdate::PRIORITY_META as $key => $meta)
                                    <option value="{{ $key }}" @selected($selectedPriority === $key)>{{ $meta['label'] }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Update "Darurat" ditandai paling menonjol di halaman user.</p>
                            @error('priority')
                                <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="version" class="ui-label">Versi</label>
                            <input type="text" name="version" id="version" placeholder="v2.1.0"
                                   class="ui-field @error('version') border-red-500 @enderror"
                                   value="{{ old('version') }}">
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Opsional. Untuk update fitur.</p>
                            @error('version')
                                <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Jadwal tayang --}}
                    <div>
                        <label for="published_at" class="ui-label">Jadwal Tayang</label>
                        <input type="datetime-local" name="published_at" id="published_at"
                               class="ui-field @error('published_at') border-red-500 @enderror"
                               value="{{ $publishedAt }}">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Kosongkan untuk tayang langsung. Isi tanggal untuk menyiapkannya lebih dulu — baru muncul di halaman user setelah waktunya tiba.
                        </p>
                        @error('published_at')
                            <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" @checked(old('is_active', true))>
                            <div class="peer ui-switch ui-switch-primary"></div>
                            <span class="ml-3 text-sm font-medium text-slate-700 dark:text-slate-200">Tampilkan ke user</span>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                        <a href="{{ route('superadmin.info_updates.index') }}"
                           class="ui-btn ui-btn-md ui-btn-secondary">
                            Batal
                        </a>
                        <button type="submit"
                                class="ui-btn ui-btn-md ui-btn-primary">
                            <i class="fa-solid fa-save mr-2"></i> Simpan Info Update
                        </button>
                    </div>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page-layout>
@endsection