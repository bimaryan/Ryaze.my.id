@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Master Data Project" 
            description="Kelola seluruh project hosting dari semua pengguna." 
            icon="box-open" 
            iconColor="emerald">
            <x-slot:actions>
                <a href="{{ route('admin_hosting.dashboard') }}" class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
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
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-semibold text-slate-800">{{ $project->project_name }}</p>
                        <p class="text-xs text-slate-400 uppercase">{{ $project->framework }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-slate-700">{{ $project->client?->name ?? '—' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <a href="https://{{ $project->ryaze_domain }}" target="_blank"
                            class="text-indigo-600 hover:underline text-xs font-mono">{{ $project->ryaze_domain }}</a>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $st = match ($project->status) {
                                'active' => 'bg-emerald-100 text-emerald-700',
                                'building' => 'bg-blue-100 text-blue-700',
                                'unpaid' => 'bg-amber-100 text-amber-700',
                                'suspended' => 'bg-slate-100 text-slate-600',
                                'error' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span
                            class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $st }}">{{ ucfirst($project->status) }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            {{-- Kelola Detail --}}
                            <a href="{{ route('user_hosting.show', $project->hashid) }}"
                                class="text-xs bg-indigo-100 hover:bg-indigo-200 text-indigo-700 px-3 py-1.5 rounded-lg transition-colors font-medium">Kelola</a>

                            {{-- Aktivasi --}}
                            @if (in_array($project->status, ['unpaid', 'suspended', 'error']))
                                <form method="POST"
                                    action="{{ route('admin_hosting.activate', $project->hashid) }}"
                                    class="admin-action-form"
                                    data-msg="Aktifkan project {{ $project->project_name }}?">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="text-xs bg-emerald-100 hover:bg-emerald-200 text-emerald-700 px-3 py-1.5 rounded-lg transition-colors font-medium">Aktifkan</button>
                                </form>
                            @endif

                            {{-- Suspend --}}
                            @if ($project->status === 'active')
                                <form method="POST"
                                    action="{{ route('admin_hosting.suspend', $project->hashid) }}"
                                    class="admin-action-form"
                                    data-msg="Suspend project {{ $project->project_name }}?">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="text-xs bg-amber-100 hover:bg-amber-200 text-amber-700 px-3 py-1.5 rounded-lg transition-colors font-medium">Suspend</button>
                                </form>
                            @endif

                            {{-- Hapus --}}
                            <form method="POST"
                                action="{{ route('admin_hosting.destroy', $project->hashid) }}"
                                class="admin-action-form"
                                data-msg="Hapus PERMANEN project {{ $project->project_name }}?">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-xs bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1.5 rounded-lg transition-colors font-medium">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">Belum ada project hosting.
                    </td>
                </tr>
            @endforelse

            <x-slot:pagination>
                {{ $projects->links() }}
            </x-slot:pagination>
        </x-ui.table>

        <script nonce="{{ app('csp_nonce') ?? '' }}">
            document.addEventListener('DOMContentLoaded', function() {
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
                });
            });
        </script>
    </x-ui.page-layout>
@endsection
