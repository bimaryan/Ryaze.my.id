@extends('index')
@section('content')
    <x-ui.page-layout>
{{-- ── 3. ADMIN HOSTING – Membutuhkan Tindakan ────────────────────── --}}
        <div class="p-5 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="shrink-0 w-11 h-11 flex items-center justify-center bg-emerald-50 text-emerald-600 rounded-lg">
                    <i class="fa-solid fa-clock text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Membutuhkan Tindakan</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Project yang butuh aktivasi, suspend, atau perbaikan error.</p>
                </div>
            </div>
            <a href="{{ route('admin_hosting.dashboard') }}" class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                &larr; Kembali
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mt-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">Project & Klien</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                            </x-slot:head>
                        @forelse ($projects as $project)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-slate-800">{{ $project->project_name }}</p>
                                    <p class="text-xs text-slate-500">{{ $project->client?->name ?? '—' }} ·
                                        {{ $project->ryaze_domain }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $badge = match ($project->status) {
                                            'unpaid' => ['bg-amber-100 text-amber-700', 'Belum Bayar'],
                                            'error' => ['bg-red-100 text-red-700', 'Error'],
                                            'suspended' => ['bg-slate-100 text-slate-600', 'Disuspend'],
                                            default => ['bg-indigo-100 text-indigo-700', ucfirst($project->status)],
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
                                <td colspan="3" class="px-6 py-10 text-center text-slate-400">Tidak ada project yang
                                    membutuhkan tindakan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-200">{{ $projects->links() }}</div>
        </div>
    </div>

    <script nonce="{{ app('csp_nonce') }}">
        document.addEventListener('DOMContentLoaded', function() {
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
            });
        });
    </script>
@endsection
