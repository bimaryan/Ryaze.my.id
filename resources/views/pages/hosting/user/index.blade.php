@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Dashboard Hosting" 
            icon="fa-solid fa-gauge">
            <x-slot:subtitle>
                Halo, <span class="font-semibold text-indigo-600">{{ Auth::user()->name ?? 'Klien' }}</span>! Selamat datang kembali.
            </x-slot:subtitle>
            <x-slot:actions>
                <a href="{{ route('user_hosting.create') }}" class="inline-flex justify-center items-center bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    + Deploy Baru
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        {{-- Statistik Dinamis --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-stat-card title="Hosting Aktif" value="{{ $stats['active'] }}" icon="fa-globe" color="emerald" />
            <x-stat-card title="Tagihan Belum Lunas" value="{{ $stats['unpaid'] }}" icon="fa-file-invoice-dollar" color="rose" />
            <x-stat-card title="Tiket Bantuan" value="{{ $stats['tickets'] }}" icon="fa-headset" color="sky" />
        </div>

        {{-- Tabel Layanan --}}
        <x-ui.table class="mt-8">
            <x-slot:header>
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50 flex flex-wrap gap-3 justify-between items-center">
                    <h2 class="text-lg font-bold text-slate-800">Layanan Terbaru</h2>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('user_hosting.projects') }}"
                            class="text-sm text-slate-500 font-semibold hover:text-indigo-600 transition-colors">
                            Lihat Semua <i class="fa-solid fa-arrow-right text-xs ml-1"></i>
                        </a>
                    </div>
                </div>
            </x-slot:header>
            <x-slot:head>
                <th class="px-6 py-4">Domain/Project</th>
                <th class="px-6 py-4">Framework</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </x-slot:head>
            @forelse ($projects as $project)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-800">
                                    <a href="https://{{ $project->ryaze_domain }}" target="_blank"
                                        class="text-indigo-600 hover:underline">
                                        {{ $project->ryaze_domain }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 uppercase">{{ $project->framework }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $colors = ['active' => 'emerald', 'building' => 'amber', 'failed' => 'rose'];
                                        $color = $colors[$project->status] ?? 'slate';
                                    @endphp
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $color }}-50 text-{{ $color }}-600 border border-{{ $color }}-200">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('user_hosting.show', $project->hashid) }}"
                                        class="text-xs bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded hover:bg-indigo-600 hover:text-white transition-colors">
                                        Kelola
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-slate-400">Belum ada project hosting.
                                </td>
                            </tr>
            @endforelse
        </x-ui.table>
    </x-ui.page-layout>
@endsection
