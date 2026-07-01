@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- ── 10. USER HOSTING – Storage Detail (per Project) ───────────── --}}
        <div
            class="p-5 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4 min-w-0">
                <div class="shrink-0 w-11 h-11 flex items-center justify-center bg-emerald-50 text-emerald-600 rounded-lg">
                    <i class="fa-solid fa-folder-open text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-800 truncate">{{ $project->project_name }}</h1>
                    <p
                        class="text-xs text-slate-400 font-mono mt-1 px-2 py-0.5 bg-slate-50 border border-slate-100 rounded inline-block truncate max-w-full">
                        {{ $project_dir }}
                    </p>
                </div>
            </div>
            <a href="{{ route('user_hosting.storage') }}"
                class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                &larr; Kembali
            </a>
        </div>

        {{-- Tips --}}
        <div class="mt-6 bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <h3 class="font-bold text-slate-700 text-sm mb-3 flex items-center gap-2">
                <i class="fa-solid fa-lightbulb text-amber-400"></i> Tips Hemat Storage
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-slate-500">
                <div class="flex items-start gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400 mt-0.5 shrink-0"></i>
                    <span>Folder <code class="bg-slate-100 px-1 rounded">node_modules</code> bisa sangat besar. Jalankan
                        <code class="bg-slate-100 px-1 rounded">npm install</code> saat deploy, bukan di-commit ke
                        Git.</span>
                </div>
                <div class="flex items-start gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400 mt-0.5 shrink-0"></i>
                    <span>Folder <code class="bg-slate-100 px-1 rounded">vendor</code> di Laravel juga auto-generated. Cukup
                        commit <code class="bg-slate-100 px-1 rounded">composer.json</code> saja.</span>
                </div>
                <div class="flex items-start gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400 mt-0.5 shrink-0"></i>
                    <span>Hapus log lama di <code class="bg-slate-100 px-1 rounded">storage/logs</code> secara
                        berkala.</span>
                </div>
                <div class="flex items-start gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400 mt-0.5 shrink-0"></i>
                    <span>Gunakan tab <strong>Terminal</strong> untuk menjalankan <code class="bg-slate-100 px-1 rounded">rm
                            -rf storage/logs/*.log</code>.</span>
                </div>
            </div>
        </div>

        @php
            $barColor = $percent >= 90 ? 'bg-rose-500' : ($percent >= 70 ? 'bg-amber-500' : 'bg-indigo-500');
            $textColor = $percent >= 90 ? 'text-rose-600' : ($percent >= 70 ? 'text-amber-600' : 'text-indigo-600');
            $bgLight =
                $percent >= 90
                    ? 'bg-rose-50 border-rose-200'
                    : ($percent >= 70
                        ? 'bg-amber-50 border-amber-200'
                        : 'bg-indigo-50 border-indigo-200');
            $free = max(0, $limit_bytes - $used_bytes);
            $freeHuman = '';
            $freeBytes = $free;
            $units = ['B', 'KB', 'MB', 'GB'];
            $i = $freeBytes > 0 ? (int) floor(log($freeBytes, 1024)) : 0;
            $freeHuman = round($freeBytes / pow(1024, max($i, 0)), 1) . ' ' . $units[min($i, 3)];
        @endphp

        {{-- Usage summary card --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6 mt-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex flex-col justify-between">
                <div>
                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-1">Terpakai</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $used_human }}</p>
                    <p class="text-xs text-slate-400 mt-1">dari {{ $limit_human }}</p>
                </div>
                @if (($project->storage_limit_mb ?? 1024) < 2048)
                    <form action="{{ route('user_hosting.storage.upgrade', $project->hashid) }}" method="POST"
                        class="mt-4">
                        @csrf
                        <button type="submit"
                            class="w-full inline-flex justify-center items-center gap-2 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 px-3 py-2 rounded-lg text-xs font-semibold transition">
                            <i class="fa-solid fa-arrow-up-right-dots"></i> Upgrade 2GB (Rp 50.000)
                        </button>
                    </form>
                @endif
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-1">Tersedia</p>
                <p class="text-2xl font-bold text-emerald-600">{{ $freeHuman }}</p>
                <p class="text-xs text-slate-400 mt-1">sisa kapasitas</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex flex-col justify-center">
                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-2">Penggunaan</p>
                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden mb-1">
                    <div class="{{ $barColor }} h-2.5 rounded-full" style="width:{{ $percent }}%"></div>
                </div>
                <p class="{{ $textColor }} text-sm font-bold">{{ $percent }}%</p>
            </div>
        </div>

        @if ($percent >= 90)
            <div class="mb-6 bg-rose-50 border border-rose-200 rounded-xl px-5 py-4 flex items-start gap-3">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-lg mt-0.5"></i>
                <div>
                    <p class="font-bold text-rose-700 text-sm">Storage hampir penuh!</p>
                    <p class="text-rose-600 text-xs mt-0.5">Hapus file atau folder yang tidak diperlukan. Deployment baru
                        akan gagal jika storage melebihi batas.</p>
                </div>
            </div>
        @endif

        {{-- Breakdown tabel --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="font-bold text-slate-800 text-sm">Breakdown Folder & File</h2>
                <span class="text-xs text-slate-400">{{ count($breakdown) }} item</span>
            </div>

            @if (count($breakdown) === 0)
                <div class="px-6 py-16 text-center">
                    <i class="fa-regular fa-folder-open text-slate-200 text-5xl mb-4"></i>
                    <p class="text-slate-400">Folder kosong atau project belum di-deploy.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-3 text-left w-full sm:w-auto">Nama</th>
                                <th class="px-4 py-3 text-right whitespace-nowrap">Ukuran</th>
                                <th class="px-6 py-3 text-left hidden sm:table-cell sm:w-[200px]">Proporsi</th>
                                <th class="px-4 py-3 text-right hidden sm:table-cell sm:w-[80px]">%</th>
                                </x-slot:head>
                                @foreach ($breakdown as $item)
                                    @php
                                        $bc =
                                            $item['percent'] >= 50
                                                ? 'bg-indigo-500'
                                                : ($item['percent'] >= 20
                                                    ? 'bg-indigo-400'
                                                    : 'bg-indigo-300');
                                    @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2 min-w-0">
                                        @if ($item['is_dir'])
                                            <i class="fa-solid fa-folder text-amber-400 shrink-0"></i>
                                        @else
                                            <i class="fa-regular fa-file-lines text-slate-400 shrink-0"></i>
                                        @endif
                                        <span class="font-mono text-slate-700 text-xs truncate">{{ $item['name'] }}</span>
                                        @if (in_array($item['name'], ['vendor', 'node_modules', '.git']))
                                            <span
                                                class="text-[10px] px-1.5 py-0.5 bg-slate-100 text-slate-400 rounded font-medium shrink-0">auto</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-700 text-xs whitespace-nowrap">
                                    {{ $item['human'] }}
                                </td>
                                <td class="px-6 py-3 hidden sm:table-cell">
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="{{ $bc }} h-1.5 rounded-full"
                                            style="width:{{ $item['percent'] }}%"></div>
                                    </div>
                                </td>
                                <td
                                    class="px-4 py-3 text-right text-xs text-slate-400 hidden sm:table-cell whitespace-nowrap">
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
