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
                                class="focus:ring-emerald-500 w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            @error('current_password')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password
                                Baru</label>
                            <input type="password" id="password" name="password" placeholder="Minimal 8 karakter"
                                class="focus:ring-emerald-500 w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            @error('password')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-semibold text-slate-700 mb-1.5">Ulangi Password Baru</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                placeholder="••••••••"
                                class="focus:ring-emerald-500 w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
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

            <!-- 2FA Section -->
            <x-ui.card>
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <h2 class="text-sm font-bold text-slate-800">Two-Factor Authentication (2FA)</h2>
                    @if(auth()->user()->two_factor_secret)
                        <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-xs font-bold flex items-center gap-1">
                            <i class="fa-solid fa-shield-check"></i> Aktif
                        </span>
                    @else
                        <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-xs font-bold flex items-center gap-1">
                            <i class="fa-solid fa-shield"></i> Belum Aktif
                        </span>
                    @endif
                </div>

                <div class="p-6">
                    <p class="text-sm text-slate-600 mb-6">
                        Tambahkan lapisan keamanan ekstra ke akun Anda menggunakan aplikasi autentikator (seperti Google Authenticator).
                    </p>

                    @if(!auth()->user()->two_factor_secret)
                        <form action="#" method="POST" onsubmit="event.preventDefault(); swAlert('Info', 'Fitur 2FA Setup sedang dalam pengembangan MVP.', 'info');">
                            @csrf
                            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold py-2.5 px-6 rounded-lg transition-colors shadow-sm">
                                Aktifkan 2FA
                            </button>
                        </form>
                    @else
                        <form action="#" method="POST" onsubmit="event.preventDefault(); swConfirm('Nonaktifkan 2FA?', 'Apakah Anda yakin ingin menonaktifkan pengamanan ekstra ini?').then(res => { if(res.isConfirmed) { swAlert('Info', 'Fitur 2FA Disable sedang dalam pengembangan MVP.', 'info'); } }); return false;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold py-2.5 px-6 rounded-lg transition-colors shadow-sm">
                                Nonaktifkan 2FA
                            </button>
                        </form>
                    @endif
                </div>
            </x-ui.card>

        </div>
    </x-ui.page-layout>
@endsection
