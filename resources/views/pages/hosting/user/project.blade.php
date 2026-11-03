@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- ── 8. USER HOSTING – Aplikasi Ter-deploy ──────────────────────── --}}
        <x-ui.page-header 
            title="Aplikasi Ter-deploy" 
            eyebrow="Inventaris Aplikasi"
            status="LIVE"
            subtitle="Kelola semua proyek dan aplikasi yang berjalan di Ryaze." 
            icon="fa-solid fa-box-open" 
            iconColor="emerald">
            <x-slot:actions>
                <a href="{{ route('user_hosting.create') }}"
                    class="ui-btn-primary flex-shrink-0">
                    <i class="fa-solid fa-plus text-xs"></i> Deploy Proyek Baru
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
                <div class="bg-[#12233A] border border-[#1D5B96] shadow-none p-5 mb-6 text-white flex items-center justify-between relative overflow-hidden">
                    <div class="absolute -right-4 -top-10 opacity-20 transform rotate-12 pointer-events-none">
                        <i class="fa-solid fa-gift text-9xl"></i>
                    </div>
                    <div class="relative z-10 flex items-start gap-4">
                        <div class="w-12 h-12 border border-white/20 bg-white/10 text-white flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-gift text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg mb-1">Apresiasi Pengguna Beta! 🎁</h3>
                            <p class="text-[#E8F0F9] text-sm leading-relaxed max-w-3xl">
                                Terima kasih telah menggunakan versi Beta kami! Sebagai bentuk apresiasi, kami telah memberikan Anda <strong>Masa Tenggang (Grace Period) 1 Bulan secara gratis</strong>. 
                                Tagihan Anda berikutnya akan dimulai pada <strong class="text-white">{{ $activeGracePeriod->next_due_date->translatedFormat('d F Y') }}</strong>.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('user_hosting.billing') ?? '#' }}" class="ui-btn relative z-10 ml-4 shrink-0 whitespace-nowrap bg-white text-[#0F1A2A] hover:bg-[#E8F0F9]">
                        Cek Tagihan <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            @endif

            @if ($projects->isEmpty())
                <div class="ui-panel p-12 text-center">
                    <div
                        class="w-16 h-16 border border-[#E3EAF1] dark:border-[#2A4463] bg-[#F0F6FC] dark:bg-[#0F1D30] text-[#94A3B4] dark:text-white/40 flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                    <h3 class="ui-display text-lg mb-2">Belum ada aplikasi</h3>
                    <p class="text-[#525252] dark:text-white/50 mb-2 text-sm">Mulai deploy aplikasi pertamamu dari repositori GitHub.</p>
                    <p class="ui-eyebrow-muted">Deploy &middot; Build &middot; Live</p>
                </div>
            @else
                <div class="ui-grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($projects as $project)
                        <div
                            class="ui-cell flex flex-col transition-colors hover:bg-[#E8F0F9] dark:hover:bg-[#1A2F49]">
                            <div class="p-5 border-b border-[#EDF2F7] dark:border-[#2A4463] flex justify-between items-start">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 border border-[#E3EAF1] dark:border-[#2A4463] flex items-center justify-center bg-[#F4F8FC] dark:bg-[#0F1D30] shrink-0">
                                        <i class="{{ get_framework_icon($project->framework) }} text-xl"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('user_hosting.show', $project->hashid) }}"
                                            class="font-bold text-[#0F1A2A] dark:text-white hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] text-lg line-clamp-1">
                                            {{ $project->project_name }}
                                        </a>
                                        @php
                                            $activeDomain = $project->domains()->where('ssl_status', 'active')->first();
                                            $displayUrl = $activeDomain ? $activeDomain->domain_name : $project->ryaze_domain;
                                        @endphp
                                        <div class="mt-2 text-sm text-[#525252] dark:text-[#8A9BAE] flex items-center">
                                            <a href="https://{{ $displayUrl }}" target="_blank"
                                                class="hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] hover:underline transition-colors flex items-center">
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
                                        <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-emerald-600 dark:text-emerald-400"><i class="fa-solid fa-file-zipper mr-1"></i> ZIP:</span>
                                        <span class="font-mono text-[#0F1A2A] dark:text-white/85 truncate" title="{{ $project->repo_source }}">
                                            {{ basename(str_replace('upload:', '', $project->repo_source)) }}
                                        </span>
                                    @elseif($isTemplateSource)
                                        <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC]"><i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Template:</span>
                                        <span class="font-mono text-[#0F1A2A] dark:text-white/85 truncate">
                                            {{ ucwords(str_replace(['template:', '_', '-'], ['', ' ', ' '], (string) $project->repo_source)) }}
                                        </span>
                                    @else
                                        <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-[#737373] dark:text-white/40"><i class="fa-brands fa-github mr-1"></i> Repo:</span>
                                        <span class="font-mono text-[#0F1A2A] dark:text-white/85 truncate" title="{{ $project->repo_source }}">
                                            {{ str_replace('https://github.com/', '', $project->repo_source) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 text-xs">
                                    @if(!$isUploadSource)
                                        <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-[#737373] dark:text-white/40"><i class="fa-solid fa-code-branch mr-1"></i> Branch:</span>
                                        <span class="font-mono bg-[#F0F6FC] dark:bg-[#0F1D30] px-1.5 py-0.5 text-[#0F1A2A] dark:text-white/85">
                                            {{ $project->branch }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div
                                class="px-5 py-3 bg-[#F4F8FC] dark:bg-[#12233A] border-t border-[#EDF2F7] dark:border-[#2A4463] flex justify-between items-center">
                                <span
                                    class="inline-flex items-center font-mono text-[10px] font-semibold uppercase tracking-[0.2em]
                                {{ $project->status == 'active' ? 'text-emerald-600 dark:text-emerald-400' : ($project->status == 'building' ? 'text-amber-600 dark:text-amber-400 animate-pulse' : 'text-rose-600 dark:text-rose-400') }}">
                                    <i
                                        class="fa-solid {{ $project->status == 'active' ? 'fa-circle-check' : ($project->status == 'building' ? 'fa-spinner fa-spin' : 'fa-circle-xmark') }} mr-1.5"></i>
                                    {{ $project->status }}
                                </span>

                                <a href="{{ route('user_hosting.show', $project->hashid) }}" class="ui-arrow-link">
                                    Kelola <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </x-ui.page-layout>
@endsection
