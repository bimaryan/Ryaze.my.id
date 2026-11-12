@extends('index')

@section('title', 'Two-Factor Authentication')
@section('seo_description', 'Verifikasi dua langkah untuk keamanan akun Ryaze.')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header
        title="Two-Factor Authentication"
        eyebrow="Keamanan · Akun"
        icon="fa-solid fa-shield-halved">
        <x-slot:subtitle>
            Verifikasi dua langkah untuk keamanan akun Anda.
        </x-slot:subtitle>
        <x-slot:actions>
            <a href="{{ route('profile.edit') }}" class="ui-btn ui-btn-secondary">
                <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Profil
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
        @if ($enabled)
            {{-- ══ 2FA SUDAH AKTIF ══ --}}
            <x-ui.section
                title="Status 2FA"
                description="Akun Anda dilindungi kode dari aplikasi autentikator."
                icon="fa-solid fa-shield-halved">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="ui-badge ui-badge-success"><i class="fa-solid fa-circle-check"></i> 2FA Aktif</span>
                    <span class="ui-badge ui-badge-neutral">Diaktifkan {{ optional($user->two_factor_confirmed_at)->translatedFormat('d M Y H:i') }}</span>
                </div>

                <div class="flex flex-wrap gap-3 mt-6">
                    <a href="{{ route('two-factor.recovery-codes') }}" class="ui-btn ui-btn-primary">
                        <i class="fa-solid fa-key text-xs"></i> Lihat Kode Pemulihan
                    </a>
                    <a href="{{ route('profile.edit') }}" class="ui-btn ui-btn-secondary">
                        Kembali ke Profil
                    </a>
                </div>
            </x-ui.section>

            {{-- Nonaktifkan: perlu password + OTP --}}
            <x-ui.section
                title="Nonaktifkan 2FA"
                description="Menonaktifkan 2FA mengurangi keamanan akun Anda."
                icon="fa-solid fa-triangle-exclamation">
                <form action="{{ route('two-factor.disable') }}" method="POST" class="max-w-sm space-y-4">
                    @csrf
                    @method('DELETE')

                    <p class="text-xs text-[#525252] dark:text-white/50">
                        Untuk keamanan, konfirmasi dengan password dan kode OTP Anda.
                    </p>

                    <div>
                        <label for="disable_password" class="ui-label">Password</label>
                        <input type="password" name="password" id="disable_password" required
                            autocomplete="current-password"
                            class="ui-field @error('password') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror">
                        @error('password')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="disable_code" class="ui-label">Kode OTP (6 digit)</label>
                        <input type="text" name="code" id="disable_code" required inputmode="numeric"
                            pattern="\d{6}" maxlength="6" placeholder="000000"
                            class="ui-field tracking-[0.4em] @error('code') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror">
                        @error('code')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="ui-btn ui-btn-danger">
                        <i class="fa-solid fa-shield-halved text-xs"></i> Nonaktifkan Permanen
                    </button>
                </form>
            </x-ui.section>
        @else
            {{-- ══ SETUP ══ --}}
            <x-ui.section
                title="Cara Kerja"
                description="Tiga langkah mengaktifkan verifikasi dua langkah."
                icon="fa-solid fa-list-check">
                <ol class="space-y-4 text-sm">
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 w-6 h-6 bg-[#E8F0F9] dark:bg-[#1D5B96]/20 text-[#1D5B96] dark:text-[#6BA8DC] text-xs font-bold flex items-center justify-center border border-[#C9D5E2] dark:border-[#1D5B96]/40">1</span>
                        <span class="text-[#525252] dark:text-white/60">Pindai QR code dengan aplikasi autentikator Anda
                            (Google Authenticator, Microsoft Authenticator, Authy, dll).</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 w-6 h-6 bg-[#E8F0F9] dark:bg-[#1D5B96]/20 text-[#1D5B96] dark:text-[#6BA8DC] text-xs font-bold flex items-center justify-center border border-[#C9D5E2] dark:border-[#1D5B96]/40">2</span>
                        <span class="text-[#525252] dark:text-white/60">Masukkan kode 6 digit dari aplikasi untuk
                            memverifikasi.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 w-6 h-6 bg-[#E8F0F9] dark:bg-[#1D5B96]/20 text-[#1D5B96] dark:text-[#6BA8DC] text-xs font-bold flex items-center justify-center border border-[#C9D5E2] dark:border-[#1D5B96]/40">3</span>
                        <span class="text-[#525252] dark:text-white/60">Simpan kode pemulihan sebagai cadangan bila
                            perangkat hilang.</span>
                    </li>
                </ol>
            </x-ui.section>

            <div id="setup-stage" class="hidden">
                <x-ui.section
                    title="Pindai & Verifikasi"
                    description="Pindai QR code, lalu masukkan kode 6 digit dari aplikasi autentikator."
                    icon="fa-solid fa-qrcode">
                    <div class="flex flex-col items-center">
                        <div class="p-4 bg-white border-2 border-[#E3EAF1] dark:border-[#2A4463]">
                            <img id="qr-image" alt="QR Code 2FA" class="w-48 h-48">
                        </div>

                        <p class="mt-4 text-xs text-[#525252] dark:text-white/50">Tidak bisa memindai? Masukkan kunci manual:</p>
                        <div class="mt-2 flex flex-wrap items-center justify-center gap-2">
                            <code id="manual-secret"
                                class="ui-code break-all max-w-xs"></code>
                            <button type="button" id="copy-secret" title="Salin kunci"
                                class="ui-icon-btn ui-icon-btn-neutral">
                                <i class="fa-solid fa-copy"></i>
                            </button>
                        </div>

                        <form action="{{ route('two-factor.confirm') }}" method="POST" class="mt-6 w-full max-w-sm space-y-4">
                            @csrf

                            <div>
                                <label for="code" class="ui-label">Kode OTP (6 digit)</label>
                                <input type="text" name="code" id="code" required inputmode="numeric" pattern="\d{6}"
                                    maxlength="6" placeholder="000000" autofocus
                                    class="ui-field text-center text-lg tracking-[0.5em] @error('code') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror">
                                @error('code')
                                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="ui-btn ui-btn-primary w-full">
                                <i class="fa-solid fa-check text-xs"></i> Verifikasi & Aktifkan
                            </button>
                        </form>

                        <button type="button" id="restart-setup"
                            class="mt-4 text-xs text-[#525252] dark:text-white/50 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors">
                            <i class="fa-solid fa-rotate-right mr-1"></i> Buat QR baru
                        </button>
                    </div>
                </x-ui.section>
            </div>

            <div id="setup-start">
                <x-ui.section
                    title="Mulai Setup"
                    description="Klik tombol di bawah untuk membuat QR code dan kunci rahasia 2FA Anda."
                    icon="fa-solid fa-qrcode">
                    <button type="button" id="start-setup"
                        class="ui-btn ui-btn-primary w-full max-w-sm">
                        <i class="fa-solid fa-qrcode text-xs"></i> Aktifkan 2FA
                    </button>
                    <div class="mt-3">
                        <a href="{{ route('profile.edit') }}" class="ui-btn ui-btn-ghost">Nanti saja</a>
                    </div>
                </x-ui.section>
            </div>
        @endif
    </div>
</x-ui.page-layout>
@endsection

@push('scripts')
    <script nonce="{{ csp_nonce() }}">
        (function () {
            const startBtn = document.getElementById('start-setup');
            if (!startBtn) return;

            const startStage = document.getElementById('setup-start');
            const setupStage = document.getElementById('setup-stage');
            const qrImage = document.getElementById('qr-image');
            const manualSecret = document.getElementById('manual-secret');
            const restartBtn = document.getElementById('restart-setup');
            const copyBtn = document.getElementById('copy-secret');

            const GENERATE_URL = @json(route('two-factor.generate'));

            async function loadQr() {
                startBtn.disabled = true;
                const original = startBtn.innerHTML;
                startBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuat QR...';

                try {
                    const res = await fetch(GENERATE_URL, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });

                    if (!res.ok) throw new Error('Gagal memuat QR');

                    const data = await res.json();
                    qrImage.src = data.qr;
                    manualSecret.textContent = data.secret;

                    startStage.classList.add('hidden');
                    setupStage.classList.remove('hidden');
                } catch (e) {
                    hotToast('Gagal memuat QR code. Silakan coba lagi.', 'error');
                } finally {
                    startBtn.disabled = false;
                    startBtn.innerHTML = original;
                }
            }

            startBtn.addEventListener('click', loadQr);
            restartBtn?.addEventListener('click', loadQr);

            copyBtn?.addEventListener('click', async function () {
                try {
                    await navigator.clipboard.writeText(manualSecret.textContent.trim());
                    const icon = this.querySelector('i');
                    icon.classList.remove('fa-copy');
                    icon.classList.add('fa-check');
                    setTimeout(() => {
                        icon.classList.remove('fa-check');
                        icon.classList.add('fa-copy');
                    }, 2000);
                } catch (e) {}
            });
        })();
    </script>
@endpush
