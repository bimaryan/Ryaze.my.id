@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Alokasi Penyimpanan Proyek" 
            description="Daftar semua proyek hosting dan batasan penyimpanannya." 
            icon="hard-drive" 
            iconColor="teal">
        </x-ui.page-header>

        <x-ui.table>
            <x-slot:head>
                <th class="px-6 py-4">Nama Proyek</th>
                <th class="px-6 py-4">Pemilik (Klien)</th>
                <th class="px-6 py-4 text-center">Status</th>
                <th class="px-6 py-4 text-right">Limit Storage</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </x-slot:head>
                    @forelse($projects as $project)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $project->project_name }}
                                <div class="text-xs font-normal text-slate-500 mt-1">{{ $project->ryaze_domain }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($project->client)
                                    <div class="font-medium text-slate-800">{{ $project->client->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $project->client->email }}</div>
                                @else
                                    <span class="text-slate-400 italic">Unknown</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusColors = [
                                        'active' => 'bg-emerald-100 text-emerald-700',
                                        'suspended' => 'bg-red-100 text-red-700',
                                        'building' => 'bg-blue-100 text-blue-700',
                                        'unpaid' => 'bg-amber-100 text-amber-700',
                                    ];
                                    $colorClass = $statusColors[$project->status] ?? 'bg-slate-100 text-slate-700';
                                @endphp
                                <span class="px-2.5 py-1 rounded-md text-xs font-medium {{ $colorClass }}">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-semibold text-slate-800">
                                    {{ $project->storage_limit_mb >= 1024 ? number_format($project->storage_limit_mb / 1024, 1) . ' GB' : number_format($project->storage_limit_mb) . ' MB' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center flex items-center justify-center gap-2">
                                <button type="button" class="btn-edit-storage w-8 h-8 rounded-lg flex items-center justify-center text-indigo-500 hover:bg-indigo-50 hover:text-indigo-700 transition-colors tooltip" title="Ubah Limit Storage" data-modal-target="editStorageModal" data-modal-toggle="editStorageModal" data-hashid="{{ $project->hashid }}" data-name="{{ $project->project_name }}" data-limit="{{ $project->storage_limit_mb }}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <a href="{{ route('admin_hosting.projects') }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors tooltip" title="Detail Proyek">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-solid fa-folder-open text-3xl text-slate-300"></i>
                                    <p>Belum ada proyek hosting.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
            @if($projects->hasPages())
                <x-slot:pagination>
                    {{ $projects->links() }}
                </x-slot:pagination>
            @endif
        </x-ui.table>

    <!-- Modal Edit Storage -->
    <div id="editStorageModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden relative" onclick="event.stopPropagation()">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">Ubah Limit Storage</h3>
                <button type="button" data-modal-hide="editStorageModal" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form id="editStorageForm" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-sm text-slate-600 mb-4">Ubah limit penyimpanan untuk proyek: <strong id="storageProjectName" class="text-slate-800"></strong></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Limit Storage Baru (MB)</label>
                        <input type="number" name="storage_limit_mb" id="storageLimitInput" required min="100" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <p class="text-[11px] text-slate-500 mt-1">1024 MB = 1 GB. Minimal 100 MB.</p>
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                    <button type="button" data-modal-hide="editStorageModal" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 bg-white border border-slate-200 rounded-2xl shadow-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-2xl shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script nonce="{{ app('csp_nonce') ?? '' }}">
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.btn-edit-storage').forEach(button => {
            button.addEventListener('click', function() {
                const form = document.getElementById('editStorageForm');
                form.action = `/admin/hosting/storage/${this.dataset.hashid}`;
                document.getElementById('storageProjectName').textContent = this.dataset.name;
                document.getElementById('storageLimitInput').value = this.dataset.limit;
            });
        });
    });
    </script>
    </x-ui.page-layout>
@endsection
