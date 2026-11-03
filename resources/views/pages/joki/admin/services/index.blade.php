@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Manajemen Layanan Joki"
            eyebrow="Admin Joki · Katalog Layanan"
            subtitle="Kelola tipe layanan joki, harga dasar, dan status aktifnya."
            icon="fa-solid fa-list"
        >
            <x-slot:actions>
                <button id="btnOpenCreateModal" class="ui-btn-primary">
                    + Tambah Layanan
                </button>
            </x-slot:actions>
        </x-ui.page-header>

        @if ($errors->any())
            <div class="mt-6 p-4 bg-rose-50 dark:bg-rose-500/10 border border-rose-300 dark:border-rose-500/40 text-rose-700 dark:text-rose-300">
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
        <div class="mt-6">
            <x-ui.table>
                <x-slot:head>
                    <th class="px-6 py-4">Nama Layanan</th>
                    <th class="px-6 py-4">Harga Dasar</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </x-slot:head>
                @forelse ($services as $service)
                    <tr class="hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828]">
                        <td class="px-6 py-4">
                            <div class="font-bold text-[#0F1A2A] dark:text-white">{{ $service->name }}</div>
                            <div class="text-xs text-[#94A3B4] dark:text-white/35 truncate max-w-xs">{{ Str::limit($service->description, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 font-mono font-medium text-[#0F1A2A] dark:text-white">Rp{{ number_format($service->base_price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            <span class="ui-badge whitespace-nowrap {{ $service->is_active ? 'ui-badge-success' : 'ui-badge-danger' }}">
                                {{ $service->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button class="ui-icon-btn ui-icon-btn-primary tooltip btn-edit-modal"
                                    title="Edit Layanan"
                                    data-id="{{ $service->hashid }}"
                                    data-name="{{ $service->name }}"
                                    data-desc="{{ $service->description }}"
                                    data-price="{{ $service->base_price }}"
                                    data-active="{{ $service->is_active ? '1' : '0' }}">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>
                                <form action="{{ route('admin_joki.services.destroy', $service->hashid) }}" method="POST" class="inline form-delete-service">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ui-icon-btn ui-icon-btn-danger tooltip" title="Hapus Layanan">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-[#94A3B4] dark:text-white/35 font-mono text-[11px] uppercase tracking-[0.2em]">Belum ada layanan yang ditambahkan.</td>
                    </tr>
                @endforelse
            </x-ui.table>
        </div>

    {{-- Modal Create --}}
    <div id="createModal" class="fixed inset-0 z-50 hidden bg-[#0A1322]/60 flex items-center justify-center p-4 transition-opacity opacity-0 duration-300">
        <div class="ui-panel w-full max-w-lg overflow-hidden scale-95 transition-transform duration-300 transform">
            <div class="ui-panel-head p-5">
                <h3 class="ui-modal-title">Tambah Layanan Joki</h3>
                <button id="btnCloseCreateModal" type="button" class="ui-icon-btn ui-icon-btn-danger">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form action="{{ route('admin_joki.services.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="ui-eyebrow-muted block mb-1.5">Nama Layanan</label>
                        <input type="text" name="name" required class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition">
                    </div>
                    <div>
                        <label class="ui-eyebrow-muted block mb-1.5">Deskripsi</label>
                        <textarea name="description" rows="3" required class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition"></textarea>
                    </div>
                    <div>
                        <label class="ui-eyebrow-muted block mb-1.5">Harga Dasar (Rp)</label>
                        <input type="number" name="base_price" required min="0" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm font-mono focus:border-[#1D5B96] outline-none transition">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="accent-[#1D5B96] w-4 h-4">
                        <label for="is_active" class="text-sm font-medium text-[#525252] dark:text-white/70">Aktifkan Layanan</label>
                    </div>
                </div>
                <div class="p-5 border-t border-[#E3EAF1] dark:border-[#1B2C44] bg-[#FAFBFD] dark:bg-[#0D1828] flex justify-end gap-3">
                    <button type="button" id="btnCancelCreateModal" class="ui-btn-secondary">
                        Batal
                    </button>
                    <button type="submit" class="ui-btn-primary">
                        Simpan Layanan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div id="editModal" class="fixed inset-0 z-50 hidden bg-[#0A1322]/60 flex items-center justify-center p-4 transition-opacity opacity-0 duration-300">
        <div class="ui-panel w-full max-w-lg overflow-hidden scale-95 transition-transform duration-300 transform">
            <div class="ui-panel-head p-5">
                <h3 class="ui-modal-title">Edit Layanan Joki</h3>
                <button type="button" id="btnCloseEditModal" class="ui-icon-btn ui-icon-btn-danger">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4">
                    <div>
                        <label class="ui-eyebrow-muted block mb-1.5">Nama Layanan</label>
                        <input type="text" name="name" id="edit_name" required class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition">
                    </div>
                    <div>
                        <label class="ui-eyebrow-muted block mb-1.5">Deskripsi</label>
                        <textarea name="description" id="edit_description" rows="3" required class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition"></textarea>
                    </div>
                    <div>
                        <label class="ui-eyebrow-muted block mb-1.5">Harga Dasar (Rp)</label>
                        <input type="number" name="base_price" id="edit_base_price" required min="0" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm font-mono focus:border-[#1D5B96] outline-none transition">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="accent-[#1D5B96] w-4 h-4">
                        <label for="edit_is_active" class="text-sm font-medium text-[#525252] dark:text-white/70">Aktifkan Layanan</label>
                    </div>
                </div>
                <div class="p-5 border-t border-[#E3EAF1] dark:border-[#1B2C44] bg-[#FAFBFD] dark:bg-[#0D1828] flex justify-end gap-3">
                    <button type="button" id="btnCancelEditModal" class="ui-btn-secondary">
                        Batal
                    </button>
                    <button type="submit" class="ui-btn-primary">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script nonce="{{ csp_nonce() ?? '' }}">
        (function() {
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
                        confirmButtonColor: '#e11d48',
                        cancelButtonColor: '#94a3b8',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });        })();
    </script>
    </x-ui.page-layout>
@endsection
