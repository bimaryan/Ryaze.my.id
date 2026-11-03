@extends('index')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header 
        title="{{ $ticket->subject }}" 
        eyebrow="Percakapan Tiket"
        subtitle="Tiket #{{ $ticket->hashid }} &bull; Departemen {{ $ticket->department }}">
        <x-slot:iconSlot>
            <div class="shrink-0 w-12 h-12 bg-[#E8F0F9] dark:bg-[#1D5B96]/20 flex items-center justify-center">
                <i class="fa-solid fa-comments text-[#1D5B96] dark:text-[#6BA8DC] text-xl"></i>
            </div>
        </x-slot:iconSlot>
        <x-slot:actions>
            <div id="ticket-status-badge">
                @if($ticket->status == 'open')
                    <span class="px-3 py-1.5 font-mono text-[11px] uppercase tracking-wider font-bold border bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/50">
                        <i class="fa-solid fa-clock mr-1"></i> Menunggu Balasan
                    </span>
                @elseif($ticket->status == 'answered')
                    <span class="px-3 py-1.5 font-mono text-[11px] uppercase tracking-wider font-bold border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/50">
                        <i class="fa-solid fa-check mr-1"></i> Dijawab
                    </span>
                @else
                    <span class="px-3 py-1.5 font-mono text-[11px] uppercase tracking-wider font-bold border bg-[#F0F6FC] text-[#525252] border-[#E3EAF1] dark:bg-[#12233A] dark:text-white/60 dark:border-[#2A4463]">
                        <i class="fa-solid fa-lock mr-1"></i> Tiket Ditutup
                    </span>
                @endif
            </div>
            <a href="{{ route('user_hosting.tickets.index') }}" class="ui-btn ui-btn-md ui-btn-secondary">
                Kembali
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="max-w-4xl mt-6 flex flex-col h-[700px] ui-panel overflow-hidden">
        
        {{-- Chat Messages Area --}}
        <div id="chat-messages-area" class="flex-1 overflow-y-auto p-6 bg-[#F4F8FC] dark:bg-[#0A1322] flex flex-col gap-6">
            @foreach($ticket->replies as $reply)
                @php
                    $isSelf = $reply->user_id == Auth::id();
                @endphp
                <div class="flex {{ $isSelf ? 'justify-end' : 'justify-start' }}">
                    <div class="flex max-w-[80%] {{ $isSelf ? 'flex-row-reverse' : 'flex-row' }} items-end gap-3">
                        
                        {{-- Avatar --}}
                        <div class="shrink-0">
                            @if($isSelf)
                                <div class="w-10 h-10 rounded-full bg-[#E8F0F9] dark:bg-[#1D5B96]/20 border-2 border-white dark:border-[#2A4463] shadow-none flex items-center justify-center text-[#1D5B96] dark:text-[#6BA8DC] font-bold">
                                    {{ substr($reply->user->name, 0, 1) }}
                                </div>
                            @else
                                <div class="w-10 h-10 rounded-full bg-[#12233A] border-2 border-white dark:border-[#2A4463] shadow-none flex items-center justify-center text-white font-bold">
                                    <i class="fa-solid fa-headset text-sm"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Bubble --}}
                        <div class="flex flex-col {{ $isSelf ? 'items-end' : 'items-start' }}">
                            <div class="{{ $isSelf ? 'bg-[#E8F0F9] dark:bg-[#12233A] text-[#0F1A2A] dark:text-white' : 'bg-white dark:bg-[#0F1D30] text-[#0F1A2A] dark:text-white/85 border border-[#E3EAF1] dark:border-[#2A4463]' }} px-4 py-2 text-[15px] leading-relaxed break-words break-all min-w-[120px]">
                                <div class="font-mono text-[10px] uppercase tracking-[0.2em] mb-1.5 pb-1 border-b border-[#C9D5E2]/60 dark:border-[#2A4463] text-[#525252] dark:text-white/45 {{ $isSelf ? 'text-right' : 'text-left' }}">
                                    {{ $isSelf ? 'Anda' : $reply->user->name }} &bull; {{ $reply->created_at->format('H:i') }}
                                    @if($isSelf)
                                        <span class="ml-1 ticket-read-status" data-reply-id="{{ $reply->id }}">
                                            @if($reply->read_at)
                                                <i class="fa-solid fa-check-double text-[#1D5B96] dark:text-[#6BA8DC]"></i>
                                            @else
                                                <i class="fa-solid fa-check-double text-[#8A9BAE] dark:text-white/45"></i>
                                            @endif
                                        </span>
                                    @endif
                                </div>
                                @if($reply->attachment_path)
                                    <div class="mb-2">
                                        <a href="{{ asset('storage/' . $reply->attachment_path) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $reply->attachment_path) }}" class="max-w-full h-auto max-h-64 object-cover" alt="Attachment">
                                        </a>
                                    </div>
                                @endif
                                <div class="whitespace-pre-wrap">{{ $reply->message }}</div>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- Reply Form --}}
        <div class="px-4 py-3 bg-[#F4F8FC] dark:bg-[#12233A] border-t border-[#E3EAF1] dark:border-[#2A4463] flex flex-col relative">
            <div class="flex items-center justify-between gap-4 px-1 pb-2">
                <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">Ketik Balasan</span>
                <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC]">#{{ $ticket->hashid }}</span>
            </div>
            <div id="typing-indicator" class="hidden text-[13px] text-[#525252] dark:text-white/50 italic mb-2 px-1 transition-all duration-300">
                <i class="fa-solid fa-pen-nib mr-1"></i> <span id="typing-name"></span> sedang mengetik...
            </div>

            <!-- Emoji Picker Container -->
            <div id="emoji-picker-container" class="hidden absolute bottom-full left-4 mb-2 z-50 shadow-none overflow-hidden border border-[#E3EAF1] dark:border-[#2A4463] bg-white dark:bg-[#0F1D30]">
                <emoji-picker class="light"></emoji-picker>
            </div>

            <div id="attachment-preview-container" class="hidden mb-3 bg-white dark:bg-[#0F1D30] p-2 border border-[#E3EAF1] dark:border-[#2A4463] w-max relative">
                <button type="button" onclick="removeAttachment()" class="absolute -top-2 -right-2 bg-rose-600 text-white w-6 h-6 flex items-center justify-center text-xs hover:bg-rose-700 transition"><i class="fa-solid fa-xmark"></i></button>
                <img id="attachment-preview-img" src="" class="h-20 object-cover">
            </div>

            @if($ticket->status != 'closed')
                <form id="chat-form" action="{{ route('user_hosting.tickets.reply', $ticket->hashid) }}" method="POST" enctype="multipart/form-data" class="flex gap-2 items-end">
                    @csrf
                    
                    <input type="file" name="attachment" id="attachment-input" accept="image/png, image/jpeg, image/jpg" class="hidden" onchange="previewAttachment(this)">

                    <div class="flex-1 bg-white dark:bg-[#0F1D30] border border-[#E3EAF1] dark:border-[#2A4463] flex items-end px-2 py-1.5">
                        <button type="button" id="emoji-btn" class="shrink-0 text-[#8A9BAE] dark:text-white/40 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] w-9 h-9 flex items-center justify-center text-xl transition mb-0.5">
                            <i class="fa-regular fa-face-smile"></i>
                        </button>

                        <textarea name="message" id="message-input" rows="1" class="bg-transparent border-none px-2 py-1.5 text-[15px] focus:ring-0 focus:outline-none resize-none m-0 w-full text-[#0F1A2A] dark:text-white placeholder-[#8A9BAE] dark:placeholder-white/40" placeholder="Ketik pesan" style="min-height: 24px; max-height: 120px; overflow-y: auto;" oninput="this.style.height = '24px'; this.style.height = Math.min(this.scrollHeight, 120) + 'px'"></textarea>

                        <button type="button" onclick="document.getElementById('attachment-input').click()" class="shrink-0 text-[#8A9BAE] dark:text-white/40 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] w-9 h-9 flex items-center justify-center text-xl transition mb-0.5">
                            <i class="fa-solid fa-paperclip"></i>
                        </button>
                    </div>

                    <button type="submit" class="shrink-0 bg-[#12233A] hover:bg-[#1D5B96] text-white w-12 h-12 flex items-center justify-center transition-colors mb-0.5">
                        <i class="fa-solid fa-paper-plane mr-1"></i>
                    </button>
                </form>
            @else
                <div class="text-center py-3 text-sm text-[#525252] dark:text-white/50 font-medium">
                    <i class="fa-solid fa-lock mr-2 text-[#8A9BAE] dark:text-white/45"></i> Tiket ini telah ditutup dan tidak dapat menerima balasan lagi.
                </div>
            @endif
        </div>

    </div>

