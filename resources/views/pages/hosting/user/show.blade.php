@extends('index')

@section('content')
    <x-ui.page-layout>

        {{-- Alerts --}}
        {{-- ── Header Project ────────────────────────────────────────────────── --}}
        <x-ui.page-header 
            title="{{ $project->project_name }}">
            <x-slot:iconSlot>
                @php
                    $fwIcon = match ($project->framework) {
                        'react' => 'fa-brands fa-react text-sky-500',
                        'nextjs' => 'fa-brands fa-node-js text-slate-800',
                        'laravel' => 'fa-brands fa-laravel text-red-500',
                        'python' => 'fa-brands fa-python text-yellow-500',
                        'node' => 'fa-brands fa-node text-emerald-500',
                        'php' => 'fa-brands fa-php text-indigo-500',
                        'vue' => 'fa-brands fa-vuejs text-emerald-500',
                        default => 'fa-brands fa-html5 text-orange-500',
                    };
                @endphp
                <div class="shrink-0 w-12 h-12 border border-slate-200 rounded-lg flex items-center justify-center bg-white shadow-sm">
                    <i class="{{ $fwIcon }} text-2xl"></i>
                </div>
            </x-slot:iconSlot>
            <x-slot:subtitle>
                <a href="https://{{ $project->ryaze_domain }}" target="_blank"
                    class="text-sm font-medium text-indigo-600 hover:underline flex items-center gap-1 mt-1">
                    {{ $project->ryaze_domain }}
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                </a>
            </x-slot:subtitle>
            <x-slot:actions>
                @php
                    $statusClass = match ($project->status) {
                        'active' => 'bg-emerald-100 text-emerald-700',
                        'building' => 'bg-amber-100 text-amber-700 animate-pulse',
                        'unpaid' => 'bg-rose-100 text-rose-700 font-bold',
                        default => 'bg-rose-100 text-rose-700',
                    };
                    $statusIcon = match ($project->status) {
                        'active' => 'fa-circle-check',
                        'building' => 'fa-spinner fa-spin',
                        'unpaid' => 'fa-file-invoice-dollar',
                        default => 'fa-triangle-exclamation',
                    };
                @endphp
                <span
                    class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide {{ $statusClass }}">
                    <i class="fa-solid {{ $statusIcon }}"></i>
                    {{ $project->status }}
                </span>
                <a href="{{ route('user_hosting.projects') }}" class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        {{-- Tab Navigation --}}
        <div class="flex flex-wrap gap-2 mb-6 mt-6 bg-white border border-slate-200 rounded-xl p-1.5 shadow-sm w-full">
            <button data-tab="overview" id="tab-overview"
                class="tab-btn flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all bg-indigo-600 text-white shadow">
                <i class="fa-solid fa-chart-simple"></i> <span>Overview</span>
            </button>
            <button data-tab="logs" id="tab-logs"
                class="tab-btn flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all text-slate-500 hover:text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-scroll"></i> <span>Build Logs</span>
            </button>
            <button data-tab="terminal" id="tab-terminal"
                class="tab-btn flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all text-slate-500 hover:text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-terminal"></i> <span>Terminal</span>
            </button>
            <button data-tab="files" id="tab-files"
                class="tab-btn flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all text-slate-500 hover:text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-folder-tree"></i> <span>Root Files</span>
            </button>
            <button data-tab="env" id="tab-env"
                class="tab-btn flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all text-slate-500 hover:text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-key"></i> <span>.env</span>
            </button>
            <button data-tab="settings" id="tab-settings"
                class="tab-btn flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all text-slate-500 hover:text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-gears"></i> <span>Settings</span>
            </button>
            <button data-tab="domains" id="tab-domains"
                class="tab-btn flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all text-slate-500 hover:text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-globe"></i> <span>Domains</span>
            </button>
            <button data-tab="crons" id="tab-crons"
                class="tab-btn flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all text-slate-500 hover:text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-clock"></i> <span>Cron Jobs</span>
            </button>
        </div>

        {{-- TAB: OVERVIEW --}}
        <div id="panel-overview" class="tab-panel">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    @if ($project->status == 'active')
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="bg-slate-100 px-4 py-2.5 border-b border-slate-200 flex items-center gap-3">
                                <div class="flex gap-1.5">
                                    <div class="w-3 h-3 rounded-full bg-rose-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                                </div>
                                <div
                                    class="ml-2 bg-white px-3 py-1 rounded-md text-xs text-slate-500 w-full max-w-md flex items-center gap-2 border border-slate-200 shadow-sm">
                                    <i class="fa-solid fa-lock text-[10px] text-emerald-600"></i>
                                    https://{{ $project->ryaze_domain }}
                                </div>
                                <a href="https://{{ $project->ryaze_domain }}" target="_blank"
                                    class="ml-auto text-slate-400 hover:text-indigo-600 transition-colors">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                </a>
                            </div>
                            <div class="w-full h-[450px] bg-slate-50 flex items-center justify-center relative">
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <i class="fa-solid fa-circle-notch fa-spin text-slate-300 text-3xl"></i>
                                </div>
                                <iframe src="https://{{ $project->ryaze_domain }}"
                                    class="w-full h-full border-0 relative z-10 bg-white"></iframe>
                            </div>
                        </div>
                    @elseif ($project->status == 'unpaid')
                        @php
                            $unpaidPayment = $project->payments->where('status', 'unpaid')->first();
                        @endphp
                        <div class="bg-white rounded-xl border border-rose-200 p-10 text-center shadow-sm">
                            <div class="w-16 h-16 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-file-invoice-dollar text-rose-500 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 mb-2">Menunggu Pembayaran</h3>
                            <p class="text-slate-500 mb-6 text-sm max-w-md mx-auto">Tagihan hosting Anda belum dibayar. Deployment akan otomatis dimulai setelah Anda menyelesaikan pembayaran.</p>
                            @if($unpaidPayment)
                                <a href="https://app.pakasir.com/pay/{{ config('services.pakasir.slug', 'ryaze') }}/{{ $unpaidPayment->amount }}?order_id={{ $unpaidPayment->invoice_number }}" target="_blank"
                                    class="inline-flex items-center justify-center gap-2 bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-md shadow-rose-200">
                                    <i class="fa-solid fa-credit-card"></i> Bayar Rp {{ number_format($unpaidPayment->amount, 0, ',', '.') }}
                                </a>
                            @else
                                <p class="text-xs text-rose-500">Invoice tidak ditemukan. Harap hubungi Admin.</p>
                            @endif
                        </div>
                    @else
                        <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
                            <i class="fa-solid fa-satellite-dish text-slate-300 text-5xl mb-4"></i>
                            <p class="text-slate-500 font-medium">Preview tersedia setelah deployment selesai.</p>
                        </div>
                    @endif
                </div>
                <div class="space-y-4">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                        <h3 class="font-bold text-slate-800 mb-4 border-b pb-2 text-sm">Detail Deployment</h3>
                        <div class="space-y-4 text-sm">
                            <div>
                                <span class="block text-slate-500 text-xs mb-1">Source Repository</span>
                                <a href="{{ $project->repo_source }}" target="_blank"
                                    class="font-semibold text-slate-800 hover:text-indigo-600 flex items-center">
                                    <i class="fa-brands fa-github mr-2 text-lg"></i>
                                    {{ str_replace('https://github.com/', '', $project->repo_source) }}
                                </a>
                            </div>
                            <div>
                                <span class="block text-slate-500 text-xs mb-1">Branch</span>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded bg-slate-100 border border-slate-200 font-mono text-slate-700 text-xs">
                                    <i class="fa-solid fa-code-branch mr-1.5"></i> {{ $project->branch }}
                                </span>
                            </div>
                            <div>
                                <span class="block text-slate-500 text-xs mb-1">Framework</span>
                                <span class="font-semibold text-slate-800 uppercase">{{ $project->framework }}</span>
                            </div>
                            <div>
                                <span class="block text-slate-500 text-xs mb-1">Root Directory</span>
                                <span
                                    class="font-mono text-xs text-slate-600 bg-slate-50 border border-slate-200 px-2 py-1 rounded block truncate">
                                    /{{ str_replace('.ryaze.my.id', '', $project->ryaze_domain) }}
                                </span>
                            </div>
                            <form action="{{ route('user_hosting.redeploy', $project->hashid) }}" method="POST"
                                class="mt-2">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition-colors text-sm">
                                    <i class="fa-solid fa-rotate"></i> Redeploy Sekarang
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB: BUILD LOGS --}}
        <div id="panel-logs" class="tab-panel hidden">
            <div class="bg-slate-900 rounded-xl shadow-md border border-slate-800 overflow-hidden">
                <div
                    class="bg-slate-800 px-4 py-3 flex flex-col sm:flex-row items-start sm:items-center gap-3 border-b border-slate-700">
                    <div class="flex items-center gap-2 text-slate-400 text-xs">
                        <i class="fa-solid fa-globe"></i>
                        <a id="website-log-link" href="https://{{ $project->ryaze_domain }}" target="_blank"
                            class="text-indigo-400 hover:text-indigo-300 truncate">
                            {{ $project->ryaze_domain }}
                        </a>
                    </div>
                    <div class="text-slate-400 text-xs">
                        Status: <span id="build-log-status"
                            class="font-semibold text-slate-200">{{ $project->status }}</span>
                    </div>
                    <div class="text-slate-400 text-xs ml-auto">
                        <span
                            id="build-log-updated">{{ $project->deployments->first()?->created_at?->diffForHumans() ?? 'Initial Build' }}</span>
                    </div>
                </div>
                <div class="p-4 h-[500px] overflow-y-auto font-mono text-sm" id="build-log-container">
                    @if ($project->deployments->count() > 0)
                        <pre id="build-log-text" class="text-emerald-400 whitespace-pre-wrap leading-relaxed">{{ $project->deployments->first()->build_logs }}</pre>
                        @if ($project->status == 'building')
                            <div id="build-log-pulse" class="mt-2 flex items-center text-slate-400 animate-pulse">
                                <span class="mr-2">></span>
                                <span class="w-2 h-4 bg-slate-400 inline-block animate-ping"></span>
                            </div>
                        @endif
                    @else
                        <p class="text-slate-500">Belum ada log deployment.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- TAB: TERMINAL --}}
        <div id="panel-terminal" class="tab-panel hidden">
            <div class="bg-slate-900 rounded-xl shadow-xl border border-slate-700 overflow-hidden">
                <div class="bg-slate-800 px-4 py-3 flex items-center gap-3 border-b border-slate-700 select-none">
                    <div class="flex gap-1.5">
                        {{-- Tombol merah: clear — pakai data-action, BUKAN onclick --}}
                        <div class="w-3 h-3 rounded-full bg-rose-500 hover:bg-rose-400 cursor-pointer transition-colors"
                            data-action="clear-terminal" title="Clear"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                    </div>
                    <div class="flex items-center gap-2 ml-2 min-w-0">
                        <i class="fa-solid fa-terminal text-slate-400 text-xs shrink-0"></i>
                        <span class="text-slate-300 text-xs font-mono font-semibold shrink-0">bash</span>
                        <span class="text-slate-600 text-xs shrink-0">—</span>
                        <span class="text-slate-400 text-xs font-mono truncate" id="terminal-cwd-display">
                            /{{ str_replace('.ryaze.my.id', '', $project->ryaze_domain) }}
                        </span>
                    </div>
                    <div class="ml-auto shrink-0">
                        <button data-action="clear-terminal"
                            class="text-slate-500 hover:text-slate-300 text-xs transition-colors px-2 py-1 rounded hover:bg-slate-700">
                            <i class="fa-solid fa-trash-can mr-1"></i><span class="hidden sm:inline">Clear</span>
                        </button>
                    </div>
                </div>
                {{-- Terminal output: data-action menggantikan onclick --}}
                <div id="terminal-output"
                    class="px-4 pt-4 pb-2 font-mono text-sm text-slate-200 overflow-y-auto leading-relaxed cursor-text"
                    style="height:420px;background:#0f1117;" data-action="focus-terminal">
                    <div id="terminal-welcome" class="text-slate-500 mb-3 select-none border-b border-slate-800 pb-3">
                        <span class="text-emerald-500 font-bold">ryaze</span><span class="text-slate-400"> hosting
                            terminal</span><br>
                        <span class="text-slate-600 text-xs">Project: <span
                                class="text-slate-400">{{ $project->project_name }}</span> · Ketik perintah dan tekan
                            Enter.</span>
                    </div>
                </div>
                <div class="flex items-center bg-[#0f1117] border-t border-slate-800 px-4 py-3 gap-2">
                    <span id="terminal-prompt"
                        class="text-emerald-400 font-mono text-sm font-bold select-none shrink-0 whitespace-nowrap">
                        <span
                            class="text-indigo-400">{{ str_replace('.ryaze.my.id', '', $project->ryaze_domain) }}</span><span
                            class="text-slate-400"> $</span>
                    </span>
                    <input type="text" id="terminal-input" autocomplete="off" autocorrect="off" autocapitalize="off"
                        spellcheck="false" placeholder="ketik perintah..."
                        class="flex-1 bg-transparent text-slate-100 font-mono text-sm outline-none placeholder-slate-700 caret-emerald-400 min-w-0">
                    {{-- Tombol kirim: data-action, BUKAN onclick --}}
                    <button data-action="run-command"
                        class="text-slate-500 hover:text-emerald-400 transition-colors shrink-0">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                    </button>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-3 flex items-center gap-1.5">
                <i class="fa-solid fa-circle-info text-slate-500"></i>
                Terminal berjalan di folder project. Mendukung <kbd
                    class="bg-slate-200 text-slate-600 px-1 rounded text-[10px]">cd</kbd>, <kbd
                    class="bg-slate-200 text-slate-600 px-1 rounded text-[10px]">↑↓</kbd> history, <kbd
                    class="bg-slate-200 text-slate-600 px-1 rounded text-[10px]">Ctrl+L</kbd> clear.
            </p>
        </div>

        {{-- TAB: FILE MANAGER --}}
        <div id="panel-files" class="tab-panel hidden relative">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative">
                <div
                    class="px-4 py-3 border-b border-slate-100 bg-slate-50 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2 min-w-0">
                        {{-- Semua tombol pakai data-action --}}
                        <button data-action="navigate-up"
                            class="text-slate-500 hover:text-indigo-600 transition-colors bg-white px-2 py-1.5 rounded border border-slate-200 shadow-sm shrink-0"
                            title="Kembali">
                            <i class="fa-solid fa-level-up-alt fa-flip-horizontal"></i>
                        </button>
                        <div
                            class="text-sm font-mono text-slate-600 bg-white px-3 py-1.5 rounded border border-slate-200 truncate max-w-xs">
                            <i class="fa-solid fa-server text-slate-400 mr-1"></i>/<span id="current-path-display"
                                class="text-indigo-600 font-bold"></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button data-action="new-file"
                            class="text-xs bg-white border border-slate-200 text-slate-600 px-3 py-1.5 rounded hover:bg-slate-50 transition-colors">
                            <i class="fa-solid fa-file-circle-plus text-emerald-500 mr-1"></i><span
                                class="hidden sm:inline">New File</span>
                        </button>
                        <button data-action="new-dir"
                            class="text-xs bg-white border border-slate-200 text-slate-600 px-3 py-1.5 rounded hover:bg-slate-50 transition-colors">
                            <i class="fa-solid fa-folder-plus text-amber-500 mr-1"></i><span class="hidden sm:inline">New
                                Folder</span>
                        </button>
                        <label
                            class="text-xs bg-indigo-600 text-white px-3 py-1.5 rounded hover:bg-indigo-700 transition-colors cursor-pointer">
                            <i class="fa-solid fa-cloud-arrow-up mr-1"></i><span class="hidden sm:inline">Upload</span>
                            {{-- onchange diganti data-action --}}
                            <input type="file" id="upload-input" class="hidden" data-action="upload-file">
                        </label>
                        <button data-action="refresh-files"
                            class="text-xs bg-white border border-slate-200 text-slate-600 px-2.5 py-1.5 rounded hover:bg-slate-50 transition-colors">
                            <i class="fa-solid fa-rotate-right"></i>
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto h-[500px] relative">
                    <table class="w-full text-sm text-left text-slate-600 table-fixed">
                        <thead
                            class="bg-white text-xs uppercase font-semibold text-slate-400 border-b border-slate-100 sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th class="px-6 py-3">Nama</th>
                                <th class="px-4 py-3 w-24">Ukuran</th>
                                <th class="px-4 py-3 hidden sm:table-cell">Diubah</th>
                                <th class="px-4 py-3 w-28 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="file-manager-body" class="divide-y divide-slate-50 font-mono text-[13px]"></tbody>
                    </table>
                    <div id="file-manager-loader"
                        class="hidden absolute inset-0 bg-white/80 flex items-center justify-center z-20">
                        <i class="fa-solid fa-circle-notch fa-spin text-3xl text-indigo-500"></i>
                    </div>
                </div>

                {{-- File Editor Modal --}}
                <div id="file-editor-modal" class="hidden absolute inset-0 bg-slate-900 z-30 flex flex-col">
                    <div
                        class="px-4 py-3 border-b border-slate-700 bg-slate-800 flex justify-between items-center text-white">
                        <div class="font-mono text-sm flex items-center gap-2 min-w-0">
                            <i class="fa-solid fa-file-code text-indigo-400 shrink-0"></i>
                            <span id="editor-filename" class="truncate">filename.php</span>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            {{-- data-action menggantikan onclick --}}
                            <button data-action="close-editor"
                                class="px-3 py-1.5 bg-slate-700 hover:bg-slate-600 rounded text-xs transition font-semibold">Batal</button>
                            <button data-action="save-editor"
                                class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 rounded text-xs transition font-semibold flex items-center gap-1.5">
                                <i class="fa-solid fa-save"></i> Simpan
                            </button>
                        </div>
                    </div>
                    <textarea id="file-editor-textarea" spellcheck="false"
                        class="flex-1 w-full bg-slate-900 text-emerald-400 font-mono text-sm p-4 outline-none resize-none leading-relaxed"></textarea>
                    <div id="editor-loader"
                        class="hidden absolute inset-0 bg-slate-900/80 flex items-center justify-center z-40">
                        <i class="fa-solid fa-circle-notch fa-spin text-3xl text-indigo-500"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB: ENV --}}
        <div id="panel-env" class="tab-panel hidden">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden max-w-3xl">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-key text-amber-500"></i> Environment Variables
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Format: <code
                            class="bg-slate-100 px-1 py-0.5 rounded text-rose-500">KUNCI=nilai</code>. Perubahan berlaku
                        setelah redeploy.</p>
                </div>
                <form action="{{ route('user_hosting.env.update', $project->hashid) }}" method="POST">
                    @csrf
                    <div class="bg-slate-900 border-b border-slate-800 p-1">
                        <textarea name="env_content" rows="18"
                            class="w-full bg-transparent text-emerald-400 font-mono text-sm p-4 focus:outline-none resize-y"
                            placeholder="API_KEY=rahasia&#10;DB_HOST=127.0.0.1" spellcheck="false">{{ old('env_content', $envContent) }}</textarea>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 flex justify-end">
                        <button type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                            <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan .env
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- TAB: SETTINGS --}}
        <div id="panel-settings" class="tab-panel hidden space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="font-bold text-slate-800">Konfigurasi Aplikasi</h3>
                    <p class="text-xs text-slate-500">Atur parameter dasar environment project.</p>
                </div>
                <form action="{{ route('user_hosting.settings.update', $project->hashid) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="p-6 space-y-5">
                        <div
                            class="flex items-center justify-between p-4 rounded-xl border border-slate-100 bg-slate-50/50">
                            <div>
                                <h4 class="text-sm font-semibold text-slate-700">Maintenance Mode</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Tampilkan halaman "Under Maintenance" ke
                                    pengunjung.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" name="maintenance_mode" value="1" class="sr-only peer"
                                    {{ $project->maintenance_mode ?? false ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-amber-500
                                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                    after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all
                                    peer-checked:after:translate-x-full">
                                </div>
                            </label>
                        </div>
                        <div
                            class="flex items-center justify-between p-4 rounded-xl border border-slate-100 bg-slate-50/50">
                            <div>
                                <h4 class="text-sm font-semibold text-slate-700">Force HTTPS</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Redirect semua traffic HTTP ke HTTPS secara
                                    otomatis.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" name="force_https" value="1" class="sr-only peer"
                                    {{ $project->force_https ?? true ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500
                                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                    after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all
                                    peer-checked:after:translate-x-full">
                                </div>
                            </label>
                        </div>
                        <div
                            class="flex items-center justify-between p-4 rounded-xl border border-rose-100 bg-rose-50/50">
                            <div>
                                <h4 class="text-sm font-semibold text-rose-700">DDoS Protection (Rate Limit)</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Aktifkan limitasi koneksi ketat jika website Anda sedang diserang.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" name="is_under_attack" value="1" class="sr-only peer"
                                    {{ $project->is_under_attack ?? false ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-rose-600
                                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                    after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all
                                    peer-checked:after:translate-x-full">
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-6 py-3 border-t border-slate-200 flex justify-end">
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2 px-5 rounded-lg transition-colors shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Danger Zone --}}
            <div class="bg-white rounded-2xl shadow-sm border border-rose-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-rose-100 bg-rose-50/50">
                    <h3 class="font-bold text-rose-600 flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation"></i> Danger Zone
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-slate-600 mb-5">Tindakan di bawah bersifat destruktif dan tidak dapat
                        dibatalkan.</p>
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between p-4 border border-rose-100 rounded-xl bg-rose-50/30 gap-4">
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm">Hapus Proyek</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Menghapus folder root, DNS Cloudflare, dan semua
                                record secara permanen.</p>
                        </div>
                        <form id="delete-form" action="{{ route('user_hosting.destroy', $project->hashid) }}"
                            method="POST" class="shrink-0">
                            @csrf @method('DELETE')
                            {{-- data-action menggantikan onclick --}}
                            <button type="button" data-action="confirm-delete"
                                class="w-full sm:w-auto bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold py-2.5 px-5 rounded-lg transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-trash-can"></i> Hapus Permanen
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB: DOMAINS --}}
        <div id="panel-domains" class="tab-panel hidden space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-slate-800">Custom Domains</h3>
                        <p class="text-xs text-slate-500">Tambahkan domain kustom untuk project Anda.</p>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('user_hosting.domains.store', $project->hashid) }}" method="POST" class="flex gap-4">
                        @csrf
                        <div class="flex-1">
                            <input type="text" name="domain_name" placeholder="example.com" required
                                class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg text-sm">
                            Tambah Domain
                        </button>
                    </form>

                    <div class="mt-6 border border-slate-200 rounded-xl overflow-hidden">
                        <table class="w-full text-sm text-left text-slate-500">
                            <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Nama Domain</th>
                                    <th scope="col" class="px-6 py-3">Status SSL</th>
                                    <th scope="col" class="px-6 py-3">DNS Target (CNAME/A)</th>
                                    <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($project->domains as $domain)
                                    <tr class="bg-white border-b border-slate-100 hover:bg-slate-50">
                                        <td class="px-6 py-4 font-semibold text-slate-800">{{ $domain->domain_name }}</td>
                                        <td class="px-6 py-4">
                                            @if($domain->ssl_status == 'active')
                                                <span class="text-emerald-600 bg-emerald-100 px-2 py-1 rounded text-xs font-bold">Active</span>
                                            @elseif($domain->ssl_status == 'pending')
                                                <span class="text-amber-600 bg-amber-100 px-2 py-1 rounded text-xs font-bold">Pending</span>
                                            @else
                                                <span class="text-rose-600 bg-rose-100 px-2 py-1 rounded text-xs font-bold">Failed</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 font-mono text-xs">
                                            {{ env('APP_URL') ? parse_url(env('APP_URL'), PHP_URL_HOST) : 'ryaze.my.id' }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <form action="{{ route('user_hosting.domains.destroy', $domain->hashid) }}" method="POST" onsubmit="return confirm('Hapus domain ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-bold"><i class="fa-solid fa-trash"></i> Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-slate-500">Belum ada domain kustom yang didaftarkan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB: CRON JOBS --}}
        <div id="panel-crons" class="tab-panel hidden space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-slate-800">Cron Jobs</h3>
                        <p class="text-xs text-slate-500">Jadwalkan eksekusi command background.</p>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('user_hosting.crons.store', $project->hashid) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        @csrf
                        <div class="md:col-span-1">
                            <label class="block text-xs font-medium text-slate-700 mb-1">Command</label>
                            <input type="text" name="command" placeholder="php artisan schedule:run" required
                                class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-xs font-medium text-slate-700 mb-1">Schedule (Cron Expr)</label>
                            <input type="text" name="schedule_expression" placeholder="* * * * *" required value="* * * * *"
                                class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-mono">
                        </div>
                        <div class="md:col-span-1">
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg text-sm">
                                Tambah Cron
                            </button>
                        </div>
                    </form>

                    <div class="mt-6 border border-slate-200 rounded-xl overflow-hidden">
                        <table class="w-full text-sm text-left text-slate-500">
                            <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Command</th>
                                    <th scope="col" class="px-6 py-3">Schedule</th>
                                    <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($project->crons as $cron)
                                    <tr class="bg-white border-b border-slate-100 hover:bg-slate-50">
                                        <td class="px-6 py-4 font-mono text-xs text-slate-800">{{ $cron->command }}</td>
                                        <td class="px-6 py-4 font-mono text-xs">{{ $cron->schedule_expression }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <form action="{{ route('user_hosting.crons.destroy', $cron->hashid) }}" method="POST" onsubmit="return confirm('Hapus cron job ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-bold"><i class="fa-solid fa-trash"></i> Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-slate-500">Belum ada cron job yang didaftarkan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </x-ui.page-layout>

    {{-- SweetAlert2 --}}
    <script nonce="{{ csp_nonce() }}" src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- ── SCRIPT 1: SweetAlert helpers ─────────────────────────────────────── --}}
    <script nonce="{{ csp_nonce() }}">
                function swAlert(icon, title, text = '') {
            return Swal.fire({
                icon,
                title,
                text,
                confirmButtonColor: '#4F46E5',
                customClass: {
                    popup: 'rounded-xl text-sm'
                }
            });
        }

        function swConfirm(title, text, icon = 'warning') {
            return Swal.fire({
                title,
                text,
                icon,
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, lanjutkan',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-xl text-sm'
                }
            });
        }

        function swInput(title, inputPlaceholder) {
            return Swal.fire({
                title,
                input: 'text',
                inputPlaceholder,
                showCancelButton: true,
                confirmButtonColor: '#4F46E5',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-xl text-sm'
                },
                inputValidator: (v) => {
                    if (!v) return 'Nama tidak boleh kosong!';
                }
            });
        }

        function confirmDelete() {
            Swal.fire({
                title: 'Hapus Proyek Permanen?',
                text: 'Semua file server, database, dan record DNS akan dihapus. Ini tidak bisa kembali!',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Sekarang!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-xl text-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('delete-form').submit();
            });
        }
    </script>

    {{-- ── SCRIPT 2: Helpers & Build Log polling ────────────────────────────── --}}
    <script nonce="{{ csp_nonce() }}">
        const fixUrl = u => window.location.protocol === 'https:' ?
            u.replace(/^http:\/\//i, 'https://') :
            u;

        function escapeHtml(text) {
            return String(text)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/\u001b\[0m/g, '</span>')
                .replace(/\u001b\[1m/g, '<span style="font-weight:700">')
                .replace(/\u001b\[31m/g, '<span style="color:#f87171">')
                .replace(/\u001b\[32m/g, '<span style="color:#4ade80">')
                .replace(/\u001b\[33m/g, '<span style="color:#facc15">')
                .replace(/\u001b\[34m/g, '<span style="color:#60a5fa">')
                .replace(/\u001b\[36m/g, '<span style="color:#22d3ee">')
                .replace(/\u001b\[[0-9;]*m/g, '');
        }

        const buildLogUrl = fixUrl('{{ route('user_hosting.build_logs', $project->hashid) }}');
        const buildLogText = document.getElementById('build-log-text');
        const buildLogStatus = document.getElementById('build-log-status');
        const buildLogUpdated = document.getElementById('build-log-updated');
        const websiteLogLink = document.getElementById('website-log-link');
        const buildLogPulse = document.getElementById('build-log-pulse');
        let buildLogInterval = null;

        function refreshBuildLogs() {
            fetch(buildLogUrl, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.ok ? r.json() : Promise.reject())
                .then(data => {
                    if (buildLogText && data.build_logs !== undefined)
                        buildLogText.innerHTML = escapeHtml(data.build_logs);
                    if (buildLogStatus)
                        buildLogStatus.textContent = data.status || '';
                    if (buildLogUpdated && data.last_updated)
                        buildLogUpdated.textContent = 'Updated: ' + data.last_updated;
                    if (websiteLogLink && data.website_url) {
                        websiteLogLink.href = data.website_url;
                        websiteLogLink.textContent = data.website_url.replace(/^https?:\/\//, '');
                    }
                    if (data.status !== 'building') {
                        if (buildLogPulse) buildLogPulse.style.opacity = '0';
                        if (buildLogInterval) {
                            clearInterval(buildLogInterval);
                            buildLogInterval = null;
                        }
                    }
                }).catch(() => {});
        }

        if ('{{ $project->status }}' === 'building') {
            refreshBuildLogs();
            buildLogInterval = setInterval(refreshBuildLogs, 2000);
        }
    </script>

    {{-- ── SCRIPT 3: Tab switching ──────────────────────────────────────────── --}}
    <script nonce="{{ csp_nonce() }}">
        function switchTab(name) {
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('bg-indigo-600', 'text-white', 'shadow');
                b.classList.add('text-slate-500');
            });
            document.getElementById('panel-' + name).classList.remove('hidden');
            const btn = document.getElementById('tab-' + name);
            btn.classList.add('bg-indigo-600', 'text-white', 'shadow');
            btn.classList.remove('text-slate-500');
            if (name === 'terminal') setTimeout(() => document.getElementById('terminal-input').focus(), 80);
            // Auto-load file manager saat pertama dibuka (dipindah ke sini dari listener terpisah)
            if (name === 'files' && !document.getElementById('file-manager-body').innerHTML.trim()) {
                loadFileManager();
            }
        }

        // Event listener tab — TIDAK ada onclick di HTML
        document.querySelectorAll('.tab-btn[data-tab]').forEach(btn => {
            btn.addEventListener('click', () => switchTab(btn.dataset.tab));
        });
    </script>

    {{-- ── SCRIPT 4: Terminal ───────────────────────────────────────────────── --}}
    <script nonce="{{ csp_nonce() }}">
        const termOut = document.getElementById('terminal-output');
        const termInput = document.getElementById('terminal-input');
        const termPrompt = document.getElementById('terminal-prompt');
        const cwdDisplay = document.getElementById('terminal-cwd-display');
        const termUrl = fixUrl('{{ route('user_hosting.terminal', $project->hashid) }}');
        const csrfToken = '{{ csrf_token() }}';
        const projectRoot = '/www/sites/hosting_clients/{{ str_replace('.ryaze.my.id', '', $project->ryaze_domain) }}';
        const projectSlug = '{{ str_replace('.ryaze.my.id', '', $project->ryaze_domain) }}';

        let cmdHistory = [],
            histIdx = -1,
            currentCwd = projectRoot,
            running = false;

        function getPromptLabel(cwd) {
            const rel = cwd.startsWith(projectRoot) ? (cwd.slice(projectRoot.length) || '') : cwd;
            return projectSlug + rel;
        }

        function updatePrompt(cwd) {
            currentCwd = cwd;
            const relPath = cwd.startsWith(projectRoot) ? cwd.slice(projectRoot.length) : '';
            cwdDisplay.textContent = '/' + projectSlug + relPath;
            termPrompt.innerHTML =
                `<span class="text-indigo-400">${getPromptLabel(cwd)}</span><span class="text-slate-400"> $</span>`;
        }

        function appendRaw(html) {
            termOut.insertAdjacentHTML('beforeend', html);
            termOut.scrollTop = termOut.scrollHeight;
        }

        async function runCommand() {
            if (running) return;
            const cmd = termInput.value.trim();
            if (!cmd) return;

            cmdHistory.unshift(cmd);
            if (cmdHistory.length > 100) cmdHistory.pop();
            histIdx = -1;

            appendRaw(
                `<div class="flex items-start gap-2 mb-0.5">` +
                `<span class="text-indigo-400 select-none shrink-0">${escapeHtml(getPromptLabel(currentCwd))} $</span>` +
                `<span class="text-slate-100 break-all">${escapeHtml(cmd)}</span></div>`
            );
            termInput.value = '';
            running = true;

            const lid = 'ld-' + Date.now();
            appendRaw(`<div id="${lid}" class="text-slate-600 animate-pulse">▌</div>`);

            try {
                const res = await fetch(termUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        command: cmd
                    }),
                });
                const data = await res.json();
                document.getElementById(lid)?.remove();

                if (data.cwd && data.cwd !== currentCwd) updatePrompt(data.cwd);

                if (data.error) {
                    appendRaw(`<div class="text-rose-400 mb-1">${escapeHtml(data.error)}</div>`);
                } else if (data.output && data.output.trim() !== '') {
                    const cls = data.exit_code !== 0 ? 'text-rose-300' : 'text-slate-200';
                    appendRaw(
                        `<pre class="${cls} whitespace-pre-wrap break-words mb-1 leading-relaxed">${escapeHtml(data.output)}</pre>`
                        );
                }
            } catch (err) {
                document.getElementById(lid)?.remove();
                appendRaw(`<div class="text-rose-400 mb-1">Network error: ${escapeHtml(err.message)}</div>`);
            }
            running = false;
            termInput.focus();
        }

        function clearTerminal() {
            Array.from(termOut.children)
                .filter(c => c.id !== 'terminal-welcome')
                .forEach(c => c.remove());
        }

        // Keyboard: Enter, ↑↓ history, Ctrl+L
        termInput.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                runCommand();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (histIdx < cmdHistory.length - 1) termInput.value = cmdHistory[++histIdx];
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                histIdx > 0 ?
                    (termInput.value = cmdHistory[--histIdx]) :
                    (histIdx = -1, termInput.value = '');
            } else if (e.ctrlKey && e.key === 'l') {
                e.preventDefault();
                clearTerminal();
            }
        });

        // Event listeners terminal — menggantikan semua onclick inline
        document.querySelectorAll('[data-action="clear-terminal"]').forEach(el => {
            el.addEventListener('click', clearTerminal);
        });
        document.getElementById('terminal-output').addEventListener('click', () => {
            document.getElementById('terminal-input').focus();
        });
        document.querySelector('[data-action="run-command"]')
            ?.addEventListener('click', runCommand);
    </script>

    {{-- ── SCRIPT 5: File Manager ───────────────────────────────────────────── --}}
    <script nonce="{{ csp_nonce() }}">
        let currentFolderPath = '';
        let currentEditingFile = '';

        const fileManagerUrl = fixUrl('{{ route('user_hosting.files', $project->hashid) }}');
        const fileReadUrl = fixUrl('{{ route('user_hosting.files.read', $project->hashid) }}');
        const fileSaveUrl = fixUrl('{{ route('user_hosting.files.save', $project->hashid) }}');
        const fileUploadUrl = fixUrl('{{ route('user_hosting.files.upload', $project->hashid) }}');
        const fileCreateUrl = fixUrl('{{ route('user_hosting.files.create', $project->hashid) }}');
        const fileDeleteUrl = fixUrl('{{ route('user_hosting.files.delete', $project->hashid) }}');
        const fileDownloadUrl = fixUrl('{{ route('user_hosting.files.download', $project->hashid) }}');

        const PROTECTED_FILES = ['.suspended', '.htaccess', '.user.ini', '.maintenance'];
        const isProtected = name => PROTECTED_FILES.includes(name);

        // ── Load direktori ─────────────────────────────────────────────────────
        function loadFileManager(path = '') {
            const loader = document.getElementById('file-manager-loader');
            const tbody = document.getElementById('file-manager-body');
            const pathEl = document.getElementById('current-path-display');

            loader.classList.remove('hidden');

            fetch(`${fileManagerUrl}?path=${encodeURIComponent(path)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.error) {
                        hotToast(data.error, 'error');
                        loader.classList.add('hidden');
                        return;
                    }

                    currentFolderPath = data.current_path;
                    pathEl.textContent = currentFolderPath || '(root)';
                    tbody.innerHTML = '';

                    if (!data.items.length) {
                        tbody.innerHTML = `<tr><td colspan="4" class="px-6 py-10 text-center text-slate-400">
                            <i class="fa-regular fa-folder-open text-3xl mb-2 opacity-50 block"></i>Folder kosong
                        </td></tr>`;
                    } else {
                        data.items.forEach(item => {
                            const isDir = item.type === 'dir';
                            const locked = !isDir && isProtected(item.name);

                            const icon = isDir ?
                                '<i class="fa-solid fa-folder text-amber-400 text-lg"></i>' :
                                locked ?
                                '<i class="fa-solid fa-lock text-slate-300 text-lg" title="File sistem"></i>' :
                                '<i class="fa-regular fa-file-lines text-slate-400 text-lg"></i>';

                            // Tombol aksi — TIDAK ada onclick, pakai class + data-op
                            let actions = '';
                            if (locked) {
                                actions =
                                    `<span class="text-xs text-slate-300 italic select-none px-1">sistem</span>`;
                            } else if (isDir) {
                                actions = `
                                    <button class="file-action text-rose-400 hover:text-rose-600 px-1 transition-colors" data-op="delete" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>`;
                            } else {
                                actions = `
                                    <button class="file-action text-sky-400 hover:text-sky-600 px-1 transition-colors" data-op="edit" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <a href="${fileDownloadUrl}?path=${encodeURIComponent(item.path)}" target="_blank"
                                        class="text-emerald-400 hover:text-emerald-600 px-1 transition-colors" title="Download">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                    <button class="file-action text-rose-400 hover:text-rose-600 px-1 transition-colors" data-op="delete" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>`;
                            }

                            const tr = document.createElement('tr');
                            tr.className =
                                `hover:bg-slate-50 transition-colors group ${locked ? 'opacity-60' : ''}`;
                            // Simpan data di dataset — dipakai event delegation
                            tr.dataset.path = item.path;
                            tr.dataset.name = item.name;
                            tr.dataset.type = item.type;
                            tr.dataset.locked = locked ? '1' : '0';

                            tr.innerHTML = `
                                <td class="px-6 py-2.5 truncate max-w-0">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span class="shrink-0">${icon}</span>
                                        <a href="#" class="file-name-link font-semibold
                                            ${locked
                                                ? 'text-slate-400 cursor-not-allowed'
                                                : 'text-slate-600 hover:text-indigo-600 cursor-pointer'} truncate">
                                            ${item.name}
                                        </a>
                                        ${locked ? '<span class="text-[10px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded font-mono shrink-0">protected</span>' : ''}
                                    </div>
                                </td>
                                <td class="px-4 py-2.5 text-slate-400 text-xs whitespace-nowrap">${item.size}</td>
                                <td class="px-4 py-2.5 text-slate-400 text-xs hidden sm:table-cell whitespace-nowrap">${item.modified}</td>
                                <td class="px-4 py-2.5 text-right whitespace-nowrap">${actions}</td>`;
                            tbody.appendChild(tr);
                        });
                    }

                    loader.classList.add('hidden');
                })
                .catch(() => {
                    hotToast('Gagal memuat file browser.', 'error');
                    loader.classList.add('hidden');
                });
        }

        // ── Event delegation tbody — satu listener untuk semua baris dinamis ──
        document.getElementById('file-manager-body').addEventListener('click', e => {
            const tr = e.target.closest('tr[data-path]');
            if (!tr) return;

            const {
                path,
                name,
                type,
                locked
            } = tr.dataset;
            const isDir = type === 'dir';
            const isLocked = locked === '1';

            // Klik nama file/folder
            if (e.target.closest('.file-name-link')) {
                e.preventDefault();
                if (isLocked) swAlert('warning', 'File Terlindungi', 'File sistem ini tidak dapat diubah.');
                else if (isDir) loadFileManager(path);
                else openFileEditor(path, name);
                return;
            }

            // Klik tombol aksi (edit / delete)
            const btn = e.target.closest('.file-action[data-op]');
            if (!btn) return;

            if (btn.dataset.op === 'edit') openFileEditor(path, name);
            if (btn.dataset.op === 'delete') deleteItem(path, name);
        });

        // ── Navigasi naik ──────────────────────────────────────────────────────
        function navigateUp() {
            if (!currentFolderPath) return;
            loadFileManager(currentFolderPath.split('/').slice(0, -1).join('/'));
        }

        // ── Buat file / folder baru ────────────────────────────────────────────
        async function promptCreateItem(type) {
            const label = type === 'dir' ? 'Folder' : 'File';
            const {
                value: name
            } = await swInput(`Buat ${label} Baru`, `Nama ${label}...`);
            if (!name) return;

            if (isProtected(name)) {
                swAlert('error', 'Nama Tidak Diizinkan',
                'Nama file tersebut adalah file sistem dan tidak bisa dibuat.');
                return;
            }

            fetch(fileCreateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    type,
                    name,
                    current_path: currentFolderPath
                })
            }).then(r => r.json()).then(data => {
                if (data.error) swAlert('error', 'Gagal', data.error);
                else {
                    hotToast(`${label} berhasil dibuat!`, 'success');
                    loadFileManager(currentFolderPath);
                }
            });
        }

        // ── Hapus file / folder ────────────────────────────────────────────────
        async function deleteItem(path, name) {
            if (isProtected(name)) {
                swAlert('warning', 'File Terlindungi', 'File sistem ini tidak dapat dihapus.');
                return;
            }
            const result = await swConfirm('Hapus permanen?', `"${name}" akan dihapus dan tidak bisa dikembalikan.`);
            if (!result.isConfirmed) return;

            fetch(fileDeleteUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    path
                })
            }).then(r => r.json()).then(data => {
                if (data.error) swAlert('error', 'Gagal', data.error);
                else {
                    hotToast('Berhasil dihapus!', 'success');
                    loadFileManager(currentFolderPath);
                }
            });
        }

        // ── Upload file ────────────────────────────────────────────────────────
        function uploadFile(inputEl) {
            if (!inputEl.files.length) return;
            const fileName = inputEl.files[0].name;
            if (isProtected(fileName)) {
                swAlert('error', 'Upload Ditolak', 'Nama file tersebut adalah file sistem dan tidak bisa diupload.');
                inputEl.value = '';
                return;
            }

            const formData = new FormData();
            formData.append('file', inputEl.files[0]);
            formData.append('current_path', currentFolderPath);
            document.getElementById('file-manager-loader').classList.remove('hidden');

            fetch(fileUploadUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            }).then(r => r.json()).then(data => {
                inputEl.value = '';
                if (data.error) swAlert('error', 'Upload gagal', data.error);
                else {
                    hotToast('File berhasil diupload!', 'success');
                    loadFileManager(currentFolderPath);
                }
            }).catch(() => {
                document.getElementById('file-manager-loader').classList.add('hidden');
                swAlert('error', 'Upload gagal');
            });
        }

        // ── Buka editor ────────────────────────────────────────────────────────
        function openFileEditor(path, filename) {
            if (isProtected(filename)) {
                swAlert('warning', 'File Terlindungi', 'File sistem ini tidak dapat diubah.');
                return;
            }

            const modal = document.getElementById('file-editor-modal');
            const loader = document.getElementById('editor-loader');
            const textarea = document.getElementById('file-editor-textarea');
            currentEditingFile = path;
            document.getElementById('editor-filename').textContent = filename;
            modal.classList.remove('hidden');
            loader.classList.remove('hidden');
            textarea.value = '';

            fetch(`${fileReadUrl}?path=${encodeURIComponent(path)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.error) {
                        swAlert('error', 'Gagal baca file', data.error);
                        closeFileEditor();
                    } else textarea.value = data.content;
                    loader.classList.add('hidden');
                })
                .catch(() => {
                    swAlert('error', 'Gagal membaca file.');
                    closeFileEditor();
                });
        }

        function closeFileEditor() {
            document.getElementById('file-editor-modal').classList.add('hidden');
            currentEditingFile = '';
        }

        // ── Simpan file ────────────────────────────────────────────────────────
        function saveFileEditor() {
            if (isProtected(currentEditingFile.split('/').pop())) {
                swAlert('warning', 'File Terlindungi', 'File sistem ini tidak dapat disimpan.');
                return;
            }

            const loader = document.getElementById('editor-loader');
            const content = document.getElementById('file-editor-textarea').value;
            loader.classList.remove('hidden');

            fetch(fileSaveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    path: currentEditingFile,
                    content
                })
            }).then(async r => {
                const text = await r.text();
                try {
                    const data = JSON.parse(text);
                    loader.classList.add('hidden');
                    if (data.error) swAlert('error', 'Gagal simpan', data.error);
                    else hotToast('File berhasil disimpan!', 'success');
                } catch (e) {
                    loader.classList.add('hidden');
                    swAlert('error', 'Response Error', 'Status: ' + r.status + ' Text: ' + text.substring(0, 100));
                }
            }).catch((err) => {
                loader.classList.add('hidden');
                swAlert('error', 'Network/Fetch Error', err.message);
            });
        }

        // ── Event listeners toolbar file manager — menggantikan onclick inline ──
        document.querySelector('[data-action="navigate-up"]')
            ?.addEventListener('click', navigateUp);

        document.querySelector('[data-action="new-file"]')
            ?.addEventListener('click', () => promptCreateItem('file'));

        document.querySelector('[data-action="new-dir"]')
            ?.addEventListener('click', () => promptCreateItem('dir'));

        document.querySelector('[data-action="refresh-files"]')
            ?.addEventListener('click', () => loadFileManager(currentFolderPath));

        document.querySelector('[data-action="upload-file"]')
            ?.addEventListener('change', function() {
                uploadFile(this);
            });

        document.querySelector('[data-action="close-editor"]')
            ?.addEventListener('click', closeFileEditor);

        document.querySelector('[data-action="save-editor"]')
            ?.addEventListener('click', saveFileEditor);

        document.querySelector('[data-action="confirm-delete"]')
            ?.addEventListener('click', confirmDelete);
    </script>

    <style nonce="{{ csp_nonce() }}">
        .scrollbar-hide::-webkit-scrollbar {
            display: none
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none
        }
    </style>
@endsection
