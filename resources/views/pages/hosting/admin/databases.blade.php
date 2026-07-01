@extends('index')

@section('content')
<div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50">
    <div class="p-6 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="shrink-0 w-12 h-12 flex items-center justify-center bg-orange-50 text-orange-600 rounded-xl">
                <i class="fa-solid fa-database text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800">Semua Database</h1>
                <p class="text-sm text-slate-500 mt-1">Daftar semua database klien di server.</p>
            </div>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
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
</div>
@endsection
