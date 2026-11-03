{{-- Tombol bantu AI untuk mengisi deskripsi promo. Pakai: @include('pages.admin.promo_events.partials.ai-description') --}}
<div class="mb-2" x-data="promoAiWriter">
    <div class="flex flex-wrap items-center gap-2">
        <button
            type="button"
            x-on:click="generate"
            x-bind:disabled="loading"
            class="inline-flex items-center gap-2 bg-[#E8F0F9] dark:bg-[#1D5B96]/10 hover:bg-[#1D5B96] dark:hover:bg-[#1D5B96] border border-[#C9D5E2] dark:border-[#1D5B96]/40 text-[#1D5B96] dark:text-[#6BA8DC] text-xs font-semibold px-3 py-1.5  transition disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <i class="fa-solid fa-wand-magic-sparkles" x-bind:class="loading && 'fa-spin'"></i>
            <span x-text="loading ? 'Menyusun...' : 'Buat deskripsi dengan AI'"></span>
        </button>
        <button
            type="button"
            x-show="filled"
            x-on:click="generate"
            x-bind:disabled="loading"
            class="text-xs font-semibold text-[#737373] dark:text-white/40 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition"
        >
            Buat ulang
        </button>
    </div>

    <div class="mt-2" x-show="briefOpen" x-cloak>
        <label for="ai_brief" class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">
            Catatan untuk AI <span class="font-normal text-[#94A3B4]">(opsional)</span>
        </label>
        <textarea
            id="ai_brief"
            rows="2"
            x-model="brief"
            placeholder="Contoh: tonongkapi, garansi migrasi gratis, tanpa kode voucher"
            class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44]  px-3 py-2 text-xs focus:ring-2 focus:border-[#1D5B96] outline-none transition"
        ></textarea>
    </div>

    <button
        type="button"
        x-on:click="briefOpen = !briefOpen"
        class="mt-1.5 text-[11px] font-medium text-[#94A3B4] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition"
    >
        <span x-text="briefOpen ? 'Sembunyikan catatan' : 'Tambah catatan untuk AI'"></span>
    </button>

    <p x-show="error" x-cloak class="mt-2 text-xs text-rose-500 dark:text-rose-400" x-text="error"></p>
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