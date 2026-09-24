<!DOCTYPE html>
<html lang="id" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terima Kasih atas Tipnya!</title>
    <meta name="robots" content="noindex, nofollow">

    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet" nonce="{{ csp_nonce() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" nonce="{{ csp_nonce() }}" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" nonce="{{ csp_nonce() }}"></noscript>

    @vite(['resources/css/app.css'])

    <script nonce="{{ csp_nonce() }}">
        (function () {
            var t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>

<body class="bg-mesh font-sans antialiased text-slate-900 dark:text-slate-100 min-h-screen flex items-center justify-center px-6">
    <main class="w-full max-w-lg">
        <div class="glass-card rounded-3xl p-8 md:p-10 shadow-xl text-center">
            @if($tip->status === 'completed')
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 text-white flex items-center justify-center mx-auto mb-6 shadow-lg shadow-amber-500/30">
                    <i class="fa-solid fa-heart text-3xl" aria-hidden="true"></i>
                </div>
                <span class="section-label justify-center">Tip Diterima</span>
                <h1 class="text-2xl md:text-3xl font-black tracking-tight text-slate-900 dark:text-white mb-3">
                    Terima kasih! 🙏
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed mb-6">
                    Tip sebesar <span class="font-black text-indigo-600 dark:text-indigo-400">Rp {{ number_format($tip->amount, 0, ',', '.') }}</span>
                    sudah saya terima. Dukungan Anda sangat berarti — semoga sehat selalu dan dilancarkan rezekinya.
                </p>
            @elseif($tip->status === 'pending')
                <div class="w-20 h-20 rounded-full bg-amber-500/10 text-amber-500 flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-hourglass-half text-3xl" aria-hidden="true"></i>
                </div>
                <span class="section-label justify-center">Menunggu Konfirmasi</span>
                <h1 class="text-2xl md:text-3xl font-black tracking-tight text-slate-900 dark:text-white mb-3">
                    Pembayaran sedang diproses
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed mb-6">
                    Tip sebesar <span class="font-black">Rp {{ number_format($tip->amount, 0, ',', '.') }}</span> akan segera terkonfirmasi.
                    Halaman ini memperbarui status secara otomatis.
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400" id="poll-hint">
                    <i class="fa-solid fa-rotate fa-spin mr-1.5" aria-hidden="true"></i> Memeriksa status…
                </p>
                <script nonce="{{ csp_nonce() }}">
                    (function () {
                        var attempts = 0;
                        var max = 60;
                        var timer = setInterval(function () {
                            attempts++;
                            if (attempts > max) {
                                clearInterval(timer);
                                var hint = document.getElementById('poll-hint');
                                if (hint) hint.textContent = 'Belum terkonfirmasi. Silakan muat ulang halaman nanti.';
                                return;
                            }
                            fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                                .then(function (r) { return r.text(); })
                                .then(function (html) {
                                    var doc = new DOMParser().parseFromString(html, 'text/html');
                                    var status = doc.querySelector('[data-tip-status]');
                                    if (status && status.getAttribute('data-tip-status') !== 'pending') {
                                        clearInterval(timer);
                                        window.location.reload();
                                    }
                                })
                                .catch(function () {});
                        }, 3000);
                    })();
                </script>
            @else
                <div class="w-20 h-20 rounded-full bg-rose-500/10 text-rose-500 flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-circle-xmark text-3xl" aria-hidden="true"></i>
                </div>
                <span class="section-label justify-center">Gagal</span>
                <h1 class="text-2xl md:text-3xl font-black tracking-tight text-slate-900 dark:text-white mb-3">
                    Pembayaran tidak selesai
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed mb-6">
                    Tip sebesar Rp {{ number_format($tip->amount, 0, ',', '.') }} tidak jadi terkirim. Anda tetap bisa mencoba lagi kapan saja.
                </p>
            @endif

            <a href="{{ route('portfolio.index') }}#tip-jar" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 transition-colors">
                &larr; Kembali ke portfolio
            </a>
        </div>
        <span data-tip-status="{{ $tip->status }}" class="hidden" aria-hidden="true"></span>
    </main>

    @include('components.hot-toast')
</body>

</html>
