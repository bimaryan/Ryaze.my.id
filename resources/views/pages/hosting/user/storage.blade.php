@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50">

        {{-- ── 11. USER HOSTING – Storage Overview ────────────────────────── --}}
        <div class="p-5 bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="shrink-0 w-11 h-11 flex items-center justify-center bg-emerald-50 text-emerald-600 rounded-lg">
                    <i class="fa-solid fa-hard-drive text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Storage</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Monitor penggunaan disk seluruh project Anda.</p>
                </div>
            </div>
            <a href="{{ route('user_hosting.dashboard') }}" class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                &larr; Kembali
            </a>
        </div>

        {{-- Total Usage Card --}}
        @php
            $barColor = $percent >= 90 ? 'bg-rose-500' : ($percent >= 70 ? 'bg-amber-500' : 'bg-indigo-500');
            $textColor = $percent >= 90 ? 'text-rose-600' : ($percent >= 70 ? 'text-amber-600' : 'text-indigo-600');
            $bgLight =
                $percent >= 90
                    ? 'bg-rose-50 border-rose-200'
                    : ($percent >= 70
                        ? 'bg-amber-50 border-amber-200'
                        : 'bg-indigo-50 border-indigo-200');
        @endphp

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6 mt-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Total Penggunaan</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-bold text-slate-800">{{ $total_human }}</span>
                        <span class="text-slate-400 text-sm">/ {{ $limit_human }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <span
                        class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold {{ $bgLight }} {{ $textColor }} border">
                        @if ($percent >= 90)
                            <i class="fa-solid fa-triangle-exclamation mr-1.5"></i>
                        @elseif($percent >= 70)
                            <i class="fa-solid fa-circle-exclamation mr-1.5"></i>
                        @else
                            <i class="fa-solid fa-hard-drive mr-1.5"></i>
                        @endif
                        {{ $percent }}% terpakai
                    </span>
                </div>
            </div>

            {{-- Progress bar --}}
            <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                <div class="{{ $barColor }} h-3 rounded-full transition-all duration-700"
                    style="width: {{ $percent }}%"></div>
            </div>
            <div class="flex justify-between mt-2 text-xs text-slate-400">
                <span>{{ $total_human }} digunakan</span>
                <span>{{ $limit_human }} batas</span>
            </div>

            @if ($percent >= 90)
                <div class="mt-4 bg-rose-50 border border-rose-200 rounded-lg px-4 py-3 flex items-start gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500 mt-0.5"></i>
                    <p class="text-rose-700 text-sm">Storage hampir penuh! Hapus file yang tidak diperlukan atau hubungi
                        admin untuk upgrade kapasitas.</p>
                </div>
            @elseif($percent >= 70)
                <div class="mt-4 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation text-amber-500 mt-0.5"></i>
                    <p class="text-amber-700 text-sm">Penggunaan storage melebihi 70%. Pertimbangkan untuk membersihkan file
                        yang tidak diperlukan.</p>
                </div>
            @endif
        </div>

        {{-- Per-project breakdown --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="font-bold text-slate-800 text-sm">Penggunaan Per Project</h2>
                <span class="text-xs text-slate-400">{{ count($items) }} project</span>
            </div>

            @if (count($items) === 0)
                <div class="px-6 py-16 text-center">
                    <i class="fa-solid fa-box-open text-slate-200 text-5xl mb-4"></i>
                    <p class="text-slate-400 font-medium">Belum ada project yang di-deploy.</p>
                </div>
            @else
                <div class="divide-y divide-slate-50">
                    @foreach ($items as $item)
                        @php
                            $pc = $item['percent'];
                            $bc = $pc >= 90 ? 'bg-rose-500' : ($pc >= 70 ? 'bg-amber-500' : 'bg-indigo-400');
                        @endphp
                        <div class="px-6 py-4 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center justify-between gap-4 mb-2">
                                <div class="flex items-center gap-3 min-w-0">
                                    {{-- Framework icon --}}
                                    <div
                                        class="w-9 h-9 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center shrink-0">
                                        @if ($item['project']->framework == 'react')
                                            <i class="fa-brands fa-react text-sky-500"></i>
                                        @elseif($item['project']->framework == 'nextjs')
                                            <i class="fa-brands fa-node-js text-slate-700"></i>
                                        @elseif($item['project']->framework == 'laravel')
                                            <i class="fa-brands fa-laravel text-red-500"></i>
                                        @elseif($item['project']->framework == 'python')
                                            <i class="fa-brands fa-python text-yellow-500"></i>
                                        @elseif($item['project']->framework == 'node')
                                            <i class="fa-brands fa-node text-emerald-500"></i>
                                        @elseif($item['project']->framework == 'vue')
                                            <i class="fa-brands fa-vuejs text-emerald-500"></i>
                                        @else
                                            <i class="fa-brands fa-html5 text-orange-500"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 text-sm truncate">
                                            {{ $item['project']->project_name }}</p>
                                        <p class="text-xs text-slate-400 font-mono truncate">{{ $item['dir'] }}</p>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="font-bold text-slate-700 text-sm">{{ $item['used_human'] }}</p>
                                    <p class="text-xs text-slate-400">{{ $pc }}%</p>
                                </div>
                            </div>
                            {{-- Mini progress bar --}}
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="{{ $bc }} h-1.5 rounded-full transition-all duration-500"
                                    style="width: {{ $pc }}%"></div>
                            </div>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-slate-400">
                                    <i
                                        class="fa-solid fa-circle text-xs mr-1
                            {{ $item['project']->status == 'active' ? 'text-emerald-400' : ($item['project']->status == 'building' ? 'text-amber-400' : 'text-rose-400') }}"></i>
                                    {{ $item['project']->status }}
                                </span>
                                <a href="{{ route('user_hosting.storage.detail', $item['project']->hashid) }}"
                                    class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-1">
                                    Detail <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Info --}}
        <p class="text-xs text-slate-400 mt-4 flex items-center gap-1.5">
            <i class="fa-solid fa-circle-info text-slate-300"></i>
            Limit storage gabungan: <strong>{{ $limit_human }}</strong>. Data diperbarui setiap kali halaman dimuat.
        </p>

    </div>
@endsection
