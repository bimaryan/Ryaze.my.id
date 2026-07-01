@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50 relative">
        {{-- Header --}}
        <div class="p-5 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="shrink-0 w-11 h-11 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg">
                    <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Riwayat Tagihan</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Daftar lengkap transaksi dan status pembayaran pesanan joki Anda.</p>
                </div>
            </div>
            <a href="{{ route('user_joki.dashboard') }}" class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                &larr; Kembali
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">Nomor Pesanan</th>
                            <th class="px-6 py-4">Pembayaran</th>
                            <th class="px-6 py-4">Jumlah</th>
                            <th class="px-6 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($payments as $payment)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-slate-800">
                                    {{ $payment->order->order_number ?? '-' }}
                                </td>
                                <td class="px-6 py-4">{{ $payment->payment_name }}</td>
                                <td class="px-6 py-4 font-mono">Rp{{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusClass = match ($payment->status) {
                                            'paid', 'verified' => 'bg-emerald-100 text-emerald-700',
                                            'pending', 'pending_verification' => 'bg-amber-100 text-amber-700',
                                            'rejected', 'failed' => 'bg-rose-100 text-rose-700',
                                            default => 'bg-slate-100 text-slate-700',
                                        };
                                        
                                        $statusLabel = match ($payment->status) {
                                            'paid', 'verified' => 'LUNAS',
                                            'pending' => 'PENDING',
                                            'pending_verification' => 'MENUNGGU VERIFIKASI',
                                            'rejected', 'failed' => 'GAGAL / DITOLAK',
                                            default => strtoupper($payment->status),
                                        };
                                    @endphp
                                    <span class="text-xs font-bold px-2 py-1 rounded-full {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-slate-400">Belum ada riwayat tagihan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
