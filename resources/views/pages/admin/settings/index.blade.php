@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Pengaturan Sistem" 
            subtitle="Kelola pengaturan global untuk platform Ryaze." 
            icon="fa-solid fa-cogs">
        </x-ui.page-header>
        
        <x-ui.card class="p-6 mt-6">
            @if(session('success'))
                <div class="mb-4 p-4 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('superadmin.settings.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2">Konfigurasi 1Panel</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">URL 1Panel</label>
                            <input type="text" name="onepanel_url" value="{{ $settings['onepanel_url'] ?? '' }}" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2.5" placeholder="https://panel.yourdomain.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">API Key 1Panel</label>
                            <input type="password" name="onepanel_api_key" value="{{ $settings['onepanel_api_key'] ?? '' }}" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2.5" placeholder="Masukkan API Key">
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2">Pengaturan Umum</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Website</label>
                            <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'Ryaze' }}" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Maintenance Mode</label>
                            <select name="maintenance_mode" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
                                <option value="0" {{ ($settings['maintenance_mode'] ?? '0') == '0' ? 'selected' : '' }}>Nonaktif</option>
                                <option value="1" {{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'selected' : '' }}>Aktif</option>
                            </select>
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
