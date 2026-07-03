@extends('index')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header 
        title="Manajemen Tiket Bantuan" 
        subtitle="Kelola dan balas keluhan dari klien Hosting, Joki, dll.">
    </x-ui.page-header>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-medium">
                    <tr>
                        <th class="px-6 py-4">Klien</th>
                        <th class="px-6 py-4">Subjek & Dept</th>
                        <th class="px-6 py-4">Prioritas</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Waktu Terakhir</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $ticket->user->name }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $ticket->user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ Str::limit($ticket->subject, 40) }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">#{{ $ticket->hashid }} &bull; {{ $ticket->department }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($ticket->priority == 'high')
                                    <span class="text-xs font-bold text-rose-600"><i class="fa-solid fa-fire mr-1"></i> Tinggi</span>
                                @elseif($ticket->priority == 'medium')
                                    <span class="text-xs font-semibold text-amber-600">Sedang</span>
                                @else
                                    <span class="text-xs font-medium text-slate-500">Rendah</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($ticket->status == 'open')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-700">
                                        Open
                                    </span>
                                @elseif($ticket->status == 'answered')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700">
                                        Answered
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600">
                                        Closed
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-xs">
                                {{ $ticket->updated_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin_hosting.tickets.show', $ticket->hashid) }}" class="bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-3 py-1.5 rounded text-xs font-bold transition">
                                    Balas
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-inbox text-3xl mb-3 text-slate-300"></i>
                                <p>Tidak ada tiket bantuan saat ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</x-ui.page-layout>
@endsection
