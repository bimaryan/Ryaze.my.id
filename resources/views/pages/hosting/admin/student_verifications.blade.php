@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header title="Verifikasi Mahasiswa" icon="graduation-cap" iconColor="indigo">
            <x-slot:subtitle>
                Klaim promo hosting gratis mahasiswa aktif — verifikasi otomatis lewat PDDIKTI.
            </x-slot:subtitle>
        </x-ui.page-header>

        @if (session('success'))
            <div class="ui-panel px-4 py-3 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Pengaturan Promo --}}
        <x-ui.section
            title="Pengaturan Promo Mahasiswa"
            icon="fa-solid fa-sliders"
            description="NIM yang diklaim dicek langsung ke PDDIKTI. Verifikasi hanya bisa 1x per akun dan 1 NIM hanya boleh dipakai 1 akun."
        >
            @if ($settings['active'])
                <x-slot:status>PROMO AKTIF</x-slot:status>
            @else
                <x-slot:actions>
                    <span class="ui-badge ui-badge-warning"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> NONAKTIF</span>
                </x-slot:actions>
            @endif
            <form action="{{ route('admin_hosting.student_verifications.settings') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @csrf
                @method('PUT')
                <div>
                    <label class="ui-label">Status Promo</label>
                    <select name="student_promo_active" class="ui-field font-mono uppercase">
                        <option value="1" {{ $settings['active'] ? 'selected' : '' }}>AKTIF</option>
                        <option value="0" {{ ! $settings['active'] ? 'selected' : '' }}>TIDAK AKTIF</option>
                    </select>
                </div>
                <div>
                    <label class="ui-label">Paket Gratis</label>
                    <select name="student_promo_plan" class="ui-field">
                        <option value="starter" {{ $settings['plan'] === 'starter' ? 'selected' : '' }}>Starter</option>
                        <option value="pro" {{ $settings['plan'] === 'pro' ? 'selected' : '' }}>Pro</option>
                        <option value="business" {{ $settings['plan'] === 'business' ? 'selected' : '' }}>Business</option>
                    </select>
                </div>
                <div>
                    <label class="ui-label">Durasi (hari)</label>
                    <input type="number" name="student_promo_days" min="1" max="365" value="{{ $settings['days'] }}"
                        class="ui-field font-mono">
                </div>
                <div>
                    <label class="ui-label">Base URL Data PDDIKTI</label>
                    <input type="url" name="pddikti_api_base" value="{{ $settings['api_base'] }}"
                        class="ui-field font-mono text-xs">
                    <p class="text-[11px] text-[#737373] dark:text-white/35 mt-1.5">Situs resmi: https://pddikti.kemdiktisaintek.go.id (dibaca langsung dari endpoint JSON internalnya)</p>
                </div>

                <div class="sm:col-span-2 lg:col-span-4 pt-5 border-t border-[#E3EAF1] dark:border-[#2A4463] flex items-center justify-between gap-3">
                    <p class="text-xs text-[#737373] dark:text-white/35">Ketika promo nonaktif, klaim baru ditolak — klaim yang sudah aktif tetap jalan.</p>
                    <button type="submit" class="ui-btn ui-btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </x-ui.section>

        {{-- Daftar Klaim --}}
        <x-ui.table class="mt-4">
            <x-slot:pagination>
                <span class="block mb-2 text-xs text-[#737373] dark:text-white/35 font-mono uppercase tracking-wider">Total {{ $verifications->total() }} klaim</span>
                @if ($verifications->hasPages())
                    {{ $verifications->links() }}
                @endif
            </x-slot:pagination>

            <x-slot:head>
                <th class="px-6 py-4">NIM</th>
                <th class="px-6 py-4">Nama (PDDIKTI)</th>
                <th class="px-6 py-4">Akun</th>
                <th class="px-6 py-4">Perguruan Tinggi</th>
                <th class="px-6 py-4 text-center">Status</th>
                <th class="px-6 py-4 text-right">Verifikasi</th>
            </x-slot:head>

            @forelse ($verifications as $verification)
                <tr class="hover:bg-slate-50 dark:bg-slate-800/50 dark:hover:bg-slate-700/40 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-bold text-slate-800 dark:text-slate-100 font-mono">{{ $verification->nim }}</div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ $verification->jenjang ?: '-' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-slate-800 dark:text-slate-100 font-medium">{{ $verification->nama_pddikti }}</div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400">diisi: {{ $verification->nama_input }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                        <div class="font-medium text-slate-700 dark:text-slate-200">{{ $verification->user?->name }}</div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ $verification->user?->email }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                        <div class="font-medium text-slate-700 dark:text-slate-200">{{ $verification->nama_pt }}</div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ $verification->nama_prodi ?: 'Prodi tidak valid' }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="ui-badge ui-badge-success">
                            <i class="fa-solid fa-user-graduate"></i> {{ $verification->pddikti_status ?? 'AKTIF' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                        <div class="font-medium text-slate-700 dark:text-slate-200">{{ $verification->verified_at->format('d M Y') }}</div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">{{ $verification->verified_at->format('H:i') }} · {{ $verification->ip_address }}</div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <i class="fa-solid fa-graduation-cap text-3xl text-slate-300 dark:text-slate-400"></i>
                            <p>Belum ada klaim verifikasi mahasiswa.</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Klaim akan muncul di sini setelah user mengisi form promo.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.page-layout>
@endsection
