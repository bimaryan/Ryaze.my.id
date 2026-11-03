@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Pengalaman" 
            subtitle="Kelola data pengalaman kerja untuk halaman portfolio." 
            icon="fa-solid fa-briefcase">
            <x-slot:actions>
                <a href="{{ route('superadmin.experiences.create') }}"
                    class="bg-[#1D5B96] hover:bg-[#2A6FAD] text-white font-bold py-2.5 px-5   transition-colors text-sm">
                    <i class="fa-solid fa-plus mr-2"></i> Tambah
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card class="mt-6">
            <div class="p-4">
                <form action="{{ route('superadmin.experiences.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="flex-1 bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none"
                        placeholder="Cari pengalaman...">
                    <button type="submit" class="bg-[#1D5B96] hover:bg-[#2A6FAD] text-white px-5 py-2.5  text-sm font-medium transition-colors">
                        <i class="fa-solid fa-search mr-1"></i> Cari
                    </button>
                </form>
            </div>

            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Urutan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Posisi</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Perusahaan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Periode</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-[#737373] uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-[#737373] uppercase">Aksi</th>
                    </tr>
                </x-slot:head>

                @forelse($experiences as $exp)
                    <tr class="hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828] transition-colors">
                        <td class="px-6 py-4 text-sm text-[#737373]">{{ $exp->sort_order }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-[#0F1A2A] dark:text-white">{{ $exp->role }}</div>
                            <div class="text-xs text-[#737373] dark:text-white/40 mt-0.5">
                                <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-[#F0F6FC] dark:bg-[#0D1828]">{{ $exp->type }}</span>
                                <span class="ml-1"><i class="fa-solid fa-location-dot mr-0.5"></i>{{ $exp->location ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-[#525252] dark:text-white/60">{{ $exp->company }}</td>
                        <td class="px-6 py-4 text-sm text-[#737373]">{{ $exp->period }}</td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('superadmin.experiences.status.toggle', $exp->hashid) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-2 py-0.5 text-[10px] font-bold uppercase rounded {{ $exp->is_active ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-[#F0F6FC] dark:bg-[#0D1828] text-[#737373] dark:text-white/40' }}">
                                    {{ $exp->is_active ? 'Aktif' : 'Draft' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('superadmin.experiences.edit', $exp->hashid) }}"
                                    class="ui-icon-btn ui-icon-btn-primary">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <form action="{{ route('superadmin.experiences.destroy', $exp->hashid) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this)"
                                        class="ui-icon-btn ui-icon-btn-danger">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-[#737373] dark:text-white/40">
                            <i class="fa-solid fa-briefcase text-3xl mb-3 text-[#C9D5E2] dark:text-white/25"></i>
                            <p>Belum ada data pengalaman.</p>
                        </td>
                    </tr>
                @endforelse
            </x-ui.table>

            <div class="p-4">
                {{ $experiences->links() }}
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
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) btn.closest('form').submit();
            });
        }
    </script>
@endsection
