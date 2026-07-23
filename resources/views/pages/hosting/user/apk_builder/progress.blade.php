@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Proses Build APK"
            subtitle="Menampilkan proses kompilasi aplikasi {{ $build->app_name }} secara real-time"
            icon="fa-solid fa-microchip"
            iconColor="indigo">
            <x-slot:actions>
                <a href="{{ route('user_hosting.apk.index') }}"
                    class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    Kembali ke Daftar
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="bg-slate-950 rounded-xl shadow-lg border border-slate-800 overflow-hidden flex flex-col mt-4" style="height: 600px;">
            <div class="bg-slate-900 border-b border-slate-800 px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                    </div>
                    <span class="text-slate-400 text-xs font-mono">Terminal - {{ $build->package_name }}</span>
                </div>
                <div id="statusBadge">
                    @if($build->status === 'success')
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-900/50 text-emerald-400 border border-emerald-800"><i class="fa-solid fa-check mr-1"></i> Selesai</span>
                    @elseif($build->status === 'failed')
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-rose-900/50 text-rose-400 border border-rose-800"><i class="fa-solid fa-xmark mr-1"></i> Gagal</span>
                    @else
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-indigo-900/50 text-indigo-400 border border-indigo-800"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Memproses...</span>
                    @endif
                </div>
            </div>
            <pre id="logContent" class="flex-1 overflow-y-auto p-6 text-sm font-mono text-green-400 leading-relaxed whitespace-pre-wrap focus:outline-none"></pre>
            
            <div id="downloadSection" class="bg-slate-900 border-t border-slate-800 p-4 flex justify-between items-center {{ $build->status === 'success' ? '' : 'hidden' }}">
                <p class="text-emerald-400 text-sm"><i class="fa-solid fa-party-horn mr-1"></i> Build berhasil! APK Anda siap diunduh.</p>
                <a href="{{ route('user_hosting.apk.download', $build->id) }}" data-pjax="false" download class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg font-bold transition-colors">
                    <i class="fa-solid fa-download"></i> Download APK
                </a>
            </div>
        </div>
    </x-ui.page-layout>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pre = document.getElementById('logContent');
            const statusBadge = document.getElementById('statusBadge');
            const downloadSection = document.getElementById('downloadSection');
            let currentStatus = '{{ $build->status }}';
            let initialLog = {!! json_encode($build->log_output) !!};
            
            pre.textContent = initialLog || 'Menyiapkan proses build...';
            pre.scrollTop = pre.scrollHeight;

            if (currentStatus === 'pending' || currentStatus === 'building') {
                pre.textContent += '\n[LIVE] Menyambungkan ke worker...';
                
                const logInterval = setInterval(() => {
                    fetch(`/user/hosting/apk/{{ $build->id }}/log`)
                        .then(r => r.json())
                        .then(data => {
                            if (data.log && data.log !== pre.textContent) {
                                pre.textContent = data.log;
                                pre.scrollTop = pre.scrollHeight;
                            }
                            
                            if (data.status === 'success' || data.status === 'failed') {
                                clearInterval(logInterval);
                                pre.textContent += `\n\n[SISTEM] Proses selesai dengan status: ${data.status.toUpperCase()}`;
                                pre.scrollTop = pre.scrollHeight;
                                
                                if (data.status === 'success') {
                                    statusBadge.innerHTML = '<span class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-900/50 text-emerald-400 border border-emerald-800"><i class="fa-solid fa-check mr-1"></i> Selesai</span>';
                                    downloadSection.classList.remove('hidden');
                                } else {
                                    statusBadge.innerHTML = '<span class="text-xs font-bold px-2.5 py-1 rounded-full bg-rose-900/50 text-rose-400 border border-rose-800"><i class="fa-solid fa-xmark mr-1"></i> Gagal</span>';
                                }
                            }
                        })
                        .catch(err => console.error('Gagal fetch log', err));
                }, 2000);
            }
        });
    </script>
@endsection
