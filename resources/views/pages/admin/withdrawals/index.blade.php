@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Kelola Penarikan Dana (Withdrawals)" 
            subtitle="Manajemen permohonan penarikan dana dari user." 
            icon="fa-money-bill-transfer" 
            iconColor="indigo">
        </x-ui.page-header>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider font-bold">
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">Nominal</th>
                            <th class="px-6 py-4">Rekening Tujuan</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($withdrawals as $w)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-slate-800">{{ $w->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-slate-500 font-mono">{{ $w->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-slate-800">{{ $w->user->name ?? 'User Terhapus' }}</div>
                                    <div class="text-xs text-slate-500">{{ $w->user->email ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-black text-indigo-600 font-mono">Rp {{ number_format($w->amount, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-slate-800">{{ $w->bank_name }}</div>
                                    <div class="text-xs text-slate-600">{{ $w->account_number }} - {{ $w->account_name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClass = match ($w->status) {
                                            'pending' => 'bg-amber-100 text-amber-700',
                                            'approved', 'completed' => 'bg-emerald-100 text-emerald-700',
                                            'rejected' => 'bg-rose-100 text-rose-700',
                                            default => 'bg-slate-100 text-slate-700',
                                        };
                                    @endphp
                                    <span class="text-xs font-bold px-2 py-1 rounded-full {{ $statusClass }}">
                                        {{ strtoupper($w->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($w->status === 'pending')
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('superadmin.withdrawals.update', $w->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="bg-emerald-100 hover:bg-emerald-200 text-emerald-700 px-3 py-1.5 rounded-lg text-xs font-bold transition" onclick="return confirm('Tandai selesai? Pastikan dana sudah ditransfer.')">
                                                    <i class="fa-solid fa-check"></i> Selesai
                                                </button>
                                            </form>
                                            <form action="{{ route('superadmin.withdrawals.update', $w->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="bg-rose-100 hover:bg-rose-200 text-rose-700 px-3 py-1.5 rounded-lg text-xs font-bold transition" onclick="return confirm('Tolak penarikan dan kembalikan saldo ke user?')">
                                                    <i class="fa-solid fa-xmark"></i> Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">Tidak ada aksi</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-500">Belum ada permohonan penarikan dana.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($withdrawals->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $withdrawals->links() }}
                </div>
            @endif
        </div>
    </x-ui.page-layout>
@endsection
