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
                    class="inline-flex justify-center items-center bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-200 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Form Data Diri --}}
            <x-ui.card class="overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 flex items-center gap-3">
                    <i class="fa-solid fa-id-card text-indigo-500 dark:text-indigo-400"></i>
                    <h3 class="font-bold text-slate-800 dark:text-slate-100">Informasi Pribadi</h3>
                </div>
                <form action="{{ route('profile.update') }}" method="POST" class="p-6">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-5">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">Nama
                                Lengkap</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                required
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition-shadow">
                            @error('name')
                                <span class="text-xs text-rose-500 dark:text-rose-400 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">Alamat
                                Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                required
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition-shadow">
                            @error('email')
                                <span class="text-xs text-rose-500 dark:text-rose-400 mt-1 block">{{ $message }}</span>
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
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 flex items-center gap-3">
                    <i class="fa-solid fa-shield-halved text-emerald-500 dark:text-emerald-400"></i>
                    <h3 class="font-bold text-slate-800 dark:text-slate-100">Keamanan Akun</h3>
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
                            <label for="current_password" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">Password
                                Saat Ini</label>
                            <input type="password" id="current_password" name="current_password" placeholder="••••••••"
                                class="focus:ring-emerald-500 w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            @error('current_password')
                                <span class="text-xs text-rose-500 dark:text-rose-400 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">Password
                                Baru</label>
                            <input type="password" id="password" name="password" placeholder="Minimal 8 karakter"
                                class="focus:ring-emerald-500 w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            @error('password')
                                <span class="text-xs text-rose-500 dark:text-rose-400 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">Ulangi Password Baru</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                placeholder="••••••••"
                                class="focus:ring-emerald-500 w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
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
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex justify-between items-center">
                    <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100">Two-Factor Authentication (2FA)</h2>
                    @if(auth()->user()->two_factor_confirmed_at)
                        <span class="bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 px-2 py-0.5 rounded text-xs font-bold flex items-center gap-1">
                            <i class="fa-solid fa-shield-check"></i> Aktif
                        </span>
                    @elseif(auth()->user()->two_factor_secret)
                        <span class="bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 px-2 py-0.5 rounded text-xs font-bold flex items-center gap-1">
                            <i class="fa-solid fa-clock"></i> Belum Selesai
                        </span>
                    @else
                        <span class="bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300 px-2 py-0.5 rounded text-xs font-bold flex items-center gap-1">
                            <i class="fa-solid fa-shield"></i> Belum Aktif
                        </span>
                    @endif
                </div>

                <div class="p-6">
                    <p class="text-sm text-slate-600 dark:text-slate-300 mb-6">
                        Tambahkan lapisan keamanan ekstra ke akun Anda menggunakan aplikasi autentikator (seperti Google Authenticator).
                    </p>

                    @if(auth()->user()->two_factor_confirmed_at)
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('two-factor.recovery-codes') }}"
                                class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-700/50 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-100 text-sm font-bold py-2.5 px-6 rounded-lg transition-colors">
                                <i class="fa-solid fa-key mr-1"></i> Kode Pemulihan
                            </a>
                            <form action="{{ route('two-factor.disable') }}" method="POST" id="twofa-disable-form"
                                onsubmit="event.preventDefault(); twofaPromptDisable(); return false;">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="password" id="twofa-disable-password">
                                <input type="hidden" name="code" id="twofa-disable-code">
                                <button type="submit"
                                    class="bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold py-2.5 px-6 rounded-lg transition-colors shadow-sm">
                                    Nonaktifkan 2FA
                                </button>
                            </form>
                        </div>
                    @elseif(auth()->user()->two_factor_secret)
                        <form action="{{ route('two-factor.confirm') }}" method="POST" class="space-y-4 max-w-sm">
                            @csrf
                            <p class="text-sm text-amber-700 dark:text-amber-400 flex items-center gap-2">
                                <i class="fa-solid fa-circle-info"></i> Setup belum selesai. Masukkan kode dari aplikasi
                                autentikator untuk mengaktifkan 2FA.
                            </p>
                            <input type="text" name="code" required inputmode="numeric" pattern="\d{6}" maxlength="6"
                                placeholder="000000" autofocus
                                class="w-full text-center text-sm tracking-[0.4em] bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:border-indigo-500 outline-none transition">
                            @error('code')
                                <p class="text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2.5 px-6 rounded-lg transition-colors shadow-sm">
                                Selesaikan Setup
                            </button>
                        </form>
                    @else
                        <a href="{{ route('two-factor.setup') }}"
                            class="inline-block bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold py-2.5 px-6 rounded-lg transition-colors shadow-sm">
                            Aktifkan 2FA
                        </a>
                    @endif
                </div>
            </x-ui.card>

        </div>
    </x-ui.page-layout>
@endsection

@push('scripts')
    <script nonce="{{ csp_nonce() }}">
        // Dipanggil oleh form "Nonaktifkan 2FA": kumpulkan password + OTP
        // sebelum form benar-benar disubmit.
        function twofaPromptDisable() {
            Swal.fire({
                title: 'Konfirmasi Nonaktifkan 2FA',
                html: `
                    <input id="swal-password" type="password" placeholder="Password akun"
                        class="swal2-input" autocomplete="current-password">
                    <input id="swal-otp" type="text" inputmode="numeric" maxlength="6" placeholder="Kode OTP 6 digit"
                        class="swal2-input" style="letter-spacing:.4em;text-align:center">
                `,
                showCancelButton: true,
                confirmButtonText: 'Nonaktifkan',
                cancelButtonText: 'Batal',
                focusConfirm: false,
                preConfirm: function () {
                    const password = document.getElementById('swal-password').value;
                    const code = document.getElementById('swal-otp').value;

                    if (!password) {
                        Swal.showValidationMessage('Password wajib diisi.');
                        return false;
                    }

                    if (!/^\d{6}$/.test(code)) {
                        Swal.showValidationMessage('Kode OTP harus 6 digit.');
                        return false;
                    }

                    document.getElementById('twofa-disable-password').value = password;
                    document.getElementById('twofa-disable-code').value = code;

                    return true;
                },
            }).then(function (result) {
                if (result.isConfirmed) {
                    document.getElementById('twofa-disable-form').submit();
                }
            });
        }
    </script>
@endpush