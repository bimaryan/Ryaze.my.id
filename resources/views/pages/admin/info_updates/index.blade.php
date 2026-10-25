@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Info Update"
            subtitle="Kelola info & pengumuman yang dibaca user di halaman /updates."
            icon="fa-solid fa-bullhorn">
            <x-slot:actions>
                <a href="{{ route('superadmin.info_updates.create') }}"
                    class="ui-btn ui-btn-md ui-btn-primary border-transparent">
                    <i class="fa-solid fa-plus mr-2"></i> Tambah Info Update
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="mt-6">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-4 px-1 gap-4">
                {{-- Filter Kategori --}}
                <div class="flex flex-wrap gap-1 rounded-lg p-0.5">
                    <a href="{{ route('superadmin.info_updates.index', request()->except('category')) }}"
                        class="{{ !request()->filled('category') ? 'ui-pill ui-pill-active' : 'ui-pill' }}">
                        Semua
                    </a>
                    @foreach(\App\Models\InfoUpdate::CATEGORY_META as $key => $meta)
                        <a href="{{ route('superadmin.info_updates.index', array_merge(request()->except('category'), ['category' => $key])) }}"
                            class="{{ request('category') === $key ? 'ui-pill ui-pill-active' : 'ui-pill' }}">
                            {{ $meta['label'] }}
                        </a>
                    @endforeach
                </div>

                {{-- Cari --}}
                <form action="{{ route('superadmin.info_updates.index') }}" method="GET" class="flex min-w-0 items-center w-full sm:w-auto">
                    @if (request()->filled('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <div class="relative min-w-0 w-full sm:w-64">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <i class="fa-solid fa-search text-slate-400 dark:text-slate-500"></i>
                        </div>
                        <input type="text" name="search" placeholder="Cari info update..." value="{{ request('search') }}"
                            class="ui-field ps-9">
                    </div>
                    <button type="submit" class="ui-btn ui-btn-md ui-btn-primary ms-2">
                        Cari
                    </button>
                    @if (request()->filled('search'))
                        <a href="{{ route('superadmin.info_updates.index') }}" class="p-2 ms-2 text-sm font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700/50 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <x-ui.table>
                <x-slot:head>
                    <th scope="col" class="px-6 py-4">Info Update</th>
                    <th scope="col" class="px-6 py-4">Kategori</th>
                    <th scope="col" class="px-6 py-4">Prioritas</th>
                    <th scope="col" class="px-6 py-4">Tayang</th>
                    <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                </x-slot:head>

                @forelse($updates as $update)
                    <tr class="hover:bg-slate-50 dark:bg-slate-800/50 dark:hover:bg-slate-700/40 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 w-9 h-9 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center border border-indigo-100 dark:border-indigo-500/30">
                                    <i class="fa-solid {{ $update->categoryIcon() }} text-indigo-500 dark:text-indigo-400 text-xs"></i>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <a href="{{ route('superadmin.info_updates.edit', $update->hashid) }}"
                                       class="font-medium text-slate-800 dark:text-slate-100 hover:text-indigo-600 dark:hover:text-indigo-400 truncate max-w-[280px]">
                                        {{ $update->title }}
                                    </a>
                                    <span class="text-xs text-slate-400 dark:text-slate-500">{{ Str::limit($update->description, 60) }}</span>
                                    @if ($update->version)
                                        <span class="ui-badge ui-badge-neutral mt-0.5 inline-block text-[10px] font-mono px-1.5 w-fit">
                                            {{ $update->version }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded {{ $update->categoryBadgeClass() }}">
                                {{ $update->categoryLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded {{ $update->priorityBadgeClass() }}">
                                {{ $update->priorityLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if (! $update->is_active)
                                <span class="ui-badge ui-badge-neutral text-[10px]">Nonaktif</span>
                            @elseif ($update->published_at && $update->published_at->isFuture())
                                <span class="block text-xs font-semibold text-amber-600 dark:text-amber-400">Terjadwal</span>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ $update->published_at->format('d M Y, H:i') }}</span>
                            @else
                                <span class="block text-sm text-slate-900 dark:text-slate-100">{{ ($update->published_at ?? $update->created_at)->format('d M Y') }}</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $update->author?->name ?? 'Superadmin' }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ route('superadmin.info_updates.status', $update->hashid) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="{{ $update->is_active ? 'Sembunyikan dari user' : 'Tampilkan ke user' }}"
                                        class="ui-icon-btn {{ $update->is_active ? 'ui-icon-btn-success' : 'ui-icon-btn-neutral' }}">
                                        <i class="fa-solid {{ $update->is_active ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                    </button>
                                </form>
                                <a href="{{ route('superadmin.info_updates.edit', $update->hashid) }}" title="Edit"
                                    class="ui-icon-btn ui-icon-btn-primary">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('superadmin.info_updates.destroy', $update->hashid) }}" method="POST" class="inline"
                                    onsubmit="event.preventDefault(); swConfirm('Hapus info update?', '{{ $update->title }} akan hilang dari halaman user.').then(r => { if(r.isConfirmed) f.submit(); }); return false;">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Hapus"
                                        class="ui-icon-btn ui-icon-btn-danger">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-bullhorn text-3xl mb-3 text-slate-300 dark:text-slate-400"></i>
                            <p>Belum ada info update.</p>
                        </td>
                    </tr>
                @endforelse
            </x-ui.table>

            @if ($updates->hasPages())
                <div class="mt-4">
                    {{ $updates->links() }}
                </div>
            @endif
        </div>
    </x-ui.page-layout>
@endsection