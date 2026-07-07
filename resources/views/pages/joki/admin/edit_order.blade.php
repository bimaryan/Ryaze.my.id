@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Control Center: {{ $order->order_number }}" 
            icon="fa-solid fa-pen-to-square">
            <x-slot:subtitle>
                Klien: <span class="font-semibold text-indigo-600">{{ $order->client->name }}</span> | Layanan: {{ $order->service->name }}
            </x-slot:subtitle>
            <x-slot:actions>
                @if($order->status == 'completed')
                    <form action="{{ route('admin_joki.orders.portfolio', $order->hashid) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex justify-center items-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm mr-2">
                            <i class="fa-solid fa-star mr-2"></i> Jadikan Portofolio
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin_joki.orders') }}"
                    class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700">
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

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            <div class="xl:col-span-2 space-y-6">

                <x-ui.card class="p-6">
                    <h3 class="font-bold text-slate-800 mb-4 border-b pb-2">Update Status & Hasil Kerja</h3>
                    <form action="{{ route('admin_joki.orders.update', $order->hashid) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Status Proyek</label>
                                <select name="status"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
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
                                <label class="block text-xs font-bold text-slate-700 mb-1">Progres (%)</label>
                                <input type="number" name="progress" min="0" max="100"
                                    value="{{ $order->progress }}"
                                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Harga Deal (Rp)</label>
                                <input type="number" name="price" value="{{ $order->price }}"
                                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Link Repo / GitHub</label>
                                <input type="url" name="repo_link" value="{{ $order->repo_link }}"
                                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Link Demo / Hosting</label>
                                <input type="url" name="demo_link" value="{{ $order->demo_link }}"
                                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                            </div>
                        </div>
                        <div class="text-right pt-2">
                            <button type="submit"
                                class="bg-indigo-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-indigo-700 transition-colors text-sm">Simpan
                                Perubahan Utama</button>
                        </div>
                    </form>
                </x-ui.card>

                <x-ui.card class="p-6">
                    <h3 class="font-bold text-slate-800 mb-4 border-b pb-2">Target Pengerjaan (Milestones)</h3>

                    <form action="{{ route('admin_joki.milestone.store', $order->hashid) }}" method="POST"
                        class="mb-6 bg-slate-50 p-4 rounded-lg border border-slate-100">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                            <div class="md:col-span-2">
                                <input type="text" name="title" required placeholder="Judul Tugas (Cth: Slicing UI)"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            </div>
                            <div>
                                <input type="date" name="due_date"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="md:col-span-2">
                                <input type="text" name="description" placeholder="Deskripsi singkat..."
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            </div>
                            <div class="flex gap-2">
                                <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                                    <option value="pending">Pending</option>
                                    <option value="working">Working</option>
                                    <option value="done">Done</option>
                                </select>
                                <button type="submit"
                                    class="bg-emerald-500 text-white px-4 rounded-lg hover:bg-emerald-600 font-bold"><i
                                        class="fa-solid fa-plus"></i></button>
                            </div>
                        </div>
                    </form>

                    <div class="space-y-3">
                        @forelse($order->milestones as $m)
                            <div
                                class="flex justify-between items-center p-3 border border-slate-100 rounded-lg {{ $m->status == 'done' ? 'bg-emerald-50' : 'bg-white' }}">
                                <div>
                                    <h4 class="font-bold text-sm text-slate-800">{{ $m->title }}</h4>
                                    <p class="text-xs text-slate-500">{{ $m->description }}</p>
                                </div>
                                <div class="text-right">
                                    <span
                                        class="text-xs font-bold px-2 py-1 rounded {{ $m->status == 'done' ? 'bg-emerald-200 text-emerald-800' : ($m->status == 'working' ? 'bg-blue-200 text-blue-800' : 'bg-slate-200 text-slate-800') }}">
                                        {{ strtoupper($m->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 text-center py-2">Belum ada milestone.</p>
                        @endforelse
                    </div>
                </x-ui.card>

                <x-ui.card class="p-6">
                    <div class="flex justify-between items-center mb-5 border-b pb-3">
                        <h3 class="font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-comments text-indigo-500"></i>
                            Permintaan Revisi Klien
                        </h3>
                        <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2.5 py-1 rounded-full">
                            {{ $order->revisions->count() }} Permintaan
                        </span>
                    </div>

                    <div class="space-y-6">
                        @forelse($order->revisions as $rev)
                            <div class="relative pl-4 border-l-2 {{ $rev->status == 'resolved' ? 'border-emerald-500' : ($rev->status == 'fixing' ? 'border-blue-500' : ($rev->status == 'rejected' ? 'border-rose-500' : 'border-amber-400')) }}">
                                <!-- Status Dot -->
                                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full border-4 border-white shadow-sm {{ $rev->status == 'resolved' ? 'bg-emerald-500' : ($rev->status == 'fixing' ? 'bg-blue-500' : ($rev->status == 'rejected' ? 'bg-rose-500' : 'bg-amber-400')) }}"></div>
                                
                                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                                    <!-- Header (Client Note) -->
                                    <div class="p-4 {{ $rev->status == 'pending' ? 'bg-amber-50/30' : 'bg-slate-50/50' }} border-b border-slate-100">
                                        <div class="flex justify-between items-start gap-4">
                                            <div class="flex items-start gap-3">
                                                <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <i class="fa-solid fa-user text-xs"></i>
                                                </div>
                                                <div>
                                                    <p class="text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">
                                                        {{ $order->client->name }} &bull; {{ $rev->created_at->format('d M Y, H:i') }}
                                                    </p>
                                                    <div class="text-sm text-slate-800 leading-relaxed font-medium">
                                                        "{{ $rev->revision_note }}"
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider flex-shrink-0
                                                {{ $rev->status == 'resolved' ? 'bg-emerald-100 text-emerald-700' : 
                                                   ($rev->status == 'fixing' ? 'bg-blue-100 text-blue-700' : 
                                                   ($rev->status == 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700')) }}">
                                                {{ $rev->status }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Body (Admin Reply & Form) -->
                                    <div class="p-4 bg-white">
                                        @if($rev->admin_reply)
                                        <div class="mb-4 bg-indigo-50/50 rounded-lg p-3 border border-indigo-100/50">
                                            <div class="flex items-start gap-3">
                                                <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <i class="fa-solid fa-headset text-[10px]"></i>
                                                </div>
                                                <div>
                                                    <p class="text-[11px] font-bold text-indigo-500 mb-0.5 uppercase tracking-wider">Admin Reply</p>
                                                    <p class="text-sm text-slate-700">{{ $rev->admin_reply }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <form action="{{ route('admin_joki.revision.reply', $rev->hashid) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <label class="block text-xs font-bold text-slate-600 mb-2">Update Status & Tanggapan</label>
                                            <div class="flex flex-col sm:flex-row gap-3">
                                                <div class="relative flex-1">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                                        <i class="fa-solid fa-pen text-xs"></i>
                                                    </div>
                                                    <input type="text" name="admin_reply" value="{{ $rev->admin_reply }}"
                                                        placeholder="Ketik tanggapan Anda di sini..." required
                                                        class="w-full pl-9 pr-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                                                </div>

                                                <select name="status"
                                                    class="sm:w-40 flex-shrink-0 transition-all font-medium text-slate-700 w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                                                    <option value="pending" {{ $rev->status == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                                    <option value="fixing" {{ $rev->status == 'fixing' ? 'selected' : '' }}>🛠️ Fixing</option>
                                                    <option value="resolved" {{ $rev->status == 'resolved' ? 'selected' : '' }}>✅ Resolved</option>
                                                    <option value="rejected" {{ $rev->status == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
                                                </select>

                                                <button type="submit"
                                                    class="w-full sm:w-auto flex-shrink-0 bg-indigo-600 text-white px-5 py-2.5 rounded-lg hover:bg-indigo-700 hover:shadow-md text-sm font-bold transition-all flex items-center justify-center gap-2">
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
                                <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fa-solid fa-clipboard-check text-2xl"></i>
                                </div>
                                <h4 class="text-sm font-bold text-slate-700 mb-1">Belum ada revisi</h4>
                                <p class="text-xs font-medium text-slate-500">Klien belum mengajukan permintaan revisi untuk proyek ini.</p>
                            </div>
                        @endforelse
                    </div>
                </x-ui.card>
            </div>

            <div class="space-y-6">
                <x-ui.card class="p-6">
                    <h3 class="font-bold text-slate-800 mb-3 border-b pb-2">Kebutuhan Proyek</h3>
                    <div class="text-sm text-slate-600 mb-3">
                        <span class="block font-bold text-slate-800">Nama:</span> {{ $order->project_name }}
                    </div>
                    <div class="text-sm text-slate-600 mb-3">
                        <span class="block font-bold text-slate-800">Tech Stack:</span> {{ $order->tech_stack ?? '-' }}
                    </div>
                    <div class="text-sm text-slate-600">
                        <span class="block font-bold text-slate-800 mb-1">Deskripsi:</span>
                        <div class="bg-slate-50 p-3 rounded border border-slate-100 whitespace-pre-line">
                            {{ $order->description }}</div>
                    </div>
                </x-ui.card>

                <x-ui.card class="p-6">
                    <h3 class="font-bold text-slate-800 mb-4 border-b pb-2">Tagihan & Pembayaran</h3>

                    <form action="{{ route('admin_joki.payment.store', $order->hashid) }}" method="POST"
                        class="mb-5 bg-slate-50 p-3 rounded-lg border border-slate-100">
                        @csrf
                        <div class="space-y-2">
                            <input type="text" name="payment_name" required placeholder="Nama (Cth: DP 50%)"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            <input type="number" name="amount" required placeholder="Nominal (Cth: 1500000)"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            <button type="submit"
                                class="w-full bg-slate-800 text-white font-bold py-2 rounded-lg hover:bg-slate-900 text-sm">Buat
                                Tagihan</button>
                        </div>
                    </form>

                    <div class="space-y-4">
                        @forelse($order->payments as $payment)
                            <div
                                class="border border-slate-200 rounded-lg p-4 {{ $payment->status == 'pending_verification' ? 'border-amber-400 bg-amber-50/30' : '' }}">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="font-bold text-sm text-slate-800">{{ $payment->payment_name }}</p>
                                        <p class="text-xs font-medium text-emerald-600">Rp
                                            {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                    </div>
                                    <span
                                        class="text-[10px] font-bold px-2 py-1 rounded uppercase bg-slate-100 text-slate-600">
                                        {{ $payment->status }}
                                    </span>
                                </div>

                                @if ($payment->proof_image)
                                    <div class="mt-3 pt-3 border-t border-slate-100">
                                        <p class="text-xs font-bold text-slate-700 mb-2">Bukti Transfer:</p>
                                        <a href="{{ asset('storage/' . $payment->proof_image) }}" target="_blank"
                                            class="block">
                                            <img src="{{ asset('storage/' . $payment->proof_image) }}" alt="Bukti TF"
                                                class="w-full h-24 object-cover rounded border border-slate-200 mb-3 hover:opacity-80 transition-opacity">
                                        </a>

                                        @if ($payment->status == 'pending_verification')
                                            <form action="{{ route('admin_joki.payment.verify', $payment->hashid) }}"
                                                method="POST" class="flex gap-2">
                                                @csrf
                                                @method('PUT')
                                                <select name="status"
                                                    class="flex-1 px-2 py-1 w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                                                    <option value="paid">Terima (Lunas)</option>
                                                    <option value="failed">Tolak (Gagal)</option>
                                                    <option value="unpaid">Reset ke Unpaid</option>
                                                </select>
                                                <button type="submit"
                                                    class="bg-indigo-600 text-white px-3 py-1 rounded text-xs font-bold">Proses</button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 text-center py-2">Belum ada tagihan dibuat.</p>
                        @endforelse
                    </div>
                </x-ui.card>

            </div>
        </div>
    </x-ui.page-layout>
@endsection
