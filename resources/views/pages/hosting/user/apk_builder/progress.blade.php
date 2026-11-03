@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Proses Build APK"
            eyebrow="Android App Builder"
            subtitle="Menampilkan proses kompilasi aplikasi {{ $build->app_name }} secara real-time"
            icon="fa-solid fa-microchip"
            iconColor="indigo">
            <x-slot:actions>
                <a href="{{ route('user_hosting.apk.index') }}" class="ui-btn ui-btn-md ui-btn-secondary">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    Kembali ke Daftar
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="ui-panel flex flex-col overflow-hidden" style="height: 600px;">
            <div class="flex items-center justify-between border-b border-[#E3EAF1] bg-[#F4F8FC] px-4 py-3 dark:border-[#2A4463] dark:bg-[#12233A]">
                <div class="flex items-center gap-3">
                    <div class="flex gap-1.5">
                        <span class="h-3 w-3 rounded-full bg-rose-500"></span>
                        <span class="h-3 w-3 rounded-full bg-amber-500"></span>
                        <span class="h-3 w-3 rounded-full bg-emerald-500"></span>
                    </div>
                    <span class="font-mono text-xs text-[#525252] dark:text-white/50">Terminal — {{ $build->package_name }}</span>
                </div>
                <div id="statusBadge">
                    @if ($build->status === 'success')
                        <span class="ui-badge ui-badge-success"><i class="fa-solid fa-check"></i> Selesai</span>
                    @elseif ($build->status === 'failed')
                        <span class="ui-badge ui-badge-danger"><i class="fa-solid fa-xmark"></i> Gagal</span>
                    @else
                        <span class="ui-badge ui-badge-primary"><i class="fa-solid fa-spinner fa-spin"></i> Memproses</span>
                    @endif
                </div>
            </div>

            <pre id="logContent" class="flex-1 overflow-y-auto whitespace-pre-wrap break-words bg-[#0A1322] p-6 font-mono text-sm leading-relaxed text-emerald-300 focus:outline-none"></pre>

            <div id="downloadSection" class="flex flex-col gap-3 border-t border-[#2A4463] bg-[#0F1D30] p-4 sm:flex-row sm:items-center sm:justify-between {{ $build->status === 'success' ? '' : 'hidden' }}">
                <p class="text-sm text-emerald-300"><i class="fa-solid fa-circle-check mr-1"></i> Build berhasil! APK Anda siap diunduh.</p>
                <a href="{{ route('user_hosting.apk.download', $build->id) }}" data-pjax="false" download class="ui-btn ui-btn-md ui-btn-success">
                    <i class="fa-solid fa-download"></i> Download APK
                </a>
            </div>
        </div>
    </x-ui.page-layout>

    <script nonce="{{ app('csp_nonce') }}">
        (function () {
            const pre = document.getElementById('logContent');
            const statusBadge = document.getElementById('statusBadge');
            const downloadSection = document.getElementById('downloadSection');

            if (!pre) return;

            let currentStatus = @js($build->status);
            let initialLog = @js($build->log_output);

            pre.textContent = initialLog || 'Menyiapkan proses build...';
            pre.scrollTop = pre.scrollHeight;

            if (window.apkBuildInterval) {
                clearInterval(window.apkBuildInterval);
            }

            const successBadge = '<span class="ui-badge ui-badge-success"><i class="fa-solid fa-check"></i> Selesai</span>';
            const failedBadge = '<span class="ui-badge ui-badge-danger"><i class="fa-solid fa-xmark"></i> Gagal</span>';

            if (currentStatus === 'pending' || currentStatus === 'building') {
                if (!initialLog) {
                    pre.textContent += '\n[LIVE] Menyambungkan ke worker...';
                }

                window.apkBuildInterval = setInterval(() => {
                    if (!document.getElementById('logContent')) {
                        clearInterval(window.apkBuildInterval);
                        return;
                    }

                    fetch(`/user/hosting/apk/{{ $build->id }}/log`)
                        .then(r => r.json())
                        .then(data => {
                            if (data.log && data.log !== pre.textContent) {
                                pre.textContent = data.log;
                                pre.scrollTop = pre.scrollHeight;
                            }

                            if (data.status === 'success' || data.status === 'failed') {
                                clearInterval(window.apkBuildInterval);
                                pre.textContent += `\n\n[SISTEM] Proses selesai dengan status: ${data.status.toUpperCase()}`;
                                pre.scrollTop = pre.scrollHeight;

                                if (data.status === 'success') {
                                    statusBadge.innerHTML = successBadge;
                                    downloadSection.classList.remove('hidden');
                                } else {
                                    statusBadge.innerHTML = failedBadge;
                                }
                            }
                        })
                        .catch(err => console.error('Gagal fetch log', err));
                }, 2000);
            }
        })();
    </script>
@endsection
