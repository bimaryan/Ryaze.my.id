@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Sertifikat" 
            subtitle="Kelola sertifikat & lisensi untuk halaman portfolio." 
            icon="fa-solid fa-certificate">
            <x-slot:actions>
                <a href="{{ route('superadmin.certifications.create') }}"
                    class="bg-[#1D5B96] hover:bg-[#2A6FAD] text-white font-bold py-2.5 px-5   transition-colors text-sm">
                    <i class="fa-solid fa-plus mr-2"></i> Tambah
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card class="mt-6">
            <div class="p-4">
                <form action="{{ route('superadmin.certifications.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="flex-1 bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none"
                        placeholder="Cari sertifikat atau penerbit...">
                    <button type="submit" class="bg-[#1D5B96] hover:bg-[#2A6FAD] text-white px-5 py-2.5  text-sm font-medium transition-colors">
                        <i class="fa-solid fa-search mr-1"></i> Cari
                    </button>
                </form>
            </div>

            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Urutan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Nama Sertifikat</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Penerbit</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Terbit</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Kedaluwarsa</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-[#737373] uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-[#737373] uppercase">Aksi</th>
                    </tr>
                </x-slot:head>

                @forelse($certifications as $c)
                    <tr class="hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828] transition-colors">
                        <td class="px-6 py-4 text-sm text-[#737373]">{{ $c->sort_order }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-[#0F1A2A] dark:text-white">{{ $c->name }}</div>
                            @if($c->credential_id)
                                <div class="text-xs text-[#737373] dark:text-white/40 mt-0.5">ID: {{ $c->credential_id }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-[#525252] dark:text-white/60">{{ $c->issuer }}</td>
                        <td class="px-6 py-4 text-sm text-[#737373]">{{ $c->issued_at?->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-[#737373]">
                            @if($c->expired_at)
                                {{ $c->expired_at->format('d M Y') }}
                                @if($c->expired_at->isPast())
                                    <span class="ml-1 px-1.5 py-0.5 text-[10px] font-bold uppercase rounded bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-500/40">Kadaluarsa</span>
                                @endif
                            @else
                                Tidak berlaku surut
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('superadmin.certifications.status.toggle', $c->hashid) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-2 py-0.5 text-[10px] font-bold uppercase rounded {{ $c->is_active ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-[#F0F6FC] dark:bg-[#0D1828] text-[#737373] dark:text-white/40' }}">
                                    {{ $c->is_active ? 'Aktif' : 'Draft' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('superadmin.certifications.edit', $c->hashid) }}"
                                    class="ui-icon-btn ui-icon-btn-primary">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <form action="{{ route('superadmin.certifications.destroy', $c->hashid) }}" method="POST" class="inline">
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
                        <td colspan="7" class="px-6 py-12 text-center text-[#737373] dark:text-white/40">
                            <i class="fa-solid fa-certificate text-3xl mb-3 text-[#C9D5E2] dark:text-white/25"></i>
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
