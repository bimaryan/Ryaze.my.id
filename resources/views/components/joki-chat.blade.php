@props(['order', 'user', 'fetchRoute', 'storeRoute'])

<div x-data="jokiChat()" x-init="initChat()" class="mt-8 flex flex-col ui-panel overflow-hidden shadow-none h-[500px]">
    <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#0F1A2A]">
        <h3 class="ui-eyebrow text-[11px] flex items-center gap-2">
            <i class="fa-solid fa-comments text-[10px]"></i> Live Chat Diskusi Proyek
        </h3>
        <span class="ui-status">
            <span class="ui-status-dot animate-pulse"></span> Online
        </span>
    </div>

    <!-- Area Pesan -->
    <div id="chat-box" class="flex-1 p-6 overflow-y-auto space-y-4 bg-[#F4F8FC] dark:bg-[#0A1322]">
        <template x-if="loading">
            <div class="flex justify-center py-4 text-[#94A3B4] dark:text-white/40">
                <i class="fa-solid fa-spinner fa-spin text-2xl"></i>
            </div>
        </template>

        <template x-for="msg in messages" :key="msg.id">
            <div :class="msg.sender_id == {{ $user->id }} ? 'flex justify-end' : 'flex justify-start'">
                <div :class="msg.sender_id == {{ $user->id }} ? 'bg-[#1D5B96] text-white' : 'bg-white dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] text-[#0F1A2A] dark:text-white'"
                     class="max-w-[75%] px-4 py-2.5 shadow-none relative group">
                    <p class="font-mono text-[10px] uppercase tracking-[0.15em] font-bold mb-1" :class="msg.sender_id == {{ $user->id }} ? 'text-[#7FB4E2]' : 'text-[#737373] dark:text-white/40'" x-text="msg.sender_id == {{ $user->id }} ? 'Anda' : msg.sender_name"></p>
                    <p class="text-sm whitespace-pre-wrap" x-text="msg.message"></p>
                    <span class="font-mono text-[10px] mt-1 block text-right" :class="msg.sender_id == {{ $user->id }} ? 'text-[#7FB4E2]/70' : 'text-[#94A3B4] dark:text-white/35'" x-text="new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                </div>
            </div>
        </template>
        <div x-ref="bottom"></div>
    </div>

    <!-- Area Input -->
    <div class="bg-white dark:bg-[#0A1322] border-t border-[#E3EAF1] dark:border-[#1B2C44] p-4">
        <form @submit.prevent="sendMessage" class="flex gap-2">
            <input type="text" x-model="newMessage" placeholder="Ketik pesan Anda di sini..." required
                   class="flex-1 bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1D5B96]/20 focus:border-[#1D5B96] dark:focus:border-[#6BA8DC] outline-none transition disabled:opacity-50" :disabled="sending">
            <button type="submit" :disabled="sending || !newMessage.trim()"
                    class="bg-[#12233A] hover:bg-[#1D5B96] disabled:bg-[#C9D5E2] dark:disabled:bg-[#1B2C44] disabled:cursor-not-allowed text-white font-mono text-[11px] uppercase tracking-[0.15em] font-semibold px-5 py-2.5 transition-colors flex items-center gap-2">
                <i class="fa-solid fa-paper-plane text-[10px]" x-show="!sending"></i>
                <i class="fa-solid fa-spinner fa-spin text-[10px]" x-show="sending"></i>
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
