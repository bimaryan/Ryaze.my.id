<x-public-layout
    title="Kode Pemulihan"
    :with-nav="false"
    :with-footer="false"
    body-class="bg-slate-50 dark:bg-slate-900 font-sans antialiased text-slate-900 dark:text-slate-50">

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-lg w-full bg-white dark:bg-slate-800/60 shadow-xl border border-slate-100 dark:border-slate-700 overflow-hidden">

            <div class="bg-indigo-600 px-8 py-8 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-white/15 mb-3">
                    <i class="fa-solid fa-key text-white text-xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Kode Pemulihan</h1>
                <p class="text-indigo-200 mt-1 text-sm">Cadangan bila perangkat autentikator tidak tersedia</p>
            </div>

            <div class="p-8">
                @if (session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-xl">
                        <p class="text-sm text-emerald-700 dark:text-emerald-300 flex items-center gap-2">
                            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                        </p>
                    </div>
                @endif

                @if (!empty($codes))
                    <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded-xl">
                        <p class="text-sm text-amber-800 dark:text-amber-300 flex items-start gap-2 font-semibold">
                            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                            <span>Kode ini hanya ditampilkan SEKALI. Simpan di tempat aman sebelum menutup halaman ini.</span>
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-6">
                        @foreach ($codes as $recoveryCode)
                            <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-center font-mono text-sm tracking-wider text-slate-800 dark:text-slate-100 select-all">
                                {{ $recoveryCode }}
                            </div>
                        @endforeach
                    </div>

                    <button type="button" onclick="navigator.clipboard.writeText(@json(implode(PHP_EOL, $codes)))"
                        class="w-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-700/50 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-100 font-semibold py-3 px-4 border border-slate-200 dark:border-slate-600 transition-colors">
                        <i class="fa-solid fa-copy mr-2"></i> Salin Semua Kode
                    </button>

                    <a href="{{ route('two-factor.setup') }}"
                        class="mt-3 block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 transition-colors shadow-sm">
                        Saya sudah menyimpannya
                    </a>
                @else
                    <p class="text-sm text-slate-600 dark:text-slate-300 mb-6">
                        Kode pemulihan tidak ditampilkan karena halaman ini dibuka ulang.
                        Buat kode baru di bawah ini — kode lama otomatis tidak berlaku.
                    </p>

                    <form action="{{ route('two-factor.recovery-codes.regenerate') }}" method="POST" class="space-y-4">
                        @csrf

                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Dikonfirmasi dengan kode OTP untuk membuat kode baru.
                        </p>

                        <div>
                            <label for="code"
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Kode OTP (6 digit)</label>
                            <input type="text" name="code" id="code" required inputmode="numeric" pattern="\d{6}" maxlength="6"
                                placeholder="000000" autofocus
                                class="w-full text-center text-lg tracking-[0.4em] bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            @error('code')
                                <p class="text-xs text-rose-600 dark:text-rose-400 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 transition-colors shadow-sm">
                            Buat Kode Baru
                        </button>
                    </form>

                    <a href="{{ route('two-factor.setup') }}"
                        class="mt-3 block w-full text-center text-sm text-slate-500 dark:text-slate-400 hover:text-indigo-600 transition-colors">
                        Kembali
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-public-layout>