@extends('index')

@section('content')
    <x-ui.page-layout>
{{-- ── 6. USER HOSTING – Deploy Proyek Baru ───────────────────────── --}}
        <div class="p-5 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="shrink-0 w-11 h-11 flex items-center justify-center bg-emerald-50 text-emerald-600 rounded-lg">
                    <i class="fa-solid fa-plus text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Deploy Proyek Baru</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Impor repository Git Anda dan biarkan sistem kami melakukan sisanya.</p>
                </div>
            </div>
            <a href="{{ route('user_hosting.dashboard') }}" class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                &larr; Kembali
            </a>
        </div>

        <div class="mx-auto mt-6">
            <form action="{{ route('user_hosting.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fa-brands fa-github text-xl"></i> Sumber Repository
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">URL Git Repository <span
                                    class="text-rose-500">*</span></label>
                            <input type="url" name="repo_source" required
                                placeholder="https://github.com/username/my-project"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Branch</label>
                            <input type="text" name="branch" value="main" required
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition-all">
                            <p class="text-[11px] text-slate-500 mt-1">Cabang Git yang akan di-build (misal: main, master,
                                atau production).</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3">Konfigurasi Proyek</h3>

                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Proyek <span
                                class="text-rose-500">*</span></label>
                        <input type="text" name="project_name" required placeholder="my-awesome-app"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-t-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition-all">
                        <div
                            class="bg-slate-100 border border-t-0 border-slate-200 rounded-b-lg px-4 py-2 text-xs text-slate-500 font-medium flex items-center">
                            <i class="fa-solid fa-link mr-2"></i> Domain: <span
                                class="text-indigo-600 ml-1">nama-proyek.ryaze.my.id</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-3">Pilih Framework <span
                                class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">

                            <label class="relative cursor-pointer">
                                <input type="radio" name="framework" value="html" class="peer hidden" required>
                                <div
                                    class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 transition-all text-center">
                                    <i class="fa-brands fa-html5 text-3xl text-orange-500 mb-2"></i>
                                    <p class="font-bold text-slate-700 text-sm">HTML Statis</p>
                                </div>
                            </label>

                            <label class="relative cursor-pointer">
                                <input type="radio" name="framework" value="react" class="peer hidden">
                                <div
                                    class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 transition-all text-center">
                                    <i class="fa-brands fa-react text-3xl text-sky-500 mb-2"></i>
                                    <p class="font-bold text-slate-700 text-sm">React JS</p>
                                </div>
                            </label>

                            <label class="relative cursor-pointer">
                                <input type="radio" name="framework" value="nextjs" class="peer hidden">
                                <div
                                    class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 transition-all text-center">
                                    <i class="fa-brands fa-node-js text-3xl text-slate-800 mb-2"></i>
                                    <p class="font-bold text-slate-700 text-sm">Next.js</p>
                                </div>
                            </label>

                            <label class="relative cursor-pointer">
                                <input type="radio" name="framework" value="laravel" class="peer hidden">
                                <div
                                    class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 transition-all text-center">
                                    <i class="fa-brands fa-laravel text-3xl text-red-500 mb-2"></i>
                                    <p class="font-bold text-slate-700 text-sm">Laravel</p>
                                </div>
                            </label>

                            <label class="relative cursor-pointer">
                                <input type="radio" name="framework" value="python" class="peer hidden">
                                <div
                                    class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 transition-all text-center">
                                    <i class="fa-brands fa-python text-3xl text-yellow-500 mb-2"></i>
                                    <p class="font-bold text-slate-700 text-sm">Python (FastAPI/Flask)</p>
                                </div>
                            </label>

                            <label class="relative cursor-pointer">
                                <input type="radio" name="framework" value="node" class="peer hidden">
                                <div
                                    class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-slate-300 transition-all text-center">
                                    <i class="fa-brands fa-node text-3xl text-emerald-500 mb-2"></i>
                                    <p class="font-bold text-slate-700 text-sm">Node.js Express</p>
                                </div>
                            </label>

                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-2">
                    <a href="{{ route('user_hosting.dashboard') }}"
                        class="text-sm font-bold text-slate-500 hover:text-slate-800 transition-colors">Batal</a>
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-8 py-3 rounded-lg shadow-md shadow-indigo-200 transition-all">
                        <i class="fa-solid fa-rocket mr-2"></i> Deploy Sekarang
                    </button>
                </div>
            </form>
        </div>
    </x-ui.page-layout>
@endsection
