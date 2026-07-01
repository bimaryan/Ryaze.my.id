@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- ── 2. ADMIN HOSTING – Riwayat Deployment ──────────────────────── --}}
        <x-ui.page-header 
            title="Riwayat Deployment" 
            subtitle="Pantau status build dan log dari seluruh project klien." 
            icon="fa-solid fa-rocket">
            <x-slot:actions>
                <a href="{{ route('admin_hosting.dashboard') }}"
                    class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        {{-- Tabel Riwayat Deploy --}}
        <x-ui.table class="mt-6">
            <x-slot:head>
                <th class="px-6 py-4">Project</th>
                <th class="px-6 py-4">Klien & Info Commit</th>
                <th class="px-6 py-4">Waktu</th>
                <th class="px-6 py-4 text-center">Status</th>
            </x-slot:head>
            @forelse ($deployments as $deploy)
                        <tr class="hover:bg-slate-50 transition-colors">

                            {{-- Project Info --}}
                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-800">
                                    {{ $deploy->project?->project_name ?? 'Project Dihapus' }}</p>
                                @if ($deploy->project)
                                    <a href="https://{{ $deploy->project->ryaze_domain }}" target="_blank"
                                        class="text-xs text-indigo-600 hover:underline font-mono">
                                        {{ $deploy->project->ryaze_domain }}
                                    </a>
                                @endif
                            </td>

                            {{-- Client & Commit --}}
                            <td class="px-6 py-4">
                                <p class="font-medium text-slate-700">{{ $deploy->project?->client?->name ?? '—' }}</p>
                                <p class="text-xs text-slate-400 mt-0.5 truncate max-w-[250px]"
                                    title="{{ $deploy->commit_message }}">
                                    <i class="fa-solid fa-code-commit mr-1"></i>
                                    {{ $deploy->commit_message ? Str::limit($deploy->commit_message, 45) : 'System / Manual Deploy' }}
                                </p>
                            </td>

                            {{-- Time --}}
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-700">{{ $deploy->created_at->format('d M Y, H:i') }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $deploy->created_at->diffForHumans() }}</p>
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4 text-center">
                                @php
                                    $ds = match ($deploy->status) {
                                        'success' => 'bg-emerald-100 text-emerald-700',
                                        'failed', 'error' => 'bg-red-100 text-red-700',
                                        'queued' => 'bg-slate-100 text-slate-600',
                                        'running', 'building' => 'bg-blue-100 text-blue-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $ds }}">
                                    {{ ucfirst($deploy->status) }}
                                </span>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 text-sm">
                                <i class="fa-solid fa-rocket text-3xl mb-3 opacity-50 block"></i>
                                Belum ada riwayat deployment.
                            </td>
                        </tr>
                        @endforelse
            <x-slot:pagination>
                @if ($deployments->hasPages())
                    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
                        {{ $deployments->links() }}
                    </div>
                @endif
            </x-slot:pagination>
        </x-ui.table>
    </x-ui.page-layout>
@endsection
