<x-public-layout
    title="Konsultasi AI Joki - Ryaze"
    description="Konsultasikan ide proyek atau sistem yang ingin Anda buat dengan AI Konsultan kami."
    body-class="bg-slate-50 font-sans antialiased text-slate-900 dark:bg-slate-950 dark:text-slate-100 selection:bg-indigo-600 selection:text-white"
    og-image="{{ url('/og-image.png') }}"
    :links="[
        ['label' => 'Beranda', 'href' => url('/')],
        ['label' => 'Portofolio', 'href' => url('/#portfolio')],
        ['label' => 'Blog', 'href' => route('blog.index')],
    ]"
    :withNav="false"
    :withFooter="false">

    <style>
        .wa-bubble-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: -8px;
            width: 0;
            height: 0;
            border-top: 0px solid transparent;
            border-bottom: 12px solid transparent;
            border-right: 12px solid #ffffff;
        }
        .dark .wa-bubble-left::before {
            border-right-color: #202c33;
        }
        
        .wa-bubble-right::before {
            content: '';
            position: absolute;
            top: 0;
            right: -8px;
            width: 0;
            height: 0;
            border-top: 0px solid transparent;
            border-bottom: 12px solid transparent;
            border-left: 12px solid #d9fdd3;
        }
        .dark .wa-bubble-right::before {
            border-left-color: #005c4b;
        }
    </style>

    <!-- Background for Desktop WA Web (Green top band) -->
    <div class="hidden md:block fixed inset-0 z-0">
        <div class="h-[127px] bg-[#00a884] dark:bg-[#202c33] w-full"></div>
        <div class="h-[calc(100vh-127px)] bg-[#e3e1db] dark:bg-[#111b21] w-full"></div>
    </div>

    <!-- Main App Container -->
    <div class="relative z-10 h-screen w-full md:h-[calc(100vh-38px)] md:max-w-[1400px] md:mx-auto md:my-[19px] md:rounded-sm md:shadow-xl flex bg-white dark:bg-[#111b21] overflow-hidden" x-data="aiConsultationPage()">
        
        <!-- Sidebar (Desktop Only) -->
        <div class="hidden md:flex flex-col w-[30%] lg:w-[30%] border-r border-slate-200 dark:border-[#202c33] bg-white dark:bg-[#111b21]">
            <div class="bg-[#f0f2f5] dark:bg-[#202c33] px-4 py-3 flex items-center justify-between h-[59px] shrink-0">
                <a href="{{ url('/') }}" class="w-10 h-10 rounded-full bg-slate-300 flex items-center justify-center overflow-hidden cursor-pointer hover:ring-2 ring-indigo-500 transition-all" title="Kembali ke Beranda">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=random" alt="User" class="w-full h-full object-cover">
                </a>
                <div class="flex items-center gap-5 text-[#54656f] dark:text-[#aebac1]">
                    <i class="fa-solid fa-users text-[22px] cursor-pointer" title="Komunitas"></i>
                    <i class="fa-solid fa-message text-[20px] cursor-pointer" title="Pesan Baru"></i>
                    <i class="fa-solid fa-ellipsis-vertical text-[20px] cursor-pointer" title="Menu"></i>
                </div>
            </div>
            
            <div class="bg-white dark:bg-[#111b21] border-b border-slate-200 dark:border-[#202c33] p-2">
                <div class="bg-[#f0f2f5] dark:bg-[#202c33] rounded-lg flex items-center px-3 h-[35px]">
                    <i class="fa-solid fa-magnifying-glass text-[#54656f] dark:text-[#aebac1] text-[13px] mr-3"></i>
                    <input type="text" placeholder="Cari atau mulai chat baru" class="bg-transparent border-0 focus:ring-0 text-sm w-full p-0 text-[#111b21] dark:text-[#e9edef] placeholder:text-[#54656f] dark:placeholder:text-[#8696a0]">
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto">
                <div class="flex items-center px-3 py-3 hover:bg-[#f5f6f6] dark:hover:bg-[#202c33] cursor-pointer bg-[#f0f2f5] dark:bg-[#2a3942]">
                    <div class="w-[49px] h-[49px] rounded-full bg-slate-300 flex items-center justify-center overflow-hidden shrink-0">
                        <img src="https://ui-avatars.com/api/?name=AI+Joki&background=ffffff&color=008069" alt="Avatar" class="w-full h-full object-cover">
                    </div>
                    <div class="ml-3 flex-1 border-b border-slate-100 dark:border-[#202c33] pb-3 h-full flex flex-col justify-center">
                        <div class="flex justify-between items-center mb-0.5">
                            <h3 class="text-[17px] text-[#111b21] dark:text-[#e9edef]">AI Konsultan Joki</h3>
                            <span class="text-xs text-[#00a884]">Hari ini</span>
                        </div>
                        <p class="text-[14px] text-[#54656f] dark:text-[#8696a0] line-clamp-1">Sedang mengetik...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Area Wrapper (Mobile: full width, Desktop: 70%) -->
        <div class="flex-1 flex flex-col h-full bg-[#efeae2] dark:bg-[#0b141a] relative">
            
            <!-- Header -->
            <div class="bg-[#008069] md:bg-[#f0f2f5] dark:bg-[#202c33] px-2 md:px-4 py-2 md:py-3 flex items-center justify-between shrink-0 shadow-sm md:shadow-none z-10 w-full h-[59px]">
                <div class="flex items-center gap-1 md:gap-4 cursor-pointer flex-1">
                    <a href="{{ url('/') }}" class="text-white hover:bg-white/10 rounded-full p-2 pr-3 transition-colors flex items-center gap-1 md:hidden" title="Kembali ke Beranda">
                        <i class="fa-solid fa-arrow-left text-[20px]"></i>
                    </a>
                    <div class="w-10 h-10 rounded-full bg-slate-300 flex items-center justify-center overflow-hidden shrink-0 ml-1 md:ml-0">
                        <img src="https://ui-avatars.com/api/?name=AI+Joki&background=ffffff&color=008069" alt="Avatar" class="w-full h-full object-cover">
                    </div>
                    <div class="ml-1 md:ml-0 flex-1">
                        <h1 class="text-[17px] md:text-[16px] font-medium md:font-semibold text-white md:text-[#111b21] md:dark:text-[#e9edef] leading-tight line-clamp-1">AI Konsultan Joki</h1>
                        <p class="text-[13px] text-white/80 md:text-[#54656f] md:dark:text-[#8696a0] leading-tight">online</p>
                    </div>
                </div>
                <div class="flex items-center text-white md:text-[#54656f] md:dark:text-[#aebac1] mr-1 md:mr-0 shrink-0 gap-1 md:gap-5">
                    <button type="button" class="w-11 h-11 md:w-auto md:h-auto rounded-full hover:bg-white/10 md:hover:bg-transparent transition-colors flex items-center justify-center md:hidden" title="Video call"><i class="fa-solid fa-video text-[19px]"></i></button>
                    <button type="button" class="hidden md:flex w-11 h-11 md:w-auto md:h-auto rounded-full hover:bg-white/10 md:hover:bg-transparent transition-colors items-center justify-center" title="Search"><i class="fa-solid fa-magnifying-glass text-[19px]"></i></button>
                    <button type="button" class="w-11 h-11 md:w-auto md:h-auto rounded-full hover:bg-white/10 md:hover:bg-transparent transition-colors flex items-center justify-center md:hidden" title="Voice call"><i class="fa-solid fa-phone text-[19px]"></i></button>
                    <button type="button" class="w-11 h-11 md:w-auto md:h-auto rounded-full hover:bg-white/10 md:hover:bg-transparent transition-colors flex items-center justify-center" title="Menu"><i class="fa-solid fa-ellipsis-vertical text-[21px] md:text-[20px]"></i></button>
                </div>
            </div>

        <!-- Chat Area -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 space-y-4 relative z-10" id="chat-container">
            <!-- WA Background Pattern -->
            <div class="absolute inset-0 z-0 opacity-[0.06] dark:opacity-[0.03]" style="background-image: url('https://web.whatsapp.com/img/bg-chat-tile-dark_a4be512e7195b6b733d9110b408f075d.png'); background-repeat: repeat;"></div>
                    
                    <div class="relative z-10 space-y-3 flex flex-col">
                        <template x-for="(msg, index) in messages" :key="index">
                            <div :class="msg.role === 'user' ? 'flex justify-end pl-12 sm:pl-20' : 'flex justify-start pr-12 sm:pr-20'">
                                <div :class="msg.role === 'user' ? 'bg-[#d9fdd3] dark:bg-[#005c4b] text-[#111b21] dark:text-[#e9edef] rounded-[10px] rounded-tr-none wa-bubble-right relative' : 'bg-white dark:bg-[#202c33] text-[#111b21] dark:text-[#e9edef] rounded-[10px] rounded-tl-none wa-bubble-left relative'" class="px-2 py-1.5 shadow-[0_1px_0.5px_rgba(11,20,26,.13)] text-[15px] leading-relaxed break-words relative inline-block">
                                    <div x-html="msg.content" class="pb-3 pr-8 min-w-[80px]"></div>
                                    <span class="text-[10px] text-gray-500 dark:text-gray-300/80 absolute bottom-1 right-2 flex items-center gap-1">
                                        <span x-text="new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                                        <template x-if="msg.role === 'user'"><i class="fa-solid fa-check-double text-[#53bdeb] text-[10px]"></i></template>
                                    </span>
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
            <div class="px-4 py-3 bg-[#f0f2f5] dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex flex-col relative z-10 w-full shrink-0">
                <form @submit.prevent="sendMessage" class="flex gap-2 items-end max-w-5xl mx-auto w-full">
                    
                    <div class="flex-1 bg-white dark:bg-slate-800/60 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-end px-2 py-1.5 min-h-[44px]">
                        <button type="button" class="shrink-0 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 w-9 h-9 flex items-center justify-center text-xl transition mb-0.5">
                            <i class="fa-regular fa-face-smile"></i>
                        </button>
                        
                        <textarea x-model="inputText" @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()" :disabled="isLoading" placeholder="Ketik pesan" class="bg-transparent border-none px-2 py-1.5 text-[15px] focus:ring-0 focus:outline-none resize-none m-0 w-full text-slate-800 dark:text-slate-100 placeholder:text-slate-500 disabled:opacity-50" rows="1" style="min-height: 24px; max-height: 120px; overflow-y: auto;" oninput="this.style.height = '24px'; this.style.height = Math.min(this.scrollHeight, 120) + 'px'"></textarea>
                        
                        <button type="button" class="shrink-0 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 w-9 h-9 flex items-center justify-center text-xl transition mb-0.5">
                            <i class="fa-solid fa-paperclip"></i>
                        </button>
                    </div>
                    
                    <button type="submit" :disabled="isLoading" class="shrink-0 bg-[#00a884] hover:bg-[#029676] text-white w-12 h-12 rounded-full flex items-center justify-center transition shadow-sm mb-0.5 disabled:opacity-50">
                        <i class="fa-solid fa-microphone text-[19px]" x-show="inputText.trim() === '' && !isLoading"></i>
                        <i class="fa-solid fa-paper-plane text-[17px] mr-1" x-show="inputText.trim() !== '' && !isLoading"></i>
                        <i class="fa-solid fa-circle-notch fa-spin text-[17px]" x-show="isLoading" style="display:none;"></i>
                    </button>
                </form>
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
