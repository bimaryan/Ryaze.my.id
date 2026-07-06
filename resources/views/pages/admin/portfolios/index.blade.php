@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Manajemen Portofolio" 
            subtitle="Kelola mahakarya terbaru yang akan ditampilkan di halaman depan." 
            icon="fa-solid fa-briefcase">
            <x-slot:actions>
                <a href="{{ route('superadmin.portfolios.create') }}"
                    class="inline-flex justify-center items-center bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm border border-transparent">
                    <i class="fa-solid fa-plus mr-2"></i> Tambah Portofolio
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div>
            <div class="flex flex-col sm:flex-row justify-between items-center mb-4 px-1 gap-4">
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    {{-- Status Filter --}}
                    <div class="flex bg-slate-100 rounded-lg p-0.5">
                        <a href="{{ route('superadmin.portfolios.index', request()->except('status')) }}" 
                            class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ !request()->has('status') ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700' }}">
                            Semua
                        </a>
                        <a href="{{ route('superadmin.portfolios.index', array_merge(request()->except('status'), ['status' => '1'])) }}" 
                            class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ request('status') === '1' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700' }}">
                            Aktif
                        </a>
                        <a href="{{ route('superadmin.portfolios.index', array_merge(request()->except('status'), ['status' => '0'])) }}" 
                            class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ request('status') === '0' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700' }}">
                            Draft
                        </a>
                    </div>
                </div>
                
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                    <h2 class="text-lg font-bold text-slate-800">Daftar Portofolio</h2>
                </div>
                <div class="p-4 overflow-x-auto">
                    <table id="portfoliosTable" class="w-full text-sm text-left text-slate-500">
                        <thead class="text-xs text-slate-700 uppercase bg-slate-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 rounded-tl-lg">Judul</th>
                                <th scope="col" class="px-6 py-4">Tags</th>
                                <th scope="col" class="px-6 py-4">Status</th>
                                <th scope="col" class="px-6 py-4">Tgl Dibuat</th>
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
    </x-ui.page-layout>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#portfoliosTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{!! route('superadmin.portfolios.index', request()->query()) !!}",
            columns: [
                { data: 'title_html', name: 'title' },
                { data: 'tags_html', name: 'tags', orderable: false, searchable: false },
                { data: 'status_html', name: 'is_active', searchable: false },
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
@endpush

@push('scripts')
<script>
    function confirmDelete(button) {
        Swal.fire({
            title: 'Hapus Portofolio?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('form').submit();
            }
        })
    }
</script>
@endpush
