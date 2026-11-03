@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Testimoni" 
            subtitle="Kelola testimoni klien untuk halaman portfolio." 
            icon="fa-solid fa-comment-dots">
            <x-slot:actions>
                <a href="{{ route('superadmin.testimonials.create') }}"
                    class="bg-[#1D5B96] hover:bg-[#2A6FAD] text-white font-bold py-2.5 px-5   transition-colors text-sm">
                    <i class="fa-solid fa-plus mr-2"></i> Tambah
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card class="mt-6">
            <div class="p-4">
                <form action="{{ route('superadmin.testimonials.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="flex-1 bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none"
                        placeholder="Cari nama atau perusahaan...">
                    <button type="submit" class="bg-[#1D5B96] hover:bg-[#2A6FAD] text-white px-5 py-2.5  text-sm font-medium transition-colors">
                        <i class="fa-solid fa-search mr-1"></i> Cari
                    </button>
                </form>
            </div>

            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Urutan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Peran</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-[#737373] uppercase">Rating</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-[#737373] uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-[#737373] uppercase">Aksi</th>
                    </tr>
                </x-slot:head>

                @forelse($testimonials as $t)
                    <tr class="hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828] transition-colors">
                        <td class="px-6 py-4 text-sm text-[#737373]">{{ $t->sort_order }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($t->avatar_path)
                                    <img src="{{ asset('storage/' . $t->avatar_path) }}" alt="{{ $t->name }}" class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-[#E8F0F9] dark:bg-[#1D5B96]/20 text-[#1D5B96] dark:text-[#6BA8DC] flex items-center justify-center text-xs font-bold">
                                        {{ strtoupper(substr($t->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="text-sm font-semibold text-[#0F1A2A] dark:text-white">{{ $t->name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-[#525252] dark:text-white/60">{{ $t->role }}{{ $t->company ? ' @ ' . $t->company : '' }}</td>
                        <td class="px-6 py-4 text-center">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa-solid fa-star {{ $i <= $t->rating ? 'text-amber-400' : 'text-[#C9D5E2] dark:text-white/20' }} text-xs"></i>
                            @endfor
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('superadmin.testimonials.status.toggle', $t->hashid) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-2 py-0.5 text-[10px] font-bold uppercase rounded {{ $t->is_active ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-[#F0F6FC] dark:bg-[#0D1828] text-[#737373] dark:text-white/40' }}">
                                    {{ $t->is_active ? 'Aktif' : 'Draft' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('superadmin.testimonials.edit', $t->hashid) }}"
                                    class="ui-icon-btn ui-icon-btn-primary">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <form action="{{ route('superadmin.testimonials.destroy', $t->hashid) }}" method="POST" class="inline">
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
                            <i class="fa-solid fa-comment-dots text-3xl mb-3 text-[#C9D5E2] dark:text-white/25"></i>
                            <p>Belum ada testimoni.</p>
                        </td>
                    </tr>
                @endforelse
            </x-ui.table>

            <div class="p-4">
                {{ $testimonials->links() }}
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
