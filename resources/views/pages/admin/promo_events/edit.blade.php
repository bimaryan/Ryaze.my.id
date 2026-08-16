@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Edit Promo Event" 
            subtitle="Perbarui data promo event." 
            icon="fa-solid fa-pen">
            <x-slot name="actions">
                <a href="{{ route('admin.promo_events.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-700">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
                </a>
            </x-slot>
        </x-ui.page-header>
        
        <x-ui.card class="p-6 mt-6">
            <form action="{{ route('admin.promo_events.update', $promo_event->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Judul Promo <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $promo_event->title) }}" required class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Target URL (Opsional)</label>
                        <input type="url" name="target_url" value="{{ old('target_url', $promo_event->target_url) }}" placeholder="https://..." class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Deskripsi Promo</label>
                    <textarea name="description" rows="3" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">{{ old('description', $promo_event->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Mulai Berlaku <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" name="start_date" value="{{ old('start_date', $promo_event->start_date->format('Y-m-d\TH:i')) }}" required class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Selesai Berlaku <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" name="end_date" value="{{ old('end_date', $promo_event->end_date->format('Y-m-d\TH:i')) }}" required class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Banner Image (Opsional)</label>
                    @if($promo_event->banner_image)
                        <div class="mb-3">
                            <img src="{{ $promo_event->banner_url }}" alt="Banner" class="h-32 object-cover rounded-lg border border-slate-200 dark:border-slate-700">
                        </div>
                    @endif
                    <input type="file" name="banner_image" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-400">
                    <p class="mt-1 text-xs text-slate-500">Biarkan kosong jika tidak ingin mengubah banner.</p>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $promo_event->is_active) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-700">
                    <label for="is_active" class="ml-2 block text-sm text-slate-700 dark:text-slate-200">
                        Aktifkan promo ini
                    </label>
                </div>

                <div class="pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                        <i class="fa-solid fa-save mr-2"></i> Perbarui Promo
                    </button>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page-layout>
@endsection