<script type="module" src="https://unpkg.com/emoji-picker-element@1"></script>
<script type="module">
(function() {

    const chatArea = document.getElementById('chat-messages-area');
    const chatForm = document.getElementById('chat-form');
    
    // Emoji Picker
    const emojiBtn = document.getElementById('emoji-btn');
    const messageInput = document.getElementById('message-input');
    const emojiContainer = document.getElementById('emoji-picker-container');
    const emojiPicker = document.querySelector('emoji-picker');
    
    if (emojiBtn && emojiContainer && emojiPicker) {
        emojiBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            emojiContainer.classList.toggle('hidden');
        });

        emojiPicker.addEventListener('emoji-click', event => {
            messageInput.value += event.detail.unicode;
            messageInput.style.height = '24px'; 
            messageInput.style.height = Math.min(messageInput.scrollHeight, 120) + 'px';
            messageInput.focus();
        });

        // Hide when clicking outside
        document.addEventListener('click', (e) => {
            if (!emojiContainer.contains(e.target) && !emojiBtn.contains(e.target)) {
                emojiContainer.classList.add('hidden');
            }
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
                
                // Update status badge if available
                if (e.ticket_status) {
                    const statusBadge = document.getElementById('ticket-status-badge');
                    if (statusBadge) {
                        if (e.ticket_status === 'open') {
                            statusBadge.innerHTML = '<span class="px-3 py-1.5 font-mono text-[11px] uppercase tracking-wider font-bold border bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/50"><i class="fa-solid fa-clock mr-1"></i> Menunggu Balasan</span>';
                        } else if (e.ticket_status === 'answered') {
                            statusBadge.innerHTML = '<span class="px-3 py-1.5 font-mono text-[11px] uppercase tracking-wider font-bold border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/50"><i class="fa-solid fa-check mr-1"></i> Dijawab</span>';
                        } else {
                            statusBadge.innerHTML = '<span class="px-3 py-1.5 font-mono text-[11px] uppercase tracking-wider font-bold border bg-[#F0F6FC] text-[#525252] border-[#E3EAF1] dark:bg-[#12233A] dark:text-white/60 dark:border-[#2A4463]"><i class="fa-solid fa-lock mr-1"></i> Tiket Ditutup</span>';
                        }
                    }
                }

                // Render message bubble
                const isSelf = e.user_id == {{ Auth::id() }};
                const bubbleHtml = `
                    <div class="flex ${isSelf ? 'justify-end' : 'justify-start'}">
                        <div class="flex max-w-[80%] ${isSelf ? 'flex-row-reverse' : 'flex-row'} items-end gap-3">
                            
                            <!-- Avatar -->
                            <div class="shrink-0">
                                ${isSelf 
                                    ? `<div class="w-10 h-10 rounded-full bg-[#E8F0F9] dark:bg-[#1D5B96]/20 border-2 border-white dark:border-[#2A4463] shadow-none flex items-center justify-center text-[#1D5B96] dark:text-[#6BA8DC] font-bold">
                                        ${e.user_name.substring(0, 1).toUpperCase()}
                                       </div>`
                                    : `<div class="w-10 h-10 rounded-full bg-[#12233A] border-2 border-white dark:border-[#2A4463] shadow-none flex items-center justify-center text-white font-bold">
                                        <i class="fa-solid fa-headset text-sm"></i>
                                       </div>`
                                }
                            </div>

                            <!-- Bubble -->
                            <div class="flex flex-col ${isSelf ? 'items-end' : 'items-start'}">
                                <div class="${isSelf ? 'bg-[#E8F0F9] dark:bg-[#12233A] text-[#0F1A2A] dark:text-white' : 'bg-white dark:bg-[#0F1D30] text-[#0F1A2A] dark:text-white/85 border border-[#E3EAF1] dark:border-[#2A4463]'} px-4 py-2 text-[15px] leading-relaxed break-words break-all min-w-[120px]">
                                    <div class="font-mono text-[10px] uppercase tracking-[0.2em] mb-1.5 pb-1 border-b border-[#C9D5E2]/60 dark:border-[#2A4463] text-[#525252] dark:text-white/45 ${isSelf ? 'text-right' : 'text-left'}">
                                        ${isSelf ? 'Anda' : e.user_name} &bull; ${e.created_at.split(', ')[1] || e.created_at}
                                        ${isSelf ? `
                                            <span class="ml-1 ticket-read-status" data-reply-id="${e.id}">
                                                <i class="fa-solid fa-check-double text-[#8A9BAE] dark:text-white/45"></i>
                                            </span>
                                        ` : ''}
                                    </div>
                                    ${e.attachment_url ? `
                                        <div class="mb-2">
                                            <a href="${e.attachment_url}" target="_blank">
                                                <img src="${e.attachment_url}" class="max-w-full h-auto max-h-64 object-cover" alt="Attachment">
                                            </a>
                                        </div>
                                    ` : ''}
                                    <div class="whitespace-pre-wrap">${e.message}</div>
                                </div>
                            </div>

                        </div>
                    </div>
                `;
                
                const isScrolledToBottom = chatArea.scrollHeight - chatArea.clientHeight <= chatArea.scrollTop + 10;
                
                chatArea.insertAdjacentHTML('beforeend', bubbleHtml);
                
                if (isScrolledToBottom || isSelf) {
                    chatArea.scrollTop = chatArea.scrollHeight;
                }

                if (e.is_admin && document.hasFocus()) {
                    fetch('{{ route("user_hosting.tickets.markAsRead", $ticket->hashid) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                }
            })
            .listen('TicketRepliesRead', (e) => {
                document.querySelectorAll('.ticket-read-status i').forEach(icon => {
                    icon.classList.remove('text-[#8A9BAE]', 'dark:text-white/45');
                    icon.classList.add('text-[#1D5B96]', 'dark:text-[#6BA8DC]');
                });
            });
    }

    window.addEventListener('focus', () => {
        fetch('{{ route("user_hosting.tickets.markAsRead", $ticket->hashid) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
    });

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
                if(typeof removeAttachment === 'function') removeAttachment();
                window.location.reload();
                if(typeof removeAttachment === 'function') removeAttachment();
            })
            .catch(err => {
                console.error('Error sending message:', err);
                hotToast('Gagal mengirim pesan. Silakan coba lagi.', 'error');
            })
            .finally(() => {
                submitBtn.innerHTML = originalBtnContent;
                submitBtn.disabled = false;
            });
        });
    }
})();

window.previewAttachment = function(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('attachment-preview-img').src = e.target.result;
            document.getElementById('attachment-preview-container').classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
};

window.removeAttachment = function() {
    document.getElementById('attachment-input').value = "";
    document.getElementById('attachment-preview-img').src = "";
    document.getElementById('attachment-preview-container').classList.add('hidden');
};
</script>

</x-ui.page-layout>
@endsection
