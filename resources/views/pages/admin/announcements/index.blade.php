@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Informasi / Pengumuman" 
            subtitle="Kelola informasi dan pengumuman untuk ditampilkan kepada pengguna." 
            icon="fa-solid fa-circle-info">
            <x-slot:actions>
                <a href="{{ route('superadmin.announcements.create') }}"
                    class="inline-flex justify-center items-center bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm border border-transparent">
                    <i class="fa-solid fa-plus mr-2"></i> Tambah Informasi
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div>
            <div class="flex flex-col sm:flex-row justify-between items-center mb-4 px-1 gap-4">
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    {{-- Status Filter --}}
                    <div class="flex bg-slate-100 dark:bg-slate-700/50 rounded-lg p-0.5">
                        <a href="{{ route('superadmin.announcements.index', request()->except('status')) }}" 
                            class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ !request()->has('status') ? 'bg-white dark:bg-slate-800/60 shadow-sm text-slate-800 dark:text-slate-100' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                            Semua
                        </a>
                        <a href="{{ route('superadmin.announcements.index', array_merge(request()->except('status'), ['status' => '1'])) }}" 
                            class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ request('status') === '1' ? 'bg-white dark:bg-slate-800/60 shadow-sm text-slate-800 dark:text-slate-100' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                            Aktif
                        </a>
                        <a href="{{ route('superadmin.announcements.index', array_merge(request()->except('status'), ['status' => '0'])) }}" 
                            class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ request('status') === '0' ? 'bg-white dark:bg-slate-800/60 shadow-sm text-slate-800 dark:text-slate-100' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                            Tidak Aktif
                        </a>
                    </div>

                    {{-- Type Filter --}}
                    <select onchange="window.location.href=this.value" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                        <option value="{{ route('superadmin.announcements.index', request()->except('type')) }}" {{ !request()->has('type') ? 'selected' : '' }}>Semua Tipe</option>
                        <option value="{{ route('superadmin.announcements.index', array_merge(request()->except('type'), ['type' => 'info'])) }}" {{ request('type') === 'info' ? 'selected' : '' }}>Info</option>
                        <option value="{{ route('superadmin.announcements.index', array_merge(request()->except('type'), ['type' => 'update'])) }}" {{ request('type') === 'update' ? 'selected' : '' }}>Update</option>
                        <option value="{{ route('superadmin.announcements.index', array_merge(request()->except('type'), ['type' => 'maintenance'])) }}" {{ request('type') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="{{ route('superadmin.announcements.index', array_merge(request()->except('type'), ['type' => 'warning'])) }}" {{ request('type') === 'warning' ? 'selected' : '' }}>Warning</option>
                    </select>
                </div>

                <form action="{{ route('superadmin.announcements.index') }}" method="GET" class="flex items-center w-full sm:w-auto">
                    @foreach(['status', 'type'] as $key)
                        @if(request()->has($key))
                            <input type="hidden" name="{{ $key }}" value="{{ request($key) }}">
                        @endif
                    @endforeach
                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <i class="fa-solid fa-search text-slate-400 dark:text-slate-500"></i>
                        </div>
                        <input type="text" name="search" class="text-slate-800 dark:text-slate-100 block ps-9 p-2 w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="Cari judul informasi..." value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="p-2 ms-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                        Cari
                    </button>
                    @if(request()->has('search') && request()->search != '')
                        <a href="{{ route('superadmin.announcements.index') }}" class="p-2 ms-2 text-sm font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700/50 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <x-ui.table>
                <x-slot:head>
                    <th scope="col" class="px-6 py-4">Judul</th>
                    <th scope="col" class="px-6 py-4">Tipe</th>
                    <th scope="col" class="px-6 py-4">Target</th>
                    <th scope="col" class="px-6 py-4">Jadwal</th>
                    <th scope="col" class="px-6 py-4">Status</th>
                    <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                </x-slot:head>

                @forelse($announcements as $item)
                    <tr class="hover:bg-slate-50 dark:bg-slate-800/50 dark:hover:bg-slate-700/40 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @php
                                    $typeColors = [
                                        'info'        => 'bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-100 dark:border-blue-500/30',
                                        'update'      => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-500/30',
                                        'maintenance' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-100 dark:border-amber-500/30',
                                        'warning'     => 'bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-100 dark:border-rose-500/30',
                                    ];
                                    $typeIcons = [
                                        'info'        => 'fa-circle-info',
                                        'update'      => 'fa-arrow-up',
                                        'maintenance' => 'fa-person-digging',
                                        'warning'     => 'fa-triangle-exclamation',
                                    ];
                                @endphp
                                <div class="w-10 h-10 rounded-lg {{ $typeColors[$item->type] }} flex items-center justify-center border shrink-0">
                                    <i class="fa-solid {{ $typeIcons[$item->type] }}"></i>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="font-medium text-slate-800 dark:text-slate-100 truncate max-w-[250px]">
                                        @if($item->is_pinned)
                                            <i class="fa-solid fa-thumbtack text-amber-500 dark:text-amber-400 mr-1 text-[10px]"></i>
                                        @endif
                                        {{ $item->title }}
                                    </span>
                                    <span class="text-xs text-slate-400 dark:text-slate-500">{{ Str::limit($item->content, 60) }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded {{ $typeColors[$item->type] }}">{{ $item->type }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-slate-700 dark:text-slate-300 capitalize">{{ $item->audience }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($item->starts_at || $item->expires_at)
                                <div class="text-sm text-slate-900 dark:text-slate-100">
                                    {{ $item->starts_at ? $item->starts_at->format('d M Y, H:i') : '...' }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    s.d. {{ $item->expires_at ? $item->expires_at->format('d M Y, H:i') : '...' }}
                                </div>
                            @else
                                <span class="text-xs text-slate-400 dark:text-slate-500">Selalu aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($item->is_active)
                                <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold uppercase rounded">Aktif</span>
                            @else
                                <span class="px-2 py-0.5 bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 text-[10px] font-bold uppercase rounded">Tidak Aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ route('superadmin.announcements.status', $item->hashid) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}" class="p-1.5 rounded-lg transition {{ $item->is_active ? 'text-emerald-500 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20' : 'text-slate-400 dark:text-slate-500 hover:text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-500/10' }}">
                                        <i class="fa-solid {{ $item->is_active ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                    </button>
                                </form>
                                <a href="{{ route('superadmin.announcements.edit', $item->hashid) }}" class="p-1.5 text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 rounded-lg transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('superadmin.announcements.destroy', $item->hashid) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this)" class="p-1.5 text-rose-600 dark:text-rose-300 bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-100 dark:hover:bg-rose-500/20 rounded-lg transition">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-circle-info text-3xl mb-3 text-slate-300 dark:text-slate-400"></i>
                            <p>Belum ada data informasi / pengumuman.</p>
                        </td>
                    </tr>
                @endforelse
            </x-ui.table>

            @if($announcements->hasPages())
                <div class="mt-4">
                    {{ $announcements->links() }}
                </div>
            @endif
        </div>

        <script>
            function confirmDelete(button) {
                Swal.fire({
                    title: 'Hapus Informasi?',
                    text: 'Informasi ini akan dihapus permanen dan tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.closest('form').submit();
                    }
                });
            }
        </script>
    </x-ui.page-layout>
@endsection
