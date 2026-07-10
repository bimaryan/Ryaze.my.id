@extends('index')

@section('title', 'Galeri Template Desain')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header title="Galeri Template ✨">
        <x-slot name="subtitle">
            <p class="text-sm text-slate-500">Pilih desain UI siap pakai berbasis Tailwind CSS untuk website Anda.</p>
        </x-slot>
    </x-ui.page-header>

    <!-- Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        
        {{-- Tailwind Portfolio --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
            <div class="absolute top-4 right-4 text-xs font-black bg-cyan-100 text-cyan-700 px-2.5 py-1 rounded-full z-20 uppercase tracking-wide shadow-sm">PORTFOLIO</div>
            
            <a href="#" class="relative h-48 overflow-hidden block border-b border-slate-100">
                <img src="https://placehold.co/600x400/4f46e5/ffffff?text=Portfolio+Template" alt="Portfolio" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/40 transition-colors flex items-center justify-center">
                    <span class="opacity-0 group-hover:opacity-100 bg-white text-slate-900 text-sm font-bold py-2 px-4 rounded-lg shadow-lg transition-all transform translate-y-4 group-hover:translate-y-0 flex items-center">
                        <i class="fa-solid fa-eye mr-2 text-indigo-600"></i>Live Preview
                    </span>
                </div>
            </a>

            <div class="p-5 pb-0 flex-1 relative z-10">
                <h3 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">Tailwind Portfolio</h3>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed">Template UI portfolio personal yang elegan, menggunakan dark mode dan gradient khas Tailwind.</p>
            </div>
            <div class="p-5 pt-0 mt-auto relative z-10">
                <form action="{{ route('user_hosting.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="source_type" value="template">
                    <input type="hidden" name="template_key" value="tailwind_portfolio">
                    <div class="flex gap-2">
                        <input type="text" name="project_name" placeholder="Nama Proyek" required class="flex-1 text-sm px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                        <button type="submit" class="bg-slate-900 hover:bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0" title="Deploy Now">
                            <i class="fa-solid fa-play"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tailwind Landing --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
            <div class="absolute top-4 right-4 text-xs font-black bg-blue-100 text-blue-700 px-2.5 py-1 rounded-full z-20 uppercase tracking-wide shadow-sm">LANDING PAGE</div>
            
            <a href="#" class="relative h-48 overflow-hidden block border-b border-slate-100">
                <img src="https://placehold.co/600x400/2563eb/ffffff?text=Landing+Page" alt="Landing Page" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/40 transition-colors flex items-center justify-center">
                    <span class="opacity-0 group-hover:opacity-100 bg-white text-slate-900 text-sm font-bold py-2 px-4 rounded-lg shadow-lg transition-all transform translate-y-4 group-hover:translate-y-0 flex items-center">
                        <i class="fa-solid fa-eye mr-2 text-indigo-600"></i>Live Preview
                    </span>
                </div>
            </a>

            <div class="p-5 pb-0 flex-1 relative z-10">
                <h3 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">Tailwind Landing</h3>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed">Template UI SaaS landing page yang bersih, profesional, dengan section hero yang menarik.</p>
            </div>
            <div class="p-5 pt-0 mt-auto relative z-10">
                <form action="{{ route('user_hosting.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="source_type" value="template">
                    <input type="hidden" name="template_key" value="tailwind_landing">
                    <div class="flex gap-2">
                        <input type="text" name="project_name" placeholder="Nama Proyek" required class="flex-1 text-sm px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                        <button type="submit" class="bg-slate-900 hover:bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0" title="Deploy Now">
                            <i class="fa-solid fa-play"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tailwind Blog --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
            <div class="absolute top-4 right-4 text-xs font-black bg-orange-100 text-orange-700 px-2.5 py-1 rounded-full z-20 uppercase tracking-wide shadow-sm">BLOG</div>
            
            <a href="#" class="relative h-48 overflow-hidden block border-b border-slate-100">
                <img src="https://placehold.co/600x400/ea580c/ffffff?text=Blog+Template" alt="Blog" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/40 transition-colors flex items-center justify-center">
                    <span class="opacity-0 group-hover:opacity-100 bg-white text-slate-900 text-sm font-bold py-2 px-4 rounded-lg shadow-lg transition-all transform translate-y-4 group-hover:translate-y-0 flex items-center">
                        <i class="fa-solid fa-eye mr-2 text-indigo-600"></i>Live Preview
                    </span>
                </div>
            </a>

            <div class="p-5 pb-0 flex-1 relative z-10">
                <h3 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">Tailwind Blog</h3>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed">Template UI bergaya minimalis dan clean typography, sangat cocok untuk blog atau jurnal personal.</p>
            </div>
            <div class="p-5 pt-0 mt-auto relative z-10">
                <form action="{{ route('user_hosting.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="source_type" value="template">
                    <input type="hidden" name="template_key" value="tailwind_blog">
                    <div class="flex gap-2">
                        <input type="text" name="project_name" placeholder="Nama Proyek" required class="flex-1 text-sm px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                        <button type="submit" class="bg-slate-900 hover:bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0" title="Deploy Now">
                            <i class="fa-solid fa-play"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        {{-- Tailwind E-Commerce --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
            <div class="absolute top-4 right-4 text-xs font-black bg-pink-100 text-pink-700 px-2.5 py-1 rounded-full z-20 uppercase tracking-wide shadow-sm">E-COMMERCE</div>
            
            <a href="#" class="relative h-48 overflow-hidden block border-b border-slate-100">
                <img src="https://placehold.co/600x400/db2777/ffffff?text=E-Commerce" alt="E-Commerce" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/40 transition-colors flex items-center justify-center">
                    <span class="opacity-0 group-hover:opacity-100 bg-white text-slate-900 text-sm font-bold py-2 px-4 rounded-lg shadow-lg transition-all transform translate-y-4 group-hover:translate-y-0 flex items-center">
                        <i class="fa-solid fa-eye mr-2 text-indigo-600"></i>Live Preview
                    </span>
                </div>
            </a>

            <div class="p-5 pb-0 flex-1 relative z-10">
                <h3 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">Tailwind E-Commerce</h3>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed">Katalog produk lengkap dengan shopping cart. Desain modern untuk meningkatkan konversi penjualan.</p>
            </div>
            <div class="p-5 pt-0 mt-auto relative z-10">
                <form action="{{ route('user_hosting.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="source_type" value="template">
                    <input type="hidden" name="template_key" value="tailwind_ecommerce">
                    <div class="flex gap-2">
                        <input type="text" name="project_name" placeholder="Nama Proyek" required class="flex-1 text-sm px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                        <button type="submit" class="bg-slate-900 hover:bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0" title="Deploy Now">
                            <i class="fa-solid fa-play"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tailwind Admin --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
            <div class="absolute top-4 right-4 text-xs font-black bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full z-20 uppercase tracking-wide shadow-sm">DASHBOARD</div>
            
            <a href="#" class="relative h-48 overflow-hidden block border-b border-slate-100">
                <img src="https://placehold.co/600x400/059669/ffffff?text=Admin+Dashboard" alt="Admin Dashboard" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/40 transition-colors flex items-center justify-center">
                    <span class="opacity-0 group-hover:opacity-100 bg-white text-slate-900 text-sm font-bold py-2 px-4 rounded-lg shadow-lg transition-all transform translate-y-4 group-hover:translate-y-0 flex items-center">
                        <i class="fa-solid fa-eye mr-2 text-indigo-600"></i>Live Preview
                    </span>
                </div>
            </a>

            <div class="p-5 pb-0 flex-1 relative z-10">
                <h3 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">Tailwind Admin</h3>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed">Template admin dashboard responsif dengan sidebar, chart, dan komponen UI lengkap.</p>
            </div>
            <div class="p-5 pt-0 mt-auto relative z-10">
                <form action="{{ route('user_hosting.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="source_type" value="template">
                    <input type="hidden" name="template_key" value="tailwind_admin">
                    <div class="flex gap-2">
                        <input type="text" name="project_name" placeholder="Nama Proyek" required class="flex-1 text-sm px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                        <button type="submit" class="bg-slate-900 hover:bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0" title="Deploy Now">
                            <i class="fa-solid fa-play"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tailwind Link in Bio --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
            <div class="absolute top-4 right-4 text-xs font-black bg-purple-100 text-purple-700 px-2.5 py-1 rounded-full z-20 uppercase tracking-wide shadow-sm">LINK IN BIO</div>
            
            <a href="#" class="relative h-48 overflow-hidden block border-b border-slate-100">
                <img src="https://placehold.co/600x400/7c3aed/ffffff?text=Link+in+Bio" alt="Link in Bio" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/40 transition-colors flex items-center justify-center">
                    <span class="opacity-0 group-hover:opacity-100 bg-white text-slate-900 text-sm font-bold py-2 px-4 rounded-lg shadow-lg transition-all transform translate-y-4 group-hover:translate-y-0 flex items-center">
                        <i class="fa-solid fa-eye mr-2 text-indigo-600"></i>Live Preview
                    </span>
                </div>
            </a>

            <div class="p-5 pb-0 flex-1 relative z-10">
                <h3 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">Tailwind Link in Bio</h3>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed">Alternatif Linktree yang cantik dan ringan. Mudah di-kustomisasi untuk menaruh semua link sosial media Anda.</p>
            </div>
            <div class="p-5 pt-0 mt-auto relative z-10">
                <form action="{{ route('user_hosting.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="source_type" value="template">
                    <input type="hidden" name="template_key" value="tailwind_linkinbio">
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
