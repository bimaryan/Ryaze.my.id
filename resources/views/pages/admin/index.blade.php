@extends('index')

@section('content')
    <!-- sm:ml-64 agar konten tidak tertutup sidebar, pt-20 agar tidak tertutup navbar atas -->
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50">

        <!-- Header Dashboard -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <!-- Icon Accent -->
                <div class="w-12 h-12 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg">
                    <i class="fa-solid fa-border-all text-xl"></i>
                </div>

                <!-- Title & Welcome Message -->
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Dashboard Superadmin</h1>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Selamat datang kembali, <span
                            class="font-semibold text-indigo-600">{{ Auth::user()->name ?? 'Superadmin' }}</span>! Berikut
                        adalah ringkasan sistem hari ini.
                    </p>
                </div>
            </div>

            <!-- Breadcrumb -->
            <div class="hidden md:block">
                <p class="text-sm font-medium text-slate-400">Ryaze Portal / <span class="text-indigo-600">Dashboard</span>
                </p>
            </div>
        </div>

        <!-- Space untuk konten selanjutnya (Card Statistik, Tabel, dll) -->
        <div class="mt-6">

            <!-- GRID STATISTIK -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <!-- Card 1: Total Pengguna -->
                <div
                    class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-300">
                    <div class="w-14 h-14 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                        <i class="fa-solid fa-users text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total Pengguna</p>
                        <h3 class="text-2xl font-bold text-slate-800">1,254</h3>
                    </div>
                </div>

                <!-- Card 2: Pesanan Joki -->
                <div
                    class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-300">
                    <div class="w-14 h-14 flex items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                        <i class="fa-solid fa-code-branch text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Pesanan Joki Aktif</p>
                        <h3 class="text-2xl font-bold text-slate-800">28</h3>
                    </div>
                </div>

                <!-- Card 3: Layanan Hosting (Disesuaikan ke PaaS) -->
                <div
                    class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-300">
                    <div class="w-14 h-14 flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <i class="fa-solid fa-server text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Server / App Instances</p>
                        <h3 class="text-2xl font-bold text-slate-800">142</h3>
                    </div>
                </div>

                <!-- Card 4: Pendapatan -->
                <div
                    class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-300">
                    <div class="w-14 h-14 flex items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                        <i class="fa-solid fa-wallet text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Pendapatan Bulan Ini</p>
                        <h3 class="text-xl font-bold text-slate-800">Rp 4.5M</h3>
                    </div>
                </div>

            </div>

            <!-- SECTION TABEL TERBARU -->
            <div class="mt-8 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-800">Pendaftar Klien Terbaru</h2>
                    <a href="#"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors">Lihat Semua
                        &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th scope="col" class="px-6 py-4">Nama Klien</th>
                                <th scope="col" class="px-6 py-4">Email</th>
                                <th scope="col" class="px-6 py-4">Minat Layanan</th>
                                <th scope="col" class="px-6 py-4 text-center">Tanggal Daftar</th>
                                <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <!-- Contoh Data 1 -->
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-800 flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">
                                        A
                                    </div>
                                    Ahmad Maulana
                                </td>
                                <td class="px-6 py-4">ahmad.m@gmail.com</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600 border border-blue-200">
                                        Jasa Joki Code
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">Hari ini, 10:45</td>
                                <td class="px-6 py-4 text-center">
                                    <button class="text-slate-400 hover:text-indigo-600 transition-colors"
                                        title="Detail Profil"><i class="fa-solid fa-eye"></i></button>
                                </td>
                            </tr>

                            <!-- Contoh Data 2 (Disesuaikan ke PaaS) -->
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-800 flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs">
                                        S
                                    </div>
                                    Siti Nurhaliza
                                </td>
                                <td class="px-6 py-4">siti.nur@company.com</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-200">
                                        App Deployment
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">Kemarin, 14:20</td>
                                <td class="px-6 py-4 text-center">
                                    <button class="text-slate-400 hover:text-indigo-600 transition-colors"
                                        title="Detail Profil"><i class="fa-solid fa-eye"></i></button>
                                </td>
                            </tr>

                            <!-- Contoh Data 3 -->
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-800 flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">
                                        B
                                    </div>
                                    Budi Santoso
                                </td>
                                <td class="px-6 py-4">budi.s@yahoo.com</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600 border border-blue-200">
                                        Jasa Joki Code
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">02 Jun 2026</td>
                                <td class="px-6 py-4 text-center">
                                    <button class="text-slate-400 hover:text-indigo-600 transition-colors"
                                        title="Detail Profil"><i class="fa-solid fa-eye"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
