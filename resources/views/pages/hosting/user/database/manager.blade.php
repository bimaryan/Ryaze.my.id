@extends('index')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header 
        title="Database Manager" 
        eyebrow="Editor Data"
        subtitle="Kelola tabel dan isi database {{ $database->db_name }}" 
        icon="fa-table" 
        iconColor="indigo">
        <x-slot:actions>
            <a href="{{ route('user_hosting.databases') }}" class="ui-btn ui-btn-md ui-btn-secondary">
                <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="flex flex-col md:flex-row gap-6">
        <!-- Sidebar: Daftar Tabel -->
        <div class="w-full md:w-64 shrink-0">
            <div class="ui-panel overflow-hidden sticky top-6">
                <div class="ui-panel-head px-4 py-3 bg-[#F4F8FC] dark:bg-[#12233A]">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-list text-[10px] text-[#1D5B96] dark:text-[#6BA8DC]"></i>
                        <span class="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">Daftar Tabel</span>
                        <span class="font-mono text-[10px] text-[#1D5B96] dark:text-[#6BA8DC] border border-[#C9D5E2] dark:border-[#2A4463] bg-[#E8F0F9] dark:bg-[#0F1D30] px-1.5 py-px">{{ count($tables) }}</span>
                    </span>
                    <span class="ui-status"><span class="ui-status-dot"></span>{{ $database->db_name }}</span>
                </div>
                <div class="p-2 max-h-[60vh] overflow-y-auto divide-y divide-[#EDF2F7] dark:divide-[#24384F]">
                    @forelse($tables as $tbl)
                        <a href="{{ route('user_hosting.databases.manager.table', ['hashid' => $database->hashid, 'table' => $tbl]) }}" class="flex items-center gap-2 px-3 py-2 text-sm font-mono text-[#525252] dark:text-white/70 hover:bg-[#E8F0F9] dark:hover:bg-[#1A2F49] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors {{ (isset($tableName) && $tableName === $tbl) ? 'bg-[#E8F0F9] dark:bg-[#12233A] text-[#1D5B96] dark:text-[#6BA8DC] font-semibold' : '' }}">
                            <i class="fa-regular fa-folder-open text-[#8A9BAE] dark:text-white/45 text-xs"></i>
                            <span class="truncate">{{ $tbl }}</span>
                        </a>
                    @empty
                        <div class="text-center py-6 font-mono text-[10px] uppercase tracking-[0.2em] text-[#94A3B4] dark:text-white/30">
                            Tidak ada tabel ditemukan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Konten Kanan -->
        <div class="flex-1 min-w-0">
            @yield('manager_content', View::make('pages.hosting.user.database.manager_empty', ['database' => $database]))
        </div>
    </div>
</x-ui.page-layout>
@endsection
