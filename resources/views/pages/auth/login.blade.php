<x-public-layout
    title="Login"
    :with-nav="false"
    :with-footer="false"
    body-class="bg-slate-50 dark:bg-slate-900 font-sans antialiased text-slate-900 dark:text-slate-50">

    @push('head')
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer nonce="{{ csp_nonce() }}"></script>
    @endpush

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-md w-full bg-white dark:bg-slate-800/60 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 overflow-hidden">

            <div class="bg-indigo-600 px-8 py-10 text-center">
                <h1 class="text-3xl font-bold text-white tracking-tight">{{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze Portal' }}</h1>
                <p class="text-indigo-200 mt-2 text-sm">Masuk untuk mengelola Joki & Hosting Anda</p>
            </div>

            <div class="p-8">
                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Email
                            Address</label>
                        <input type="email" name="email" id="email"
                            class="py-3 transition-all duration-200 focus:bg-white {{ $errors->has('email') ? 'border-red-500 ring-1 ring-red-500' : '' }} w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"
                            placeholder="nama@email.com" value="{{ old('email') }}" autofocus>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Password</label>
                            <a href="{{ route('password.request') }}"
                                class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 transition-colors">Lupa
                                password?</a>
                        </div>
                        <div class="relative">
                            <input type="password" name="password" id="password"
                                class="py-3 pr-10 transition-all duration-200 focus:bg-white {{ $errors->has('password') ? 'border-red-500 ring-1 ring-red-500' : '' }} w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"
                                placeholder="********">
                            <button type="button" onclick="togglePassword('password', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="remember" id="remember"
                            class="h-4 w-4 text-indigo-600 dark:text-indigo-400 focus:ring-indigo-500 border-slate-300 dark:border-slate-600 rounded cursor-pointer">
                        <label for="remember" class="ml-2 block text-sm text-slate-600 dark:text-slate-300 cursor-pointer">
                            Ingat saya
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 ease-in-out transform hover:-translate-y-0.5">
                        Masuk ke Dashboard
                    </button>
                </form>

                <div class="mt-8 text-center text-sm text-slate-600 dark:text-slate-300">
                    Belum punya akun?
                    <a href="{{ route('register') }}"
                        class="font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 transition-colors">Daftar
                        sekarang</a>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            function togglePassword(inputId, btn) {
                const input = document.getElementById(inputId);
                const icon = btn.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        </script>
    @endpush
</x-public-layout>
