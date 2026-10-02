@extends('layouts.public')

@section('title', 'Speed Test Internet')

@section('seo_description', 'Cek kecepatan internet gratis: download, upload, latency (ping), jitter. Ukur bandwidth koneksi Anda secara real-time tanpa install aplikasi.')

@push('head')
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze' }}">
    <meta property="og:locale" content="id_ID">
    <meta property="og:image" content="{{ url('/og-image.png') }}">
    <meta property="twitter:image" content="{{ url('/og-image.png') }}">

    {{-- JSON-LD: WebPage --}}
    <script type="application/ld+json" nonce="{{ csp_nonce() }}">
    {
        "@@context": "https://schema.org",
        "@type": "WebPage",
        "name": "Speed Test Internet",
        "description": "Cek kecepatan internet gratis: download, upload, latency (ping), jitter. Ukur bandwidth koneksi Anda secara real-time tanpa install aplikasi.",
        "url": "{{ url()->current() }}",
        "inLanguage": "id-ID",
        "publisher": {
            "@type": "Organization",
            "name": "{{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze' }}",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ url('/og-image.png') }}",
                "width": 1200,
                "height": 630
            }
        }
    }
    </script>

    {{-- JSON-LD: BreadcrumbList --}}
    <script type="application/ld+json" nonce="{{ csp_nonce() }}">
    {
        "@@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [{
            "@type": "ListItem",
            "position": 1,
            "name": "Beranda",
            "item": "{{ url('/') }}"
        }, {
            "@type": "ListItem",
            "position": 2,
            "name": "Speed Test Internet",
            "item": "{{ url()->current() }}"
        }]
    }
    </script>
