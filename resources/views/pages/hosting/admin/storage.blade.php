@extends('index')

@section('content')
<div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50">
    <div class="p-6 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="shrink-0 w-12 h-12 flex items-center justify-center bg-teal-50 text-teal-600 rounded-xl">
                <i class="fa-solid fa-hard-drive text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800">Alokasi Penyimpanan Proyek</h1>
                <p class="text-sm text-slate-500 mt-1">Daftar semua proyek hosting dan batasan penyimpanannya.</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Nama Proyek</th>
                        <th class="px-6 py-4">Pemilik (Klien)</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Limit Storage</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
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
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin_hosting.projects') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                    Detail
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
                </tbody>
            </table>
        </div>
        @if($projects->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
