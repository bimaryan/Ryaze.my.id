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
                            <a href="https://{{ $project->ryaze_domain }}" target="_blank" title="Buka di tab baru"
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
                @endif

                <div class="bg-slate-900 rounded-xl shadow-md border border-slate-800 overflow-hidden">
                    <div class="bg-slate-800 px-4 py-3 flex items-center gap-2 border-b border-slate-700">
                        <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        <span class="text-slate-400 text-xs font-mono ml-2">Deployment Log -
                            {{ $project->deployments->first()->commit_hash ?? 'Initial Build' }}</span>
                    </div>

                    <div class="p-4 h-[400px] overflow-y-auto font-mono text-sm">
                        @if ($project->deployments->count() > 0)
                            <pre class="text-emerald-400 whitespace-pre-wrap leading-relaxed">{{ $project->deployments->first()->build_logs }}</pre>

                            @if ($project->status == 'building')
                                <div class="mt-4 flex items-center text-slate-400 animate-pulse">
                                    <span class="mr-2">></span> <span
                                        class="w-2 h-4 bg-slate-400 inline-block animate-ping"></span>
                                </div>
                            @endif
                        @else
                            <p class="text-slate-500">Belum ada log deployment.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-800 mb-4 border-b pb-2">Detail Deployment</h3>
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
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <div class="flex items-start gap-4">
                        <div
                            class="w-10 h-10 bg-amber-50 text-amber-500 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 mb-1">Environment Variables</h3>
                            <p class="text-xs text-slate-500 mb-3">Atur .env file, API keys, dan secret tokens.</p>
                            <button type="button" onclick="document.getElementById('envModal').classList.remove('hidden')"
                                class="text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-600 hover:text-white px-3 py-1.5 rounded transition-colors">
                                Kelola .env &rarr;
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="envModal" class="hidden fixed inset-0 z-[999] overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" aria-hidden="true"
                    onclick="document.getElementById('envModal').classList.add('hidden')"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="relative z-10 inline-block bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-slate-200">
                    <form action="{{ route('user_hosting.env.update', $project->hashid) }}" method="POST">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-amber-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <i class="fa-solid fa-key text-amber-600"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">
                                        Editor .env
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-slate-500 mb-3">
                                            Masukkan variabel environment dengan format <code
                                                class="bg-slate-100 px-1 py-0.5 rounded text-rose-500">KUNCI=nilai</code>.
                                        </p>
                                        <div class="bg-slate-900 rounded-lg p-1 border border-slate-800 relative">
                                            <textarea name="env_content" rows="10"
                                                class="w-full bg-transparent text-emerald-400 font-mono text-sm p-3 focus:outline-none focus:ring-0 border-0 resize-y"
                                                placeholder="API_KEY=rahasia_negara&#10;DB_HOST=127.0.0.1" spellcheck="false">{{ old('env_content', $envContent) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-200">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                Simpan .env
                            </button>
                            <button type="button" onclick="document.getElementById('envModal').classList.add('hidden')"
                                class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @if ($project->status == 'building')
            <script>
                setTimeout(function() {
                    window.location.href = window.location.pathname + '?t=' + new Date().getTime();
                }, 2000);
            </script>
        @endif
    </div>
@endsection
