@extends('index')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header 
        title="{{ $ticket->subject }}" 
        subtitle="Tiket #{{ $ticket->hashid }} &bull; Departemen {{ $ticket->department }}">
        <x-slot:iconSlot>
            <div class="shrink-0 w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-comments text-indigo-600 text-xl"></i>
            </div>
        </x-slot:iconSlot>
        <x-slot:actions>
            @if($ticket->status == 'open')
                <span class="px-3 py-1.5 rounded-lg text-sm font-bold bg-amber-100 text-amber-700 border border-amber-200">
                    <i class="fa-solid fa-clock mr-1"></i> Menunggu Balasan
                </span>
            @elseif($ticket->status == 'answered')
                <span class="px-3 py-1.5 rounded-lg text-sm font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                    <i class="fa-solid fa-check mr-1"></i> Dijawab
                </span>
            @else
                <span class="px-3 py-1.5 rounded-lg text-sm font-bold bg-slate-100 text-slate-600 border border-slate-200">
                    <i class="fa-solid fa-lock mr-1"></i> Tiket Ditutup
                </span>
            @endif
            <a href="{{ route('user_hosting.tickets.index') }}" class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg font-medium transition text-sm flex items-center gap-2 shadow-sm">
                Kembali
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="max-w-4xl mt-6 flex flex-col h-[700px] bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        
        {{-- Chat Messages Area --}}
        <div class="flex-1 overflow-y-auto p-6 bg-slate-50 flex flex-col gap-6">
            @foreach($ticket->replies as $reply)
                @php
                    $isSelf = $reply->user_id == Auth::id();
                @endphp
                <div class="flex {{ $isSelf ? 'justify-end' : 'justify-start' }}">
                    <div class="flex max-w-[80%] {{ $isSelf ? 'flex-row-reverse' : 'flex-row' }} items-end gap-3">
                        
                        {{-- Avatar --}}
                        <div class="shrink-0">
                            @if($isSelf)
                                <div class="w-10 h-10 rounded-full bg-indigo-100 border-2 border-white shadow-sm flex items-center justify-center text-indigo-700 font-bold">
                                    {{ substr($reply->user->name, 0, 1) }}
                                </div>
                            @else
                                <div class="w-10 h-10 rounded-full bg-slate-800 border-2 border-white shadow-sm flex items-center justify-center text-white font-bold">
                                    <i class="fa-solid fa-headset text-sm"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Bubble --}}
                        <div class="flex flex-col {{ $isSelf ? 'items-end' : 'items-start' }}">
                            <div class="text-xs text-slate-500 mb-1 px-1">
                                <span class="font-bold text-slate-700">{{ $isSelf ? 'Anda' : 'Admin Support' }}</span> &bull; 
                                {{ $reply->created_at->format('d M Y, H:i') }}
                            </div>
                            <div class="{{ $isSelf ? 'bg-indigo-600 text-white rounded-l-2xl rounded-tr-2xl' : 'bg-white border border-slate-200 text-slate-700 rounded-r-2xl rounded-tl-2xl shadow-sm' }} px-5 py-3 text-sm leading-relaxed whitespace-pre-wrap">{{ $reply->message }}</div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- Reply Form --}}
        <div class="p-4 bg-white border-t border-slate-200">
            @if($ticket->status != 'closed')
                <form action="{{ route('user_hosting.tickets.reply', $ticket->hashid) }}" method="POST" class="flex gap-3 items-end">
                    @csrf
                    <div class="flex-1">
                        <textarea name="message" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition resize-none" placeholder="Ketik balasan Anda di sini..." required></textarea>
                    </div>
                    <button type="submit" class="shrink-0 bg-indigo-600 hover:bg-indigo-700 text-white w-12 h-12 rounded-xl flex items-center justify-center transition shadow-md">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            @else
                <div class="text-center py-3 text-sm text-slate-500 font-medium">
                    <i class="fa-solid fa-lock mr-2 text-slate-400"></i> Tiket ini telah ditutup dan tidak dapat menerima balasan lagi.
                </div>
            @endif
        </div>

    </div>
</x-ui.page-layout>
@endsection
