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
                <table class="w-full text-sm text-left text-[#737373] dark:text-white/40">
                    <thead class="text-xs text-[#0F1A2A] dark:text-white uppercase bg-[#F4F8FC] dark:bg-[#0D1828] border-b">
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
                            <tr class="border-b hover:bg-[#F4F8FC] dark:bg-[#0D1828] dark:hover:bg-[#0D1828] transition-colors">
                                <td class="px-4 py-3 font-medium text-[#0F1A2A] dark:text-white whitespace-nowrap">
                                    {{ $log->created_at->format('d M Y H:i:s') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($log->user)
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-[#0F1A2A] dark:text-white">{{ $log->user->name }}</span>
                                            <span class="text-[10px] bg-[#E3EAF1] dark:bg-[#1B2C44] text-[#525252] dark:text-white/60 px-1.5 py-0.5 rounded">{{ $log->user->role }}</span>
                                        </div>
                                    @else
                                        <span class="text-[#94A3B4] dark:text-white/35 italic">Sistem (Otomatis)</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="bg-[#E8F0F9] dark:bg-[#1D5B96]/10 text-[#1D5B96] dark:text-[#6BA8DC] px-2 py-1 rounded border border-[#C9D5E2] dark:border-[#1D5B96]/40 text-xs font-semibold">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-[#525252] dark:text-white/60">
                                    {{ $log->description ?? '-' }}
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-[#94A3B4] dark:text-white/35">
                                    {{ $log->ip_address ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-[#737373] dark:text-white/40">
                                    <i class="fa-solid fa-inbox text-3xl mb-2 text-[#C9D5E2] dark:text-white/25 block"></i>
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