@endpush

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
    <div class="mb-8">
        <span class="text-[11px] font-bold text-[#7c3aed] dark:text-[#a78bfa] uppercase tracking-[0.2em] mb-3 block">Network Tools</span>
        <h1 class="text-3xl sm:text-4xl font-black text-[#7c3aed] dark:text-white tracking-tight">Speed Test Internet</h1>
        <p class="text-[#666] dark:text-white/60 text-[15px] mt-2 max-w-2xl leading-relaxed">
            Ukur kecepatan internet Anda: download, upload, latency (ping), dan jitter. Hasil real-time tanpa install aplikasi.
        </p>
    </div>

    <div class="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-6 sm:p-8" x-data="speedTest()" x-init="init()">
        {{-- Status & Controls --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <span class="text-[11px] font-bold text-[#7c3aed] dark:text-[#a78bfa] uppercase tracking-[0.2em] mb-1 block">Status</span>
                <div class="flex items-center gap-3">
                    <span x-show="!running" class="inline-flex items-center gap-1.5 text-sm font-medium text-[#666] dark:text-white/60">
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span> Siap
                    </span>
                    <span x-show="running" class="inline-flex items-center gap-1.5 text-sm font-medium text-[#7c3aed] dark:text-[#a78bfa]">
                        <span class="w-2 h-2 rounded-full bg-[#7c3aed] animate-pulse"></span>
                        <span x-text="currentTest"></span>
                    </span>
                </div>
            </div>
            <button
                @click="runAllTests()"
                :disabled="running"
                class="inline-flex justify-center items-center gap-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-semibold px-6 py-3 rounded-xl transition-colors whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fa-solid fa-play text-sm"></i>
                <span x-show="!running">Mulai Speed Test</span>
                <span x-show="running">Menjalankan...</span>
            </button>
        </div>

        {{-- Results Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-6 text-center">
                <div class="w-14 h-14 mx-auto mb-3 flex items-center justify-center bg-[#f5f0ff] dark:bg-[#7c3aed]/20 rounded-full">
                    <i class="fa-solid fa-download text-2xl text-[#7c3aed] dark:text-[#a78bfa]"></i>
                </div>
                <p class="text-[11px] font-bold text-[#7c3aed] dark:text-[#a78bfa] uppercase tracking-widest mb-1">Download</p>
                <p class="text-3xl sm:text-4xl font-black text-[#7c3aed] dark:text-white" x-text="downloadSpeed + ' Mbps'"></p>
                <p class="text-xs text-[#999] dark:text-white/40 mt-1" x-show="downloadSpeed > 0" x-text="'Server: ' + downloadServer"></p>
            </div>

            <div class="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-6 text-center">
                <div class="w-14 h-14 mx-auto mb-3 flex items-center justify-center bg-[#fef3c7] dark:bg-[#f59e0b]/20 rounded-full">
                    <i class="fa-solid fa-upload text-2xl text-amber-500 dark:text-amber-400"></i>
                </div>
                <p class="text-[11px] font-bold text-amber-500 dark:text-amber-400 uppercase tracking-widest mb-1">Upload</p>
                <p class="text-3xl sm:text-4xl font-black text-amber-500 dark:text-amber-400" x-text="uploadSpeed + ' Mbps'"></p>
                <p class="text-xs text-[#999] dark:text-white/40 mt-1" x-show="uploadSpeed > 0" x-text="'Size: ' + uploadSize + ' MB'"></p>
            </div>

            <div class="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-6 text-center">
                <div class="w-14 h-14 mx-auto mb-3 flex items-center justify-center bg-[#dcfce7] dark:bg-[#22c55e]/20 rounded-full">
                    <i class="fa-solid fa-gauge-high text-2xl text-emerald-500 dark:text-emerald-400"></i>
                </div>
                <p class="text-[11px] font-bold text-emerald-500 dark:text-emerald-400 uppercase tracking-widest mb-1">Latency</p>
                <p class="text-3xl sm:text-4xl font-black text-emerald-500 dark:text-emerald-400" x-text="pingMs + ' ms'"></p>
                <p class="text-xs text-[#999] dark:text-white/40 mt-1" x-show="pingMs > 0" x-text="'Jitter: ' + jitterMs + ' ms'"></p>
            </div>

            <div class="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-6 text-center">
                <div class="w-14 h-14 mx-auto mb-3 flex items-center justify-center bg-[#fce7f3] dark:bg-[#ec4899]/20 rounded-full">
                    <i class="fa-solid fa-signal text-2xl text-pink-500 dark:text-pink-400"></i>
                </div>
                <p class="text-[11px] font-bold text-pink-500 dark:text-pink-400 uppercase tracking-widest mb-1">Jitter</p>
                <p class="text-3xl sm:text-4xl font-black text-pink-500 dark:text-pink-400" x-text="jitterMs + ' ms'"></p>
                <p class="text-xs text-[#999] dark:text-white/40 mt-1" x-show="jitterMs > 0" x-text="'Stability: ' + stability + '%'"></p>
            </div>
        </div>

        {{-- Progress & Logs --}}
        <div x-show="running" class="mb-6 p-4 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 border border-[#e9e0ff] dark:border-[#7c3aed]/30 rounded-xl">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-4 h-4 border-2 border-[#7c3aed] border-t-transparent rounded-full animate-spin"></div>
                <span class="text-sm font-medium text-[#7c3aed] dark:text-[#a78bfa]" x-text="currentTest"></span>
            </div>
            <div class="w-full bg-white dark:bg-[#0d0d18] rounded h-2 overflow-hidden">
                <div class="bg-[#7c3aed] h-full rounded transition-all duration-300" :style="{ width: progress + '%' }"></div>
            </div>
            <p class="text-xs text-[#666] dark:text-white/50 mt-2 text-right" x-text="progress + '% complete'"></p>
        </div>

        {{-- History / Previous Results --}}
        <div x-show="history.length > 0" class="border-t border-[#e5e5e5] dark:border-[#1a1a2e] pt-6">
            <h3 class="text-lg font-semibold text-[#7c3aed] dark:text-white mb-4 flex items-center gap-2">
                <i class="fa-solid fa-history"></i> Riwayat Test
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <template x-for="(result, index) in history.slice().reverse()" :key="index">
                    <div class="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg p-4">
                        <p class="text-[11px] font-bold text-[#7c3aed] dark:text-[#a78bfa] uppercase tracking-widest mb-2" x-text="new Date(result.timestamp).toLocaleString('id-ID')"></p>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div><span class="text-[#666] dark:text-white/50">⬇</span> <span class="font-semibold text-[#7c3aed] dark:text-white" x-text="result.download + ' Mbps'"></span></div>
                            <div><span class="text-[#666] dark:text-white/50">⬆</span> <span class="font-semibold text-amber-500 dark:text-amber-400" x-text="result.upload + ' Mbps'"></span></div>
                            <div><span class="text-[#666] dark:text-white/50">Ping</span> <span class="font-semibold text-emerald-500 dark:text-emerald-400" x-text="result.ping + ' ms'"></span></div>
                            <div><span class="text-[#666] dark:text-white/50">Jitter</span> <span class="font-semibold text-pink-500 dark:text-pink-400" x-text="result.jitter + ' ms'"></span></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Info / Tips --}}
        <div class="mt-8 p-4 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 border border-[#e9e0ff] dark:border-[#7c3aed]/30 rounded-xl">
            <h4 class="font-semibold text-[#7c3aed] dark:text-[#a78bfa] mb-2 flex items-center gap-2">
                <i class="fa-solid fa-circle-info"></i> Tips Akurat
            </h4>
            <ul class="text-sm text-[#666] dark:text-white/60 space-y-1">
                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-xs text-[#7c3aed] dark:text-[#a78bfa] mt-0.5"></i> Tutup aplikasi lain yang menggunakan bandwidth (streaming, download, update)</li>
                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-xs text-[#7c3aed] dark:text-[#a78bfa] mt-0.5"></i> Gunakan kabel LAN (Ethernet) untuk hasil paling stabil</li>
                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-xs text-[#7c3aed] dark:text-[#a78bfa] mt-0.5"></i> Jika Wi-Fi, dekatkan ke router & hindari interferensi (microwave, Bluetooth)</li>
                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-xs text-[#7c3aed] dark:text-[#a78bfa] mt-0.5"></i> Jalankan test beberapa kali untuk rata-rata yang lebih akurat</li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script nonce="{{ csp_nonce() }}">
    window.speedTest = function () {
        return {
            running: false,
            currentTest: '',
            progress: 0,

            downloadSpeed: 0,
            uploadSpeed: 0,
            pingMs: 0,
            jitterMs: 0,
            stability: 100,
            downloadServer: '',
            uploadSize: 0,

            history: [],

            init() {
                // Load history from localStorage
                const saved = localStorage.getItem('speed_test_history');
                if (saved) {
                    try { this.history = JSON.parse(saved); } catch (e) {}
                }
            },

            async runAllTests() {
                this.running = true;
                this.progress = 0;
                this.downloadSpeed = 0;
                this.uploadSpeed = 0;
                this.pingMs = 0;
                this.jitterMs = 0;
                this.stability = 100;

                try {
                    // 1. Ping / Latency
                    await this.testPing();
                    this.progress = 25;

                    // 2. Download
                    await this.testDownload();
                    this.progress = 50;

                    // 3. Upload
                    await this.testUpload();
                    this.progress = 75;

                    // 4. Calculate stability
                    this.calculateStability();
                    this.progress = 100;

                    // Save to history
                    this.saveResult();

                } catch (e) {
                    console.error('Speed test error:', e);
                } finally {
                    this.running = false;
                    this.currentTest = 'Selesai';
                }
            },

            async testPing() {
                this.currentTest = 'Mengukur Latency (Ping)...';
                const samples = 10;
                let total = 0;
                let minPing = Infinity;
                let maxPing = 0;

                for (let i = 0; i < samples; i++) {
                    const start = performance.now();
                    try {
                        await fetch('{{ route('speed-test.ping') }}', { method: 'GET', cache: 'no-cache' });
                    } catch (e) {}
                    const elapsed = performance.now() - start;
                    total += elapsed;
                    minPing = Math.min(minPing, elapsed);
                    maxPing = Math.max(maxPing, elapsed);
                    // Small delay between pings
                    await new Promise(r => setTimeout(r, 50));
                }

                this.pingMs = Math.round(total / samples * 10) / 10;
                this.jitterMs = Math.round((maxPing - minPing) * 10) / 10;
            },

            async testDownload() {
                this.currentTest = 'Mengukur Download Speed...';
                const sizes = [5, 10, 20]; // MB
                let bestSpeed = 0;
                let bestServer = '';

                for (const size of sizes) {
                    const start = performance.now();
                    try {
                        const response = await fetch(`{{ route('speed-test.download') }}?size=${size}`, {
                            method: 'GET',
                            cache: 'no-cache',
                        });
                        if (!response.ok) continue;
                        await response.arrayBuffer();
                    } catch (e) {
                        continue;
                    }
                    const elapsed = (performance.now() - start) / 1000; // seconds
                    const speedMbps = (size * 8) / elapsed; // Mbps
                    if (speedMbps > bestSpeed) {
                        bestSpeed = speedMbps;
                        bestServer = `${size} MB`;
                    }
                    this.downloadSpeed = Math.round(bestSpeed * 10) / 10;
                    this.downloadServer = bestServer;
                    this.progress = 25 + (sizes.indexOf(size) + 1) / sizes.length * 25;
                }
            },

            async testUpload() {
                this.currentTest = 'Mengukur Upload Speed...';
                const sizes = [1, 5, 10]; // MB
                let bestSpeed = 0;

                for (const size of sizes) {
                    const data = new Uint8Array(size * 1024 * 1024);
                    crypto.getRandomValues(data);
                    this.uploadSize = size;

                    const start = performance.now();
                    try {
                        const response = await fetch('{{ route('speed-test.upload') }}', {
                            method: 'POST',
                            body: data,
                            headers: { 'Content-Type': 'application/octet-stream' },
                        });
                        if (!response.ok) continue;
                        await response.json();
                    } catch (e) {
                        continue;
                    }
                    const elapsed = (performance.now() - start) / 1000;
                    const speedMbps = (size * 8) / elapsed;
                    if (speedMbps > bestSpeed) bestSpeed = speedMbps;
                    this.uploadSpeed = Math.round(bestSpeed * 10) / 10;
                    this.progress = 50 + (sizes.indexOf(size) + 1) / sizes.length * 25;
                }
            },

            calculateStability() {
                if (this.pingMs > 0) {
                    // Simple stability: inverse of jitter relative to ping
                    this.stability = Math.max(0, Math.min(100, Math.round(100 - (this.jitterMs / this.pingMs) * 100)));
                }
            },

            saveResult() {
                const result = {
                    timestamp: new Date().toISOString(),
                    download: this.downloadSpeed,
                    upload: this.uploadSpeed,
                    ping: this.pingMs,
                    jitter: this.jitterMs,
                };
                this.history.unshift(result);
                if (this.history.length > 10) this.history.pop();
                localStorage.setItem('speed_test_history', JSON.stringify(this.history));
            },
        };
    };
</script>
@endpush