<x-public-layout
    title="Konsultasi AI Joki - Ryaze"
    description="Konsultasikan ide proyek atau sistem yang ingin Anda buat dengan AI Konsultan kami."
    body-class="bg-slate-50 font-sans antialiased text-slate-900 dark:bg-slate-950 dark:text-slate-100 selection:bg-indigo-600 selection:text-white"
    og-image="{{ url('/og-image.png') }}"
    :links="[
        ['label' => 'Beranda', 'href' => url('/')],
        ['label' => 'Portofolio', 'href' => url('/#portfolio')],
        ['label' => 'Blog', 'href' => route('blog.index')],
    ]">

    <div class="pt-32 pb-20 min-h-screen">
        <div class="container mx-auto px-4 max-w-4xl" x-data="aiConsultationPage()">
            
            <div class="flex flex-col h-[75vh] min-h-[500px] bg-[#efeae2] dark:bg-[#0b141a] rounded-3xl shadow-xl border border-slate-200 dark:border-slate-800 overflow-hidden relative">
                
                <!-- Header -->
                <div class="bg-[#008069] dark:bg-[#202c33] px-4 py-3 flex items-center justify-between shrink-0 shadow-sm z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white text-lg">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                        <div>
                            <h1 class="text-base font-semibold text-white tracking-tight">AI Konsultan Joki</h1>
                            <p class="text-xs text-white/80">Online</p>
                        </div>
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="flex-1 overflow-y-auto p-4 md:p-6 space-y-4 relative" id="chat-container">
                    <!-- WA Background Pattern -->
                    <div class="absolute inset-0 z-0 opacity-[0.06] dark:opacity-[0.03]" style="background-image: url('https://web.whatsapp.com/img/bg-chat-tile-dark_a4be512e7195b6b733d9110b408f075d.png'); background-repeat: repeat;"></div>
                    
                    <div class="relative z-10 space-y-3 flex flex-col">
                        <template x-for="(msg, index) in messages" :key="index">
                            <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                                <div :class="msg.role === 'user' ? 'bg-[#d9fdd3] dark:bg-[#005c4b] text-[#111b21] dark:text-[#e9edef] rounded-md rounded-tr-none' : 'bg-white dark:bg-[#202c33] text-[#111b21] dark:text-[#e9edef] rounded-md rounded-tl-none'" class="max-w-[85%] sm:max-w-[75%] px-3 py-2 shadow-[0_1px_0.5px_rgba(11,20,26,.13)] text-[15px] leading-relaxed break-words">
                                    <div x-html="msg.content"></div>
                                </div>
                            </div>
                        </template>

                        <!-- Loading Indicator -->
                        <div x-show="isLoading" class="flex justify-start">
                            <div class="bg-white dark:bg-[#202c33] rounded-md rounded-tl-none px-4 py-3 shadow-[0_1px_0.5px_rgba(11,20,26,.13)] flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-slate-400 animate-bounce"></div>
                                <div class="w-2 h-2 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 0.15s"></div>
                                <div class="w-2 h-2 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 0.3s"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Input Area -->
                <div class="bg-[#f0f2f5] dark:bg-[#202c33] px-4 py-3 shrink-0 flex flex-col gap-2">
                    <form @submit.prevent="sendMessage" class="flex-1 flex gap-3 items-end">
                        <div class="flex-1 bg-white dark:bg-[#2a3942] rounded-lg flex items-center shadow-[0_1px_0.5px_rgba(11,20,26,.13)] overflow-hidden">
                            <input type="text" x-model="inputText" :disabled="isLoading" placeholder="Ketik pesan" class="block w-full border-0 bg-transparent py-3 px-4 text-[#111b21] dark:text-[#e9edef] placeholder:text-[#667781] dark:placeholder:text-[#8696a0] focus:ring-0 sm:text-[15px] disabled:opacity-50">
                        </div>
                        <button type="submit" :disabled="isLoading || inputText.trim() === ''" class="inline-flex items-center justify-center rounded-full bg-[#00a884] text-white hover:bg-[#008f6f] disabled:opacity-50 disabled:cursor-not-allowed w-12 h-12 shrink-0 transition-colors shadow-sm">
                            <i class="fa-solid fa-paper-plane text-[15px]" x-show="!isLoading"></i>
                            <i class="fa-solid fa-circle-notch fa-spin text-[15px]" x-show="isLoading" style="display:none;"></i>
                        </button>
                    </form>
                    <p class="text-[11px] text-center text-slate-400 dark:text-[#8696a0] mt-1 font-medium">AI Ryaze dapat membuat kesalahan. Harap periksa kembali informasi penting.</p>
                </div>
            </div>
            
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('aiConsultationPage', () => ({
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
                if (this.token) {
                    this.loadHistory();
                }
                setTimeout(() => {
                    this.scrollToBottom();
                }, 100);
            },
            
            async loadHistory() {
                try {
                    const response = await fetch('/api/consultation/history?token=' + this.token);
                    if (response.ok) {
                        const data = await response.json();
                        if (data.history && data.history.length > 0) {
                            this.messages = [];
                            data.history.forEach(msg => {
                                this.messages.push({
                                    role: msg.role,
                                    content: msg.role === 'assistant' ? this.parseMarkdown(msg.content) : msg.content
                                });
                            });
                            setTimeout(() => this.scrollToBottom(), 100);
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
    @endpush

</x-public-layout>
