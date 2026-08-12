@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header title="Alokasi Penyimpanan Akun"
            description="Daftar semua klien hosting dan batasan penyimpanannya." icon="hard-drive" iconColor="teal">
        </x-ui.page-header>

        <x-ui.table>
            <x-slot:head>
                <th class="px-6 py-4">Klien</th>
                <th class="px-6 py-4 text-center">Jumlah Proyek</th>
                <th class="px-6 py-4 text-right">Limit Storage Akun</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </x-slot:head>
            @forelse($users as $user)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-800 dark:text-slate-100">{{ $user->name }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300">
                            {{ $user->hosting_projects_count }} Proyek
                        </span>
                        @if($user->hostingProjects->isNotEmpty())
                            <div class="mt-3 space-y-1.5 border-t border-slate-100 dark:border-slate-700 pt-3">
                                @foreach($user->hostingProjects as $project)
                                    @php
                                        // Cek domain kustom dari relasi (jika ada)
                                        $customDomainObj = $project->domains->first();
                                        $displayDomain = $customDomainObj ? $customDomainObj->domain_name : ($project->custom_domain ?: $project->ryaze_domain);
                                        $url = 'https://' . $displayDomain;
                                    @endphp
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-1.5">
                                            <div class="w-1.5 h-1.5 rounded-full {{ $project->status === 'active' ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                                            <span class="text-xs font-medium text-slate-700 dark:text-slate-200">{{ $project->name }}</span>
                                        </div>
                                        <a href="{{ $url }}" target="_blank" class="text-[10px] text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 truncate max-w-[200px] ml-3 transition-colors">
                                            <i class="fa-solid fa-link mr-1"></i>{{ $displayDomain }}
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="font-semibold text-slate-800 dark:text-slate-100">
                            @if(in_array($user->role, ['superadmin', 'admin_hosting']))
                                <span class="text-xl leading-none" style="vertical-align: middle;">&infin;</span> <span class="text-sm">(Unlimited)</span>
                            @else
                                {{ $user->hosting_storage_limit_mb >= 1024 ? number_format($user->hosting_storage_limit_mb / 1024, 1) . ' GB' : number_format($user->hosting_storage_limit_mb) . ' MB' }}
                            @endif
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center flex items-center justify-center gap-2">
                        <button type="button"
                            class="btn-edit-storage w-8 h-8 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-600 hover:text-white transition-all duration-200 shadow-sm tooltip"
                            title="Ubah Limit Storage" data-modal-target="editStorageModal"
                            data-modal-toggle="editStorageModal" data-hashid="{{ $user->hashid }}"
                            data-name="{{ $user->name }}" data-limit="{{ $user->hosting_storage_limit_mb }}">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <i class="fa-solid fa-users text-3xl text-slate-300 dark:text-slate-400"></i>
                            <p>Belum ada klien hosting.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            @if ($users->hasPages())
                <x-slot:pagination>
                    {{ $users->links() }}
                </x-slot:pagination>
            @endif
        </x-ui.table>

        <!-- Modal Edit Storage -->
        <div id="editStorageModal"
            class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 p-4">
            <div class="modal-content-stop bg-white dark:bg-slate-800/60 rounded-2xl shadow-xl w-full max-w-md overflow-hidden relative">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Ubah Limit Storage</h3>
                    <button type="button" data-modal-hide="editStorageModal" class="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <form id="editStorageForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-4">
                        <div>
                            <p class="text-sm text-slate-600 dark:text-slate-300 mb-4">Ubah limit penyimpanan untuk klien: <strong
                                    id="storageProjectName" class="text-slate-800 dark:text-slate-100"></strong></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Limit Storage Baru (MB)</label>
                            <input type="number" name="storage_limit_mb" id="storageLimitInput" required min="100"
                                class="focus:ring-1 w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">1024 MB = 1 GB. Minimal 100 MB.</p>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                        <button type="button" data-modal-hide="editStorageModal"
                            class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100 bg-white dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 border border-slate-200 dark:border-slate-600 rounded-2xl shadow-sm transition-colors">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-2xl shadow-sm">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <script nonce="{{ app('csp_nonce') ?? '' }}">
            (function() {
                document.querySelectorAll('.btn-edit-storage').forEach(button => {
                    button.addEventListener('click', function() {
                        var form = document.getElementById('editStorageForm');
                        form.action = `/admin/hosting/storage/${this.dataset.hashid}`;
                        document.getElementById('storageProjectName').textContent = this.dataset.name;
                        document.getElementById('storageLimitInput').value = this.dataset.limit;
                    });
                });
                document.querySelectorAll('.modal-content-stop').forEach(el => {
                    el.addEventListener('click', e => e.stopPropagation());
                });
            })();
        </script>
    </x-ui.page-layout>
@endsection
