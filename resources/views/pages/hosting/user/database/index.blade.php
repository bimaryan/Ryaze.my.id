@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50 relative">

        {{-- Alerts --}}
        @if (session('success'))
            <div
                class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-circle-check text-xl"></i>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        @elseif (session('error'))
            <div
                class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                <span class="font-medium text-sm">{{ session('error') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div
                class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl flex flex-col gap-1 shadow-sm">
                @foreach ($errors->all() as $error)
                    <div class="flex items-center gap-2 text-sm"><i class="fa-solid fa-circle-xmark text-xs"></i>
                        {{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- Header --}}
        <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Database & phpMyAdmin</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola database MySQL untuk aplikasi Anda.</p>
            </div>
            <button onclick="document.getElementById('createDbModal').classList.remove('hidden')"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-sm hover:shadow-md transition-all flex items-center gap-2 text-sm">
                <i class="fa-solid fa-plus"></i> Buat Database
            </button>
        </div>

        {{-- Daftar Database --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            @forelse ($databases as $db)
                <div
                    class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-4 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center shadow-inner">
                                <i class="fa-solid fa-database text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-base" id="dbname-{{ $db->hashid }}">
                                    {{ $db->db_name }}</h3>
                                <span
                                    class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">Active</span>
                            </div>
                        </div>
                        <form action="{{ route('user_hosting.databases.destroy', $db->hashid) }}" method="POST"
                            onsubmit="return confirm('Hapus database permanen? Semua data tabel akan hilang!');">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="text-slate-400 hover:text-rose-500 p-2 hover:bg-rose-50 rounded-lg transition-colors"
                                title="Hapus Database">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </form>
                    </div>

                    <div class="p-5 space-y-4">
                        {{-- Host & Port --}}
                        <div
                            class="flex items-center justify-between p-3 rounded-xl border border-slate-100 bg-slate-50/50">
                            <div class="flex flex-col">
                                <span class="text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-0.5">Host &
                                    Port</span>
                                <code
                                    class="text-sm font-mono text-slate-700">{{ $db->host }}:{{ $db->port }}</code>
                            </div>
                        </div>

                        {{-- Username & Password Grid --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex flex-col border border-slate-100 rounded-xl p-3 bg-white relative group">
                                <span
                                    class="text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-1">Username</span>
                                <code class="text-sm font-mono text-slate-800 break-all"
                                    id="user-{{ $db->hashid }}">{{ $db->db_username }}</code>
                                <button onclick="copyText('user-{{ $db->hashid }}')"
                                    class="absolute top-2 right-2 text-slate-300 hover:text-indigo-600 bg-white rounded p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                            <div class="flex flex-col border border-slate-100 rounded-xl p-3 bg-white relative group">
                                <span
                                    class="text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-1">Password</span>
                                <div class="flex items-center justify-between">
                                    <input type="password" readonly value="{{ $db->db_password }}"
                                        id="pass-{{ $db->hashid }}"
                                        class="text-sm font-mono text-slate-800 bg-transparent outline-none w-full select-none cursor-text">
                                </div>
                                <div
                                    class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity bg-white">
                                    <button onclick="togglePassword('pass-{{ $db->hashid }}', this)"
                                        class="text-slate-300 hover:text-slate-600 p-1 rounded">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                    <button onclick="copyText('pass-{{ $db->hashid }}', true)"
                                        class="text-slate-300 hover:text-indigo-600 p-1 rounded">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <hr class="border-slate-100">

                        <div
                            class="bg-indigo-50/50 border border-indigo-100 p-4 rounded-xl flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="text-sm text-indigo-900">
                                <strong>Login phpMyAdmin</strong><br>
                                <span class="opacity-80 text-xs">Gunakan Username & Password di atas. Anda hanya akan
                                    melihat database milik Anda.</span>
                            </div>
                            <a href="{{ env('PMA_URL', '#') }}" target="_blank"
                                class="shrink-0 bg-white border border-indigo-200 text-indigo-700 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all text-xs font-bold py-2 px-4 rounded-lg shadow-sm flex items-center">
                                Buka phpMyAdmin <i class="fa-solid fa-arrow-up-right-from-square ml-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full bg-white rounded-2xl border border-slate-300 p-12 text-center flex flex-col items-center justify-center h-64">
                    <div
                        class="w-16 h-16 bg-slate-50 text-slate-300 rounded-2xl flex items-center justify-center mb-4 border border-slate-100 shadow-sm">
                        <i class="fa-solid fa-server text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700 mb-1">Belum ada Database</h3>
                    <p class="text-slate-500 text-sm">Anda belum memiliki database aktif. Silakan buat baru.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div id="createDbModal"
        class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform scale-100">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2"><i
                        class="fa-solid fa-database text-indigo-500"></i> Buat Database Baru</h3>
                <button onclick="document.getElementById('createDbModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-rose-500 transition-colors bg-white rounded-lg p-1.5 shadow-sm border border-slate-200"><i
                        class="fa-solid fa-xmark"></i></button>
            </div>

            <form action="{{ route('user_hosting.databases.store') }}" method="POST" class="p-6 space-y-5">
                @csrf

                {{-- Nama DB --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Database <span
                            class="text-rose-500">*</span></label>
                    <div class="flex shadow-sm rounded-xl">
                        <span
                            class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-slate-300 bg-slate-100 text-slate-500 sm:text-sm font-mono">
                            ryz_{{ Auth::id() }}_
                        </span>
                        <input type="text" name="db_name" required pattern="[A-Za-z0-9_]+" placeholder="myapp"
                            maxlength="15"
                            class="flex-1 block w-full min-w-0 rounded-none rounded-r-xl sm:text-sm border-slate-300 border p-2.5 outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-mono transition-all">
                    </div>
                </div>

                {{-- Username --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Username MySQL <span
                            class="text-rose-500">*</span></label>
                    <div class="flex shadow-sm rounded-xl">
                        <span
                            class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-slate-300 bg-slate-100 text-slate-500 sm:text-sm font-mono">
                            ryz_{{ Auth::id() }}_
                        </span>
                        <input type="text" name="db_username" required pattern="[A-Za-z0-9_]+" placeholder="user"
                            maxlength="15"
                            class="flex-1 block w-full min-w-0 rounded-none rounded-r-xl sm:text-sm border-slate-300 border p-2.5 outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-mono transition-all">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5 flex justify-between">
                        <span>Password <span class="text-rose-500">*</span></span>
                        <button type="button" onclick="generatePassword()"
                            class="text-xs text-indigo-600 hover:text-indigo-800"><i
                                class="fa-solid fa-wand-magic-sparkles"></i> Generate</button>
                    </label>
                    <div class="relative">
                        <input type="text" name="db_password" id="modalPassword" required minlength="8"
                            maxlength="32" placeholder="Masukkan password kuat"
                            class="block w-full rounded-xl sm:text-sm border-slate-300 border p-2.5 pr-10 outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-mono transition-all shadow-sm">
                        <button type="button" onclick="copyModalPassword()"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-indigo-600"
                            title="Copy Password">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                    <p class="mt-1.5 text-[11px] text-slate-500"><i class="fa-solid fa-shield-halved"></i> Simpan password
                        ini baik-baik. Minimal 8 karakter.</p>
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('createDbModal').classList.add('hidden')"
                        class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-sm transition-all flex items-center gap-2">
                        <i class="fa-solid fa-server"></i> Eksekusi Pembuatan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script Interaksi UI --}}
    <script>
        // Fungsi Copy
        function copyText(elementId, isInput = false) {
            const element = document.getElementById(elementId);
            const text = isInput ? element.value : element.innerText;

            navigator.clipboard.writeText(text).then(() => {
                // Tampilkan toast sederhana (bisa diganti SweetAlert jika mas pakai)
                const toast = document.createElement('div');
                toast.className =
                    'fixed bottom-5 right-5 bg-slate-800 text-white px-4 py-2 rounded-lg text-sm shadow-xl z-50 animate-bounce';
                toast.innerHTML = '<i class="fa-solid fa-check text-emerald-400 mr-2"></i> Berhasil disalin!';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 2000);
            });
        }

        // Toggle Password Show/Hide
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Random Password Generator
        function generatePassword() {
            const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
            let pass = "";
            for (let i = 0; i < 16; i++) {
                pass += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('modalPassword').value = pass;
        }

        // Copy Password di Modal
        function copyModalPassword() {
            const pass = document.getElementById('modalPassword').value;
            if (pass) {
                navigator.clipboard.writeText(pass);
                alert("Password berhasil disalin sementara!");
            }
        }
    </script>
@endsection
