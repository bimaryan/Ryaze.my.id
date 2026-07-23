@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Buat Aplikasi Android Baru"
            subtitle="Isi detail di bawah ini untuk memulai proses build website Anda menjadi .apk"
            icon="fa-solid fa-hammer"
            iconColor="indigo">
            <x-slot:actions>
                <a href="{{ route('user_hosting.apk.index') }}"
                    class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <form action="{{ route('user_hosting.apk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Kolom Kiri: Info Dasar & Ikon -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="text-base font-bold text-slate-800"><i class="fa-solid fa-circle-info text-indigo-500 mr-2"></i> Informasi Dasar</h3>
                            <p class="text-xs text-slate-500 mt-1">Detail utama aplikasi Android Anda.</p>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label for="app_name" class="block mb-2 text-sm font-bold text-slate-700">Nama Aplikasi</label>
                                <input type="text" id="app_name" name="app_name" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 transition-shadow hover:border-slate-400" placeholder="Contoh: Toko Online Saya" required value="{{ old('app_name') }}">
                                @error('app_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="app_url" class="block mb-2 text-sm font-bold text-slate-700">URL Website</label>
                                <input type="url" id="app_url" name="app_url" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 transition-shadow hover:border-slate-400" placeholder="https://example.com" required value="{{ old('app_url') }}">
                                <p class="mt-1.5 text-xs text-slate-500"><i class="fa-solid fa-lock text-emerald-500 mr-1"></i> Wajib menggunakan protokol HTTPS.</p>
                                @error('app_url') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="package_name" class="block mb-2 text-sm font-bold text-slate-700">Package Name</label>
                                <input type="text" id="package_name" name="package_name" class="bg-slate-50 border border-slate-300 text-slate-600 font-mono text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 transition-shadow hover:border-slate-400" placeholder="com.example.app" required value="{{ old('package_name') }}">
                                <p class="mt-1.5 text-xs text-slate-500">Identitas unik aplikasi di Android. Format: <code>com.namaperusahaan.namaapp</code></p>
                                @error('package_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="text-base font-bold text-slate-800"><i class="fa-solid fa-palette text-indigo-500 mr-2"></i> Tampilan & Desain</h3>
                            <p class="text-xs text-slate-500 mt-1">Sesuaikan warna dan perilaku layar aplikasi.</p>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="theme_color" class="block mb-2 text-sm font-bold text-slate-700">Theme Color</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" id="theme_color" name="theme_color" class="h-10 w-14 rounded cursor-pointer border border-slate-300 p-0.5" value="{{ old('theme_color', '#FFFFFF') }}">
                                    <p class="text-xs text-slate-500 leading-relaxed">Warna status bar HP saat aplikasi dibuka.</p>
                                </div>
                                @error('theme_color') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="background_color" class="block mb-2 text-sm font-bold text-slate-700">Background Color</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" id="background_color" name="background_color" class="h-10 w-14 rounded cursor-pointer border border-slate-300 p-0.5" value="{{ old('background_color', '#FFFFFF') }}">
                                    <p class="text-xs text-slate-500 leading-relaxed">Warna latar belakang <i>Splash Screen</i>.</p>
                                </div>
                                @error('background_color') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            
                            <div>
                                <label for="display_mode" class="block mb-2 text-sm font-bold text-slate-700">Display Mode</label>
                                <select id="display_mode" name="display_mode" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                                    <option value="standalone" {{ old('display_mode') == 'standalone' ? 'selected' : '' }}>Standalone (Normal)</option>
                                    <option value="fullscreen" {{ old('display_mode') == 'fullscreen' ? 'selected' : '' }}>Fullscreen (Tanpa Status Bar)</option>
                                    <option value="minimal-ui" {{ old('display_mode') == 'minimal-ui' ? 'selected' : '' }}>Minimal UI</option>
                                </select>
                            </div>

                            <div>
                                <label for="orientation" class="block mb-2 text-sm font-bold text-slate-700">Orientasi Layar</label>
                                <select id="orientation" name="orientation" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                                    <option value="default" {{ old('orientation') == 'default' ? 'selected' : '' }}>Default (Sesuai Rotasi HP)</option>
                                    <option value="portrait" {{ old('orientation') == 'portrait' ? 'selected' : '' }}>Portrait (Selalu Berdiri)</option>
                                    <option value="landscape" {{ old('orientation') == 'landscape' ? 'selected' : '' }}>Landscape (Selalu Mendatar)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Ikon & Advanced -->
                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="text-base font-bold text-slate-800"><i class="fa-solid fa-image text-indigo-500 mr-2"></i> Ikon Aplikasi</h3>
                        </div>
                        <div class="p-6">
                            <label for="icon" class="flex flex-col items-center justify-center w-full h-40 border-2 border-slate-300 border-dashed rounded-xl cursor-pointer bg-slate-50 hover:bg-slate-100 hover:border-indigo-400 transition">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                                    <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 mb-3"></i>
                                    <p class="mb-1 text-sm text-slate-600"><span class="font-bold text-indigo-600">Klik untuk upload</span></p>
                                    <p class="text-xs text-slate-500">PNG atau JPG (Min. 512x512px)</p>
                                </div>
                                <input type="file" id="icon" name="icon" accept="image/png, image/jpeg" class="hidden" />
                            </label>
                            @error('icon') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="text-base font-bold text-slate-800"><i class="fa-solid fa-sliders text-indigo-500 mr-2"></i> Advanced (Opsional)</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label for="version_name" class="block mb-2 text-sm font-bold text-slate-700">Version Name</label>
                                <input type="text" id="version_name" name="version_name" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" placeholder="1.0.0" value="{{ old('version_name', '1.0.0') }}">
                            </div>
                            <div>
                                <label for="version_code" class="block mb-2 text-sm font-bold text-slate-700">Version Code</label>
                                <input type="number" id="version_code" name="version_code" min="1" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" placeholder="1" value="{{ old('version_code', 1) }}">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-bold rounded-xl text-base px-6 py-4 text-center transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fa-solid fa-rocket mr-2"></i> Mulai Build APK
                    </button>
                </div>
            </div>
        </form>
    </x-ui.page-layout>
@endsection