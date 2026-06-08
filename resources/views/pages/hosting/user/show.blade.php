@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50 relative">

        {{-- Alerts --}}
        @if (session('success'))
            <div
                class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center gap-3 shadow-sm animate-fade-in-down">
                <i class="fa-solid fa-circle-check text-xl"></i>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        @elseif (session('error'))
            <div
                class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg flex items-center gap-3 shadow-sm animate-fade-in-down">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                <span class="font-medium text-sm">{{ session('error') }}</span>
            </div>
        @endif

        {{-- ── 9. USER HOSTING – Detail Project (Show) ────────────────────── --}}
        <div
            class="p-5 bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                {{-- Icon Framework --}}
                <div
                    class="shrink-0 w-12 h-12 border border-slate-200 rounded-xl flex items-center justify-center bg-slate-50">
                    @php
                        $fwIcon = match ($project->framework) {
                            'react' => 'fa-brands fa-react text-sky-500',
                            'nextjs' => 'fa-brands fa-node-js text-slate-800',
                            'laravel' => 'fa-brands fa-laravel text-red-500',
                            'python' => 'fa-brands fa-python text-yellow-500',
                            'node' => 'fa-brands fa-node text-emerald-500',
                            'vue' => 'fa-brands fa-vuejs text-emerald-500',
                            default => 'fa-brands fa-html5 text-orange-500',
                        };
                    @endphp
                    <i class="{{ $fwIcon }} text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">{{ $project->project_name }}</h1>
                    <a href="https://{{ $project->ryaze_domain }}" target="_blank"
                        class="text-sm font-medium text-indigo-600 hover:underline flex items-center gap-1 mt-0.5">
                        {{ $project->ryaze_domain }}
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    </a>
                </div>
            </div>
            {{-- Badge Status --}}
            @php
                $statusClass = match ($project->status) {
                    'active' => 'bg-emerald-100 text-emerald-700',
                    'building' => 'bg-amber-100 text-amber-700 animate-pulse',
                    default => 'bg-rose-100 text-rose-700',
                };
                $statusIcon = match ($project->status) {
                    'active' => 'fa-circle-check',
                    'building' => 'fa-spinner fa-spin',
                    default => 'fa-triangle-exclamation',
                };
            @endphp
            <span
                class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide {{ $statusClass }}">
                <i class="fa-solid {{ $statusIcon }}"></i>
                {{ $project->status }}
            </span>
        </div>

        {{-- Tab Navigation --}}
        <div class="flex flex-wrap gap-2 mb-6 mt-6 bg-white border border-slate-200 rounded-xl p-1.5 shadow-sm w-full">
            <button onclick="switchTab('overview')" id="tab-overview"
                class="tab-btn flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all bg-indigo-600 text-white shadow">
                <i class="fa-solid fa-chart-simple"></i> <span>Overview</span>
            </button>
            <button onclick="switchTab('logs')" id="tab-logs"
                class="tab-btn flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all text-slate-500 hover:text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-scroll"></i> <span>Build Logs</span>
            </button>
            <button onclick="switchTab('terminal')" id="tab-terminal"
                class="tab-btn flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all text-slate-500 hover:text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-terminal"></i> <span>Terminal</span>
            </button>
            <button onclick="switchTab('files')" id="tab-files"
                class="tab-btn flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all text-slate-500 hover:text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-folder-tree"></i> <span>Root Files</span>
            </button>
            <button onclick="switchTab('env')" id="tab-env"
                class="tab-btn flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all text-slate-500 hover:text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-key"></i> <span>.env</span>
            </button>
            <button onclick="switchTab('settings')" id="tab-settings"
                class="tab-btn flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all text-slate-500 hover:text-slate-700 hover:bg-slate-50"><i
                    class="fa-solid fa-gears"></i> <span>Settings</span></button>
        </div>

        {{-- TAB: OVERVIEW --}}
        <div id="panel-overview" class="tab-panel">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    @if ($project->status == 'active')
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
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
                    @else
                        <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
                            <i class="fa-solid fa-satellite-dish text-slate-300 text-5xl mb-4"></i>
                            <p class="text-slate-500 font-medium">Preview tersedia setelah deployment selesai.</p>
                        </div>
                    @endif
                </div>
                <div class="space-y-4">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
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
                                    /www/sites/hosting_clients/{{ str_replace('.ryaze.my.id', '', $project->ryaze_domain) }}
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
                            id="build-log-updated">{{ $project->deployments->first()->created_at?->diffForHumans() ?? 'Initial Build' }}</span>
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
                        <div class="w-3 h-3 rounded-full bg-rose-500 hover:bg-rose-400 cursor-pointer transition-colors"
                            onclick="clearTerminal()" title="Clear"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                    </div>
                    <div class="flex items-center gap-2 ml-2 min-w-0">
                        <i class="fa-solid fa-terminal text-slate-400 text-xs shrink-0"></i>
                        <span class="text-slate-300 text-xs font-mono font-semibold shrink-0">bash</span>
                        <span class="text-slate-600 text-xs shrink-0">—</span>
                        <span class="text-slate-400 text-xs font-mono truncate" id="terminal-cwd-display">
                            /www/sites/hosting_clients/{{ str_replace('.ryaze.my.id', '', $project->ryaze_domain) }}
                        </span>
                    </div>
                    <div class="ml-auto shrink-0">
                        <button onclick="clearTerminal()"
                            class="text-slate-500 hover:text-slate-300 text-xs transition-colors px-2 py-1 rounded hover:bg-slate-700">
                            <i class="fa-solid fa-trash-can mr-1"></i><span class="hidden sm:inline">Clear</span>
                        </button>
                    </div>
                </div>
                <div id="terminal-output"
                    class="px-4 pt-4 pb-2 font-mono text-sm text-slate-200 overflow-y-auto leading-relaxed cursor-text"
                    style="height:420px;background:#0f1117;" onclick="document.getElementById('terminal-input').focus()">
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
                    <button onclick="runCommand()"
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
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden relative">
                <div
                    class="px-4 py-3 border-b border-slate-100 bg-slate-50 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2 min-w-0">
                        <button onclick="navigateUp()"
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
                        <button onclick="promptCreateItem('file')"
                            class="text-xs bg-white border border-slate-200 text-slate-600 px-3 py-1.5 rounded hover:bg-slate-50 transition-colors">
                            <i class="fa-solid fa-file-circle-plus text-emerald-500 mr-1"></i><span
                                class="hidden sm:inline">New File</span>
                        </button>
                        <button onclick="promptCreateItem('dir')"
                            class="text-xs bg-white border border-slate-200 text-slate-600 px-3 py-1.5 rounded hover:bg-slate-50 transition-colors">
                            <i class="fa-solid fa-folder-plus text-amber-500 mr-1"></i><span class="hidden sm:inline">New
                                Folder</span>
                        </button>
                        <label
                            class="text-xs bg-indigo-600 text-white px-3 py-1.5 rounded hover:bg-indigo-700 transition-colors cursor-pointer">
                            <i class="fa-solid fa-cloud-arrow-up mr-1"></i><span class="hidden sm:inline">Upload</span>
                            <input type="file" id="upload-input" class="hidden" onchange="uploadFile(this)">
                        </label>
                        <button onclick="loadFileManager(currentFolderPath)"
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
                <div id="file-editor-modal" class="hidden absolute inset-0 bg-slate-900 z-30 flex flex-col">
                    <div
                        class="px-4 py-3 border-b border-slate-700 bg-slate-800 flex justify-between items-center text-white">
                        <div class="font-mono text-sm flex items-center gap-2 min-w-0">
                            <i class="fa-solid fa-file-code text-indigo-400 shrink-0"></i>
                            <span id="editor-filename" class="truncate">filename.php</span>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <button onclick="closeFileEditor()"
                                class="px-3 py-1.5 bg-slate-700 hover:bg-slate-600 rounded text-xs transition font-semibold">Batal</button>
                            <button onclick="saveFileEditor()"
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
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-3xl">
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

        {{-- TAB: SETTINGS (KOMPLEKS) --}}
        <div id="panel-settings" class="tab-panel hidden space-y-6">

            {{-- Konfigurasi Aplikasi Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="font-bold text-slate-800">Konfigurasi Aplikasi</h3>
                    <p class="text-xs text-slate-500">Atur parameter dasar aplikasi dan environment Anda.</p>
                </div>

                <form action="{{ route('user_hosting.settings.update', $project->hashid) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="p-6 space-y-6">
                        {{-- Versi PHP --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Versi PHP</label>
                            <select name="php_version" class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5 outline-none transition-colors">
                                <option value="8.1" {{ ($project->php_version ?? '') == '8.1' ? 'selected' : '' }}>PHP 8.1</option>
                                <option value="8.2" {{ ($project->php_version ?? '') == '8.2' ? 'selected' : '' }}>PHP 8.2</option>
                                <option value="8.3" {{ ($project->php_version ?? '8.3') == '8.3' ? 'selected' : '' }}>PHP 8.3 (Recommended)</option>
                                <option value="8.4" {{ ($project->php_version ?? '') == '8.4' ? 'selected' : '' }}>PHP 8.4</option>
                            </select>
                            <p class="text-xs text-slate-500 mt-1.5">Pilih versi PHP yang sesuai dengan requirement <code class="bg-slate-100 px-1 py-0.5 rounded">composer.json</code> Anda.</p>
                        </div>

                        <hr class="border-slate-100">

                        {{-- Toggles --}}
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-semibold text-slate-700">Maintenance Mode</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Tampilkan halaman "Under Maintenance" ke pengunjung.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="maintenance_mode" value="1" class="sr-only peer" {{ ($project->maintenance_mode ?? false) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                            </label>
                        </div>

                        <hr class="border-slate-100">

                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-semibold text-slate-700">Force HTTPS</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Otomatis redirect semua traffic HTTP ke HTTPS.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="force_https" value="1" class="sr-only peer" {{ ($project->force_https ?? true) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            </label>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-6 py-3 border-t border-slate-200 flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition-colors shadow-sm">Simpan Perubahan</button>
                    </div>
                </form>
            </div>

            {{-- Danger Zone Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-rose-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-rose-100 bg-rose-50/50">
                    <h3 class="font-bold text-rose-600 flex items-center gap-2"><i class="fa-solid fa-triangle-exclamation"></i> Danger Zone</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-slate-600 mb-5">Tindakan di bawah ini bersifat destruktif dan tidak dapat dibatalkan. Pastikan Anda sudah mem-backup data penting sebelum melakukan penghapusan.</p>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 border border-rose-100 rounded-lg bg-rose-50/30 gap-4">
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm">Hapus Proyek</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Menghapus folder root, database, dan memutus DNS Cloudflare secara permanen.</p>
                        </div>
                        <form id="delete-form" action="{{ route('user_hosting.destroy', $project->hashid) }}" method="POST" class="shrink-0">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDelete()" class="w-full sm:w-auto bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold py-2.5 px-5 rounded-lg transition-all shadow-sm shadow-rose-200 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-trash-can"></i> Hapus Permanen
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ── SweetAlert2 helpers ────────────────────────────────────────────────
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (t) => {
                t.addEventListener('mouseenter', Swal.stopTimer);
                t.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

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

        function confirmDelete() {
            Swal.fire({
                title: 'Hapus Proyek Permanen?',
                text: "Semua file server, database, dan record DNS akan dihapus. Ini tidak bisa kembali!",
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
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            })
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
    </script>

    <script>
        // Helper URL untuk memaksa HTTPS jika halaman diakses via HTTPS
        const fixUrl = u => window.location.protocol === 'https:' ? u.replace(/^http:\/\//i, 'https://') : u;

        // ── Build log polling ──────────────────────────────────────────────────
        const buildLogUrl = fixUrl('{{ route('user_hosting.build_logs', $project->hashid) }}');
        const buildLogText = document.getElementById('build-log-text');
        const buildLogStatus = document.getElementById('build-log-status');
        const buildLogUpdated = document.getElementById('build-log-updated');
        const websiteLogLink = document.getElementById('website-log-link');
        const buildLogPulse = document.getElementById('build-log-pulse');
        let buildLogInterval = null;

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

        function refreshBuildLogs() {
            fetch(buildLogUrl, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.ok ? r.json() : Promise.reject())
                .then(data => {
                    if (buildLogText && data.build_logs !== undefined) buildLogText.innerHTML = escapeHtml(data
                        .build_logs);
                    if (buildLogStatus) buildLogStatus.textContent = data.status || '';
                    if (buildLogUpdated && data.last_updated) buildLogUpdated.textContent = 'Updated: ' + data
                        .last_updated;
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

    <script>
        // ── Tab switching ──────────────────────────────────────────────────────
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
        }
    </script>

    <script>
        // ── Terminal ───────────────────────────────────────────────────────────
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
            cwdDisplay.textContent = cwd;
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
            Array.from(termOut.children).filter(c => c.id !== 'terminal-welcome').forEach(c => c.remove());
        }

        termInput.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                runCommand();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (histIdx < cmdHistory.length - 1) termInput.value = cmdHistory[++histIdx];
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                histIdx > 0 ? (termInput.value = cmdHistory[--histIdx]) : (histIdx = -1, termInput.value = '');
            } else if (e.ctrlKey && e.key === 'l') {
                e.preventDefault();
                clearTerminal();
            }
        });
    </script>

    <script>
        // ── File Manager ───────────────────────────────────────────────────────
        let currentFolderPath = '';
        let currentEditingFile = '';

        const fileManagerUrl = fixUrl('{{ route('user_hosting.files', $project->hashid) }}');
        const fileReadUrl = fixUrl('{{ route('user_hosting.files.read', $project->hashid) }}');
        const fileSaveUrl = fixUrl('{{ route('user_hosting.files.save', $project->hashid) }}');
        const fileUploadUrl = fixUrl('{{ route('user_hosting.files.upload', $project->hashid) }}');
        const fileCreateUrl = fixUrl('{{ route('user_hosting.files.create', $project->hashid) }}');
        const fileDeleteUrl = fixUrl('{{ route('user_hosting.files.delete', $project->hashid) }}');
        const fileDownloadUrl = fixUrl('{{ route('user_hosting.files.download', $project->hashid) }}');

        // File yang tidak boleh disentuh klien sama sekali
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
                        Toast.fire({
                            icon: 'error',
                            title: data.error
                        });
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

                            // Klik nama: folder → navigasi, file biasa → editor, file terkunci → tidak bisa
                            const nameAction = isDir ?
                                `onclick="loadFileManager('${item.path}')"` :
                                locked ?
                                `onclick="swAlert('warning','File Terlindungi','File sistem ini tidak dapat diubah.')"` :
                                `onclick="openFileEditor('${item.path}','${item.name}')"`;

                            // Tombol aksi
                            let actions = '';
                            if (locked) {
                                // File sistem: tidak ada tombol apapun, hanya label
                                actions =
                                    `<span class="text-xs text-slate-300 italic select-none px-1">sistem</span>`;
                            } else if (isDir) {
                                // Folder: hanya hapus
                                actions = `
                                    <button onclick="deleteItem('${item.path}', '${item.name}')"
                                        class="text-rose-400 hover:text-rose-600 px-1 transition-colors" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>`;
                            } else {
                                // File biasa: edit + download + hapus
                                actions = `
                                    <button onclick="openFileEditor('${item.path}','${item.name}')"
                                        class="text-sky-400 hover:text-sky-600 px-1 transition-colors" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <a href="${fileDownloadUrl}?path=${encodeURIComponent(item.path)}" target="_blank"
                                        class="text-emerald-400 hover:text-emerald-600 px-1 transition-colors" title="Download">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                    <button onclick="deleteItem('${item.path}', '${item.name}')"
                                        class="text-rose-400 hover:text-rose-600 px-1 transition-colors" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>`;
                            }

                            const tr = document.createElement('tr');
                            tr.className =
                                `hover:bg-slate-50 transition-colors group ${locked ? 'opacity-60' : ''}`;
                            tr.innerHTML = `
                                <td class="px-6 py-2.5 truncate max-w-0">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span class="shrink-0">${icon}</span>
                                        <a href="javascript:void(0)" ${nameAction}
                                            class="font-semibold ${locked ? 'text-slate-400 cursor-not-allowed' : 'text-slate-600 hover:text-indigo-600 cursor-pointer'} truncate">
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
                    Toast.fire({
                        icon: 'error',
                        title: 'Gagal memuat file browser.'
                    });
                    loader.classList.add('hidden');
                });
        }

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

            // Cegah klien buat file dengan nama protected
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
                    Toast.fire({
                        icon: 'success',
                        title: `${label} berhasil dibuat!`
                    });
                    loadFileManager(currentFolderPath);
                }
            });
        }

        // ── Hapus file / folder ────────────────────────────────────────────────
        async function deleteItem(path, name) {
            // Guard frontend — double-check nama file
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
                    Toast.fire({
                        icon: 'success',
                        title: 'Berhasil dihapus!'
                    });
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
                })
                .then(r => r.json())
                .then(data => {
                    inputEl.value = '';
                    if (data.error) swAlert('error', 'Upload gagal', data.error);
                    else {
                        Toast.fire({
                            icon: 'success',
                            title: 'File berhasil diupload!'
                        });
                        loadFileManager(currentFolderPath);
                    }
                })
                .catch(() => {
                    document.getElementById('file-manager-loader').classList.add('hidden');
                    swAlert('error', 'Upload gagal');
                });
        }

        // ── Buka editor ────────────────────────────────────────────────────────
        function openFileEditor(path, filename) {
            // Guard frontend
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
                    } else {
                        textarea.value = data.content;
                    }
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
            // Guard frontend — cek nama file yang sedang diedit
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
            }).then(r => r.json()).then(data => {
                loader.classList.add('hidden');
                if (data.error) swAlert('error', 'Gagal simpan', data.error);
                else Toast.fire({
                    icon: 'success',
                    title: 'File berhasil disimpan!'
                });
            }).catch(() => {
                loader.classList.add('hidden');
                swAlert('error', 'Terjadi kesalahan saat menyimpan.');
            });
        }

        // ── Auto-load saat tab files dibuka pertama kali ───────────────────────
        document.getElementById('tab-files').addEventListener('click', () => {
            if (!document.getElementById('file-manager-body').innerHTML.trim()) loadFileManager();
        });
    </script>

    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none
        }
    </style>
@endsection
