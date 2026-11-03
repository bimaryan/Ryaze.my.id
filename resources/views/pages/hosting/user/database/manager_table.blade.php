@extends('pages.hosting.user.database.manager')

@section('manager_content')
<div class="ui-panel overflow-hidden mb-6">
    <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
        <span class="flex items-center gap-2.5 min-w-0">
            <span class="ui-icon-box"><i class="fa-solid fa-table text-xs"></i></span>
            <span class="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#525252] dark:text-white/40 shrink-0">Tabel</span>
            <code class="ui-code text-xs truncate">{{ $tableName }}</code>
        </span>
        <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-[#737373] dark:text-white/40 whitespace-nowrap shrink-0">Total: {{ number_format($totalRows) }} baris</span>
    </div>

    <!-- Data Table -->
    <div class="overflow-x-auto w-full">
        <table class="w-full text-left border-collapse text-sm whitespace-nowrap">
            <thead>
                <tr class="bg-[#F4F8FC] dark:bg-[#12233A] border-b border-[#E3EAF1] dark:border-[#2A4463]">
                    @foreach($columns as $col)
                        <th class="px-4 py-3 font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#737373] dark:text-white/40">{{ $col['Field'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-[#EDF2F7] dark:divide-[#24384F]">
                @forelse($rows as $row)
                    <tr class="hover:bg-[#E8F0F9] dark:hover:bg-[#1A2F49] transition-colors">
                        @foreach($columns as $col)
                            <td class="px-4 py-2.5 font-mono text-xs text-[#525252] dark:text-white/70 max-w-[200px] truncate" title="{{ htmlspecialchars((string) $row[$col['Field']]) }}">
                                {{ \Illuminate\Support\Str::limit((string) $row[$col['Field']], 50) }}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) }}" class="px-4 py-16 text-center font-mono text-[11px] uppercase tracking-[0.2em] text-[#94A3B4] dark:text-white/30">
                            Tabel ini masih kosong.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($totalPages > 1)
    <div class="px-5 py-3 border-t border-[#EDF2F7] dark:border-[#24384F] flex justify-between items-center bg-[#FAFBFD] dark:bg-[#0F1D30]">
        <div class="ui-pagination-info">
            Halaman {{ $page }} dari {{ $totalPages }}
        </div>
        <div class="flex items-center gap-1">
            @if($page > 1)
                <a href="?page={{ $page - 1 }}" class="ui-btn ui-btn-secondary">Prev</a>
            @endif

            @if($page < $totalPages)
                <a href="?page={{ $page + 1 }}" class="ui-btn ui-btn-secondary">Next</a>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
