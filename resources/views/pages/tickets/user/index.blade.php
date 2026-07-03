@extends('index')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header 
        title="Tiket Bantuan" 
        subtitle="Daftar tiket dukungan Anda. Hubungi kami jika Anda memiliki kendala.">
        <x-slot:actions>
            <a href="{{ route('user_hosting.tickets.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm flex items-center gap-2 shadow-md">
                <i class="fa-solid fa-plus"></i> Buat Tiket Baru
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-medium">
                    <tr>
                        <th class="px-6 py-4">Subjek</th>
                        <th class="px-6 py-4">Departemen</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Update Terakhir</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ $ticket->subject }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">#{{ $ticket->hashid }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                    {{ $ticket->department }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($ticket->status == 'open')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700 border border-amber-200">
                                        Open
                                    </span>
                                @elseif($ticket->status == 'answered')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        Answered
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                        Closed
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-xs">
                                {{ $ticket->updated_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('user_hosting.tickets.show', $ticket->hashid) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm inline-flex items-center gap-1">
                                    Lihat <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-inbox text-3xl mb-3 text-slate-300"></i>
                                <p>Belum ada tiket bantuan yang Anda buat.</p>
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
