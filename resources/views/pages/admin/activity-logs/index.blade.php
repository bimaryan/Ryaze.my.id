@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Log Aktivitas Sistem" 
            subtitle="Pantau semua aktivitas admin dan sistem." 
            icon="fa-solid fa-list-check">
        </x-ui.page-header>
        
        <x-ui.card class="p-6 mt-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-500">
                    <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b">
                        <tr>
                            <th scope="col" class="px-4 py-3 rounded-tl-lg">Waktu</th>
                            <th scope="col" class="px-4 py-3">User / Aktor</th>
                            <th scope="col" class="px-4 py-3">Aksi</th>
                            <th scope="col" class="px-4 py-3">Deskripsi</th>
                            <th scope="col" class="px-4 py-3 rounded-tr-lg">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr class="border-b hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 font-medium text-slate-800 whitespace-nowrap">
                                    {{ $log->created_at->format('d M Y H:i:s') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($log->user)
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-slate-700">{{ $log->user->name }}</span>
                                            <span class="text-[10px] bg-slate-200 text-slate-600 px-1.5 py-0.5 rounded">{{ $log->user->role }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">Sistem (Otomatis)</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded border border-indigo-100 text-xs font-semibold">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    {{ $log->description ?? '-' }}
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-slate-400">
                                    {{ $log->ip_address ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                    <i class="fa-solid fa-inbox text-3xl mb-2 text-slate-300 block"></i>
                                    Belum ada log aktivitas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        </x-ui.card>
    </x-ui.page-layout>
@endsection
