@extends('index')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header 
        title="Manajemen Artikel" 
        subtitle="Kelola semua artikel dan konten blog yang dipublikasikan." 
        icon="fa-solid fa-newspaper">
        <x-slot:actions>
            <a href="{{ route('superadmin.article_categories.index') }}"
                class="inline-flex items-center bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-4 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fa-solid fa-folder mr-2"></i> Kategori
            </a>
            <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')"
                class="inline-flex items-center bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fa-solid fa-file-excel mr-2"></i> Import Excel
            </button>
            <a href="{{ route('superadmin.articles.create') }}"
                class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fa-solid fa-plus mr-2"></i> Tulis Artikel
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <div>
        <div class="flex flex-col sm:flex-row justify-between items-center mb-4 px-1 gap-4">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                {{-- Status Filter --}}
                <div class="flex bg-slate-100 rounded-lg p-0.5">
                    <a href="{{ route('superadmin.articles.index', request()->except('status')) }}" 
                        class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ !request('status') ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700' }}">
                        Semua
                    </a>
                    <a href="{{ route('superadmin.articles.index', array_merge(request()->except('status'), ['status' => 'draft'])) }}" 
                        class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ request('status') == 'draft' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700' }}">
                        Draft
                    </a>
                    <a href="{{ route('superadmin.articles.index', array_merge(request()->except('status'), ['status' => 'published'])) }}" 
                        class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ request('status') == 'published' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700' }}">
                        Published
                    </a>
                </div>
            </div>
            
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mt-4">
            <div class="p-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                <h2 class="text-lg font-bold text-slate-800">Daftar Artikel</h2>
            </div>
            <div class="p-4 overflow-x-auto">
                <table id="articlesTable" class="w-full text-sm text-left text-slate-500">
                    <thead class="text-xs text-slate-700 uppercase bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 rounded-tl-lg">Artikel</th>
                            <th scope="col" class="px-6 py-4">Penulis</th>
                            <th scope="col" class="px-6 py-4">Status</th>
                            <th scope="col" class="px-6 py-4">Views</th>
                            <th scope="col" class="px-6 py-4">Tanggal</th>
                            <th scope="col" class="px-6 py-4 text-center rounded-tr-lg">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will inject rows here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full flex bg-slate-900/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-md max-h-full m-auto">
            <div class="relative bg-white rounded-xl shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b border-slate-100 rounded-t">
                    <h3 class="text-lg font-semibold text-slate-900">
                        Import Artikel (Excel/CSV)
                    </h3>
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="text-slate-400 bg-transparent hover:bg-slate-200 hover:text-slate-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <form action="{{ route('superadmin.articles.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-4 md:p-5 space-y-4">
                        <div class="bg-indigo-50 text-indigo-700 p-3 rounded-lg text-xs font-medium border border-indigo-100 mb-4">
                            Unduh template Excel untuk memastikan format kolom sudah benar sebelum mengunggah.
                            <a href="{{ route('superadmin.articles.template') }}" class="inline-block mt-2 underline font-bold"><i class="fa-solid fa-download"></i> Download Template</a>
                        </div>
                        
                        <div>
                            <label class="block mb-2 text-sm font-medium text-slate-900" for="file">Upload File Excel/CSV</label>
                            <input class="block w-full text-sm text-slate-900 border border-slate-300 rounded-lg cursor-pointer bg-slate-50 focus:outline-none p-2.5" id="file" type="file" name="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                        </div>
                    </div>
                    <div class="flex items-center p-4 md:p-5 border-t border-slate-100 rounded-b">
                        <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition">Import Data</button>
                        <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="py-2.5 px-5 ms-3 text-sm font-medium text-slate-900 focus:outline-none bg-white rounded-lg border border-slate-200 hover:bg-slate-100 hover:text-indigo-700 transition">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-ui.page-layout>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#articlesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{!! route('superadmin.articles.index', request()->query()) !!}",
            columns: [
                { data: 'title_html', name: 'title' },
                { data: 'author', name: 'user.name' },
                { data: 'status_html', name: 'status', searchable: false },
                { data: 'views', name: 'views', searchable: false },
                { data: 'created_at_formatted', name: 'created_at', searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                zeroRecords: "Tidak ada data yang ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });
    });
</script>

@push('scripts')
<script nonce="{{ app('csp_nonce') ?? '' }}">
    document.addEventListener('DOMContentLoaded', function() {
        const deleteBtns = document.querySelectorAll('.delete-btn');
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                
                Swal.fire({
                    title: 'Hapus Artikel?',
                    text: "Artikel yang dihapus tidak dapat dikembalikan!",
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
