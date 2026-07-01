@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- Header --}}
        <div
            class="p-5 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="shrink-0 w-11 h-11 flex items-center justify-center bg-emerald-50 text-emerald-600 rounded-lg">
                    <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Riwayat Tagihan</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Daftar lengkap transaksi dan status pembayaran hosting Anda.</p>
                </div>
            </div>
            <a href="{{ route('user_hosting.dashboard') }}"
                class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                &larr; Kembali
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-sm text-left text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
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
                    </tbody>
            </table>
            <div class="px-6 py-4 border-t border-slate-100">{{ $billings->links() }}</div>
        </div>
    </x-ui.page-layout>
@endsection
