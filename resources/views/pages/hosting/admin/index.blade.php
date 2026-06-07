@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50">

        {{-- ══ HEADER ══════════════════════════════════════════════════ --}}
        <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg">
                    <i class="fa-solid fa-server text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Manajemen Hosting</h1>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Halo Admin <span class="font-semibold text-indigo-600">{{ Auth::user()->name ?? '' }}</span>.
                        Berikut status server hari ini.
                    </p>
                </div>
            </div>
            <span class="text-xs text-slate-400">{{ now()->format('d M Y, H:i') }} WIB</span>
        </div>

        {{-- ══ FLASH MESSAGE ══════════════════════════════════════════ --}}
        @if (session('success'))
            <div
                class="mt-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mt-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm flex items-center gap-3">
                <i class="fa-solid fa-circle-xmark text-red-500"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- ══ KARTU STATISTIK ════════════════════════════════════════ --}}
        <div class="mt-6 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">

            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500">Total Project</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['total_projects'] }}</h3>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500">Aktif</p>
                        <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['active_projects'] }}</h3>
                    </div>
                    <div
                        class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-check"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500">Total Klien</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['total_clients'] }}</h3>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500">Database</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['total_databases'] }}</h3>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-database"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500">Tagihan Belum Bayar</p>
                        <h3
                            class="text-2xl font-bold {{ $stats['pending_billing'] > 0 ? 'text-amber-600' : 'text-slate-800' }} mt-1">
                            {{ $stats['pending_billing'] }}
                        </h3>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500">Sedang Build</p>
                        <h3
                            class="text-2xl font-bold {{ $stats['building_now'] > 0 ? 'text-blue-600' : 'text-slate-800' }} mt-1">
                            {{ $stats['building_now'] }}
                        </h3>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-sky-50 text-sky-500 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-gears"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- ══ GRID: PENDING + RECENT DEPLOY ═════════════════════════ --}}
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Menunggu Perhatian --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-amber-500 text-sm"></i>
                        Membutuhkan Tindakan
                    </h2>
                    <span class="text-xs bg-amber-100 text-amber-700 font-semibold px-2 py-0.5 rounded-full">
                        {{ $pendingProjects->count() }} item
                    </span>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($pendingProjects as $project)
                        <div class="px-6 py-4 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-800 truncate">{{ $project->project_name }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $project->client?->name ?? '—' }} ·
                                    {{ $project->ryaze_domain }}</p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                {{-- Badge Status --}}
                                @php
                                    $badge = match ($project->status) {
                                        'unpaid' => ['bg-amber-100 text-amber-700', 'Belum Bayar'],
                                        'error' => ['bg-red-100 text-red-700', 'Error'],
                                        'suspended' => ['bg-slate-100 text-slate-600', 'Disuspend'],
                                        default => ['bg-indigo-100 text-indigo-700', ucfirst($project->status)],
                                    };
                                @endphp
                                <span
                                    class="text-xs font-medium px-2 py-0.5 rounded-full {{ $badge[0] }}">{{ $badge[1] }}</span>

                                {{-- Tombol Aktivasi --}}
                                @if ($project->status === 'unpaid' || $project->status === 'suspended')
                                    <form method="POST" action="{{ route('admin_hosting.activate', $project->hashid) }}"
                                        onsubmit="return confirm('Aktifkan project {{ $project->project_name }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="text-xs bg-emerald-500 text-white px-3 py-1.5 rounded-lg hover:bg-emerald-600 transition-colors font-medium">
                                            Aktifkan
                                        </button>
                                    </form>
                                @endif

                                {{-- Tombol Suspend --}}
                                @if ($project->status === 'active' || $project->status === 'error')
                                    <form method="POST" action="{{ route('admin_hosting.suspend', $project->hashid) }}"
                                        onsubmit="return confirm('Suspend project {{ $project->project_name }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="text-xs bg-slate-500 text-white px-3 py-1.5 rounded-lg hover:bg-slate-600 transition-colors font-medium">
                                            Suspend
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center text-slate-400 text-sm">
                            <i class="fa-solid fa-circle-check text-2xl text-emerald-400 mb-2 block"></i>
                            Tidak ada project yang membutuhkan tindakan.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Deploy Terbaru --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50">
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-rocket text-indigo-500 text-sm"></i>
                        Deploy Terbaru
                    </h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($recentDeployments as $deploy)
                        <div class="px-6 py-3.5 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate">
                                    {{ $deploy->project?->project_name ?? '—' }}
                                </p>
                                <p class="text-xs text-slate-400 truncate">
                                    {{ $deploy->project?->client?->name ?? '—' }} ·
                                    {{ $deploy->commit_message ? Str::limit($deploy->commit_message, 40) : 'No message' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                @php
                                    $ds = match ($deploy->status) {
                                        'success' => 'bg-emerald-100 text-emerald-700',
                                        'failed' => 'bg-red-100 text-red-700',
                                        'queued' => 'bg-slate-100 text-slate-600',
                                        'running' => 'bg-blue-100 text-blue-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span
                                    class="text-xs font-medium px-2 py-0.5 rounded-full {{ $ds }}">{{ ucfirst($deploy->status) }}</span>
                                <span
                                    class="text-xs text-slate-400 whitespace-nowrap">{{ $deploy->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center text-slate-400 text-sm">
                            Belum ada deployment.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ══ TABEL SEMUA PROJECT ═════════════════════════════════════ --}}
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-800">Semua Project Hosting</h2>
                <span class="text-xs text-slate-500">{{ $allProjects->total() }} total</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3">Project</th>
                            <th class="px-6 py-3">Klien</th>
                            <th class="px-6 py-3">Domain</th>
                            <th class="px-6 py-3">Framework</th>
                            <th class="px-6 py-3">Paket / Tagihan</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($allProjects as $project)
                            <tr class="hover:bg-slate-50 transition-colors">

                                {{-- Project --}}
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-slate-800">{{ $project->project_name }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $project->created_at->format('d M Y') }}
                                    </p>
                                </td>

                                {{-- Klien --}}
                                <td class="px-6 py-4">
                                    <p class="font-medium text-slate-700">{{ $project->client?->name ?? '—' }}</p>
                                    <p class="text-xs text-slate-400">{{ $project->client?->email ?? '' }}</p>
                                </td>

                                {{-- Domain --}}
                                <td class="px-6 py-4">
                                    <a href="https://{{ $project->ryaze_domain }}" target="_blank"
                                        class="text-indigo-600 hover:underline text-xs font-mono">
                                        {{ $project->ryaze_domain }}
                                    </a>
                                    @if ($project->custom_domain)
                                        <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $project->custom_domain }}
                                        </p>
                                    @endif
                                </td>

                                {{-- Framework --}}
                                <td class="px-6 py-4">
                                    @php
                                        $fw = strtolower($project->framework ?? '');
                                        $fwColor = match ($fw) {
                                            'react' => 'bg-sky-100 text-sky-700',
                                            'nextjs' => 'bg-slate-100 text-slate-700',
                                            'laravel' => 'bg-red-100 text-red-700',
                                            'node' => 'bg-green-100 text-green-700',
                                            'python' => 'bg-yellow-100 text-yellow-700',
                                            default => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $fwColor }}">
                                        {{ strtoupper($project->framework ?? '—') }}
                                    </span>
                                </td>

                                {{-- Billing --}}
                                <td class="px-6 py-4">
                                    @if ($project->billing)
                                        <p class="text-sm text-slate-700 font-medium">{{ $project->billing->plan_name }}
                                        </p>
                                        <p
                                            class="text-xs {{ $project->billing->status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }} font-medium mt-0.5">
                                            {{ $project->billing->status === 'paid' ? 'Lunas' : 'Belum Bayar' }}
                                            · Rp{{ number_format($project->billing->amount, 0, ',', '.') }}
                                        </p>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusConfig = match ($project->status) {
                                            'active' => ['bg-emerald-100 text-emerald-700', 'Aktif'],
                                            'building' => ['bg-blue-100 text-blue-700', 'Building'],
                                            'unpaid' => ['bg-amber-100 text-amber-700', 'Belum Bayar'],
                                            'suspended' => ['bg-slate-100 text-slate-600', 'Disuspend'],
                                            'error' => ['bg-red-100 text-red-700', 'Error'],
                                            default => ['bg-gray-100 text-gray-600', ucfirst($project->status)],
                                        };
                                    @endphp
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusConfig[0] }}">
                                        {{ $statusConfig[1] }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Aktivasi (jika belum aktif) --}}
                                        @if (in_array($project->status, ['unpaid', 'suspended', 'error']))
                                            <form method="POST"
                                                action="{{ route('admin_hosting.activate', $project->hashid) }}"
                                                onsubmit="return confirm('Aktifkan project ini?')">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="text-xs bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg transition-colors font-medium">
                                                    Aktifkan
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Suspend (jika aktif) --}}
                                        @if ($project->status === 'active')
                                            <form method="POST"
                                                action="{{ route('admin_hosting.suspend', $project->hashid) }}"
                                                onsubmit="return confirm('Suspend project ini?')">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="text-xs bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg transition-colors font-medium">
                                                    Suspend
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Hapus --}}
                                        <form method="POST"
                                            action="{{ route('admin_hosting.destroy', $project->hashid) }}"
                                            onsubmit="return confirm('HAPUS project {{ $project->project_name }}? Tindakan ini tidak bisa dibatalkan!')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="text-xs bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1.5 rounded-lg transition-colors font-medium">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                                    Belum ada project hosting.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($allProjects->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
                    {{ $allProjects->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
