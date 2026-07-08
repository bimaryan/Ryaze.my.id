<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>VERIFIKASI EMAIL - RYAZE.MY.ID</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 font-sans antialiased text-slate-900">

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">

            <div class="bg-indigo-600 px-8 py-10 text-center">
                <h1 class="text-3xl font-bold text-white tracking-tight">{{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze Portal' }}</h1>
                <p class="text-indigo-200 mt-2 text-sm">Verifikasi Alamat Email Anda</p>
            </div>

            <div class="p-8">
                <div class="mb-6 text-sm text-slate-600 text-center">
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

                <div class="mt-8 pt-6 border-t border-slate-100 flex justify-center text-sm">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="font-semibold text-slate-500 hover:text-slate-700 transition-colors">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    @include('components.hot-toast')

    @if (session('success'))
        <script nonce="{{ app('csp_nonce') ?? '' }}">
            document.addEventListener('DOMContentLoaded', () => {
                hotToast('{{ session('success') }}', 'success');
            });
        </script>
    @endif
    @if (session('message'))
        <script nonce="{{ app('csp_nonce') ?? '' }}">
            document.addEventListener('DOMContentLoaded', () => {
                hotToast('{{ session('message') }}', 'success');
            });
        </script>
    @endif
</body>

</html>
