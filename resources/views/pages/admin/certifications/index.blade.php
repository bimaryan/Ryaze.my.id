@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Sertifikat" 
            subtitle="Kelola sertifikat & lisensi untuk halaman portfolio." 
            icon="fa-solid fa-certificate">
            <x-slot:actions>
                <a href="{{ route('superadmin.certifications.create') }}"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-lg shadow-md transition-colors text-sm">
                    <i class="fa-solid fa-plus mr-2"></i> Tambah
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card class="mt-6">
            <div class="p-4">
                <form action="{{ route('superadmin.certifications.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="flex-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none"
                        placeholder="Cari sertifikat atau penerbit...">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        <i class="fa-solid fa-search mr-1"></i> Cari
                    </button>
                </form>
            </div>

            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Urutan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Nama Sertifikat</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Penerbit</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Terbit</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Kedaluwarsa</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </x-slot:head>

                @forelse($certifications as $c)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors">
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $c->sort_order }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $c->name }}</div>
                            @if($c->credential_id)
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">ID: {{ $c->credential_id }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $c->issuer }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $c->issued_at?->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">
                            @if($c->expired_at)
                                {{ $c->expired_at->format('d M Y') }}
                                @if($c->expired_at->isPast())
                                    <span class="ml-1 px-1.5 py-0.5 text-[10px] font-bold uppercase rounded bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-300">Kadaluarsa</span>
                                @endif
                            @else
                                Tidak berlaku surut
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('superadmin.certifications.status.toggle', $c->hashid) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-2 py-0.5 text-[10px] font-bold uppercase rounded {{ $c->is_active ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }}">
                                    {{ $c->is_active ? 'Aktif' : 'Draft' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('superadmin.certifications.edit', $c->hashid) }}"
                                    class="p-1.5 text-indigo-600 bg-indigo-50 dark:bg-indigo-500/10 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition-colors">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <form action="{{ route('superadmin.certifications.destroy', $c->hashid) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this)"
                                        class="p-1.5 text-rose-600 bg-rose-50 dark:bg-rose-500/10 rounded-lg hover:bg-rose-100 dark:hover:bg-rose-500/20 transition-colors">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-certificate text-3xl mb-3 text-slate-300 dark:text-slate-500"></i>
                            <p>Belum ada sertifikat.</p>
                        </td>
                    </tr>
                @endforelse
            </x-ui.table>

            <div class="p-4">
                {{ $certifications->links() }}
            </div>
        </x-ui.card>
    </x-ui.page-layout>

    <script>
        function confirmDelete(btn) {
            Swal.fire({
                title: 'Hapus Data?',
                text: 'Data ini akan dihapus secara permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) btn.closest('form').submit();
            });
        }
    </script>
@endsection
