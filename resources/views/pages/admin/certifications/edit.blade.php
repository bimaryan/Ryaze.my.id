@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Edit Sertifikat" 
            subtitle="Ubah data sertifikat." 
            icon="fa-solid fa-pen-to-square">
            <x-slot:actions>
                <a href="{{ route('superadmin.certifications.index') }}"
                    class="inline-flex items-center bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-200 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card class="w-full mt-6">
            <form action="{{ route('superadmin.certifications.update', $certification->hashid) }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf @method('PUT')
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Nama Sertifikat <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $certification->name) }}" placeholder="Contoh: AWS Certified Solutions Architect"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none @error('name') border-red-500 @enderror" required>
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="issuer" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Penerbit <span class="text-red-500">*</span></label>
                            <input type="text" name="issuer" id="issuer" value="{{ old('issuer', $certification->issuer) }}" placeholder="Contoh: Amazon Web Services"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none @error('issuer') border-red-500 @enderror" required>
                            @error('issuer') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="issued_at" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Tanggal Terbit <span class="text-red-500">*</span></label>
                            <input type="date" name="issued_at" id="issued_at" value="{{ old('issued_at', $certification->issued_at?->format('Y-m-d')) }}"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none @error('issued_at') border-red-500 @enderror" required>
                            @error('issued_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="expired_at" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Kedaluwarsa <span class="text-slate-400 font-normal">(opsional)</span></label>
                            <input type="date" name="expired_at" id="expired_at" value="{{ old('expired_at', $certification->expired_at?->format('Y-m-d')) }}"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none @error('expired_at') border-red-500 @enderror">
                            @error('expired_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="credential_id" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Credential ID</label>
                            <input type="text" name="credential_id" id="credential_id" value="{{ old('credential_id', $certification->credential_id) }}" placeholder="Contoh: AWS-SA-123456"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label for="credential_url" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">URL Verifikasi</label>
                            <input type="url" name="credential_url" id="credential_url" value="{{ old('credential_url', $certification->credential_url) }}" placeholder="https://..."
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none @error('credential_url') border-red-500 @enderror">
                            @error('credential_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="image" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Gambar Sertifikat</label>
                            @if($certification->image_path)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $certification->image_path) }}" alt="{{ $certification->name }}" class="w-full max-w-xs rounded-lg border border-slate-200 dark:border-slate-700">
                                </div>
                            @endif
                            <input type="file" name="image" id="image" accept="image/*"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-600 file:text-sm file:font-medium hover:file:bg-indigo-100">
                            @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="color" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Warna</label>
                            <input type="color" name="color" id="color" value="{{ old('color', $certification->color) }}"
                                class="w-full h-10 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-2 py-1 cursor-pointer">
                        </div>
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Urutan</label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $certification->sort_order) }}"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $certification->is_active) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            <span class="ml-3 text-sm font-medium text-slate-700 dark:text-slate-200">Tampilkan di portfolio</span>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition-colors">
                            <i class="fa-solid fa-save mr-2"></i> Perbarui
                        </button>
                    </div>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page-layout>
@endsection
