@extends('index')

@section('content')
    <!-- SCRIPT MIDTRANS (Wajib ada agar Pop-up bisa muncul) -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50">

        <div
            class="p-6 bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Detail Proyek: {{ $order->project_name }}</h1>
                <p class="text-sm text-slate-500 mt-0.5">Pantau progres, tagihan, dan ajukan revisi di halaman ini.</p>
            </div>
            <a href="{{ route('user_joki.dashboard') }}"
                class="inline-block text-sm font-semibold text-slate-500 hover:text-indigo-600 transition-colors bg-slate-100 hover:bg-indigo-50 px-4 py-2 rounded-lg">
                &larr; Kembali
            </a>
        </div>

        @if (session('success'))
            <div
                class="p-4 mb-6 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center">
                <i class="fa-solid fa-circle-check mr-2 text-lg"></i> {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri: Progress, Milestone, Revisi -->
            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-800 mb-4">Progres Keseluruhan</h3>
                    <div class="w-full bg-slate-100 rounded-full h-4 mb-3 overflow-hidden">
                        <div class="bg-indigo-600 h-4 rounded-full transition-all duration-500"
                            style="width: {{ $order->progress }}%"></div>
                    </div>
                    <p class="text-sm text-slate-600">Saat ini pengerjaan mencapai <strong
                            class="text-indigo-600 text-base">{{ $order->progress }}%</strong></p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-800 mb-4 border-b pb-2">Target Pengerjaan (Milestone)</h3>
                    @if ($order->milestones->count() > 0)
                        <div class="space-y-4 relative border-l-2 border-slate-100 ml-3 pl-5">
                            @foreach ($order->milestones as $milestone)
                                <div class="relative">
                                    <div
                                        class="absolute -left-[29px] top-1 w-4 h-4 rounded-full border-2 border-white {{ $milestone->status == 'done' ? 'bg-emerald-500' : ($milestone->status == 'working' ? 'bg-blue-500' : 'bg-slate-300') }}">
                                    </div>
                                    <h4 class="font-bold text-slate-800 text-sm">{{ $milestone->title }}</h4>
                                    <p class="text-xs text-slate-500 mt-1 mb-1">{{ $milestone->description }}</p>
                                    <div class="flex gap-3 text-[11px] font-semibold mt-2">
                                        <span
                                            class="{{ $milestone->status == 'done' ? 'text-emerald-600 bg-emerald-50' : ($milestone->status == 'working' ? 'text-blue-600 bg-blue-50' : 'text-slate-500 bg-slate-100') }} px-2 py-1 rounded">
                                            Status: {{ strtoupper($milestone->status) }}
                                        </span>
                                        @if ($milestone->due_date)
                                            <span class="text-rose-600 bg-rose-50 px-2 py-1 rounded"><i
                                                    class="fa-regular fa-calendar mr-1"></i> Target:
                                                {{ \Carbon\Carbon::parse($milestone->due_date)->format('d M Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500 italic text-center py-4">Belum ada milestone yang ditambahkan oleh
                            Admin.</p>
                    @endif
                </div>

                @if ($order->status == 'review' || $order->status == 'progress')
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <h3 class="font-bold text-slate-800 mb-4 border-b pb-2">Ajukan Revisi</h3>
                        <form action="{{ route('user_joki.revision.store', $order->id) }}" method="POST">
                            @csrf
                            <textarea name="revision_note" rows="3" required placeholder="Jelaskan bagian mana yang perlu diperbaiki..."
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm mb-3"></textarea>
                            <button type="submit"
                                class="bg-rose-500 hover:bg-rose-600 text-white font-semibold px-5 py-2.5 rounded-lg text-sm transition-colors w-full sm:w-auto">
                                Kirim Permintaan Revisi
                            </button>
                        </form>

                        @if ($order->revisions->count() > 0)
                            <div class="mt-6 space-y-3">
                                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Riwayat Revisi</h4>
                                @foreach ($order->revisions as $rev)
                                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-100 text-sm">
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="font-semibold text-slate-800">Catatan Anda:</span>
                                            <span
                                                class="text-[10px] px-2 py-1 rounded font-bold uppercase {{ $rev->status == 'resolved' ? 'bg-emerald-100 text-emerald-700' : ($rev->status == 'fixing' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                                                {{ $rev->status }}
                                            </span>
                                        </div>
                                        <p class="text-slate-600 mb-3">{{ $rev->revision_note }}</p>
                                        @if ($rev->admin_reply)
                                            <div class="bg-indigo-50 p-3 rounded border border-indigo-100">
                                                <span class="text-xs font-bold text-indigo-700 block mb-1">Balasan
                                                    Admin:</span>
                                                <p class="text-indigo-900 text-xs">{{ $rev->admin_reply }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Kolom Kanan: Info & Pembayaran MIDTRANS -->
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3">Tagihan Pembayaran</h3>

                    @if ($order->payments->count() > 0)
                        <div class="space-y-4">
                            @foreach ($order->payments as $payment)
                                <div
                                    class="border border-slate-200 rounded-lg p-4 {{ $payment->status == 'paid' ? 'bg-emerald-50/30' : 'bg-white' }}">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-bold text-slate-800 text-sm">{{ $payment->payment_name }}</span>
                                        <span
                                            class="text-xs px-2 py-1 rounded font-bold uppercase {{ $payment->status == 'paid' ? 'bg-emerald-100 text-emerald-700' : ($payment->status == 'unpaid' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                            {{ $payment->status }}
                                        </span>
                                    </div>
                                    <div class="text-xl font-black text-slate-900 mb-3">Rp
                                        {{ number_format($payment->amount, 0, ',', '.') }}</div>

                                    <!-- TOMBOL BAYAR MIDTRANS -->
                                    @if ($payment->status == 'unpaid' || $payment->status == 'failed')
                                        <button id="pay-button-{{ $payment->id }}"
                                            class="mt-2 w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2.5 rounded-lg transition-colors shadow-md shadow-indigo-200">
                                            <i class="fa-solid fa-credit-card mr-1"></i> Bayar Sekarang
                                        </button>

                                        <!-- Script Pemanggilan Popup Snap -->
                                        <script>
                                            document.getElementById('pay-button-{{ $payment->id }}').onclick = function() {
                                                snap.pay('{{ $payment->snap_token }}', {
                                                    // Jika pembayaran berhasil
                                                    onSuccess: function(result) {
                                                        window.location.reload();
                                                    },
                                                    // Jika pembayaran tertunda (misal VA)
                                                    onPending: function(result) {
                                                        alert("Menunggu pembayaran Anda diselesaikan!");
                                                        window.location.reload();
                                                    },
                                                    // Jika pembayaran gagal
                                                    onError: function(result) {
                                                        alert("Pembayaran gagal!");
                                                    },
                                                    // Jika klien menutup pop-up
                                                    onClose: function() {
                                                        console.log('Pop-up ditutup tanpa menyelesaikan pembayaran');
                                                    }
                                                });
                                            };
                                        </script>
                                    @else
                                        <!-- TAMPILAN JIKA LUNAS -->
                                        <p
                                            class="mt-2 text-xs text-emerald-600 font-bold text-center bg-emerald-100 py-2 rounded-lg border border-emerald-200">
                                            <i class="fa-solid fa-check-circle mr-1"></i> LUNAS
                                            @if ($payment->paid_at)
                                                ({{ \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y') }})
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6">
                            <i class="fa-solid fa-file-invoice-dollar text-3xl text-slate-300 mb-2"></i>
                            <p class="text-sm text-slate-500">Belum ada tagihan dari Admin.</p>
                        </div>
                    @endif
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3">Informasi Dasar</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Status</span>
                            <span class="font-bold text-indigo-600 uppercase">{{ $order->status }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Tech Stack</span>
                            <span class="font-semibold text-slate-800">{{ $order->tech_stack ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Harga Total</span>
                            <span class="font-bold text-emerald-600">Rp
                                {{ number_format($order->price ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
