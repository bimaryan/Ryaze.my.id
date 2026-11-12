<x-public-layout
    title="Verifikasi Dua Langkah"
    :with-nav="false"
    :with-footer="false"
    body-class="bg-slate-50 dark:bg-[#0A1322] font-sans antialiased">

    <div class="min-h-screen flex items-center justify-center p-4 py-12">
        <div class="w-full max-w-[420px]">

            <div class="bg-white dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] overflow-hidden">

                {{-- Header --}}
                <div class="bg-[#F0F6FC] dark:bg-[#0F1A2A] border-b border-[#E3EAF1] dark:border-[#1B2C44] px-8 py-6">
                    <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC] mb-1">
                        Keamanan Akun
                    </p>
                    <h1 class="text-xl font-black text-[#0F1A2A] dark:text-white tracking-tight">
                        Verifikasi Dua Langkah
                    </h1>
                    <p class="text-[#525252] dark:text-[#8A9BAE] text-sm mt-1">
                        Password benar. Masukkan kode dari aplikasi autentikator.
                    </p>
                </div>

                {{-- Form --}}
                <div class="p-8">
                    <form action="{{ route('two-factor.challenge.verify') }}" method="POST" class="space-y-5">
                        @csrf

                        <div>
                            <label for="code" class="block font-mono text-[10px] uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC] mb-2">
                                Kode OTP
                            </label>
                            <input type="text" name="code" id="code" required autofocus
                                inputmode="numeric" autocomplete="one-time-code" maxlength="9"
                                placeholder="000000"
                                oninput="this.value = this.value.replace(/\s+/g, '')"
                                class="w-full bg-slate-50 dark:bg-[#0A1322] border {{ $errors->has('code') ? 'border-rose-500' : 'border-[#E3EAF1] dark:border-[#1B2C44] focus:border-[#1D5B96]' }} px-4 py-3.5 text-center text-xl font-mono tracking-[0.35em] text-[#0F1A2A] dark:text-white placeholder-[#94A3B4] dark:placeholder-[#3D556E] focus:outline-none focus:ring-1 focus:ring-[#1D5B96] transition-all">
                            @error('code')
                                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                                    <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full bg-[#1D5B96] text-white font-mono text-[11px] uppercase tracking-[0.2em] py-3.5 px-4 hover:bg-[#2568A8] flex items-center justify-center gap-2.5 transition-colors">
                            <span>Verifikasi</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </form>

                    {{-- Info --}}
                    <div class="mt-6 bg-[#F0F6FC] dark:bg-[#0F1A2A] border border-[#E3EAF1] dark:border-[#1B2C44] px-4 py-3.5">
                        <p class="font-mono text-[10px] uppercase tracking-[0.15em] text-[#1D5B96] dark:text-[#6BA8DC] mb-1">
                            <i class="fa-solid fa-key mr-1"></i> Alternatif
                        </p>
                        <p class="text-xs leading-relaxed text-[#525252] dark:text-[#8A9BAE]">
                            Jika aplikasi autentikator tidak tersedia, gunakan kode pemulihan
                            berformat <span class="font-mono font-semibold text-[#0F1A2A] dark:text-[#C9D5E2]">XXXX-XXXX</span>.
                            Setiap kode hanya dapat dipakai sekali.
                        </p>
                    </div>

                    {{-- Footer --}}
                    <div class="mt-8 pt-6 border-t border-[#E3EAF1] dark:border-[#1B2C44] text-center">
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="font-mono text-[10px] uppercase tracking-[0.15em] text-[#737373] dark:text-[#4A7A9B] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors">
                                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke halaman login
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-public-layout>
