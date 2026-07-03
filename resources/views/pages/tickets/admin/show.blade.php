@extends('index')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header 
        title="{{ $ticket->subject }}" 
        subtitle="Tiket Klien: {{ $ticket->user->name }} ({{ $ticket->user->email }})">
        <x-slot:iconSlot>
            <div class="shrink-0 w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-headset text-indigo-600 text-xl"></i>
            </div>
        </x-slot:iconSlot>
        <x-slot:actions>
            @if($ticket->status != 'closed')
                <form action="{{ route('admin_hosting.tickets.close', $ticket->hashid) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-rose-100 text-rose-700 hover:bg-rose-200 px-4 py-2 rounded-lg font-bold transition text-sm flex items-center gap-2 shadow-sm border border-rose-200">
                        <i class="fa-solid fa-lock"></i> Tutup Tiket
                    </button>
                </form>
            @endif
            <a href="{{ route('admin_hosting.tickets.index') }}" class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg font-medium transition text-sm flex items-center gap-2 shadow-sm">
                Kembali
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mt-6">
        
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
                <h3 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Detail Klien</h3>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                        {{ substr($ticket->user->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-bold text-slate-800 text-sm">{{ $ticket->user->name }}</div>
                        <div class="text-xs text-slate-500">{{ $ticket->user->email }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
                <h3 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Informasi Tiket</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <div class="text-xs text-slate-500 mb-1">Status</div>
                        @if($ticket->status == 'open')
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-100 text-amber-700">Open</span>
                        @elseif($ticket->status == 'answered')
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700">Answered</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-600">Closed</span>
                        @endif
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 mb-1">Prioritas</div>
                        <div class="font-semibold text-slate-800 capitalize">{{ $ticket->priority }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 mb-1">Departemen</div>
                        <div class="font-semibold text-slate-800">{{ $ticket->department }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 mb-1">Dibuat</div>
                        <div class="font-semibold text-slate-800">{{ $ticket->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-3 flex flex-col h-[700px] bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            
            {{-- Chat Messages Area --}}
            <div id="chat-messages-area" class="flex-1 overflow-y-auto p-6 bg-slate-50 flex flex-col gap-6">
                @foreach($ticket->replies as $reply)
                    @php
                        $isAdmin = $reply->user->role == 'admin_hosting' || $reply->user->role == 'superadmin';
                    @endphp
                    <div class="flex {{ $isAdmin ? 'justify-end' : 'justify-start' }}">
                        <div class="flex max-w-[80%] {{ $isAdmin ? 'flex-row-reverse' : 'flex-row' }} items-end gap-3">
                            
                            {{-- Avatar --}}
                            <div class="shrink-0">
                                @if($isAdmin)
                                    <div class="w-10 h-10 rounded-full bg-slate-800 border-2 border-white shadow-sm flex items-center justify-center text-white font-bold">
                                        <i class="fa-solid fa-headset text-sm"></i>
                                    </div>
                                @else
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 border-2 border-white shadow-sm flex items-center justify-center text-indigo-700 font-bold">
                                        {{ substr($reply->user->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>

                            {{-- Bubble --}}
                            <div class="flex flex-col {{ $isAdmin ? 'items-end' : 'items-start' }}">
                                <div class="text-xs text-slate-500 mb-1 px-1">
                                    <span class="font-bold text-slate-700">{{ $isAdmin ? 'Anda (Support)' : $reply->user->name }}</span> &bull; 
                                    {{ $reply->created_at->format('d M Y, H:i') }}
                                </div>
                                <div class="{{ $isAdmin ? 'bg-slate-800 text-white rounded-l-2xl rounded-tr-2xl' : 'bg-white border border-slate-200 text-slate-700 rounded-r-2xl rounded-tl-2xl shadow-sm' }} px-5 py-3 text-sm leading-relaxed whitespace-pre-wrap">{{ $reply->message }}</div>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Reply Form --}}
            <div class="p-4 bg-white border-t border-slate-200">
                @if($ticket->status != 'closed')
                    <form id="chat-form" action="{{ route('admin_hosting.tickets.reply', $ticket->hashid) }}" method="POST" class="flex gap-3 items-end">
                        @csrf
                        <div class="flex-1">
                            <textarea name="message" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-slate-500/20 focus:border-slate-500 outline-none transition resize-none" placeholder="Balas keluhan klien..." required></textarea>
                        </div>
                        <button type="submit" class="shrink-0 bg-slate-800 hover:bg-slate-900 text-white w-12 h-12 rounded-xl flex items-center justify-center transition shadow-md">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </form>
                @else
                    <div class="text-center py-3 text-sm text-slate-500 font-medium">
                        <i class="fa-solid fa-lock mr-2 text-slate-400"></i> Tiket telah ditutup.
                    </div>
                @endif
            </div>

        </div>

    </div>
</x-ui.page-layout>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatArea = document.getElementById('chat-messages-area');
    const chatForm = document.getElementById('chat-form');
    
    // Auto scroll ke bawah saat pertama load
    chatArea.scrollTop = chatArea.scrollHeight;

    // Polling setiap 3 detik untuk mengambil pesan baru
    setInterval(() => {
        fetch(window.location.href)
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newChatArea = doc.getElementById('chat-messages-area');
                
                // Jika ada perubahan konten (pesan baru)
                if (chatArea.innerHTML !== newChatArea.innerHTML) {
                    const isScrolledToBottom = chatArea.scrollHeight - chatArea.clientHeight <= chatArea.scrollTop + 10;
                    
                    chatArea.innerHTML = newChatArea.innerHTML;
                    
                    // Scroll ke bawah otomatis jika posisi scroll user sudah di bawah
                    if (isScrolledToBottom) {
                        chatArea.scrollTop = chatArea.scrollHeight;
                    }
                }
            })
            .catch(err => console.error('Error polling chat:', err));
    }, 3000);

    // Kirim pesan tanpa reload (AJAX)
    if (chatForm) {
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(chatForm);
            const submitBtn = chatForm.querySelector('button[type="submit"]');
            const textarea = chatForm.querySelector('textarea');
            const originalBtnContent = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>';
            submitBtn.disabled = true;

            fetch(chatForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.text()) // Controller saat ini mereturn back() yang berupa HTML redirect/page
            .then(() => {
                textarea.value = '';
                // Paksa trigger fetch untuk update langsung tanpa tunggu 3 detik
                return fetch(window.location.href);
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                chatArea.innerHTML = doc.getElementById('chat-messages-area').innerHTML;
                chatArea.scrollTop = chatArea.scrollHeight;
            })
            .finally(() => {
                submitBtn.innerHTML = originalBtnContent;
                submitBtn.disabled = false;
            });
        });
    }
});
</script>
@endsection
