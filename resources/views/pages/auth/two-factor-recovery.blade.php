@extends('index')

@section('title', 'Kode Pemulihan')
@section('seo_description', 'Kode pemulihan cadangan bila perangkat autentikator tidak tersedia.')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header
        title="Kode Pemulihan"
        eyebrow="Keamanan · Two-Factor Authentication"
        icon="fa-solid fa-key">
        <x-slot:subtitle>
            Cadangan bila perangkat autentikator tidak tersedia.
        </x-slot:subtitle>
        <x-slot:actions>
            <a href="{{ route('two-factor.setup') }}" class="ui-btn ui-btn-secondary">
                <i class="fa-solid fa-arrow-left text-xs"></i> Kembali
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    @if (session('success'))
        <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50 flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
            <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
        </div>
    @endif

    <div class="space-y-6">
        @if (!empty($codes))
            <x-ui.section
                title="Kode Pemulihan Baru"
                description="Simpan kode ini di tempat aman sebelum menutup halaman."
                icon="fa-solid fa-key">
                <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/50 flex items-start gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 dark:text-amber-400 mt-0.5"></i>
                    <p class="text-sm text-amber-800 dark:text-amber-300 font-semibold">
                        Kode ini hanya ditampilkan SEKALI. Simpan di tempat aman sebelum menutup halaman ini.
                    </p>
                </div>

                <div id="recovery-codes" class="grid grid-cols-2 gap-3 mb-6">
                    @foreach ($codes as $recoveryCode)
                        <div
                            class="recovery-code ui-cell-alt border border-[#E3EAF1] dark:border-[#2A4463] px-4 py-3 text-center font-mono text-sm tracking-wider text-[#0F1A2A] dark:text-white select-all">
                            {{ $recoveryCode }}
                        </div>
                    @endforeach
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="button" id="copy-recovery-codes" class="ui-btn ui-btn-secondary">
                        <i class="fa-solid fa-copy text-xs"></i>
                        <span id="copy-recovery-codes-label">Salin Semua Kode</span>
                    </button>
                    <a href="{{ route('profile.edit') }}" class="ui-btn ui-btn-primary">
                        <i class="fa-solid fa-check text-xs"></i> Saya sudah menyimpannya
                    </a>
                </div>
            </x-ui.section>
        @else
            <x-ui.section
                title="Buat Kode Baru"
                description="Kode pemulihan tidak ditampilkan karena halaman ini dibuka ulang."
                icon="fa-solid fa-rotate">
                <form action="{{ route('two-factor.recovery-codes.regenerate') }}" method="POST" class="max-w-sm space-y-4">
                    @csrf

                    <p class="text-sm text-[#525252] dark:text-white/60">
                        Buat kode baru di bawah ini — kode lama otomatis tidak berlaku.
                        Dikonfirmasi dengan kode OTP untuk membuat kode baru.
                    </p>

                    <div>
                        <label for="code" class="ui-label">Kode OTP (6 digit)</label>
                        <input type="text" name="code" id="code" required inputmode="numeric" pattern="\d{6}"
                            maxlength="6" placeholder="000000" autofocus
                            class="ui-field text-center text-lg tracking-[0.4em] @error('code') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror">
                        @error('code')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="ui-btn ui-btn-primary w-full">
                        <i class="fa-solid fa-rotate text-xs"></i> Buat Kode Baru
                    </button>
                </form>

                <div class="mt-3">
                    <a href="{{ route('two-factor.setup') }}" class="ui-btn ui-btn-ghost">Kembali</a>
                </div>
            </x-ui.section>
        @endif
    </div>
</x-ui.page-layout>
@endsection

@push('scripts')
    <script nonce="{{ csp_nonce() }}">
        function initRecoveryCopy() {
            const button = document.getElementById('copy-recovery-codes');
            const label = document.getElementById('copy-recovery-codes-label');
            if (!button || !label || button.dataset.bound === '1') return;
            button.dataset.bound = '1';

            const originalLabel = label.textContent;

            // Kode diambil dari DOM, bukan dari atribut onclick. Menyisipkan
            // kode lewat atribut inline dan membuat handler-nya tidak jalan sama sekali.
            function collectCodes() {
                return Array.from(document.querySelectorAll('#recovery-codes .recovery-code'))
                    .map((el) => el.textContent.trim())
                    .filter(Boolean)
                    .join('\n');
            }

            // Fallback untuk browser lama / konteks non-HTTPS, di mana
            // navigator.clipboard tidak tersedia.
            function copyViaTextarea(text) {
                const area = document.createElement('textarea');
                area.value = text;
                area.setAttribute('readonly', '');
                area.style.position = 'fixed';
                area.style.opacity = '0';
                document.body.appendChild(area);
                area.select();

                let ok = false;
                try {
                    ok = document.execCommand('copy');
                } catch (e) {
                    ok = false;
                }

                document.body.removeChild(area);
                return ok;
            }

            function feedback(ok) {
                const icon = button.querySelector('i');
                label.textContent = ok ? 'Tersalin!' : 'Gagal menyalin';
                button.classList.toggle('text-emerald-600', ok);
                button.classList.toggle('dark:text-emerald-400', ok);
                icon.classList.toggle('fa-copy', !ok);
                icon.classList.toggle('fa-check', ok);

                setTimeout(() => {
                    label.textContent = originalLabel;
                    button.classList.remove('text-emerald-600', 'dark:text-emerald-400');
                    icon.classList.remove('fa-check');
                    icon.classList.add('fa-copy');
                }, 2000);
            }

            button.addEventListener('click', async function () {
                const text = collectCodes();
                if (!text) return;

                if (navigator.clipboard && window.isSecureContext) {
                    try {
                        await navigator.clipboard.writeText(text);
                        feedback(true);
                        return;
                    } catch (e) {
                        // jatuh ke fallback di bawah
                    }
                }

                feedback(copyViaTextarea(text));
            });
        }

        document.addEventListener('DOMContentLoaded', initRecoveryCopy);
        document.addEventListener('pjax:end', initRecoveryCopy);
    </script>
@endpush
