@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Buat Aplikasi Android Baru"
            eyebrow="Android App Builder"
            subtitle="Isi detail di bawah ini untuk memulai proses build website Anda menjadi .apk"
            icon="fa-solid fa-hammer"
            iconColor="indigo">
            <x-slot:actions>
                <a href="{{ route('user_hosting.apk.index') }}" class="ui-btn ui-btn-md ui-btn-secondary">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        @if (! auth()->user()->hasActiveHostingSubscription())
            <div class="ui-panel p-6">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-start">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center border border-amber-200 bg-amber-50 text-amber-600 dark:border-amber-800/50 dark:bg-amber-950/40 dark:text-amber-400">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="ui-eyebrow">Fitur Eksklusif Pro</p>
                        <h2 class="ui-display mt-1 text-lg">Web to APK Builder butuh langganan aktif</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[#525252] dark:text-white/60">
                            Fitur Web to APK Builder (Bubblewrap) memerlukan sumber daya server yang tinggi.
                            Anda harus memiliki langganan Hosting aktif untuk menggunakan fitur ini.
                        </p>
                    </div>
                    <a href="{{ route('user_hosting.billing') }}" class="ui-btn ui-btn-md ui-btn-primary shrink-0">
                        <i class="fa-solid fa-rocket"></i> Berlangganan Sekarang
                    </a>
                </div>
            </div>
        @else
            <form action="{{ route('user_hosting.apk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                    <div class="space-y-5 lg:col-span-2">
                        <div class="ui-panel">
                            <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                                <div>
                                    <h3 class="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#0F1A2A] dark:text-white">
                                        <i class="fa-solid fa-circle-info mr-2 text-[#1D5B96] dark:text-[#6BA8DC]"></i> Informasi Dasar
                                    </h3>
                                    <p class="ui-eyebrow-muted mt-1.5">Detail utama aplikasi Android Anda.</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <label for="app_name" class="ui-label">Nama Aplikasi <span class="text-rose-500">*</span></label>
                                    <input type="text" id="app_name" name="app_name" class="ui-field" placeholder="Contoh: Toko Online Saya" required value="{{ old('app_name') }}">
                                    @error('app_name') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="app_url" class="ui-label">URL Website <span class="text-rose-500">*</span></label>
                                    <input type="url" id="app_url" name="app_url" class="ui-field" placeholder="https://example.com" required value="{{ old('app_url') }}">
                                    <p class="mt-1.5 text-[11px] text-[#525252] dark:text-white/45">
                                        <i class="fa-solid fa-lock mr-1 text-emerald-500"></i> Wajib menggunakan protokol HTTPS.
                                    </p>
                                    @error('app_url') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="package_name" class="ui-label">Package Name <span class="text-rose-500">*</span></label>
                                    <input type="text" id="package_name" name="package_name" class="ui-field font-mono" placeholder="com.example.app" required value="{{ old('package_name') }}">
                                    <p class="mt-1.5 text-[11px] text-[#525252] dark:text-white/45">
                                        Identitas unik aplikasi di Android. Format: <code class="ui-code">com.namaperusahaan.namaapp</code>
                                    </p>
                                    @error('package_name') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="ui-panel">
                            <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                                <div>
                                    <h3 class="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#0F1A2A] dark:text-white">
                                        <i class="fa-solid fa-palette mr-2 text-[#1D5B96] dark:text-[#6BA8DC]"></i> Tampilan &amp; Desain
                                    </h3>
                                    <p class="ui-eyebrow-muted mt-1.5">Sesuaikan warna dan perilaku layar aplikasi.</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">
                                <div>
                                    <label for="theme_color" class="ui-label">Theme Color</label>
                                    <div class="flex items-center gap-3">
                                        <input type="color" id="theme_color" name="theme_color" class="h-10 w-14 cursor-pointer border border-[#C9D5E2] bg-white p-0.5 dark:border-[#2A4463] dark:bg-[#0F1D30]" value="{{ old('theme_color', '#FFFFFF') }}">
                                        <p class="text-[11px] leading-relaxed text-[#525252] dark:text-white/45">Warna status bar HP saat aplikasi dibuka.</p>
                                    </div>
                                    @error('theme_color') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="background_color" class="ui-label">Background Color</label>
                                    <div class="flex items-center gap-3">
                                        <input type="color" id="background_color" name="background_color" class="h-10 w-14 cursor-pointer border border-[#C9D5E2] bg-white p-0.5 dark:border-[#2A4463] dark:bg-[#0F1D30]" value="{{ old('background_color', '#FFFFFF') }}">
                                        <p class="text-[11px] leading-relaxed text-[#525252] dark:text-white/45">Warna latar belakang <i>Splash Screen</i>.</p>
                                    </div>
                                    @error('background_color') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="navigation_color" class="ui-label">
                                        Navigation Bar Color <span class="ui-badge ui-badge-warning ml-1">PRO</span>
                                    </label>
                                    <div class="flex items-center gap-3">
                                        <input type="color" id="navigation_color" name="navigation_color" class="h-10 w-14 cursor-pointer border border-[#C9D5E2] bg-white p-0.5 dark:border-[#2A4463] dark:bg-[#0F1D30]" value="{{ old('navigation_color', '#000000') }}">
                                        <p class="text-[11px] leading-relaxed text-[#525252] dark:text-white/45">Warna tombol navigasi bawah (Home, Back).</p>
                                    </div>
                                    @error('navigation_color') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="splash_fade_duration" class="ui-label">
                                        Splash Fade Duration (ms) <span class="ui-badge ui-badge-warning ml-1">PRO</span>
                                    </label>
                                    <input type="number" id="splash_fade_duration" name="splash_fade_duration" class="ui-field" placeholder="300" value="{{ old('splash_fade_duration', 300) }}">
                                </div>

                                <div>
                                    <label for="display_mode" class="ui-label">Display Mode</label>
                                    <select id="display_mode" name="display_mode" class="ui-field">
                                        <option value="standalone" {{ old('display_mode') == 'standalone' ? 'selected' : '' }}>Standalone (Normal)</option>
                                        <option value="fullscreen" {{ old('display_mode') == 'fullscreen' ? 'selected' : '' }}>Fullscreen (Tanpa Status Bar)</option>
                                        <option value="minimal-ui" {{ old('display_mode') == 'minimal-ui' ? 'selected' : '' }}>Minimal UI</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="orientation" class="ui-label">Orientasi Layar</label>
                                    <select id="orientation" name="orientation" class="ui-field">
                                        <option value="default" {{ old('orientation') == 'default' ? 'selected' : '' }}>Default (Sesuai Rotasi HP)</option>
                                        <option value="portrait" {{ old('orientation') == 'portrait' ? 'selected' : '' }}>Portrait (Selalu Berdiri)</option>
                                        <option value="landscape" {{ old('orientation') == 'landscape' ? 'selected' : '' }}>Landscape (Selalu Mendatar)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="ui-panel">
                            <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                                <div>
                                    <h3 class="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#0F1A2A] dark:text-white">
                                        <i class="fa-solid fa-cogs mr-2 text-[#1D5B96] dark:text-[#6BA8DC]"></i> Perilaku Aplikasi
                                        <span class="ui-badge ui-badge-warning ml-1">PRO</span>
                                    </h3>
                                    <p class="ui-eyebrow-muted mt-1.5">Konfigurasi native Android tingkat lanjut.</p>
                                </div>
                            </div>
                            <div class="space-y-5 p-6">
                                <div class="flex flex-col gap-4 border border-[#E3EAF1] p-4 dark:border-[#2A4463] sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <h4 class="text-sm font-semibold text-[#0F1A2A] dark:text-white">Aktifkan Push Notifications</h4>
                                        <p class="mt-0.5 text-xs text-[#525252] dark:text-white/50">Izinkan aplikasi menerima notifikasi native dari server (via Web Push / OneSignal).</p>
                                    </div>
                                    <label class="relative inline-flex cursor-pointer items-center">
                                        <input type="checkbox" name="enable_notifications" class="peer sr-only" {{ old('enable_notifications') ? 'checked' : '' }}>
                                        <div class="ui-switch"></div>
                                    </label>
                                </div>

                                <div>
                                    <label for="fallback_type" class="ui-label">Fallback Engine</label>
                                    <select id="fallback_type" name="fallback_type" class="ui-field">
                                        <option value="customtabs" {{ old('fallback_type') == 'customtabs' ? 'selected' : '' }}>Custom Tabs (Direkomendasikan, Cepat &amp; Aman)</option>
                                        <option value="webview" {{ old('fallback_type') == 'webview' ? 'selected' : '' }}>WebView (Mode Klasik, cocok untuk web lawas)</option>
                                    </select>
                                    <p class="mt-1.5 text-[11px] text-[#525252] dark:text-white/45">Mesin browser cadangan yang digunakan jika Chrome TWA tidak didukung di perangkat pengguna.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div class="ui-panel">
                            <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                                <h3 class="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#0F1A2A] dark:text-white">
                                    <i class="fa-solid fa-image mr-2 text-[#1D5B96] dark:text-[#6BA8DC]"></i> Ikon Aplikasi
                                </h3>
                            </div>
                            <div class="p-6">
                                <label for="icon" class="flex h-40 w-full cursor-pointer flex-col items-center justify-center border-2 border-dashed border-[#C9D5E2] bg-[#FAFBFD] transition hover:border-[#1D5B96] hover:bg-[#E8F0F9] dark:border-[#2A4463] dark:bg-[#0F1D30] dark:hover:border-[#6BA8DC] dark:hover:bg-[#1A2F49]">
                                    <div class="flex flex-col items-center justify-center px-4 pb-6 pt-5 text-center">
                                        <i class="fa-solid fa-cloud-arrow-up mb-3 text-3xl text-[#8A9BAE] dark:text-white/40"></i>
                                        <p class="mb-1 text-sm text-[#525252] dark:text-white/60"><span class="font-semibold text-[#1D5B96] dark:text-[#6BA8DC]">Klik untuk upload</span></p>
                                        <p class="text-xs text-[#8A9BAE] dark:text-white/40">PNG atau JPG (Min. 512x512px)</p>
                                    </div>
                                    <input type="file" id="icon" name="icon" accept="image/png, image/jpeg" class="hidden" />
                                </label>
                                @error('icon') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="ui-panel" x-data="{ showKeystore: false }">
                            <button type="button" class="ui-panel-head w-full bg-[#F4F8FC] text-left transition hover:bg-[#F0F6FC] dark:bg-[#12233A] dark:hover:bg-[#1A2F49]" @click="showKeystore = !showKeystore">
                                <div class="min-w-0">
                                    <h3 class="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#0F1A2A] dark:text-white">
                                        <i class="fa-solid fa-key mr-2 text-[#1D5B96] dark:text-[#6BA8DC]"></i> Keystore / Versioning
                                        <span class="ui-badge ui-badge-warning ml-1">PRO</span>
                                    </h3>
                                    <p class="ui-eyebrow-muted mt-1.5">Klik untuk membuka</p>
                                </div>
                                <i class="fa-solid fa-chevron-down text-[#8A9BAE] transition-transform dark:text-white/40" :class="showKeystore ? 'rotate-180' : ''"></i>
                            </button>
                            <div class="space-y-4 p-6" x-show="showKeystore" style="display: none;">
                                <div class="border border-[#C9D5E2] bg-[#E8F0F9] p-4 text-xs leading-relaxed text-[#1D5B96] dark:border-[#1D5B96]/40 dark:bg-[#1D5B96]/15 dark:text-[#6BA8DC]">
                                    <i class="fa-solid fa-circle-info mr-1"></i>
                                    <strong>Penting:</strong> Jika Anda membuat aplikasi baru, kosongkan bagian Keystore di bawah ini agar kami men-generate secara otomatis. Jika Anda memperbarui aplikasi yang sudah rilis di <b>Google Play Store</b>, Anda wajib memasukkan alias dan password Keystore lama Anda.
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="version_name" class="ui-label">Version Name</label>
                                        <input type="text" id="version_name" name="version_name" class="ui-field" placeholder="1.0.0" value="{{ old('version_name', '1.0.0') }}">
                                    </div>
                                    <div>
                                        <label for="version_code" class="ui-label">Version Code</label>
                                        <input type="number" id="version_code" name="version_code" min="1" class="ui-field" placeholder="1" value="{{ old('version_code', 1) }}">
                                    </div>
                                </div>

                                <hr class="border-[#E3EAF1] dark:border-[#2A4463]">

                                <div>
                                    <label for="keystore_alias" class="ui-label">Keystore Alias</label>
                                    <input type="text" id="keystore_alias" name="keystore_alias" class="ui-field" placeholder="android" value="{{ old('keystore_alias') }}">
                                </div>
                                <div>
                                    <label for="keystore_password" class="ui-label">Store Password</label>
                                    <input type="text" id="keystore_password" name="keystore_password" class="ui-field" placeholder="********" value="{{ old('keystore_password') }}">
                                </div>
                                <div>
                                    <label for="key_password" class="ui-label">Key Password</label>
                                    <input type="text" id="key_password" name="key_password" class="ui-field" placeholder="********" value="{{ old('key_password') }}">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="ui-btn ui-btn-primary w-full py-3.5">
                            <i class="fa-solid fa-rocket"></i> Mulai Build APK Pro
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </x-ui.page-layout>
@endsection
