@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ url('user/joki/dashboard') }}" class="text-sm text-slate-500 hover:text-indigo-600 transition-colors">
                &larr; Kembali ke Dashboard
            </a>
            <h1 class="text-2xl font-bold text-slate-800 mt-2">Detail Proyek: {{ $order->project_name }}</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri: Informasi Utama -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Status & Progress -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-800 mb-4">Progres Pengerjaan</h3>
                    <div class="w-full bg-slate-100 rounded-full h-4 mb-2">
                        <div class="bg-indigo-600 h-4 rounded-full transition-all duration-500"
                            style="width: {{ $order->progress }}%"></div>
                    </div>
                    <p class="text-sm text-slate-600">Saat ini pengerjaan mencapai <strong>{{ $order->progress }}%</strong>
                    </p>
                </div>

                <!-- Deskripsi -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-800 mb-2">Deskripsi Kebutuhan</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">{{ $order->description }}</p>
                </div>

                <!-- Hasil Kerja (GitHub/Demo) -->
                @if ($order->status == 'review' || $order->status == 'completed')
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <h3 class="font-bold text-slate-800 mb-4">Hasil Pekerjaan</h3>
                        <div class="flex gap-4">
                            <a href="{{ $order->repo_link }}" target="_blank"
                                class="flex-1 text-center bg-slate-800 text-white py-3 rounded-lg font-semibold hover:bg-slate-900 transition">
                                <i class="fa-brands fa-github mr-2"></i> Buka GitHub
                            </a>
                            <a href="{{ $order->demo_link }}" target="_blank"
                                class="flex-1 text-center bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                                <i class="fa-solid fa-globe mr-2"></i> Lihat Demo
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Kolom Kanan: Sidebar Detail -->
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-800 mb-4">Informasi Proyek</h3>
                    <div class="space-y-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Tech Stack</span>
                            <span class="font-semibold">{{ $order->tech_stack }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Deadline</span>
                            <span
                                class="font-semibold text-rose-600">{{ \Carbon\Carbon::parse($order->deadline)->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Status</span>
                            <span
                                class="px-2 py-1 bg-indigo-50 text-indigo-600 rounded text-xs uppercase font-bold">{{ $order->status }}</span>
                        </div>
                    </div>
                </div>

                <!-- Kontak Admin/Dev -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-800 mb-3">Diskusi Proyek</h3>
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center font-bold">
                            {{ substr($order->worker->name ?? 'Admin', 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold text-sm">{{ $order->worker->name ?? 'Admin Joki' }}</p>
                            <p class="text-xs text-slate-500">Developer in charge</p>
                        </div>
                    </div>
                    <a href="https://wa.me/6285157433395" target="_blank"
                        class="block w-full text-center bg-emerald-500 text-white py-2 rounded-lg font-semibold hover:bg-emerald-600 transition">
                        <i class="fa-brands fa-whatsapp mr-2"></i> Chat Admin
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
