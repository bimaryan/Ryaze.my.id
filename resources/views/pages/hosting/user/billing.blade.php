@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- Header --}}
        <x-ui.page-header 
            title="Riwayat Tagihan" 
            subtitle="Daftar lengkap transaksi dan status pembayaran hosting Anda." 
            icon="fa-file-invoice-dollar" 
            iconColor="emerald">
            <x-slot:actions>
                <a href="{{ route('user_hosting.dashboard') }}"
                    class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        @if (!Auth::user()->hasActiveHostingSubscription())
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 mb-6">
                <h3 class="font-bold text-slate-800 mb-2 flex items-center gap-2 text-lg">
                    <i class="fa-solid fa-crown text-indigo-600"></i> Langganan Hosting
                </h3>
                <p class="text-sm text-slate-500 mb-4">Anda belum memiliki langganan hosting aktif. Silakan berlangganan seharga <span class="font-bold text-slate-800">Rp 10.000 / bulan</span> untuk dapat mendeploy project baru.</p>
                
                <form action="{{ route('user_hosting.billing.subscribe') }}" method="POST" class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                    @csrf
                    <div class="w-full sm:max-w-xs">
                        <input type="text" name="voucher_code" placeholder="Kode Voucher (Opsional)" class="w-full uppercase px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition-all font-mono">
                    </div>
                    <button type="submit" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2.5 rounded-lg shadow-sm transition-all whitespace-nowrap">
                        <i class="fa-solid fa-file-invoice-dollar mr-2"></i> Buat Tagihan
                    </button>
                </form>
            </div>
        @endif

        <x-ui.table>
            <x-slot:head>
                <th class="px-6 py-4">Invoice / Tanggal</th>
                <th class="px-6 py-4">Keterangan</th>
                <th class="px-6 py-4">Jumlah</th>
                <th class="px-6 py-4">Metode</th>
                <th class="px-6 py-4 text-center">Status</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </x-slot:head>
            @forelse ($billings as $bill)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800">{{ $bill->invoice_number }}</div>
                        <div class="text-xs text-slate-500 font-mono">{{ $bill->created_at->format('d M Y, H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 font-semibold text-slate-800">{{ $bill->project->project_name ?? 'Langganan Akun' }}</td>
                    <td class="px-6 py-4 font-mono font-medium">Rp{{ number_format($bill->amount, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">{{ $bill->payment_method ?? '-' }}</td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $statusClass = match ($bill->status) {
                                'paid' => 'bg-emerald-100 text-emerald-700',
                                'unpaid' => 'bg-amber-100 text-amber-700',
                                'failed' => 'bg-rose-100 text-rose-700',
                                default => 'bg-slate-100 text-slate-700',
                            };
                            $statusLabel = strtoupper($bill->status);
                        @endphp
                        <span class="text-xs font-bold px-2 py-1 rounded-full {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if ($bill->status === 'unpaid')
                            <a href="https://app.pakasir.com/pay/{{ config('services.pakasir.slug', 'ryaze') }}/{{ $bill->amount }}?order_id={{ $bill->invoice_number }}" 
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
                    <td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada riwayat tagihan.</td>
                </tr>
            @endforelse
            <x-slot:pagination>
                {{ $billings->links() }}
            </x-slot:pagination>
        </x-ui.table>
    </x-ui.page-layout>
@endsection
