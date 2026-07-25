@extends('pages.hosting.user.database.manager')

@section('manager_content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <h3 class="font-bold text-slate-800 flex items-center gap-2">
            <i class="fa-solid fa-table text-indigo-500"></i> Tabel: <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">{{ $tableName }}</code>
        </h3>
        <span class="text-xs text-slate-500 font-medium">Total: {{ number_format($totalRows) }} baris</span>
    </div>

    <!-- Data Table -->
    <div class="overflow-x-auto w-full">
        <table class="w-full text-left border-collapse text-sm whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                    @foreach($columns as $col)
                        <th class="px-4 py-3 font-semibold">{{ $col['Field'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                        @foreach($columns as $col)
                            <td class="px-4 py-2.5 text-slate-600 max-w-[200px] truncate" title="{{ htmlspecialchars((string) $row[$col['Field']]) }}">
                                {{ \Illuminate\Support\Str::limit((string) $row[$col['Field']], 50) }}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) }}" class="px-4 py-8 text-center text-slate-400">
                            Tabel ini masih kosong.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($totalPages > 1)
    <div class="px-5 py-3 border-t border-slate-100 flex justify-between items-center bg-slate-50/50">
        <div class="text-xs text-slate-500">
            Halaman {{ $page }} dari {{ $totalPages }}
        </div>
        <div class="flex items-center gap-1">
            @if($page > 1)
                <a href="?page={{ $page - 1 }}" class="px-3 py-1 bg-white border border-slate-200 rounded hover:bg-slate-50 text-xs font-medium text-slate-700">Prev</a>
            @endif
            
            @if($page < $totalPages)
                <a href="?page={{ $page + 1 }}" class="px-3 py-1 bg-white border border-slate-200 rounded hover:bg-slate-50 text-xs font-medium text-slate-700">Next</a>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
