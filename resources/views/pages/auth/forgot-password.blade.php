<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LUPA PASSWORD - RYAZE.MY.ID</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer nonce="{{ app('csp_nonce') ?? '' }}"></script>
</head>

<body class="bg-slate-50 font-sans antialiased text-slate-900">

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">

            <div class="bg-indigo-600 px-8 py-10 text-center relative">
                <a href="{{ route('login') }}"
                    class="absolute top-4 left-4 text-indigo-200 hover:text-white transition">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-key text-2xl text-white"></i>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Lupa Password?</h1>
                <p class="text-indigo-200 mt-2 text-sm">Masukkan email Anda untuk menerima link reset password.</p>
            </div>

            <div class="p-8">
                <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email
                            Address</label>
                        <input type="email" name="email" id="email"
                            class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 outline-none transition-all duration-200 bg-slate-50 focus:bg-white {{ $errors->has('email') ? 'border-red-500 ring-1 ring-red-500' : '' }}"
                            placeholder="nama@email.com" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-center">
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                    </div>
                    @error('cf-turnstile-response')
                        <p class="mt-1 text-sm text-red-500 text-center">{{ $message }}</p>
                    @enderror

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 ease-in-out transform hover:-translate-y-0.5 flex justify-center items-center gap-2">
                        <span>Kirim Link Reset</span>
                        <i class="fa-solid fa-paper-plane text-sm"></i>
                    </button>
                </form>

                <div class="mt-8 text-center text-sm text-slate-600">
                    Ingat password Anda?
                    <a href="{{ route('login') }}"
                        class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors">Masuk di sini</a>
                </div>
            </div>

        </div>
    </div>

    @include('components.hot-toast')

    @if (session('status'))
        <script nonce="{{ app('csp_nonce') ?? '' }}">
            document.addEventListener('DOMContentLoaded', () => {
                hotToast('{{ session('status') }}', 'success');
            });
        </script>
    @endif
</body>

</html>
