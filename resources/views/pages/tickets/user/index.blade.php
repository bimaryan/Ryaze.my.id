@extends('index')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header 
        title="Tiket Bantuan" 
        eyebrow="Dukungan Teknis"
        status="SIAP"
        subtitle="Daftar tiket dukungan Anda. Hubungi kami jika Anda memiliki kendala.">
        <x-slot:actions>
            <a href="{{ route('user_hosting.tickets.create') }}" class="ui-btn ui-btn-md ui-btn-primary font-mono text-[11px] uppercase tracking-[0.2em]">
                <i class="fa-solid fa-plus text-xs"></i> Buat Tiket Baru
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="ui-panel overflow-hidden mt-6">
        {{-- Flat mono tab strip (Home.jsx adjacent-cell pattern) --}}
        <div class="flex items-center justify-between gap-4 px-6 bg-[#F4F8FC] dark:bg-[#12233A] border-b border-[#E3EAF1] dark:border-[#2A4463]">
            <div class="flex items-center gap-6 -mb-px">
                <span class="py-3.5 font-mono text-[11px] font-semibold uppercase tracking-[0.2em] border-b-2 border-[#1D5B96] dark:border-[#6BA8DC] text-[#0F1A2A] dark:text-white">
                    Semua Tiket
                </span>
            </div>
            <span class="flex items-center gap-1.5 font-mono text-[10px] uppercase tracking-widest text-[#525252] dark:text-white/40">
                {{ $tickets->total() }} Tiket
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="border-b border-[#E3EAF1] dark:border-[#2A4463] text-[#525252] dark:text-white/40">
                    <tr>
                        <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-wider font-semibold">Subjek</th>
                        <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-wider font-semibold">Departemen</th>
                        <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-wider font-semibold">Status</th>
                        <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-wider font-semibold">Update Terakhir</th>
                        <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-wider font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EDF2F7] dark:divide-[#24384F]">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-[#F4F8FC] dark:hover:bg-[#12233A] transition">
                            <td class="px-6 py-4">
                                <div class="font-medium text-[#0F1A2A] dark:text-white">{{ $ticket->subject }}</div>
                                <div class="text-xs font-mono text-[#8A9BAE] dark:text-white/40 mt-0.5">#{{ $ticket->hashid }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 font-mono text-[10px] uppercase tracking-wider bg-[#F0F6FC] dark:bg-[#12233A] text-[#525252] dark:text-white/60 border border-[#E3EAF1] dark:border-[#2A4463]">
                                    {{ $ticket->department }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($ticket->status == 'open')
                                    <span class="px-2.5 py-1 text-[10px] font-mono uppercase tracking-wider font-semibold border bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/50">
                                        Open
                                    </span>
                                @elseif($ticket->status == 'answered')
                                    <span class="px-2.5 py-1 text-[10px] font-mono uppercase tracking-wider font-semibold border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/50">
                                        Answered
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-[10px] font-mono uppercase tracking-wider font-semibold border bg-[#F0F6FC] dark:bg-[#12233A] text-[#525252] dark:text-white/60 border-[#E3EAF1] dark:border-[#2A4463]">
                                        Closed
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-[#525252] dark:text-white/50 text-xs font-mono">
                                {{ $ticket->updated_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('user_hosting.tickets.show', $ticket->hashid) }}" class="ui-arrow-link justify-end">
                                    Lihat <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-[#525252] dark:text-white/45">
                                <i class="fa-solid fa-inbox text-3xl mb-3 text-[#C9D5E2] dark:text-white/30"></i>
                                <p>Belum ada tiket bantuan yang Anda buat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
            <div class="px-6 py-4 border-t border-[#E3EAF1] dark:border-[#2A4463]">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</x-ui.page-layout>
@endsection
