@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- ── 8. USER HOSTING – Aplikasi Ter-deploy ──────────────────────── --}}
        {{-- ── 8. USER HOSTING – Aplikasi Ter-deploy ──────────────────────── --}}
        <x-ui.page-header 
            title="Aplikasi Ter-deploy" 
            subtitle="Kelola semua proyek dan aplikasi yang berjalan di Ryaze." 
            icon="fa-solid fa-box-open" 
            iconColor="emerald">
            <x-slot:actions>
                <a href="{{ route('user_hosting.create') }}"
                    class="inline-flex justify-center items-center flex-shrink-0 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    + Deploy Proyek Baru
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="mt-6">
            @php
                $activeGracePeriod = auth()->user()->hostingBillings()
                    ->where('status', 'active')
                    ->where('plan_name', 'like', '%Grace Period%')
                    ->where('next_due_date', '>', now())
                    ->first();
            @endphp
            
            @if($activeGracePeriod)
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-md border-0 p-5 mb-6 text-white flex items-center justify-between relative overflow-hidden">
                    <div class="absolute -right-4 -top-10 opacity-20 transform rotate-12 pointer-events-none">
                        <i class="fa-solid fa-gift text-9xl"></i>
                    </div>
                    <div class="relative z-10 flex items-start gap-4">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-gift text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg mb-1">Apresiasi Pengguna Beta! 🎁</h3>
                            <p class="text-indigo-100 text-sm leading-relaxed max-w-3xl">
                                Terima kasih telah menggunakan versi Beta kami! Sebagai bentuk apresiasi, kami telah memberikan Anda <strong>Masa Tenggang (Grace Period) 1 Bulan secara gratis</strong>. 
                                Tagihan Anda berikutnya akan dimulai pada <strong class="text-white">{{ $activeGracePeriod->next_due_date->translatedFormat('d F Y') }}</strong>.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('user_hosting.billing') ?? '#' }}" class="relative z-10 shrink-0 bg-white dark:bg-slate-800/60 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 dark:hover:bg-indigo-500/10 px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm ml-4 whitespace-nowrap">
                        Cek Tagihan &rarr;
                    </a>
                </div>
            @endif

            @if ($projects->isEmpty())
                <div class="bg-white dark:bg-slate-800/60 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-12 text-center">
                    <div
                        class="w-16 h-16 bg-slate-100 dark:bg-slate-700/50 text-slate-400 dark:text-slate-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">Belum ada aplikasi</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-6 text-sm">Mulai deploy aplikasi pertamamu dari repositori GitHub.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($projects as $project)
                        <div
                            class="bg-white dark:bg-slate-800/60 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 hover:border-indigo-300 hover:shadow-md transition-all duration-200 flex flex-col">
                            <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-start">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 border border-slate-200 dark:border-slate-700 rounded-lg flex items-center justify-center bg-slate-50 dark:bg-slate-800/60 shrink-0">
                                        <i class="{{ get_framework_icon($project->framework) }} text-xl"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('user_hosting.show', $project->hashid) }}"
                                            class="font-bold text-slate-800 dark:text-slate-100 hover:text-indigo-600 dark:hover:text-indigo-400 dark:hover:text-indigo-400 text-lg line-clamp-1">
                                            {{ $project->project_name }}
                                        </a>
                                        @php
                                            $activeDomain = $project->domains()->where('ssl_status', 'active')->first();
                                            $displayUrl = $activeDomain ? $activeDomain->domain_name : $project->ryaze_domain;
                                        @endphp
                                        <div class="mt-2 text-sm text-slate-500 dark:text-slate-400 flex items-center">
                                            <a href="https://{{ $displayUrl }}" target="_blank"
                                                class="hover:text-indigo-600 dark:hover:text-indigo-400 dark:hover:text-indigo-400 hover:underline transition-colors flex items-center">
                                                {{ $displayUrl }} <i
                                                    class="fa-solid fa-arrow-up-right-from-square ml-1 text-[10px]"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-5 flex-grow">
                                @php
                                    $isUploadSource   = ($project->source_type === 'upload') || (is_string($project->repo_source) && str_starts_with($project->repo_source, 'upload:'));
                                    $isTemplateSource = ($project->source_type === 'template') || (is_string($project->repo_source) && str_starts_with($project->repo_source, 'template:'));
                                @endphp
                                <div class="flex items-center gap-2 mb-3 text-xs">
                                    @if($isUploadSource)
                                        <span class="text-emerald-600 dark:text-emerald-300"><i class="fa-solid fa-file-zipper mr-1"></i> ZIP:</span>
                                        <span class="font-mono text-slate-700 dark:text-slate-200 truncate" title="{{ $project->repo_source }}">
                                            {{ basename(str_replace('upload:', '', $project->repo_source)) }}
                                        </span>
                                    @elseif($isTemplateSource)
                                        <span class="text-indigo-500 dark:text-indigo-400"><i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Template:</span>
                                        <span class="font-mono text-slate-700 dark:text-slate-200 truncate">
                                            {{ ucwords(str_replace(['template:', '_', '-'], ['', ' ', ' '], (string) $project->repo_source)) }}
                                        </span>
                                    @else
                                        <span class="text-slate-500 dark:text-slate-400"><i class="fa-brands fa-github mr-1"></i> Repo:</span>
                                        <span class="font-mono text-slate-700 dark:text-slate-200 truncate" title="{{ $project->repo_source }}">
                                            {{ str_replace('https://github.com/', '', $project->repo_source) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 text-xs">
                                    @if(!$isUploadSource)
                                        <span class="text-slate-500 dark:text-slate-400"><i class="fa-solid fa-code-branch mr-1"></i> Branch:</span>
                                        <span class="font-mono bg-slate-100 dark:bg-slate-700/50 px-1.5 py-0.5 rounded text-slate-700 dark:text-slate-200">
                                            {{ $project->branch }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div
                                class="px-5 py-3 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-700 rounded-b-xl flex justify-between items-center">
                                <span
                                    class="inline-flex items-center text-[11px] font-bold uppercase tracking-wider
                                {{ $project->status == 'active' ? 'text-emerald-600 dark:text-emerald-300' : ($project->status == 'building' ? 'text-amber-500 dark:text-amber-400 animate-pulse' : 'text-rose-500 dark:text-rose-400') }}">
                                    <i
                                        class="fa-solid {{ $project->status == 'active' ? 'fa-circle-check' : ($project->status == 'building' ? 'fa-spinner fa-spin' : 'fa-circle-xmark') }} mr-1.5"></i>
                                    {{ $project->status }}
                                </span>

                                <a href="{{ route('user_hosting.show', $project->hashid) }}"
                                    class="text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 dark:hover:text-indigo-400 transition-colors">
                                    Kelola &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </x-ui.page-layout>
@endsection
