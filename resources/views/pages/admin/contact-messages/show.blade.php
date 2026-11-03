@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Detail Pesan" 
            subtitle="Pesan dari formulir kontak portfolio." 
            icon="fa-solid fa-envelope-open-text">
            <x-slot:actions>
                <a href="{{ route('superadmin.contact_messages.index') }}"
                    class="inline-flex items-center bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463] hover:bg-[#E8F0F9] dark:hover:bg-[#0D1828] text-[#0F1A2A] dark:text-white px-5 py-2.5  text-sm font-medium transition ">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card class="w-full mt-6">
            <div class="p-6 space-y-6">
                <div class="flex items-start justify-between gap-4 pb-4 border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                    <div>
                        <h2 class="text-lg font-bold text-[#0F1A2A] dark:text-white">{{ $message->subject }}</h2>
                        <p class="text-sm text-[#737373] dark:text-white/40 mt-1">
                            Dari <span class="font-semibold text-[#0F1A2A] dark:text-white">{{ $message->name }}</span>
                            &lt;{{ $message->email }}&gt;
                        </p>
                        <p class="text-xs text-[#94A3B4] dark:text-white/35 mt-1">
                            {{ $message->created_at->format('d M Y H:i') }} WIB
                        </p>
                    </div>
                    <form action="{{ route('superadmin.contact_messages.destroy', $message->hashid) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="button" onclick="confirmDelete(this)"
                            class="ui-icon-btn ui-icon-btn-danger" title="Hapus">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </form>
                </div>

                <div class="bg-[#F4F8FC] dark:bg-[#0D1828]  p-5">
                    <p class="text-sm text-[#525252] dark:text-white/60 leading-relaxed whitespace-pre-line">{{ $message->message }}</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="mailto:{{ $message->email }}?subject=Re: {{ urlencode($message->subject) }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1D5B96] hover:bg-[#2A6FAD] text-white text-sm font-bold transition-colors">
                        <i class="fa-solid fa-reply text-xs"></i> Balas via Email
                    </a>
                </div>

                <div class="text-xs text-[#94A3B4] dark:text-white/35 space-y-1 pt-4 border-t border-[#E3EAF1] dark:border-[#1B2C44]">
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
