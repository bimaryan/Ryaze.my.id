@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Web to APK Builder"
            eyebrow="Android App Builder"
            status="ONLINE"
            subtitle="Konversi website Anda menjadi aplikasi Android (.apk) secara otomatis."
            icon="fa-brands fa-android"
            iconColor="indigo">
            <x-slot:actions>
                <a href="{{ route('user_hosting.apk.create') }}" class="ui-btn ui-btn-md ui-btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    Buat Aplikasi Baru
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        @if (session('success'))
            <div class="mb-5 flex items-start gap-3 border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 dark:border-emerald-800/50 dark:bg-emerald-950/40 dark:text-emerald-300">
                <i class="fa-solid fa-circle-check mt-0.5"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-5 flex items-start gap-3 border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 dark:border-rose-800/50 dark:bg-rose-950/40 dark:text-rose-300">
                <i class="fa-solid fa-circle-xmark mt-0.5"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <x-ui.table>
            <x-slot:head>
                <th class="px-6 py-4">Nama Aplikasi</th>
                <th class="px-6 py-4">URL Website</th>
                <th class="px-6 py-4 text-center">Status</th>
                <th class="px-6 py-4">Dibuat</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </x-slot:head>

            @forelse ($builds as $build)
                <tr class="transition-colors hover:bg-[#E8F0F9] dark:hover:bg-[#1A2F49]">
                    <td class="px-6 py-4">
                        <div class="font-semibold text-[#0F1A2A] dark:text-white">{{ $build->app_name }}</div>
                        <div class="mt-0.5 font-mono text-[11px] text-[#8A9BAE] dark:text-white/40">{{ $build->package_name }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ $build->app_url }}" target="_blank" class="text-sm text-[#1D5B96] hover:underline dark:text-[#6BA8DC]">
                            {{ Str::limit($build->app_url, 35) }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if ($build->status === 'success')
                            <span class="ui-badge ui-badge-success">
                                <i class="fa-solid fa-circle-check"></i> Selesai
                            </span>
                        @elseif ($build->status === 'building')
                            <span class="ui-badge ui-badge-primary">
                                <i class="fa-solid fa-spinner fa-spin"></i> Membangun
                            </span>
                        @elseif ($build->status === 'failed')
                            <span class="ui-badge ui-badge-danger">
                                <i class="fa-solid fa-circle-xmark"></i> Gagal
                            </span>
                        @else
                            <span class="ui-badge ui-badge-neutral">
                                <i class="fa-solid fa-hourglass-half"></i> Antrian
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-[#525252] dark:text-white/60">
                        {{ $build->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap items-center justify-center gap-2">
                            <a href="{{ route('user_hosting.apk.progress', $build->id) }}" class="ui-chip">
                                <i class="fa-solid fa-terminal text-[10px]"></i>
                                {{ in_array($build->status, ['pending', 'building']) ? 'Lihat Proses' : 'Log' }}
                            </a>
                            @if ($build->status === 'success' && $build->apk_path)
                                <a href="{{ route('user_hosting.apk.download', $build->id) }}" data-pjax="false" download
                                   class="inline-flex items-center gap-1.5 border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100 dark:border-emerald-800/50 dark:bg-emerald-950/40 dark:text-emerald-300 dark:hover:bg-emerald-900/40">
                                    <i class="fa-solid fa-download text-[10px]"></i> Download
                                </a>
                            @endif
                            <form action="{{ route('user_hosting.apk.destroy', $build->id) }}" method="POST"
                                  onsubmit="event.preventDefault(); let f = this; swConfirm('Hapus Aplikasi?', 'Data build ini akan dihapus permanen.').then(r => { if (r.isConfirmed) f.submit(); }); return false;">
                                @csrf @method('DELETE')
                                <button type="submit" class="ui-btn ui-btn-sm ui-btn-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center border border-[#E3EAF1] bg-[#F0F6FC] text-[#1D5B96] dark:border-[#2A4463] dark:bg-[#12233A] dark:text-[#6BA8DC]">
                            <i class="fa-brands fa-android text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-[#0F1A2A] dark:text-white">Belum ada aplikasi</h3>
                        <p class="mx-auto mt-1 max-w-sm text-sm text-[#525252] dark:text-white/50">Buat aplikasi Android dari website Anda sekarang.</p>
                        <a href="{{ route('user_hosting.apk.create') }}" class="ui-btn ui-btn-md ui-btn-primary mt-5">
                            <i class="fa-solid fa-plus"></i> Buat Sekarang
                        </a>
                    </td>
                </tr>
            @endforelse

            <x-slot:pagination>{{ $builds->links() }}</x-slot:pagination>
        </x-ui.table>
    </x-ui.page-layout>
@endsection
