@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Progres Pesanan Joki" 
            subtitle="Pantau status pengerjaan proyek Anda yang sedang aktif." 
            icon="fa-solid fa-spinner">
            <x-slot:actions>
                <a href="{{ route('user_joki.dashboard') }}" class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="mt-6">
            <div>
                <h2 class="text-lg font-bold text-slate-800 mb-4 px-1">Proyek Sedang Berjalan</h2>
                <x-ui.table>
                    <x-slot:head>
                        <th class="px-6 py-4 whitespace-nowrap">Nama Proyek</th>
                        <th class="px-6 py-4 whitespace-nowrap">Tech Stack</th>
                        <th class="px-6 py-4 whitespace-nowrap w-48">Progres</th>
                        <th class="px-6 py-4 whitespace-nowrap text-center">Status</th>
                        <th class="px-6 py-4 whitespace-nowrap text-center">Aksi</th>
                    </x-slot:head>
                            @forelse($activeOrders as $order)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-slate-800 whitespace-nowrap">
                                        {{ $order->project_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="bg-slate-100 text-slate-600 border border-slate-200 text-xs px-2.5 py-1 rounded-md font-medium">
                                            {{ $order->tech_stack ?? 'Menunggu Info' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 w-48 align-middle">
                                        <div class="flex justify-between text-xs mb-1.5">
                                            <span class="font-medium text-slate-500">Berjalan</span>
                                            <span class="font-bold text-indigo-600">{{ $order->progress }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                            <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500"
                                                style="width: {{ $order->progress }}%"></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        @if ($order->status == 'pending')
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-600 border border-amber-200">Pending</span>
                                        @elseif($order->status == 'progress')
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600 border border-blue-200">In
                                                Progress</span>
                                        @elseif($order->status == 'review')
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-600 border border-purple-200">Butuh
                                                Review</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <a href="{{ route('user_joki.detail', $order->hashid) }}"
                                            class="w-8 h-8 mx-auto rounded-lg flex items-center justify-center text-indigo-600 bg-indigo-50 hover:bg-indigo-600 hover:text-white transition-all duration-200 shadow-sm tooltip" title="Detail Proyek">
                                            <i class="fa-solid fa-file-lines"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div
                                                class="w-16 h-16 mb-4 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center text-2xl">
                                                <i class="fa-solid fa-folder-open"></i>
                                            </div>
                                            <p class="text-slate-500 font-medium">Anda belum memiliki proyek yang sedang
                                                berjalan.</p>
                                            <a href="{{ route('user_joki.create') }}"
                                                class="mt-2 text-indigo-600 hover:text-indigo-700 text-sm font-semibold hover:underline">
                                                Pesan Proyek Sekarang &rarr;
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                </x-ui.table>
            </div>
        </div>
    </x-ui.page-layout>
@endsection
