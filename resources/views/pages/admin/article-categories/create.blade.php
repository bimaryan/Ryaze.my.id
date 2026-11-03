@extends('index')
@section('content')
<x-ui.page-layout>
    <x-ui.page-header title="Tambah Kategori" subtitle="Buat kategori baru untuk mengelompokkan artikel." icon="fa-solid fa-folder-plus">
        <x-slot:actions>
            <a href="{{ route('superadmin.article_categories.index') }}" class="bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463] text-[#0F1A2A] dark:text-white hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828] px-4 py-2  font-medium transition text-sm ">Kembali</a>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="max-w-2xl">
        <div class="bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463]   p-6">
            <form action="{{ route('superadmin.article_categories.store') }}" method="POST">
                @csrf
                <div class="mb-5">
                    <label for="name" class="block mb-1.5 text-sm font-medium text-[#0F1A2A] dark:text-white">Nama Kategori <span class="text-rose-500 dark:text-rose-400">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="text-[#0F1A2A] dark:text-white block p-3 w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="Contoh: Tutorial">
                    @error('name') <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-5">
                    <label for="description" class="block mb-1.5 text-sm font-medium text-[#0F1A2A] dark:text-white">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" class="text-[#0F1A2A] dark:text-white block p-3 w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="Deskripsi singkat kategori (opsional)">{{ old('description') }}</textarea>
                    @error('description') <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="bg-[#1D5B96] hover:bg-[#2A6FAD] text-white font-semibold text-sm py-3 px-6  transition ">
                    <i class="fa-solid fa-save mr-2"></i>Simpan Kategori
                </button>
            </form>
        </div>
    </div>
</x-ui.page-layout>
@endsection
