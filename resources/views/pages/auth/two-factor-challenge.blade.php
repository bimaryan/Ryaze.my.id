<x-public-layout
    title="Verifikasi Dua Langkah"
    :with-nav="false"
    :with-footer="false"
    body-class="bg-slate-50 dark:bg-slate-900 font-sans antialiased text-slate-900 dark:text-slate-50">

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-md w-full bg-white dark:bg-slate-800/60 shadow-xl border border-slate-100 dark:border-slate-700 overflow-hidden">

            <div class="bg-indigo-600 px-8 py-10 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-white/15 mb-3">
                    <i class="fa-solid fa-lock text-white text-xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Verifikasi Dua Langkah</h1>
                <p class="text-indigo-200 mt-2 text-sm">Password benar. Masukkan kode OTP Anda untuk melanjutkan.</p>
            </div>

            <div class="p-8">
                <form action="{{ route('two-factor.challenge.verify') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="code" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                            Kode OTP atau Kode Pemulihan
                        </label>
                        <input type="text" name="code" id="code" required autofocus inputmode="numeric" autocomplete="one-time-code"
                            placeholder="000000"
                            class="w-full text-center text-lg tracking-[0.4em] bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition {{ $errors->has('code') ? 'border-rose-500 ring-1 ring-rose-500' : '' }}">
                        @error('code')
                            <p class="text-sm text-rose-600 dark:text-rose-400 mt-2 flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 shadow-md hover:shadow-lg transition-all duration-200">
                        Verifikasi
                    </button>
                </form>

                <div class="mt-6 p-4 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded-xl">
                    <p class="text-xs text-amber-800 dark:text-amber-300 flex items-start gap-2">
                        <i class="fa-solid fa-lightbulb mt-0.5"></i>
                        <span>Kode pemulihan (format <code class="font-mono font-bold">XXXX-XXXX</code>) juga bisa dipakai
                            bila aplikasi autentikator tidak tersedia. Setiap kode hanya dapat dipakai sekali.</span>
                    </p>
                </div>

                <div class="mt-6 text-center">
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="text-sm text-slate-500 dark:text-slate-400 hover:text-rose-600 transition-colors">
                            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke halaman login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>