<x-public-layout
    title="Verifikasi Email"
    :with-nav="false"
    :with-footer="false"
    body-class="bg-slate-50 dark:bg-slate-900 font-sans antialiased text-slate-900 dark:text-slate-50">

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-md w-full bg-white dark:bg-slate-800/60 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 overflow-hidden">

            <div class="bg-indigo-600 px-8 py-10 text-center">
                <h1 class="text-3xl font-bold text-white tracking-tight">{{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze Portal' }}</h1>
                <p class="text-indigo-200 mt-2 text-sm">Verifikasi Alamat Email Anda</p>
            </div>

            <div class="p-8">
                <div class="mb-6 text-sm text-slate-600 dark:text-slate-300 text-center">
                    Terima kasih telah mendaftar! Sebelum memulai, bisakah Anda memverifikasi alamat email Anda dengan mengeklik tautan yang baru saja kami kirimkan melalui email kepada Anda? Jika Anda tidak menerima email tersebut, kami dengan senang hati akan mengirimkan email lain kepada Anda.
                </div>

                <form action="{{ route('verification.send') }}" method="POST" class="space-y-6">
                    @csrf
                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 ease-in-out transform hover:-translate-y-0.5 flex justify-center items-center gap-2">
                        <span>Kirim Ulang Email Verifikasi</span>
                        <i class="fa-solid fa-paper-plane text-sm"></i>
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-700 flex justify-center text-sm">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
                            Keluar
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
