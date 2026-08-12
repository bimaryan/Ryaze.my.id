@extends('index')
@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Membutuhkan Tindakan" 
            subtitle="Project yang butuh aktivasi, suspend, atau perbaikan error." 
            icon="fa-solid fa-clock">
            <x-slot:actions>
                <a href="{{ route('admin_hosting.dashboard') }}" class="inline-flex justify-center items-center bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-200 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>
        <x-ui.table class="mt-6">
            <x-slot:head>
                <th class="px-6 py-4">Project & Klien</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </x-slot:head>
            @forelse ($projects as $project)
                            <tr class="hover:bg-slate-50 dark:bg-slate-800/50 dark:hover:bg-slate-700/40 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $project->project_name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $project->client?->name ?? '—' }} ·
                                        {{ $project->ryaze_domain }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $badge = match ($project->status) {
                                            'unpaid' => ['bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300', 'Belum Bayar'],
                                            'error' => ['bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300', 'Error'],
                                            'suspended' => ['bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300', 'Disuspend'],
                                            default => ['bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300', ucfirst($project->status)],
                                        };
                                    @endphp
                                    <span
                                        class="text-xs font-medium px-2.5 py-1 rounded-full {{ $badge[0] }}">{{ $badge[1] }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if ($project->status === 'unpaid' || $project->status === 'suspended')
                                            <form method="POST"
                                                action="{{ route('admin_hosting.activate', $project->hashid) }}"
                                                class="form-activate-project" data-name="{{ $project->project_name }}">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="text-xs bg-emerald-500 text-white px-3 py-1.5 rounded-lg hover:bg-emerald-600 transition-colors">Aktifkan</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-slate-400 dark:text-slate-500">Tidak ada project yang
                                    membutuhkan tindakan.</td>
                            </tr>
                        @endforelse
            <x-slot:pagination>
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">{{ $projects->links() }}</div>
            </x-slot:pagination>
        </x-ui.table>
    </x-ui.page-layout>

    <script nonce="{{ app('csp_nonce') }}">
        (function() {
            const activateForms = document.querySelectorAll('.form-activate-project');
            activateForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const projectName = this.getAttribute('data-name');
                    Swal.fire({
                        title: 'Aktifkan Project?',
                        text: `Yakin ingin mengaktifkan project ${projectName}?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#94a3b8',
                        confirmButtonText: 'Ya, Aktifkan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });        })();
    </script>
@endsection
