@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Edit Sertifikat" 
            subtitle="Ubah data sertifikat." 
            icon="fa-solid fa-pen-to-square">
            <x-slot:actions>
                <a href="{{ route('superadmin.certifications.index') }}"
                    class="inline-flex items-center bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463] hover:bg-[#E8F0F9] dark:hover:bg-[#0D1828] text-[#0F1A2A] dark:text-white px-5 py-2.5  text-sm font-medium transition ">
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
                            <label for="name" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Nama Sertifikat <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $certification->name) }}" placeholder="Contoh: AWS Certified Solutions Architect"
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none @error('name') border-rose-500 @enderror" required>
                            @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="issuer" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Penerbit <span class="text-rose-500">*</span></label>
                            <input type="text" name="issuer" id="issuer" value="{{ old('issuer', $certification->issuer) }}" placeholder="Contoh: Amazon Web Services"
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none @error('issuer') border-rose-500 @enderror" required>
                            @error('issuer') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="issued_at" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Tanggal Terbit <span class="text-rose-500">*</span></label>
                            <input type="date" name="issued_at" id="issued_at" value="{{ old('issued_at', $certification->issued_at?->format('Y-m-d')) }}"
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none @error('issued_at') border-rose-500 @enderror" required>
                            @error('issued_at') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="expired_at" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Kedaluwarsa <span class="text-[#94A3B4] font-normal">(opsional)</span></label>
                            <input type="date" name="expired_at" id="expired_at" value="{{ old('expired_at', $certification->expired_at?->format('Y-m-d')) }}"
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none @error('expired_at') border-rose-500 @enderror">
                            @error('expired_at') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="credential_id" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Credential ID</label>
                            <input type="text" name="credential_id" id="credential_id" value="{{ old('credential_id', $certification->credential_id) }}" placeholder="Contoh: AWS-SA-123456"
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none">
                        </div>
                        <div>
                            <label for="credential_url" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">URL Verifikasi</label>
                            <input type="url" name="credential_url" id="credential_url" value="{{ old('credential_url', $certification->credential_url) }}" placeholder="https://..."
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none @error('credential_url') border-rose-500 @enderror">
                            @error('credential_url') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="image" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Gambar Sertifikat</label>
                            @if($certification->image_path)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $certification->image_path) }}" alt="{{ $certification->name }}" class="w-full max-w-xs  border border-[#C9D5E2] dark:border-[#1B2C44]">
                                </div>
                            @endif
                            <input type="file" name="image" id="image" accept="image/*"
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none file:mr-3 file:py-1 file:px-3 file:border-0 file:bg-[#E8F0F9] file:text-[#1D5B96] file:text-sm file:font-medium hover:file:bg-[#C9D5E2]">
                            @error('image') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="color" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Warna</label>
                            <input type="color" name="color" id="color" value="{{ old('color', $certification->color) }}"
                                class="w-full h-10 bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44]  px-2 py-1 cursor-pointer">
                        </div>
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Urutan</label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $certification->sort_order) }}"
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $certification->is_active) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-[#E3EAF1] dark:bg-[#1B2C44] peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#1D5B96]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[#C9D5E2] after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#1D5B96]"></div>
                            <span class="ml-3 text-sm font-medium text-[#0F1A2A] dark:text-white">Tampilkan di portfolio</span>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-[#E3EAF1] dark:border-[#1B2C44] flex justify-end">
                        <button type="submit" class="bg-[#1D5B96] hover:bg-[#2A6FAD] text-white font-bold py-2.5 px-6   transition-colors">
                            <i class="fa-solid fa-save mr-2"></i> Perbarui
                        </button>
                    </div>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page-layout>
@endsection
