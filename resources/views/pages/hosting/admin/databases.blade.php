@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header title="Semua Database" description="Kelola semua database klien di server." icon="database"
            iconColor="orange">
            <x-slot:actions>
                <button data-modal-target="createDbModal" data-modal-toggle="createDbModal"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Buat Database
                </button>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.table>
            <x-slot:head>
                <th class="px-6 py-4 w-1/3">Informasi Klien & Kredensial</th>
                <th class="px-6 py-4">Daftar Database</th>
            </x-slot:head>
            @forelse($usersWithDatabases as $user)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors border-b border-slate-100 dark:border-slate-700">
                    <td class="px-6 py-4 align-top border-r border-slate-100 dark:border-slate-700">
                        <div class="font-bold text-slate-800 dark:text-slate-100">{{ $user->name }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mb-3">{{ $user->email }}</div>
                        
                        <div class="bg-indigo-50/50 dark:bg-indigo-500/10 rounded-xl p-3 border border-indigo-100 dark:border-indigo-500/30">
                            <div class="text-[11px] font-bold text-indigo-800 dark:text-indigo-300 uppercase tracking-wider mb-2">Kredensial Database</div>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500 dark:text-slate-400">User:</span>
                                    <span class="font-mono bg-white dark:bg-slate-800/60 text-slate-700 dark:text-slate-200 px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700">{{ $user->db_username }}</span>
                                </div>
                                <div class="flex justify-between items-center group">
                                    <span class="text-slate-500 dark:text-slate-400">Pass:</span>
                                    <span class="font-mono bg-white dark:bg-slate-800/60 text-slate-700 dark:text-slate-200 px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700 filter blur-[4px] group-hover:blur-none transition-all duration-300 cursor-pointer" title="Arahkan kursor untuk melihat">
                                        {{ $user->db_password_decrypted ?? 'Error' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500 dark:text-slate-400">Host:</span>
                                    <span class="font-mono bg-white dark:bg-slate-800/60 text-slate-700 dark:text-slate-200 px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700">{{ $user->db_host ?? 'localhost' }}:{{ $user->db_port ?? 3306 }}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 align-top">
                        <div class="space-y-2">
                            {{-- MySQL Databases --}}
                            @foreach($user->hostingDatabases as $db)
                                <div class="flex items-center justify-between p-3 bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl hover:border-indigo-300 hover:shadow-sm transition-all">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <div class="font-mono font-semibold text-slate-700 dark:text-slate-200">{{ $db->db_name }}</div>
                                            <span class="text-[10px] bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-300 border border-blue-200 dark:border-blue-500/40 px-1.5 py-0.5 rounded font-bold">MySQL</span>
                                        </div>
                                        <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-1"><i class="fa-regular fa-calendar mr-1"></i> {{ $db->created_at->format('d M Y') }}</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ rtrim(env('PMA_URL', '#'), '/') }}/index.php" target="_blank" class="inline-block">
                                            <input type="hidden" name="pma_username" value="{{ $user->db_username }}">
                                            <input type="hidden" name="pma_password" value="{{ $user->db_password_decrypted ?? '' }}">
                                            <input type="hidden" name="server" value="1">
                                            <input type="hidden" name="pma_servername" value="{{ $db->host }}">
                                            <button type="submit"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-600 hover:text-white transition-all duration-200 shadow-sm tooltip"
                                                title="Buka phpMyAdmin">
                                                <i class="fa-solid fa-server"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin_hosting.databases.destroy', $db->hashid) }}" method="POST" class="inline-block">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDatabaseDelete(this)" class="btn-delete w-8 h-8 rounded-lg flex items-center justify-center text-red-600 dark:text-red-300 bg-red-50 dark:bg-red-500/10 hover:bg-red-600 hover:text-white transition-all duration-200 shadow-sm tooltip" title="Hapus Database" data-hashid="{{ $db->hashid }}">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach

                            {{-- PostgreSQL Databases --}}
                            @foreach($user->hostingPgsqlDatabases as $db)
                                <div class="flex items-center justify-between p-3 bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl hover:border-indigo-300 hover:shadow-sm transition-all">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <div class="font-mono font-semibold text-slate-700 dark:text-slate-200">{{ $db->db_name }}</div>
                                            <span class="text-[10px] bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/40 px-1.5 py-0.5 rounded font-bold">PostgreSQL</span>
                                        </div>
                                        <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-1"><i class="fa-regular fa-calendar mr-1"></i> {{ $db->created_at->format('d M Y') }}</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <form action="{{ route('admin_hosting.databases.destroy', $db->hashid) }}" method="POST" class="inline-block">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDatabaseDelete(this)" class="btn-delete w-8 h-8 rounded-lg flex items-center justify-center text-red-600 dark:text-red-300 bg-red-50 dark:bg-red-500/10 hover:bg-red-600 hover:text-white transition-all duration-200 shadow-sm tooltip" title="Hapus Database" data-hashid="{{ $db->hashid }}">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Redis Databases --}}
                            @foreach($user->hostingNosqlDatabases as $db)
                                <div class="flex items-center justify-between p-3 bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl hover:border-indigo-300 hover:shadow-sm transition-all">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <div class="font-mono font-semibold text-slate-700 dark:text-slate-200">{{ $db->db_name }}</div>
                                            <span class="text-[10px] bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-300 border border-rose-200 dark:border-rose-500/40 px-1.5 py-0.5 rounded font-bold">Redis</span>
                                        </div>
                                        <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-1"><i class="fa-regular fa-calendar mr-1"></i> {{ $db->created_at->format('d M Y') }}</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <form action="{{ route('admin_hosting.databases.destroy', $db->hashid) }}" method="POST" class="inline-block">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDatabaseDelete(this)" class="btn-delete w-8 h-8 rounded-lg flex items-center justify-center text-red-600 dark:text-red-300 bg-red-50 dark:bg-red-500/10 hover:bg-red-600 hover:text-white transition-all duration-200 shadow-sm tooltip" title="Hapus Database" data-hashid="{{ $db->hashid }}">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <i class="fa-solid fa-database text-3xl text-slate-300 dark:text-slate-400"></i>
                            <p>Belum ada database yang dibuat.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            @if ($usersWithDatabases->hasPages())
                <x-slot:pagination>
                    {{ $usersWithDatabases->links() }}
                </x-slot:pagination>
            @endif
        </x-ui.table>

        <!-- Modal Create Database -->
        <div id="createDbModal"
            class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 p-4">
            <div class="modal-content-stop bg-white dark:bg-slate-800/60 rounded-2xl shadow-xl w-full max-w-md overflow-hidden relative">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Buat Database Baru</h3>
                    <button type="button" data-modal-hide="createDbModal" class="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <form action="{{ route('admin_hosting.databases.store') }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Pilih Klien</label>
                            <select name="user_id" required
                                class="focus:ring-1 w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                                <option value="">-- Pilih Klien --</option>
                                @foreach ($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Prefix ryz_{id}_ akan ditambahkan otomatis.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Nama Database <span class="text-rose-500 dark:text-rose-400">*</span></label>
                            <div class="flex rounded-xl overflow-hidden border border-slate-300 dark:border-slate-600 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                                <span class="prefix-addon inline-flex items-center px-3 bg-slate-100 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 text-sm font-mono border-r border-slate-300 dark:border-slate-600 whitespace-nowrap">
                                    ryz_.._
                                </span>
                                <input type="text" name="db_name" required pattern="[A-Za-z0-9\-_]+" maxlength="15"
                                    placeholder="contoh: wp_blog"
                                    class="flex-1 font-mono w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Username Database <span class="text-slate-400 dark:text-slate-500 font-normal">(opsional)</span></label>
                            <div class="flex rounded-xl overflow-hidden border border-slate-300 dark:border-slate-600 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                                <span class="prefix-addon inline-flex items-center px-3 bg-slate-100 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 text-sm font-mono border-r border-slate-300 dark:border-slate-600 whitespace-nowrap">
                                    ryz_.._
                                </span>
                                <input type="text" name="db_username" pattern="[A-Za-z0-9\-_]+" maxlength="15"
                                    placeholder="Kosongkan jika klien sudah punya database"
                                    class="flex-1 font-mono w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Kosongkan username & password jika klien sudah memiliki database sebelumnya.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Password Database <span class="text-slate-400 dark:text-slate-500 font-normal">(opsional)</span></label>
                            <div class="flex rounded-xl overflow-hidden border border-slate-300 dark:border-slate-600 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                                <span class="prefix-addon inline-flex items-center px-3 bg-slate-100 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 text-sm font-mono border-r border-slate-300 dark:border-slate-600 whitespace-nowrap">
                                    ryz_.._
                                </span>
                                <input type="text" name="db_password" maxlength="32"
                                    placeholder="Kosongkan jika klien sudah punya database"
                                    class="flex-1 font-mono w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                        <button type="button" data-modal-hide="createDbModal"
                            class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-200 bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-2xl shadow-sm">Buat
                            Database</button>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <script nonce="{{ app('csp_nonce') ?? '' }}">
            (function() {
                const clientSelect = document.querySelector('select[name="user_id"]');
                if (clientSelect) {
                    clientSelect.addEventListener('change', function() {
                        const id = this.value;
                        const prefix = id ? `ryz_${id}_` : `ryz_.._`;
                        document.querySelectorAll('.prefix-addon').forEach(el => el.textContent = prefix);
                    });
                }
                
                window.confirmDatabaseDelete = function(btn) {
                    Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        text: "Semua data di dalam database ini akan hilang permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#94a3b8',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        customClass: {
                            popup: document.documentElement.classList.contains('dark') ? 'bg-slate-800 border border-slate-700' : '',
                            title: document.documentElement.classList.contains('dark') ? 'text-slate-100' : '',
                            htmlContainer: document.documentElement.classList.contains('dark') ? 'text-slate-300' : ''
                        },
                        background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            btn.closest('form').submit();
                        }
                    });
                };
            })();
        </script>
    </x-ui.page-layout>
@endsection
