@extends('index')
@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50">
        {{-- ── 3. ADMIN HOSTING – Membutuhkan Tindakan ────────────────────── --}}
        <div class="p-5 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center gap-4">
            <a href="{{ route('admin_hosting.dashboard') }}"
                class="shrink-0 w-10 h-10 flex items-center justify-center bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div class="shrink-0 w-11 h-11 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg">
                <i class="fa-solid fa-triangle-exclamation text-lg"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800">Membutuhkan Tindakan</h1>
                <p class="text-sm text-slate-500 mt-0.5">Project yang butuh aktivasi, suspend, atau perbaikan error.</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mt-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">Project & Klien</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($projects as $project)
                            <tr class="hover:bg-slate-50">
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
                                                onsubmit="return confirm('Aktifkan project {{ $project->project_name }}?')">
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
@endsection
