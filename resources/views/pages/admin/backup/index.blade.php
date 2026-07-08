@extends('index')

@section('content')
<div class="p-4 sm:ml-64 bg-slate-50 min-h-screen pt-24">
    <div class="max-w-7xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                        <i class="fa-solid fa-server"></i>
                    </div>
                    Sistem Backup & Restore
                </h1>
                <p class="text-sm text-slate-500 mt-1 ml-13">Kelola pencadangan data Ryaze dan pemulihan sistem (Database + File Klien).</p>
            </div>
            
            <div class="flex items-center gap-3 w-full md:w-auto">
                <button type="button" onclick="document.getElementById('restoreModal').classList.remove('hidden')" class="w-full md:w-auto flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-2.5 px-5 rounded-xl transition-all duration-300">
                    <i class="fa-solid fa-upload"></i> Restore Backup
                </button>
                <form action="{{ route('superadmin.backup.create') }}" method="POST" class="w-full md:w-auto" onsubmit="event.preventDefault(); let f = this; swConfirm('Buat Backup?', 'Proses ini memakan waktu beberapa menit karena akan membungkus database dan seluruh file klien. Lanjutkan?').then(res => { if(res.isConfirmed) f.submit(); }); return false;">
                    @csrf
                    <button type="submit" class="w-full md:w-auto flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-5 rounded-xl transition-all duration-300">
                        <i class="fa-solid fa-download"></i> Buat Backup Baru
                    </button>
                </form>
            </div>
        </div>

        <!-- Tabel Backup -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h2 class="text-lg font-bold text-slate-800"><i class="fa-solid fa-file-archive text-slate-400 mr-2"></i>Riwayat Backup Server</h2>
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
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500">
                                            <i class="fa-solid fa-file-zipper"></i>
                                        </div>
                                        {{ $backup['name'] }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-slate-100 text-slate-600 text-xs font-semibold">
                                        <i class="fa-solid fa-hard-drive"></i> {{ $backup['size'] }} MB
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 text-slate-500 text-sm">
                                        <i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($backup['date'])->translatedFormat('d F Y, H:i') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('superadmin.backup.download', $backup['name']) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors" title="Download">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                    <form action="{{ route('superadmin.backup.destroy', $backup['name']) }}" method="POST" class="inline-block" onsubmit="event.preventDefault(); let f = this; swConfirm('Hapus Backup?', 'Yakin ingin menghapus file backup ini?').then(res => { if(res.isConfirmed) f.submit(); }); return false;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-colors" title="Hapus">
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
        </div>

    </div>
</div>

<!-- Modal Restore -->
<div id="restoreModal" tabindex="-1" class="hidden fixed inset-0 z-[100] flex items-center justify-center w-full h-full bg-slate-900/50 backdrop-blur-sm">
    <div class="relative p-4 w-full max-w-md">
        <!-- Modal content -->
        <div class="relative bg-white rounded-2xl shadow-xl">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                <h3 class="text-xl font-bold text-slate-800">
                    Restore Backup Sistem
                </h3>
                <button type="button" onclick="document.getElementById('restoreModal').classList.add('hidden')" class="text-slate-400 bg-transparent hover:bg-slate-200 hover:text-slate-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-4 md:p-5">
                <div class="p-4 mb-4 text-sm text-amber-800 rounded-lg bg-amber-50 font-medium">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> Peringatan: Proses ini akan menimpa (overwrite) seluruh database Ryaze dan file-file klien yang ada. Pastikan file ZIP valid.
                </div>
                
                <form action="{{ route('superadmin.backup.restore') }}" method="POST" enctype="multipart/form-data" class="space-y-4" onsubmit="event.preventDefault(); let f = this; swConfirm('Restore Backup?', 'APAKAH ANDA YAKIN? Data saat ini akan DITIMPA. Lanjutkan jika Anda paham risikonya.').then(res => { if(res.isConfirmed) f.submit(); }); return false;">
                    @csrf
                    
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-slate-900">Upload File Backup (.zip)</label>
                        <input type="file" name="backup_file" accept=".zip" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-200 rounded-xl cursor-pointer bg-slate-50">
                        <p class="mt-1 text-xs text-slate-500">Maksimal ukuran file: 500MB</p>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-xl text-sm px-5 py-2.5 text-center">
                            Restore Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
