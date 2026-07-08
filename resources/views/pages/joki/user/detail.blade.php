@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Detail Proyek: {{ $order->project_name }}" 
            subtitle="Pantau progres, tagihan, dan ajukan revisi di halaman ini." 
            icon="fa-solid fa-file-invoice">
            <x-slot:actions>
                <a href="{{ route('user_joki.progress') }}"
                    class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri: Progress, Milestone, Revisi -->
            <div class="lg:col-span-2 space-y-6">

                <x-ui.card class="p-6">
                    <h3 class="font-bold text-slate-800 mb-4">Progres Keseluruhan</h3>
                    <div class="w-full bg-slate-100 rounded-full h-4 mb-3 overflow-hidden">
                        <div class="bg-indigo-600 h-4 rounded-full transition-all duration-500"
                            style="width: {{ $order->progress }}%"></div>
                    </div>
                    <p class="text-sm text-slate-600">Saat ini pengerjaan mencapai <strong
                            class="text-indigo-600 text-base">{{ $order->progress }}%</strong></p>
                </x-ui.card>

                <x-ui.card class="p-6">
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
                </x-ui.card>

                @if ($order->status == 'review' || $order->status == 'progress')
                    <x-ui.card class="p-6">
                        <h3 class="font-bold text-slate-800 mb-4 border-b pb-2">Ajukan Revisi</h3>
                        <form action="{{ route('user_joki.revision.store', $order->hashid) }}" method="POST">
                            @csrf
                            <textarea name="revision_note" rows="3" required placeholder="Jelaskan bagian mana yang perlu diperbaiki..."
                                class="mb-3 w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"></textarea>
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
                    </x-ui.card>
                @endif

                @if ($order->status == 'completed')
                    <x-ui.card class="p-6">
                        <h3 class="font-bold text-slate-800 mb-4 border-b pb-2">Ulasan Anda</h3>
                        @if ($order->rating || $order->review)
                            <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                                <div class="flex items-center gap-1 text-amber-500 mb-2 text-lg">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid fa-star {{ $i <= $order->rating ? '' : 'text-slate-300' }}"></i>
                                    @endfor
                                </div>
                                <p class="text-sm text-slate-700 italic">"{{ $order->review }}"</p>
                            </div>
                        @else
                            <form action="{{ route('user_joki.review.store', $order->hashid) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-xs font-bold text-slate-700 mb-2">Rating (1-5)</label>
                                    <div class="flex items-center gap-4">
                                        @for($i = 5; $i >= 1; $i--)
                                        <label class="cursor-pointer text-center group">
                                            <input type="radio" name="rating" value="{{ $i }}" class="peer sr-only" required>
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 border-slate-200 peer-checked:border-amber-500 peer-checked:bg-amber-50 group-hover:border-amber-300 transition-all">
                                                <span class="font-bold text-slate-500 peer-checked:text-amber-500">{{ $i }}</span>
                                            </div>
                                        </label>
                                        @endfor
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-xs font-bold text-slate-700 mb-2">Ulasan</label>
                                    <textarea name="review" rows="3" required placeholder="Bagaimana pengalaman Anda bekerja sama dengan kami?"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"></textarea>
                                </div>
                                <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2.5 rounded-lg text-sm transition-colors w-full">
                                    Kirim Ulasan
                                </button>
                            </form>
                        @endif
                    </x-ui.card>
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
                    <x-ui.card class="p-6 bg-slate-800 text-white border-none shadow-md">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-slate-700 rounded-full flex justify-center items-center">
                                <i class="fa-solid fa-eye text-emerald-400"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-white text-sm">Live Preview</h3>
                                <p class="text-xs text-slate-400">Lihat hasil pengerjaan secara langsung.</p>
                            </div>
                        </div>
                        <a href="{{ $order->preview_url }}" target="_blank"
                            class="block w-full bg-indigo-600 hover:bg-indigo-500 text-center text-white font-bold px-4 py-2 rounded-lg text-sm transition-colors">
                            Buka Preview URL &rarr;
                        </a>
                    </x-ui.card>
                @endif

                @if ($order->status == 'completed')
                    <x-ui.card class="p-6 bg-gradient-to-br from-indigo-600 to-violet-600 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10">
                            <i class="fa-solid fa-server text-6xl"></i>
                        </div>
                        <div class="relative z-10">
                            <h3 class="font-bold text-white mb-2 text-lg">🚀 1-Click Deploy</h3>
                            <p class="text-indigo-100 text-xs mb-4">Proyek Anda sudah selesai! Anda bisa langsung meng-online-kannya ke layanan Ryaze Hosting hanya dengan satu klik.</p>
                            
                            @if ($order->is_deployed_to_hosting)
                                <div class="bg-white/20 border border-white/30 rounded-lg px-4 py-2 text-sm font-semibold flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-check-circle"></i> Sudah di-deploy
                                </div>
                            @else
                                <form action="{{ route('user_joki.deploy_hosting', $order->hashid) }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin men-deploy project ini ke Ryaze Hosting? (Memerlukan penyimpanan aktif)')"
                                        class="w-full bg-white text-indigo-700 hover:bg-slate-50 font-bold px-4 py-2.5 rounded-lg text-sm shadow-md transition-colors flex justify-center items-center gap-2">
                                        Deploy ke Ryaze Hosting
                                    </button>
                                </form>
                            @endif
                        </div>
                    </x-ui.card>
                @endif

                <x-ui.card class="p-6">
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

                                    <!-- TOMBOL BAYAR PAKASIR -->
                                    @if ($payment->status == 'unpaid' || $payment->status == 'failed')
                                        <button type="button" onclick="openPaymentModal({{ $payment->amount }}, '{{ number_format($payment->amount, 0, ',', '.') }}', '{{ $payment->invoice_number }}')"
                                            class="mt-2 block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2.5 rounded-lg transition-colors shadow-md shadow-indigo-200">
                                            <i class="fa-solid fa-credit-card mr-1"></i> Pilih Metode Pembayaran
                                        </button>
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
                </x-ui.card>

                <x-ui.card class="p-6">
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
                </x-ui.card>
            </div>
        </div>
    </x-ui.page-layout>

    <!-- Payment Modal -->
    <div id="paymentModal" tabindex="-1" class="hidden fixed inset-0 z-[100] flex items-center justify-center w-full h-full bg-slate-900/50 backdrop-blur-sm p-4">
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="text-lg font-bold text-slate-800">
                    Pilih Metode Pembayaran
                </h3>
                <button type="button" onclick="closePaymentModal()" class="text-slate-400 hover:bg-slate-100 hover:text-slate-900 rounded-lg text-sm w-8 h-8 flex justify-center items-center">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="text-center mb-6">
                    <p class="text-sm text-slate-500 font-medium mb-1">Total Tagihan</p>
                    <div class="text-3xl font-black text-slate-800">Rp <span id="modalPaymentAmount">0</span></div>
                    <p class="text-xs text-slate-400 mt-1">Invoice: <span id="modalPaymentInvoice" class="font-mono"></span></p>
                </div>

                <!-- Option 1: Pakasir -->
                <a id="btnPakasir" href="#" target="_blank" onclick="closePaymentModal()" class="flex items-center justify-between p-4 border-2 border-slate-100 rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <i class="fa-solid fa-bolt text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm">Otomatis (Virtual Account/QRIS)</h4>
                            <p class="text-xs text-slate-500">Konfirmasi instan, diproses otomatis.</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-300 group-hover:text-indigo-500"></i>
                </a>

                <!-- Option 2: DANA -->
                <div class="p-4 border-2 border-slate-100 rounded-xl space-y-3 mt-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                            <i class="fa-solid fa-wallet text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm">Transfer DANA</h4>
                            <p class="text-xs text-slate-500 font-mono text-lg font-bold mt-1 text-slate-700">{{ \App\Models\Setting::val('payment_dana', '085157433395') }}</p>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-slate-100">
                        <p class="text-[11px] text-slate-500 leading-relaxed mb-3">
                            Setelah melakukan transfer, silakan kirim bukti pembayaran melalui WhatsApp untuk diverifikasi secara manual oleh Admin.
                        </p>
                        <a id="btnWA" href="#" target="_blank" onclick="closePaymentModal()" class="block w-full text-center bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2 rounded-lg text-sm transition-colors shadow-sm">
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
            
            let pakasirSlug = "{{ config('services.pakasir.slug', 'ryaze') }}";
            let pakasirUrl = `https://app.pakasir.com/pay/${pakasirSlug}/${amount}?order_id=${invoice}`;
            document.getElementById('btnPakasir').href = pakasirUrl;

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
