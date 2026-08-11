@extends('index')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header 
        title="Database Manager" 
        subtitle="Kelola tabel dan isi database {{ $database->db_name }}" 
        icon="fa-table" 
        iconColor="indigo">
        <x-slot:actions>
            <a href="{{ route('user_hosting.databases') }}" class="inline-flex justify-center items-center bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/40 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="flex flex-col md:flex-row gap-6">
        <!-- Sidebar: Daftar Tabel -->
        <div class="w-full md:w-64 shrink-0">
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden sticky top-6">
                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 font-bold text-slate-700 dark:text-slate-200 text-sm flex justify-between items-center">
                    <span><i class="fa-solid fa-list mr-1"></i> Daftar Tabel</span>
                    <span class="bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 text-[10px] px-2 py-0.5 rounded-full">{{ count($tables) }}</span>
                </div>
                <div class="p-2 max-h-[60vh] overflow-y-auto">
                    @forelse($tables as $tbl)
                        <a href="{{ route('user_hosting.databases.manager.table', ['hashid' => $database->hashid, 'table' => $tbl]) }}" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-600 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 dark:hover:bg-indigo-500/10 hover:text-indigo-700 dark:hover:text-indigo-400 dark:hover:text-indigo-300 rounded-lg transition-colors {{ (isset($tableName) && $tableName === $tbl) ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 font-semibold' : '' }}">
                            <i class="fa-regular fa-folder-open text-slate-400 dark:text-slate-500"></i>
                            <span class="truncate">{{ $tbl }}</span>
                        </a>
                    @empty
                        <div class="text-center py-6 text-slate-400 dark:text-slate-500 text-xs">
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
