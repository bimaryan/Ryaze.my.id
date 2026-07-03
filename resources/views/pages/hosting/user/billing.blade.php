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

        <x-ui.table>
            <x-slot:head>
                <th class="px-6 py-4">Invoice / Tanggal</th>
                <th class="px-6 py-4">Keterangan</th>
                <th class="px-6 py-4">Jumlah</th>
                <th class="px-6 py-4">Metode</th>
                <th class="px-6 py-4 text-center">Status</th>
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
