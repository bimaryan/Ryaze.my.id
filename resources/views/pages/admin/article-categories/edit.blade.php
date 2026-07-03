@extends('index')
@section('content')
<x-ui.page-layout>
    <x-ui.page-header title="Edit Kategori" subtitle="Perbarui kategori: {{ $category->name }}" icon="fa-solid fa-folder-open">
        <x-slot:actions>
            <a href="{{ route('superadmin.article_categories.index') }}" class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg font-medium transition text-sm shadow-sm">Kembali</a>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="max-w-2xl">
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
            <form action="{{ route('superadmin.article_categories.update', $category->hashid) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-5">
                    <label for="name" class="block mb-1.5 text-sm font-medium text-slate-700">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required class="bg-white border border-slate-200 text-slate-800 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-3">
                    @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-5">
                    <label for="description" class="block mb-1.5 text-sm font-medium text-slate-700">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" class="bg-white border border-slate-200 text-slate-800 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-3">{{ old('description', $category->description) }}</textarea>
                    @error('description') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm py-3 px-6 rounded-lg transition shadow-sm">
                    <i class="fa-solid fa-save mr-2"></i>Perbarui Kategori
                </button>
            </form>
        </div>
    </div>
</x-ui.page-layout>
@endsection
