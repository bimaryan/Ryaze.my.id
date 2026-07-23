@extends('index')

@section('content')
<div class="p-4 sm:ml-64 pt-24 bg-slate-50 min-h-screen">
    <div class="max-w-3xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('user_hosting.apk.index') }}" class="text-sm text-slate-500 hover:text-indigo-600 font-medium flex items-center gap-2 mb-4 transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar APK
            </a>
            <h1 class="text-2xl font-bold text-slate-800">Buat Aplikasi Android Baru</h1>
            <p class="text-slate-500 text-sm mt-1">Isi detail di bawah ini untuk memulai proses *build* website Anda menjadi `.apk`.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden p-6">
            <form action="{{ route('user_hosting.apk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="app_name" class="block mb-2 text-sm font-medium text-slate-900">Nama Aplikasi</label>
                        <input type="text" id="app_name" name="app_name" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" placeholder="Contoh: Toko Online Saya" required value="{{ old('app_name') }}">
                        @error('app_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="app_url" class="block mb-2 text-sm font-medium text-slate-900">URL Website</label>
                        <input type="url" id="app_url" name="app_url" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" placeholder="https://example.com" required value="{{ old('app_url') }}">
                        <p class="mt-1.5 text-xs text-slate-500">URL website yang ingin dijadikan aplikasi. Harus menggunakan protokol HTTPS.</p>
                        @error('app_url')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="package_name" class="block mb-2 text-sm font-medium text-slate-900">Package Name</label>
                        <input type="text" id="package_name" name="package_name" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" placeholder="com.example.app" required value="{{ old('package_name') }}">
                        <p class="mt-1.5 text-xs text-slate-500">Identitas unik aplikasi Anda di Android (format: com.namaperusahaan.namaapp).</p>
                        @error('package_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-slate-900" for="icon">Ikon Aplikasi <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <input class="block w-full text-sm text-slate-900 border border-slate-300 rounded-lg cursor-pointer bg-slate-50 focus:outline-none" id="icon" name="icon" type="file" accept="image/png, image/jpeg">
                        <p class="mt-1.5 text-xs text-slate-500">Gunakan format PNG atau JPG dengan ukuran persegi (misal: 512x512px).</p>
                        @error('icon')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm w-full sm:w-auto px-6 py-2.5 text-center transition-colors shadow-sm">
                            <i class="fa-solid fa-hammer mr-1.5"></i> Proses Build APK
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection