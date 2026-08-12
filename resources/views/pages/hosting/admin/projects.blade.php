@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header title="Master Data Project" description="Kelola seluruh project hosting dari semua pengguna."
            icon="box-open" iconColor="emerald">
            <x-slot:actions>
                <a href="{{ route('admin_hosting.dashboard') }}"
                    class="inline-flex justify-center items-center bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-200 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.table>
            <x-slot:head>
                <th class="px-6 py-4">Project</th>
                <th class="px-6 py-4">Klien</th>
                <th class="px-6 py-4">Domain</th>
                <th class="px-6 py-4 text-center">Status</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </x-slot:head>

            @forelse ($projects as $project)
                <tr class="hover:bg-slate-50 dark:bg-slate-800/50 dark:hover:bg-slate-700/40 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-semibold text-slate-800 dark:text-slate-100">{{ $project->project_name }}</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 uppercase">{{ $project->framework }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-slate-700 dark:text-slate-200">{{ $project->client?->name ?? '—' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $activeDomain = $project->domains()->where('ssl_status', 'active')->first();
                            $displayUrl = $activeDomain ? $activeDomain->domain_name : $project->ryaze_domain;
                        @endphp
                        <a href="https://{{ $displayUrl }}" target="_blank"
                            class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs font-mono">{{ $displayUrl }}</a>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $st = match ($project->status) {
                                'active' => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300',
                                'building' => 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300',
                                'unpaid' => 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300',
                                'suspended' => 'bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300',
                                'error' => 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300',
                                default => 'bg-gray-100 dark:bg-slate-700/50 text-gray-600 dark:text-slate-300',
                            };
                        @endphp
                        <span
                            class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $st }}">{{ ucfirst($project->status) }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            {{-- Kelola Detail --}}
                            <a href="{{ route('user_hosting.show', $project->hashid) }}"
                                class="w-8 h-8 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-600 hover:text-white transition-all duration-200 shadow-sm tooltip"
                                title="Kelola">
                                <i class="fa-solid fa-gear"></i>
                            </a>

                            {{-- Aktivasi --}}
                            @if (in_array($project->status, ['unpaid', 'suspended', 'error']))
                                <form method="POST" action="{{ route('admin_hosting.activate', $project->hashid) }}"
                                    class="admin-action-form" data-msg="Aktifkan project {{ $project->project_name }}?">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-emerald-600 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-600 hover:text-white transition-all duration-200 shadow-sm tooltip"
                                        title="Aktifkan">
                                        <i class="fa-solid fa-play"></i>
                                    </button>
                                </form>
                            @endif

                            {{-- Suspend --}}
                            @if ($project->status === 'active')
                                <form method="POST" action="{{ route('admin_hosting.suspend', $project->hashid) }}"
                                    class="admin-action-form" data-msg="Suspend project {{ $project->project_name }}?">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-amber-600 dark:text-amber-300 bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-600 hover:text-white transition-all duration-200 shadow-sm tooltip"
                                        title="Suspend">
                                        <i class="fa-solid fa-pause"></i>
                                    </button>
                                </form>
                            @endif

                            {{-- Hapus --}}
                            <form method="POST" action="{{ route('admin_hosting.destroy', $project->hashid) }}"
                                class="admin-action-form" data-msg="Hapus PERMANEN project {{ $project->project_name }}?">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-red-600 dark:text-red-300 bg-red-50 dark:bg-red-500/10 hover:bg-red-600 hover:text-white transition-all duration-200 shadow-sm tooltip"
                                    title="Hapus">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">Belum ada project hosting.
                    </td>
                </tr>
            @endforelse

            <x-slot:pagination>
                {{ $projects->links() }}
            </x-slot:pagination>
        </x-ui.table>

        <script nonce="{{ app('csp_nonce') ?? '' }}">
            (function() {
                document.querySelectorAll('.admin-action-form').forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Konfirmasi Tindakan',
                            text: this.getAttribute('data-msg'),
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#4f46e5',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, Lanjutkan!'
                        }).then((result) => {
                            if (result.isConfirmed) this.submit();
                        });
                    });
                });        })();
        </script>
    </x-ui.page-layout>
@endsection
