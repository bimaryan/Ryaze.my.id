@extends('index')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header 
        title="{{ $ticket->subject }}" 
        subtitle="Tiket Klien: {{ $ticket->user->name }} ({{ $ticket->user->email }})">
        <x-slot:iconSlot>
            <div class="shrink-0 w-12 h-12 bg-indigo-100 dark:bg-indigo-500/20 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-headset text-indigo-600 dark:text-indigo-400 text-xl"></i>
            </div>
        </x-slot:iconSlot>
        <x-slot:actions>
            @if($ticket->status != 'closed')
                <form action="{{ route('admin_hosting.tickets.close', $ticket->hashid) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 hover:bg-rose-200 px-4 py-2 rounded-lg font-bold transition text-sm flex items-center gap-2 shadow-sm border border-rose-200 dark:border-rose-500/40">
                        <i class="fa-solid fa-lock"></i> Tutup Tiket
                    </button>
                </form>
            @endif
            <a href="{{ route('admin_hosting.tickets.index') }}" class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/40 px-4 py-2 rounded-lg font-medium transition text-sm flex items-center gap-2 shadow-sm">
                Kembali
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mt-6">
        
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm p-5">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-4 border-b border-slate-100 dark:border-slate-700 pb-2">Detail Klien</h3>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold">
                        {{ substr($ticket->user->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-bold text-slate-800 dark:text-slate-100 text-sm">{{ $ticket->user->name }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $ticket->user->email }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm p-5">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-4 border-b border-slate-100 dark:border-slate-700 pb-2">Informasi Tiket</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">Status</div>
                        <div id="ticket-status-badge">
                            @if($ticket->status == 'open')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300">Open</span>
                            @elseif($ticket->status == 'answered')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300">Answered</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300">Closed</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">Prioritas</div>
                        <div class="font-semibold text-slate-800 dark:text-slate-100 capitalize">{{ $ticket->priority }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">Departemen</div>
                        <div class="font-semibold text-slate-800 dark:text-slate-100">{{ $ticket->department }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">Dibuat</div>
                        <div class="font-semibold text-slate-800 dark:text-slate-100">{{ $ticket->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-3 flex flex-col h-[700px] bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm overflow-hidden">
            
            {{-- Chat Messages Area --}}
            <div id="chat-messages-area" class="flex-1 overflow-y-auto p-6 bg-[#efeae2] flex flex-col gap-6">
                @foreach($ticket->replies as $reply)
                    @php
                        $isAdmin = $reply->user->role == 'admin_hosting' || $reply->user->role == 'superadmin';
                    @endphp
                    <div class="flex {{ $isAdmin ? 'justify-end' : 'justify-start' }}">
                        <div class="flex max-w-[80%] {{ $isAdmin ? 'flex-row-reverse' : 'flex-row' }} items-end gap-3">
                            
                            {{-- Avatar --}}
                            <div class="shrink-0">
                                @if($isAdmin)
                                    <div class="w-10 h-10 rounded-full bg-slate-800 border-2 border-white dark:border-slate-700 shadow-sm flex items-center justify-center text-white font-bold">
                                        <i class="fa-solid fa-headset text-sm"></i>
                                    </div>
                                @else
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-500/20 border-2 border-white dark:border-slate-700 shadow-sm flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold">
                                        {{ substr($reply->user->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>

                            {{-- Bubble --}}
                            <div class="flex flex-col {{ $isAdmin ? 'items-end' : 'items-start' }}">
                                <div class="{{ $isAdmin ? 'bg-[#d9fdd3] dark:bg-slate-700 text-slate-800 dark:text-slate-100 rounded-l-xl rounded-br-xl' : 'bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-r-xl rounded-bl-xl shadow-sm' }} px-4 py-2 text-[15px] leading-relaxed break-words break-all min-w-[120px]">
                                    <div class="text-[11px] mb-1.5 opacity-75 font-semibold border-b border-black/5 pb-1 {{ $isAdmin ? 'text-right' : 'text-left' }}">
                                        {{ $isAdmin ? 'Anda (Support)' : $reply->user->name }} &bull; {{ $reply->created_at->format('H:i') }}
                                        @if($isAdmin)
                                            <span class="ml-1 ticket-read-status" data-reply-id="{{ $reply->id }}">
                                                @if($reply->read_at)
                                                    <i class="fa-solid fa-check-double text-blue-500 dark:text-blue-400"></i>
                                                @else
                                                    <i class="fa-solid fa-check-double text-slate-400 dark:text-slate-500"></i>
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                    @if($reply->attachment_path)
                                        <div class="mb-2">
                                            <a href="{{ asset('storage/' . $reply->attachment_path) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $reply->attachment_path) }}" class="rounded-lg max-w-full h-auto max-h-64 object-cover" alt="Attachment">
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
            <div class="px-4 py-3 bg-[#f0f2f5] border-t border-slate-200 dark:border-slate-700 flex flex-col relative">
                <div id="typing-indicator" class="hidden text-[13px] text-slate-500 dark:text-slate-400 italic mb-2 px-1 transition-all duration-300">
                    <i class="fa-solid fa-pen-nib mr-1"></i> <span id="typing-name"></span> sedang mengetik...
                </div>

                <!-- Emoji Picker Container -->
                <div id="emoji-picker-container" class="hidden absolute bottom-full left-4 mb-2 z-50 shadow-xl rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/60">
                    <emoji-picker class="light"></emoji-picker>
                </div>

                <div id="attachment-preview-container" class="hidden mb-3 bg-white dark:bg-slate-800/60 p-2 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 w-max relative">
                    <button type="button" onclick="removeAttachment()" class="absolute -top-2 -right-2 bg-red-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs hover:bg-red-600 transition shadow"><i class="fa-solid fa-xmark"></i></button>
                    <img id="attachment-preview-img" src="" class="h-20 object-cover rounded-lg">
                </div>

                @if($ticket->status != 'closed')
                    <form id="chat-form" action="{{ route('admin_hosting.tickets.reply', $ticket->hashid) }}" method="POST" enctype="multipart/form-data" class="flex gap-2 items-end">
                        @csrf
                        
                        <input type="file" name="attachment" id="attachment-input" accept="image/png, image/jpeg, image/jpg" class="hidden" onchange="previewAttachment(this)">

                        <div class="flex-1 bg-white dark:bg-slate-800/60 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-end px-2 py-1.5">
                            <button type="button" id="emoji-btn" class="shrink-0 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 w-9 h-9 flex items-center justify-center text-xl transition mb-0.5">
                                <i class="fa-regular fa-face-smile"></i>
                            </button>

                            <textarea name="message" id="message-input" rows="1" class="bg-transparent border-none px-2 py-1.5 text-[15px] focus:ring-0 focus:outline-none resize-none m-0 w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="Ketik pesan" style="min-height: 24px; max-height: 120px; overflow-y: auto;" oninput="this.style.height = '24px'; this.style.height = Math.min(this.scrollHeight, 120) + 'px'"></textarea>

                            <button type="button" onclick="document.getElementById('attachment-input').click()" class="shrink-0 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 w-9 h-9 flex items-center justify-center text-xl transition mb-0.5">
                                <i class="fa-solid fa-paperclip"></i>
                            </button>
                        </div>

                        <button type="submit" class="shrink-0 bg-[#00a884] hover:bg-[#029676] text-white w-12 h-12 rounded-full flex items-center justify-center transition shadow-sm mb-0.5">
                            <i class="fa-solid fa-paper-plane mr-1"></i>
                        </button>
                    </form>
                @else
                    <div class="text-center py-3 text-sm text-slate-500 dark:text-slate-400 font-medium">
                        <i class="fa-solid fa-lock mr-2 text-slate-400 dark:text-slate-500"></i> Tiket telah ditutup.
                    </div>
                @endif
            </div>

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
                            statusBadge.innerHTML = '<span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300">Open</span>';
                        } else if (e.ticket_status === 'answered') {
                            statusBadge.innerHTML = '<span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300">Answered</span>';
                        } else {
                            statusBadge.innerHTML = '<span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300">Closed</span>';
                        }
                    }
                }

                // Render message bubble
                const isSelf = e.user_id == {{ Auth::id() }};
                const bubbleHtml = `
                    <div class="flex ${e.is_admin ? 'justify-end' : 'justify-start'}">
                        <div class="flex max-w-[80%] ${e.is_admin ? 'flex-row-reverse' : 'flex-row'} items-end gap-3">
                            
                            <!-- Avatar -->
                            <div class="shrink-0">
                                ${e.is_admin 
                                    ? `<div class="w-10 h-10 rounded-full bg-slate-800 border-2 border-white dark:border-slate-700 shadow-sm flex items-center justify-center text-white font-bold">
                                        <i class="fa-solid fa-headset text-sm"></i>
                                       </div>`
                                    : `<div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-500/20 border-2 border-white dark:border-slate-700 shadow-sm flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold">
                                        ${e.user_name.substring(0, 1).toUpperCase()}
                                       </div>`
                                }
                            </div>

                            <!-- Bubble -->
                            <div class="flex flex-col ${e.is_admin ? 'items-end' : 'items-start'}">
                                <div class="${e.is_admin ? 'bg-[#d9fdd3] dark:bg-slate-700 text-slate-800 dark:text-slate-100 rounded-l-xl rounded-br-xl' : 'bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-r-xl rounded-bl-xl shadow-sm'} px-4 py-2 text-[15px] leading-relaxed break-words break-all min-w-[120px]">
                                    <div class="text-[11px] mb-1.5 opacity-75 font-semibold border-b border-black/5 pb-1 ${e.is_admin ? 'text-right' : 'text-left'}">
                                        ${e.is_admin ? 'Anda (Support)' : e.user_name} &bull; ${e.created_at.split(', ')[1] || e.created_at}
                                        ${e.is_admin ? `
                                            <span class="ml-1 ticket-read-status" data-reply-id="${e.id}">
                                                <i class="fa-solid fa-check-double text-slate-400 dark:text-slate-500"></i>
                                            </span>
                                        ` : ''}
                                    </div>
                                    ${e.attachment_url ? `
                                        <div class="mb-2">
                                            <a href="${e.attachment_url}" target="_blank">
                                                <img src="${e.attachment_url}" class="rounded-lg max-w-full h-auto max-h-64 object-cover" alt="Attachment">
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

                // If user sent a message and we are focused, mark as read
                if (!e.is_admin && document.hasFocus()) {
                    fetch('{{ route("admin_hosting.tickets.markAsRead", $ticket->hashid) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                }
            })
            .listen('TicketRepliesRead', (e) => {
                document.querySelectorAll('.ticket-read-status i.text-slate-400 dark:text-slate-500').forEach(icon => {
                    icon.classList.remove('text-slate-400 dark:text-slate-500');
                    icon.classList.add('text-blue-500 dark:text-blue-400');
                });
            });
    }

    window.addEventListener('focus', () => {
        fetch('{{ route("admin_hosting.tickets.markAsRead", $ticket->hashid) }}', {
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
