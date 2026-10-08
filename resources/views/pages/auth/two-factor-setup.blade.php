<x-public-layout
    title="Two-Factor Authentication"
    :with-nav="false"
    :with-footer="false"
    body-class="bg-slate-50 dark:bg-slate-900 font-sans antialiased text-slate-900 dark:text-slate-50">

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-lg w-full bg-white dark:bg-slate-800/60 shadow-xl border border-slate-100 dark:border-slate-700 overflow-hidden">

            <div class="bg-indigo-600 px-8 py-8 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-white/15 mb-3">
                    <i class="fa-solid fa-shield-halved text-white text-xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Two-Factor Authentication</h1>
                <p class="text-indigo-200 mt-1 text-sm">Verifikasi dua langkah untuk keamanan akun</p>
            </div>

            <div class="p-8">
                @if (session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-xl">
                        <p class="text-sm text-emerald-700 dark:text-emerald-300 flex items-center gap-2">
                            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                        </p>
                    </div>
                @endif

                @if ($enabled)
                    {{-- ══ 2FA SUDAH AKTIF ══ --}}
                    <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-xl flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
                        <div>
                            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">2FA aktif</p>
                            <p class="text-xs text-emerald-600 dark:text-emerald-400/80">
                                Diaktifkan sejak {{ optional($user->two_factor_confirmed_at)->translatedFormat('d M Y H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <a href="{{ route('two-factor.recovery-codes') }}"
                            class="block w-full text-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-700/50 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-100 font-semibold py-3 px-4 border border-slate-200 dark:border-slate-600 transition-colors">
                            <i class="fa-solid fa-key mr-1"></i> Lihat Kode Pemulihan
                        </a>
                        <a href="{{ route('profile.edit') }}"
                            class="block w-full text-center bg-slate-50 hover:bg-slate-100 dark:bg-slate-800/40 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 text-sm py-3 px-4 border border-slate-200 dark:border-slate-600 transition-colors">
                            Kembali ke Profil
                        </a>
                    </div>

                    {{-- Nonaktifkan: perlu password + OTP --}}
                    <details class="mt-8 border-t border-slate-200 dark:border-slate-700 pt-6">
                        <summary class="cursor-pointer text-sm font-semibold text-rose-600 dark:text-rose-400 hover:text-rose-700 transition-colors">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i> Nonaktifkan 2FA
                        </summary>

                        <form action="{{ route('two-factor.disable') }}" method="POST" class="mt-4 space-y-4">
                            @csrf
                            @method('DELETE')

                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Untuk keamanan, konfirmasi dengan password dan kode OTP Anda.
                            </p>

                            <div>
                                <label for="disable_password"
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Password</label>
                                <input type="password" name="password" id="disable_password" required
                                    class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none transition">
                                @error('password')
                                    <p class="text-xs text-rose-600 dark:text-rose-400 mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="disable_code"
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Kode OTP (6 digit)</label>
                                <input type="text" name="code" id="disable_code" required inputmode="numeric" pattern="\d{6}" maxlength="6"
                                    placeholder="000000"
                                    class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm tracking-[0.4em] focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none transition">
                                @error('code')
                                    <p class="text-xs text-rose-600 dark:text-rose-400 mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit"
                                class="w-full bg-rose-600 hover:bg-rose-700 text-white font-semibold py-3 px-4 transition-colors shadow-sm">
                                Nonaktifkan Permanen
                            </button>
                        </form>
                    </details>
                @else
                    {{-- ══ SETUP ══ --}}
                    <ol class="space-y-4 mb-8 text-sm">
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 text-xs font-bold flex items-center justify-center">1</span>
                            <span class="text-slate-600 dark:text-slate-300">Pindai QR code dengan aplikasi autentikator Anda
                                (Google Authenticator, Microsoft Authenticator, Authy, dll).</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 text-xs font-bold flex items-center justify-center">2</span>
                            <span class="text-slate-600 dark:text-slate-300">Masukkan kode 6 digit dari aplikasi untuk
                                memverifikasi.</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 text-xs font-bold flex items-center justify-center">3</span>
                            <span class="text-slate-600 dark:text-slate-300">Simpan kode pemulihan sebagai cadangan bila
                                perangkat hilang.</span>
                        </li>
                    </ol>

                    <div id="setup-stage" class="hidden">
                        <div class="flex flex-col items-center">
                            <div class="p-4 bg-white border-2 border-slate-200 dark:border-slate-600 rounded-2xl">
                                <img id="qr-image" alt="QR Code 2FA" class="w-48 h-48">
                            </div>

                            <p class="mt-4 text-xs text-slate-500 dark:text-slate-400">Tidak bisa memindai? Masukkan kunci manual:</p>
                            <div class="mt-2 flex items-center gap-2">
                                <code id="manual-secret"
                                    class="px-3 py-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg text-xs tracking-widest font-mono break-all"></code>
                                <button type="button" id="copy-secret" title="Salin kunci"
                                    class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-600 dark:text-slate-300 transition-colors">
                                    <i class="fa-solid fa-copy"></i>
                                </button>
                            </div>

                            <form action="{{ route('two-factor.confirm') }}" method="POST" class="mt-6 w-full space-y-4">
                                @csrf

                                <div>
                                    <label for="code"
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Kode OTP (6 digit)</label>
                                    <input type="text" name="code" id="code" required inputmode="numeric" pattern="\d{6}" maxlength="6"
                                        placeholder="000000" autofocus
                                        class="w-full text-center text-lg tracking-[0.5em] bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                                    @error('code')
                                        <p class="text-xs text-rose-600 dark:text-rose-400 mt-1.5">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 shadow-md transition-all duration-200">
                                    Verifikasi & Aktifkan
                                </button>
                            </form>

                            <button type="button" id="restart-setup"
                                class="mt-4 text-xs text-slate-500 dark:text-slate-400 hover:text-indigo-600 transition-colors">
                                <i class="fa-solid fa-rotate-right mr-1"></i> Buat QR baru
                            </button>
                        </div>
                    </div>

                    <div id="setup-start">
                        <button type="button" id="start-setup"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 shadow-md transition-all duration-200">
                            <i class="fa-solid fa-qrcode mr-2"></i> Aktifkan 2FA
                        </button>
                        <a href="{{ route('profile.edit') }}"
                            class="mt-3 block w-full text-center text-sm text-slate-500 dark:text-slate-400 hover:text-indigo-600 transition-colors">
                            Nanti saja
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

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
                        alert('Gagal memuat QR code. Silakan coba lagi.');
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
</x-public-layout>