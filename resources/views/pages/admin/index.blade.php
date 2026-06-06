@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50">

        <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg">
                    <i class="fa-solid fa-border-all text-xl"></i>
                </div>

                <div>
                    <h1 class="text-xl font-bold text-slate-800">Dashboard Superadmin</h1>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Selamat datang kembali, <span
                            class="font-semibold text-indigo-600">{{ Auth::user()->name ?? 'Superadmin' }}</span>! Berikut
                        adalah ringkasan sistem hari ini.
                    </p>
                </div>
            </div>

            <div class="hidden md:block">
                <p class="text-sm font-medium text-slate-400">Ryaze Portal / <span class="text-indigo-600">Dashboard</span>
                </p>
            </div>
        </div>

        <div class="mt-6">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <div
                    class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-300">
                    <div class="w-14 h-14 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                        <i class="fa-solid fa-users text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total Pengguna</p>
                        <h3 class="text-2xl font-bold text-slate-800">{{ number_format($totalUsers) }}</h3>
                    </div>
                </div>

                <div
                    class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-300">
                    <div class="w-14 h-14 flex items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                        <i class="fa-solid fa-code-branch text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Pesanan Joki Aktif</p>
                        <h3 class="text-2xl font-bold text-slate-800">{{ number_format($activeJokiOrders) }}</h3>
                    </div>
                </div>

                <div
                    class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-300">
                    <div class="w-14 h-14 flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <i class="fa-solid fa-server text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Server / App Instances</p>
                        <h3 class="text-2xl font-bold text-slate-800">{{ number_format($activeHosting) }}</h3>
                    </div>
                </div>

                <div
                    class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-300">
                    <div class="w-14 h-14 flex items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                        <i class="fa-solid fa-wallet text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Pendapatan Bulan Ini</p>
                        <h3 class="text-xl font-bold text-slate-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                    </div>
                </div>

            </div>

            <div class="mt-8 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-800">Pendaftar Klien Terbaru</h2>
                    <a href="{{ route('superadmin.users.index') }}"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors">Lihat Semua
                        &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th scope="col" class="px-6 py-4">Nama Klien</th>
                                <th scope="col" class="px-6 py-4">Email</th>
                                <th scope="col" class="px-6 py-4">Minat Layanan / Role</th>
                                <th scope="col" class="px-6 py-4 text-center">Tanggal Daftar</th>
                                <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($recentUsers as $user)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-slate-800 flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs uppercase">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        {{ $user->name }}
                                    </td>
                                    <td class="px-6 py-4">{{ $user->email }}</td>
                                    <td class="px-6 py-4">
                                        @if ($user->role == 'user_joki')
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600 border border-blue-200">
                                                Jasa Joki Code
                                            </span>
                                        @elseif($user->role == 'user_hosting')
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-200">
                                                App Deployment
                                            </span>
                                        @else
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                                {{ ucfirst(str_replace('_', ' ', $user->role ?? 'User')) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">{{ $user->created_at->diffForHumans() }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('superadmin.users.show', $user->id) }}"
                                            class="inline-block text-slate-400 hover:text-indigo-600 transition-colors"
                                            title="Detail Profil">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">Belum ada pengguna yang
                                        mendaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
