@extends('index')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header 
        title="Kategori Artikel" 
        subtitle="Kelola kategori untuk mengelompokkan artikel blog." 
        icon="fa-solid fa-folder-open">
        <x-slot:actions>
            <a href="{{ route('superadmin.articles.index') }}" class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/40 px-4 py-2 rounded-lg font-medium transition text-sm flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-arrow-left mr-1"></i> Artikel
            </a>
            <a href="{{ route('superadmin.article_categories.create') }}"
                class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fa-solid fa-plus mr-2"></i> Tambah Kategori
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.table>
        <x-slot:head>
            <th scope="col" class="px-6 py-4">Nama</th>
            <th scope="col" class="px-6 py-4">Slug</th>
            <th scope="col" class="px-6 py-4">Deskripsi</th>
            <th scope="col" class="px-6 py-4">Jumlah Artikel</th>
            <th scope="col" class="px-6 py-4 text-center">Aksi</th>
        </x-slot:head>

        @forelse($categories as $category)
            <tr class="hover:bg-slate-50 dark:bg-slate-800/50 dark:hover:bg-slate-700/40 transition-colors">
                <td class="px-6 py-4 font-medium text-slate-800 dark:text-slate-100">{{ $category->name }}</td>
                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400"><code class="bg-slate-100 dark:bg-slate-700/50 px-2 py-0.5 rounded text-xs">{{ $category->slug }}</code></td>
                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">{{ Str::limit($category->description, 60) ?: '-' }}</td>
                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $category->articles_count }} artikel</td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('superadmin.article_categories.edit', $category->hashid) }}" class="p-1.5 text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 dark:hover:bg-indigo-500/20 rounded-lg transition">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <form action="{{ route('superadmin.article_categories.destroy', $category->hashid) }}" method="POST" class="inline delete-form">
                            @csrf @method('DELETE')
                            <button type="button" class="p-1.5 text-rose-600 dark:text-rose-300 bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-100 dark:hover:bg-rose-500/20 rounded-lg transition delete-btn">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                    <p class="font-medium">Belum ada kategori.</p>
                </td>
            </tr>
        @endforelse
    </x-ui.table>

    <div class="mt-4">{{ $categories->links() }}</div>
</x-ui.page-layout>