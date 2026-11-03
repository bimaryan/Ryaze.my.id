@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Pesan Masuk" 
            subtitle="Pesan dari formulir kontak halaman portfolio." 
            icon="fa-solid fa-inbox">
            <x-slot:actions>
                <div class="flex gap-2">
                    <a href="{{ route('superadmin.contact_messages.index') }}"
                        class="px-4 py-2.5  text-sm font-medium transition border {{ !request('status') ? 'bg-[#1D5B96] text-white border-[#1D5B96]' : 'bg-[#F4F8FC] dark:bg-[#0D1828] border-[#C9D5E2] dark:border-[#1B2C44] text-[#525252] dark:text-white/60' }}">
                        Semua
                    </a>
                    <a href="{{ route('superadmin.contact_messages.index', ['status' => 'unread']) }}"
                        class="px-4 py-2.5  text-sm font-medium transition border {{ request('status') === 'unread' ? 'bg-[#1D5B96] text-white border-[#1D5B96]' : 'bg-[#F4F8FC] dark:bg-[#0D1828] border-[#C9D5E2] dark:border-[#1B2C44] text-[#525252] dark:text-white/60' }}">
                        Belum Dibaca
                    </a>
                </div>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card class="mt-6">
            <div class="p-4">
                <form action="{{ route('superadmin.contact_messages.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="flex-1 bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none"
                        placeholder="Cari nama, email, atau subjek...">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <button type="submit" class="bg-[#1D5B96] hover:bg-[#2A6FAD] text-white px-5 py-2.5  text-sm font-medium transition-colors">
                        <i class="fa-solid fa-search mr-1"></i> Cari
                    </button>
                </form>
            </div>

            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Pengirim</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Subjek</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Pesan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#737373] uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-[#737373] uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-[#737373] uppercase">Aksi</th>
                    </tr>
                </x-slot:head>

                @forelse($messages as $msg)
                    <tr class="hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828] transition-colors {{ !$msg->is_read ? 'bg-[#E8F0F9]/60 dark:bg-[#1D5B96]/5' : '' }}">
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-[#0F1A2A] dark:text-white">{{ $msg->name }}</div>
                            <div class="text-xs text-[#737373] dark:text-white/40 mt-0.5">{{ $msg->email }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-[#525252] dark:text-white/60">{{ $msg->subject }}</td>
                        <td class="px-6 py-4 text-sm text-[#737373] dark:text-white/40 max-w-xs truncate">{{ $msg->message }}</td>
                        <td class="px-6 py-4 text-sm text-[#737373]">{{ $msg->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded {{ $msg->is_read ? 'bg-[#F0F6FC] dark:bg-[#0D1828] text-[#737373] dark:text-white/40' : 'bg-[#E8F0F9] dark:bg-[#1D5B96]/20 text-[#1D5B96] dark:text-[#6BA8DC]' }}">
                                {{ $msg->is_read ? 'Dibaca' : 'Baru' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('superadmin.contact_messages.show', $msg->hashid) }}"
                                    class="ui-icon-btn ui-icon-btn-primary" title="Baca">
                                    <i class="fa-solid fa-envelope-open-text text-xs"></i>
                                </a>
                                <form action="{{ route('superadmin.contact_messages.destroy', $msg->hashid) }}" method="POST" class="inline">
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
                            <i class="fa-solid fa-inbox text-3xl mb-3 text-[#C9D5E2] dark:text-white/25"></i>
                            <p>Belum ada pesan masuk.</p>
                        </td>
                    </tr>
                @endforelse
            </x-ui.table>

            <div class="p-4">
                {{ $messages->links() }}
            </div>
        </x-ui.card>
    </x-ui.page-layout>

    <script>
        function confirmDelete(btn) {
            Swal.fire({
                title: 'Hapus Pesan?',
                text: 'Pesan ini akan dihapus secara permanen.',
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
