@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- ── Halaman Aplikasi Node (ala cPanel Setup Node.js App) ──────── --}}
        <x-ui.page-header
            title="Aplikasi Node"
            eyebrow="Node.js App Manager"
            status="{{ $runtimeAvailable ? 'ONLINE' : 'OFFLINE' }}"
            subtitle="Deploy & kelola aplikasi Node.js Anda: build otomatis, start/stop/restart, log, dan environment variables."
            icon="fa-node-js"
            iconColor="emerald">
            <x-slot:actions>
                <a href="{{ route('user_hosting.dashboard') }}"
                    class="ui-btn ui-btn-secondary px-5 py-2.5 text-sm font-medium">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        @if (session('success'))
            <div class="mt-6 border border-emerald-300 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/40 p-5 flex items-center gap-4">
                <div class="w-10 h-10 flex-shrink-0 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-200 dark:border-emerald-800/60">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="mt-6 border border-rose-300 dark:border-rose-800 bg-rose-50 dark:bg-rose-950/40 p-5 flex items-center gap-4">
                <div class="w-10 h-10 flex-shrink-0 bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-200 dark:border-rose-800/60">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <p class="text-sm font-semibold text-rose-800 dark:text-rose-300">{{ session('error') }}</p>
            </div>
        @endif

        @if (!$runtimeAvailable)
            <div class="mt-6 bg-[#E8F0F9] dark:bg-[#1D5B96]/10 border border-[#C9D5E2] dark:border-[#1D5B96]/40 px-5 py-4 flex items-center gap-4">
                <i class="fa-solid fa-circle-info text-[#1D5B96] dark:text-[#6BA8DC]"></i>
                <p class="text-sm text-[#1D5B96] dark:text-[#6BA8DC]">
                    Node runtime sedang tidak tersedia. Aplikasi yang sudah dibuat tetap ditampilkan, tapi build/start hanya bisa dijalankan setelah admin mengaktifkan runtime.
                </p>
            </div>
        @endif

        {{-- ── Buat Aplikasi ─────────────────────────────────────────────── --}}
        <div class="ui-panel mt-6 overflow-hidden" x-data="{ open: false }">
            <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A] px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#1D5B96]/40 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-plus text-[#1D5B96] dark:text-[#6BA8DC]"></i>
                    </div>
                    <div>
                        <p class="font-bold text-[#0F1A2A] dark:text-white text-sm">Buat Aplikasi Baru</p>
                        <p class="text-[#525252] dark:text-white/50 text-xs mt-0.5">
                            Dari source baru (git/zip) atau project Node.js yang sudah ada. Kuota paket {{ ucfirst($plan) }}: {{ $apps->count() }} / {{ $limit }} aplikasi.
                        </p>
                    </div>
                </div>
                @if ($runtimeAvailable && $apps->count() < $limit)
                    <button type="button" @click="open = !open"
                        class="ui-btn ui-btn-primary shrink-0">
                        <i class="fa-solid fa-bolt mr-1"></i> Buat Aplikasi
                    </button>
                @endif
            </div>

            @if ($runtimeAvailable && $apps->count() < $limit)
                <div x-show="open" x-cloak x-data="{ mode: 'new', src: 'repo' }" class="px-6 py-6 border-t border-[#E3EAF1] dark:border-white/10">
                    {{-- Pemilihan mode --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-2xl mb-6">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="create_mode" value="new" class="peer hidden" x-model="mode">
                            <div class="p-4 border border-[#E3EAF1] dark:border-[#2A4463] peer-checked:border-[#1D5B96] peer-checked:bg-[#E8F0F9] dark:peer-checked:bg-[#1D5B96]/20 dark:peer-checked:border-[#6BA8DC] hover:border-[#1D5B96] transition-all">
                                <i class="fa-solid fa-seedling text-xl text-emerald-500 mb-1.5 block"></i>
                                <p class="font-bold text-xs text-[#0F1A2A] dark:text-white">Bikin Project + Aplikasi Baru</p>
                                <p class="text-[11px] text-[#525252] dark:text-white/40 mt-1">Dari Git repo atau ZIP upload — tanpa lewat halaman Deploy Aplikasi.</p>
                            </div>
                        </label>
                        <label class="relative cursor-pointer {{ $availableProjects->isEmpty() ? 'opacity-40 pointer-events-none' : '' }}">
                            <input type="radio" name="create_mode" value="existing" class="peer hidden" x-model="mode" {{ $availableProjects->isEmpty() ? 'disabled' : '' }}>
                            <div class="p-4 border border-[#E3EAF1] dark:border-[#2A4463] peer-checked:border-[#1D5B96] peer-checked:bg-[#E8F0F9] dark:peer-checked:bg-[#1D5B96]/20 dark:peer-checked:border-[#6BA8DC] hover:border-[#1D5B96] transition-all">
                                <i class="fa-solid fa-folder-open text-xl text-[#1D5B96] dark:text-[#6BA8DC] mb-1.5 block"></i>
                                <p class="font-bold text-xs text-[#0F1A2A] dark:text-white">Pakai Project yang Sudah Ada</p>
                                <p class="text-[11px] text-[#525252] dark:text-white/40 mt-1">{{ $availableProjects->isEmpty() ? 'Belum ada project Node.js tanpa aplikasi.' : $availableProjects->count() . ' project Node.js tersedia.' }}</p>
                            </div>
                        </label>
                    </div>

                    {{-- ── Mode A: project baru + aplikasi ── --}}
                    <div x-show="mode === 'new'" x-cloak>
                        <form action="{{ route('user_hosting.apps.store_project') }}" method="POST" enctype="multipart/form-data" class="space-y-4 max-w-2xl">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="ui-label mb-1.5 block">Nama Aplikasi <span class="text-rose-500">*</span></label>
                                    <input type="text" name="project_name" required maxlength="50"
                                        placeholder="mis. api-toko"
                                        class="ui-field ui-field-sm w-full font-mono">
                                    <p class="text-xs text-[#525252] dark:text-white/40 mt-1">Menjadi subdomain: <span class="font-mono">api-toko{{ $domainExtension ?? '.ryaze.my.id' }}</span></p>
                                </div>
                                <div>
                                    <label class="ui-label mb-1.5 block">Domain Extension</label>
                                    <select name="domain_extension" class="ui-field ui-field-sm w-full">
                                        <option value=".ryaze.my.id" selected>.ryaze.my.id</option>
                                        <option value=".ryz.my.id">.ryz.my.id</option>
                                        <option value=".safetalkai.my.id">.safetalkai.my.id</option>
                                        <option value=".ryze.cloud">.ryze.cloud</option>
                                    </select>
                                </div>
                            </div>
                            <div x-data="{ src: 'repo' }">
                                <label class="ui-label mb-1.5 block">Source Code <span class="text-rose-500">*</span></label>
                                <div class="flex items-center gap-4 mb-3">
                                    <label class="inline-flex items-center gap-2 text-xs cursor-pointer">
                                        <input type="radio" name="source_type" value="repo" class="accent-[#1D5B96]" x-model="src" checked> Git Repository
                                    </label>
                                    <label class="inline-flex items-center gap-2 text-xs cursor-pointer">
                                        <input type="radio" name="source_type" value="upload" class="accent-[#1D5B96]" x-model="src"> Upload ZIP
                                    </label>
                                </div>
                                <div x-show="src === 'repo'" x-cloak class="space-y-3">
                                    <input type="url" name="repo_source" placeholder="https://github.com/user/repo.git"
                                        class="ui-field ui-field-sm w-full font-mono text-xs">
                                    <input type="text" name="branch" placeholder="branch (default: main)"
                                        class="ui-field ui-field-sm w-full font-mono text-xs">
                                </div>
                                <div x-show="src === 'upload'" x-cloak>
                                    <input type="file" name="project_zip" accept=".zip" class="ui-field ui-field-sm w-full">
                                    <p class="text-xs text-[#525252] dark:text-white/40 mt-1">Maksimal 50 MB.</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="ui-label mb-1.5 block">Entry File (opsional)</label>
                                    <input type="text" name="entry" placeholder="mis. server.js — kosongkan untuk deteksi otomatis"
                                        class="ui-field ui-field-sm w-full font-mono">
                                </div>
                                <div>
                                    <label class="ui-label mb-1.5 block">Mode Aplikasi</label>
                                    <select name="node_env" class="ui-field ui-field-sm w-full">
                                        <option value="production" selected>Production</option>
                                        <option value="development">Development</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="ui-label mb-1.5 block">Environment Variables (opsional)</label>
                                <textarea name="env_vars_raw" rows="4" placeholder="API_KEY=xxxx&#10;DATABASE_URL=yyyy"
                                    class="ui-field ui-field-sm w-full font-mono text-xs"></textarea>
                                <p class="text-xs text-[#525252] dark:text-white/40 mt-1">Satu KEY=VALUE per baris.</p>
                            </div>
                            <button type="submit" class="ui-btn ui-btn-success w-full sm:w-auto">
                                <i class="fa-solid fa-rocket mr-1"></i> Buat &amp; Deploy Aplikasi
                            </button>
                        </form>
                    </div>

                    {{-- ── Mode B: dari project yang sudah ada ── --}}
                    @if ($availableProjects->isNotEmpty())
                        <div x-show="mode === 'existing'" x-cloak class="mt-2">
                            <form action="{{ route('user_hosting.apps.store') }}" method="POST" class="space-y-4 max-w-2xl">
                                @csrf
                                <div>
                                    <label class="ui-label mb-1.5 block">Project</label>
                                    <select name="project_hashid" required class="ui-field ui-field-sm w-full">
                                        <option value="">-- Pilih project framework Node --</option>
                                        @foreach ($availableProjects as $project)
                                            <option value="{{ $project->hashid }}">{{ $project->ryaze_domain }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-[#525252] dark:text-white/40 mt-1">Project akan di-build (npm install) di runtime container lalu di-start otomatis.</p>
                                </div>
                                <div>
                                    <label class="ui-label mb-1.5 block">Entry File (opsional)</label>
                                    <input type="text" name="entry" placeholder="mis. server.js — kosongkan untuk deteksi otomatis"
                                        class="ui-field ui-field-sm w-full font-mono">
                                </div>
                                <div>
                                    <label class="ui-label mb-1.5 block">Mode Aplikasi</label>
                                    <select name="node_env" class="ui-field ui-field-sm w-full">
                                        <option value="production" selected>Production</option>
                                        <option value="development">Development</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="ui-label mb-1.5 block">Environment Variables (opsional)</label>
                                    <textarea name="env_vars_raw" rows="4" placeholder="API_KEY=xxxx&#10;DATABASE_URL=yyyy"
                                        class="ui-field ui-field-sm w-full font-mono text-xs"></textarea>
                                    <p class="text-xs text-[#525252] dark:text-white/40 mt-1">Satu KEY=VALUE per baris.</p>
                                </div>
                                <button type="submit" class="ui-btn ui-btn-success w-full sm:w-auto">
                                    <i class="fa-solid fa-rocket mr-1"></i> Build &amp; Start
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @elseif (!$runtimeAvailable)
                <div class="px-6 py-5 border-t border-[#E3EAF1] dark:border-white/10">
                    <p class="text-sm text-[#525252] dark:text-white/50">
                        Node runtime belum aktif — aplikasi baru bisa dibuat setelah admin mengaktifkan runtime.
                    </p>
                </div>
            @elseif ($apps->count() >= $limit)
                <div class="px-6 py-5 border-t border-[#E3EAF1] dark:border-white/10">
                    <p class="text-sm text-[#525252] dark:text-white/50">
                        Kuota {{ $limit }} aplikasi untuk paket {{ ucfirst($plan) }} sudah tercapai. Upgrade paket untuk menambah.
                    </p>
                </div>
            @endif
        </div>

        {{-- ── Daftar Aplikasi ───────────────────────────────────────────── --}}
        <div class="ui-panel mt-6 overflow-hidden">
            <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A] px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#1D5B96]/40 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-server text-[#1D5B96] dark:text-[#6BA8DC]"></i>
                    </div>
                    <div>
                        <p class="font-bold text-[#0F1A2A] dark:text-white text-sm">Aplikasi Anda</p>
                        <p class="text-[#525252] dark:text-white/50 text-xs mt-0.5">{{ $apps->count() }} aplikasi Node.js aktif</p>
                    </div>
                </div>
            </div>

            @if ($apps->isEmpty())
                <div class="px-6 py-10 text-center border-t border-[#E3EAF1] dark:border-white/10">
                    <i class="fa-solid fa-box-open text-3xl text-[#C9D5E2] dark:text-white/20"></i>
                    <p class="text-sm text-[#525252] dark:text-white/40 mt-3">Belum ada aplikasi Node.js.</p>
                </div>
            @else
                @foreach ($apps as $app)
                    @php
                        $statusColor = match ($app->status) {
                            'running' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/50',
                            'starting' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/50',
                            'error' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800/50',
                            default => 'bg-[#F0F6FC] text-[#525252] border-[#E3EAF1] dark:bg-[#12233A] dark:text-white/60 dark:border-[#2A4463]',
                        };
                        $statusIcon = match ($app->status) {
                            'running' => 'fa-circle-play',
                            'starting' => 'fa-spinner fa-spin',
                            'error' => 'fa-triangle-exclamation',
                            default => 'fa-circle-stop',
                        };
                    @endphp
                    <div class="border-t border-[#E3EAF1] dark:border-white/10 px-6 py-5"
                        x-data="{ config: false, logs: false }">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="flex items-start gap-4 min-w-0">
                                <div class="w-11 h-11 bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#1D5B96]/40 flex items-center justify-center shrink-0">
                                    <i class="fa-solid {{ $statusIcon }} {{ $app->status === 'running' ? 'text-emerald-600 dark:text-emerald-400' : 'text-[#1D5B96] dark:text-[#6BA8DC]' }}"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="font-bold text-[#0F1A2A] dark:text-white text-sm">{{ $app->project->ryaze_domain }}</p>
                                        <span class="px-2 py-0.5 text-[10px] font-mono font-bold uppercase tracking-wider border {{ $statusColor }}">
                                            {{ strtoupper($app->status) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-[#525252] dark:text-white/50 mt-1">
                                        {{ $app->entry ? 'Entry: <span class="font-mono">' . e($app->entry) . '</span>' : 'Entry: auto-detect' }}
                                        &middot; {{ strtoupper($app->node_env) }}
                                        @if ($app->port) &middot; Port {{ $app->port }} @endif
                                        @if ($app->status === 'running' && $app->started_at) &middot; Jalan {{ $app->started_at->diffForHumans() }} @endif
                                    </p>
                                    @if ($app->last_error)
                                        <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $app->last_error }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 flex-wrap shrink-0">
                                @if ($app->status !== 'running')
                                    <form action="{{ route('user_hosting.apps.start', $app->hashid) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="ui-btn ui-btn-sm ui-btn-success" title="Start aplikasi">
                                            <i class="fa-solid fa-play"></i> Start
                                        </button>
                                    </form>
                                @endif
                                @if ($app->status === 'running')
                                    <form action="{{ route('user_hosting.apps.stop', $app->hashid) }}" method="POST" class="m-0"
                                        onsubmit="event.preventDefault(); let f=this; swConfirm('Stop Aplikasi?', 'Subdomain ini akan tidak dapat diakses sampai aplikasi di-start kembali.').then(res => { if(res.isConfirmed) f.submit(); }); return false;">
                                        @csrf
                                        <button type="submit" class="ui-btn ui-btn-sm ui-btn-secondary" title="Stop aplikasi">
                                            <i class="fa-solid fa-stop"></i> Stop
                                        </button>
                                    </form>
                                    <form action="{{ route('user_hosting.apps.restart', $app->hashid) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="ui-btn ui-btn-sm ui-btn-secondary" title="Restart aplikasi">
                                            <i class="fa-solid fa-rotate"></i> Restart
                                        </button>
                                    </form>
                                @endif
                                <button type="button" @click="logs = !logs" class="ui-btn ui-btn-sm ui-btn-secondary" title="Lihat log">
                                    <i class="fa-solid fa-terminal"></i> Log
                                </button>
                                <button type="button" @click="config = !config" class="ui-btn ui-btn-sm ui-btn-secondary" title="Konfigurasi">
                                    <i class="fa-solid fa-gear"></i> Config
                                </button>
                                <form action="{{ route('user_hosting.apps.redeploy', $app->hashid) }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="ui-btn ui-btn-sm ui-btn-primary" title="Build ulang + start">
                                        <i class="fa-solid fa-arrows-rotate"></i> Re-deploy
                                    </button>
                                </form>
                                <form action="{{ route('user_hosting.apps.destroy', $app->hashid) }}" method="POST" class="m-0"
                                    onsubmit="event.preventDefault(); let f=this; swConfirm('Hapus Aplikasi?', 'Aplikasi akan di-stop dan dihapus dari daftar. File project tetap aman.').then(res => { if(res.isConfirmed) f.submit(); }); return false;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ui-btn ui-btn-sm ui-btn-danger" title="Hapus aplikasi">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Konfigurasi --}}
                        <div x-show="config" x-cloak class="mt-5 border border-[#E3EAF1] dark:border-white/10 rounded p-4 bg-[#F8FBFE] dark:bg-[#0D1B2E]">
                            <form action="{{ route('user_hosting.apps.update', $app->hashid) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @csrf
                                <div>
                                    <label class="ui-label mb-1.5 block">Entry File</label>
                                    <input type="text" name="entry" value="{{ $app->entry }}" placeholder="kosong = auto-detect"
                                        class="ui-field ui-field-sm w-full font-mono">
                                </div>
                                <div>
                                    <label class="ui-label mb-1.5 block">Mode</label>
                                    <select name="node_env" class="ui-field ui-field-sm w-full">
                                        <option value="production" {{ $app->node_env === 'production' ? 'selected' : '' }}>Production</option>
                                        <option value="development" {{ $app->node_env === 'development' ? 'selected' : '' }}>Development</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="ui-label mb-1.5 block">Environment Variables</label>
                                    <textarea name="env_vars_raw" rows="4" class="ui-field ui-field-sm w-full font-mono text-xs"
                                        placeholder="KEY=VALUE per baris">@foreach (($app->env_vars ?? []) as $k => $v){{ $k }}={{ $v }}&#10;@endforeach</textarea>
                                </div>
                                <div class="sm:col-span-2">
                                    <button type="submit" class="ui-btn ui-btn-primary w-full sm:w-auto">
                                        <i class="fa-solid fa-save mr-1"></i> Simpan Konfigurasi
                                    </button>
                                    <p class="text-xs text-[#525252] dark:text-white/40 mt-2">Jika aplikasi sedang jalan, otomatis di-restart untuk menerapkan perubahan.</p>
                                </div>
                            </form>
                        </div>

                        {{-- Log --}}
                        <div x-show="logs" x-cloak x-effect="if (logs) loadAppLogs('{{ $app->hashid }}')"
                            class="mt-5 border border-[#E3EAF1] dark:border-white/10 rounded overflow-hidden">
                            <div class="bg-[#F4F8FC] dark:bg-[#12233A] px-4 py-2 flex items-center justify-between">
                                <p class="text-xs font-bold text-[#0F1A2A] dark:text-white/80 font-mono">LOG APLIKASI</p>
                                <button type="button" class="text-xs font-semibold text-[#1D5B96] dark:text-[#6BA8DC]"
                                    onclick="loadAppLogs('{{ $app->hashid }}')">
                                    <i class="fa-solid fa-refresh"></i> Refresh
                                </button>
                            </div>
                            <pre id="app-log-{{ $app->hashid }}"
                                class="text-[11px] leading-relaxed font-mono p-4 bg-[#0B1622] text-[#9BE3A0] overflow-auto max-h-80 whitespace-pre-wrap">Memuat log...</pre>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </x-ui.page-layout>

    <script>
        function loadAppLogs(appId) {
            const el = document.getElementById('app-log-' + appId);
            if (!el) return;
            el.textContent = 'Memuat log...';
            fetch("{{ url('user/hosting/apps') }}/" + appId + "/logs?lines=300", {
                headers: { 'Accept': 'application/json' }
            })
                .then(r => r.json())
                .then(data => {
                    const app = (data.app_log || '').trim();
                    const build = (data.build_log || '').trim();
                    const stored = (data.stored_build_log || '').trim();
                    el.textContent =
                        (build ? '=== BUILD LOG ===\n' + build + '\n\n' : '') +
                        (app ? '=== APP LOG ===\n' + app : '') +
                        (!app && !build && stored ? '=== BUILD LOG (tercatat) ===\n' + stored : '') +
                        (!app && !build && !stored ? '(log kosong — aplikasi belum pernah di-start)' : '');
                })
                .catch(() => { el.textContent = 'Gagal memuat log.'; });
        }
    </script>
@endsection
