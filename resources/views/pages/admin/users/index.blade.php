@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Manajemen Pengguna" 
            subtitle="Daftar semua klien dan admin di dalam sistem." 
            icon="fa-solid fa-users" 
        />

        <div id="users-container" class="transition-opacity duration-300">
            <div class="mb-6 flex overflow-x-auto gap-2 pb-2 hide-scrollbar">
                <a href="{{ route('superadmin.users.index') }}" class="px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition {{ !request()->has('role') || request('role') == '' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Semua Pengguna</a>
                <a href="{{ route('superadmin.users.index', ['role' => 'user_joki']) }}" class="px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition {{ request('role') == 'user_joki' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Klien Joki</a>
                <a href="{{ route('superadmin.users.index', ['role' => 'user_hosting']) }}" class="px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition {{ request('role') == 'user_hosting' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Klien Hosting</a>
                <a href="{{ route('superadmin.users.index', ['role' => 'admin']) }}" class="px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition {{ request('role') == 'admin' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Admin Joki/Hosting</a>
                <a href="{{ route('superadmin.users.index', ['role' => 'superadmin']) }}" class="px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition {{ request('role') == 'superadmin' ? 'bg-purple-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Superadmin</a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                    <h2 class="text-lg font-bold text-slate-800">Daftar Pengguna</h2>
                </div>
                <div class="p-4 overflow-x-auto">
                    <table id="usersTable" class="w-full text-sm text-left text-slate-500">
                        <thead class="text-xs text-slate-700 uppercase bg-slate-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 rounded-tl-lg">Nama Pengguna</th>
                                <th scope="col" class="px-6 py-4">Email Address</th>
                                <th scope="col" class="px-6 py-4">Role / Tipe Akun</th>
                                <th scope="col" class="px-6 py-4">Tanggal Daftar</th>
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
        $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{!! route('superadmin.users.index', request()->query()) !!}",
            columns: [
                { 
                    data: 'name', 
                    name: 'name',
                    render: function(data, type, row) {
                        return `<div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-sm uppercase shadow-sm border border-slate-200">
                                        ${row.avatar}
                                    </div>
                                    <span class="font-medium text-slate-800">${data}</span>
                                </div>`;
                    }
                },
                { data: 'email', name: 'email' },
                { data: 'role_badge', name: 'role', orderable: false, searchable: false },
                { data: 'created_at_formatted', name: 'created_at', searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data yang ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
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
