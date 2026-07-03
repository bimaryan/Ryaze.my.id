@extends('index')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header 
        title="Kategori Artikel" 
        subtitle="Kelola kategori untuk mengelompokkan artikel blog." 
        icon="fa-solid fa-folder-open">
        <x-slot:actions>
            <a href="{{ route('superadmin.articles.index') }}" class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg font-medium transition text-sm flex items-center gap-2 shadow-sm">
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
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4 font-medium text-slate-800">{{ $category->name }}</td>
                <td class="px-6 py-4 text-sm text-slate-500"><code class="bg-slate-100 px-2 py-0.5 rounded text-xs">{{ $category->slug }}</code></td>
                <td class="px-6 py-4 text-sm text-slate-500">{{ Str::limit($category->description, 60) ?: '-' }}</td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ $category->articles_count }} artikel</td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('superadmin.article_categories.edit', $category->hashid) }}" class="p-1.5 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <form action="{{ route('superadmin.article_categories.destroy', $category->hashid) }}" method="POST" class="inline delete-form">
                            @csrf @method('DELETE')
                            <button type="button" class="p-1.5 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition delete-btn">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                    <p class="font-medium">Belum ada kategori.</p>
                </td>
            </tr>
        @endforelse
    </x-ui.table>

    <div class="mt-4">{{ $categories->links() }}</div>
</x-ui.page-layout>
@endsection

@push('scripts')
<script nonce="{{ app('csp_nonce') ?? '' }}">
    document.addEventListener('DOMContentLoaded', function() {
        const deleteBtns = document.querySelectorAll('.delete-btn');
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                
                Swal.fire({
                    title: 'Hapus Kategori?',
                    text: "Artikel di kategori ini mungkin akan kehilangan kategorinya.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
