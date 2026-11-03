@extends('layouts.public')

@section('title', 'WHOIS Domain Lookup')
@section('seo_description', 'Cek informasi registrasi domain WHOIS/RDAP gratis: registrar, tanggal registrasi & kedaluwarsa, nameserver, status, dan record DNS.')

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
        "name": "WHOIS Domain Lookup",
        "description": "Cek informasi registrasi domain WHOIS/RDAP gratis: registrar, tanggal registrasi & kedaluwarsa, nameserver, status, dan record DNS.",
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
            "name": "WHOIS Domain Lookup",
            "item": "{{ url()->current() }}"
        }]
    }
    </script>
@endpush

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        <div class="mb-8">
            <p class="ui-eyebrow mb-3">Domain Tools</p>
            <h1 class="ui-display text-3xl sm:text-4xl leading-[0.98] tracking-[-0.04em]">WHOIS Domain Lookup</h1>
            <p class="text-[#525252] dark:text-white/60 text-[15px] mt-3 max-w-2xl leading-relaxed">
                Cek informasi registrasi &amp; DNS suatu domain (registrar, tanggal kedaluwarsa, nameserver, status, dan record DNS).
            </p>
        </div>

        <div class="ui-panel p-6 sm:p-8" x-data="whoisLookup()">
            {{-- Form Pencarian --}}
            <form id="whois-form" class="max-w-3xl" @submit.prevent="search()">
                @csrf
                <label for="domain-input" class="ui-eyebrow-muted block mb-3">
                    Nama Domain <span class="text-rose-500 dark:text-rose-400">*</span>
                </label>
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <i class="fa-solid fa-globe text-[#8A9BAE] dark:text-white/40"></i>
                        </div>
                        <input
                            type="text"
                            id="domain-input"
                            name="domain"
                            placeholder="contoh: ryaze.my.id"
                            autocomplete="off"
                            spellcheck="false"
                            required
                            class="w-full pl-11 pr-4 py-3 border border-[#E3EAF1] dark:border-[#2A4463] bg-white dark:bg-[#0A1322] text-[#525252] dark:text-white/80 placeholder-[#94A3B4] dark:placeholder-white/30 focus:outline-none focus:border-[#1D5B96] transition font-mono text-sm">
                    </div>
                    <button
                        type="submit"
                        id="whois-submit"
                        class="ui-btn ui-btn-primary whitespace-nowrap">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                        <span id="whois-submit-text">Cek Domain</span>
                    </button>
                </div>
            </form>

            {{-- Area Hasil --}}
            <div id="whois-result" class="mt-8" x-cloak>
                {{-- Loading State --}}
                <div x-show="loading" class="flex flex-col items-center justify-center py-16 gap-4">
                    <div class="relative">
                        <div class="w-16 h-16 border-4 border-[#E8F0F9] dark:border-[#1D5B96]/30"></div>
                        <div class="absolute top-0 left-0 w-16 h-16 border-4 border-transparent border-t-[#1D5B96] animate-spin"></div>
                    </div>
                    <p class="ui-eyebrow-muted">Mengambil data domain...</p>
                    <p class="text-xs text-[#8A9BAE] dark:text-white/45" x-text="loadingDomain"></p>
                </div>

                {{-- Error State --}}
                <div x-show="error" class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/40 p-5 flex items-start gap-4">
                    <div class="w-10 h-10 bg-rose-100 dark:bg-rose-500/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600 dark:text-rose-400"></i>
                    </div>
                    <div>
                        <p class="font-bold text-rose-800 dark:text-rose-300 text-sm mb-1">Gagal mengambil data</p>
                        <p class="text-sm text-rose-600 dark:text-rose-400/90 leading-relaxed" x-text="errorMessage"></p>
                    </div>
                </div>

                {{-- Domain Available (belum terdaftar) --}}
                <div x-show="!loading && !error && result && !result.registered" class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/40 p-6 flex items-start gap-4">
                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold text-emerald-800 dark:text-emerald-300 text-base mb-1">Domain Tersedia</p>
                        <p class="text-sm text-emerald-700 dark:text-emerald-400/90" x-text="result?.message"></p>
                        <p class="text-xs text-emerald-600/70 dark:text-emerald-500/70 mt-2">Catatan: hasil RDAP bisa tertunda. Pastikan dengan registrar sebelum membeli.</p>
                    </div>
                </div>

                {{-- Hasil Terdaftar --}}
                <div x-show="!loading && !error && result && result.registered" class="space-y-6">
                    {{-- Header Domain --}}
                    <div class="flex flex-wrap items-center justify-between gap-4 pb-5 border-b border-[#EDF2F7] dark:border-[#2A4463]">
                        <div class="flex items-center gap-3">
                            <div class="ui-icon-box w-11 h-11">
                                <i class="fa-solid fa-globe text-[#1D5B96] dark:text-[#6BA8DC] text-lg"></i>
                            </div>
                            <div>
                                <p class="font-mono font-bold text-lg text-[#0F1A2A] dark:text-white" x-text="result?.domain"></p>
                                <p class="ui-eyebrow-muted">Domain terdaftar</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="copyRaw()"
                            class="ui-btn ui-btn-secondary">
                            <i class="fa-regular fa-copy"></i> Salin Ringkasan
                        </button>
                    </div>

                    {{-- Grid Info Utama --}}
                    <div class="ui-grid grid-cols-1 md:grid-cols-2">
                        <div class="ui-cell px-5 py-4">
                            <p class="ui-eyebrow-muted mb-1.5">Registrar</p>
                            <p class="text-sm font-semibold text-[#0F1A2A] dark:text-white" x-text="result?.whois?.registrar || 'Tidak tersedia'"></p>
                        </div>
                        <div class="ui-cell px-5 py-4">
                            <p class="ui-eyebrow-muted mb-1.5">DNSSEC</p>
                            <p class="text-sm font-semibold flex items-center gap-1.5"
                               :class="result?.whois?.dnssec?.enabled ? 'text-emerald-600 dark:text-emerald-400' : 'text-[#525252] dark:text-[#8A9BAE]'">
                                <i class="fa-solid" :class="result?.whois?.dnssec?.enabled ? 'fa-shield-halved' : 'fa-shield'"></i>
                                <span x-text="result?.whois?.dnssec?.enabled ? 'Aktif (signed)' : 'Tidak aktif'"></span>
                            </p>
                        </div>
                        <div class="ui-cell px-5 py-4">
                            <p class="ui-eyebrow-muted mb-1.5">Tanggal Registrasi</p>
                            <p class="text-sm font-semibold text-[#0F1A2A] dark:text-white" x-text="formatDate(result?.whois?.registered_at)"></p>
                        </div>
                        <div class="ui-cell px-5 py-4">
                            <p class="ui-eyebrow-muted mb-1.5">Tanggal Kedaluwarsa</p>
                            <p class="text-sm font-semibold flex items-center gap-1.5" x-text="formatDate(result?.whois?.expires_at)"></p>
                        </div>
                        <div class="ui-cell px-5 py-4 md:col-span-2">
                            <p class="ui-eyebrow-muted mb-1.5">Update Terakhir</p>
                            <p class="text-sm font-semibold text-[#0F1A2A] dark:text-white" x-text="formatDate(result?.whois?.updated_at)"></p>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div x-show="result?.whois?.status && result.whois.status.length > 0">
                        <h3 class="ui-eyebrow-muted mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-flag"></i> STATUS DOMAIN
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="status in (result?.whois?.status || [])" :key="status">
                                <span class="inline-flex items-center text-[11px] font-medium px-2.5 py-1 bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/30 font-mono">
                                    <span x-text="status"></span>
                                </span>
                            </template>
                        </div>
                    </div>

                    {{-- Nameservers --}}
                    <div x-show="result?.whois?.nameservers && result.whois.nameservers.length > 0">
                        <h3 class="ui-eyebrow-muted mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-server"></i> NAMESERVER
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                            <template x-for="ns in (result?.whois?.nameservers || [])" :key="ns">
                                <div class="flex items-center gap-2.5 ui-cell-alt border border-[#EDF2F7] dark:border-[#2A4463] px-3.5 py-2.5">
                                    <i class="fa-solid fa-dns text-[#C9D5E2] dark:text-white/45 text-xs"></i>
                                    <span class="font-mono text-xs text-[#0F1A2A] dark:text-white/70" x-text="ns"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Abuse Contact --}}
                    <div x-show="result?.whois?.abuse && (result.whois.abuse.email || result.whois.abuse.phone)">
                        <h3 class="ui-eyebrow-muted mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation"></i> KONTAK ABUSE REGISTRAR
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            <template x-for="(value, key) in { email: result?.whois?.abuse?.email, phone: result?.whois?.abuse?.phone }" :key="key">
                                <div x-show="value" class="inline-flex items-center gap-2 ui-cell-alt border border-[#EDF2F7] dark:border-[#2A4463] px-3.5 py-2.5">
                                    <i class="fa-solid text-[#C9D5E2] dark:text-white/45 text-xs" :class="key === 'email' ? 'fa-envelope' : 'fa-phone'"></i>
                                    <span class="text-xs text-[#0F1A2A] dark:text-white/70 font-mono" x-text="value"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- DNS Records --}}
                    <div>
                        <h3 class="ui-eyebrow-muted mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-network-wired"></i> RECORD DNS PUBLIK
                        </h3>

                        <div x-show="!hasDnsRecords()" class="text-xs text-[#8A9BAE] dark:text-white/45 italic bg-[#F4F8FC] dark:bg-[#12233A] px-4 py-3 border border-dashed border-[#E3EAF1] dark:border-[#2A4463]/60">
                            Tidak ada record DNS yang ditemukan oleh resolver (mungkin domain belum di-delegasikan atau resolver gagal).
                        </div>

                        <div class="space-y-4" x-show="hasDnsRecords()">
                            <template x-for="(records, type) in (result?.dns || {})" :key="type">
                                <div x-show="records && records.length > 0">
                                    <p class="text-[11px] font-bold uppercase tracking-wide text-[#8A9BAE] dark:text-white/45 mb-2">
                                        <span x-text="type"></span> <span x-text="`(${records?.length})`"></span>
                                    </p>
                                    <div class="flex flex-col gap-1">
                                        <template x-for="(record, idx) in (records || [])" :key="type + '-' + idx">
                                            <div class="flex items-center gap-2.5 ui-cell-alt border border-[#EDF2F7] dark:border-[#2A4463] px-3.5 py-2">
                                                <span class="font-mono text-xs text-[#0F1A2A] dark:text-white/70 break-all" x-text="record.ip || record.host || record.value"></span>
                                                <span x-show="record.priority !== undefined" class="ml-auto text-[10px] font-mono text-[#8A9BAE] dark:text-white/45 bg-[#F0F6FC] dark:bg-[#0F1D30] px-1.5 py-0.5">
                                                    prio <span x-text="record.priority"></span>
                                                </span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Empty State (sebelum pencarian pertama) --}}
                <div x-show="!loading && !error && !result" class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-20 h-20 bg-[#F0F6FC] dark:bg-[#0F1D30] flex items-center justify-center mb-5">
                        <i class="fa-solid fa-magnifying-glass text-3xl text-[#C9D5E2] dark:text-white/45"></i>
                    </div>
                    <p class="text-sm font-semibold text-[#525252] dark:text-white/70 mb-1.5">Belum ada domain yang dicek</p>
                    <p class="text-xs text-[#8A9BAE] dark:text-white/45 max-w-sm leading-relaxed">Masukkan nama domain pada kolom di atas, lalu klik <strong>Cek Domain</strong> untuk melihat detail registrasi & DNS-nya.</p>
                </div>
            </div>
        </div>
    </div>

    <script nonce="{{ csp_nonce() }}">
        // Daftarkan sebagai global function (bukan via event alpine:init) agar
        // tetap tersedia saat Alpine meng-inisialisasi elemen x-data halaman ini.
        window.whoisLookup = function () {
            return {
                loading: false,
                error: false,
                errorMessage: '',
                loadingDomain: '',
                result: null,

                init() {
                    // Submit-in ditangani lewat @submit.prevent="search()" pada <form>,
                    // sehingga preventDefault selalu terpasang dalam scope Alpine.
                },

                search() {
                    const input = document.getElementById('domain-input');
                    const domain = input.value.trim();

                    if (!domain) return;

                    this.loading = true;
                    this.error = false;
                    this.errorMessage = '';
                    this.result = null;
                    this.loadingDomain = domain;

                    const submitBtn = document.getElementById('whois-submit');
                    const submitText = document.getElementById('whois-submit-text');
                    submitBtn.disabled = true;
                    submitText.textContent = 'Mencari...';

                    fetch('{{ route('whois.lookup') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ domain }),
                    })
                        .then(async (res) => {
                            const data = await res.json();
                            if (!res.ok) {
                                throw new Error(data.message || `Server merespons HTTP ${res.status}`);
                            }
                            return data;
                        })
                        .then((data) => {
                            this.result = data;
                        })
                        .catch((err) => {
                            this.error = true;
                            this.errorMessage = err.message || 'Terjadi kesalahan tidak terduga.';
                        })
                        .finally(() => {
                            this.loading = false;
                            submitBtn.disabled = false;
                            submitText.textContent = 'Cek Domain';
                        });
                },

                hasDnsRecords() {
                    if (!this.result || !this.result.dns) return false;
                    return Object.values(this.result.dns).some((r) => r && r.length > 0);
                },

                formatDate(iso) {
                    if (!iso) return 'Tidak tersedia';
                    const d = new Date(iso);
                    if (isNaN(d)) return iso;
                    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                },

                async copyRaw() {
                    if (!this.result) return;
                    const w = this.result.whois || {};
                    const lines = [
                        `Domain: ${this.result.domain}`,
                        `Registrar: ${w.registrar || '-'}`,
                        `Registrasi: ${this.formatDate(w.registered_at)}`,
                        `Kedaluwarsa: ${this.formatDate(w.expires_at)}`,
                        `Update: ${this.formatDate(w.updated_at)}`,
                        `DNSSEC: ${w.dnssec?.enabled ? 'Aktif' : 'Tidak aktif'}`,
                        w.nameservers?.length ? `Nameserver:\n${w.nameservers.map((n) => `  - ${n}`).join('\n')}` : null,
                    ].filter(Boolean);

                    try {
                        await navigator.clipboard.writeText(lines.join('\n'));
                        Swal.fire({ icon: 'success', title: 'Tersalin!', text: 'Ringkasan domain disalin ke clipboard.', timer: 1800, showConfirmButton: false, toast: true, position: 'top-end' });
                    } catch (e) {
                        Swal.fire({ icon: 'error', title: 'Gagal menyalin', text: 'Browser memblokir akses clipboard.' });
                    }
                },
            };
        };
    </script>
@endsection
