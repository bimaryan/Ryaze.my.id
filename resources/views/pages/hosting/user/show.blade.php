@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50 relative">

        @if (session('success'))
            <div
                class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center gap-3 shadow-sm animate-fade-in-down">
                <i class="fa-solid fa-circle-check text-xl"></i>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Header --}}
        <div
            class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 border border-slate-200 rounded-xl flex items-center justify-center bg-slate-50">
                    @if ($project->framework == 'react')
                        <i class="fa-brands fa-react text-3xl text-sky-500"></i>
                    @elseif($project->framework == 'nextjs')
                        <i class="fa-brands fa-node-js text-3xl text-slate-800"></i>
                    @elseif($project->framework == 'laravel')
                        <i class="fa-brands fa-laravel text-3xl text-red-500"></i>
                    @elseif($project->framework == 'python')
                        <i class="fa-brands fa-python text-3xl text-yellow-500"></i>
                    @elseif($project->framework == 'node')
                        <i class="fa-brands fa-node text-3xl text-emerald-500"></i>
                    @else
                        <i class="fa-brands fa-html5 text-3xl text-orange-500"></i>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">{{ $project->project_name }}</h1>
                    <a href="https://{{ $project->ryaze_domain }}" target="_blank"
                        class="text-sm font-medium text-indigo-600 hover:underline flex items-center gap-1 mt-0.5">
                        {{ $project->ryaze_domain }} <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    </a>
                </div>
            </div>
            <div class="text-right">
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide
                    {{ $project->status == 'active' ? 'bg-emerald-100 text-emerald-700' : ($project->status == 'building' ? 'bg-amber-100 text-amber-700 animate-pulse' : 'bg-rose-100 text-rose-700') }}">
                    <i
                        class="fa-solid {{ $project->status == 'active' ? 'fa-check-circle' : ($project->status == 'building' ? 'fa-spinner fa-spin' : 'fa-triangle-exclamation') }} mr-1.5"></i>
                    {{ $project->status }}
                </span>
            </div>
        </div>

        {{-- Tab Navigation --}}
        <div class="flex gap-1 mb-6 bg-white border border-slate-200 rounded-xl p-1.5 w-fit shadow-sm">
            <button onclick="switchTab('overview')" id="tab-overview"
                class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold transition-all bg-indigo-600 text-white shadow">
                <i class="fa-solid fa-chart-simple mr-1.5"></i> Overview
            </button>
            <button onclick="switchTab('logs')" id="tab-logs"
                class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold transition-all text-slate-500 hover:text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-scroll mr-1.5"></i> Build Logs
            </button>
            <button onclick="switchTab('terminal')" id="tab-terminal"
                class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold transition-all text-slate-500 hover:text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-terminal mr-1.5"></i> Terminal
            </button>
            <button onclick="switchTab('env')" id="tab-env"
                class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold transition-all text-slate-500 hover:text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-key mr-1.5"></i> .env
            </button>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        {{-- TAB: OVERVIEW --}}
        {{-- ═══════════════════════════════════════════════════════════════════ --}}
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
                                <span class="font-mono text-xs text-slate-600 bg-slate-50 border border-slate-200 px-2 py-1 rounded block truncate">
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

        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        {{-- TAB: BUILD LOGS --}}
        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        <div id="panel-logs" class="tab-panel hidden">
            <div class="bg-slate-900 rounded-xl shadow-md border border-slate-800 overflow-hidden">
                <div class="bg-slate-800 px-4 py-3 flex items-center gap-2 border-b border-slate-700">
                    <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                    <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                    <span class="text-slate-400 text-xs font-mono ml-2">Build Log —
                        {{ $project->deployments->first()->created_at?->diffForHumans() ?? 'Initial Build' }}</span>
                </div>
                <div class="p-4 h-[500px] overflow-y-auto font-mono text-sm" id="build-log-container">
                    @if ($project->deployments->count() > 0)
                        <pre class="text-emerald-400 whitespace-pre-wrap leading-relaxed">{{ $project->deployments->first()->build_logs }}</pre>
                        @if ($project->status == 'building')
                            <div class="mt-2 flex items-center text-slate-400 animate-pulse">
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

        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        {{-- TAB: TERMINAL --}}
        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        <div id="panel-terminal" class="tab-panel hidden">
            <div class="bg-slate-900 rounded-xl shadow-xl border border-slate-700 overflow-hidden">
                {{-- Title bar --}}
                <div class="bg-slate-800 px-4 py-3 flex items-center gap-3 border-b border-slate-700 select-none">
                    <div class="flex gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-rose-500 hover:bg-rose-400 cursor-pointer transition-colors"
                            onclick="clearTerminal()" title="Clear"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                    </div>
                    <div class="flex items-center gap-2 ml-2">
                        <i class="fa-solid fa-terminal text-slate-400 text-xs"></i>
                        <span class="text-slate-300 text-xs font-mono font-semibold">bash</span>
                        <span class="text-slate-600 text-xs">—</span>
                        <span class="text-slate-500 text-xs font-mono" id="terminal-cwd">
                            /www/sites/hosting_clients/{{ str_replace('.ryaze.my.id', '', $project->ryaze_domain) }}
                        </span>
                    </div>
                    <div class="ml-auto flex items-center gap-2">
                        <button onclick="clearTerminal()"
                            class="text-slate-500 hover:text-slate-300 text-xs transition-colors px-2 py-1 rounded hover:bg-slate-700">
                            <i class="fa-solid fa-trash-can mr-1"></i> Clear
                        </button>
                    </div>
                </div>

                {{-- Terminal output --}}
                <div id="terminal-output"
                    class="px-4 pt-4 pb-2 font-mono text-sm text-slate-200 overflow-y-auto leading-relaxed"
                    style="height: 420px; background: #0f1117;"
                    onclick="document.getElementById('terminal-input').focus()">

                    {{-- Welcome message --}}
                    <div class="text-slate-500 mb-3 select-none border-b border-slate-800 pb-3">
                        <span class="text-emerald-500 font-bold">ryaze</span><span class="text-slate-400"> hosting terminal</span>
                        <br>
                        <span class="text-slate-600 text-xs">Project: <span class="text-slate-400">{{ $project->project_name }}</span> · Type a command to get started.</span>
                    </div>
                </div>

                {{-- Input bar --}}
                <div class="flex items-center gap-0 bg-[#0f1117] border-t border-slate-800 px-4 py-3">
                    <span class="text-emerald-400 font-mono text-sm font-bold select-none shrink-0">
                        <span class="text-indigo-400">~</span>
                        <span class="text-slate-500">/</span>{{ str_replace('.ryaze.my.id', '', $project->ryaze_domain) }}
                        <span class="text-slate-400 ml-1">$</span>
                    </span>
                    <input
                        type="text"
                        id="terminal-input"
                        autocomplete="off"
                        autocorrect="off"
                        autocapitalize="off"
                        spellcheck="false"
                        placeholder="ketik perintah..."
                        class="flex-1 bg-transparent text-slate-100 font-mono text-sm ml-2 outline-none placeholder-slate-700 caret-emerald-400"
                    >
                    <button onclick="runCommand()"
                        class="ml-3 text-slate-500 hover:text-emerald-400 transition-colors shrink-0">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                    </button>
                </div>
            </div>

            <p class="text-xs text-slate-400 mt-3 flex items-center gap-1.5">
                <i class="fa-solid fa-circle-info text-slate-500"></i>
                Terminal dijalankan langsung di server dalam folder project. Gunakan dengan bijak.
            </p>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        {{-- TAB: ENV --}}
        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        <div id="panel-env" class="tab-panel hidden">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-3xl">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-key text-amber-500"></i> Environment Variables
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Format: <code class="bg-slate-100 px-1 py-0.5 rounded text-rose-500">KUNCI=nilai</code>. Perubahan berlaku setelah redeploy.</p>
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

    </div>

    {{-- Auto-refresh saat building --}}
    @if ($project->status == 'building')
        <script>
            setTimeout(() => window.location.href = window.location.pathname + '?t=' + Date.now(), 2000);
        </script>
    @endif

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

            if (name === 'terminal') {
                setTimeout(() => document.getElementById('terminal-input').focus(), 100);
            }
        }

        // ── Terminal ───────────────────────────────────────────────────────────
        const terminalOutput = document.getElementById('terminal-output');
        const terminalInput  = document.getElementById('terminal-input');
        const projectHashid  = '{{ $project->hashid }}';
        const terminalUrl    = '{{ route("user_hosting.terminal", $project->hashid) }}';
        const csrfToken      = '{{ csrf_token() }}';

        let commandHistory = [];
        let historyIndex   = -1;
        let isRunning      = false;

        function appendToTerminal(html) {
            terminalOutput.insertAdjacentHTML('beforeend', html);
            terminalOutput.scrollTop = terminalOutput.scrollHeight;
        }

        function escapeHtml(text) {
            return text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                // ANSI color basic support
                .replace(/\u001b\[0m/g, '</span>')
                .replace(/\u001b\[31m/g, '<span style="color:#f87171">')
                .replace(/\u001b\[32m/g, '<span style="color:#4ade80">')
                .replace(/\u001b\[33m/g, '<span style="color:#facc15">')
                .replace(/\u001b\[34m/g, '<span style="color:#60a5fa">')
                .replace(/\u001b\[36m/g, '<span style="color:#22d3ee">')
                .replace(/\u001b\[37m/g, '<span style="color:#e2e8f0">')
                .replace(/\u001b\[1m/g, '<span style="font-weight:700">')
                .replace(/\u001b\[[0-9;]*m/g, ''); // strip sisa ANSI
        }

        async function runCommand() {
            if (isRunning) return;

            const cmd = terminalInput.value.trim();
            if (!cmd) return;

            // History
            commandHistory.unshift(cmd);
            if (commandHistory.length > 50) commandHistory.pop();
            historyIndex = -1;

            // Echo command ke terminal
            appendToTerminal(
                `<div class="flex items-start gap-2 mb-1">` +
                `<span class="text-indigo-400 shrink-0 select-none">~/${terminalInput.closest('.flex').querySelector('span').textContent.trim().split('/').pop()} $</span>` +
                `<span class="text-slate-100 break-all">${escapeHtml(cmd)}</span>` +
                `</div>`
            );

            terminalInput.value = '';
            isRunning = true;

            // Loading indicator
            const loaderId = 'loader-' + Date.now();
            appendToTerminal(`<div id="${loaderId}" class="text-slate-600 animate-pulse mb-1">▌</div>`);

            try {
                const res = await fetch(terminalUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ command: cmd }),
                });

                const data = await res.json();
                document.getElementById(loaderId)?.remove();

                if (data.error) {
                    appendToTerminal(`<div class="text-rose-400 mb-2">${escapeHtml(data.error)}</div>`);
                } else if (data.output && data.output.trim() !== '') {
                    const color = (data.exit_code !== 0) ? 'text-rose-300' : 'text-slate-200';
                    appendToTerminal(
                        `<pre class="${color} whitespace-pre-wrap break-all mb-2 leading-relaxed">${escapeHtml(data.output)}</pre>`
                    );
                }

            } catch (err) {
                document.getElementById(loaderId)?.remove();
                appendToTerminal(`<div class="text-rose-400 mb-2">Network error: ${err.message}</div>`);
            }

            isRunning = false;
            terminalInput.focus();
        }

        function clearTerminal() {
            // Hapus semua kecuali welcome message (div pertama)
            const children = Array.from(terminalOutput.children);
            children.slice(1).forEach(c => c.remove());
        }

        // Keyboard: Enter, ArrowUp/Down, Ctrl+L
        terminalInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                runCommand();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (historyIndex < commandHistory.length - 1) {
                    historyIndex++;
                    terminalInput.value = commandHistory[historyIndex];
                }
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (historyIndex > 0) {
                    historyIndex--;
                    terminalInput.value = commandHistory[historyIndex];
                } else {
                    historyIndex = -1;
                    terminalInput.value = '';
                }
            } else if (e.key === 'l' && e.ctrlKey) {
                e.preventDefault();
                clearTerminal();
            }
        });
    </script>
@endsection
