@extends('index')

@section('title', 'Galeri Template Desain')

@section('content')
<x-ui.page-layout>
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Galeri Template ✨</h1>
            <p class="text-sm text-slate-500 mt-1">Pilih desain UI siap pakai berbasis Tailwind CSS untuk website Anda.</p>
        </div>
    </div>

    <!-- Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        
        {{-- Tailwind Portfolio --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
            <div class="absolute top-4 right-4 text-xs font-black bg-cyan-100 text-cyan-700 px-2.5 py-1 rounded-full z-10 uppercase tracking-wide">PORTFOLIO</div>
            <div class="p-6 pb-0 flex-1 relative z-10">
                <div class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-inner border border-indigo-100/50">
                    <i class="fa-solid fa-user-tie text-3xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">Tailwind Portfolio</h3>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed">Template UI portfolio personal yang elegan, menggunakan dark mode dan gradient khas Tailwind.</p>
            </div>
            <div class="p-6 pt-0 mt-auto relative z-10">
                <form action="{{ route('user_hosting.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="source_type" value="template">
                    <input type="hidden" name="template_key" value="tailwind_portfolio">
                    <div class="flex gap-2">
                        <input type="text" name="project_name" placeholder="Nama Proyek" required class="flex-1 text-sm px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0" title="Gunakan Desain Ini">
                            <i class="fa-solid fa-magic mr-2"></i> Gunakan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tailwind Landing --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
            <div class="absolute top-4 right-4 text-xs font-black bg-blue-100 text-blue-700 px-2.5 py-1 rounded-full z-10 uppercase tracking-wide">LANDING PAGE</div>
            <div class="p-6 pb-0 flex-1 relative z-10">
                <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-inner border border-blue-100/50">
                    <i class="fa-solid fa-rocket text-3xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">Tailwind Landing</h3>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed">Template UI SaaS landing page yang bersih, profesional, dengan section hero yang menarik.</p>
            </div>
            <div class="p-6 pt-0 mt-auto relative z-10">
                <form action="{{ route('user_hosting.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="source_type" value="template">
                    <input type="hidden" name="template_key" value="tailwind_landing">
                    <div class="flex gap-2">
                        <input type="text" name="project_name" placeholder="Nama Proyek" required class="flex-1 text-sm px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0" title="Gunakan Desain Ini">
                            <i class="fa-solid fa-magic mr-2"></i> Gunakan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tailwind Blog --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
            <div class="absolute top-4 right-4 text-xs font-black bg-orange-100 text-orange-700 px-2.5 py-1 rounded-full z-10 uppercase tracking-wide">BLOG / JURNAL</div>
            <div class="p-6 pb-0 flex-1 relative z-10">
                <div class="w-16 h-16 bg-orange-50 text-orange-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-inner border border-orange-100/50">
                    <i class="fa-solid fa-newspaper text-3xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">Tailwind Blog</h3>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed">Template UI bergaya minimalis dan clean typography, sangat cocok untuk blog atau jurnal personal.</p>
            </div>
            <div class="p-6 pt-0 mt-auto relative z-10">
                <form action="{{ route('user_hosting.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="source_type" value="template">
                    <input type="hidden" name="template_key" value="tailwind_blog">
                    <div class="flex gap-2">
                        <input type="text" name="project_name" placeholder="Nama Proyek" required class="flex-1 text-sm px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0" title="Gunakan Desain Ini">
                            <i class="fa-solid fa-magic mr-2"></i> Gunakan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
</x-ui.page-layout>
@endsection
