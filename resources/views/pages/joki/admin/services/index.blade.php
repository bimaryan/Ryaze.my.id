@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Manajemen Layanan Joki" 
            subtitle="Kelola tipe layanan joki, harga dasar, dan status aktifnya." 
            icon="fa-solid fa-list">
            <x-slot:actions>
                <button id="btnOpenCreateModal" class="inline-flex justify-center items-center flex-shrink-0 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    + Tambah Layanan
                </button>
            </x-slot:actions>
        </x-ui.page-header>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation mt-1"></i>
                    <div>
                        <h3 class="font-bold text-sm">Gagal menyimpan layanan:</h3>
                        <ul class="list-disc list-inside text-sm mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Table --}}
        <x-ui.table>
            <x-slot:head>
                <th class="px-6 py-4">Nama Layanan</th>
                <th class="px-6 py-4">Harga Dasar</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-right">Aksi</th>
            </x-slot:head>
                        @forelse ($services as $service)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">{{ $service->name }}</div>
                                    <div class="text-xs text-slate-500 truncate max-w-xs">{{ Str::limit($service->description, 50) }}</div>
                                </td>
                                <td class="px-6 py-4 font-mono font-medium">Rp{{ number_format($service->base_price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-full whitespace-nowrap inline-block {{ $service->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $service->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button class="w-8 h-8 rounded-lg flex items-center justify-center text-indigo-600 bg-indigo-50 hover:bg-indigo-600 hover:text-white transition-all duration-200 shadow-sm tooltip btn-edit-modal" 
                                            title="Edit Layanan"
                                            data-id="{{ $service->hashid }}"
                                            data-name="{{ $service->name }}"
                                            data-desc="{{ $service->description }}"
                                            data-price="{{ $service->base_price }}"
                                            data-active="{{ $service->is_active ? '1' : '0' }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <form action="{{ route('admin_joki.services.destroy', $service->hashid) }}" method="POST" class="inline form-delete-service">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-red-600 bg-red-50 hover:bg-red-600 hover:text-white transition-all duration-200 shadow-sm tooltip" title="Hapus Layanan">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-slate-400">Belum ada layanan yang ditambahkan.</td>
                            </tr>
                        @endforelse
        </x-ui.table>

    {{-- Modal Create --}}
    <div id="createModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 flex items-center justify-center p-4 transition-opacity opacity-0 duration-300">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden scale-95 transition-transform duration-300 transform">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800">Tambah Layanan Joki</h3>
                <button id="btnCloseCreateModal" type="button" class="text-slate-400 hover:text-rose-500 transition-colors p-2 rounded-lg hover:bg-rose-50">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form action="{{ route('admin_joki.services.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Layanan</label>
                        <input type="text" name="name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                        <textarea name="description" rows="3" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Harga Dasar (Rp)</label>
                        <input type="number" name="base_price" required min="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="is_active" class="text-sm font-medium text-slate-700">Aktifkan Layanan</label>
                    </div>
                </div>
                <div class="p-6 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                    <button type="button" id="btnCancelCreateModal" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-sm transition-all">
                        Simpan Layanan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div id="editModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 flex items-center justify-center p-4 transition-opacity opacity-0 duration-300">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden scale-95 transition-transform duration-300 transform">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800">Edit Layanan Joki</h3>
                <button type="button" id="btnCloseEditModal" class="text-slate-400 hover:text-rose-500 transition-colors p-2 rounded-lg hover:bg-rose-50">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Layanan</label>
                        <input type="text" name="name" id="edit_name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                        <textarea name="description" id="edit_description" rows="3" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Harga Dasar (Rp)</label>
                        <input type="number" name="base_price" id="edit_base_price" required min="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="edit_is_active" class="text-sm font-medium text-slate-700">Aktifkan Layanan</label>
                    </div>
                </div>
                <div class="p-6 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                    <button type="button" id="btnCancelEditModal" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-sm transition-all">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script nonce="{{ csp_nonce() ?? '' }}">
        document.addEventListener('DOMContentLoaded', function () {
            // Setup modals
            const createModal = document.getElementById('createModal');
            const editModal = document.getElementById('editModal');

            function openModal(modal) {
                const inner = modal.querySelector('div');
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    inner.classList.remove('scale-95');
                }, 10);
            }

            function closeModal(modal) {
                const inner = modal.querySelector('div');
                modal.classList.add('opacity-0');
                inner.classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            // Create triggers
            if (document.getElementById('btnOpenCreateModal')) {
                document.getElementById('btnOpenCreateModal').addEventListener('click', () => openModal(createModal));
            }
            if (document.getElementById('btnCloseCreateModal')) {
                document.getElementById('btnCloseCreateModal').addEventListener('click', () => closeModal(createModal));
            }
            if (document.getElementById('btnCancelCreateModal')) {
                document.getElementById('btnCancelCreateModal').addEventListener('click', () => closeModal(createModal));
            }

            // Edit triggers
            const editButtons = document.querySelectorAll('.btn-edit-modal');
            editButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const form = document.getElementById('editForm');
                    
                    form.action = `/admin/joki/services/${id}`;
                    document.getElementById('edit_name').value = this.getAttribute('data-name');
                    document.getElementById('edit_description').value = this.getAttribute('data-desc');
                    document.getElementById('edit_base_price').value = this.getAttribute('data-price');
                    document.getElementById('edit_is_active').checked = this.getAttribute('data-active') === '1';
                    
                    openModal(editModal);
                });
            });

            if (document.getElementById('btnCloseEditModal')) {
                document.getElementById('btnCloseEditModal').addEventListener('click', () => closeModal(editModal));
            }
            if (document.getElementById('btnCancelEditModal')) {
                document.getElementById('btnCancelEditModal').addEventListener('click', () => closeModal(editModal));
            }

            // Form deletes
            const deleteForms = document.querySelectorAll('.form-delete-service');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Hapus Layanan?',
                        text: 'Yakin ingin menghapus layanan ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#94a3b8',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
    </x-ui.page-layout>
@endsection
