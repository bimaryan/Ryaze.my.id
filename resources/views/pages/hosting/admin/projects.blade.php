@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50">
        <div class="p-5 bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="shrink-0 w-11 h-11 flex items-center justify-center bg-emerald-50 text-emerald-600 rounded-lg">
                    <i class="fa-solid fa-box-open text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Master Data Project</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Kelola seluruh project hosting dari semua pengguna.</p>
                </div>
            </div>
            <a href="{{ route('admin_hosting.dashboard') }}" class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                &larr; Kembali
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">Project</th>
                            <th class="px-6 py-4">Klien</th>
                            <th class="px-6 py-4">Domain</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($projects as $project)
                            <tr class="hover:bg-slate-50">
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
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-200">{{ $projects->links() }}</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000
            });
        @endif
    </script>
@endsection
