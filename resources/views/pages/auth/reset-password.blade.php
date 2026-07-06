<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>RESET PASSWORD - RYAZE.MY.ID</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer nonce="{{ app('csp_nonce') ?? '' }}"></script>
</head>

<body class="bg-slate-50 font-sans antialiased text-slate-900">

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">

            <div class="bg-indigo-600 px-8 py-10 text-center">
                <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-lock-open text-2xl text-white"></i>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Buat Password Baru</h1>
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
                        <input type="password" name="password" id="password"
                            class="py-3 transition-all duration-200 focus:bg-white {{ $errors->has('password') ? 'border-red-500 ring-1 ring-red-500' : '' }} w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"
                            placeholder="••••••••" required autofocus>
                        @error('password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-slate-700 mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="py-3 transition-all duration-200 focus:bg-white w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"
                            placeholder="••••••••" required>
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

    @include('components.hot-toast')

</body>

</html>
