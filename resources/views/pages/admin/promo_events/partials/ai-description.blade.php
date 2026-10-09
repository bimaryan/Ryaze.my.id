{{-- Tombol bantu AI untuk mengisi deskripsi promo. Pakai: @include('pages.admin.promo_events.partials.ai-description') --}}
<div class="mb-2" x-data="promoAiWriter">
    <div class="flex flex-wrap items-center gap-2">
        <button
            type="button"
            x-on:click="generate"
            x-bind:disabled="loading"
            class="inline-flex items-center gap-2 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 border border-indigo-200 dark:border-indigo-500/40 text-indigo-700 dark:text-indigo-300 text-xs font-semibold px-3 py-1.5 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <i class="fa-solid fa-wand-magic-sparkles" x-bind:class="loading && 'fa-spin'"></i>
            <span x-text="loading ? 'Menyusun...' : 'Buat deskripsi dengan AI'"></span>
        </button>
        <button
            type="button"
            x-show="filled"
            x-on:click="generate"
            x-bind:disabled="loading"
            class="text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition"
        >
            Buat ulang
        </button>
    </div>

    <div class="mt-2" x-show="briefOpen" x-cloak>
        <label for="ai_brief" class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">
            Catatan untuk AI <span class="font-normal text-slate-400">(opsional)</span>
        </label>
        <textarea
            id="ai_brief"
            rows="2"
            x-model="brief"
            placeholder="Contoh: tonongkapi, garansi migrasi gratis, tanpa kode voucher"
            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"
        ></textarea>
    </div>

    <button
        type="button"
        x-on:click="briefOpen = !briefOpen"
        class="mt-1.5 text-[11px] font-medium text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition"
    >
        <span x-text="briefOpen ? 'Sembunyikan catatan' : 'Tambah catatan untuk AI'"></span>
    </button>

    <p x-show="error" x-cloak class="mt-2 text-xs text-red-500 dark:text-red-400" x-text="error"></p>
</div>

@once
    @push('scripts')
        <script nonce="{{ csp_nonce() }}">
            document.addEventListener('alpine:init', () => {
                Alpine.data('promoAiWriter', () => ({
                    loading: false,
                    filled: false,
                    error: '',
                    brief: '',
                    briefOpen: false,

                    async generate() {
                        const titleInput = document.getElementById('title');
                        const descInput = document.getElementById('description');
                        if (!descInput) return;

                        this.loading = true;
                        this.error = '';

                        try {
                            const response = await fetch('{{ route('admin.promo_events.generate_description') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify({
                                    title: titleInput ? titleInput.value : '',
                                    brief: this.brief,
                                }),
                            });

                            const payload = await response.json().catch(() => ({}));

                            if (!response.ok) {
                                throw new Error(payload.message || 'Gagal membuat deskripsi AI. Coba lagi.');
                            }

                            if (payload.title && titleInput && !titleInput.value.trim()) {
                                titleInput.value = payload.title;
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
            });
        </script>
    @endpush
@endonce