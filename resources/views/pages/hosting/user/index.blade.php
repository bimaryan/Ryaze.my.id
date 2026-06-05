@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50">
        <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg">
                    <i class="fa-solid fa-hard-drive text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Dashboard Hosting Saya</h1>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Halo, <span class="font-semibold text-indigo-600">{{ Auth::user()->name ?? 'Klien' }}</span>! Pantau
                        layanan hosting Anda di sini.
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-300">
                    <div class="w-14 h-14 flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <i class="fa-solid fa-globe text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Domain & Hosting Aktif</p>
                        <h3 class="text-2xl font-bold text-slate-800">2</h3>
                    </div>
                </div>
                <div
                    class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-300">
                    <div class="w-14 h-14 flex items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                        <i class="fa-solid fa-file-invoice-dollar text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Tagihan Belum Lunas</p>
                        <h3 class="text-2xl font-bold text-slate-800">1</h3>
                    </div>
                </div>
                <div
                    class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-300">
                    <div class="w-14 h-14 flex items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                        <i class="fa-solid fa-headset text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Tiket Bantuan Terbuka</p>
                        <h3 class="text-2xl font-bold text-slate-800">0</h3>
                    </div>
                </div>
            </div>

            <div class="mt-8 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-800">Layanan Anda</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Domain</th>
                                <th class="px-6 py-4">Paket</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Jatuh Tempo</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-800"><a href="#"
                                        class="text-indigo-600 hover:underline">toko-dea.com</a></td>
                                <td class="px-6 py-4">Paket Bisnis (10GB)</td>
                                <td class="px-6 py-4"><span
                                        class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-200">Aktif</span>
                                </td>
                                <td class="px-6 py-4">12 Nov 2026</td>
                                <td class="px-6 py-4 text-center">
                                    <button
                                        class="text-xs bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded hover:bg-indigo-600 hover:text-white transition-colors">Login
                                        cPanel</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
