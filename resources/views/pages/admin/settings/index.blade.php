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
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2">Pengaturan Umum</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Website</label>
                            <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'Ryaze' }}" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
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

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 shadow-sm transition">
                        <i class="fa-solid fa-save mr-2"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page-layout>
@endsection
