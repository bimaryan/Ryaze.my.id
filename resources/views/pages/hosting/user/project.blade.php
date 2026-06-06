@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Aplikasi Ter-deploy</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola semua proyek dan aplikasi yang berjalan di Ryaze.</p>
            </div>
            <a href="{{ route('user_hosting.create') }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-plus"></i> Deploy Proyek Baru
            </a>
        </div>

        @if ($projects->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
                <div
                    class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Belum ada aplikasi</h3>
                <p class="text-slate-500 mb-6 text-sm">Mulai deploy aplikasi pertamamu dari repositori GitHub.</p>
                <a href="{{ route('user_hosting.create') }}"
                    class="text-indigo-600 bg-indigo-50 hover:bg-indigo-100 font-semibold px-4 py-2 rounded-lg text-sm transition-colors">
                    Deploy Sekarang &rarr;
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($projects as $project)
                    <div
                        class="bg-white rounded-xl shadow-sm border border-slate-200 hover:border-indigo-300 hover:shadow-md transition-all duration-200 flex flex-col">
                        <div class="p-5 border-b border-slate-100 flex justify-between items-start">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 border border-slate-200 rounded-lg flex items-center justify-center bg-slate-50 shrink-0">
                                    @if ($project->framework == 'react')
                                        <i class="fa-brands fa-react text-xl text-sky-500"></i>
                                    @elseif($project->framework == 'nextjs')
                                        <i class="fa-brands fa-node-js text-xl text-slate-800"></i>
                                    @elseif($project->framework == 'laravel')
                                        <i class="fa-brands fa-laravel text-xl text-red-500"></i>
                                    @elseif($project->framework == 'python')
                                        <i class="fa-brands fa-python text-xl text-yellow-500"></i>
                                    @elseif($project->framework == 'node')
                                        <i class="fa-brands fa-node text-xl text-emerald-500"></i>
                                    @else
                                        <i class="fa-brands fa-html5 text-xl text-orange-500"></i>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('user_hosting.show', $project->hashid) }}"
                                        class="font-bold text-slate-800 hover:text-indigo-600 text-lg line-clamp-1">
                                        {{ $project->project_name }}
                                    </a>
                                    <a href="https://{{ $project->ryaze_domain }}" target="_blank"
                                        class="text-xs text-slate-500 hover:text-indigo-600 flex items-center gap-1 mt-0.5">
                                        {{ $project->ryaze_domain }} <i
                                            class="fa-solid fa-arrow-up-right-from-square text-[8px]"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 flex-grow">
                            <div class="flex items-center gap-2 mb-3 text-xs">
                                <span class="text-slate-500"><i class="fa-brands fa-github mr-1"></i> Repo:</span>
                                <span class="font-mono text-slate-700 truncate" title="{{ $project->repo_source }}">
                                    {{ str_replace('https://github.com/', '', $project->repo_source) }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                <span class="text-slate-500"><i class="fa-solid fa-code-branch mr-1"></i> Branch:</span>
                                <span class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-slate-700">
                                    {{ $project->branch }}
                                </span>
                            </div>
                        </div>

                        <div
                            class="px-5 py-3 bg-slate-50 border-t border-slate-100 rounded-b-xl flex justify-between items-center">
                            <span
                                class="inline-flex items-center text-[11px] font-bold uppercase tracking-wider
                                {{ $project->status == 'active' ? 'text-emerald-600' : ($project->status == 'building' ? 'text-amber-500 animate-pulse' : 'text-rose-500') }}">
                                <i
                                    class="fa-solid {{ $project->status == 'active' ? 'fa-circle-check' : ($project->status == 'building' ? 'fa-spinner fa-spin' : 'fa-circle-xmark') }} mr-1.5"></i>
                                {{ $project->status }}
                            </span>

                            <a href="{{ route('user_hosting.show', $project->hashid) }}"
                                class="text-xs font-semibold text-slate-600 hover:text-indigo-600 transition-colors">
                                Kelola &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
@endsection
