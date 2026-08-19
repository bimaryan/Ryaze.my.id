<x-public-layout
    title="Konsultasi AI Joki - Ryaze"
    description="Konsultasikan ide proyek atau sistem yang ingin Anda buat dengan AI Konsultan kami."
    body-class="bg-slate-50 font-sans antialiased text-slate-900 dark:bg-slate-950 dark:text-slate-100"
    og-image="{{ url('/og-image.png') }}"
    :links="[
        ['label' => 'Beranda', 'href' => url('/')],
        ['label' => 'Portofolio', 'href' => url('/#portfolio')],
        ['label' => 'Blog', 'href' => route('blog.index')],
    ]"
    :withNav="false"
    :withFooter="false">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body { font-family: 'Inter', sans-serif; }

        .ai-bg {
            background: radial-gradient(ellipse 80% 50% at 50% -20%, rgba(99,102,241,0.15), transparent),
                        radial-gradient(ellipse 60% 40% at 80% 100%, rgba(139,92,246,0.1), transparent);
        }
        .dark .ai-bg {
            background: radial-gradient(ellipse 80% 50% at 50% -20%, rgba(99,102,241,0.08), transparent),
                        radial-gradient(ellipse 60% 40% at 80% 100%, rgba(139,92,246,0.06), transparent);
        }

        .chat-bubble-user {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border-radius: 18px 18px 4px 18px;
        }
        .chat-bubble-ai {
            background: white;
            color: #1e293b;
            border-radius: 18px 18px 18px 4px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .dark .chat-bubble-ai {
            background: #1e293b;
            color: #e2e8f0;
            border-color: #334155;
        }

        .input-area {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }
        .dark .input-area {
            background: #1e293b;
            border-color: #334155;
        }

        .send-btn {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        .send-btn:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(99,102,241,0.4);
        }
        .send-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        #chat-container::-webkit-scrollbar { width: 4px; }
        #chat-container::-webkit-scrollbar-track { background: transparent; }
        #chat-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .dark #chat-container::-webkit-scrollbar-thumb { background: #334155; }

        .typing-dot {
            width: 8px; height: 8px;
            background: #94a3b8;
            border-radius: 50%;
            animation: typing-bounce 1.2s ease-in-out infinite;
        }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typing-bounce {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
            30% { transform: translateY(-6px); opacity: 1; }
        }

        .msg-appear {
            animation: msg-in 0.25s ease-out forwards;
        }
        @keyframes msg-in {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="h-screen w-full flex flex-col bg-slate-50 dark:bg-slate-950 ai-bg" x-data="aiConsultationPage()">

        <!-- Custom Confirm Modal -->
        <div x-show="confirmReset" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
            <div class="absolute inset-0 bg-black/50" @click="confirmReset = false"></div>
            <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl p-6 w-full max-w-sm border border-slate-200 dark:border-slate-700"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">
                <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-red-50 dark:bg-red-900/30 mx-auto mb-4">
                    <i class="fa-solid fa-rotate-right text-red-500 text-[22px]"></i>
                </div>
                <h3 class="text-[17px] font-semibold text-slate-900 dark:text-white text-center mb-1">Reset Percakapan?</h3>
                <p class="text-[14px] text-slate-500 dark:text-slate-400 text-center mb-6">Semua riwayat chat akan dihapus dan tidak bisa dikembalikan.</p>
                <div class="flex gap-3">
                    <button type="button" @click="confirmReset = false" class="flex-1 h-11 rounded-xl border border-slate-200 dark:border-slate-700 text-[14px] font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        Batal
                    </button>
                    <button type="button" @click="doReset()" class="flex-1 h-11 rounded-xl bg-red-500 hover:bg-red-600 text-white text-[14px] font-medium transition-colors">
                        Ya, Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Top Bar -->
        <header class="shrink-0 border-b border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md z-10">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
                <!-- Left: Back + Identity -->
                <div class="flex items-center gap-3">
                    <a href="{{ url('/') }}" class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" title="Kembali">
                        <i class="fa-solid fa-arrow-left text-[15px]"></i>
                    </a>
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white shrink-0 shadow-sm">
                                <i class="fa-solid fa-robot text-[15px]"></i>
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-400 border-2 border-white dark:border-slate-900 rounded-full"></span>
                        </div>
                        <div>
                            <h1 class="text-[15px] font-semibold text-slate-900 dark:text-white leading-tight">AI Konsultan Joki</h1>
                            <p class="text-[12px] text-emerald-500 font-medium leading-tight">Online &bull; Siap membantu</p>
                        </div>
                    </div>
                </div>
                <!-- Right: Actions -->
                <div class="flex items-center gap-1">
                    <button type="button" @click="confirmReset = true" class="h-9 px-3 rounded-xl text-[13px] font-medium text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex items-center gap-2" title="Reset chat">
                        <i class="fa-solid fa-rotate-right text-[13px]"></i>
                        <span class="hidden sm:inline">Reset</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Chat Area -->
        <main class="flex-1 overflow-hidden relative">
            <div class="h-full overflow-y-auto" id="chat-container">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 py-6 space-y-5">

                    <!-- Date badge -->
                    <div class="flex justify-center">
                        <span class="text-[11px] font-medium text-slate-400 dark:text-slate-500 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-1 rounded-full shadow-sm">
                            Hari ini &bull; {{ now()->format('d M Y') }}
                        </span>
                    </div>

                    <!-- Messages -->
                    <template x-for="(msg, index) in messages" :key="index">
                        <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start items-end gap-3'" class="msg-appear">
                            <!-- AI Avatar -->
                            <template x-if="msg.role === 'assistant'">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white shrink-0 shadow-sm mb-0.5">
                                    <i class="fa-solid fa-robot text-[12px]"></i>
                                </div>
                            </template>

                            <div :class="msg.role === 'user' ? 'chat-bubble-user max-w-[80%] sm:max-w-[65%] px-4 py-3' : 'chat-bubble-ai max-w-[85%] sm:max-w-[70%] px-4 py-3'" class="text-[15px] leading-relaxed break-words">
                                <div x-html="msg.content" class="prose prose-sm max-w-none dark:prose-invert prose-p:my-1 prose-li:my-0.5"></div>
                                <p class="text-[11px] mt-1.5 opacity-60 text-right" x-text="msg.time || ''"></p>
                            </div>
                        </div>
                    </template>

                    <!-- Loading / Typing Indicator -->
                    <div x-show="isLoading" class="flex justify-start items-end gap-3 msg-appear">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white shrink-0 shadow-sm">
                            <i class="fa-solid fa-robot text-[12px]"></i>
                        </div>
                        <div class="chat-bubble-ai px-4 py-3 flex items-center gap-1.5">
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <!-- Input Area -->
        <footer class="shrink-0 p-4 sm:p-5 border-t border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md">
            <div class="max-w-4xl mx-auto">
                <!-- Suggested prompts (only when 1 message) -->
                <div x-show="messages.length <= 1 && !isLoading" class="flex flex-wrap gap-2 mb-3">
                    <template x-for="prompt in suggestedPrompts" :key="prompt">
                        <button type="button" @click="inputText = prompt; sendMessage()" class="text-[13px] px-3 py-1.5 rounded-full border border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors" x-text="prompt"></button>
                    </template>
                </div>

                <form @submit.prevent="sendMessage" class="input-area flex items-end gap-3 p-3">
                    <textarea
                        x-model="inputText"
                        @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                        :disabled="isLoading"
                        placeholder="Tanyakan sesuatu tentang proyek Anda..."
                        class="flex-1 bg-transparent border-0 focus:ring-0 focus:outline-none resize-none text-[15px] text-slate-900 dark:text-slate-100 placeholder:text-slate-400 disabled:opacity-50 max-h-40 min-h-[24px] overflow-y-auto leading-relaxed py-1"
                        rows="1"
                        oninput="this.style.height='auto'; this.style.height=Math.min(this.scrollHeight,160)+'px'"></textarea>

                    <button type="submit" :disabled="isLoading || inputText.trim() === ''" class="send-btn flex items-center justify-center w-11 h-11 text-white shrink-0 self-end">
                        <i class="fa-solid fa-paper-plane text-[14px]" x-show="!isLoading" style="margin-left:1px;"></i>
                        <i class="fa-solid fa-circle-notch fa-spin text-[14px]" x-show="isLoading" style="display:none;"></i>
                    </button>
                </form>
                <p class="text-[11px] text-center text-slate-400 dark:text-slate-600 mt-2">
                    <i class="fa-solid fa-shield-halved mr-1"></i>AI dapat membuat kesalahan. Harap periksa informasi penting.
                </p>
            </div>
        </footer>

    </div>

    @push('scripts')
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('aiConsultationPage', () => ({
            isLoading: false,
            inputText: '',
            confirmReset: false,
            token: localStorage.getItem('ryaze_consultation_token') || null,
            messages: [
                {
                    role: 'assistant',
                    content: 'Halo! 👋 Saya <strong>AI Konsultan Joki</strong> dari Ryaze.<br><br>Saya siap membantu Anda merencanakan, mendiskusikan, dan mengembangkan ide proyek atau sistem. Ceritakan kebutuhan Anda!',
                    time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})
                }
            ],
            suggestedPrompts: [
                '💡 Saya ingin membuat website',
                '📱 Butuh aplikasi mobile',
                '🤖 Sistem otomatisasi',
                '📊 Aplikasi manajemen data',
            ],

            init() {
                if (this.token) {
                    this.loadHistory();
                }
                setTimeout(() => this.scrollToBottom(), 100);
            },

            doReset() {
                localStorage.removeItem('ryaze_consultation_token');
                location.reload();
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
                                    content: msg.role === 'assistant' ? this.parseMarkdown(msg.content) : msg.content,
                                    time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})
                                });
                            });
                            setTimeout(() => this.scrollToBottom(), 100);
                        }
                    }
                } catch (error) {
                    console.error("Gagal memuat riwayat:", error);
                }
            },

            scrollToBottom() {
                const container = document.getElementById('chat-container');
                if (container) container.scrollTop = container.scrollHeight;
            },

            parseMarkdown(text) {
                let html = text
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\*(.*?)\*/g, '<em>$1</em>')
                    .replace(/`(.*?)`/g, '<code class="bg-slate-100 dark:bg-slate-700 px-1.5 py-0.5 rounded text-[13px] font-mono">$1</code>')
                    .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" class="text-indigo-500 hover:text-indigo-600 underline" target="_blank">$1</a>')
                    .replace(/\n/g, '<br>');
                return html;
            },

            async sendMessage() {
                if (this.inputText.trim() === '' || this.isLoading) return;

                const userText = this.inputText;
                this.inputText = '';
                const textarea = document.querySelector('textarea');
                if (textarea) textarea.style.height = 'auto';

                this.messages.push({
                    role: 'user',
                    content: userText,
                    time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})
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
                        body: JSON.stringify({ message: userText, token: this.token })
                    });

                    const data = await response.json();

                    if (data.token) {
                        this.token = data.token;
                        localStorage.setItem('ryaze_consultation_token', this.token);
                    }

                    this.messages.push({
                        role: 'assistant',
                        content: response.ok
                            ? this.parseMarkdown(data.reply)
                            : 'Maaf, terjadi kesalahan: ' + (data.error || 'Server error'),
                        time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})
                    });

                } catch (error) {
                    this.messages.push({
                        role: 'assistant',
                        content: 'Gagal terhubung ke server. Silakan coba lagi.',
                        time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})
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
