@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Profil Klien: {{ $user->name }}" 
            subtitle="Lihat informasi detail dan riwayat pesanan klien." 
            icon="fa-solid fa-user">
            <x-slot:actions>
                <a href="{{ route('superadmin.users.index') }}"
                    class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

            <div class="lg:col-span-1 space-y-6">
                <x-ui.card class="p-6">

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
                        <div class="flex items-center gap-4 min-w-0">
                            <div
                                class="w-16 h-16 shrink-0 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center font-black text-3xl shadow-inner">
                                {{ substr($user->name, 0, 1) }}
                            </div>

                            <div class="min-w-0">
                                <h2 class="text-lg font-bold text-slate-800 leading-tight truncate">{{ $user->name }}</h2>
                                <p class="text-sm text-slate-500 mt-0.5 truncate">{{ $user->email }}</p>
                            </div>
                        </div>

                        <div class="shrink-0 self-start sm:self-auto">
                            <span
                                class="inline-block px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-[11px] tracking-wide font-bold uppercase border border-slate-200">
                                {{ str_replace('_', ' ', $user->role ?? 'User') }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-5 text-left space-y-3 text-sm">
                        <div class="flex items-center text-slate-600">
                            <i class="fa-solid fa-calendar-alt w-6 text-center text-slate-400"></i>
                            <span>Terdaftar: <strong
                                    class="text-slate-800">{{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('d F Y') }}</strong></span>
                        </div>
                        <div class="flex items-center text-slate-600">
                            <i class="fa-solid fa-clock w-6 text-center text-slate-400"></i>
                            <span>Waktu: <strong
                                    class="text-slate-800">{{ \Carbon\Carbon::parse($user->created_at)->format('H:i') }}
                                    WIB</strong></span>
                        </div>
                    </div>

                </x-ui.card>
            </div>

            <div class="lg:col-span-2">
                <x-ui.card class="p-6">
                    <h3 class="font-bold text-slate-800 mb-4 border-b pb-2">Riwayat Pesanan Joki Klien Ini</h3>

                    @if (isset($jokiOrders) && $jokiOrders->count() > 0)
                        <div class="space-y-4">
                            @foreach ($jokiOrders as $order)
                                <div
                                    class="border border-slate-200 rounded-xl p-4 hover:bg-slate-50 transition-colors flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="font-bold text-slate-800">{{ $order->project_name }}</h4>
                                            <span
                                                class="text-[10px] px-2 py-0.5 rounded font-bold uppercase
                                                {{ $order->status == 'completed' ? 'bg-emerald-100 text-emerald-700' : ($order->status == 'progress' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600') }}">
                                                {{ $order->status }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500">Order ID: {{ $order->order_number }} | Harga: Rp
                                            {{ number_format($order->price ?? 0, 0, ',', '.') }}</p>
                                    </div>
                                    <a href="{{ route('admin_joki.orders.edit', $order->hashid) }}"
                                        class="inline-block text-xs border border-indigo-200 text-indigo-700 bg-indigo-50 px-4 py-2 rounded-lg hover:bg-indigo-600 hover:text-white transition-all duration-200 font-semibold shadow-sm">
                                        Kelola Proyek
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div
                                class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300 text-2xl">
                                <i class="fa-solid fa-box-open"></i>
                            </div>
                            <p class="text-sm font-medium text-slate-500">Klien ini belum pernah membuat pesanan Joki.</p>
                        </div>
                    @endif
                </x-ui.card>
            </div>

        </div>
    </x-ui.page-layout>
@endsection
