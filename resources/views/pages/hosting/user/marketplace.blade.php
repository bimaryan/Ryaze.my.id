@extends('index')

@section('content')
    <x-ui.page-layout>
        <div class="relative bg-gradient-to-r from-indigo-600 to-purple-700 rounded-2xl p-8 mb-8 overflow-hidden shadow-lg border border-indigo-500/50">
            <div class="absolute right-0 top-0 opacity-10 pointer-events-none">
                <i class="fa-solid fa-rocket text-[200px] -mt-10 -mr-10"></i>
            </div>
            <div class="relative z-10 max-w-2xl">
                <h1 class="text-3xl font-black text-white mb-2 tracking-tight">App Marketplace</h1>
                <p class="text-indigo-100 text-sm mb-6 leading-relaxed">
                    Deploy aplikasi modern dalam hitungan detik. Tanpa konfigurasi manual, tanpa setup server. Pilih framework favorit Anda dan biarkan Ryaze Auto-Deployer melakukan sisanya.
                </p>
                <div class="flex items-center gap-3">
                    <span class="bg-white/20 text-white text-xs font-bold px-3 py-1.5 rounded-full backdrop-blur-sm border border-white/10">
                        <i class="fa-solid fa-bolt text-yellow-300 mr-1"></i> 1-Click Install
                    </span>
                    <span class="bg-white/20 text-white text-xs font-bold px-3 py-1.5 rounded-full backdrop-blur-sm border border-white/10">
                        <i class="fa-solid fa-shield-halved text-emerald-300 mr-1"></i> Production Ready
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            
            {{-- WordPress --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
                <div class="absolute top-4 right-4 text-xs font-black bg-blue-100 text-blue-700 px-2.5 py-1 rounded-full z-10 uppercase tracking-wide">CMS</div>
                <div class="p-6 pb-0 flex-1 relative z-10">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-inner border border-blue-100/50">
                        <i class="fa-brands fa-wordpress text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">WordPress</h3>
                    <p class="text-sm text-slate-500 mb-6 leading-relaxed">Platform CMS terpopuler di dunia. Sudah termasuk database otomatis dan siap digunakan.</p>
                </div>
                <div class="p-6 pt-0 mt-auto relative z-10">
                    <form action="{{ route('user_hosting.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="source_type" value="template">
                        <input type="hidden" name="template_key" value="wordpress">
                        <div class="flex gap-2">
                            <input type="text" name="project_name" placeholder="Nama Proyek" required class="flex-1 text-sm px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                            <button type="submit" class="bg-slate-900 hover:bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0" title="Deploy Now">
                                <i class="fa-solid fa-play"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Laravel 13 --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
                <div class="absolute top-4 right-4 text-xs font-black bg-rose-100 text-rose-700 px-2.5 py-1 rounded-full z-10 uppercase tracking-wide">FRAMEWORK</div>
                <div class="p-6 pb-0 flex-1 relative z-10">
                    <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-inner border border-rose-100/50">
                        <i class="fa-brands fa-laravel text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">Laravel 13</h3>
                    <p class="text-sm text-slate-500 mb-6 leading-relaxed">Framework PHP modern versi terbaru. Cepat, aman, dan dirancang untuk developer profesional.</p>
                </div>
                <div class="p-6 pt-0 mt-auto relative z-10">
                    <form action="{{ route('user_hosting.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="source_type" value="template">
                        <input type="hidden" name="template_key" value="laravel_starter_13">
                        <div class="flex gap-2">
                            <input type="text" name="project_name" placeholder="Nama Proyek" required class="flex-1 text-sm px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                            <button type="submit" class="bg-slate-900 hover:bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0" title="Deploy Now">
                                <i class="fa-solid fa-play"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- React + Vite --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
                <div class="absolute top-4 right-4 text-xs font-black bg-sky-100 text-sky-700 px-2.5 py-1 rounded-full z-10 uppercase tracking-wide">FRONTEND</div>
                <div class="p-6 pb-0 flex-1 relative z-10">
                    <div class="w-16 h-16 bg-sky-50 text-sky-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-inner border border-sky-100/50">
                        <i class="fa-brands fa-react text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">React + Vite</h3>
                    <p class="text-sm text-slate-500 mb-6 leading-relaxed">Bangun antarmuka dinamis super cepat dengan React dan build tool generasi terbaru (Vite).</p>
                </div>
                <div class="p-6 pt-0 mt-auto relative z-10">
                    <form action="{{ route('user_hosting.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="source_type" value="template">
                        <input type="hidden" name="template_key" value="react_starter">
                        <div class="flex gap-2">
                            <input type="text" name="project_name" placeholder="Nama Proyek" required class="flex-1 text-sm px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                            <button type="submit" class="bg-slate-900 hover:bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0" title="Deploy Now">
                                <i class="fa-solid fa-play"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Next.js --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
                <div class="absolute top-4 right-4 text-xs font-black bg-slate-100 text-slate-700 px-2.5 py-1 rounded-full z-10 uppercase tracking-wide">FULLSTACK</div>
                <div class="p-6 pb-0 flex-1 relative z-10">
                    <div class="w-16 h-16 bg-slate-100 text-slate-900 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-inner border border-slate-200/50">
                        <svg viewBox="0 0 128 128" class="w-8 h-8 fill-current"><path d="M64 0C28.7 0 0 28.7 0 64s28.7 64 64 64 64-28.7 64-64S99.3 0 64 0zm0 115.2C35.8 115.2 12.8 92.2 12.8 64S35.8 12.8 64 12.8 115.2 35.8 115.2 64 92.2 115.2 64 115.2z"></path><path d="M96 42.7H86L54.7 87.5 42.7 68.3H32L54.7 104l41.3-61.3z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">Next.js</h3>
                    <p class="text-sm text-slate-500 mb-6 leading-relaxed">Framework React untuk produksi dengan fitur rendering SSR/SSG, dan optimasi bawaan.</p>
                </div>
                <div class="p-6 pt-0 mt-auto relative z-10">
                    <form action="{{ route('user_hosting.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="source_type" value="template">
                        <input type="hidden" name="template_key" value="nextjs_starter">
                        <div class="flex gap-2">
                            <input type="text" name="project_name" placeholder="Nama Proyek" required class="flex-1 text-sm px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                            <button type="submit" class="bg-slate-900 hover:bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0" title="Deploy Now">
                                <i class="fa-solid fa-play"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Node Express --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
                <div class="absolute top-4 right-4 text-xs font-black bg-green-100 text-green-700 px-2.5 py-1 rounded-full z-10 uppercase tracking-wide">BACKEND</div>
                <div class="p-6 pb-0 flex-1 relative z-10">
                    <div class="w-16 h-16 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-inner border border-green-100/50">
                        <i class="fa-brands fa-node-js text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">Node + Express</h3>
                    <p class="text-sm text-slate-500 mb-6 leading-relaxed">Bangun REST API yang cepat, skalabel, dan efisien dengan Node.js dan Express.</p>
                </div>
                <div class="p-6 pt-0 mt-auto relative z-10">
                    <form action="{{ route('user_hosting.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="source_type" value="template">
                        <input type="hidden" name="template_key" value="node_express">
                        <div class="flex gap-2">
                            <input type="text" name="project_name" placeholder="Nama Proyek" required class="flex-1 text-sm px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                            <button type="submit" class="bg-slate-900 hover:bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0" title="Deploy Now">
                                <i class="fa-solid fa-play"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            {{-- HTML Landing --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
                <div class="absolute top-4 right-4 text-xs font-black bg-orange-100 text-orange-700 px-2.5 py-1 rounded-full z-10 uppercase tracking-wide">STATIC</div>
                <div class="p-6 pb-0 flex-1 relative z-10">
                    <div class="w-16 h-16 bg-orange-50 text-orange-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-inner border border-orange-100/50">
                        <i class="fa-brands fa-html5 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">HTML Landing</h3>
                    <p class="text-sm text-slate-500 mb-6 leading-relaxed">Situs statis super ringan untuk landing page. Tanpa database, loading instan.</p>
                </div>
                <div class="p-6 pt-0 mt-auto relative z-10">
                    <form action="{{ route('user_hosting.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="source_type" value="template">
                        <input type="hidden" name="template_key" value="html_landing">
                        <div class="flex gap-2">
                            <input type="text" name="project_name" placeholder="Nama Proyek" required class="flex-1 text-sm px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                            <button type="submit" class="bg-slate-900 hover:bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0" title="Deploy Now">
                                <i class="fa-solid fa-play"></i>
                            </button>
                        </div>
                    </form>
                </div>
                {{-- Vue.js --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
                <div class="absolute top-4 right-4 text-xs font-black bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full z-10 uppercase tracking-wide">FRONTEND</div>
                <div class="p-6 pb-0 flex-1 relative z-10">
                    <div class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-inner border border-emerald-100/50">
                        <i class="fa-brands fa-vuejs text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">Vue 3 + Vite</h3>
                    <p class="text-sm text-slate-500 mb-6 leading-relaxed">Framework JavaScript yang progresif dan mudah digunakan untuk membangun UI interaktif.</p>
                </div>
                <div class="p-6 pt-0 mt-auto relative z-10">
                    <form action="{{ route('user_hosting.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="source_type" value="template">
                        <input type="hidden" name="template_key" value="vue_starter">
                        <div class="flex gap-2">
                            <input type="text" name="project_name" placeholder="Nama Proyek" required class="flex-1 text-sm px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                            <button type="submit" class="bg-slate-900 hover:bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0" title="Deploy Now">
                                <i class="fa-solid fa-play"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Nuxt.js --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
                <div class="absolute top-4 right-4 text-xs font-black bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full z-10 uppercase tracking-wide">FULLSTACK</div>
                <div class="p-6 pb-0 flex-1 relative z-10">
                    <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-inner border border-emerald-100/50">
                        <svg viewBox="0 0 128 128" class="w-8 h-8 fill-current"><path d="M72.9 22L45.4 69.5h16.6l10.9-18.8 24.6 42.6H128L72.9 22zM28.4 46.2L0 95.3h33.2l16.1-27.9 10.9 18.8h33.2L28.4 46.2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">Nuxt.js</h3>
                    <p class="text-sm text-slate-500 mb-6 leading-relaxed">Framework intuitif berbasis Vue.js untuk membangun aplikasi web SSR dan static-site generation.</p>
                </div>
                <div class="p-6 pt-0 mt-auto relative z-10">
                    <form action="{{ route('user_hosting.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="source_type" value="template">
                        <input type="hidden" name="template_key" value="nuxt_starter">
                        <div class="flex gap-2">
                            <input type="text" name="project_name" placeholder="Nama Proyek" required class="flex-1 text-sm px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                            <button type="submit" class="bg-slate-900 hover:bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0" title="Deploy Now">
                                <i class="fa-solid fa-play"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- SvelteKit --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
                <div class="absolute top-4 right-4 text-xs font-black bg-orange-100 text-orange-700 px-2.5 py-1 rounded-full z-10 uppercase tracking-wide">FULLSTACK</div>
                <div class="p-6 pb-0 flex-1 relative z-10">
                    <div class="w-16 h-16 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-inner border border-orange-100/50">
                        <svg viewBox="0 0 128 128" class="w-8 h-8 fill-current"><path d="M125.7 66.8c-.5 5.5-2.2 10.9-4.9 15.7-3.8 6.7-9.3 12.3-15.9 16.3-6.6 4-14.2 6.1-21.9 6.1-4 0-8-.6-11.8-1.7-8.1-2.4-15.3-7.5-20.4-14.5-3.3-4.5-5.6-9.8-6.6-15.4-1.2-6.5-.4-13.1 2.2-19.1 2-4.5 4.9-8.5 8.5-11.8.6-.6 1.4-1.1 2.3-1.1 1.7 0 2.5 2 1.4 3.2-1.9 2-3.5 4.3-4.7 6.7-2.1 4.2-3.1 8.9-2.7 13.7.6 7 4.1 13.5 9.6 18.2 4.4 3.7 10.2 5.9 16.2 5.9 5.6 0 11.2-1.8 15.9-5.3 4.7-3.5 8.1-8.5 9.7-14.2 1.6-5.8 1.4-11.9-.6-17.6-2.1-5.7-6.2-10.5-11.5-13.4-5.4-3-11.7-4.1-17.8-3.3-6.1.8-11.9 3.5-16.3 7.8l-9.8 9.8c-7.3 7.3-11.4 17.2-11.4 27.5s4.1 20.2 11.4 27.5c7.3 7.3 17.2 11.4 27.5 11.4 9 0 17.8-3 24.8-8.5 4.6-3.6 8.5-8.2 11-13.6.5-1.1-1.3-2.1-1.8-1-1.4 2.8-3.4 5.3-5.8 7.5-6.7 6.1-15.6 9.4-24.8 9.4-9.2 0-18.1-3.6-24.6-10.1-6.5-6.5-10.1-15.3-10.1-24.6 0-9.2 3.6-18.1 10.1-24.6l9.8-9.8c5.4-5.4 12.7-8.7 20.4-9.4 7.7-.7 15.5.9 22.1 4.5 6.6 3.6 11.9 9.3 14.9 16.2 2.8 6.5 3.5 13.8 1.9 20.6z"></path><path d="M117.8 35.8c-7.3-7.3-17.2-11.4-27.5-11.4-9 0-17.8 3-24.8 8.5-4.6 3.6-8.5 8.2-11 13.6-.5 1.1 1.3 2.1 1.8 1 1.4-2.8 3.4-5.3 5.8-7.5 6.7-6.1 15.6-9.4 24.8-9.4 9.2 0 18.1 3.6 24.6 10.1 6.5 6.5 10.1 15.3 10.1 24.6 0 9.2-3.6 18.1-10.1 24.6l-9.8 9.8c-5.4 5.4-12.7 8.7-20.4 9.4-7.7.7-15.5-.9-22.1-4.5-6.6-3.6-11.9-9.3-14.9-16.2-2.8-6.5-3.5-13.8-1.9-20.6.5-2.2-.7-4.4-2.9-4.9-2.2-.5-4.4.7-4.9 2.9-1.9 8.2-1 16.9 2.4 24.7 3.8 8.4 10.3 15.4 18.5 19.8 8.2 4.4 17.7 6.4 27 5.6 9.3-.8 18.2-4.9 24.9-11.6l9.8-9.8c7.3-7.3 11.4-17.2 11.4-27.5.1-10.3-4.1-20.2-11.4-27.5z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">SvelteKit</h3>
                    <p class="text-sm text-slate-500 mb-6 leading-relaxed">Framework super cepat karena tidak menggunakan virtual DOM. Performa kelas atas.</p>
                </div>
                <div class="p-6 pt-0 mt-auto relative z-10">
                    <form action="{{ route('user_hosting.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="source_type" value="template">
                        <input type="hidden" name="template_key" value="svelte_starter">
                        <div class="flex gap-2">
                            <input type="text" name="project_name" placeholder="Nama Proyek" required class="flex-1 text-sm px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                            <button type="submit" class="bg-slate-900 hover:bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0" title="Deploy Now">
                                <i class="fa-solid fa-play"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Ghost CMS --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
                <div class="absolute top-4 right-4 text-xs font-black bg-slate-100 text-slate-700 px-2.5 py-1 rounded-full z-10 uppercase tracking-wide">CMS</div>
                <div class="p-6 pb-0 flex-1 relative z-10">
                    <div class="w-16 h-16 bg-slate-900 text-white rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-inner border border-slate-700">
                        <svg viewBox="0 0 128 128" class="w-8 h-8 fill-current"><path d="M64 0C28.7 0 0 28.7 0 64c0 14.1 4.6 27.2 12.4 37.7 2.3 3.1 6.8 3.5 9.7.9l12-10.8c2.4-2.2 6.1-2.2 8.5 0l9.8 8.8c3.2 2.9 8 2.9 11.2 0l9.8-8.8c2.4-2.2 6.1-2.2 8.5 0l12 10.8c2.8 2.6 7.3 2.1 9.7-.9C123.4 91.2 128 78.1 128 64 128 28.7 99.3 0 64 0zM42.7 58.7c-4.4 0-8-3.6-8-8s3.6-8 8-8 8 3.6 8 8-3.6 8-8 8zm42.6 0c-4.4 0-8-3.6-8-8s3.6-8 8-8 8 3.6 8 8-3.6 8-8 8z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">Ghost CMS</h3>
                    <p class="text-sm text-slate-500 mb-6 leading-relaxed">Platform penerbitan profesional dan modern berbasis Node.js untuk blog & newsletter.</p>
                </div>
                <div class="p-6 pt-0 mt-auto relative z-10">
                    <form action="{{ route('user_hosting.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="source_type" value="template">
                        <input type="hidden" name="template_key" value="ghost_cms">
                        <div class="flex gap-2">
                            <input type="text" name="project_name" placeholder="Nama Proyek" required class="flex-1 text-sm px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                            <button type="submit" class="bg-slate-900 hover:bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0" title="Deploy Now">
                                <i class="fa-solid fa-play"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- PHP Native --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
                <div class="absolute top-4 right-4 text-xs font-black bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded-full z-10 uppercase tracking-wide">BACKEND</div>
                <div class="p-6 pb-0 flex-1 relative z-10">
                    <div class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-inner border border-indigo-100/50">
                        <i class="fa-brands fa-php text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">PHP Basic (Native)</h3>
                    <p class="text-sm text-slate-500 mb-6 leading-relaxed">Boilerplate aplikasi PHP murni tanpa framework tambahan. Cepat dan klasik.</p>
                </div>
                <div class="p-6 pt-0 mt-auto relative z-10">
                    <form action="{{ route('user_hosting.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="source_type" value="template">
                        <input type="hidden" name="template_key" value="php_basic">
                        <div class="flex gap-2">
                            <input type="text" name="project_name" placeholder="Nama Proyek" required class="flex-1 text-sm px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                            <button type="submit" class="bg-slate-900 hover:bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0" title="Deploy Now">
                                <i class="fa-solid fa-play"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </x-ui.page-layout>
@endsection
