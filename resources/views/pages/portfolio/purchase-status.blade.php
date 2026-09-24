<!DOCTYPE html>
<html lang="id" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pembelian — {{ $purchase->product->name }}</title>
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
        <div class="glass-card rounded-3xl p-8 md:p-10 shadow-xl">
            <div class="text-center mb-8">
                <div class="w-16 h-16 rounded-2xl mx-auto mb-5 flex items-center justify-center {{ $purchase->status === 'paid' ? 'bg-emerald-500/10 text-emerald-500' : ($purchase->status === 'failed' ? 'bg-rose-500/10 text-rose-500' : 'bg-amber-500/10 text-amber-500') }}">
                    <i class="fa-solid {{ $purchase->status === 'paid' ? 'fa-circle-check' : ($purchase->status === 'failed' ? 'fa-circle-xmark' : 'fa-hourglass-half') }} text-2xl" aria-hidden="true"></i>
                </div>
                <span class="section-label justify-center">{{ $purchase->status === 'paid' ? 'Lunas' : ($purchase->status === 'failed' ? 'Gagal' : 'Menunggu Pembayaran') }}</span>
                <h1 class="text-2xl md:text-3xl font-black tracking-tight text-slate-900 dark:text-white mb-2">
                    {{ $purchase->product->name }}
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Order ID: <span class="font-mono font-semibold">{{ $purchase->order_id }}</span>
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200/70 dark:border-white/10 bg-white/60 dark:bg-white/5 p-5 mb-6 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <span class="text-slate-500 dark:text-slate-400">Produk</span>
                    <span class="font-bold text-slate-800 dark:text-slate-100 text-right">{{ $purchase->product->name }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-slate-500 dark:text-slate-400">Harga</span>
                    <span class="font-black text-slate-900 dark:text-white">Rp {{ number_format($purchase->amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-slate-500 dark:text-slate-400">Status</span>
                    @php
                        $statusMap = [
                            'paid' => ['label' => 'Lunas', 'class' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'],
                            'pending' => ['label' => 'Pending', 'class' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400'],
                            'failed' => ['label' => 'Gagal', 'class' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400'],
                        ];
                        $st = $statusMap[$purchase->status] ?? ['label' => $purchase->status, 'class' => 'bg-slate-500/10 text-slate-500'];
                    @endphp
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase {{ $st['class'] }}">{{ $st['label'] }}</span>
                </div>
                @if($purchase->paid_at)
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500 dark:text-slate-400">Dibayar</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $purchase->paid_at->format('d M Y H:i') }}</span>
                    </div>
                @endif
            </div>

            @if(session('error'))
                <div class="rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-700 dark:text-rose-300 text-sm px-4 py-3 mb-5" role="alert">
                    <i class="fa-solid fa-triangle-exclamation mr-2" aria-hidden="true"></i>{{ session('error') }}
                </div>
            @endif

            @if($purchase->status === 'paid')
                <a href="{{ route('portfolio.products.download', $purchase->order_id) }}"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white text-sm font-black hover:shadow-xl hover:shadow-indigo-500/25 transition-all">
                    <i class="fa-solid fa-download" aria-hidden="true"></i> Download File Produk
                </a>
            @elseif($purchase->status === 'pending' && $payUrl)
                <div class="space-y-3" x-data="{ checking: false }" id="pay-area">
                    <a href="{{ $payUrl }}" target="_blank" rel="noopener" id="btn-pay"
                        class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white text-sm font-black hover:shadow-xl hover:shadow-indigo-500/25 transition-all">
                        <i class="fa-solid fa-wallet" aria-hidden="true"></i> Bayar Sekarang — Rp {{ number_format($purchase->amount, 0, ',', '.') }}
                    </a>
                    <p class="text-center text-xs text-slate-500 dark:text-slate-400" id="poll-hint">
                        <i class="fa-solid fa-rotate fa-spin mr-1.5" aria-hidden="true"></i>
                        Halaman ini memeriksa status pembayaran secara otomatis.
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
                                    if (hint) hint.textContent = 'Belum terdeteksi. Muat ulang halaman untuk cek status.';
                                    return;
                                }
                                fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                                    .then(function (r) { return r.text(); })
                                    .then(function (html) {
                                        var doc = new DOMParser().parseFromString(html, 'text/html');
                                        var status = doc.querySelector('[data-purchase-status]');
                                        if (status && status.getAttribute('data-purchase-status') !== 'pending') {
                                            clearInterval(timer);
                                            window.location.reload();
                                        }
                                    })
                                    .catch(function () {});
                            }, 3000);
                        })();
                    </script>
                </div>
            @elseif($purchase->status === 'failed')
                <p class="text-center text-sm text-slate-500 dark:text-slate-400 mb-4">
                    Pembayaran gagal atau dibatalkan. Anda bisa mencoba lagi dari halaman portfolio.
                </p>
            @endif

            <div class="mt-6 text-center">
                <a href="{{ route('portfolio.index') }}#products" class="text-sm font-semibold text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 transition-colors">
                    &larr; Kembali ke portfolio
                </a>
            </div>
        </div>
        <span data-purchase-status="{{ $purchase->status }}" class="hidden" aria-hidden="true"></span>
    </main>

    @include('components.hot-toast')
</body>

</html>
