@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Pengaturan Sistem" 
            subtitle="Kelola pengaturan global untuk platform Ryaze." 
            icon="fa-solid fa-cogs">
        </x-ui.page-header>
        
        <x-ui.card class="p-6 mt-6">

            <form action="{{ route('superadmin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                {{-- Identitas & SEO --}}
                <div>
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2"><i class="fa-solid fa-id-card text-indigo-500 mr-2"></i> Identitas & SEO</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Website</label>
                            <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'Ryaze Portal' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi Website (SEO)</label>
                            <input type="text" name="site_description" value="{{ $settings['site_description'] ?? '' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="Portal layanan joki dan hosting terbaik...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Upload Logo (opsional, disarankan rasio 1:1)</label>
                            @if(isset($settings['site_logo']))
                                <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="Logo" class="h-12 mb-2 rounded border p-1 bg-slate-50 object-contain">
                            @endif
                            <input type="file" name="site_logo" accept="image/*" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Upload Favicon (opsional, rasio 1:1)</label>
                            @if(isset($settings['site_favicon']))
                                <img src="{{ asset('storage/' . $settings['site_favicon']) }}" alt="Favicon" class="h-8 mb-2 rounded border p-1 bg-slate-50 object-contain">
                            @endif
                            <input type="file" name="site_favicon" accept="image/*" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                    </div>
                </div>

                {{-- Kontak & Sosial Media --}}
                <div>
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2"><i class="fa-solid fa-address-book text-indigo-500 mr-2"></i> Kontak & Sosial Media</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nomor WhatsApp CS</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-4 py-2.5 text-sm text-slate-500 bg-slate-100 border border-r-0 border-slate-200 rounded-l-xl">+62</span>
                                <input type="text" name="contact_whatsapp" value="{{ $settings['contact_whatsapp'] ?? '' }}" class="w-full bg-slate-50 border border-slate-200 rounded-r-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="81234567890">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Email Dukungan</label>
                            <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="support@ryaze.my.id">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">URL GitHub</label>
                            <input type="url" name="social_github" value="{{ $settings['social_github'] ?? '' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="https://github.com/bimaryan">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">URL Instagram</label>
                            <input type="url" name="social_instagram" value="{{ $settings['social_instagram'] ?? '' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="https://instagram.com/bimaryan">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">URL LinkedIn</label>
                            <input type="url" name="social_linkedin" value="{{ $settings['social_linkedin'] ?? '' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="https://linkedin.com/in/bimaryan">
                        </div>
                    </div>
                </div>

                {{-- Kontrol Layanan --}}
                <div>
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2"><i class="fa-solid fa-sliders text-indigo-500 mr-2"></i> Kontrol Layanan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Biaya Admin / Pajak Layanan (%)</label>
                            <input type="number" name="admin_fee_percentage" value="{{ $settings['admin_fee_percentage'] ?? '0' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" min="0" max="100" step="0.1">
                            <p class="text-xs text-slate-500 mt-1">Biaya admin yang ditambahkan ke setiap tagihan pembayaran (%).</p>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="p-4 bg-indigo-50 border border-indigo-100 rounded-xl">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-bold text-indigo-800 text-sm">Buka Pendaftaran Akun</h4>
                                        <p class="text-xs text-indigo-600 mt-1">Matikan untuk menutup registrasi pengguna baru jika server penuh.</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="enable_registration" value="0">
                                        <input type="checkbox" name="enable_registration" value="1" class="sr-only peer" {{ (!isset($settings['enable_registration']) || $settings['enable_registration'] == '1') ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>
                            </div>

                            <div class="p-4 bg-rose-50 border border-rose-100 rounded-xl">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-bold text-rose-800 text-sm">Mode Pemeliharaan (Maintenance Mode)</h4>
                                        <p class="text-xs text-rose-600 mt-1">Aktifkan untuk memblokir akses ke fitur klien (hanya Admin yang bisa mengakses).</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="maintenance_mode" value="0">
                                        <input type="checkbox" name="maintenance_mode" value="1" class="sr-only peer" {{ (isset($settings['maintenance_mode']) && $settings['maintenance_mode'] == '1') ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-rose-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-rose-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-rose-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Integrasi Eksternal --}}
                <div>
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2"><i class="fa-solid fa-plug text-indigo-500 mr-2"></i> Integrasi Eksternal</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Pakasir Server Key</label>
                            <input type="password" name="pakasir_server_key" value="{{ $settings['pakasir_server_key'] ?? '' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="Pakasir API Server Key">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Google Analytics ID</label>
                            <input type="text" name="google_analytics_id" value="{{ $settings['google_analytics_id'] ?? '' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="G-XXXXXXXXXX">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 shadow-sm transition">
                        <i class="fa-solid fa-save mr-2"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page-layout>
@endsection
