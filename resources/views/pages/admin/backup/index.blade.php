@extends('index')

@section('content')
    <x-ui.page-layout>

        <!-- Header -->
        <x-ui.page-header title="Sistem Backup & Restore"
            subtitle="Kelola pencadangan data Ryaze dan pemulihan sistem (Database + File Klien)." icon="fa-solid fa-server">

            <x-slot name="actions">
                <button type="button" onclick="document.getElementById('restoreModal').classList.remove('hidden')"
                    class="flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-4 py-2.5 rounded-xl transition-all duration-300">
                    <i class="fa-solid fa-upload"></i> Restore Backup
                </button>
                <form action="{{ route('superadmin.backup.create') }}" method="POST" class="inline"
                    onsubmit="event.preventDefault(); let f = this; swConfirm('Buat Backup?', 'Proses ini memakan waktu beberapa menit karena akan membungkus database dan seluruh file klien. Lanjutkan?').then(res => { if(res.isConfirmed) f.submit(); }); return false;">
                    @csrf
                    <button type="submit"
                        class="flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2.5 rounded-xl transition-all duration-300">
                        <i class="fa-solid fa-download"></i> Buat Backup Baru
                    </button>
                </form>
            </x-slot>
        </x-ui.page-header>

        <!-- Tabel Backup -->
        <x-ui.card class="overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h2 class="text-lg font-bold text-slate-800"><i
                        class="fa-solid fa-file-archive text-slate-400 mr-2"></i>Riwayat
                    Backup Server</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-500">
                    <thead class="text-xs text-slate-700 uppercase bg-slate-50/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold">Nama File</th>
                            <th scope="col" class="px-6 py-4 font-bold">Ukuran</th>
                            <th scope="col" class="px-6 py-4 font-bold">Tanggal Dibuat</th>
                            <th scope="col" class="px-6 py-4 font-bold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $backup)
                            <tr class="bg-white border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-800">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500">
                                            <i class="fa-solid fa-file-zipper"></i>
                                        </div>
                                        {{ $backup['name'] }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-slate-100 text-slate-600 text-xs font-semibold">
                                        <i class="fa-solid fa-hard-drive"></i> {{ $backup['size'] }} MB
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 text-slate-500 text-sm">
                                        <i class="fa-regular fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($backup['date'])->translatedFormat('d F Y, H:i') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('superadmin.backup.download', $backup['name']) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors"
                                        title="Download">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                    <form action="{{ route('superadmin.backup.destroy', $backup['name']) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="event.preventDefault(); let f = this; swConfirm('Hapus Backup?', 'Yakin ingin menghapus file backup ini?').then(res => { if(res.isConfirmed) f.submit(); }); return false;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-colors"
                                            title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa-solid fa-box-open text-4xl mb-3 text-slate-300"></i>
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
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                        <i class="fa-solid fa-upload text-indigo-500"></i> Restore Backup Sistem
                    </h3>
                    <button type="button" onclick="document.getElementById('restoreModal').classList.add('hidden')"
                        class="text-slate-400 hover:text-rose-500 transition-colors w-8 h-8 flex items-center justify-center rounded-lg hover:bg-rose-50">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <form action="{{ route('superadmin.backup.restore') }}" method="POST" enctype="multipart/form-data"
                    class="p-6 space-y-4"
                    onsubmit="event.preventDefault(); let f = this; swConfirm('Restore Backup?', 'APAKAH ANDA YAKIN? Data saat ini akan DITIMPA. Lanjutkan jika Anda paham risikonya.').then(res => { if(res.isConfirmed) f.submit(); }); return false;">
                    @csrf

                    <div
                        class="p-4 mb-2 text-sm text-amber-800 rounded-xl bg-amber-50 border border-amber-200/50 flex items-start gap-3">
                        <i class="fa-solid fa-triangle-exclamation mt-0.5 text-amber-600"></i>
                        <div class="leading-relaxed">
                            <span class="font-bold block mb-0.5">Peringatan Kritis</span>
                            Proses ini akan menimpa (overwrite) seluruh database Ryaze dan file-file klien yang ada.
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-semibold text-slate-700">Upload File Backup (.zip)</label>
                        <input type="file" name="backup_file" accept=".zip" required
                            class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-200 rounded-xl cursor-pointer bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                        <p class="mt-2 text-xs text-slate-500 font-medium"><i
                                class="fa-solid fa-circle-info mr-1 text-slate-400"></i>Maksimal ukuran file: 500MB</p>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 mt-6">
                        <button type="button" onclick="document.getElementById('restoreModal').classList.add('hidden')"
                            class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-semibold rounded-xl text-sm px-5 py-2.5 transition-colors flex items-center gap-2">
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
                        popup: 'rounded-xl text-sm'
                    }
                });
            }
        </script>
    </x-ui.page-layout>
@endsection
