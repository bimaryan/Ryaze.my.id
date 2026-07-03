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
        <div id="chat-messages-area" class="flex-1 overflow-y-auto p-6 bg-[#efeae2] flex flex-col gap-6">
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
                            <div class="{{ $isSelf ? 'bg-[#d9fdd3] text-slate-800 rounded-l-xl rounded-br-xl' : 'bg-white border border-slate-200 text-slate-800 rounded-r-xl rounded-bl-xl shadow-sm' }} px-4 py-2 text-[15px] leading-relaxed whitespace-pre-wrap">{{ $reply->message }}</div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- Reply Form --}}
        <div class="px-4 py-3 bg-[#f0f2f5] border-t border-slate-200">
            @if($ticket->status != 'closed')
                <form id="chat-form" action="{{ route('user_hosting.tickets.reply', $ticket->hashid) }}" method="POST" class="flex gap-3 items-end">
                    @csrf
                    
                    {{-- Tombol Kosmetik (Emoticon & Attachment) --}}
                    <button type="button" class="shrink-0 text-slate-500 hover:text-slate-700 w-10 h-10 flex items-center justify-center text-xl transition mb-1">
                        <i class="fa-regular fa-face-smile"></i>
                    </button>
                    <button type="button" class="shrink-0 text-slate-500 hover:text-slate-700 w-10 h-10 flex items-center justify-center text-xl transition mb-1">
                        <i class="fa-solid fa-paperclip"></i>
                    </button>

                    <div class="flex-1 bg-white rounded-3xl shadow-sm border border-slate-100 flex items-center px-4 py-2">
                        <textarea name="message" rows="1" class="w-full bg-transparent border-none px-1 py-1 text-[15px] focus:ring-0 focus:outline-none resize-none m-0" placeholder="Ketik pesan" style="min-height: 24px; max-height: 120px; overflow-y: auto;" oninput="this.style.height = '24px'; this.style.height = Math.min(this.scrollHeight, 120) + 'px'" required></textarea>
                    </div>

                    <button type="submit" class="shrink-0 bg-[#00a884] hover:bg-[#029676] text-white w-12 h-12 rounded-full flex items-center justify-center transition shadow-sm mb-0.5">
                        <i class="fa-solid fa-paper-plane mr-1"></i>
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

<script type="module">
document.addEventListener('DOMContentLoaded', function() {
    const chatArea = document.getElementById('chat-messages-area');
    const chatForm = document.getElementById('chat-form');
    
    // Auto scroll ke bawah saat pertama load
    chatArea.scrollTop = chatArea.scrollHeight;

    // Listen to WebSocket (Reverb)
    if (window.Echo) {
        window.Echo.private('ticket.{{ $ticket->hashid }}')
            .listen('TicketReplyCreated', (e) => {
                // Render message bubble
                const isSelf = e.user_id == {{ Auth::id() }};
                const bubbleHtml = `
                    <div class="flex gap-4 ${isSelf ? 'flex-row-reverse' : ''}">
                        <div class="shrink-0">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold shadow-sm">
                                ${e.user_name.substring(0, 2).toUpperCase()}
                            </div>
                        </div>
                        <div class="max-w-[80%] ${isSelf ? 'text-right' : ''}">
                            <div class="flex items-center gap-2 mb-1 ${isSelf ? 'justify-end' : ''}">
                                <span class="font-medium text-sm text-slate-900">${e.user_name}</span>
                                ${e.is_admin ? '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700">ADMIN</span>' : '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-600">KLIEN</span>'}
                                <span class="text-xs text-slate-400">${e.created_at}</span>
                            </div>
                            <div class="px-4 py-2 mt-1 rounded-xl ${isSelf ? 'bg-[#d9fdd3] text-slate-800 rounded-tr-none' : 'bg-white border border-slate-200 text-slate-800 rounded-tl-none'} shadow-sm">
                                <p class="text-[15px] whitespace-pre-wrap">${e.message}</p>
                            </div>
                        </div>
                    </div>
                `;
                
                const isScrolledToBottom = chatArea.scrollHeight - chatArea.clientHeight <= chatArea.scrollTop + 10;
                
                chatArea.insertAdjacentHTML('beforeend', bubbleHtml);
                
                if (isScrolledToBottom || isSelf) {
                    chatArea.scrollTop = chatArea.scrollHeight;
                }
            });
    }

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
            .then(res => res.json())
            .then(() => {
                textarea.value = '';
            })
            .catch(err => {
                console.error('Error sending message:', err);
                alert('Gagal mengirim pesan. Silakan coba lagi.');
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
