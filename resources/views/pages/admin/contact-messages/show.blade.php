@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Detail Pesan" 
            subtitle="Pesan dari formulir kontak portfolio." 
            icon="fa-solid fa-envelope-open-text">
            <x-slot:actions>
                <a href="{{ route('superadmin.contact_messages.index') }}"
                    class="inline-flex items-center bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-200 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card class="w-full mt-6">
            <div class="p-6 space-y-6">
                <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-700">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $message->subject }}</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Dari <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $message->name }}</span>
                            &lt;{{ $message->email }}&gt;
                        </p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                            {{ $message->created_at->format('d M Y H:i') }} WIB
                        </p>
                    </div>
                    <form action="{{ route('superadmin.contact_messages.destroy', $message->hashid) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="button" onclick="confirmDelete(this)"
                            class="p-2 text-rose-600 bg-rose-50 dark:bg-rose-500/10 rounded-lg hover:bg-rose-100 dark:hover:bg-rose-500/20 transition-colors" title="Hapus">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </form>
                </div>

                <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-5">
                    <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $message->message }}</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="mailto:{{ $message->email }}?subject=Re: {{ urlencode($message->subject) }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-indigo-600 to-violet-600 text-white text-sm font-bold hover:shadow-lg hover:shadow-indigo-500/25 transition-all">
                        <i class="fa-solid fa-reply text-xs"></i> Balas via Email
                    </a>
                </div>

                <div class="text-xs text-slate-400 dark:text-slate-500 space-y-1 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <p><i class="fa-solid fa-network-wired mr-1"></i> IP: {{ $message->ip_address ?? '-' }}</p>
                    <p class="break-all"><i class="fa-solid fa-globe mr-1"></i> User Agent: {{ $message->user_agent ?? '-' }}</p>
                </div>
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
