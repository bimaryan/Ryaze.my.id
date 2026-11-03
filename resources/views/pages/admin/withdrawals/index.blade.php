@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Kelola Penarikan Dana (Withdrawals)" 
            subtitle="Manajemen permohonan penarikan dana dari user." 
            icon="fa-money-bill-transfer" 
            iconColor="indigo">
        </x-ui.page-header>

        <div class="bg-white dark:bg-[#0F1D30]  border border-[#C9D5E2] dark:border-[#2A4463]  overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#F4F8FC] dark:bg-[#0D1828] border-b border-[#C9D5E2] dark:border-[#1B2C44] text-[#737373] dark:text-white/40 text-xs uppercase tracking-wider font-bold">
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">Nominal</th>
                            <th class="px-6 py-4">Rekening Tujuan</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E3EAF1] dark:divide-[#1B2C44]">
                        @forelse ($withdrawals as $w)
                            <tr class="hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828] dark:bg-[#0D1828] transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-[#0F1A2A] dark:text-white">{{ $w->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-[#737373] dark:text-white/40 font-mono">{{ $w->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-[#0F1A2A] dark:text-white">{{ $w->user->name ?? 'User Terhapus' }}</div>
                                    <div class="text-xs text-[#737373] dark:text-white/40">{{ $w->user->email ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-black text-[#1D5B96] dark:text-[#6BA8DC] font-mono">Rp {{ number_format($w->amount, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-[#0F1A2A] dark:text-white">{{ $w->bank_name }}</div>
                                    <div class="text-xs text-[#525252] dark:text-white/60">{{ $w->account_number }} - {{ $w->account_name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClass = match ($w->status) {
                                            'pending' => 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300',
                                            'approved', 'completed' => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300',
                                            'rejected' => 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300',
                                            default => 'bg-[#F0F6FC] dark:bg-[#0D1828] text-[#0F1A2A] dark:text-white',
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
                                                <button type="submit" class="bg-emerald-100 dark:bg-emerald-500/20 hover:bg-emerald-200 text-emerald-700 dark:text-emerald-300 px-3 py-1.5  text-xs font-bold transition" onclick="event.preventDefault(); let f = this.closest('form'); swConfirm('Selesaikan Penarikan?', 'Pastikan dana sudah ditransfer sebelum menandai selesai.').then(res => { if(res.isConfirmed) f.submit(); }); return false;">
                                                    <i class="fa-solid fa-check"></i> Selesai
                                                </button>
                                            </form>
                                            <form action="{{ route('superadmin.withdrawals.update', $w->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="bg-rose-100 dark:bg-rose-500/20 hover:bg-rose-200 text-rose-700 dark:text-rose-300 px-3 py-1.5  text-xs font-bold transition" onclick="event.preventDefault(); let f = this.closest('form'); swConfirm('Tolak Penarikan?', 'Saldo akan dikembalikan ke user.').then(res => { if(res.isConfirmed) f.submit(); }); return false;">
                                                    <i class="fa-solid fa-xmark"></i> Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-[#94A3B4] dark:text-white/35">Tidak ada aksi</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-[#737373] dark:text-white/40">Belum ada permohonan penarikan dana.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($withdrawals->hasPages())
                <div class="p-4 border-t border-[#E3EAF1] dark:border-[#1B2C44]">
                    {{ $withdrawals->links() }}
                </div>
            @endif
        </div>
    </x-ui.page-layout>
@endsection
