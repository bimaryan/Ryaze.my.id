@extends('index')

@section('content')
<div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50 relative">
    <div class="p-6 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="shrink-0 w-12 h-12 flex items-center justify-center bg-orange-50 text-orange-600 rounded-xl">
                <i class="fa-solid fa-database text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800">Semua Database</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola semua database klien di server.</p>
            </div>
        </div>
        <div>
            <button onclick="document.getElementById('createDbModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition-colors flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Buat Database
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Database Name</th>
                        <th class="px-6 py-4">Username</th>
                        <th class="px-6 py-4">Host : Port</th>
                        <th class="px-6 py-4">Pemilik (Klien)</th>
                        <th class="px-6 py-4 text-center">Dibuat Pada</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($databases as $db)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $db->db_name }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-600 bg-slate-50 rounded px-2">{{ $db->db_username }}</td>
                            <td class="px-6 py-4">
                                <span class="text-slate-700">{{ $db->host }}</span>:<span class="text-slate-500">{{ $db->port }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($db->user)
                                    <div class="font-medium text-slate-800">{{ $db->user->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $db->user->email }}</div>
                                @else
                                    <span class="text-slate-400 italic">Unknown</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-slate-500">
                                {{ $db->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('admin_hosting.databases.destroy', $db->hashid) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus database ini? Semua data di dalamnya akan hilang permanen!');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-red-500 hover:bg-red-50 hover:text-red-700 transition-colors tooltip" title="Hapus Database">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-solid fa-database text-3xl text-slate-300"></i>
                                    <p>Belum ada database yang dibuat.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($databases->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $databases->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Create Database -->
    <div id="createDbModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden relative" onclick="event.stopPropagation()">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">Buat Database Baru</h3>
                <button type="button" onclick="document.getElementById('createDbModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form action="{{ route('admin_hosting.databases.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Klien</label>
                        <select name="user_id" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="">-- Pilih Klien --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500 mt-1">Prefix ryz_{id}_ akan ditambahkan otomatis.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Database</label>
                        <input type="text" name="db_name" required pattern="[A-Za-z0-9\-_]+" maxlength="15" placeholder="contoh: wp_blog" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Username Database</label>
                        <input type="text" name="db_username" required pattern="[A-Za-z0-9\-_]+" maxlength="15" placeholder="contoh: wp_user" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Password Database</label>
                        <input type="text" name="db_password" required minlength="8" maxlength="32" placeholder="Masukkan password kuat" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('createDbModal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 bg-white border border-slate-200 rounded-lg shadow-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm">Buat Database</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
