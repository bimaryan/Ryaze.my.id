<div x-data="aiConsultation()" 
     x-show="isOpen" 
     @keydown.escape.window="isOpen = false"
     class="relative z-[100]" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true"
     style="display: none;">
     
    <!-- Backdrop -->
    <div x-show="isOpen" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-slate-900/60 transition-opacity backdrop-blur-sm"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <!-- Modal Panel -->
            <div x-show="isOpen" 
                 @click.away="isOpen = false"
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 text-left shadow-xl transition-all sm:my-8 w-full sm:max-w-lg md:max-w-2xl border border-slate-200 dark:border-slate-800 flex flex-col h-[80vh] sm:h-[600px]">
                
                <!-- Header -->
                <div class="bg-indigo-600 px-4 py-4 sm:px-6 flex justify-between items-center shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold leading-6 text-white" id="modal-title">AI Konsultan Joki</h3>
                            <p class="text-xs text-indigo-200">Ryaze Ecosystem</p>
                        </div>
                    </div>
                    <button type="button" @click="isOpen = false" class="text-indigo-200 hover:text-white transition-colors">
                        <span class="sr-only">Close</span>
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <!-- Chat Area -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-6 bg-slate-50 dark:bg-slate-900/50 space-y-4" id="chat-container">
                    
                    <template x-for="(msg, index) in messages" :key="index">
                        <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                            <div :class="msg.role === 'user' ? 'bg-indigo-600 text-white rounded-2xl rounded-tr-sm' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-2xl rounded-tl-sm'" class="max-w-[85%] px-4 py-3 shadow-sm text-sm" x-html="msg.content">
                            </div>
                        </div>
                    </template>

                    <!-- Loading Indicator -->
                    <div x-show="isLoading" class="flex justify-start">
                        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-slate-400 animate-bounce"></div>
                            <div class="w-2 h-2 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 0.15s"></div>
                            <div class="w-2 h-2 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 0.3s"></div>
                        </div>
                    </div>
                </div>

                <!-- Input Area -->
                <div class="bg-white dark:bg-slate-900 px-4 py-4 sm:px-6 border-t border-slate-200 dark:border-slate-800 shrink-0">
                    <form @submit.prevent="sendMessage" class="flex gap-3">
                        <input type="text" x-model="inputText" :disabled="isLoading" placeholder="Ketik pesan Anda di sini..." class="block w-full rounded-full border-0 py-2.5 px-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:bg-slate-800 dark:ring-slate-700 dark:text-white dark:placeholder:text-slate-500 sm:text-sm sm:leading-6 disabled:opacity-50">
                        <button type="submit" :disabled="isLoading || inputText.trim() === ''" class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed w-12 h-12 shrink-0 transition-colors">
                            <i class="fa-solid fa-paper-plane" x-show="!isLoading"></i>
                            <i class="fa-solid fa-circle-notch fa-spin" x-show="isLoading" style="display:none;"></i>
                        </button>
                    </form>
                    <p class="text-[10px] text-center text-slate-400 mt-2">AI dapat membuat kesalahan. Harap periksa kembali informasi penting.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('aiConsultation', () => ({
        isOpen: false,
        isLoading: false,
        inputText: '',
        token: localStorage.getItem('ryaze_consultation_token') || null,
        messages: [
            {
                role: 'assistant',
                content: 'Halo! Saya AI Konsultan dari Ryaze. Ada ide proyek atau sistem yang ingin Anda buat? Ceritakan kebutuhan Anda, dan mari kita diskusikan solusinya!'
            }
        ],
        
        init() {
            // Load history if token exists
            if (this.token) {
                this.loadHistory();
            }

            window.addEventListener('open-consultation', () => {
                this.isOpen = true;
                setTimeout(() => {
                    this.scrollToBottom();
                }, 100);
            });
        },
        
        async loadHistory() {
            try {
                const response = await fetch('/api/consultation/history?token=' + this.token);
                if (response.ok) {
                    const data = await response.json();
                    if (data.history && data.history.length > 0) {
                        // Reset messages and add history
                        this.messages = [];
                        data.history.forEach(msg => {
                            this.messages.push({
                                role: msg.role,
                                content: msg.role === 'assistant' ? this.parseMarkdown(msg.content) : msg.content
                            });
                        });
                    }
                }
            } catch (error) {
                console.error("Gagal memuat riwayat chat:", error);
            }
        },
        
        scrollToBottom() {
            const container = document.getElementById('chat-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },
        
        parseMarkdown(text) {
            let html = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            html = html.replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" class="text-indigo-400 hover:text-indigo-500 font-bold underline">$1</a>');
            html = html.replace(/\n/g, '<br>');
            return html;
        },

        async sendMessage() {
            if (this.inputText.trim() === '' || this.isLoading) return;

            const userText = this.inputText;
            this.inputText = '';
            
            this.messages.push({
                role: 'user',
                content: userText
            });
            
            this.isLoading = true;
            this.scrollToBottom();

            try {
                // Remove CSRF header if we send it in api route, but for api route it is not needed.
                const response = await fetch('/api/consultation/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        message: userText,
                        token: this.token
                    })
                });

                const data = await response.json();
                
                if (data.token) {
                    this.token = data.token;
                    localStorage.setItem('ryaze_consultation_token', this.token);
                }

                if (response.ok) {
                    this.messages.push({
                        role: 'assistant',
                        content: this.parseMarkdown(data.reply)
                    });
                } else {
                    this.messages.push({
                        role: 'assistant',
                        content: 'Maaf, terjadi kesalahan: ' + (data.error || 'Server error')
                    });
                }
            } catch (error) {
                this.messages.push({
                    role: 'assistant',
                    content: 'Gagal terhubung ke server. Silakan coba lagi.'
                });
            } finally {
                this.isLoading = false;
                setTimeout(() => this.scrollToBottom(), 100);
            }
        }
    }));
});
</script>
