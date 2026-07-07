@props(['order', 'user', 'fetchRoute', 'storeRoute'])

<div x-data="jokiChat()" x-init="initChat()" class="mt-8 flex flex-col bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm h-[500px]">
    <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex justify-between items-center">
        <h3 class="font-bold text-slate-800 flex items-center gap-2">
            <i class="fa-solid fa-comments text-indigo-500"></i> Live Chat Diskusi Proyek
        </h3>
        <span class="text-xs font-semibold px-2 py-1 bg-green-100 text-green-700 rounded-full flex items-center gap-1">
            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
            Online
        </span>
    </div>

    <!-- Area Pesan -->
    <div id="chat-box" class="flex-1 p-6 overflow-y-auto space-y-4 bg-slate-50/50">
        <template x-if="loading">
            <div class="flex justify-center py-4 text-slate-400">
                <i class="fa-solid fa-spinner fa-spin text-2xl"></i>
            </div>
        </template>
        
        <template x-for="msg in messages" :key="msg.id">
            <div :class="msg.sender_id == {{ $user->id }} ? 'flex justify-end' : 'flex justify-start'">
                <div :class="msg.sender_id == {{ $user->id }} ? 'bg-indigo-600 text-white rounded-l-2xl rounded-tr-2xl' : 'bg-white border border-slate-200 text-slate-800 rounded-r-2xl rounded-tl-2xl'" 
                     class="max-w-[75%] px-4 py-2.5 shadow-sm relative group">
                    <p class="text-xs font-bold mb-1 opacity-75" x-text="msg.sender_id == {{ $user->id }} ? 'Anda' : msg.sender_name"></p>
                    <p class="text-sm whitespace-pre-wrap" x-text="msg.message"></p>
                    <span class="text-[10px] opacity-50 mt-1 block text-right" x-text="new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                </div>
            </div>
        </template>
        <div x-ref="bottom"></div>
    </div>

    <!-- Area Input -->
    <div class="bg-white border-t border-slate-200 p-4">
        <form @submit.prevent="sendMessage" class="flex gap-2">
            <input type="text" x-model="newMessage" placeholder="Ketik pesan Anda di sini..." required
                   class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition disabled:opacity-50" :disabled="sending">
            <button type="submit" :disabled="sending || !newMessage.trim()"
                    class="bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-300 text-white px-5 py-2.5 rounded-xl transition-colors flex items-center gap-2">
                <i class="fa-solid fa-paper-plane" x-show="!sending"></i>
                <i class="fa-solid fa-spinner fa-spin" x-show="sending"></i>
                <span class="hidden sm:inline">Kirim</span>
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('jokiChat', () => ({
        messages: [],
        newMessage: '',
        loading: true,
        sending: false,
        orderId: '{{ $order->id }}',

        initChat() {
            this.fetchMessages();
            this.listenForMessages();
        },

        fetchMessages() {
            fetch('{{ $fetchRoute }}')
                .then(res => res.json())
                .then(data => {
                    this.messages = data;
                    this.loading = false;
                    this.scrollToBottom();
                })
                .catch(err => {
                    console.error('Error fetching messages:', err);
                    this.loading = false;
                });
        },

        sendMessage() {
            if (!this.newMessage.trim()) return;
            this.sending = true;

            fetch('{{ $storeRoute }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: this.newMessage })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    this.messages.push(data.message);
                    this.newMessage = '';
                    this.scrollToBottom();
                }
            })
            .finally(() => {
                this.sending = false;
            });
        },

        listenForMessages() {
            if (window.Echo) {
                window.Echo.private(`chat.joki_order.${this.orderId}`)
                    .listen('MessageSent', (e) => {
                        this.messages.push(e);
                        this.scrollToBottom();
                    });
            }
        },

        scrollToBottom() {
            setTimeout(() => {
                this.$refs.bottom.scrollIntoView({ behavior: 'smooth' });
            }, 100);
        }
    }));
});
</script>
