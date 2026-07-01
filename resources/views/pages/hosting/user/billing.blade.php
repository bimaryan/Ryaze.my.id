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
                <th class="px-6 py-4">Project</th>
                <th class="px-6 py-4">Plan</th>
                <th class="px-6 py-4">Jumlah</th>
                <th class="px-6 py-4">Jatuh Tempo</th>
                <th class="px-6 py-4 text-center">Status</th>
            </x-slot:head>
            @forelse ($billings as $bill)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-semibold text-slate-800">{{ $bill->project->project_name }}</td>
                    <td class="px-6 py-4">{{ $bill->plan_name }}</td>
                    <td class="px-6 py-4 font-mono">Rp{{ number_format($bill->amount, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($bill->next_due_date)->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span
                            class="text-xs font-bold px-2 py-1 rounded-full {{ $bill->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ strtoupper($bill->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-slate-400">Belum ada riwayat tagihan.</td>
                </tr>
            @endforelse
            <x-slot:pagination>
                {{ $billings->links() }}
            </x-slot:pagination>
        </x-ui.table>
    </x-ui.page-layout>
@endsection
