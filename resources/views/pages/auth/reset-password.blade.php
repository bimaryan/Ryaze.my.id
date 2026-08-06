<x-public-layout
    title="Reset Password"
    :with-nav="false"
    :with-footer="false"
    body-class="bg-slate-50 font-sans antialiased text-slate-900">

    @push('head')
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer nonce="{{ csp_nonce() }}"></script>
    @endpush

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">

            <div class="bg-indigo-600 px-8 py-10 text-center">
                <h1 class="text-3xl font-bold text-white tracking-tight">{{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze Portal' }}</h1>
                <p class="text-indigo-200 mt-2 text-sm">Silakan masukkan password baru untuk akun Anda.</p>
            </div>

            <div class="p-8">
                <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ request('email') }}">

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password
                            Baru</label>
                        <div class="relative">
                            <input type="password" name="password" id="password"
                                class="py-3 pr-10 transition-all duration-200 focus:bg-white {{ $errors->has('password') ? 'border-red-500 ring-1 ring-red-500' : '' }} w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"
                                placeholder="••••••••" required autofocus>
                            <button type="button" onclick="togglePassword('password', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">
                            Gunakan minimal 8 karakter dengan kombinasi huruf besar & kecil, angka, dan simbol.
                        </p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-slate-700 mb-2">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="py-3 pr-10 transition-all duration-200 focus:bg-white w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"
                                placeholder="••••••••" required>
                            <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                    </div>
                    @error('cf-turnstile-response')
                        <p class="mt-1 text-sm text-red-500 text-center">{{ $message }}</p>
                    @enderror

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 ease-in-out transform hover:-translate-y-0.5 flex justify-center items-center gap-2">
                        <span>Reset Password</span>
                        <i class="fa-solid fa-check text-sm"></i>
                    </button>
                </form>
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
