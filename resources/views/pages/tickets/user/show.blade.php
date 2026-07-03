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
                            <div class="{{ $isSelf ? 'bg-[#d9fdd3] text-slate-800 rounded-l-xl rounded-br-xl' : 'bg-white border border-slate-200 text-slate-800 rounded-r-xl rounded-bl-xl shadow-sm' }} px-4 py-2 text-[15px] leading-relaxed whitespace-pre-wrap">@if($reply->attachment_path)<div class="mb-2"><a href="{{ asset('storage/' . $reply->attachment_path) }}" target="_blank"><img src="{{ asset('storage/' . $reply->attachment_path) }}" class="rounded-lg max-w-full h-auto max-h-64 object-cover" alt="Attachment"></a></div>@endif{{ $reply->message }}</div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- Reply Form --}}
        <div class="px-4 py-3 bg-[#f0f2f5] border-t border-slate-200 flex flex-col relative">
            <div id="typing-indicator" class="hidden text-[13px] text-slate-500 italic mb-2 px-1 transition-all duration-300">
                <i class="fa-solid fa-pen-nib mr-1"></i> <span id="typing-name"></span> sedang mengetik...
            </div>

            <div id="attachment-preview-container" class="hidden mb-3 bg-white p-2 rounded-xl shadow-sm border border-slate-200 w-max relative">
                <button type="button" onclick="removeAttachment()" class="absolute -top-2 -right-2 bg-red-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs hover:bg-red-600 transition shadow"><i class="fa-solid fa-xmark"></i></button>
                <img id="attachment-preview-img" src="" class="h-20 object-cover rounded-lg">
            </div>

            @if($ticket->status != 'closed')
                <form id="chat-form" action="{{ route('user_hosting.tickets.reply', $ticket->hashid) }}" method="POST" enctype="multipart/form-data" class="flex gap-2 items-end">
                    @csrf
                    
                    <input type="file" name="attachment" id="attachment-input" accept="image/png, image/jpeg, image/jpg" class="hidden" onchange="previewAttachment(this)">

                    <div class="flex-1 bg-white rounded-3xl shadow-sm border border-slate-100 flex items-end px-2 py-1.5">
                        <button type="button" id="emoji-btn" class="shrink-0 text-slate-500 hover:text-slate-700 w-9 h-9 flex items-center justify-center text-xl transition mb-0.5">
                            <i class="fa-regular fa-face-smile"></i>
                        </button>

                        <textarea name="message" id="message-input" rows="1" class="w-full bg-transparent border-none px-2 py-1.5 text-[15px] focus:ring-0 focus:outline-none resize-none m-0" placeholder="Ketik pesan" style="min-height: 24px; max-height: 120px; overflow-y: auto;" oninput="this.style.height = '24px'; this.style.height = Math.min(this.scrollHeight, 120) + 'px'"></textarea>

                        <button type="button" onclick="document.getElementById('attachment-input').click()" class="shrink-0 text-slate-500 hover:text-slate-700 w-9 h-9 flex items-center justify-center text-xl transition mb-0.5">
                            <i class="fa-solid fa-paperclip"></i>
                        </button>
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

