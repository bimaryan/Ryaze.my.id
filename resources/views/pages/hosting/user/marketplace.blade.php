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
            </div>

        </div>
    </x-ui.page-layout>
@endsection
