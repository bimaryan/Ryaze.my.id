@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Promo Mahasiswa"
            eyebrow="Hosting Gratis · Verifikasi PDDIKTI"
            status="{{ $promoActive ? 'AKTIF' : 'TIDAK AKTIF' }}"
            subtitle="Cek NIM Anda dan dapatkan Paket Pro gratis 1 bulan."
            icon="fa-graduation-cap"
            iconColor="indigo">
            <x-slot:actions>
                <a href="{{ route('user_hosting.subscription') }}"
                    class="ui-btn ui-btn-md ui-btn-icon ui-btn-secondary" title="Kembali" aria-label="Kembali">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </x-slot:actions>
        </x-ui.page-layout>

        {{-- Banner hasil kloning verifikasi --}}
        @if (session('success'))
            <div class="mb-6 border border-emerald-300 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/40 p-5 flex items-center gap-4">
                <div class="w-10 h-10 flex-shrink-0 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-200 dark:border-emerald-800/60">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 border border-rose-300 dark:border-rose-800 bg-rose-50 dark:bg-rose-950/40 p-5 flex items-center gap-4">
                <div class="w-10 h-10 flex-shrink-0 bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-200 dark:border-rose-800/60">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <p class="text-sm font-semibold text-rose-800 dark:text-rose-300">{{ session('error') }}</p>
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 border border-rose-300 dark:border-rose-800 bg-rose-50 dark:bg-rose-950/40 p-5">
                <p class="text-sm font-semibold text-rose-800 dark:text-rose-300 mb-1">Periksa kembali isian Anda:</p>
                <ul class="list-disc list-inside text-xs text-rose-600 dark:text-rose-400">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($verification)
            {{-- Sudah pernah klaim --}}
            <div class="ui-card mb-6 p-6 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="ui-icon-box w-12 h-12 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800/50">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                    <div>
                        <p class="ui-eyebrow-muted mb-0.5">Status Mahasiswa</p>
                        <p class="text-sm font-bold text-[#0F1A2A] dark:text-white">Terverifikasi — Promonya sudah Anda klaim setelah membuat akun.</p>
                    </div>
                </div>
                <a href="{{ route('user_hosting.dashboard') }}" class="ui-btn ui-btn-primary shrink-0">
                    <i class="fa-solid fa-server mr-1"></i> Buka Dashboard
                </a>
            </div>

            <div class="mb-6">
                <p class="ui-eyebrow mb-1.5">Data Verifikasi</p>
                <div class="ui-card p-5 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <p class="text-[11px] font-mono uppercase tracking-[0.15em] text-[#8A9BAE] dark:text-white/40 mb-0.5">NIM</p>
                        <p class="font-mono font-bold text-sm text-[#0F1A2A] dark:text-white">{{ $verification->nim }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-mono uppercase tracking-[0.15em] text-[#8A9BAE] dark:text-white/40 mb-0.5">Nama (PDDIKTI)</p>
                        <p class="text-sm font-semibold text-[#0F1A2A] dark:text-white">{{ $verification->nama_pddikti }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-mono uppercase tracking-[0.15em] text-[#8A9BAE] dark:text-white/40 mb-0.5">Perguruan Tinggi</p>
                        <p class="text-sm text-[#525252] dark:text-[#8A9BAE]">{{ $verification->nama_pt }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-mono uppercase tracking-[0.15em] text-[#8A9BAE] dark:text-white/40 mb-0.5">Program Studi</p>
                        <p class="text-sm text-[#525252] dark:text-[#8A9BAE]">{{ $verification->nama_prodi ?: '-' }} {{ $verification->jenjang ? '· '.$verification->jenjang : '' }}</p>
                    </div>
                </div>
            </div>
        @elseif (! $promoActive)
            {{-- Promo sedang tidak aktif --}}
            <div class="ui-card p-8 text-center">
                <div class="ui-icon-box w-14 h-14 mx-auto mb-4 bg-[#F0F6FC] dark:bg-[#0F1D30] text-[#1D5B96] dark:text-[#6BA8DC] border-[#E3EAF1] dark:border-[#2A4463]">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <h3 class="ui-display text-base mb-2">Promo sedang tidak aktif</h3>
                <p class="text-sm text-[#525252] dark:text-[#8A9BAE] max-w-md mx-auto">
                    Saat ini promo hosting gratis untuk mahasiswa sedang ditutup. Pantau halaman ini atau ikuti kanal Ryaze untuk info pembukaan periode berikutnya.
                </p>
            </div>
        @else
            {{-- Form klaim --}}
            <div class="ui-grid grid-cols-1 md:grid-cols-3 mb-6">
                <div class="ui-cell md:col-span-2 p-6">
                    <p class="ui-eyebrow mb-1.5">Verifikasi Mahasiswa Aktif</p>
                    <h3 class="ui-display text-lg mb-2">Klaim Paket {{ ucfirst($planSlug) }} Gratis {{ $days }} Hari</h3>
                    <p class="text-sm text-[#525252] dark:text-[#8A9BAE] mb-6">
                        Masukkan NIM dan nama lengkap Anda. Data akan dicek secara langsung ke
                        <a href="https://pddikti.kemdiktisaintek.go.id" target="_blank" rel="noopener" class="underline decoration-dotted font-semibold text-[#1D5B96] dark:text-[#6BA8DC]">PDDIKTI</a>.
                        Keduanya harus sesuai data kampus.
                    </p>

                    <form action="{{ route('user_hosting.student_promo.verify') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="nim" class="text-[11px] font-mono uppercase tracking-[0.15em] text-[#8A9BAE] dark:text-white/40 mb-1 block">Nomor Induk Mahasiswa (NIM)</label>
                            <input id="nim" type="text" name="nim" required
                                   value="{{ old('nim') }}"
                                   placeholder="Contoh: 10121067"
                                   autocomplete="off"
                                   class="ui-field ui-field-sm font-mono uppercase">
                        </div>
                        <div>
                            <label for="nama" class="text-[11px] font-mono uppercase tracking-[0.15em] text-[#8A9BAE] dark:text-white/40 mb-1 block">Nama Lengkap Sesuai Kampus</label>
                            <input id="nama" type="text" name="nama" required
                                   value="{{ old('nama') }}"
                                   placeholder="Nama lengkap tanpa gelar"
                                   autocomplete="off"
                                   class="ui-field ui-field-sm">
                        </div>
                        <button type="submit" class="ui-btn ui-btn-primary w-full sm:w-auto">
                            <i class="fa-solid fa-check-to-slot mr-1"></i> Klaim Hosting Gratis
                        </button>
                    </form>
                </div>

                <div class="ui-cell p-6">
                    <p class="ui-eyebrow mb-3">Syarat & Ketentuan</p>
                    <ul class="space-y-3">
                        <li class="ui-check"><span class="ui-check-mark">&#10003;</span> Mahasiswa aktif di perguruan tinggi yang datanya sudah dilaporkan ke PDDIKTI.</li>
                        <li class="ui-check"><span class="ui-check-mark">&#10003;</span> 1 NIM hanya boleh dipakai 1 akun, dan 1 akun hanya bisa klaim 1x selamanya.</li>
                        <li class="ui-check"><span class="ui-check-mark">&#10003;</span> Hanya untuk akun yang belum memiliki paket berlangganan aktif.</li>
                        <li class="ui-check"><span class="ui-check-mark">&#10003;</span> Setelah periode gratis berakhir, langganan dapat dilanjutkan sesuai harga normal.</li>
                    </ul>
                    <p class="text-[11px] text-[#8A9BAE] dark:text-white/40 mt-4 pt-4 border-t border-[#EDF2F7] dark:border-[#24384F]">
                        Hasil verifikasi PDDIKTI disimpan untuk keperluan audit promo. Verifikasi hanya boleh 1x per akun dengan NIM sesuai data kampus Anda.
                    </p>
                </div>
            </div>
        @endif
    </x-ui.page-layout>
@endsection
