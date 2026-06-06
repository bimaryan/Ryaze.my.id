@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50">

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
                            <button
                                class="text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-600 hover:text-white px-3 py-1.5 rounded transition-colors">
                                Kelola .env &rarr;
                            </button>
                        </div>
                    </div>
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
