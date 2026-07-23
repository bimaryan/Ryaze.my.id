@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- Header --}}
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
            <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-sm text-left text-slate-500">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th scope="col" class="px-6 py-4">App Name</th>
                        <th scope="col" class="px-6 py-4">URL</th>
                        <th scope="col" class="px-6 py-4">Status</th>
                        <th scope="col" class="px-6 py-4">Tanggal</th>
                        <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($builds as $build)
                        <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-900">
                                {{ $build->app_name }}
                                <div class="text-xs text-slate-500 font-normal mt-0.5">{{ $build->package_name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ $build->app_url }}" target="_blank" class="text-indigo-600 hover:underline">{{ Str::limit($build->app_url, 30) }}</a>
                            </td>
                            <td class="px-6 py-4">
                                @if($build->status === 'success')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded-full border border-green-200">Success</span>
                                @elseif($build->status === 'building')
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-1 rounded-full border border-blue-200"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Building</span>
                                @elseif($build->status === 'failed')
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-1 rounded-full border border-red-200">Failed</span>
                                @else
                                    <span class="bg-slate-100 text-slate-800 text-xs font-medium px-2.5 py-1 rounded-full border border-slate-200">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                {{ $build->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($build->status === 'success' && $build->apk_path)
                                    <a href="{{ route('user_hosting.apk.download', $build->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-700 hover:bg-green-100 rounded-lg transition-colors font-medium text-xs">
                                        <i class="fa-solid fa-download"></i> Download APK
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                                    <i class="fa-brands fa-android text-2xl text-slate-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-slate-900 mb-1">Belum ada Aplikasi</h3>
                                <p class="text-slate-500 mb-4">Mulai buat aplikasi Android dari website Anda.</p>
                                <a href="{{ route('user_hosting.apk.create') }}" class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-700 font-medium">
                                    <i class="fa-solid fa-plus"></i> Buat Sekarang
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $builds->links() }}
        </div>
    </x-ui.page-layout>
@endsection