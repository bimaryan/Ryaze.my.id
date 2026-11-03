@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Pengaturan Sistem" 
            subtitle="Kelola pengaturan global untuk platform Ryaze." 
            icon="fa-solid fa-cogs">
        </x-ui.page-header>
        
        <x-ui.card class="p-6 mt-6">
            {{-- Success Toast already handled globally --}}

            <form action="{{ route('superadmin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                {{-- Profil Portfolio --}}
                <div>
                    <h3 class="text-xs font-bold text-[#525252] dark:text-white/50 mb-4 border-b border-[#E3EAF1] dark:border-[#1B2C44] pb-2 font-mono uppercase tracking-[0.2em]"><i class="fa-solid fa-user text-[#1D5B96] dark:text-[#6BA8DC] mr-2"></i> Profil Portfolio</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">Nama Lengkap</label>
                            <input type="text" name="profile_name" value="{{ $settings['profile_name'] ?? '' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="Bima Ryan Alfarizi">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">Title / Gelar</label>
                            <input type="text" name="profile_title" value="{{ $settings['profile_title'] ?? '' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="Full-Stack Developer & Software Engineer">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">Bio / Deskripsi Singkat</label>
                            <textarea name="profile_bio" rows="3" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="Ceritakan tentang diri Anda...">{{ $settings['profile_bio'] ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">Lokasi</label>
                            <input type="text" name="profile_location" value="{{ $settings['profile_location'] ?? '' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="Indonesia">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">Foto Profil</label>
                            @if(!empty($settings['profile_avatar']))
                                <img src="{{ asset('storage/' . $settings['profile_avatar']) }}" alt="Avatar" class="h-14 w-14 mb-2 rounded-full border p-1 bg-[#F4F8FC] dark:bg-[#0D1828] object-cover">
                            @endif
                            <input type="file" name="profile_avatar" accept="image/*" class="w-full border-[#C9D5E2] dark:border-[#1B2C44]   focus:border-[#1D5B96] p-2 text-sm file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-[#E8F0F9] file:text-[#1D5B96] hover:file:bg-[#C9D5E2]">
                        </div>
                    </div>
                </div>

                {{-- Identitas & SEO --}}
                <div>
                    <h3 class="text-xs font-bold text-[#525252] dark:text-white/50 mb-4 border-b border-[#E3EAF1] dark:border-[#1B2C44] pb-2 font-mono uppercase tracking-[0.2em]"><i class="fa-solid fa-id-card text-[#1D5B96] dark:text-[#6BA8DC] mr-2"></i> Identitas & SEO</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">Nama Website</label>
                            <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'Ryaze Portal' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">Deskripsi Website (SEO)</label>
                            <input type="text" name="site_description" value="{{ $settings['site_description'] ?? '' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="Portal layanan joki dan hosting terbaik...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">Upload Logo (opsional, disarankan rasio 1:1)</label>
                            @if(isset($settings['site_logo']))
                                <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="Logo" class="h-12 mb-2 rounded border p-1 bg-[#F4F8FC] dark:bg-[#0D1828] object-contain">
                            @endif
                            <input type="file" name="site_logo" accept="image/*" class="w-full border-[#C9D5E2] dark:border-[#1B2C44]   focus:border-[#1D5B96] p-2 text-sm file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-[#E8F0F9] file:text-[#1D5B96] hover:file:bg-[#C9D5E2]">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">Upload Favicon (opsional, rasio 1:1)</label>
                            @if(isset($settings['site_favicon']))
                                <img src="{{ asset('storage/' . $settings['site_favicon']) }}" alt="Favicon" class="h-8 mb-2 rounded border p-1 bg-[#F4F8FC] dark:bg-[#0D1828] object-contain">
                            @endif
                            <input type="file" name="site_favicon" accept="image/*" class="w-full border-[#C9D5E2] dark:border-[#1B2C44]   focus:border-[#1D5B96] p-2 text-sm file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-[#E8F0F9] file:text-[#1D5B96] hover:file:bg-[#C9D5E2]">
                        </div>
                    </div>
                </div>

                {{-- Kontak & Sosial Media --}}
                <div>
                    <h3 class="text-xs font-bold text-[#525252] dark:text-white/50 mb-4 border-b border-[#E3EAF1] dark:border-[#1B2C44] pb-2 font-mono uppercase tracking-[0.2em]"><i class="fa-solid fa-address-book text-[#1D5B96] dark:text-[#6BA8DC] mr-2"></i> Kontak & Sosial Media</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">Nomor WhatsApp CS</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-4 py-2.5 text-sm text-[#737373] dark:text-white/40 bg-[#F0F6FC] dark:bg-[#0D1828] border border-r-0 border-[#C9D5E2] dark:border-[#1B2C44] rounded-l-xl">+62</span>
                                <input type="text" name="contact_whatsapp" value="{{ $settings['contact_whatsapp'] ?? '' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] rounded-r-xl px-4 py-2.5 text-sm focus:ring-2 focus:border-[#1D5B96] outline-none transition" placeholder="81234567890">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">Email Dukungan</label>
                            <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="support@ryaze.my.id">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">URL GitHub</label>
                            <input type="url" name="social_github" value="{{ $settings['social_github'] ?? '' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="https://github.com/bimaryan">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">URL Instagram</label>
                            <input type="url" name="social_instagram" value="{{ $settings['social_instagram'] ?? '' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="https://instagram.com/bimaryan">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">URL LinkedIn</label>
                            <input type="url" name="social_linkedin" value="{{ $settings['social_linkedin'] ?? '' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="https://linkedin.com/in/bimaryan">
                        </div>
                    </div>
                </div>

                {{-- API & Integrasi --}}
                <div>
                    <h3 class="text-xs font-bold text-[#525252] dark:text-white/50 mb-4 border-b border-[#E3EAF1] dark:border-[#1B2C44] pb-2 font-mono uppercase tracking-[0.2em]"><i class="fa-solid fa-plug text-[#1D5B96] dark:text-[#6BA8DC] mr-2"></i> API & Integrasi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">WhatsApp API Endpoint</label>
                            <input type="text" name="wa_api_endpoint" value="{{ $settings['wa_api_endpoint'] ?? 'https://api.ryz.my.id/api/whatsapp/v1/send-message' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="https://api.ryz.my.id/api/whatsapp/v1/send-message">
                            <p class="text-xs text-[#737373] dark:text-white/40 mt-1">Kosongkan jika tidak menggunakan WA Notif.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">WhatsApp API Token</label>
                            <input type="password" name="wa_api_token" value="{{ $settings['wa_api_token'] ?? '' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="Token API">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">Nomor DANA (Transfer Manual)</label>
                            <input type="text" name="payment_dana" value="{{ $settings['payment_dana'] ?? '085157433395' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="085157433395">
                            <p class="text-xs text-[#737373] dark:text-white/40 mt-1">Digunakan untuk opsi pembayaran Transfer Manual via DANA.</p>
                        </div>
                    </div>
                </div>

                {{-- Kontrol Layanan --}}
                <div>
                    <h3 class="text-xs font-bold text-[#525252] dark:text-white/50 mb-4 border-b border-[#E3EAF1] dark:border-[#1B2C44] pb-2 font-mono uppercase tracking-[0.2em]"><i class="fa-solid fa-sliders text-[#1D5B96] dark:text-[#6BA8DC] mr-2"></i> Kontrol Layanan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">Biaya Admin / Pajak Layanan (%)</label>
                            <input type="number" name="admin_fee_percentage" value="{{ $settings['admin_fee_percentage'] ?? '0' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" min="0" max="100" step="0.1">
                            <p class="text-xs text-[#737373] dark:text-white/40 mt-1">Biaya admin yang ditambahkan ke setiap tagihan pembayaran (%).</p>
                        </div>

                        <div x-data="{
                            tags: '{{ $settings['available_frameworks'] ?? 'html,php,laravel,react,nextjs,node,vue' }}'.split(',').filter(t => t.trim() !== ''),
                            newTag: '',
                            addTag() {
                                let tag = this.newTag.trim().toLowerCase().replace(/[^a-z0-9]/g, '');
                                if (tag && !this.tags.includes(tag)) {
                                    this.tags.push(tag);
                                }
                                this.newTag = '';
                            },
                            removeTag(index) {
                                this.tags.splice(index, 1);
                            }
                        }">
                            <input type="hidden" name="available_frameworks" :value="tags.join(',')">
                            
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">Pilihan Framework Tersedia</label>
                            
                            <div class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44]  p-2 flex flex-wrap gap-2 focus-within:border-[#1D5B96] transition-all min-h-[46px]">
                                <template x-for="(tag, index) in tags" :key="index">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1  text-sm font-medium bg-[#E8F0F9] dark:bg-[#1D5B96]/20 text-[#1D5B96] dark:text-[#6BA8DC]">
                                        <span x-text="tag"></span>
                                        <button type="button" @click="removeTag(index)" class="ui-icon-btn ui-icon-btn-primary">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </span>
                                </template>
                                
                                <input type="text" x-model="newTag" @keydown.enter.prevent="addTag()" @keydown.comma.prevent="addTag()" @keydown.space.prevent="addTag()" @keydown.backspace="if(newTag === '' && tags.length > 0) removeTag(tags.length - 1)" placeholder="Ketik lalu Enter..." class="flex-1 bg-transparent border-none outline-none focus:ring-0 text-sm min-w-[120px] p-1">
                            </div>
                            <p class="text-xs text-[#737373] dark:text-white/40 mt-1">Ketik nama framework (misal: svelte) lalu tekan <kbd class="px-1 py-0.5 bg-[#F0F6FC] dark:bg-[#0D1828] border border-[#C9D5E2] dark:border-[#1B2C44] rounded text-[#525252] dark:text-white/60 text-[10px] font-mono">Enter</kbd> atau <kbd class="px-1 py-0.5 bg-[#F0F6FC] dark:bg-[#0D1828] border border-[#C9D5E2] dark:border-[#1B2C44] rounded text-[#525252] dark:text-white/60 text-[10px] font-mono">Spasi</kbd>.</p>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="p-4 bg-[#E8F0F9] dark:bg-[#1D5B96]/10 border border-[#C9D5E2] dark:border-[#1D5B96]/40 ">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-bold text-[#1D5B96] dark:text-[#6BA8DC] text-sm">Buka Pendaftaran Akun</h4>
                                        <p class="text-xs text-[#1D5B96] dark:text-[#6BA8DC] mt-1">Matikan untuk menutup registrasi pengguna baru jika server penuh.</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="enable_registration" value="0">
                                        <input type="checkbox" name="enable_registration" value="1" class="sr-only peer" {{ (!isset($settings['enable_registration']) || $settings['enable_registration'] == '1') ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-[#E3EAF1] dark:bg-[#1B2C44] peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#1D5B96]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[#C9D5E2] after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#1D5B96]"></div>
                                    </label>
                                </div>
                            </div>

                            <div class="p-4 bg-rose-50 dark:bg-rose-500/10 border border-rose-100 dark:border-rose-500/30 ">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-bold text-rose-800 dark:text-rose-200 text-sm">Mode Pemeliharaan (Maintenance Mode)</h4>
                                        <p class="text-xs text-rose-600 dark:text-rose-300 mt-1">Aktifkan untuk memblokir akses ke fitur klien (hanya Admin yang bisa mengakses).</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="maintenance_mode" value="0">
                                        <input type="checkbox" name="maintenance_mode" value="1" class="sr-only peer" {{ (isset($settings['maintenance_mode']) && $settings['maintenance_mode'] == '1') ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-rose-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-rose-500/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-rose-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Harga & Langganan Hosting --}}
                <div>
                    <h3 class="text-xs font-bold text-[#525252] dark:text-white/50 mb-4 border-b border-[#E3EAF1] dark:border-[#1B2C44] pb-2 font-mono uppercase tracking-[0.2em]"><i class="fa-solid fa-tags text-[#1D5B96] dark:text-[#6BA8DC] mr-2"></i> Harga Langganan Hosting (Paket)</h3>
                    <p class="text-xs text-[#737373] dark:text-white/40 mb-4">Perubahan di sini otomatis sync ke landing page & halaman pilih paket.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Free -->
                        <div class="bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463] p-4  flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-3 border-b border-[#C9D5E2] dark:border-[#1B2C44] pb-2">
                                    <h4 class="font-bold text-[#0F1A2A] dark:text-white">Paket Free</h4>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="plan_free_active" value="0">
                                        <input type="checkbox" name="plan_free_active" value="1" class="sr-only peer" {{ (!isset($settings['plan_free_active']) || $settings['plan_free_active'] == '1') ? 'checked' : '' }}>
                                        <div class="w-9 h-5 bg-[#E3EAF1] dark:bg-[#1B2C44] peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#1D5B96]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[#C9D5E2] after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#1D5B96]"></div>
                                    </label>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Storage (MB)</label>
                                        <input type="number" name="plan_free_storage" value="{{ $settings['plan_free_storage'] ?? '256' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="Contoh: 1024 (dalam MB)">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Maks. Project (-1 = unlimited)</label>
                                        <input type="number" name="plan_free_max_projects" value="{{ $settings['plan_free_max_projects'] ?? '1' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" min="-1">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Fitur (satu per baris)</label>
                                        <textarea name="plan_free_features" rows="4" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="256 MB Storage&#10;Maks. 1 Project">{{ $settings['plan_free_features'] ?? "256 MB Storage\nMaks. 1 Project\n1 MySQL Database\nSubdomain Bawaan\nPrioritas Support" }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Starter -->
                        <div class="bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463] p-4 ">
                            <div class="flex items-center justify-between mb-3 border-b border-[#C9D5E2] dark:border-[#1B2C44] pb-2">
                                <h4 class="font-bold text-[#0F1A2A] dark:text-white">Paket Starter</h4>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="plan_starter_active" value="0">
                                    <input type="checkbox" name="plan_starter_active" value="1" class="sr-only peer" {{ (!isset($settings['plan_starter_active']) || $settings['plan_starter_active'] == '1') ? 'checked' : '' }}>
                                    <div class="w-9 h-5 bg-[#E3EAF1] dark:bg-[#1B2C44] peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#1D5B96]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[#C9D5E2] after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#1D5B96]"></div>
                                </label>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Harga Normal (Rp)</label>
                                    <input type="number" name="plan_starter_price" value="{{ $settings['plan_starter_price'] ?? '15000' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" min="0" step="1000">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Harga Promo (Rp)</label>
                                    <input type="number" name="plan_starter_promo" value="{{ $settings['plan_starter_promo'] ?? '' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" min="0" step="1000" placeholder="Kosongkan jika tak ada promo">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Harga Normal Tahunan (Rp)</label>
                                        <input type="number" name="plan_starter_price_yearly" value="{{ $settings['plan_starter_price_yearly'] ?? '150000' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" min="0" step="1000">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Harga Promo Tahunan (Rp)</label>
                                        <input type="number" name="plan_starter_promo_yearly" value="{{ $settings['plan_starter_promo_yearly'] ?? '' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" min="0" step="1000" placeholder="Kosongkan jika tak ada">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Storage (MB)</label>
                                        <input type="number" name="plan_starter_storage" value="{{ $settings['plan_starter_storage'] ?? '1024' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="Contoh: 1024 (dalam MB)">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Maks. Project (-1 = unlimited)</label>
                                    <input type="number" name="plan_starter_max_projects" value="{{ $settings['plan_starter_max_projects'] ?? '3' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" min="-1">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Fitur (satu per baris)</label>
                                    <textarea name="plan_starter_features" rows="4" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="1 GB Storage&#10;Maks. 3 Project">{{ $settings['plan_starter_features'] ?? "1 GB Storage\nMaks. 3 Project\nMySQL & PostgreSQL\nSubdomain Bawaan\nPrioritas Support" }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Pro -->
                        <div class="bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463] p-4 ">
                            <div class="flex items-center justify-between mb-3 border-b border-[#C9D5E2] dark:border-[#1B2C44] pb-2">
                                <h4 class="font-bold text-[#0F1A2A] dark:text-white">Paket Pro</h4>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="plan_pro_active" value="0">
                                    <input type="checkbox" name="plan_pro_active" value="1" class="sr-only peer" {{ (!isset($settings['plan_pro_active']) || $settings['plan_pro_active'] == '1') ? 'checked' : '' }}>
                                    <div class="w-9 h-5 bg-[#E3EAF1] dark:bg-[#1B2C44] peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#1D5B96]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[#C9D5E2] after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#1D5B96]"></div>
                                </label>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Harga Normal (Rp)</label>
                                    <input type="number" name="plan_pro_price" value="{{ $settings['plan_pro_price'] ?? '30000' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" min="0" step="1000">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Harga Promo (Rp)</label>
                                    <input type="number" name="plan_pro_promo" value="{{ $settings['plan_pro_promo'] ?? '' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" min="0" step="1000" placeholder="Kosongkan jika tak ada promo">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Harga Normal Tahunan (Rp)</label>
                                        <input type="number" name="plan_pro_price_yearly" value="{{ $settings['plan_pro_price_yearly'] ?? '300000' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" min="0" step="1000">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Harga Promo Tahunan (Rp)</label>
                                        <input type="number" name="plan_pro_promo_yearly" value="{{ $settings['plan_pro_promo_yearly'] ?? '' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" min="0" step="1000" placeholder="Kosongkan jika tak ada">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Storage (MB)</label>
                                        <input type="number" name="plan_pro_storage" value="{{ $settings['plan_pro_storage'] ?? '3072' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="Contoh: 1024 (dalam MB)">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Maks. Project (-1 = unlimited)</label>
                                    <input type="number" name="plan_pro_max_projects" value="{{ $settings['plan_pro_max_projects'] ?? '10' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" min="-1">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Fitur (satu per baris)</label>
                                    <textarea name="plan_pro_features" rows="4" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="3 GB Storage&#10;Maks. 10 Project">{{ $settings['plan_pro_features'] ?? "3 GB Storage\nMaks. 10 Project\nMySQL, PostgreSQL & Redis\nSubdomain Bawaan\nPrioritas Support" }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Business -->
                        <div class="bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463] p-4 ">
                            <div class="flex items-center justify-between mb-3 border-b border-[#C9D5E2] dark:border-[#1B2C44] pb-2">
                                <h4 class="font-bold text-[#0F1A2A] dark:text-white">Paket Business</h4>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="plan_business_active" value="0">
                                    <input type="checkbox" name="plan_business_active" value="1" class="sr-only peer" {{ (!isset($settings['plan_business_active']) || $settings['plan_business_active'] == '1') ? 'checked' : '' }}>
                                    <div class="w-9 h-5 bg-[#E3EAF1] dark:bg-[#1B2C44] peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#1D5B96]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[#C9D5E2] after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#1D5B96]"></div>
                                </label>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Harga Normal (Rp)</label>
                                    <input type="number" name="plan_business_price" value="{{ $settings['plan_business_price'] ?? '75000' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" min="0" step="1000">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Harga Promo (Rp)</label>
                                    <input type="number" name="plan_business_promo" value="{{ $settings['plan_business_promo'] ?? '' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" min="0" step="1000" placeholder="Kosongkan jika tak ada promo">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Harga Normal Tahunan (Rp)</label>
                                        <input type="number" name="plan_business_price_yearly" value="{{ $settings['plan_business_price_yearly'] ?? '750000' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" min="0" step="1000">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Harga Promo Tahunan (Rp)</label>
                                        <input type="number" name="plan_business_promo_yearly" value="{{ $settings['plan_business_promo_yearly'] ?? '' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" min="0" step="1000" placeholder="Kosongkan jika tak ada">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Storage (MB)</label>
                                        <input type="number" name="plan_business_storage" value="{{ $settings['plan_business_storage'] ?? '10240' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="Contoh: 1024 (dalam MB)">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Maks. Project (-1 = unlimited)</label>
                                    <input type="number" name="plan_business_max_projects" value="{{ $settings['plan_business_max_projects'] ?? '-1' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" min="-1">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Fitur (satu per baris)</label>
                                    <textarea name="plan_business_features" rows="4" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-3 py-2 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="10 GB Storage&#10;Project Unlimited">{{ $settings['plan_business_features'] ?? "10 GB Storage\nProject Unlimited\nSemua Database\nSubdomain Bawaan\nPrioritas Support" }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Otomasi Blog AI --}}
                <div>
                    <h3 class="text-xs font-bold text-[#525252] dark:text-white/50 mb-4 border-b border-[#E3EAF1] dark:border-[#1B2C44] pb-2 font-mono uppercase tracking-[0.2em]"><i class="fa-solid fa-wand-magic-sparkles text-[#1D5B96] dark:text-[#6BA8DC] mr-2"></i>Otomasi Blog AI</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">Daftar Topik</label>
                            <textarea name="blog_ai_topics" rows="4" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none focus:border-[#1D5B96] outline-none transition" placeholder="Satu topik per baris, misalnya:&#10;Panduan deploy website Laravel&#10;Cara memilih hosting untuk UMKM">{{ $settings['blog_ai_topics'] ?? '' }}</textarea>
                            <p class="text-xs text-[#737373] dark:text-white/40 mt-1">Sistem memilih satu topik secara acak dan menghasilkan artikel beserta cover image.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">Kategori Default</label>
                            <select name="blog_ai_category_id" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none">
                                <option value="">Tanpa kategori</option>
                                @foreach ($articleCategories as $category)
                                    <option value="{{ $category->id }}" {{ ($settings['blog_ai_category_id'] ?? '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">Frekuensi</label>
                            <select name="blog_ai_frequency" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none">
                                <option value="daily" {{ ($settings['blog_ai_frequency'] ?? 'daily') === 'daily' ? 'selected' : '' }}>Setiap hari</option>
                                <option value="weekly" {{ ($settings['blog_ai_frequency'] ?? '') === 'weekly' ? 'selected' : '' }}>Setiap minggu</option>
                            </select>
                            <p class="text-xs text-[#737373] dark:text-white/40 mt-1">Scheduler mengecek setiap jam dan membuat satu artikel saat jadwalnya tiba.</p>
                        </div>
                        <div class="p-4 bg-[#E8F0F9] dark:bg-[#1D5B96]/10 border border-[#C9D5E2] dark:border-[#1D5B96]/40 ">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h4 class="font-bold text-[#1D5B96] dark:text-[#6BA8DC] text-sm">Aktifkan Otomasi</h4>
                                    <p class="text-xs text-[#1D5B96] dark:text-[#6BA8DC] mt-1">Butuh OPENAI_API_KEY dan queue worker yang aktif.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="blog_ai_enabled" value="0">
                                    <input type="checkbox" name="blog_ai_enabled" value="1" class="sr-only peer" {{ ($settings['blog_ai_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-[#E3EAF1] dark:bg-[#1B2C44] peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#1D5B96]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[#C9D5E2] after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#1D5B96]"></div>
                                </label>
                            </div>
                        </div>
                        <div class="p-4 bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/30 ">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h4 class="font-bold text-amber-800 dark:text-amber-200 text-sm">Auto-publish</h4>
                                    <p class="text-xs text-amber-700 dark:text-amber-300 mt-1">Matikan agar hasil otomatis selalu masuk draft untuk review.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="blog_ai_auto_publish" value="0">
                                    <input type="checkbox" name="blog_ai_auto_publish" value="1" class="sr-only peer" {{ ($settings['blog_ai_auto_publish'] ?? '0') === '1' ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-[#E3EAF1] dark:bg-[#1B2C44] peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-amber-500/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:border-amber-300 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[#C9D5E2] after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- API & Integrasi 1Panel --}}
                <div>
                    <h3 class="text-xs font-bold text-[#525252] dark:text-white/50 mb-4 border-b border-[#E3EAF1] dark:border-[#1B2C44] pb-2 font-mono uppercase tracking-[0.2em]"><i class="fa-solid fa-server text-[#1D5B96] dark:text-[#6BA8DC] mr-2"></i> API & Integrasi 1Panel</h3>
                    <p class="text-xs text-[#737373] dark:text-white/40 mb-4">Integrasi ini digunakan untuk memantau status kesehatan server langsung dari 1Panel.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">1Panel API URL</label>
                            <input type="url" name="1panel_url" value="{{ $settings['1panel_url'] ?? '' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="https://192.168.1.1:10086">
                            <p class="text-[11px] text-[#737373] dark:text-white/40 mt-1">URL panel tanpa trailing slash (contoh: https://ip:port)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">1Panel API Key / Token</label>
                            <input type="text" name="1panel_api_key" value="{{ $settings['1panel_api_key'] ?? '' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="Token dari 1Panel API">
                            <p class="text-[11px] text-[#737373] dark:text-white/40 mt-1">Dapatkan di 1Panel > Pengaturan Panel > API.</p>
                        </div>
                    </div>
                </div>


                {{-- Integrasi Eksternal --}}
                <div>
                    <h3 class="text-xs font-bold text-[#525252] dark:text-white/50 mb-4 border-b border-[#E3EAF1] dark:border-[#1B2C44] pb-2 font-mono uppercase tracking-[0.2em]"><i class="fa-solid fa-plug text-[#1D5B96] dark:text-[#6BA8DC] mr-2"></i> Integrasi Eksternal</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">Pakasir Server Key</label>
                            <input type="password" name="pakasir_server_key" value="{{ $settings['pakasir_server_key'] ?? '' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="Pakasir API Server Key">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-1">Google Analytics ID</label>
                            <input type="text" name="google_analytics_id" value="{{ $settings['google_analytics_id'] ?? '' }}" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="G-XXXXXXXXXX">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-[#E3EAF1] dark:border-[#1B2C44] flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-[#1D5B96] text-white  font-medium hover:bg-[#2A6FAD]  transition">
                        <i class="fa-solid fa-save mr-2"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page-layout>

    <script>
        function parseToMB(val) {
            val = val.toString().trim().toLowerCase().replace(/\s+/g, '');
            var num = parseFloat(val);
            if (isNaN(num)) return 0;
            if (val.match(/tb$/)) return Math.round(num * 1048576);
            if (val.match(/gb$/)) return Math.round(num * 1024);
            if (val.match(/mb$/)) return Math.round(num);
            return Math.round(num);
        }
        function formatMB(mb) {
            if (mb == 0) return '0 MB';
            if (mb < 1024) return mb + ' MB';
            if (mb < 1048576) return (mb / 1024).toFixed(2).replace(/\.?0+$/, '') + ' GB';
            return (mb / 1048576).toFixed(2).replace(/\.?0+$/, '') + ' TB';
        }
        document.querySelectorAll('input[name$="_storage"]').forEach(function(input) {
            var hint = document.createElement('p');
            hint.className = 'text-xs text-[#1D5B96] dark:text-[#6BA8DC] mt-1 font-medium';
            input.parentNode.appendChild(hint);
            function update() {
                var mb = parseToMB(input.value);
                hint.textContent = '= ' + formatMB(mb) + ' (' + mb + ' MB)';
            }
            input.addEventListener('input', update);
            update();
        });
        document.querySelector('form').addEventListener('submit', function() {
            document.querySelectorAll('input[name$="_storage"]').forEach(function(input) {
                input.value = parseToMB(input.value);
            });
        });
    </script>
@endsection
