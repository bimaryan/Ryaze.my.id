@extends('index')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header 
        title="Kategori Artikel" 
        subtitle="Kelola kategori untuk mengelompokkan artikel blog." 
        icon="fa-solid fa-folder-open">
        <x-slot:actions>
            <a href="{{ route('superadmin.articles.index') }}" class="bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463] text-[#0F1A2A] dark:text-white hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828] px-4 py-2  font-medium transition text-sm flex items-center gap-2 ">
                <i class="fa-solid fa-arrow-left mr-1"></i> Artikel
            </a>
            <a href="{{ route('superadmin.article_categories.create') }}"
                class="inline-flex items-center bg-[#1D5B96] hover:bg-[#2A6FAD] text-white px-5 py-2.5  text-sm font-medium transition ">
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
            <tr class="hover:bg-[#F4F8FC] dark:bg-[#0D1828] dark:hover:bg-[#0D1828] transition-colors">
                <td class="px-6 py-4 font-medium text-[#0F1A2A] dark:text-white">{{ $category->name }}</td>
                <td class="px-6 py-4 text-sm text-[#737373] dark:text-white/40"><code class="bg-[#F0F6FC] dark:bg-[#0D1828] px-2 py-0.5 rounded text-xs">{{ $category->slug }}</code></td>
                <td class="px-6 py-4 text-sm text-[#737373] dark:text-white/40">{{ Str::limit($category->description, 60) ?: '-' }}</td>
                <td class="px-6 py-4 text-sm text-[#525252] dark:text-white/60">{{ $category->articles_count }} artikel</td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('superadmin.article_categories.edit', $category->hashid) }}" class="ui-icon-btn ui-icon-btn-primary">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <form action="{{ route('superadmin.article_categories.destroy', $category->hashid) }}" method="POST" class="inline delete-form">
                            @csrf @method('DELETE')
                            <button type="button" class="ui-icon-btn ui-icon-btn-danger delete-btn">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-[#737373] dark:text-white/40">
                    <p class="font-medium">Belum ada kategori.</p>
                </td>
            </tr>
        @endforelse
    </x-ui.table>

    <div class="mt-4">{{ $categories->links() }}</div>
</x-ui.page-layout>