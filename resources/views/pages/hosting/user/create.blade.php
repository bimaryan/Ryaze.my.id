@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- ── USER HOSTING – Deploy Proyek Baru ───────────────────────── --}}
        <x-ui.page-header
            title="Deploy Proyek Baru"
            subtitle="Impor repository Git Anda atau mulai dengan template siap pakai."
            icon="fa-plus"
            iconColor="emerald">
            <x-slot:actions>
                <a href="{{ route('user_hosting.dashboard') }}"
                    class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="mx-auto mt-6">
            <form action="{{ route('user_hosting.store') }}" method="POST" class="space-y-6">
                @csrf


                {{-- ── STEP 1: Metode Deploy ── --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2 text-sm">
                        <span class="w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                        Pilih Metode Deploy
                    </h3>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <label class="relative cursor-pointer flex-1 group">
                            <input type="radio" name="source_type" value="repo" id="source_repo" class="peer hidden" checked>
                            <div class="h-full px-5 py-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 transition-all flex items-center gap-4">
                                <div class="w-11 h-11 bg-slate-900 rounded-xl flex items-center justify-center shrink-0">
                                    <i class="fa-brands fa-github text-xl text-white"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 text-sm">Git Repository</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Clone dari repo Git Anda sendiri</p>
                                </div>
                                <div class="ml-auto shrink-0 w-5 h-5 border-2 border-slate-300 rounded-full flex items-center justify-center transition-colors group-has-[:checked]:border-indigo-600 group-has-[:checked]:bg-indigo-600">
                                    <div class="w-2 h-2 bg-white rounded-full opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></div>
                                </div>
                            </div>
                        </label>
                        <label class="relative cursor-pointer flex-1 group">
                            <input type="radio" name="source_type" value="template" id="source_template" class="peer hidden">
                            <div class="h-full px-5 py-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 transition-all flex items-center gap-4">
                                <div class="w-11 h-11 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-wand-magic-sparkles text-xl text-white"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                        Gunakan Template
                                        <span class="bg-indigo-100 text-indigo-700 text-[9px] font-bold px-1.5 py-0.5 rounded-md uppercase">New</span>
                                    </p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Mulai cepat dengan starter code siap pakai</p>
                                </div>
                                <div class="ml-auto shrink-0 w-5 h-5 border-2 border-slate-300 rounded-full flex items-center justify-center transition-colors group-has-[:checked]:border-indigo-600 group-has-[:checked]:bg-indigo-600">
                                    <div class="w-2 h-2 bg-white rounded-full opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- ── STEP 2a: Sumber Repository (jika pilih repo) ── --}}
                <div id="section_repo" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2 text-sm">
                        <span class="w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                        Sumber Repository
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">URL Git Repository <span class="text-rose-500">*</span></label>
                            <input type="url" name="repo_source" id="input_repo_source"
                                placeholder="https://github.com/username/my-project"
                                class="transition-all w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Branch</label>
                            <input type="text" name="branch" id="input_branch" value="main"
                                class="transition-all w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            <p class="text-[11px] text-slate-500 mt-1">Cabang Git yang akan di-build (misal: main, master, atau production).</p>
                        </div>
                    </div>
                </div>

                {{-- ── STEP 2b: Pilih Template (jika pilih template) ── --}}
                <div id="section_template" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hidden">
                    <h3 class="font-bold text-slate-800 mb-1 flex items-center gap-2 text-sm">
                        <span class="w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                        Pilih Starter Template
                    </h3>
                    <p class="text-xs text-slate-500 mb-5 ml-8">Sistem akan langsung generate file starter — tidak perlu GitHub, tidak perlu konfigurasi apa pun.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                        {{-- HTML Static --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="template_key" value="html_landing" class="peer hidden">
                            <div class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-brands fa-html5 text-xl text-orange-500"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">HTML Landing Page</p>
                                        <span class="text-[10px] bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-medium">HTML</span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">Template landing page modern dengan HTML, CSS & JS. Tidak butuh build step, langsung live.</p>
                                <div class="mt-3 flex items-center gap-1.5 text-[11px] text-slate-400">
                                    <i class="fa-solid fa-bolt"></i> Instant deploy
                                </div>
                            </div>
                        </label>

                        {{-- Tailwind CSS --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="template_key" value="tailwind_starter" class="peer hidden">
                            <div class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-wind text-xl text-cyan-500"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">Tailwind CSS</p>
                                        <span class="text-[10px] bg-cyan-100 text-cyan-700 px-2 py-0.5 rounded-full font-medium">HTML & CSS</span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">Starter template UI modern dengan Tailwind CSS CDN. Langsung ngoding utility class tanpa build step.</p>
                                <div class="mt-3 flex items-center gap-1.5 text-[11px] text-slate-400">
                                    <i class="fa-solid fa-bolt"></i> Instant deploy
                                </div>
                            </div>
                        </label>



                        {{-- PHP Native --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="template_key" value="php_basic" class="peer hidden">
                            <div class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-brands fa-php text-xl text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">PHP Basic App</p>
                                        <span class="text-[10px] bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-medium">PHP Native</span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">Starter PHP murni dengan struktur MVC sederhana, koneksi database, dan router dasar.</p>
                                <div class="mt-3 flex items-center gap-1.5 text-[11px] text-slate-400">
                                    <i class="fa-solid fa-bolt"></i> Instant deploy
                                </div>
                            </div>
                        </label>

                        {{-- WordPress --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="template_key" value="wordpress" class="peer hidden">
                            <div class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-brands fa-wordpress text-xl text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">WordPress CMS</p>
                                        <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">PHP & MySQL</span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">Auto-install WordPress terbaru lengkap dengan database siap pakai.</p>
                                <div class="mt-3 flex items-center gap-1.5 text-[11px] text-slate-400">
                                    <i class="fa-solid fa-gear fa-spin" style="animation-duration:3s"></i> Auto build
                                </div>
                            </div>
                        </label>

                        {{-- Laravel 13 --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="template_key" value="laravel_starter_13" class="peer hidden">
                            <div class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-brands fa-laravel text-xl text-red-500"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">Laravel 13</p>
                                        <span class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-medium">Laravel</span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">Laravel 13 fresh install resmi, siap pakai sebagai backend API atau web app.</p>
                                <div class="mt-3 flex items-center gap-1.5 text-[11px] text-slate-400">
                                    <i class="fa-solid fa-gear fa-spin" style="animation-duration:3s"></i> Auto build
                                </div>
                            </div>
                        </label>

                        {{-- Laravel 12 --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="template_key" value="laravel_starter_12" class="peer hidden">
                            <div class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-brands fa-laravel text-xl text-red-500"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">Laravel 12</p>
                                        <span class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-medium">Laravel</span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">Laravel 12 fresh install resmi, stabil dan siap untuk produksi.</p>
                                <div class="mt-3 flex items-center gap-1.5 text-[11px] text-slate-400">
                                    <i class="fa-solid fa-gear fa-spin" style="animation-duration:3s"></i> Auto build
                                </div>
                            </div>
                        </label>

                        {{-- Laravel 11 --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="template_key" value="laravel_starter_11" class="peer hidden">
                            <div class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-brands fa-laravel text-xl text-red-500"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">Laravel 11</p>
                                        <span class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-medium">Laravel</span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">Laravel 11 fresh install, struktur sederhana dan mudah dipelajari.</p>
                                <div class="mt-3 flex items-center gap-1.5 text-[11px] text-slate-400">
                                    <i class="fa-solid fa-gear fa-spin" style="animation-duration:3s"></i> Auto build
                                </div>
                            </div>
                        </label>

                        {{-- Laravel 10 --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="template_key" value="laravel_starter_10" class="peer hidden">
                            <div class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-brands fa-laravel text-xl text-red-500"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">Laravel 10</p>
                                        <span class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-medium">Laravel</span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">Laravel 10 LTS, kompatibel dengan banyak package dan dokumentasi luas.</p>
                                <div class="mt-3 flex items-center gap-1.5 text-[11px] text-slate-400">
                                    <i class="fa-solid fa-gear fa-spin" style="animation-duration:3s"></i> Auto build
                                </div>
                            </div>
                        </label>

                        {{-- React --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="template_key" value="react_starter" class="peer hidden">
                            <div class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-sky-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-brands fa-react text-xl text-sky-500"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">React + Vite</p>
                                        <span class="text-[10px] bg-sky-100 text-sky-700 px-2 py-0.5 rounded-full font-medium">React JS</span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">React dengan Vite bundler, TailwindCSS, dan React Router. Starter app siap dikembangkan.</p>
                                <div class="mt-3 flex items-center gap-1.5 text-[11px] text-slate-400">
                                    <i class="fa-solid fa-gear fa-spin" style="animation-duration:3s"></i> Auto build
                                </div>
                            </div>
                        </label>

                        {{-- Next.js --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="template_key" value="nextjs_starter" class="peer hidden">
                            <div class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-slate-900 rounded-lg flex items-center justify-center">
                                        <i class="fa-brands fa-node-js text-xl text-white"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">Next.js App</p>
                                        <span class="text-[10px] bg-slate-100 text-slate-700 px-2 py-0.5 rounded-full font-medium">Next.js</span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">Next.js dengan App Router dan TailwindCSS. Cocok untuk SSR, SSG, maupun full-stack web app.</p>
                                <div class="mt-3 flex items-center gap-1.5 text-[11px] text-slate-400">
                                    <i class="fa-solid fa-gear fa-spin" style="animation-duration:3s"></i> Auto build
                                </div>
                            </div>
                        </label>

                        {{-- Node.js --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="template_key" value="node_express" class="peer hidden">
                            <div class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-brands fa-node text-xl text-emerald-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">Node.js Express API</p>
                                        <span class="text-[10px] bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-medium">Node.js</span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">REST API Express.js dengan struktur MVC, middleware auth JWT, dan koneksi database siap pakai.</p>
                                <div class="mt-3 flex items-center gap-1.5 text-[11px] text-slate-400">
                                    <i class="fa-solid fa-gear fa-spin" style="animation-duration:3s"></i> Auto build
                                </div>
                            </div>
                        </label>

                        {{-- Vue 3 --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="template_key" value="vue_starter" class="peer hidden">
                            <div class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-brands fa-vuejs text-xl text-emerald-500"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">Vue 3 + Vite</p>
                                        <span class="text-[10px] bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-medium">Vue JS</span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">Framework progresif JavaScript untuk membangun antarmuka pengguna yang interaktif dan cepat.</p>
                                <div class="mt-3 flex items-center gap-1.5 text-[11px] text-slate-400">
                                    <i class="fa-solid fa-gear fa-spin" style="animation-duration:3s"></i> Auto build
                                </div>
                            </div>
                        </label>

                        {{-- Nuxt --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="template_key" value="nuxt_starter" class="peer hidden">
                            <div class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                        <svg viewBox="0 0 128 128" class="w-6 h-6 fill-current text-emerald-600"><path d="M72.9 22L45.4 69.5h16.6l10.9-18.8 24.6 42.6H128L72.9 22zM28.4 46.2L0 95.3h33.2l16.1-27.9 10.9 18.8h33.2L28.4 46.2z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">Nuxt.js</p>
                                        <span class="text-[10px] bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-medium">Vue JS</span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">Framework intuitif berbasis Vue.js untuk membangun aplikasi web SSR.</p>
                                <div class="mt-3 flex items-center gap-1.5 text-[11px] text-slate-400">
                                    <i class="fa-solid fa-gear fa-spin" style="animation-duration:3s"></i> Auto build
                                </div>
                            </div>
                        </label>

                        {{-- SvelteKit --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="template_key" value="svelte_starter" class="peer hidden">
                            <div class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <svg viewBox="0 0 128 128" class="w-6 h-6 fill-current text-orange-600"><path d="M125.7 66.8c-.5 5.5-2.2 10.9-4.9 15.7-3.8 6.7-9.3 12.3-15.9 16.3-6.6 4-14.2 6.1-21.9 6.1-4 0-8-.6-11.8-1.7-8.1-2.4-15.3-7.5-20.4-14.5-3.3-4.5-5.6-9.8-6.6-15.4-1.2-6.5-.4-13.1 2.2-19.1 2-4.5 4.9-8.5 8.5-11.8.6-.6 1.4-1.1 2.3-1.1 1.7 0 2.5 2 1.4 3.2-1.9 2-3.5 4.3-4.7 6.7-2.1 4.2-3.1 8.9-2.7 13.7.6 7 4.1 13.5 9.6 18.2 4.4 3.7 10.2 5.9 16.2 5.9 5.6 0 11.2-1.8 15.9-5.3 4.7-3.5 8.1-8.5 9.7-14.2 1.6-5.8 1.4-11.9-.6-17.6-2.1-5.7-6.2-10.5-11.5-13.4-5.4-3-11.7-4.1-17.8-3.3-6.1.8-11.9 3.5-16.3 7.8l-9.8 9.8c-7.3 7.3-11.4 17.2-11.4 27.5s4.1 20.2 11.4 27.5c7.3 7.3 17.2 11.4 27.5 11.4 9 0 17.8-3 24.8-8.5 4.6-3.6 8.5-8.2 11-13.6.5-1.1-1.3-2.1-1.8-1-1.4 2.8-3.4 5.3-5.8 7.5-6.7 6.1-15.6 9.4-24.8 9.4-9.2 0-18.1-3.6-24.6-10.1-6.5-6.5-10.1-15.3-10.1-24.6 0-9.2 3.6-18.1 10.1-24.6l9.8-9.8c5.4-5.4 12.7-8.7 20.4-9.4 7.7-.7 15.5.9 22.1 4.5 6.6 3.6 11.9 9.3 14.9 16.2 2.8 6.5 3.5 13.8 1.9 20.6z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">SvelteKit</p>
                                        <span class="text-[10px] bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-medium">Node.js</span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">Framework super cepat karena tidak menggunakan virtual DOM.</p>
                                <div class="mt-3 flex items-center gap-1.5 text-[11px] text-slate-400">
                                    <i class="fa-solid fa-gear fa-spin" style="animation-duration:3s"></i> Auto build
                                </div>
                            </div>
                        </label>

                        {{-- Ghost CMS --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="template_key" value="ghost_cms" class="peer hidden">
                            <div class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-slate-900 rounded-lg flex items-center justify-center">
                                        <svg viewBox="0 0 128 128" class="w-6 h-6 fill-current text-white"><path d="M64 0C28.7 0 0 28.7 0 64c0 14.1 4.6 27.2 12.4 37.7 2.3 3.1 6.8 3.5 9.7.9l12-10.8c2.4-2.2 6.1-2.2 8.5 0l9.8 8.8c3.2 2.9 8 2.9 11.2 0l9.8-8.8c2.4-2.2 6.1-2.2 8.5 0l12 10.8c2.8 2.6 7.3 2.1 9.7-.9C123.4 91.2 128 78.1 128 64 128 28.7 99.3 0 64 0zM42.7 58.7c-4.4 0-8-3.6-8-8s3.6-8 8-8 8 3.6 8 8-3.6 8-8 8zm42.6 0c-4.4 0-8-3.6-8-8s3.6-8 8-8 8 3.6 8 8-3.6 8-8 8z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">Ghost CMS</p>
                                        <span class="text-[10px] bg-slate-100 text-slate-700 px-2 py-0.5 rounded-full font-medium">Node.js</span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">Platform CMS profesional dan modern berbasis Node.js untuk blog & newsletter.</p>
                                <div class="mt-3 flex items-center gap-1.5 text-[11px] text-slate-400">
                                    <i class="fa-solid fa-bolt"></i> Auto setup
                                </div>
                            </div>
                        </label>

                        {{-- PHP Basic --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="template_key" value="php_basic" class="peer hidden">
                            <div class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-brands fa-php text-xl text-indigo-500"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">PHP Basic</p>
                                        <span class="text-[10px] bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-medium">PHP Native</span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">Boilerplate PHP dasar tanpa framework khusus. Sangat ringan.</p>
                                <div class="mt-3 flex items-center gap-1.5 text-[11px] text-slate-400">
                                    <i class="fa-solid fa-bolt"></i> Instan
                                </div>
                            </div>
                        </label>

                    </div>
                </div>

                {{-- ── STEP 3: Konfigurasi Proyek ── --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2 text-sm">
                        <span id="step_config_num" class="w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                        Konfigurasi Proyek
                    </h3>

                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Proyek & Domain <span class="text-rose-500">*</span></label>
                        <div class="flex flex-col sm:flex-row shadow-sm">
                            <input type="text" name="project_name" required placeholder="my-awesome-app"
                                id="input_project_name"
                                class="flex-1 w-full sm:rounded-l-xl sm:rounded-tr-none rounded-t-xl bg-slate-50 border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition z-10 relative">
                            
                            <select name="domain_extension" id="select_domain_ext" class="sm:rounded-r-xl sm:rounded-bl-none rounded-b-xl bg-slate-100 border border-slate-200 sm:border-l-0 px-4 py-2.5 text-sm font-medium text-slate-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition cursor-pointer">
                                <option value=".ryaze.my.id">.ryaze.my.id</option>
                                <option value=".ryz.my.id">.ryz.my.id</option>
                                <option value=".safetalkai.my.id">.safetalkai.my.id</option>
                            </select>
                        </div>
                        <div class="mt-2 text-[11px] text-slate-500 font-medium flex items-center">
                            <i class="fa-solid fa-link mr-1.5"></i> Preview: <span id="domain_preview" class="text-indigo-600 ml-1 font-mono">my-awesome-app.ryaze.my.id</span>
                        </div>
                    </div>

                    {{-- Framework (hanya tampil saat mode repo) --}}
                    <div id="framework_section">
                        <label class="block text-xs font-bold text-slate-700 mb-3">Pilih Framework <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @php
                            $availableFrameworks = explode(',', \App\Models\Setting::val('available_frameworks', 'html,php,laravel,react,nextjs,python,node,vue'));
                            $availableFrameworks = array_map('trim', $availableFrameworks);
                            $frameworkIcons = [
                                'html' => ['icon' => 'fa-brands fa-html5', 'color' => 'text-orange-500', 'name' => 'HTML Statis'],
                                'php' => ['icon' => 'fa-brands fa-php', 'color' => 'text-indigo-500', 'name' => 'PHP Native'],
                                'laravel' => ['icon' => 'fa-brands fa-laravel', 'color' => 'text-red-500', 'name' => 'Laravel'],
                                'react' => ['icon' => 'fa-brands fa-react', 'color' => 'text-sky-500', 'name' => 'React JS'],
                                'nextjs' => ['icon' => 'fa-brands fa-node-js', 'color' => 'text-slate-800', 'name' => 'Next.js'],
                                'python' => ['icon' => 'fa-brands fa-python', 'color' => 'text-yellow-500', 'name' => 'Python'],
                                'node' => ['icon' => 'fa-brands fa-node', 'color' => 'text-emerald-500', 'name' => 'Node.js'],
                                'vue' => ['icon' => 'fa-brands fa-vuejs', 'color' => 'text-emerald-500', 'name' => 'Vue JS'],
                            ];
                            @endphp

                            @foreach($availableFrameworks as $index => $fw)
                                @php
                                    $fwKey = strtolower($fw);
                                    $info = $frameworkIcons[$fwKey] ?? ['icon' => 'fa-solid fa-code', 'color' => 'text-slate-500', 'name' => strtoupper($fw)];
                                    $isDisabled = ($fwKey == 'python'); // Python always manual contact
                                @endphp
                                
                                @if($isDisabled)
                                    <label class="relative cursor-pointer opacity-60" onclick="Swal.fire({icon: 'info', title: 'Informasi', text: 'Untuk deploy aplikasi Python, silakan hubungi admin melalui Tiket Bantuan terlebih dahulu.'})">
                                        <input type="radio" name="framework" value="{{ $fw }}" class="peer hidden" disabled>
                                        <div class="p-3 border-2 border-slate-200 rounded-xl bg-slate-50 transition-all text-center cursor-not-allowed">
                                            <i class="{{ $info['icon'] }} text-2xl {{ $info['color'] }} mb-1.5 block opacity-60"></i>
                                            <p class="font-bold text-slate-700 text-xs opacity-60">{{ $info['name'] }}</p>
                                        </div>
                                    </label>
                                @else
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="framework" value="{{ $fw }}" class="peer hidden" {{ $index === 0 ? 'required' : '' }}>
                                        <div class="p-3 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 transition-all text-center">
                                            <i class="{{ $info['icon'] }} text-2xl {{ $info['color'] }} mb-1.5 block"></i>
                                            <p class="font-bold text-slate-700 text-xs">{{ $info['name'] }}</p>
                                        </div>
                                    </label>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4">
                    <a href="{{ route('user_hosting.dashboard') }}"
                        class="text-sm font-bold text-slate-500 hover:text-slate-800 transition-colors">Batal</a>
                    
                    @if (Auth::user()->hasActiveHostingSubscription())
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-8 py-3 rounded-lg shadow-md shadow-indigo-200 transition-all">
                            <i class="fa-solid fa-rocket mr-2"></i> Deploy Sekarang
                        </button>
                    @else
                        <a href="{{ route('user_hosting.billing') }}"
                            class="bg-rose-500 hover:bg-rose-600 text-white font-bold px-6 py-3 rounded-lg shadow-md shadow-rose-200 transition-all flex items-center gap-2">
                            <i class="fa-solid fa-credit-card"></i> Langganan Untuk Deploy
                        </a>
                    @endif
                </div>
            </form>
        </div>
        
<script nonce="{{ csp_nonce() }}">
(function() {
    const sectionRepo     = document.getElementById('section_repo');
    const sectionTemplate = document.getElementById('section_template');
    const frameworkSection= document.getElementById('framework_section');
    const repoUrl         = document.getElementById('input_repo_source');
    const projectNameInput= document.getElementById('input_project_name');
    const domainPreview   = document.getElementById('domain_preview');

    function updateDomainPreview() {
        var name = projectNameInput.value.trim().toLowerCase().replace(/[^a-z0-9-]/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
        if (!name) name = 'my-awesome-app';
        
        var ext = document.getElementById('select_domain_ext');
        var extVal = ext ? ext.value : '.ryaze.my.id';
        
        domainPreview.textContent = name + extVal;
    }

    projectNameInput.addEventListener('input', updateDomainPreview);
    var selectDomainExt = document.getElementById('select_domain_ext');
    if (selectDomainExt) {
        selectDomainExt.addEventListener('change', updateDomainPreview);
    }

    // --- Toggle antara mode Repo vs Template ---
    document.querySelectorAll('input[name="source_type"]').forEach(radio => {
        radio.addEventListener('change', (e) => {
            const isTemplate = e.target.value === 'template';

            // Toggle visibility section
            if(sectionRepo) sectionRepo.classList.toggle('hidden', isTemplate);
            if(sectionTemplate) sectionTemplate.classList.toggle('hidden', !isTemplate);
            if(frameworkSection) frameworkSection.classList.toggle('hidden', isTemplate);

            // Toggle required attr untuk repo_source
            if (isTemplate) {
                if(repoUrl) repoUrl.removeAttribute('required');
                document.querySelectorAll('input[name="framework"]').forEach(r => r.removeAttribute('required'));
            } else {
                if(repoUrl) repoUrl.setAttribute('required', 'required');
                const firstFramework = document.querySelector('input[name="framework"]');
                if (firstFramework) firstFramework.setAttribute('required', 'required');
                // Reset pilihan template
                document.querySelectorAll('input[name="template_key"]').forEach(r => r.checked = false);
            }
        });
    });
})();
</script>

    </x-ui.page-layout>
@endsection
