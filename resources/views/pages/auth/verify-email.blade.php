<x-public-layout
    title="Verifikasi Email"
    :with-nav="false"
    :with-footer="false"
    body-class="bg-slate-50 dark:bg-slate-900 font-sans antialiased text-slate-900 dark:text-slate-50">

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-md w-full bg-white dark:bg-slate-800/60 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 overflow-hidden">

            <div class="bg-indigo-600 px-8 py-10 text-center">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-envelope-open-text text-white text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Cek Email Anda!</h1>
                <p class="text-indigo-200 mt-2 text-sm">Verifikasi diperlukan untuk melanjutkan</p>
            </div>

            <div class="p-8">

                <div class="mb-5 text-sm text-slate-600 dark:text-slate-300 text-center leading-relaxed">
                    Kami telah mengirimkan tautan verifikasi ke email Anda. Silakan buka email dan klik tombol verifikasi untuk mengaktifkan akun Anda.
                </div>

                {{-- Spam Alert --}}
                <div class="mb-5 flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 rounded-xl p-4">
                    <div class="flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-triangle-exclamation text-amber-500 text-base"></i>
                    </div>
                    <div>
                        <p class="text-amber-800 dark:text-amber-300 font-semibold text-sm mb-1">Email tidak ditemukan di Inbox?</p>
                        <p class="text-amber-700 dark:text-amber-400 text-xs leading-relaxed">
                            Coba periksa folder <strong>Spam</strong> atau <strong>Junk Mail</strong> di email Anda. Jika ada, tandai sebagai "Bukan Spam" agar email berikutnya langsung masuk ke inbox.
                        </p>
                    </div>
                </div>

                {{-- Steps hint --}}
                <div class="mb-6 bg-slate-50 dark:bg-slate-700/30 rounded-xl p-4 space-y-2">
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-3">Langkah verifikasi</p>
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-full text-xs font-bold flex items-center justify-center flex-shrink-0">1</span>
                        <p class="text-sm text-slate-600 dark:text-slate-300">Buka aplikasi email Anda</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-full text-xs font-bold flex items-center justify-center flex-shrink-0">2</span>
                        <p class="text-sm text-slate-600 dark:text-slate-300">Cari email dari <strong class="text-indigo-600 dark:text-indigo-400">{{ config('mail.from.address') }}</strong></p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-full text-xs font-bold flex items-center justify-center flex-shrink-0">3</span>
                        <p class="text-sm text-slate-600 dark:text-slate-300">Klik tombol <strong>"Verifikasi Email Sekarang"</strong></p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 rounded-full text-xs font-bold flex items-center justify-center flex-shrink-0">!</span>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada? Cek folder <strong>Spam / Junk</strong></p>
                    </div>
                </div>

                <form action="{{ route('verification.send') }}" method="POST" class="space-y-3">
                    @csrf
                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 ease-in-out transform hover:-translate-y-0.5 flex justify-center items-center gap-2">
                        <i class="fa-solid fa-paper-plane text-sm"></i>
                        <span>Kirim Ulang Email Verifikasi</span>
                    </button>
                </form>

                <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-700 flex justify-center text-sm">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors flex items-center gap-1.5">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                            Keluar dari akun ini
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    @if (session('success'))
        @push('scripts')
            <script nonce="{{ csp_nonce() }}">
                document.addEventListener('DOMContentLoaded', () => {
                    hotToast('{{ session('success') }}', 'success');
                });
            </script>
        @endpush
    @endif
    @if (session('message'))
        @push('scripts')
            <script nonce="{{ csp_nonce() }}">
                document.addEventListener('DOMContentLoaded', () => {
                    hotToast('{{ session('message') }}', 'success');
                });
            </script>
        @endpush
    @endif
</x-public-layout>
