<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REGISTER - RYAZE.MY.ID</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer nonce="{{ app('csp_nonce') ?? '' }}"></script>
</head>

<body class="bg-slate-50 font-sans antialiased text-slate-900">

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">

            <div class="bg-indigo-600 px-8 py-10 text-center">
                <h1 class="text-3xl font-bold text-white tracking-tight">Buat Akun</h1>
                <p class="text-indigo-200 mt-2 text-sm">Daftar untuk mulai pesan Joki atau Hosting</p>
            </div>

            <div class="p-8">
                <form action="{{ route('register.process') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                            class="py-3 transition-all duration-200 focus:bg-white {{ $errors->has('name') ? 'border-red-500 ring-1 ring-red-500' : '' }} w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"
                            placeholder="John Doe" autofocus>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email
                            Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="py-3 transition-all duration-200 focus:bg-white {{ $errors->has('email') ? 'border-red-500 ring-1 ring-red-500' : '' }} w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"
                            placeholder="nama@email.com">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                        <input type="password" name="password" id="password"
                            class="py-3 transition-all duration-200 focus:bg-white {{ $errors->has('password') ? 'border-red-500 ring-1 ring-red-500' : '' }} w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"
                            placeholder="Min. 8 karakter">

                        @if ($errors->has('password'))
                            <ul class="mt-2 text-xs text-red-500 list-disc list-inside space-y-1">
                                @foreach ($errors->get('password') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-slate-700 mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="py-3 transition-all duration-200 focus:bg-white w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"
                            placeholder="Ulangi password">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Pilih Layanan Utama</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label
                                class="relative flex items-center justify-center p-3 border border-slate-300 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50 has-[:checked]:ring-1 has-[:checked]:ring-indigo-600 {{ $errors->has('role') ? 'border-red-500 ring-1 ring-red-500' : '' }}">
                                <input type="radio" name="role" value="user_joki" class="peer sr-only"
                                    {{ old('role') == 'user_joki' ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-slate-600 peer-checked:text-indigo-700">Jasa
                                    Joki</span>
                            </label>

                            <label
                                class="relative flex items-center justify-center p-3 border border-slate-300 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50 has-[:checked]:ring-1 has-[:checked]:ring-indigo-600 {{ $errors->has('role') ? 'border-red-500 ring-1 ring-red-500' : '' }}">
                                <input type="radio" name="role" value="user_hosting" class="peer sr-only"
                                    {{ old('role') == 'user_hosting' ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-slate-600 peer-checked:text-indigo-700">Beli
                                    Hosting</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 ease-in-out transform hover:-translate-y-0.5 mt-2">
                        Daftar Sekarang
                    </button>
                </form>

                <div class="mt-8 text-center text-sm text-slate-600">
                    Sudah punya akun?
                    <a href="{{ route('login') }}"
                        class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors">Masuk di sini</a>
                </div>
            </div>

        </div>
    </div>

    @include('components.hot-toast')
</body>

</html>
