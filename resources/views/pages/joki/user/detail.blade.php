@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Detail Proyek: {{ $order->project_name }}"
            eyebrow="Layanan Joki · Detail Pesanan"
            subtitle="Pantau progres, tagihan, dan ajukan revisi di halaman ini."
            icon="fa-solid fa-file-invoice">
            <x-slot:actions>
                <a href="{{ route('user_joki.progress') }}" class="ui-btn-secondary font-mono text-[11px] uppercase tracking-[0.15em]">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Kolom Kiri: Progress, Milestone, Revisi -->
            <div class="lg:col-span-2 space-y-6">

                <div class="ui-panel p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="ui-eyebrow text-[11px]">Progres Keseluruhan</h3>
                        <span class="font-mono text-[11px] font-bold text-[#1D5B96] dark:text-[#6BA8DC] tabular-nums">{{ $order->progress }}%</span>
                    </div>
                    <div class="w-full bg-[#E3EAF1] dark:bg-[#1B2C44] h-1.5 mb-3 overflow-hidden">
                        <div class="bg-[#1D5B96] dark:bg-[#6BA8DC] h-1.5 transition-all duration-500"
                            style="width: {{ $order->progress }}%"></div>
                    </div>
                    <p class="text-sm text-[#525252] dark:text-white/70">Saat ini pengerjaan mencapai <strong
                            class="text-[#1D5B96] dark:text-[#6BA8DC] font-mono text-base">{{ $order->progress }}%</strong></p>
                </div>

                <div class="ui-panel">
                    <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#0F1A2A]">
                        <h3 class="ui-eyebrow text-[11px]">Target Pengerjaan · Milestone</h3>
                        <span class="ui-eyebrow-muted">{{ $order->milestones->count() }} Item</span>
                    </div>
                    <div class="p-6">
                        @if ($order->milestones->count() > 0)
                            <div class="space-y-5 relative border-l border-[#E3EAF1] dark:border-[#1B2C44] ml-2 pl-6">
                                @foreach ($order->milestones as $milestone)
                                    <div class="relative">
                                        <div
                                            class="absolute -left-[29px] top-1 w-3 h-3 border-2 border-white dark:border-[#0A1322] {{ $milestone->status == 'done' ? 'bg-emerald-500' : ($milestone->status == 'working' ? 'bg-[#1D5B96]' : 'bg-[#C9D5E2] dark:bg-[#24384F]') }}">
                                        </div>
                                        <h4 class="font-bold text-[#0F1A2A] dark:text-white text-sm">{{ $milestone->title }}</h4>
                                        <p class="text-xs text-[#737373] dark:text-white/45 mt-1 mb-1">{{ $milestone->description }}</p>
                                        <div class="flex gap-2 font-mono text-[10px] uppercase tracking-[0.15em] mt-2">
                                            <span
                                                class="px-2 py-1 border {{ $milestone->status == 'done' ? 'text-emerald-700 border-emerald-200 dark:text-emerald-400 dark:border-emerald-500/40' : ($milestone->status == 'working' ? 'text-[#1D5B96] border-[#C9D5E2] dark:text-[#6BA8DC] dark:border-[#1D5B96]/40' : 'text-[#737373] border-[#E3EAF1] dark:text-white/40 dark:border-[#1B2C44]') }}">
                                                Status: {{ strtoupper($milestone->status) }}
                                            </span>
                                            @if ($milestone->due_date)
                                                <span class="text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-500/40 px-2 py-1"><i
                                                        class="fa-regular fa-calendar mr-1"></i> Target:
                                                    {{ \Carbon\Carbon::parse($milestone->due_date)->format('d M Y') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-[#94A3B4] dark:text-white/35 font-mono text-[11px] uppercase tracking-[0.2em] text-center py-4">Belum ada milestone yang ditambahkan oleh Admin.</p>
                        @endif
                    </div>
                </div>

                @if ($order->status == 'review' || $order->status == 'progress')
                    <div class="ui-panel">
                        <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#0F1A2A]">
                            <h3 class="ui-eyebrow text-[11px]">Ajukan Revisi</h3>
                            <span class="ui-eyebrow-muted">{{ $order->revisions->count() }} Riwayat</span>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('user_joki.revision.store', $order->hashid) }}" method="POST">
                                @csrf
                                <textarea name="revision_note" rows="3" required placeholder="Jelaskan bagian mana yang perlu diperbaiki..."
                                    class="mb-3 w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1D5B96]/20 focus:border-[#1D5B96] dark:focus:border-[#6BA8DC] outline-none transition"></textarea>
                                <button type="submit"
                                    class="ui-btn-primary font-mono text-[11px] uppercase tracking-[0.15em]">
                                    Kirim Permintaan Revisi
                                </button>
                            </form>

                            @if ($order->revisions->count() > 0)
                                <div class="mt-6 space-y-3">
                                    <h4 class="ui-eyebrow-muted">Riwayat Revisi</h4>
                                    @foreach ($order->revisions as $rev)
                                        <div class="bg-[#FAFBFD] dark:bg-[#0D1828] p-4 border border-[#E3EAF1] dark:border-[#1B2C44] text-sm">
                                            <div class="flex justify-between items-start mb-2">
                                                <span class="font-semibold text-[#0F1A2A] dark:text-white">Catatan Anda:</span>
                                                <span
                                                    class="font-mono text-[10px] px-2 py-1 font-bold uppercase border {{ $rev->status == 'resolved' ? 'text-emerald-700 border-emerald-200 dark:text-emerald-400 dark:border-emerald-500/40' : ($rev->status == 'fixing' ? 'text-[#1D5B96] border-[#C9D5E2] dark:text-[#6BA8DC] dark:border-[#1D5B96]/40' : 'text-amber-700 border-amber-200 dark:text-amber-400 dark:border-amber-500/40') }}">
                                                    {{ $rev->status }}
                                                </span>
                                            </div>
                                            <p class="text-[#525252] dark:text-white/70 mb-3">{{ $rev->revision_note }}</p>
                                            @if ($rev->admin_reply)
                                                <div class="bg-[#E8F0F9] dark:bg-[#12233A] p-3 border border-[#C9D5E2] dark:border-[#24384F]">
                                                    <span class="font-mono text-[10px] font-bold uppercase tracking-[0.15em] text-[#1D5B96] dark:text-[#6BA8DC] block mb-1">Balasan
                                                        Admin:</span>
                                                    <p class="text-[#0F1A2A] dark:text-white/80 text-xs">{{ $rev->admin_reply }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($order->status == 'completed')
                    <div class="ui-panel">
                        <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#0F1A2A]">
                            <h3 class="ui-eyebrow text-[11px]">Ulasan Anda</h3>
                            @if ($order->rating)
                                <span class="ui-eyebrow-muted">{{ $order->rating }}/5</span>
                            @endif
                        </div>
                        <div class="p-6">
                            @if ($order->rating || $order->review)
                                <div class="bg-[#FAFBFD] dark:bg-[#0D1828] p-4 border border-[#E3EAF1] dark:border-[#1B2C44]">
                                    <div class="flex items-center gap-1 text-amber-500 dark:text-amber-400 mb-2 text-lg">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa-solid fa-star {{ $i <= $order->rating ? '' : 'text-[#C9D5E2] dark:text-white/30' }}"></i>
                                        @endfor
                                    </div>
                                    <p class="text-sm text-[#525252] dark:text-white/70 italic">"{{ $order->review }}"</p>
                                </div>
                            @else
                                <form action="{{ route('user_joki.review.store', $order->hashid) }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="ui-eyebrow-muted block mb-2">Rating (1-5)</label>
                                        <div class="flex items-center gap-4">
                                            @for($i = 5; $i >= 1; $i--)
                                            <label class="cursor-pointer text-center group">
                                                <input type="radio" name="rating" value="{{ $i }}" class="peer sr-only" required>
                                                <div class="w-10 h-10 flex items-center justify-center border-2 border-[#C9D5E2] dark:border-[#24384F] peer-checked:border-amber-500 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-500/10 group-hover:border-amber-300 transition-all">
                                                    <span class="font-mono font-bold text-[#525252] dark:text-white/50 peer-checked:text-amber-500">{{ $i }}</span>
                                                </div>
                                            </label>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="ui-eyebrow-muted block mb-2">Ulasan</label>
                                        <textarea name="review" rows="3" required placeholder="Bagaimana pengalaman Anda bekerja sama dengan kami?"
                                            class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1D5B96]/20 focus:border-[#1D5B96] dark:focus:border-[#6BA8DC] outline-none transition"></textarea>
                                    </div>
                                    <button type="submit"
                                        class="ui-btn-primary font-mono text-[11px] uppercase tracking-[0.15em] w-full sm:w-auto">
                                        Kirim Ulasan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Live Chat Component --}}
                @if ($order->status != 'pending')
                    <x-joki-chat
                        :order="$order"
                        :user="Auth::user()"
                        :fetch-route="route('user_joki.chat.fetch', $order->hashid)"
                        :store-route="route('user_joki.chat.store', $order->hashid)"
                    />
                @endif
            </div>

            <!-- Kolom Kanan: Info & Pembayaran MIDTRANS -->
            <div class="space-y-6">

                @if ($order->preview_url)
                    <div class="ui-panel p-6 bg-[#12233A] dark:bg-[#12233A] border-[#1D5B96] dark:border-[#1D5B96]">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-[#0A1322] border border-[#1D5B96] flex justify-center items-center">
                                <i class="fa-solid fa-eye text-[#6BA8DC]"></i>
                            </div>
                            <div>
                                <h3 class="font-mono text-[11px] uppercase tracking-[0.2em] font-semibold text-white">Live Preview</h3>
                                <p class="text-xs text-[#7FB4E2]/80">Lihat hasil pengerjaan secara langsung.</p>
                            </div>
                        </div>
                        <a href="{{ $order->preview_url }}" target="_blank"
                            class="block w-full bg-[#1D5B96] hover:bg-[#2A6FAD] text-center text-white font-mono text-[11px] uppercase tracking-[0.15em] font-bold px-4 py-2.5 transition-colors">
                            Buka Preview URL <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
                        </a>
                    </div>
                @endif

                @if ($order->status == 'completed')
                    <div class="ui-panel p-6 bg-[#12233A] dark:bg-[#12233A] border-[#1D5B96] dark:border-[#1D5B96]">
                        <h3 class="font-mono text-[11px] uppercase tracking-[0.2em] font-semibold text-[#7FB4E2] mb-2">1-Click Deploy</h3>
                        <p class="text-white/70 text-xs mb-4">Proyek Anda sudah selesai! Anda bisa langsung meng-online-kannya ke layanan Ryaze Hosting hanya dengan satu klik.</p>

                        @if ($order->is_deployed_to_hosting)
                            <div class="bg-[#0A1322] border border-[#1D5B96] px-4 py-2 font-mono text-[11px] uppercase tracking-[0.15em] text-emerald-400 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-check"></i> Sudah di-deploy
                            </div>
                        @else
                            <form action="{{ route('user_joki.deploy_hosting', $order->hashid) }}" method="POST">
                                @csrf
                                <button type="submit" onclick="event.preventDefault(); let f = this.closest('form'); swConfirm('Deploy ke Ryaze Hosting?', 'Memerlukan penyimpanan aktif. Lanjutkan?').then(res => { if(res.isConfirmed) f.submit(); }); return false;"
                                    class="w-full bg-white text-[#12233A] hover:bg-[#E8F0F9] font-mono text-[11px] uppercase tracking-[0.15em] font-bold px-4 py-2.5 transition-colors flex justify-center items-center gap-2">
                                    Deploy ke Ryaze Hosting
                                </button>
                            </form>
                        @endif
                    </div>
                @endif

                <div class="ui-panel">
                    <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#0F1A2A]">
                        <h3 class="ui-eyebrow text-[11px]">Tagihan Pembayaran</h3>
                        <span class="ui-eyebrow-muted">{{ $order->payments->count() }} Invoice</span>
                    </div>
                    <div class="p-6">
                        @if ($order->payments->count() > 0)
                            <div class="ui-grid grid-cols-1">
                                @foreach ($order->payments as $payment)
                                    <div
                                        class="{{ $payment->status == 'paid' ? 'ui-cell-alt' : 'ui-cell' }} p-5">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="font-mono text-[11px] font-semibold uppercase tracking-[0.15em] text-[#0F1A2A] dark:text-white">{{ $payment->payment_name }}</span>
                                            @php
                                                $payClass = match($payment->status) {
                                                    'paid' => 'text-emerald-700 border-emerald-200 dark:text-emerald-400 dark:border-emerald-500/40',
                                                    'unpaid' => 'text-rose-700 border-rose-200 dark:text-rose-400 dark:border-rose-500/40',
                                                    default => 'text-amber-700 border-amber-200 dark:text-amber-400 dark:border-amber-500/40',
                                                };
                                            @endphp
                                            <span
                                                class="ui-badge {{ $payClass }}">
                                                {{ $payment->status }}
                                            </span>
                                        </div>
                                        <div class="ui-stat-value text-xl tabular-nums mb-4">Rp
                                            {{ number_format($payment->amount, 0, ',', '.') }}</div>

                                        <!-- TOMBOL PILIH METODE PEMBAYARAN -->
                                        @if ($payment->status == 'unpaid' || $payment->status == 'failed')
                                            <button type="button" onclick="openPaymentModal({{ $payment->amount }}, '{{ number_format($payment->amount, 0, ',', '.') }}', '{{ $payment->invoice_number }}')"
                                                class="w-full ui-btn-primary font-mono text-[11px] uppercase tracking-[0.15em]">
                                                <i class="fa-solid fa-credit-card text-[10px]"></i> Pilih Metode Pembayaran
                                            </button>
                                        @else
                                            <!-- TAMPILAN JIKA LUNAS -->
                                            <p
                                                class="font-mono text-[11px] uppercase tracking-[0.15em] text-emerald-600 dark:text-emerald-400 font-bold text-center border border-emerald-200 dark:border-emerald-500/40 py-2">
                                                <i class="fa-solid fa-check mr-1"></i> LUNAS
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
                                <i class="fa-solid fa-file-invoice-dollar text-3xl text-[#C9D5E2] dark:text-white/30 mb-2"></i>
                                <p class="ui-eyebrow-muted text-[11px]">Belum ada tagihan dari Admin.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="ui-panel">
                    <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#0F1A2A]">
                        <h3 class="ui-eyebrow text-[11px]">Informasi Dasar</h3>
                    </div>
                    <dl>
                        <div class="ui-spec-row">
                            <dt class="ui-eyebrow-muted">Status</dt>
                            <dd class="font-mono text-[12px] font-bold text-[#1D5B96] dark:text-[#6BA8DC] uppercase">{{ $order->status }}</dd>
                        </div>
                        <div class="ui-spec-row">
                            <dt class="ui-eyebrow-muted">Tech Stack</dt>
                            <dd class="font-mono text-[12px] text-[#0F1A2A] dark:text-white">{{ $order->tech_stack ?? '-' }}</dd>
                        </div>
                        <div class="ui-spec-row">
                            <dt class="ui-eyebrow-muted">Harga Total</dt>
                            <dd class="font-mono text-[12px] font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">Rp
                                {{ number_format($order->price ?? 0, 0, ',', '.') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </x-ui.page-layout>

    <!-- Payment Modal -->
    <div id="paymentModal" tabindex="-1" class="hidden fixed inset-0 z-[100] flex items-center justify-center w-full h-full bg-[#0F1A2A]/60 p-4">
        <div class="relative ui-panel w-full max-w-md overflow-hidden">
            <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#0F1A2A]">
                <h3 class="ui-eyebrow text-[11px]">
                    Pilih Metode Pembayaran
                </h3>
                <button type="button" onclick="closePaymentModal()" class="ui-icon-btn ui-icon-btn-neutral">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="text-center mb-6">
                    <p class="ui-eyebrow-muted mb-1">Total Tagihan</p>
                    <div class="ui-stat-value text-3xl tabular-nums">Rp <span id="modalPaymentAmount">0</span></div>
                    <p class="text-xs text-[#94A3B4] dark:text-white/35 mt-1 font-mono">Invoice: <span id="modalPaymentInvoice" class="font-mono"></span></p>
                </div>

                <!-- Option 1: QRIS Dinamis (Rekomendasi) -->
                <a id="btnQris" href="#" onclick="closePaymentModal()" class="flex items-center justify-between p-4 border-2 border-emerald-500/40 bg-emerald-500/5 hover:border-emerald-500 hover:bg-emerald-500/10 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/40 flex items-center justify-center shrink-0 text-emerald-600 dark:text-emerald-400">
                            <i class="fa-solid fa-qrcode text-xl"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="font-bold text-[#0F1A2A] dark:text-white text-sm">QRIS Dinamis</h4>
                                <span class="font-mono text-[10px] font-bold uppercase tracking-[0.15em] border border-emerald-200 dark:border-emerald-500/40 text-emerald-700 dark:text-emerald-400 px-1.5 py-0.5">Instan</span>
                            </div>
                            <p class="text-xs text-[#737373] dark:text-white/45">Scan via m-Banking & e-Wallet (BCA, Mandiri, GoPay, OVO, Dana).</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-emerald-500 text-xs"></i>
                </a>

                <!-- Option 2: DOKU Checkout -->
                <a id="btnDoku" href="#" onclick="closePaymentModal()" class="flex items-center justify-between p-4 border-2 border-[#E3EAF1] dark:border-white/10 hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white dark:bg-white/5 border border-[#C9D5E2] dark:border-white/10 flex items-center justify-center shrink-0 text-[#1D5B96] dark:text-[#6BA8DC] group-hover:bg-[#1D5B96] group-hover:text-white transition-colors">
                            <i class="fa-solid fa-credit-card text-xl"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="font-bold text-[#0F1A2A] dark:text-white text-sm">DOKU Checkout</h4>
                                <span class="font-mono text-[10px] font-bold uppercase tracking-[0.15em] border border-[#C9D5E2] dark:border-[#1D5B96]/40 text-[#1D5B96] dark:text-[#6BA8DC] px-1.5 py-0.5">Semua Metode</span>
                            </div>
                            <p class="text-xs text-[#737373] dark:text-white/45">VA, e-Wallet, QRIS &amp; Kartu Kredit via DOKU.</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-[#1D5B96] dark:text-[#6BA8DC] text-xs"></i>
                </a>

                <!-- Option 2: DANA -->
                <div class="p-4 border-2 border-[#E3EAF1] dark:border-[#1B2C44] space-y-3 mt-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-[#E8F0F9] dark:bg-[#1D5B96]/10 border border-[#C9D5E2] dark:border-[#1D5B96]/40 flex items-center justify-center text-[#1D5B96] dark:text-[#6BA8DC]">
                            <i class="fa-solid fa-wallet text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-[#0F1A2A] dark:text-white text-sm">Transfer DANA</h4>
                            <p class="text-xs text-[#737373] dark:text-white/45 font-mono text-lg font-bold mt-1 text-[#0F1A2A] dark:text-white">{{ \App\Models\Setting::val('payment_dana', '085157433395') }}</p>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-[#EDF2F7] dark:border-[#152238]">
                        <p class="text-[11px] text-[#737373] dark:text-white/45 leading-relaxed mb-3">
                            Setelah melakukan transfer, silakan kirim bukti pembayaran melalui WhatsApp untuk diverifikasi secara manual oleh Admin.
                        </p>
                        <a id="btnWA" href="#" target="_blank" onclick="closePaymentModal()" class="block w-full text-center bg-emerald-600 hover:bg-emerald-700 text-white font-mono text-[11px] uppercase tracking-[0.15em] font-bold py-2.5 transition-colors">
                            <i class="fa-brands fa-whatsapp mr-1"></i> Konfirmasi ke Admin
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openPaymentModal(amount, formattedAmount, invoice) {
            document.getElementById('modalPaymentAmount').innerText = formattedAmount;
            document.getElementById('modalPaymentInvoice').innerText = invoice;

            let qrisUrl = "{{ url('/payment/qris') }}/" + encodeURIComponent(invoice);
            let btnQris = document.getElementById('btnQris');
            if (btnQris) btnQris.href = qrisUrl;

            let dokuUrl = "{{ url('/payment/doku') }}?order_id=" + encodeURIComponent(invoice);
            let btnDoku = document.getElementById('btnDoku');
            if (btnDoku) btnDoku.href = dokuUrl;

            let adminWa = "{{ \App\Models\Setting::val('contact_whatsapp', '') }}";
            let waMessage = `Halo Admin, saya ingin konfirmasi pembayaran untuk Invoice *${invoice}* sebesar *Rp ${formattedAmount}* via DANA. Berikut lampiran buktinya:`;
            let waUrl = `https://wa.me/62${adminWa}?text=${encodeURIComponent(waMessage)}`;
            document.getElementById('btnWA').href = waUrl;

            document.getElementById('paymentModal').classList.remove('hidden');
        }
        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }
    </script>
@endsection
