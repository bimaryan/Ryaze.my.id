@extends('index')
@section('content')
<x-ui.page-layout>
    <x-ui.page-header title="Tambah Kategori" subtitle="Buat kategori baru untuk mengelompokkan artikel." icon="fa-solid fa-folder-plus">
        <x-slot:actions>
            <a href="{{ route('superadmin.article_categories.index') }}" class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg font-medium transition text-sm shadow-sm">Kembali</a>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="max-w-2xl">
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
            <form action="{{ route('superadmin.article_categories.store') }}" method="POST">
                @csrf
                <div class="mb-5">
                    <label for="name" class="block mb-1.5 text-sm font-medium text-slate-700">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="text-slate-800 block p-3 w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="Contoh: Tutorial">
                    @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-5">
                    <label for="description" class="block mb-1.5 text-sm font-medium text-slate-700">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" class="text-slate-800 block p-3 w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="Deskripsi singkat kategori (opsional)">{{ old('description') }}</textarea>
                    @error('description') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm py-3 px-6 rounded-lg transition shadow-sm">
                    <i class="fa-solid fa-save mr-2"></i>Simpan Kategori
                </button>
            </form>
        </div>
    </div>
</x-ui.page-layout>
@endsection
