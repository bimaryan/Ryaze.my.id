@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- Alerts --}}
        <x-ui.page-header 
            title="Profil Saya" 
            subtitle="Kelola informasi pribadi dan keamanan akun Anda." 
            icon="fa-solid fa-user">
            <x-slot:actions>
                <a href="{{ url('/') }}"
                    class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Form Data Diri --}}
            <x-ui.card class="overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center gap-3">
                    <i class="fa-solid fa-id-card text-indigo-500"></i>
                    <h3 class="font-bold text-slate-800">Informasi Pribadi</h3>
                </div>
                <form action="{{ route('profile.update') }}" method="POST" class="p-6">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-5">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama
                                Lengkap</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                required
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition-shadow">
                            @error('name')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat
                                Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                required
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition-shadow">
                            @error('email')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2.5 px-6 rounded-lg transition-colors shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </x-ui.card>

            {{-- Form Keamanan --}}
            <x-ui.card class="overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center gap-3">
                    <i class="fa-solid fa-shield-halved text-emerald-500"></i>
                    <h3 class="font-bold text-slate-800">Keamanan Akun</h3>
                </div>
                <form action="{{ route('profile.update') }}" method="POST" class="p-6">
                    @csrf
                    @method('PATCH')

                    {{-- We need name and email here as well because it's the same update method, 
                     or we hide them. Actually, since the controller updates name and email from request,
                     we MUST send name and email, or split the routes.
                     Since controller requires name and email, let's put them as hidden fields --}}
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">

                    <div class="space-y-5">
                        <div>
                            <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password
                                Saat Ini</label>
                            <input type="password" id="current_password" name="current_password" placeholder="••••••••"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm transition-shadow">
                            @error('current_password')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password
                                Baru</label>
                            <input type="password" id="password" name="password" placeholder="Minimal 8 karakter"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm transition-shadow">
                            @error('password')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-semibold text-slate-700 mb-1.5">Ulangi Password Baru</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                placeholder="••••••••"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm transition-shadow">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold py-2.5 px-6 rounded-lg transition-colors shadow-sm">
                            Ganti Password
                        </button>
                    </div>
                </form>
            </x-ui.card>

        </div>
    </x-ui.page-layout>
@endsection
