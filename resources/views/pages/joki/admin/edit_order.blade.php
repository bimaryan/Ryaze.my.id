@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Control Center: {{ $order->order_number }}"
            eyebrow="Admin Joki · Kelola Pesanan"
            icon="fa-solid fa-pen-to-square"
        >
            <x-slot:subtitle>
                Klien: <span class="font-semibold text-[#1D5B96] dark:text-[#6BA8DC]">{{ $order->client->name }}</span> | Layanan: {{ $order->service->name }}
            </x-slot:subtitle>
            <x-slot:actions>
                @if($order->status == 'completed')
                    <form action="{{ route('admin_joki.orders.portfolio', $order->hashid) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="ui-btn-primary mr-2">
                            <i class="fa-solid fa-star mr-2 text-xs"></i> Jadikan Portofolio
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin_joki.orders') }}" class="ui-btn-secondary">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        @if ($errors->any())
            <div class="mt-6 p-4 bg-rose-50 dark:bg-rose-500/10 border border-rose-300 dark:border-rose-500/40 text-rose-700 dark:text-rose-300">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation mt-1"></i>
                    <div>
                        <h3 class="font-bold text-sm">Gagal memperbarui pesanan:</h3>
                        <ul class="list-disc list-inside text-sm mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-6 grid grid-cols-1 xl:grid-cols-3 gap-6">

            <div class="xl:col-span-2 space-y-6">

                <div class="ui-panel">
                    <div class="ui-panel-head">
                        <h3 class="ui-modal-title">Update Status & Hasil Kerja</h3>
                        <span class="ui-status">Form Utama</span>
                    </div>
                    <form action="{{ route('admin_joki.orders.update', $order->hashid) }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="ui-eyebrow-muted block mb-1.5">Status Proyek</label>
                                <select name="status"
                                    class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="progress" {{ $order->status == 'progress' ? 'selected' : '' }}>In
                                        Progress (Coding)</option>
                                    <option value="review" {{ $order->status == 'review' ? 'selected' : '' }}>Review Klien
                                    </option>
                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>
                                        Completed (Selesai)</option>
                                    <option value="canceled" {{ $order->status == 'canceled' ? 'selected' : '' }}>Canceled
                                        (Batal)</option>
                                </select>
                            </div>
                            <div>
                                <label class="ui-eyebrow-muted block mb-1.5">Progres (%)</label>
                                <input type="number" name="progress" min="0" max="100"
                                    value="{{ $order->progress }}"
                                    class="w-full px-3 py-2 bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] focus:border-[#1D5B96] outline-none text-sm font-mono transition">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                            <div>
                                <label class="ui-eyebrow-muted block mb-1.5">Harga Deal (Rp)</label>
                                <input type="number" name="price" value="{{ $order->price }}"
                                    class="w-full px-3 py-2 bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] focus:border-[#1D5B96] outline-none text-sm font-mono transition">
                            </div>
                            <div>
                                <label class="ui-eyebrow-muted block mb-1.5">Live Preview URL</label>
                                <input type="url" name="preview_url" value="{{ $order->preview_url }}" placeholder="https://staging.ryaze.my.id"
                                    class="w-full px-3 py-2 bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] focus:border-[#1D5B96] outline-none text-sm font-mono transition">
                            </div>
                            <div>
                                <label class="ui-eyebrow-muted block mb-1.5">Link Repo / GitHub</label>
                                <input type="url" name="repo_link" value="{{ $order->repo_link }}"
                                    class="w-full px-3 py-2 bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] focus:border-[#1D5B96] outline-none text-sm font-mono transition">
                            </div>
                            <div>
                                <label class="ui-eyebrow-muted block mb-1.5">Link Demo Akhir</label>
                                <input type="url" name="demo_link" value="{{ $order->demo_link }}"
                                    class="w-full px-3 py-2 bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] focus:border-[#1D5B96] outline-none text-sm font-mono transition">
                            </div>
                        </div>
                        <div class="text-right pt-2">
                            <button type="submit" class="ui-btn-primary">Simpan Perubahan Utama</button>
                        </div>
                    </form>
                </div>

                <div class="ui-panel">
                    <div class="ui-panel-head">
                        <h3 class="ui-modal-title">Target Pengerjaan (Milestones)</h3>
                        <span class="ui-status">{{ $order->milestones->count() }} Item</span>
                    </div>
                    <div class="p-6">

                        <form action="{{ route('admin_joki.milestone.store', $order->hashid) }}" method="POST"
                            class="mb-6 bg-[#F4F8FC] dark:bg-[#0D1828] p-4 border border-[#E3EAF1] dark:border-[#1B2C44]">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                                <div class="md:col-span-2">
                                    <input type="text" name="title" required placeholder="Judul Tugas (Cth: Slicing UI)"
                                        class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition">
                                </div>
                                <div>
                                    <input type="date" name="due_date"
                                        class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm font-mono focus:border-[#1D5B96] outline-none transition">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div class="md:col-span-2">
                                    <input type="text" name="description" placeholder="Deskripsi singkat..."
                                        class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition">
                                </div>
                                <div class="flex gap-2">
                                    <select name="status" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition">
                                        <option value="pending">Pending</option>
                                        <option value="working">Working</option>
                                        <option value="done">Done</option>
                                    </select>
                                    <button type="submit"
                                        class="ui-icon-btn ui-icon-btn-success"><i
                                            class="fa-solid fa-plus text-xs"></i></button>
                                </div>
                            </div>
                        </form>

                        <div class="space-y-3">
                            @forelse($order->milestones as $m)
                                <div
                                    class="flex justify-between items-center p-3 border border-[#E3EAF1] dark:border-[#1B2C44] {{ $m->status == 'done' ? 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-200 dark:border-emerald-500/40' : 'bg-white dark:bg-[#0A1322]' }}">
                                    <div>
                                        <h4 class="font-bold text-sm text-[#0F1A2A] dark:text-white">{{ $m->title }}</h4>
                                        <p class="text-xs text-[#737373] dark:text-white/40">{{ $m->description }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span
                                            class="ui-badge {{ $m->status == 'done' ? 'ui-badge-success' : ($m->status == 'working' ? 'ui-badge-primary' : '') }}">
                                            {{ strtoupper($m->status) }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-[#94A3B4] dark:text-white/35 font-mono uppercase tracking-[0.2em] text-center py-2">Belum ada milestone.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="ui-panel">
                    <div class="ui-panel-head">
                        <h3 class="ui-modal-title flex items-center gap-2">
                            <i class="fa-solid fa-comments text-[#1D5B96] dark:text-[#6BA8DC] text-sm"></i>
                            Permintaan Revisi Klien
                        </h3>
                        <span class="ui-badge ui-badge-primary">{{ $order->revisions->count() }} Permintaan</span>
                    </div>
                    <div class="p-6 space-y-6">
                        @forelse($order->revisions as $rev)
                            <div class="relative pl-4 border-l-2 {{ $rev->status == 'resolved' ? 'border-emerald-500' : ($rev->status == 'fixing' ? 'border-[#1D5B96]' : ($rev->status == 'rejected' ? 'border-rose-500' : 'border-amber-400')) }}">
                                <!-- Status Dot -->
                                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full border-4 border-white dark:border-[#0F1A2A] {{ $rev->status == 'resolved' ? 'bg-emerald-500' : ($rev->status == 'fixing' ? 'bg-[#1D5B96]' : ($rev->status == 'rejected' ? 'bg-rose-500' : 'bg-amber-400')) }}"></div>

                                <div class="bg-white dark:bg-[#0A1322] border border-[#E3EAF1] dark:border-[#1B2C44] overflow-hidden">
                                    <!-- Header (Client Note) -->
                                    <div class="p-4 {{ $rev->status == 'pending' ? 'bg-amber-50/30 dark:bg-amber-500/10' : 'bg-[#FAFBFD] dark:bg-[#0D1828]' }} border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                                        <div class="flex justify-between items-start gap-4">
                                            <div class="flex items-start gap-3">
                                                <div class="w-8 h-8 bg-[#F0F6FC] dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] text-[#525252] dark:text-white/60 flex items-center justify-center shrink-0 mt-0.5">
                                                    <i class="fa-solid fa-user text-xs"></i>
                                                </div>
                                                <div>
                                                    <p class="text-[11px] font-bold text-[#737373] dark:text-white/40 mb-1 font-mono uppercase tracking-[0.15em]">
                                                        {{ $order->client->name }} &bull; {{ $rev->created_at->format('d M Y, H:i') }}
                                                    </p>
                                                    <div class="text-sm text-[#0F1A2A] dark:text-white leading-relaxed font-medium">
                                                        "{{ $rev->revision_note }}"
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="ui-badge flex-shrink-0
                                                {{ $rev->status == 'resolved' ? 'ui-badge-success' :
                                                   ($rev->status == 'fixing' ? 'ui-badge-primary' :
                                                   ($rev->status == 'rejected' ? 'ui-badge-danger' : 'ui-badge-warning')) }}">
                                                {{ $rev->status }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Body (Admin Reply & Form) -->
                                    <div class="p-4 bg-white dark:bg-[#0A1322]">
                                        @if($rev->admin_reply)
                                        <div class="mb-4 bg-[#F0F6FC] dark:bg-[#0D1828] p-3 border border-[#E3EAF1] dark:border-[#1B2C44]">
                                            <div class="flex items-start gap-3">
                                                <div class="w-7 h-7 bg-[#E8F0F9] dark:bg-[#1D5B96]/20 text-[#1D5B96] dark:text-[#6BA8DC] flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <i class="fa-solid fa-headset text-[10px]"></i>
                                                </div>
                                                <div>
                                                    <p class="text-[11px] font-bold text-[#1D5B96] dark:text-[#6BA8DC] mb-0.5 font-mono uppercase tracking-[0.15em]">Admin Reply</p>
                                                    <p class="text-sm text-[#525252] dark:text-white/70">{{ $rev->admin_reply }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <form action="{{ route('admin_joki.revision.reply', $rev->hashid) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <label class="ui-eyebrow-muted block mb-2">Update Status & Tanggapan</label>
                                            <div class="flex flex-col sm:flex-row gap-3">
                                                <div class="relative flex-1">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#94A3B4] dark:text-white/35">
                                                        <i class="fa-solid fa-pen text-xs"></i>
                                                    </div>
                                                    <input type="text" name="admin_reply" value="{{ $rev->admin_reply }}"
                                                        placeholder="Ketik tanggapan Anda di sini..." required
                                                        class="w-full pl-9 pr-3 py-2.5 bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] focus:border-[#1D5B96] text-sm outline-none transition">
                                                </div>

                                                <select name="status"
                                                    class="sm:w-44 shrink-0 font-medium text-[#525252] dark:text-white/70 w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition">
                                                    <option value="pending" {{ $rev->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="fixing" {{ $rev->status == 'fixing' ? 'selected' : '' }}>Fixing</option>
                                                    <option value="resolved" {{ $rev->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                                    <option value="rejected" {{ $rev->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                </select>

                                                <button type="submit"
                                                    class="w-full sm:w-auto shrink-0 ui-btn-primary flex items-center justify-center gap-2">
                                                    Simpan
                                                    <i class="fa-solid fa-paper-plane text-xs"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-[#F0F6FC] dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] text-[#94A3B4] dark:text-white/35 flex items-center justify-center mx-auto mb-3">
                                    <i class="fa-solid fa-clipboard-check text-2xl"></i>
                                </div>
                                <h4 class="text-sm font-bold text-[#0F1A2A] dark:text-white mb-1">Belum ada revisi</h4>
                                <p class="text-xs font-medium text-[#737373] dark:text-white/40">Klien belum mengajukan permintaan revisi untuk proyek ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Live Chat Component --}}
                @if ($order->status != 'pending')
                    <x-joki-chat
                        :order="$order"
                        :user="Auth::user()"
                        :fetch-route="route('admin_joki.chat.fetch', $order->hashid)"
                        :store-route="route('admin_joki.chat.store', $order->hashid)"
                    />
                @endif
            </div>

            <div class="space-y-6">
                <div class="ui-panel">
                    <div class="ui-panel-head">
                        <h3 class="ui-modal-title">Kebutuhan Proyek</h3>
                        <span class="ui-status">Brief</span>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-0">
                            <div class="ui-spec-row">
                                <dt class="ui-stat-label">Nama</dt>
                                <dd class="font-bold text-[#0F1A2A] dark:text-white">{{ $order->project_name }}</dd>
                            </div>
                            <div class="ui-spec-row">
                                <dt class="ui-stat-label">Tech Stack</dt>
                                <dd class="font-semibold text-[#0F1A2A] dark:text-white font-mono text-[13px]">{{ $order->tech_stack ?? '-' }}</dd>
                            </div>
                        </dl>
                        <p class="ui-eyebrow-muted mt-4 mb-1.5">Deskripsi</p>
                        <div class="bg-[#F4F8FC] dark:bg-[#0D1828] p-3 border border-[#E3EAF1] dark:border-[#1B2C44] text-sm text-[#525252] dark:text-white/70 whitespace-pre-line">
                            {{ $order->description }}</div>
                    </div>
                </div>

                @if(isset($consultation) && !empty($consultation->chat_history))
                <div class="ui-panel">
                    <div class="ui-panel-head">
                        <h3 class="ui-modal-title flex items-center gap-2">
                            <i class="fa-solid fa-robot text-[#1D5B96] dark:text-[#6BA8DC] text-sm"></i> Riwayat Konsultasi AI
                        </h3>
                    </div>
                    <div class="p-6 space-y-3 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($consultation->chat_history as $msg)
                            <div class="{{ $msg['role'] == 'user' ? 'bg-[#F0F6FC] dark:bg-[#12233A] border-[#C9D5E2] dark:border-[#1B2C44] ml-4' : 'bg-[#F4F8FC] dark:bg-[#0D1828] border-[#E3EAF1] dark:border-[#1B2C44] mr-4' }} p-3 border text-sm">
                                <div class="font-bold mb-1 text-[11px] font-mono uppercase tracking-[0.15em] {{ $msg['role'] == 'user' ? 'text-[#1D5B96] dark:text-[#6BA8DC]' : 'text-[#737373] dark:text-white/40' }}">
                                    {{ $msg['role'] == 'user' ? $order->client->name : 'AI Konsultan' }}
                                </div>
                                <div class="text-[#525252] dark:text-white/70 leading-relaxed whitespace-pre-wrap">{!! \Illuminate\Support\Str::markdown($msg['content']) !!}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif


                <div class="ui-panel">
                    <div class="ui-panel-head">
                        <h3 class="ui-modal-title">Tagihan & Pembayaran</h3>
                        <span class="ui-status">Keuangan</span>
                    </div>
                    <div class="p-6">

                        <form action="{{ route('admin_joki.payment.store', $order->hashid) }}" method="POST"
                            class="mb-5 bg-[#F4F8FC] dark:bg-[#0D1828] p-3 border border-[#E3EAF1] dark:border-[#1B2C44]">
                            @csrf
                            <div class="space-y-2">
                                <input type="text" name="payment_name" required placeholder="Nama (Cth: DP 50%)"
                                    class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition">
                                <input type="number" name="amount" required placeholder="Nominal (Cth: 1500000)"
                                    class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm font-mono focus:border-[#1D5B96] outline-none transition">
                                <button type="submit"
                                    class="w-full bg-[#12233A] dark:bg-[#1D5B96] text-white font-bold py-2 hover:bg-[#1D5B96] dark:hover:bg-[#2A6FAD] font-mono text-xs uppercase tracking-[0.15em] transition-colors">Buat
                                    Tagihan</button>
                            </div>
                        </form>

                        <div class="space-y-4">
                            @forelse($order->payments as $payment)
                                <div
                                    class="border border-[#E3EAF1] dark:border-[#1B2C44] p-4 {{ $payment->status == 'pending_verification' ? 'border-amber-300 dark:border-amber-500/50 bg-amber-50/30 dark:bg-amber-500/10' : 'bg-white dark:bg-[#0A1322]' }}">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="font-bold text-sm text-[#0F1A2A] dark:text-white">{{ $payment->payment_name }}</p>
                                            <p class="text-xs font-bold font-mono text-emerald-600 dark:text-emerald-400">Rp
                                                {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                        </div>
                                        <span class="ui-badge {{ $payment->status == 'paid' ? 'ui-badge-success' : ($payment->status == 'unpaid' ? 'ui-badge-danger' : 'ui-badge-warning') }}">
                                            {{ $payment->status }}
                                        </span>
                                    </div>

                                    @if ($payment->proof_image)
                                        <div class="mt-3 pt-3 border-t border-[#E3EAF1] dark:border-[#1B2C44]">
                                            <p class="ui-eyebrow-muted mb-2">Bukti Transfer:</p>
                                            <a href="{{ asset('storage/' . $payment->proof_image) }}" target="_blank"
                                                class="block">
                                                <img src="{{ asset('storage/' . $payment->proof_image) }}" alt="Bukti TF"
                                                    class="w-full h-24 object-cover border border-[#E3EAF1] dark:border-[#1B2C44] mb-3 hover:opacity-80 transition-opacity">
                                            </a>

                                            @if ($payment->status == 'pending_verification')
                                                <form action="{{ route('admin_joki.payment.verify', $payment->hashid) }}"
                                                    method="POST" class="flex gap-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <select name="status"
                                                        class="flex-1 w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-2 py-2 text-xs focus:border-[#1D5B96] outline-none transition">
                                                        <option value="paid">Terima (Lunas)</option>
                                                        <option value="failed">Tolak (Gagal)</option>
                                                        <option value="unpaid">Reset ke Unpaid</option>
                                                    </select>
                                                    <button type="submit"
                                                        class="bg-[#1D5B96] text-white px-3 py-2 text-[10px] font-mono font-bold uppercase tracking-[0.15em] hover:bg-[#2A6FAD] transition-colors">Proses</button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-xs text-[#94A3B4] dark:text-white/35 font-mono uppercase tracking-[0.2em] text-center py-2">Belum ada tagihan dibuat.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </x-ui.page-layout>
@endsection
