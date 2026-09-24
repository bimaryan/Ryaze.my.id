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
                        class="px-4 py-2.5 rounded-lg text-sm font-medium transition border {{ !request('status') ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300' }}">
                        Semua
                    </a>
                    <a href="{{ route('superadmin.contact_messages.index', ['status' => 'unread']) }}"
                        class="px-4 py-2.5 rounded-lg text-sm font-medium transition border {{ request('status') === 'unread' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300' }}">
                        Belum Dibaca
                    </a>
                </div>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card class="mt-6">
            <div class="p-4">
                <form action="{{ route('superadmin.contact_messages.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="flex-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none"
                        placeholder="Cari nama, email, atau subjek...">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        <i class="fa-solid fa-search mr-1"></i> Cari
                    </button>
                </form>
            </div>

            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Pengirim</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Subjek</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Pesan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </x-slot:head>

                @forelse($messages as $msg)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors {{ !$msg->is_read ? 'bg-indigo-50/50 dark:bg-indigo-500/5' : '' }}">
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $msg->name }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $msg->email }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $msg->subject }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 max-w-xs truncate">{{ $msg->message }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $msg->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded {{ $msg->is_read ? 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400' : 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300' }}">
                                {{ $msg->is_read ? 'Dibaca' : 'Baru' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('superadmin.contact_messages.show', $msg->hashid) }}"
                                    class="p-1.5 text-indigo-600 bg-indigo-50 dark:bg-indigo-500/10 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition-colors" title="Baca">
                                    <i class="fa-solid fa-envelope-open-text text-xs"></i>
                                </a>
                                <form action="{{ route('superadmin.contact_messages.destroy', $msg->hashid) }}" method="POST" class="inline">
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
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-inbox text-3xl mb-3 text-slate-300 dark:text-slate-500"></i>
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
