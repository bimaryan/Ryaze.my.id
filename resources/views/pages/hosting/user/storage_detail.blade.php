@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- ── 10. USER HOSTING – Storage Detail (per Project) ───────────── --}}
        <x-ui.page-header 
            title="{{ $project->project_name }}" 
            eyebrow="Rincian Proyek"
            icon="fa-folder-open" 
            iconColor="emerald">
            <x-slot:subtitle>
                <p class="text-xs text-[#8A9BAE] dark:text-white/45 font-mono mt-1 px-2 py-0.5 bg-[#F4F8FC] dark:bg-[#0F1D30] border border-[#EDF2F7] dark:border-[#2A4463] inline-block truncate max-w-full">
                    {{ $project_dir }}
                </p>
            </x-slot:subtitle>
            <x-slot:actions>
                <a href="{{ route('user_hosting.storage') }}"
                    class="ui-btn ui-btn-secondary px-5 py-2.5 text-sm font-medium">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        {{-- Tips --}}
        <div class="ui-panel mt-6 overflow-hidden">
            <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                <h3 class="font-mono text-[11px] font-semibold uppercase tracking-[0.2em] text-[#0F1A2A] dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-lightbulb text-amber-400 dark:text-amber-300"></i> Tips Hemat Storage
                </h3>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-[#525252] dark:text-[#8A9BAE]">
                    <div class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-400 dark:text-emerald-300 mt-0.5 shrink-0"></i>
                        <span>Folder <code class="bg-[#F0F6FC] dark:bg-[#0F1D30] px-1">node_modules</code> bisa sangat besar. Jalankan
                            <code class="bg-[#F0F6FC] dark:bg-[#0F1D30] px-1">npm install</code> saat deploy, bukan di-commit ke
                            Git.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-400 dark:text-emerald-300 mt-0.5 shrink-0"></i>
                        <span>Folder <code class="bg-[#F0F6FC] dark:bg-[#0F1D30] px-1">vendor</code> di Laravel juga auto-generated. Cukup
                            commit <code class="bg-[#F0F6FC] dark:bg-[#0F1D30] px-1">composer.json</code> saja.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-400 dark:text-emerald-300 mt-0.5 shrink-0"></i>
                        <span>Hapus log lama di <code class="bg-[#F0F6FC] dark:bg-[#0F1D30] px-1">storage/logs</code> secara
                            berkala.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-400 dark:text-emerald-300 mt-0.5 shrink-0"></i>
                        <span>Gunakan tab <strong>Terminal</strong> untuk menjalankan <code class="bg-[#F0F6FC] dark:bg-[#0F1D30] px-1">rm
                                -rf storage/logs/*.log</code>.</span>
                    </div>
                </div>
            </div>
        </div>

        @php
            $barColor = $percent >= 90 ? 'bg-rose-500' : ($percent >= 70 ? 'bg-amber-500' : 'bg-[#1D5B96] dark:bg-[#6BA8DC]');
            $textColor = $percent >= 90 ? 'text-rose-600 dark:text-rose-300' : ($percent >= 70 ? 'text-amber-600 dark:text-amber-300' : 'text-[#1D5B96] dark:text-[#6BA8DC]');
            $bgLight =
                $percent >= 90
                    ? 'bg-rose-50 dark:bg-rose-500/10 border-rose-200 dark:border-rose-500/40'
                    : ($percent >= 70
                        ? 'bg-amber-50 dark:bg-amber-500/10 border-amber-200 dark:border-amber-500/40'
                        : 'bg-[#E8F0F9] dark:bg-[#1D5B96]/10 border-[#C9D5E2] dark:border-[#1D5B96]/40');
            $free = max(0, $limit_bytes - $used_bytes);
            $freeHuman = '';
            $freeBytes = $free;
            $units = ['B', 'KB', 'MB', 'GB'];
            $i = $freeBytes > 0 ? (int) floor(log($freeBytes, 1024)) : 0;
            $freeHuman = round($freeBytes / pow(1024, max($i, 0)), 1) . ' ' . $units[min($i, 3)];
        @endphp

        {{-- Usage summary — spec rows --}}
        <div class="ui-panel mb-6 mt-6">
            <dl>
                <div class="ui-spec-row">
                    <dt class="ui-eyebrow-muted">Terpakai</dt>
                    <dd class="font-mono text-sm font-bold text-[#0F1A2A] dark:text-white">
                        {{ $used_human }} <span class="text-xs font-medium text-[#8A9BAE] dark:text-white/45">dari {{ $limit_human }}</span>
                    </dd>
                </div>
                <div class="ui-spec-row">
                    <dt class="ui-eyebrow-muted">Tersedia</dt>
                    <dd class="font-mono text-sm font-bold text-emerald-600 dark:text-emerald-300">
                        {{ $freeHuman }} <span class="text-xs font-medium text-[#8A9BAE] dark:text-white/45">sisa kapasitas</span>
                    </dd>
                </div>
                <div class="ui-spec-row">
                    <dt class="ui-eyebrow-muted">Penggunaan</dt>
                    <dd class="flex items-center gap-3">
                        <span class="w-32 sm:w-48 bg-[#F0F6FC] dark:bg-[#0F1D30] h-1 overflow-hidden inline-block">
                            <span class="{{ $barColor }} block h-1 transition-all duration-700" style="width:{{ $percent }}%"></span>
                        </span>
                        <span class="{{ $textColor }} font-mono text-sm font-bold">{{ $percent }}%</span>
                    </dd>
                </div>
            </dl>
        </div>

        @if ($percent >= 90)
            <div class="mb-6 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/40 px-5 py-4 flex items-start gap-3">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 dark:text-rose-400 text-lg mt-0.5"></i>
                <div>
                    <p class="font-bold text-rose-700 dark:text-rose-300 text-sm">Storage hampir penuh!</p>
                    <p class="text-rose-600 dark:text-rose-300 text-xs mt-0.5">Hapus file atau folder yang tidak diperlukan. Deployment baru
                        akan gagal jika storage melebihi batas.</p>
                </div>
            </div>
        @endif

        {{-- Breakdown tabel --}}
        <div class="ui-panel overflow-hidden">
            <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                <h2 class="font-mono text-[11px] font-semibold uppercase tracking-[0.2em] text-[#0F1A2A] dark:text-white">Breakdown Folder &amp; File</h2>
                @if (count($breakdown) > 0)
                    <span class="ui-status"><span class="ui-status-dot"></span>{{ count($breakdown) }} item</span>
                @else
                    <span class="ui-eyebrow-muted">0 item</span>
                @endif
            </div>

            @if (count($breakdown) === 0)
                <div class="px-6 py-16 text-center border-t border-[#EDF2F7] dark:border-[#2A4463]">
                    <i class="fa-regular fa-folder-open text-[#C9D5E2] text-5xl mb-4"></i>
                    <p class="text-[#8A9BAE] dark:text-white/45">Folder kosong atau project belum di-deploy.</p>
                </div>
            @else
                <div class="overflow-x-auto border-t border-[#EDF2F7] dark:border-[#2A4463]">
                    <table class="w-full text-sm text-left text-[#525252] dark:text-white/70">
                        <thead class="bg-[#F4F8FC] text-xs font-mono uppercase tracking-wider font-semibold text-[#0F1A2A] border-b border-[#E3EAF1] dark:bg-[#12233A] dark:text-white/40 dark:border-[#2A4463]">
                            <tr>
                                <th class="px-6 py-3 text-left w-full sm:w-auto">Nama</th>
                                <th class="px-4 py-3 text-right whitespace-nowrap">Ukuran</th>
                                <th class="px-6 py-3 text-left hidden sm:table-cell sm:w-[200px]">Proporsi</th>
                                <th class="px-4 py-3 text-right hidden sm:table-cell sm:w-[80px]">%</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E3EAF1] dark:divide-[#2A4463]">
                            @foreach ($breakdown as $item)
                                @php
                                    $bc =
                                        $item['percent'] >= 50
                                            ? 'bg-[#1D5B96] dark:bg-[#6BA8DC]'
                                            : ($item['percent'] >= 20
                                                ? 'bg-[#1D5B96] dark:bg-[#6BA8DC]'
                                                : 'bg-[#6BA8DC] dark:bg-[#7FB4E2]');
                                @endphp
                                <tr class="hover:bg-[#E8F0F9] dark:bg-[#0F1D30] dark:hover:bg-[#1A2F49] transition-colors">
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2 min-w-0">
                                            @if ($item['is_dir'])
                                                <i class="fa-solid fa-folder text-amber-400 dark:text-amber-300 shrink-0"></i>
                                            @else
                                                <i class="fa-regular fa-file-lines text-[#8A9BAE] dark:text-white/45 shrink-0"></i>
                                            @endif
                                            <span class="font-mono text-[#0F1A2A] dark:text-white/85 text-xs truncate">{{ $item['name'] }}</span>
                                            @if (in_array($item['name'], ['vendor', 'node_modules', '.git']))
                                                <span
                                                    class="text-[10px] px-1.5 py-0.5 bg-[#F0F6FC] dark:bg-[#0F1D30] text-[#8A9BAE] dark:text-white/45 font-medium shrink-0">auto</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-[#0F1A2A] dark:text-white/85 text-xs whitespace-nowrap">
                                        {{ $item['human'] }}
                                    </td>
                                    <td class="px-6 py-3 hidden sm:table-cell">
                                        <div class="w-full bg-[#F0F6FC] dark:bg-[#0F1D30] h-1.5 overflow-hidden">
                                            <div class="{{ $bc }} h-1.5" style="width:{{ $item['percent'] }}%"></div>
                                        </div>
                                    </td>
                                    <td
                                        class="px-4 py-3 text-right text-xs text-[#8A9BAE] dark:text-white/45 hidden sm:table-cell whitespace-nowrap">
                                        {{ $item['percent'] }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </x-ui.page-layout>
@endsection