<script src="https://cdn.jsdelivr.net/npm/picmo@5.8.5/dist/umd/picmo.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@picmo/popup-picker@5.8.5/dist/umd/picmo-popup.min.js"></script>
<script type="module">
document.addEventListener('DOMContentLoaded', function() {
    const chatArea = document.getElementById('chat-messages-area');
    const chatForm = document.getElementById('chat-form');
    
    // Emoji Picker
    const emojiBtn = document.getElementById('emoji-btn');
    const messageInput = document.getElementById('message-input');
    
    if (emojiBtn && window.picmo && window.picmoPopup) {
        const { createPopup } = window.picmoPopup;
        
        const picker = createPopup({}, {
            referenceElement: emojiBtn,
            triggerElement: emojiBtn,
            position: 'top-start'
        });
        
        picker.addEventListener('emoji:select', (event) => {
            messageInput.value += event.emoji;
            messageInput.style.height = '24px'; 
            messageInput.style.height = Math.min(messageInput.scrollHeight, 120) + 'px';
            messageInput.focus();
        });

        emojiBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            picker.toggle();
        });
    }

    // Typing Indicator Logic
    const typingIndicator = document.getElementById('typing-indicator');
    const typingName = document.getElementById('typing-name');
    let typingTimer;

    messageInput.addEventListener('input', () => {
        if (window.Echo) {
            window.Echo.private('ticket.{{ $ticket->hashid }}')
                .whisper('typing', {
                    name: '{{ Auth::user()->name }}'
                });
        }
    });

    // Auto scroll ke bawah saat pertama load
    chatArea.scrollTop = chatArea.scrollHeight;

    // Listen to WebSocket (Reverb)
    if (window.Echo) {
        window.Echo.private('ticket.{{ $ticket->hashid }}')
            .listenForWhisper('typing', (e) => {
                if (e.name) {
                    typingName.innerText = e.name;
                    typingIndicator.classList.remove('hidden');
                    
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => {
                        typingIndicator.classList.add('hidden');
                    }, 2000);
                }
            })
            .listen('TicketReplyCreated', (e) => {
                // Sembunyikan indikator saat pesan masuk
                typingIndicator.classList.add('hidden');
                
                // Render message bubble
                const isSelf = e.user_id == {{ Auth::id() }};
                const bubbleHtml = `
                    <div class="flex ${isSelf ? 'justify-end' : 'justify-start'}">
                        <div class="flex max-w-[80%] ${isSelf ? 'flex-row-reverse' : 'flex-row'} items-end gap-3">
                            
                            <!-- Avatar -->
                            <div class="shrink-0">
                                ${isSelf 
                                    ? `<div class="w-10 h-10 rounded-full bg-indigo-100 border-2 border-white shadow-sm flex items-center justify-center text-indigo-700 font-bold">
                                        ${e.user_name.substring(0, 1).toUpperCase()}
                                       </div>`
                                    : `<div class="w-10 h-10 rounded-full bg-slate-800 border-2 border-white shadow-sm flex items-center justify-center text-white font-bold">
                                        <i class="fa-solid fa-headset text-sm"></i>
                                       </div>`
                                }
                            </div>

                            <!-- Bubble -->
                            <div class="flex flex-col ${isSelf ? 'items-end' : 'items-start'}">
                                <div class="text-xs text-slate-500 mb-1 px-1">
                                    <span class="font-bold text-slate-700">${isSelf ? 'Anda' : 'Admin Support'}</span> &bull; 
                                    ${e.created_at}
                                </div>
                                <div class="${isSelf ? 'bg-[#d9fdd3] text-slate-800 rounded-l-xl rounded-br-xl' : 'bg-white border border-slate-200 text-slate-800 rounded-r-xl rounded-bl-xl shadow-sm'} px-4 py-2 text-[15px] leading-relaxed whitespace-pre-wrap">${e.attachment_url ? `<div class="mb-2"><a href="${e.attachment_url}" target="_blank"><img src="${e.attachment_url}" class="rounded-lg max-w-full h-auto max-h-64 object-cover" alt="Attachment"></a></div>` : ''}${e.message}</div>
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
                if(typeof removeAttachment === 'function') removeAttachment();
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

function previewAttachment(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('attachment-preview-img').src = e.target.result;
            document.getElementById('attachment-preview-container').classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function removeAttachment() {
    document.getElementById('attachment-input').value = "";
    document.getElementById('attachment-preview-img').src = "";
    document.getElementById('attachment-preview-container').classList.add('hidden');
}

window.previewAttachment = previewAttachment;
window.removeAttachment = removeAttachment;
</script>
@endsection
