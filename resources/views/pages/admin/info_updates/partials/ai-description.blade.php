{{--
    Toolbar bantu AI untuk mengisi judul + deskripsi Info Update.
    Pakai: @include('pages.admin.info_updates.partials.ai-description')

    CATATAN PENTING
    Script Alpine ditulis INLINE di dalam container #pjax-container, BUKAN
    lewat @push('scripts'). Navigasi PJAX di resources/views/index.blade.php
    hanya mengeksekusi ulang <script> yang ada DI DALAM container, lalu memanggil
    Alpine.initTree() pada elemen [x-data]. Kalau script-nya ada di @push, saat
    user membuka halaman ini lewat klik di sidebar, komponennya tidak pernah
    terdaftar -> x-data="infoUpdateAiWriter" melempar error -> seluruh
    directive di dalamnya (termasuk x-text) mati, tombolnya jadi kosong.
--}}
<div class="mb-3" x-data="infoUpdateAiWriter">
    {{-- Toolbar: satu baris, nempel di atas textarea --}}
    <div class="inline-flex flex-wrap items-center gap-x-3 gap-y-2 rounded-xl border border-indigo-200/70 dark:border-indigo-500/30 bg-indigo-50/60 dark:bg-indigo-500/[0.07] px-3 py-2">
        <button
            type="button"
            x-on:click="generate"
            x-bind:disabled="loading"
            class="ui-btn ui-btn-sm ui-btn-primary"
        >
            <i class="fa-solid fa-wand-magic-sparkles text-[10px]" x-bind:class="loading && 'fa-spin'"></i>
            {{-- Teks ditulis statis sebagai fallback; x-text menimpanya kalau Alpine jalan --}}
            <span x-text="loading ? 'Menyusun…' : 'Buat deskripsi dengan AI'">Buat deskripsi dengan AI</span>
        </button>

        <button
            type="button"
            x-show="filled"
            x-cloak
            x-on:click="generate"
            x-bind:disabled="loading"
            class="whitespace-nowrap text-xs font-semibold text-indigo-600 dark:text-indigo-300 hover:text-indigo-800 dark:hover:text-indigo-200 transition"
        >
            Buat ulang
        </button>

        <span x-show="filled" x-cloak class="hidden sm:block h-3.5 w-px bg-indigo-200 dark:bg-indigo-500/30"></span>

        <button
            type="button"
            x-on:click="briefOpen = !briefOpen"
            class="whitespace-nowrap text-xs font-medium text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 transition inline-flex items-center gap-1"
        >
            <i class="fa-solid fa-sliders text-[9px]"></i>
            <span x-text="briefOpen ? 'Sembunyikan detail' : 'Tambah detail untuk AI'">Tambah detail untuk AI</span>
        </button>

        <span class="hidden xl:inline whitespace-nowrap text-[11px] text-slate-400 dark:text-slate-500 border-l border-indigo-200/70 dark:border-indigo-500/30 pl-3">
            Isi sendiri atau pakai AI
        </span>
    </div>

    {{-- Panel detail (opsional) --}}
    <div x-show="briefOpen" x-cloak x-transition.opacity class="mt-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 p-3">
        <label for="ai_brief" class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">
            Detail untuk AI <span class="font-normal text-slate-400">(opsional)</span>
        </label>
        <textarea
            id="ai_brief"
            rows="3"
            x-model="brief"
            placeholder="Contoh: deploy GitHub otomatis, mulai 12 Oktober pukul 23:00 selama 30 menit, tidak berdampak ke email"
            class="ui-field ui-field-sm text-slate-700 dark:text-slate-200"
        ></textarea>
        <p class="mt-1.5 text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
            <i class="fa-solid fa-circle-info mr-1"></i>
            AI hanya menyusun kalimat dari detail di sini — jangan menuliskan angka atau tanggal yang tidak ingin kamu tayangkan.
        </p>
    </div>

    <p x-show="error" x-cloak class="mt-2 text-xs text-red-500 dark:text-red-400" x-text="error"></p>
</div>

<script nonce="{{ csp_nonce() }}">
    (function () {
        if (window.__infoUpdateAiWriterRegistered) return;
        window.__infoUpdateAiWriterRegistered = true;

        function register() {
            Alpine.data('infoUpdateAiWriter', () => ({
                loading: false,
                filled: false,
                error: '',
                brief: '',
                briefOpen: false,

                async generate() {
                    const titleInput = document.getElementById('title');
                    const descInput = document.getElementById('description');
                    const categoryInput = document.getElementById('category');
                    if (!descInput) return;

                    this.loading = true;
                    this.error = '';

                    try {
                        const response = await fetch('{{ route('superadmin.info_updates.generate_description') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({
                                title: titleInput ? titleInput.value : '',
                                category: categoryInput ? categoryInput.value : '',
                                brief: this.brief,
                                with_title: ! (titleInput && titleInput.value.trim()),
                            }),
                        });

                        const payload = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            throw new Error(payload.message || 'Gagal membuat deskripsi AI. Coba lagi.');
                        }

                        if (payload.title && titleInput && !titleInput.value.trim()) {
                            titleInput.value = payload.title;
                            titleInput.dispatchEvent(new Event('input', { bubbles: true }));
                        }

                        descInput.value = payload.description;
                        descInput.dispatchEvent(new Event('input', { bubbles: true }));
                        this.filled = true;
                    } catch (e) {
                        this.error = e.message || 'Gagal membuat deskripsi AI. Coba lagi.';
                    } finally {
                        this.loading = false;
                    }
                },
            }));
        }

        // Full page load -> Alpine belum jalan, tunggu event alpine:init.
        // Navigasi PJAX -> Alpine sudah jalan, daftar saat ini juga.
        if (window.Alpine && window.Alpine.version) {
            register();
        } else {
            document.addEventListener('alpine:init', register);
        }
    })();
</script>