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

        @if(!auth()->user()->hasActiveHostingSubscription())
            <!-- Banner Lock Screen -->
            <div class="bg-gradient-to-r from-rose-500 to-pink-500 rounded-2xl p-8 text-white shadow-lg mb-8 relative overflow-hidden">
                <div class="relative z-10">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4 backdrop-blur-sm">
                        <i class="fa-solid fa-lock text-3xl"></i>
                    </div>
                    <h2 class="text-3xl font-black mb-2">Fitur Eksklusif Pro</h2>
                    <p class="text-rose-100 text-lg mb-6 max-w-2xl">Fitur Web to APK Builder (Bubblewrap) memerlukan sumber daya server yang tinggi. Anda harus memiliki langganan Hosting aktif untuk menggunakan fitur ini.</p>
                    <a href="{{ route('user_hosting.billing') }}" class="inline-block bg-white text-rose-600 font-bold px-8 py-3 rounded-xl hover:bg-rose-50 transition shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fa-solid fa-rocket mr-2"></i> Berlangganan Sekarang
                    </a>
                </div>
                <i class="fa-brands fa-android absolute -bottom-10 -right-10 text-9xl text-white opacity-10"></i>
            </div>
        @else
            <!-- Form Aktif -->
            <form action="{{ route('user_hosting.apk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Kolom Kiri: Info Dasar & Tampilan -->
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
                            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                                <div>
                                    <h3 class="text-base font-bold text-slate-800"><i class="fa-solid fa-palette text-indigo-500 mr-2"></i> Tampilan & Desain</h3>
                                    <p class="text-xs text-slate-500 mt-1">Sesuaikan warna dan perilaku layar aplikasi.</p>
                                </div>
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
                                    <label for="navigation_color" class="block mb-2 text-sm font-bold text-slate-700">Navigation Bar Color <span class="bg-amber-100 text-amber-700 text-[10px] px-2 py-0.5 rounded-full ml-1 font-bold">PRO</span></label>
                                    <div class="flex items-center gap-3">
                                        <input type="color" id="navigation_color" name="navigation_color" class="h-10 w-14 rounded cursor-pointer border border-slate-300 p-0.5" value="{{ old('navigation_color', '#000000') }}">
                                        <p class="text-xs text-slate-500 leading-relaxed">Warna tombol navigasi bawah (Home, Back).</p>
                                    </div>
                                    @error('navigation_color') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="splash_fade_duration" class="block mb-2 text-sm font-bold text-slate-700">Splash Fade Duration (ms) <span class="bg-amber-100 text-amber-700 text-[10px] px-2 py-0.5 rounded-full ml-1 font-bold">PRO</span></label>
                                    <input type="number" id="splash_fade_duration" name="splash_fade_duration" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" placeholder="300" value="{{ old('splash_fade_duration', 300) }}">
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

                        <!-- Perilaku & Fitur Lanjutan -->
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                                <h3 class="text-base font-bold text-slate-800"><i class="fa-solid fa-cogs text-indigo-500 mr-2"></i> Perilaku Aplikasi <span class="bg-amber-100 text-amber-700 text-[10px] px-2 py-0.5 rounded-full ml-1 font-bold">PRO</span></h3>
                                <p class="text-xs text-slate-500 mt-1">Konfigurasi native Android tingkat lanjut.</p>
                            </div>
                            <div class="p-6 space-y-6">
                                <div class="flex items-center justify-between p-4 border border-slate-200 rounded-xl bg-slate-50">
                                    <div>
                                        <h4 class="font-bold text-slate-800 text-sm">Aktifkan Push Notifications</h4>
                                        <p class="text-xs text-slate-500 mt-0.5">Izinkan aplikasi menerima notifikasi native dari server (via Web Push / OneSignal).</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="enable_notifications" class="sr-only peer" {{ old('enable_notifications') ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>

                                <div>
                                    <label for="fallback_type" class="block mb-2 text-sm font-bold text-slate-700">Fallback Engine</label>
                                    <select id="fallback_type" name="fallback_type" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                                        <option value="customtabs" {{ old('fallback_type') == 'customtabs' ? 'selected' : '' }}>Custom Tabs (Direkomendasikan, Cepat & Aman)</option>
                                        <option value="webview" {{ old('fallback_type') == 'webview' ? 'selected' : '' }}>WebView (Mode Klasik, cocok untuk web lawas)</option>
                                    </select>
                                    <p class="mt-1.5 text-xs text-slate-500">Mesin browser cadangan yang digunakan jika Chrome TWA tidak didukung di perangkat pengguna.</p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Kolom Kanan: Ikon & Keystore -->
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

                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden" x-data="{ showKeystore: false }">
                            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center cursor-pointer hover:bg-slate-100 transition" @click="showKeystore = !showKeystore">
                                <div>
                                    <h3 class="text-base font-bold text-slate-800"><i class="fa-solid fa-key text-indigo-500 mr-2"></i> Keystore / Versioning <span class="bg-amber-100 text-amber-700 text-[10px] px-2 py-0.5 rounded-full ml-1 font-bold">PRO</span></h3>
                                    <p class="text-[10px] text-slate-500 mt-1 uppercase font-bold tracking-wider">Klik untuk membuka</p>
                                </div>
                                <i class="fa-solid fa-chevron-down text-slate-400 transition-transform" :class="showKeystore ? 'rotate-180' : ''"></i>
                            </div>
                            <div class="p-6 space-y-4" x-show="showKeystore" style="display: none;">
                                <div class="bg-blue-50 border border-blue-200 text-blue-800 p-4 rounded-lg text-xs leading-relaxed mb-4">
                                    <i class="fa-solid fa-circle-info mr-1"></i>
                                    <strong>Penting:</strong> Jika Anda membuat aplikasi baru, kosongi bagian Keystore di bawah ini agar kami men-generate secara otomatis. Jika Anda memperbarui aplikasi yang sudah rilis di <b>Google Play Store</b>, Anda Wajib memasukkan alias dan password Keystore lama Anda.
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="version_name" class="block mb-2 text-xs font-bold text-slate-700">Version Name</label>
                                        <input type="text" id="version_name" name="version_name" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" placeholder="1.0.0" value="{{ old('version_name', '1.0.0') }}">
                                    </div>
                                    <div>
                                        <label for="version_code" class="block mb-2 text-xs font-bold text-slate-700">Version Code</label>
                                        <input type="number" id="version_code" name="version_code" min="1" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" placeholder="1" value="{{ old('version_code', 1) }}">
                                    </div>
                                </div>

                                <hr class="border-slate-100 my-4">

                                <div>
                                    <label for="keystore_alias" class="block mb-2 text-xs font-bold text-slate-700">Keystore Alias</label>
                                    <input type="text" id="keystore_alias" name="keystore_alias" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" placeholder="android" value="{{ old('keystore_alias') }}">
                                </div>
                                <div>
                                    <label for="keystore_password" class="block mb-2 text-xs font-bold text-slate-700">Store Password</label>
                                    <input type="text" id="keystore_password" name="keystore_password" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" placeholder="********" value="{{ old('keystore_password') }}">
                                </div>
                                <div>
                                    <label for="key_password" class="block mb-2 text-xs font-bold text-slate-700">Key Password</label>
                                    <input type="text" id="key_password" name="key_password" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" placeholder="********" value="{{ old('key_password') }}">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-bold rounded-xl text-base px-6 py-4 text-center transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fa-solid fa-rocket mr-2"></i> Mulai Build APK Pro
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </x-ui.page-layout>
@endsection