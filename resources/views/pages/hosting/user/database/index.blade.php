@extends('index')

@section('content')
    <x-ui.page-layout>

        {{-- SweetAlert2 --}}
        {{-- Flash via SweetAlert --}}
        @if ($errors->any())
        <script nonce="{{ app('csp_nonce') }}">
            document.addEventListener('DOMContentLoaded', () => Swal.fire({
                icon: 'error', title: 'Validasi Gagal',
                html: '{!! implode('<br>', array_map('addslashes', $errors->all())) !!}',
                confirmButtonColor: '#4F46E5', customClass: { popup: 'rounded-xl text-sm' }
            }));
        </script>
        @endif

        {{-- Header --}}
        <x-ui.page-header 
            title="Database & phpMyAdmin" 
            subtitle="Kelola database MySQL untuk aplikasi Anda." 
            icon="fa-database" 
            iconColor="purple">
            <x-slot:actions>
                <button id="btn-open-create-modal" class="inline-flex justify-center items-center flex-shrink-0 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    + Buat Database
                </button>
            </x-slot:actions>
        </x-ui.page-header>

    {{-- Database Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse ($databases as $db)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
            {{-- Card Header --}}
            <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center">
                        <i class="fa-solid fa-database text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-base">{{ $db->db_name }}</h3>
                        <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">Active</span>
                    </div>
                </div>
                <button data-action="{{ route('user_hosting.databases.destroy', $db->hashid) }}"
                    class="btn-delete-db text-slate-400 hover:text-rose-500 p-2 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Database">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
            </div>

            <div class="p-5 space-y-4">
                {{-- Host & Port --}}
                <div class="flex items-center justify-between p-3 rounded-xl border border-slate-100 bg-slate-50/50">
                    <div>
                        <span class="text-[11px] text-slate-400 font-bold uppercase tracking-wider block mb-0.5">Host & Port</span>
                        <code class="text-sm font-mono text-slate-700">{{ $db->host }}:{{ $db->port ?? 3306 }}</code>
                    </div>
                    <button class="text-slate-300 hover:text-indigo-500 p-1.5 rounded transition-colors btn-copy" data-copy="{{ $db->host }}:{{ $db->port ?? 3306 }}" title="Copy">
                        <i class="fa-regular fa-copy text-sm"></i>
                    </button>
                </div>

                {{-- Username & Password --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex flex-col border border-slate-100 rounded-xl p-3 bg-white relative group">
                        <span class="text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-1">Username</span>
                        <code class="text-sm font-mono text-slate-800 break-all" id="user-{{ $db->hashid }}">{{ $db->db_username }}</code>
                        <button class="absolute top-2 right-2 text-slate-300 hover:text-indigo-600 bg-white rounded p-1 opacity-0 group-hover:opacity-100 transition-opacity btn-copy" data-copy="{{ $db->db_username }}">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                    <div class="flex flex-col border border-slate-100 rounded-xl p-3 bg-white relative group">
                        <span class="text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-1">Password</span>
                        <input type="password" readonly value="{{ $db->db_password }}"
                            id="pass-{{ $db->hashid }}"
                            class="text-sm font-mono text-slate-800 bg-transparent outline-none w-full">
                        <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity bg-white">
                            <button class="text-slate-300 hover:text-slate-600 p-1 rounded btn-toggle-pass" data-target="pass-{{ $db->hashid }}">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                            <button class="text-slate-300 hover:text-indigo-600 p-1 rounded btn-copy" data-copy="{{ $db->db_password }}">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100">

                {{-- phpMyAdmin auto-login via POST --}}
                <div class="bg-indigo-50/50 border border-indigo-100 p-4 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="text-sm text-indigo-900">
                        <strong>Login phpMyAdmin Otomatis</strong><br>
                        <span class="opacity-80 text-xs">Klik tombol untuk login langsung ke database <code class="font-mono bg-indigo-100 px-1 rounded">{{ $db->db_name }}</code>.</span>
                    </div>
                    {{-- Form POST auto-login ke phpMyAdmin --}}
                    <form method="POST" action="{{ env('PMA_URL', '#') }}" target="_blank" class="shrink-0">
                        <input type="hidden" name="pma_username" value="{{ $db->db_username }}">
                        <input type="hidden" name="pma_password" value="{{ $db->db_password }}">
                        <input type="hidden" name="server" value="1">
                        <button type="submit"
                            class="bg-white border border-indigo-200 text-indigo-700 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all text-xs font-bold py-2 px-4 rounded-2xl shadow-sm flex items-center gap-1.5 whitespace-nowrap">
                            <i class="fa-solid fa-database"></i>
                            Buka phpMyAdmin
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-16 text-center flex flex-col items-center">
            <div class="w-16 h-16 bg-slate-100 text-slate-300 rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-server text-3xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-700 mb-1">Belum ada Database</h3>
            <p class="text-slate-500 text-sm">Klik "Buat Database" untuk memulai.</p>
        </div>
        @endforelse
    </div>

    </x-ui.page-layout>

{{-- Modal Buat Database --}}
<div id="createDbModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(15,23,42,0.5)">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        {{-- Modal Header --}}
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                <i class="fa-solid fa-database text-indigo-500"></i> Buat Database Baru
            </h3>
            <button class="btn-close-modal"
                class="text-slate-400 hover:text-rose-500 transition-colors w-8 h-8 flex items-center justify-center rounded-lg hover:bg-rose-50">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('user_hosting.databases.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            {{-- Nama DB --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Nama Database <span class="text-rose-500">*</span>
                </label>
                <div class="flex rounded-xl overflow-hidden border border-slate-300 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                    <span class="inline-flex items-center px-3 bg-slate-100 text-slate-500 text-sm font-mono border-r border-slate-300 whitespace-nowrap">
                        ryz_{{ Auth::id() }}_
                    </span>
                    <input type="text" name="db_name" required pattern="[A-Za-z0-9_]+" placeholder="myapp" maxlength="15"
                        class="flex-1 px-3 py-2.5 text-sm font-mono outline-none bg-white">
                </div>
            </div>

            {{-- Username --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Username MySQL <span class="text-rose-500">*</span>
                </label>
                <div class="flex rounded-xl overflow-hidden border border-slate-300 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                    <span class="inline-flex items-center px-3 bg-slate-100 text-slate-500 text-sm font-mono border-r border-slate-300 whitespace-nowrap">
                        ryz_{{ Auth::id() }}_
                    </span>
                    <input type="text" name="db_username" required pattern="[A-Za-z0-9_]+" placeholder="user" maxlength="15"
                        class="flex-1 px-3 py-2.5 text-sm font-mono outline-none bg-white">
                </div>
            </div>

            {{-- Password --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-sm font-semibold text-slate-700">Password <span class="text-rose-500">*</span></label>
                    <button type="button" id="btn-generate-password" class="text-xs text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Generate
                    </button>
                </div>
                <div class="relative flex rounded-xl overflow-hidden border border-slate-300 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                    <span class="inline-flex items-center px-3 bg-slate-100 text-slate-500 text-sm font-mono border-r border-slate-300 whitespace-nowrap">
                        ryz_{{ Auth::id() }}_
                    </span>
                    <input type="text" name="db_password" id="modalPassword" required maxlength="32"
                        placeholder="Masukkan password kuat"
                        class="flex-1 px-3 py-2.5 pr-10 text-sm font-mono outline-none bg-white">
                    <button type="button" id="btn-copy-modal-password" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-indigo-600" title="Copy">
                        <i class="fa-regular fa-copy"></i>
                    </button>
                </div>
                <p class="mt-1.5 text-[11px] text-slate-400">
                    <i class="fa-solid fa-shield-halved"></i> Simpan password ini. Otomatis ditambah prefix.
                </p>
            </div>

            {{-- Footer --}}
            <div class="pt-2 flex justify-end gap-3 border-t border-slate-100">
                <button type="button" class="btn-close-modal"
                    class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-sm transition-all flex items-center gap-2">
                    <i class="fa-solid fa-server"></i> Buat Database
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Delete form (hidden) --}}
<form id="deleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script nonce="{{ app('csp_nonce') }}">
    // ── Modal ──────────────────────────────────────────────────────────────────
    function openCreateModal() {
        document.getElementById('createDbModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeCreateModal() {
        document.getElementById('createDbModal').classList.add('hidden');
        document.body.style.overflow = '';
    }
    // Klik luar modal → tutup
    document.getElementById('createDbModal').addEventListener('click', function(e) {
        if (e.target === this) closeCreateModal();
    });

    // ── Delete dengan SweetAlert ───────────────────────────────────────────────
    function confirmDelete(actionUrl) {
        Swal.fire({
            icon: 'warning',
            title: 'Hapus Database?',
            text: 'Semua tabel dan data akan terhapus permanen dan tidak bisa dikembalikan.',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: '<i class="fa-solid fa-trash-can mr-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-xl text-sm' }
        }).then(result => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = actionUrl;
                form.submit();
            }
        });
    }

    // ── Copy to clipboard dengan Toast ────────────────────────────────────────
        function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            hotToast('Berhasil disalin!', 'success');
        }).catch(() => {
            hotToast('Gagal menyalin', 'error');
        });
    }

    // ── Toggle password visibility ─────────────────────────────────────────────
    function togglePass(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    // ── Generate random password ───────────────────────────────────────────────
    function generatePassword() {
        const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        let pass = '';
        for (let i = 0; i < 16; i++) pass += chars[Math.floor(Math.random() * chars.length)];
        document.getElementById('modalPassword').value = pass;
        hotToast('Password di-generate!', 'success');
    }

    // ── Copy password di modal ─────────────────────────────────────────────────
    function copyModalPassword() {
        const pass = document.getElementById('modalPassword').value;
        if (!pass) { hotToast('Password masih kosong', 'warning'); return; }
        navigator.clipboard.writeText(pass).then(() => {
            hotToast('Password disalin!', 'success');
        });
    }
    // ── CSP Compliant Event Listeners ──────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        // Create Modal Open
        const btnOpenModal = document.getElementById('btn-open-create-modal');
        if (btnOpenModal) btnOpenModal.addEventListener('click', openCreateModal);

        // Create Modal Close
        document.querySelectorAll('.btn-close-modal').forEach(btn => {
            btn.addEventListener('click', closeCreateModal);
        });

        // Delete DB
        document.querySelectorAll('.btn-delete-db').forEach(btn => {
            btn.addEventListener('click', (e) => {
                confirmDelete(e.currentTarget.getAttribute('data-action'));
            });
        });

        // Copy
        document.querySelectorAll('.btn-copy').forEach(btn => {
            btn.addEventListener('click', (e) => {
                copyToClipboard(e.currentTarget.getAttribute('data-copy'));
            });
        });

        // Toggle Password
        document.querySelectorAll('.btn-toggle-pass').forEach(btn => {
            btn.addEventListener('click', (e) => {
                togglePass(e.currentTarget.getAttribute('data-target'), e.currentTarget);
            });
        });

        // Generate Password
        const btnGenPass = document.getElementById('btn-generate-password');
        if (btnGenPass) btnGenPass.addEventListener('click', generatePassword);

        // Copy Modal Password
        const btnCopyModalPass = document.getElementById('btn-copy-modal-password');
        if (btnCopyModalPass) btnCopyModalPass.addEventListener('click', copyModalPassword);
    });
</script>
@endsection
