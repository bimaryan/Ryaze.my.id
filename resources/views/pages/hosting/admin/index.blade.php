@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50">
        <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg">
                    <i class="fa-solid fa-server text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Manajemen Hosting</h1>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Halo Admin <span class="font-semibold text-indigo-600">{{ Auth::user()->name ?? '' }}</span>. Berikut
                        status server hari ini.
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Uptime Server</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1">99.9%</h3>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center">
                            <i class="fa-solid fa-check"></i></div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Pesanan Pending</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1">3</h3>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center"><i
                                class="fa-solid fa-clock"></i></div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Total Domain</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1">142</h3>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center"><i
                                class="fa-solid fa-globe"></i></div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Storage Server</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1">68%</h3>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center"><i
                                class="fa-solid fa-database"></i></div>
                    </div>
                </div>
            </div>

            <div class="mt-8 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-800">Menunggu Aktivasi</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Klien</th>
                                <th class="px-6 py-4">Domain Req</th>
                                <th class="px-6 py-4">Paket</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 font-medium text-slate-800">Rizky Putra</td>
                                <td class="px-6 py-4">rizky-store.id</td>
                                <td class="px-6 py-4">Paket Basic</td>
                                <td class="px-6 py-4 text-center">
                                    <button
                                        class="text-xs bg-emerald-500 text-white px-3 py-1.5 rounded hover:bg-emerald-600 transition-colors">Aktivasi
                                        Akun</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
