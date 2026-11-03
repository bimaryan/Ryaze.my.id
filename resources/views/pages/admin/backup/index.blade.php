@extends('index')

@section('content')
    <x-ui.page-layout>

        <!-- Header -->
        <x-ui.page-header title="Sistem Backup & Restore"
            subtitle="Kelola pencadangan data Ryaze dan pemulihan sistem (Database + File Klien)." icon="fa-solid fa-server">

            <x-slot name="actions">
                <button type="button" onclick="document.getElementById('restoreModal').classList.remove('hidden')"
                    class="flex items-center justify-center gap-2 bg-[#F0F6FC] dark:bg-[#0D1828] hover:bg-[#E8F0F9] dark:hover:bg-[#1D5B96] text-[#0F1A2A] dark:text-white font-semibold px-4 py-2.5  transition-all duration-300">
                    <i class="fa-solid fa-upload"></i> Restore Backup
                </button>
                <form action="{{ route('superadmin.backup.create') }}" method="POST" class="inline"
                    onsubmit="event.preventDefault(); let f = this; swConfirm('Buat Backup?', 'Proses ini memakan waktu beberapa menit karena akan membungkus database dan seluruh file klien. Lanjutkan?').then(res => { if(res.isConfirmed) f.submit(); }); return false;">
                    @csrf
                    <button type="submit"
                        class="flex items-center justify-center gap-2 bg-[#1D5B96] hover:bg-[#2A6FAD] text-white font-semibold px-4 py-2.5  transition-all duration-300">
                        <i class="fa-solid fa-download"></i> Buat Backup Baru
                    </button>
                </form>
            </x-slot>
        </x-ui.page-header>

        <!-- Tabel Backup -->
        <x-ui.card class="overflow-hidden">
            <div class="p-6 border-b border-[#E3EAF1] dark:border-[#1B2C44] flex justify-between items-center bg-[#FAFBFD] dark:bg-[#0D1828]">
                <h2 class="text-lg font-bold text-[#0F1A2A] dark:text-white"><i
                        class="fa-solid fa-file-archive text-[#94A3B4] dark:text-white/35 mr-2"></i>Riwayat
                    Backup Server</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-[#737373] dark:text-white/40">
                    <thead class="text-xs text-[#0F1A2A] dark:text-white uppercase bg-[#FAFBFD] dark:bg-[#0D1828]">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold whitespace-nowrap">Nama File</th>
                            <th scope="col" class="px-6 py-4 font-bold whitespace-nowrap">Ukuran</th>
                            <th scope="col" class="px-6 py-4 font-bold whitespace-nowrap">Tanggal Dibuat</th>
                            <th scope="col" class="px-6 py-4 font-bold text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $backup)
                            <tr class="bg-white dark:bg-[#0F1D30] border-b border-[#E3EAF1] dark:border-[#2A4463] hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828]/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-[#0F1A2A] dark:text-white whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8  bg-[#E8F0F9] dark:bg-[#1D5B96]/10 flex items-center justify-center text-[#1D5B96] dark:text-[#6BA8DC]">
                                            <i class="fa-solid fa-file-zipper"></i>
                                        </div>
                                        {{ $backup['name'] }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1  bg-[#F0F6FC] dark:bg-[#0D1828] text-[#525252] dark:text-white/60 text-xs font-semibold">
                                        <i class="fa-solid fa-hard-drive"></i> {{ $backup['size'] }} MB
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 text-[#737373] dark:text-white/40 text-sm">
                                        <i class="fa-regular fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($backup['date'])->translatedFormat('d F Y, H:i') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    <a href="{{ route('superadmin.backup.download', $backup['name']) }}"
                                        class="ui-icon-btn ui-icon-btn-primary"
                                        title="Download">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                    <form action="{{ route('superadmin.backup.destroy', $backup['name']) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="event.preventDefault(); let f = this; swConfirm('Hapus Backup?', 'Yakin ingin menghapus file backup ini?').then(res => { if(res.isConfirmed) f.submit(); }); return false;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="ui-icon-btn ui-icon-btn-danger"
                                            title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-[#94A3B4] dark:text-white/35">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa-solid fa-box-open text-4xl mb-3 text-[#C9D5E2] dark:text-white/25"></i>
                                        <p class="font-medium">Belum ada backup sistem yang dibuat.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <!-- Modal Restore -->
        <div id="restoreModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4"
            style="background:rgba(15,23,42,0.5)">
            <div class="bg-white dark:bg-[#0F1D30]   w-full max-w-md overflow-hidden">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-[#E3EAF1] dark:border-[#1B2C44] flex justify-between items-center">
                    <h3 class="font-bold text-[#0F1A2A] dark:text-white text-lg flex items-center gap-2">
                        <i class="fa-solid fa-upload text-[#1D5B96] dark:text-[#6BA8DC]"></i> Restore Backup Sistem
                    </h3>
                    <button type="button" onclick="document.getElementById('restoreModal').classList.add('hidden')"
                        class="ui-icon-btn ui-icon-btn-danger">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <form action="{{ route('superadmin.backup.restore') }}" method="POST" enctype="multipart/form-data"
                    class="p-6 space-y-4"
                    onsubmit="event.preventDefault(); let f = this; swConfirm('Restore Backup?', 'APAKAH ANDA YAKIN? Data saat ini akan DITIMPA. Lanjutkan jika Anda paham risikonya.').then(res => { if(res.isConfirmed) f.submit(); }); return false;">
                    @csrf

                    <div
                        class="p-4 mb-2 text-sm text-amber-800 dark:text-amber-200  bg-amber-50 dark:bg-amber-500/10 border border-amber-200/50 flex items-start gap-3">
                        <i class="fa-solid fa-triangle-exclamation mt-0.5 text-amber-600 dark:text-amber-300"></i>
                        <div class="leading-relaxed">
                            <span class="font-bold block mb-0.5">Peringatan Kritis</span>
                            Proses ini akan menimpa (overwrite) seluruh database Ryaze dan file-file klien yang ada.
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-semibold text-[#0F1A2A] dark:text-white">Upload File Backup (.zip)</label>
                        <input type="file" name="backup_file" accept=".zip" required
                            class="block w-full text-sm text-[#737373] dark:text-white/40 file:mr-4 file:py-2.5 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-[#E8F0F9] file:text-[#1D5B96] hover:file:bg-[#C9D5E2] border border-[#C9D5E2] dark:border-[#1B2C44]  cursor-pointer bg-[#F4F8FC] dark:bg-[#0D1828] focus:outline-none focus:ring-2 focus:border-[#1D5B96] transition-all">
                        <p class="mt-2 text-xs text-[#737373] dark:text-white/40 font-medium"><i
                                class="fa-solid fa-circle-info mr-1 text-[#94A3B4] dark:text-white/35"></i>Maksimal ukuran file: 500MB</p>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-[#E3EAF1] dark:border-[#1B2C44] mt-6">
                        <button type="button" onclick="document.getElementById('restoreModal').classList.add('hidden')"
                            class="px-5 py-2.5 text-sm font-semibold text-[#525252] dark:text-white/60 bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463]  hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828] transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="text-white bg-[#1D5B96] hover:bg-[#2A6FAD] focus:ring-2 focus:outline-none focus:ring-[#1D5B96]/30 font-semibold  text-sm px-5 py-2.5 transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-upload"></i> Restore Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function swConfirm(title, text, icon = 'warning') {
                return Swal.fire({
                    title,
                    text,
                    icon,
                    showCancelButton: true,
                    confirmButtonColor: '#4F46E5', // Indigo to match theme, red was for delete only, but wait. Let's make it standard
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Ya, lanjutkan',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: ' text-sm'
                    }
                });
            }
        </script>
    </x-ui.page-layout>
@endsection
