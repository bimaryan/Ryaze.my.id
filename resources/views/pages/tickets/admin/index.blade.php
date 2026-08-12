@extends('index')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header 
        title="Manajemen Tiket Bantuan" 
        subtitle="Kelola dan balas keluhan dari klien Hosting, Joki, dll.">
    </x-ui.page-header>

    <div class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 font-medium">
                    <tr>
                        <th class="px-6 py-4">Klien</th>
                        <th class="px-6 py-4">Subjek & Dept</th>
                        <th class="px-6 py-4">Prioritas</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Waktu Terakhir</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-slate-50 dark:bg-slate-800/50 dark:hover:bg-slate-700/40 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 dark:text-slate-100">{{ $ticket->user->name }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $ticket->user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800 dark:text-slate-100">{{ Str::limit($ticket->subject, 40) }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">#{{ $ticket->hashid }} &bull; {{ $ticket->department }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($ticket->priority == 'high')
                                    <span class="text-xs font-bold text-rose-600 dark:text-rose-300"><i class="fa-solid fa-fire mr-1"></i> Tinggi</span>
                                @elseif($ticket->priority == 'medium')
                                    <span class="text-xs font-semibold text-amber-600 dark:text-amber-300">Sedang</span>
                                @else
                                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Rendah</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($ticket->status == 'open')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300">
                                        Open
                                    </span>
                                @elseif($ticket->status == 'answered')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300">
                                        Answered
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300">
                                        Closed
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-xs">
                                {{ $ticket->updated_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin_hosting.tickets.show', $ticket->hashid) }}" class="bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 px-3 py-1.5 rounded text-xs font-bold transition">
                                    Balas
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-inbox text-3xl mb-3 text-slate-300 dark:text-slate-400"></i>
                                <p>Tidak ada tiket bantuan saat ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</x-ui.page-layout>
@endsection
