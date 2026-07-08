@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- Header --}}
        <x-ui.page-header 
            title="Ryaze Wallet" 
            subtitle="Kelola saldo dan riwayat transaksi Anda." 
            icon="fa-wallet" 
            iconColor="indigo">
            <x-slot:actions>
                <button type="button" onclick="document.getElementById('topup-modal').classList.remove('hidden')"
                    class="inline-flex justify-center items-center bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition shadow-sm">
                    <i class="fa-solid fa-plus mr-2"></i> Top Up
                </button>
            </x-slot:actions>
        </x-ui.page-header>

        {{-- Saldo Card --}}
        <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden mb-6">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <i class="fa-solid fa-wallet text-8xl"></i>
            </div>
            <div class="relative z-10">
                <div class="text-slate-300 text-sm font-semibold mb-1">Saldo Aktif</div>
                <div class="text-4xl font-black mb-1">
                    Rp {{ number_format($wallet->balance, 0, ',', '.') }}
                </div>
            </div>
        </div>

        {{-- History Table --}}
        <x-ui.table>
            <x-slot:head>
                <th class="px-6 py-4">Tanggal / Waktu</th>
                <th class="px-6 py-4">Keterangan</th>
                <th class="px-6 py-4">Tipe</th>
                <th class="px-6 py-4">Jumlah</th>
                <th class="px-6 py-4 text-center">Status</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </x-slot:head>
            @forelse ($transactions as $tx)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800">{{ $tx->created_at->format('d M Y') }}</div>
                        <div class="text-xs text-slate-500 font-mono">{{ $tx->created_at->format('H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-slate-700">
                        {{ $tx->description }}
                        @if($tx->reference_id)
                            <div class="text-xs text-slate-400 font-mono mt-1">{{ $tx->reference_id }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($tx->type === 'credit')
                            <span class="text-xs font-bold px-2 py-1 rounded bg-emerald-100 text-emerald-700"><i class="fa-solid fa-arrow-down mr-1"></i> MASUK</span>
                        @else
                            <span class="text-xs font-bold px-2 py-1 rounded bg-rose-100 text-rose-700"><i class="fa-solid fa-arrow-up mr-1"></i> KELUAR</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-mono font-medium @if($tx->type === 'credit') text-emerald-600 @else text-rose-600 @endif">
                        {{ $tx->type === 'credit' ? '+' : '-' }}Rp{{ number_format($tx->amount, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $statusClass = match ($tx->status) {
                                'completed' => 'bg-emerald-100 text-emerald-700',
                                'pending' => 'bg-amber-100 text-amber-700',
                                'failed' => 'bg-rose-100 text-rose-700',
                                default => 'bg-slate-100 text-slate-700',
                            };
                            $statusLabel = strtoupper($tx->status);
                        @endphp
                        <span class="text-xs font-bold px-2 py-1 rounded-full {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if ($tx->status === 'pending' && $tx->reference_id && str_starts_with($tx->reference_id, 'WLT-TOPUP-'))
                            <a href="https://app.pakasir.com/pay/{{ config('services.pakasir.slug', 'ryaze') }}/{{ $tx->amount }}?order_id={{ $tx->reference_id }}" 
                               target="_blank"
                               class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-xs font-bold transition shadow-sm">
                                <i class="fa-solid fa-credit-card"></i> Bayar
                            </a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada riwayat transaksi dompet.</td>
                </tr>
            @endforelse
            <x-slot:pagination>
                {{ $transactions->links() }}
            </x-slot:pagination>
        </x-ui.table>

        {{-- Modal Top Up --}}
        <div id="topup-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-slate-900 opacity-75 backdrop-blur-sm"></div>
                </div>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full relative z-10">
                    <form action="{{ route('user.wallet.topup') }}" method="POST">
                        @csrf
                        <div class="px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-5">
                                <h3 class="text-xl leading-6 font-bold text-slate-800" id="modal-title">
                                    Top Up Ryaze Wallet
                                </h3>
                                <button type="button" onclick="document.getElementById('topup-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-500">
                                    <i class="fa-solid fa-xmark text-lg"></i>
                                </button>
                            </div>
                            <div class="mt-2">
                                <p class="text-sm text-slate-500 mb-4">Masukkan nominal top up yang Anda inginkan (Minimal Rp 10.000).</p>
                                
                                <div class="grid grid-cols-3 gap-2 mb-4">
                                    @foreach([20000, 50000, 100000, 200000, 500000, 1000000] as $preset)
                                        <button type="button" onclick="document.getElementById('amount_input').value = {{ $preset }}" class="py-2 border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:border-indigo-200 transition">
                                            {{ number_format($preset/1000, 0, ',', '.') }}K
                                        </button>
                                    @endforeach
                                </div>
                                
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-slate-500 font-bold">Rp</span>
                                    </div>
                                    <input type="number" name="amount" id="amount_input" min="10000" step="1000" class="block w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-bold text-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none" placeholder="10000" required>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-slate-50 flex justify-end gap-2 rounded-b-2xl">
                            <button type="button" onclick="document.getElementById('topup-modal').classList.add('hidden')" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-50 transition shadow-sm">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2.5 bg-indigo-600 border border-transparent text-white rounded-lg font-bold hover:bg-indigo-700 transition shadow-sm">
                                Lanjutkan Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </x-ui.page-layout>
@endsection
