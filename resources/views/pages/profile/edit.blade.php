@extends('index')

@section('title', 'Profil Saya')
@section('seo_description', 'Kelola informasi pribadi, password, dan keamanan akun Ryaze Anda.')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header
        title="Pengaturan Akun"
        eyebrow="Akun · Profil Saya"
        icon="fa-solid fa-user-gear">
        <x-slot:subtitle>
            Perbarui informasi pribadi, ganti password, dan atur keamanan akun Anda.
        </x-slot:subtitle>
    </x-ui.page-header>

    {{-- Pemberitahuan sukses --}}
    @if (session('success'))
        <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50 flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
            <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Ringkasan identitas --}}
    <div class="ui-card p-6 flex flex-wrap items-center gap-5 mb-6">
        <div class="grid place-items-center w-14 h-14 shrink-0 bg-[#12233A] dark:bg-[#1D5B96] text-white text-xl font-black"
            aria-hidden="true">
            {{ collect(explode(' ', trim($name)))->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('') }}
        </div>
        <div class="min-w-0">
            <p class="ui-display text-lg truncate">{{ $name }}</p>
            <p class="text-xs text-[#525252] dark:text-white/50 truncate">{{ $email }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 ms-auto">
            <span class="ui-badge ui-badge-primary">{{ $role_label }}</span>
            @if ($email_verified)
                <span class="ui-badge ui-badge-success"><i class="fa-solid fa-circle-check"></i> Email terverifikasi</span>
            @else
                <span class="ui-badge ui-badge-warning"><i class="fa-solid fa-triangle-exclamation"></i> Email belum terverifikasi</span>
            @endif
            <span class="ui-badge ui-badge-neutral"><i class="fa-regular fa-calendar"></i> Bergabung {{ $joined_at ?? '—' }}</span>
        </div>
    </div>

    <div class="space-y-6">

        {{-- ── INFORMASI PRIBADI ── --}}
        <x-ui.section
            title="Informasi Pribadi"
            description="Nama dan email yang terhubung ke akun Anda."
            icon="fa-solid fa-id-card">
            <form action="{{ route('profile.update') }}" method="POST" class="max-w-xl space-y-5">
                @csrf
                @method('PATCH')

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="ui-label">Nama Lengkap</label>
                        <input type="text" name="name" id="name" required maxlength="255" autocomplete="name"
                            value="{{ old('name', $name) }}"
                            class="ui-field @error('name') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror">
                        @error('name')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="ui-label">Alamat Email</label>
                        <input type="email" name="email" id="email" required maxlength="255" autocomplete="email"
                            value="{{ old('email', $email) }}"
                            class="ui-field @error('email') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror">
                        @error('email')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-[#525252] dark:text-white/50">
                            Mengganti email akan menghapus status verifikasi email sebelumnya.
                        </p>
                    </div>
                </div>

                <div>
                    <button type="submit" class="ui-btn ui-btn-primary">
                        <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </x-ui.section>

        {{-- ── PASSWORD ── --}}
        <x-ui.section
            title="Password"
            description="Gunakan password unik yang tidak dipakai di akun lain, minimal 8 karakter."
            icon="fa-solid fa-key">
            <form action="{{ route('profile.update') }}" method="POST" class="max-w-xl space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label for="current_password" class="ui-label">Password Saat Ini</label>
                    <input type="password" name="current_password" id="current_password" autocomplete="current-password"
                        class="ui-field @error('current_password') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror">
                    @error('current_password')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="password" class="ui-label">Password Baru</label>
                        <input type="password" name="password" id="password" minlength="8" autocomplete="new-password"
                            class="ui-field @error('password') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror">
                        @error('password')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="ui-label">Ulangi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" minlength="8"
                            autocomplete="new-password"
                            class="ui-field">
                    </div>
                </div>

                <div>
                    <button type="submit" class="ui-btn ui-btn-primary">
                        <i class="fa-solid fa-rotate text-xs"></i> Ganti Password
                    </button>
                </div>
            </form>
        </x-ui.section>

        {{-- ── AUTENTIKASI DUA FAKTOR ── --}}
        <x-ui.section
            title="Autentikasi Dua Faktor"
            description="Lindungi proses masuk dengan kode dari aplikasi autentikator, seperti Google Authenticator."
            icon="fa-solid fa-shield-halved">
            @if ($two_factor === 'active')
                <div class="flex flex-wrap items-center gap-3">
                    <span class="ui-badge ui-badge-success"><i class="fa-solid fa-circle-check"></i> 2FA Aktif</span>
                    <a href="{{ route('two-factor.recovery-codes') }}" class="ui-btn ui-btn-secondary">
                        <i class="fa-solid fa-key text-xs"></i> Kode Pemulihan
                    </a>
                </div>

                {{-- Nonaktifkan: perlu password + OTP --}}
                <details class="mt-6 border-t border-[#E3EAF1] dark:border-[#2A4463] pt-5">
                    <summary class="cursor-pointer text-sm font-semibold text-rose-600 dark:text-rose-400 hover:text-rose-700 transition-colors">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Nonaktifkan 2FA
                    </summary>

                    <form action="{{ route('two-factor.disable') }}" method="POST" class="mt-4 max-w-sm space-y-4">
                        @csrf
                        @method('DELETE')

                        <p class="text-xs text-[#525252] dark:text-white/50">
                            Untuk keamanan, konfirmasi dengan password dan kode OTP Anda.
                        </p>

                        <div>
                            <label for="disable_password" class="ui-label">Password</label>
                            <input type="password" name="password" id="disable_password" required
                                autocomplete="current-password"
                                class="ui-field @error('password') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror">
                            @error('password')
                                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="disable_code" class="ui-label">Kode OTP (6 digit)</label>
                            <input type="text" name="code" id="disable_code" required inputmode="numeric"
                                pattern="\d{6}" maxlength="6" placeholder="000000"
                                class="ui-field tracking-[0.4em] @error('code') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror">
                            @error('code')
                                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="ui-btn ui-btn-danger">
                            <i class="fa-solid fa-shield-halved text-xs"></i> Nonaktifkan Permanen
                        </button>
                    </form>
                </details>
            @elseif ($two_factor === 'pending')
                <div class="flex flex-wrap items-center gap-3">
                    <span class="ui-badge ui-badge-warning"><i class="fa-solid fa-triangle-exclamation"></i> Setup belum selesai</span>
                    <a href="{{ route('two-factor.setup') }}" class="ui-btn ui-btn-primary">
                        <i class="fa-solid fa-qrcode text-xs"></i> Lanjutkan Setup
                    </a>
                </div>
            @else
                <div class="flex flex-wrap items-center gap-3">
                    <span class="ui-badge ui-badge-neutral">2FA Belum Aktif</span>
                    <a href="{{ route('two-factor.setup') }}" class="ui-btn ui-btn-primary">
                        <i class="fa-solid fa-qrcode text-xs"></i> Aktifkan 2FA
                    </a>
                </div>
            @endif
        </x-ui.section>

    </div>
</x-ui.page-layout>
@endsection
