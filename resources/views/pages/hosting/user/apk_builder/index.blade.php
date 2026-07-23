@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Web to APK Builder"
            subtitle="Konversi website Anda menjadi aplikasi Android (.apk) secara otomatis."
            icon="fa-brands fa-android"
            iconColor="indigo">
            <x-slot:actions>
                <a href="{{ route('user_hosting.apk.create') }}"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    <i class="fa-solid fa-plus"></i> Buat Aplikasi Baru
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        @if(session('success'))
            <div class="p-4 mb-6 text-sm text-green-800 rounded-2xl bg-green-50 border border-green-200 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-green-500 text-lg"></i>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 mb-6 text-sm text-red-800 rounded-2xl bg-red-50 border border-red-200 flex items-center gap-3">
                <i class="fa-solid fa-circle-xmark text-red-500 text-lg"></i>
                {{ session('error') }}
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

            @forelse($builds as $build)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800">{{ $build->app_name }}</div>
                        <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $build->package_name }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ $build->app_url }}" target="_blank" class="text-indigo-600 hover:underline text-sm">
                            {{ Str::limit($build->app_url, 35) }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($build->status === 'success')
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700">
                                <i class="fa-solid fa-circle-check mr-1"></i> Selesai
                            </span>
                        @elseif($build->status === 'building')
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-blue-100 text-blue-700">
                                <i class="fa-solid fa-spinner fa-spin mr-1"></i> Membangun...
                            </span>
                        @elseif($build->status === 'failed')
                            <div class="flex flex-col items-center">
                                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 whitespace-nowrap">
                                    <i class="fa-solid fa-circle-xmark mr-1"></i> Gagal
                                </span>
                                <p class="text-xs text-slate-400 mt-1.5 whitespace-nowrap">Cek log untuk detail error</p>
                            </div>
                        @else
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                                <i class="fa-solid fa-hourglass-half mr-1"></i> Antrian
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500">
                        {{ $build->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('user_hosting.apk.progress', $build->id) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200 rounded-lg transition text-xs font-medium">
                                <i class="fa-solid fa-terminal"></i> {{ in_array($build->status, ['pending', 'building']) ? 'Lihat Proses' : 'Log' }}
                            </a>
                            @if($build->status === 'success' && $build->apk_path)
                                <a href="{{ route('user_hosting.apk.download', $build->id) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition text-xs font-medium">
                                    <i class="fa-solid fa-download"></i> Download
                                </a>
                            @endif
                            <form action="{{ route('user_hosting.apk.destroy', $build->id) }}" method="POST"
                                onsubmit="event.preventDefault(); let f = this; Swal.fire({title: 'Hapus Aplikasi?', text: 'Data build ini akan dihapus permanen.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: '<i class=\'fa-solid fa-trash-can mr-1\'></i> Ya, Hapus', cancelButtonText: 'Batal', customClass: {popup: 'rounded-2xl text-sm'}}).then(res => { if(res.isConfirmed) f.submit(); }); return false;">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 rounded-lg transition text-xs font-medium">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-14 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50 mb-4">
                            <i class="fa-brands fa-android text-2xl text-indigo-400"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-800 mb-1">Belum ada Aplikasi</h3>
                        <p class="text-slate-500 text-sm mb-4">Buat aplikasi Android dari website Anda sekarang.</p>
                        <a href="{{ route('user_hosting.apk.create') }}" class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-700 font-medium text-sm">
                            <i class="fa-solid fa-plus"></i> Buat Sekarang
                        </a>
                    </td>
                </tr>
            @endforelse
            <x-slot:pagination>{{ $builds->links() }}</x-slot:pagination>
        </x-ui.table>
    </x-ui.page-layout>

@endsection
