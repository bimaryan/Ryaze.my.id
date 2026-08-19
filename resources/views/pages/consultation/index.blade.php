@extends('index')

@section('content')
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
            
            <div class="flex flex-col h-[75vh] min-h-[500px] bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-800 overflow-hidden relative">
                
                <!-- Header -->
                <div class="bg-indigo-600 px-6 py-5 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-white text-lg shadow-inner">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-white tracking-tight">AI Konsultan Joki</h1>
                            <p class="text-sm text-indigo-200 font-medium">Diskusikan kebutuhan sistem Anda di sini</p>
                        </div>
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="flex-1 overflow-y-auto p-6 bg-slate-50 dark:bg-slate-900/50 space-y-6" id="chat-container">
                    
                    <template x-for="(msg, index) in messages" :key="index">
                        <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                            <div :class="msg.role === 'user' ? 'bg-indigo-600 text-white rounded-2xl rounded-tr-sm' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-2xl rounded-tl-sm'" class="max-w-[85%] sm:max-w-[75%] px-5 py-4 shadow-sm text-base leading-relaxed" x-html="msg.content">
                            </div>
                        </div>
                    </template>

                    <!-- Loading Indicator -->
                    <div x-show="isLoading" class="flex justify-start">
                        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl rounded-tl-sm px-5 py-4 shadow-sm flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-400 animate-bounce"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 0.15s"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 0.3s"></div>
                        </div>
                    </div>
                </div>

                <!-- Input Area -->
                <div class="bg-white dark:bg-slate-900 px-6 py-5 border-t border-slate-200 dark:border-slate-800 shrink-0">
                    <form @submit.prevent="sendMessage" class="flex gap-4">
                        <input type="text" x-model="inputText" :disabled="isLoading" placeholder="Ketik pesan Anda di sini..." class="block w-full rounded-full border-0 py-3.5 px-6 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:bg-slate-800 dark:ring-slate-700 dark:text-white dark:placeholder:text-slate-500 sm:text-base sm:leading-6 disabled:opacity-50 transition-all">
                        <button type="submit" :disabled="isLoading || inputText.trim() === ''" class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-6 py-3.5 text-base font-bold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed w-14 h-14 shrink-0 transition-colors">
                            <i class="fa-solid fa-paper-plane text-lg" x-show="!isLoading"></i>
                            <i class="fa-solid fa-circle-notch fa-spin text-lg" x-show="isLoading" style="display:none;"></i>
                        </button>
                    </form>
                    <p class="text-xs text-center text-slate-400 dark:text-slate-500 mt-3 font-medium">AI Ryaze dapat membuat kesalahan. Harap periksa kembali informasi penting.</p>
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
@endsection
