<x-public-layout
    title="Konsultasi AI Joki - Ryaze"
    description="Konsultasikan ide proyek atau sistem yang ingin Anda buat dengan AI Konsultan kami."
    body-class="font-sans antialiased"
    og-image="{{ url('/og-image.png') }}"
    :links="[
        ['label' => 'Beranda', 'href' => url('/')],
        ['label' => 'Blog', 'href' => route('blog.index')],
    ]"
    :withNav="false"
    :withFooter="false">

    <style>
        /* ====== WhatsApp-style tokens (light + dark) ====== */
        .wa-root {
            --wa-bg: #EFEAE2;
            --wa-doodle: #DFD7C6;
            --wa-panel: #F0F2F5;
            --wa-pill: #FFFFFF;
            --wa-in: #FFFFFF;
            --wa-out: #D9FDD3;
            --wa-ink: #111B21;
            --wa-sub: #667781;
            --wa-icon: #54656F;
            --wa-icon-hv: rgba(134, 150, 160, 0.15);
            --wa-green: #00A884;
            --wa-green-hv: #008069;
            --wa-status: #008069;
            --wa-tick: #53BDEB;
            --wa-link: #027EB5;
            --wa-code: #F0F2F5;
            --wa-note-bg: rgba(253, 244, 213, 0.9);
            --wa-note-ink: #54656F;
            --wa-bubble-shadow: 0 1px .5px rgba(11, 20, 26, .13);
        }
        .dark .wa-root {
            --wa-bg: #0B141A;
            --wa-doodle: #1E2930;
            --wa-panel: #202C33;
            --wa-pill: #2A3942;
            --wa-in: #202C33;
            --wa-out: #005C4B;
            --wa-ink: #E9EDEF;
            --wa-sub: #8696A0;
            --wa-icon: #7296B1;
            --wa-icon-hv: rgba(174, 186, 193, 0.1);
            --wa-green: #00A884;
            --wa-green-hv: #06CF9C;
            --wa-status: #06CF9C;
            --wa-tick: #53BDEB;
            --wa-link: #53BDEB;
            --wa-code: #2A3942;
            --wa-note-bg: rgba(24, 34, 41, 0.9);
            --wa-note-ink: #FFD279;
            --wa-bubble-shadow: 0 1px .5px rgba(0, 0, 0, .24);
        }

        .wa-root { font-family: 'Inter', 'Segoe UI', Helvetica, Arial, sans-serif; }

        /* Doodle wallpaper (SVG pattern) */
        .wa-chat {
            background-color: var(--wa-bg);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='480' height='480' viewBox='0 0 480 480'%3E%3Cg fill='none' stroke='%23DFD7C6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='64' cy='72' r='14'/%3E%3Cpath d='M58 76q6 7 12 0'/%3E%3Cpath d='M58 67h.01M70 67h.01'/%3E%3Cpath d='M154 54c-5-9-19-4-13 7l13 11 13-11c6-11-8-16-13-7z'/%3E%3Cpath d='M256 40l4 9 10 1-7 7 2 10-9-5-9 5 2-10-7-7 10-1z'/%3E%3Cpath d='M372 88a10 10 0 1 1-10 10 7 7 0 1 1 7-7 4 4 0 1 1-4 4'/%3E%3Cpath d='M312 200v-24l16-5v24'/%3E%3Ccircle cx='308' cy='200' r='5'/%3E%3Ccircle cx='324' cy='195' r='5'/%3E%3Cpath d='M96 216a11 11 0 0 1 2-22 15 15 0 0 1 29-5 12 12 0 0 1 5 27z'/%3E%3Cpath d='M420 168l-16 26h12l-10 24 22-30h-12l10-20z'/%3E%3Crect x='192' y='296' width='40' height='28' rx='4'/%3E%3Cpath d='M192 300l20 14 20-14'/%3E%3Cpath d='M52 316a10 10 0 0 1 10-10h28a10 10 0 0 1 10 10v12a10 10 0 0 1-10 10h-14l-12 10v-10h-2a10 10 0 0 1-10-10z'/%3E%3Cpath d='M72 328h.01M82 328h.01M92 328h.01'/%3E%3Ccircle cx='400' cy='330' r='14'/%3E%3Cpath d='M400 316v28M386 330h28'/%3E%3Cpath d='M164 446q-30-4-32 24 30 2 32-24z'/%3E%3Cpath d='M448 428l11 13-11 13-11-13z'/%3E%3Cpath d='M276 446a12 12 0 0 1-12-12 12 12 0 0 1 24 0c0 10-12 18-28 18'/%3E%3Cpath d='M64 200l-4 8 8 3-5 8'/%3E%3C/g%3E%3C/svg%3E");
        }
        .dark .wa-chat {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='480' height='480' viewBox='0 0 480 480'%3E%3Cg fill='none' stroke='%231E2930' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='64' cy='72' r='14'/%3E%3Cpath d='M58 76q6 7 12 0'/%3E%3Cpath d='M58 67h.01M70 67h.01'/%3E%3Cpath d='M154 54c-5-9-19-4-13 7l13 11 13-11c6-11-8-16-13-7z'/%3E%3Cpath d='M256 40l4 9 10 1-7 7 2 10-9-5-9 5 2-10-7-7 10-1z'/%3E%3Cpath d='M372 88a10 10 0 1 1-10 10 7 7 0 1 1 7-7 4 4 0 1 1-4 4'/%3E%3Cpath d='M312 200v-24l16-5v24'/%3E%3Ccircle cx='308' cy='200' r='5'/%3E%3Ccircle cx='324' cy='195' r='5'/%3E%3Cpath d='M96 216a11 11 0 0 1 2-22 15 15 0 0 1 29-5 12 12 0 0 1 5 27z'/%3E%3Cpath d='M420 168l-16 26h12l-10 24 22-30h-12l10-20z'/%3E%3Crect x='192' y='296' width='40' height='28' rx='4'/%3E%3Cpath d='M192 300l20 14 20-14'/%3E%3Cpath d='M52 316a10 10 0 0 1 10-10h28a10 10 0 0 1 10 10v12a10 10 0 0 1-10 10h-14l-12 10v-10h-2a10 10 0 0 1-10-10z'/%3E%3Cpath d='M72 328h.01M82 328h.01M92 328h.01'/%3E%3Ccircle cx='400' cy='330' r='14'/%3E%3Cpath d='M400 316v28M386 330h28'/%3E%3Cpath d='M164 446q-30-4-32 24 30 2 32-24z'/%3E%3Cpath d='M448 428l11 13-11 13-11-13z'/%3E%3Cpath d='M276 446a12 12 0 0 1-12-12 12 12 0 0 1 24 0c0 10-12 18-28 18'/%3E%3Cpath d='M64 200l-4 8 8 3-5 8'/%3E%3C/g%3E%3C/svg%3E");
        }

        /* Bubbles — bayangan halus khas bubble WhatsApp, bagian identitasnya */
            .chat-bubble-in,
            .chat-bubble-out {
                position: relative;
                border-radius: 8px;
                box-shadow: var(--wa-bubble-shadow); /* taste-skill-ignore */
                color: var(--wa-ink);
            }
        .chat-bubble-in { background: var(--wa-in); }
        .chat-bubble-out { background: var(--wa-out); }
        .chat-bubble-in { border-top-left-radius: 0; }
        .chat-bubble-out { border-top-right-radius: 0; }

        .chat-bubble-in::before,
        .chat-bubble-out::before {
            content: "";
            position: absolute;
            top: 0;
            width: 0;
            height: 0;
        }
        .chat-bubble-in::before {
            left: -8px;
            border-top: 12px solid var(--wa-in);
            border-left: 8px solid transparent;
        }
        .chat-bubble-out::before {
            right: -8px;
            border-top: 12px solid var(--wa-out);
            border-right: 8px solid transparent;
        }

        .chat-msg a { color: var(--wa-link) !important; }
        .wa-code { background: var(--wa-code); }

        /* Input */
        .wa-pill {
            background: var(--wa-pill);
            border-radius: 24px;
            transition: border-radius .12s ease;
        }
        /* Override rule global app.css (.dark textarea:focus bg #12233A) — composer harus tetap bening dalam pill */
        .wa-root .wa-pill textarea:focus,
        .wa-root .wa-pill textarea:focus-visible {
            background-color: transparent !important;
        }
        .wa-send {
            background: var(--wa-green);
            color: #fff;
            transition: background .15s ease, transform .06s ease;
        }
        .wa-send:hover:not(:disabled) { background: var(--wa-green-hv); }
        .wa-send:active:not(:disabled) { transform: scale(.94); }
        .wa-send:disabled { opacity: .55; cursor: not-allowed; }

        .wa-iconbtn {
            color: var(--wa-icon);
            transition: background .15s ease;
        }
        .wa-iconbtn:hover { background: var(--wa-icon-hv); }

        .wa-quick {
            background: var(--wa-pill);
            color: var(--wa-status);
            transition: background .15s ease, color .15s ease;
        }
        .wa-quick:hover { background: var(--wa-icon-hv); }

        /* Emoji picker */
        .wa-emoji {
            box-shadow: 0 10px 28px rgba(11, 20, 26, .22); /* taste-skill-ignore: overlay/dialog */
        }
        .wa-emoji-tab-active {
            color: var(--wa-status);
            border-bottom: 2px solid var(--wa-green);
        }
        .wa-emoji-btn:hover, .wa-emoji-grid button:hover {
            background: var(--wa-icon-hv);
        }
        .wa-emoji-grid::-webkit-scrollbar { width: 6px; }
        .wa-emoji-grid::-webkit-scrollbar-thumb { background: rgba(134, 150, 160, .35); border-radius: 3px; }
        [x-cloak] { display: none !important; }

        #chat-container::-webkit-scrollbar { width: 5px; }
        #chat-container::-webkit-scrollbar-track { background: transparent; }
        #chat-container::-webkit-scrollbar-thumb { background: rgba(84, 101, 111, .35); border-radius: 4px; }
        .dark #chat-container::-webkit-scrollbar-thumb { background: rgba(134, 150, 160, .25); }

        .typing-dot {
            width: 7px; height: 7px;
            background: var(--wa-sub);
            border-radius: 50%;
            animation: typing-bounce 1.2s ease-in-out infinite;
        }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typing-bounce {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.35; }
            30% { transform: translateY(-5px); opacity: 1; }
        }

        .msg-appear {
            animation: msg-in 0.22s ease-out forwards;
        }
        @keyframes msg-in {
            from { opacity: 0; transform: translateY(6px) scale(.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        @media (prefers-reduced-motion: reduce) {
            .msg-appear, .typing-dot { animation: none; }
        }

        textarea:focus { outline: none; }
    </style>

    <div class="h-screen w-full flex flex-col wa-root" x-data="aiConsultationPage()" @keydown.escape.window="emojiOpen = false">

        <!-- Confirm Reset Modal -->
        <x-ui.confirm-modal
            model="confirmReset"
            title="Hapus Percakapan?"
            description="Seluruh riwayat chat akan dihapus dan tidak bisa dikembalikan."
            icon="fa-solid fa-trash-can"
            icon-color="text-red-500"
            icon-bg="bg-red-50 dark:bg-red-900/30"
            cancel-label="Batal"
            confirm-label="Hapus"
            confirm-class="bg-red-500 hover:bg-red-600 text-white"
            on-cancel="confirmReset = false"
            on-confirm="doReset()"
        />

        <!-- Header ala WhatsApp -->
        <header class="shrink-0 z-10 bg-[color:var(--wa-panel)] border-b border-black/10 dark:border-white/5">
            <div class="h-[60px] px-2 sm:px-4 flex items-center gap-1 sm:gap-3">
                <a href="{{ url('/') }}" class="wa-iconbtn w-10 h-10 rounded-full flex items-center justify-center" title="Kembali ke beranda" aria-label="Kembali ke beranda">
                    <i class="fa-solid fa-arrow-left text-[17px]"></i>
                </a>
                <!-- Identitas kontak -->
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="relative shrink-0">
                        <div class="w-10 h-10 rounded-full bg-[color:var(--wa-green)] flex items-center justify-center text-white">
                            <i class="fa-solid fa-robot text-[16px]"></i>
                        </div>
                        <span class="absolute -bottom-px -right-px w-3 h-3 bg-[#25D366] border-2 border-[color:var(--wa-panel)] rounded-full"></span>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-[16px] font-medium text-[color:var(--wa-ink)] leading-tight truncate flex items-center gap-1.5">
                            AI Konsultan Joki
                            <svg class="shrink-0" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#00A884" stroke-width="2.5" aria-label="Terverifikasi"><path d="M23 10.5 21 6l-4.5-2L12 2 7.5 4 3 6l-2 4.5.5 5L4 20l5 2 3-1 3 1 5-2 2.5-4.5.5-5Z" stroke-linejoin="round"/><path d="m8 12 3 3 5-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </h1>
                        <p class="text-[13px] text-[color:var(--wa-status)] font-medium leading-tight truncate">daring &bull; biasanya membalas cepat</p>
                    </div>
                </div>
                <!-- Aksi -->
                <button type="button" @click="confirmReset = true" class="wa-iconbtn w-10 h-10 rounded-full flex items-center justify-center" title="Hapus percakapan" aria-label="Hapus percakapan">
                    <i class="fa-solid fa-trash-can text-[17px]"></i>
                </button>
            </div>
        </header>

        <!-- Area chat -->
        <main class="flex-1 overflow-hidden relative wa-chat">
            <div class="h-full overflow-y-auto" id="chat-container">
                <div class="max-w-4xl mx-auto px-4 sm:px-10 py-3 space-y-2.5">

                    <!-- Catatan enkripsi ala WhatsApp -->
                    <div class="flex justify-center">
                        <p class="max-w-[92%] text-center text-[12.5px] leading-relaxed rounded-lg px-3 py-1.5 text-[color:var(--wa-note-ink)] bg-[color:var(--wa-note-bg)]">
                            <i class="fa-solid fa-lock mr-1"></i>Percakapan ini dikenakan enkripsi penuh &bull; AI dapat membuat kesalahan. Periksa informasi penting.
                        </p>
                    </div>

                    <!-- Badge tanggal -->
                    <div class="flex justify-center pt-1">
                        <span class="text-[12px] font-medium text-[color:var(--wa-sub)] bg-[color:var(--wa-panel)] px-2.5 py-1 rounded-lg tracking-wide">
                            HARI INI &bull; {{ now()->format('d M Y') }}
                        </span>
                    </div>

                    <!-- Pesan -->
                    <template x-for="(msg, index) in messages" :key="index">
                        <div class="msg-appear flex" :class="[msg.role === 'user' ? 'justify-end pl-8 sm:pl-16 ' + (index>0 && messages[index-1].role!==msg.role ? 'mt-2.5' : '') : 'justify-start pr-8 sm:pr-16 ' + (index>0 && messages[index-1].role!==msg.role ? 'mt-2.5' : '')]">
                            <!-- USER (bubble hijau, kanan) -->
                            <template x-if="msg.role === 'user'">
                                <div class="chat-bubble-out max-w-[85%] sm:max-w-[70%] px-3 pb-[6px] pt-[7px]">
                                    <div class="prose prose-sm max-w-none prose-p:my-1 prose-li:my-0.5 text-[14.5px] leading-[21px] break-words [white-space:pre-line]" x-html="msg.content"></div>
                                    <div class="flex items-center justify-end gap-[5px] mt-0.5 text-[11px] leading-[15px] text-[color:var(--wa-sub)] select-none">
                                        <span x-text="msg.time"></span>
                                        <i class="fa-solid fa-check-double text-[11px] text-[color:var(--wa-tick)]" aria-label="Sudah dibaca"></i>
                                    </div>
                                </div>
                            </template>
                            <!-- AI (bubble putih, kiri) -->
                            <template x-if="msg.role === 'assistant'">
                                <div class="chat-bubble-in max-w-[92%] sm:max-w-[75%] px-3 pb-[6px] pt-[7px]">
                                    <div class="prose prose-sm max-w-none dark:prose-invert prose-p:my-1 prose-li:my-0.5 text-[14.5px] leading-[21px] break-words [white-space:pre-line]" x-html="msg.content"></div>
                                    <div class="flex justify-end mt-0.5 text-[11px] leading-[15px] text-[color:var(--wa-sub)] select-none" x-text="msg.time"></div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Typing indicator -->
                    <div x-show="isLoading" class="flex justify-start pr-8 sm:pr-16 msg-appear">
                        <div class="chat-bubble-in pl-3 pr-4 py-2.5 flex items-center gap-1.5">
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                        </div>
                    </div>

                    <div class="h-1"></div>
                </div>
            </div>
        </main>

        <!-- Composer ala WhatsApp -->
        <footer class="shrink-0 bg-[color:var(--wa-panel)] px-2.5 sm:px-4 pt-2 pb-2.5">
            <div class="max-w-4xl mx-auto">
                <!-- Quick replies -->
                <div x-show="messages.length <= 1 && !isLoading" class="flex flex-wrap gap-2 mb-2">
                    <template x-for="prompt in suggestedPrompts" :key="prompt">
                        <button type="button" @click="inputText = prompt; sendMessage()" class="wa-quick text-[13px] font-medium pl-3 pr-3.5 py-2 rounded-full flex items-center gap-1.5" x-text="prompt"></button>
                    </template>
                </div>

                <form @submit.prevent="sendMessage" @click.outside="emojiOpen = false" class="relative flex items-end gap-2">

                    <!-- Emoji picker ala WhatsApp -->
                    <div x-show="emojiOpen" x-cloak
                         class="wa-emoji absolute bottom-[calc(100%+10px)] left-0 z-40 w-[300px] sm:w-[340px] rounded-2xl overflow-hidden bg-[color:var(--wa-pill)]"
                         role="dialog" aria-label="Pilih emoji">
                        <div class="flex items-stretch px-1 pt-1 border-b border-black/10 dark:border-white/5">
                            <template x-for="(cat, ci) in emojiCats" :key="cat.label">
                                <button type="button"
                                        @click="emojiCat = ci"
                                        :title="cat.label" :aria-label="'Kategori ' + cat.label"
                                        class="flex-1 flex items-center justify-center py-2 rounded-t-md text-[19px] leading-none transition-colors"
                                        :class="emojiCat === ci ? 'wa-emoji-tab-active' : 'text-[color:var(--wa-icon)] hover:bg-[color:var(--wa-icon-hv)]'"
                                        x-text="cat.icon"></button>
                            </template>
                        </div>
                        <div class="wa-emoji-grid grid grid-cols-8 gap-0.5 p-2 max-h-[228px] overflow-y-auto">
                            <template x-for="(em, i) in currentEmojis" :key="emojiCat + '-' + i">
                                <button type="button" @click="insertAtCaret(em)" x-text="em"
                                        class="wa-emoji-btn h-9 rounded-lg text-[20px] leading-none active:scale-90 transition-transform"
                                        :aria-label="em"></button>
                            </template>
                        </div>
                    </div>

                    <div class="wa-pill flex-1 flex items-center gap-1.5 pl-2 pr-3 py-1.5 min-h-[44px]">
                        <!-- Tombol emoji (buka/tutup panel) -->
                        <button type="button" @click="emojiOpen = !emojiOpen"
                                class="w-10 h-9 rounded-full flex items-center justify-center shrink-0 transition-colors hover:bg-[color:var(--wa-icon-hv)]"
                                :class="emojiOpen ? 'text-[color:var(--wa-green)]' : 'text-[color:var(--wa-icon)]'"
                                aria-label="Pilih emoji" :aria-expanded="emojiOpen ? 'true' : 'false'" title="Emoji">
                            <i class="fa-solid fa-face-smile text-[22px]"></i>
                        </button>
                        <textarea
                            x-model="inputText"
                            x-ref="composer"
                            @keydown.enter.prevent="onEnter($event)"
                            :disabled="isLoading"
                            placeholder="Ketik pesan"
                            class="flex-1 bg-transparent border-0 resize-none self-center text-[15px] leading-[21px] text-[color:var(--wa-ink)] placeholder:text-[color:var(--wa-sub)] disabled:opacity-50 max-h-40 overflow-y-auto py-1"
                            rows="1"
                            oninput="this.style.height='auto'; this.style.height=Math.min(this.scrollHeight,160)+'px'"></textarea>
                    </div>

                    <button type="submit" :disabled="isLoading || inputText.trim() === ''" class="wa-send flex items-center justify-center w-[44px] h-[44px] rounded-full shrink-0">
                        <i class="fa-solid fa-paper-plane text-[15px]" x-show="!isLoading" x-cloak style="margin-left:1px;"></i>
                        <i class="fa-solid fa-circle-notch fa-spin text-[15px]" x-show="isLoading" style="display:none;"></i>
                    </button>
                </form>
            </div>
        </footer>

    </div>

    @push('scripts')
    <script nonce="{{ csp_nonce() }}">
    document.addEventListener('alpine:init', () => {
        Alpine.data('aiConsultationPage', () => ({
            isLoading: false,
            inputText: '',
            confirmReset: false,
            emojiOpen: false,
            emojiCat: 0,
            emojiCats: [
                { label: 'Emoji & Orang', icon: '😊'/* taste-skill-ignore: isi data panel emoji */, items: ['😀','😄','😊','🥰','😍','😘','😜','🤪','😋','😎','🤩','🥳','😏','😉','🙃','😶','😔','😢','😭','😤','😡','🤔','🤗','🤫','🤭','😬','🙄','😴','🥱','😷','🤒','🥴'] },
                { label: 'Gestur & Orang', icon: '👍'/* taste-skill-ignore: isi data panel emoji */, items: ['👍','👎','👌','✌️','🤞','🤟','🤘','🤙','👏','🙌','🤝','🙏','💪','👋','✊','👊','👆','👇','👈','👉','🖐️','✋','🤚','☝️','🤳','🤌','👀','🧠'] },
                { label: 'Hewan & Alam', icon: '🌿'/* taste-skill-ignore: isi data panel emoji */, items: ['🐶','🐱','🐭','🐹','🐰','🦊','🐻','🐼','🐨','🐯','🦁','🐮','🐷','🐸','🐵','🦄','🐔','🐧','🐝','🐢','🌸','🌹','🌻','🌴','🌵','🍀','🌈','⭐','🌙','☀️','🌧','❄️','🔥','💧'] },
                { label: 'Makanan & Minuman', icon: '🍔'/* taste-skill-ignore: isi data panel emoji */, items: ['🍎','🍌','🍉','🍇','🍓','🍒','🍑','🍍','🥭','🍋','🥑','🍞','🧀','🍔','🍟','🍕','🍳','🍜','🍣','🍩','🍪','🎂','🍫','☕','🍵','🥤','🧋','🍺'] },
                { label: 'Aktivitas', icon: '⚽'/* taste-skill-ignore: isi data panel emoji */, items: ['⚽','🏀','🎾','🏓','🥊','🎮','🎧','🎤','🎸','🚴','🏊','🚗','🚙','🛵','🏍️','✈️','🚀','🛒','🎁','💰','💳','🏆','🎯','🎲'] },
                { label: 'Objek & Simbol', icon: '❤️'/* taste-skill-ignore: isi data panel emoji */, items: ['❤️','🧡','💛','💚','💙','💜','🖤','🤍','💯','✅','❌','❗','❓','⚡','🎉','📱','💻','🖥️','📧','⏰','📌','📅','🔒','🔑','🎓','🏠','📦','🕐','🆗','🔔','📎','🗂️'] },
            ],

            get currentEmojis() {
                return this.emojiCats[this.emojiCat]?.items || this.emojiCats[0].items;
            },
            token: localStorage.getItem('ryaze_consultation_token') || null,
            messages: [
                {
                    role: 'assistant',
                    content: 'Halo! Saya <strong>AI Konsultan Joki</strong> dari Ryaze.<br><br>Saya siap membantu Anda merencanakan, mendiskusikan, dan mengembangkan ide proyek atau sistem. Ceritakan kebutuhan Anda!',
                    time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})
                }
            ],
            suggestedPrompts: [
                'Saya ingin membuat website',
                'Butuh aplikasi mobile',
                'Sistem otomatisasi',
                'Aplikasi manajemen data',
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

            insertAtCaret(text) {
                const el = this.$refs.composer;
                if (!el) { this.inputText += text; return; }
                const start = el.selectionStart ?? this.inputText.length;
                const end = el.selectionEnd ?? start;
                this.inputText = this.inputText.slice(0, start) + text + this.inputText.slice(end);
                const caret = start + text.length;
                this.$nextTick(() => {
                    el.focus();
                    el.setSelectionRange(caret, caret);
                    el.style.height = 'auto';
                    el.style.height = Math.min(el.scrollHeight, 160) + 'px';
                });
            },

            // Shift+Enter (sendiri atau bersama Ctrl) = baris baru; Enter / Ctrl+Enter = kirim
            onEnter(e) {
                if (e.shiftKey) {
                    e.preventDefault();
                    this.insertAtCaret('\n');
                    return;
                }
                e.preventDefault();
                this.sendMessage();
            },

            scrollToBottom() {
                const container = document.getElementById('chat-container');
                if (container) container.scrollTop = container.scrollHeight;
            },

            parseMarkdown(text) {
                let html = text
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\*(.*?)\*/g, '<em>$1</em>')
                    .replace(/`(.*?)`/g, '<code class="wa-code px-1.5 py-0.5 rounded text-[13px] font-mono">$1</code>')
                    .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" class="underline font-medium" target="_blank">$1</a>')
                    .replace(/\n/g, '<br>');
                return html;
            },

            async sendMessage() {
                if (this.inputText.trim() === '' || this.isLoading) return;

                this.emojiOpen = false;
                this.$nextTick(() => this.$refs.composer?.focus());

                const userText = this.inputText;
                this.inputText = '';
                if (this.$refs.composer) this.$refs.composer.style.height = 'auto';

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
