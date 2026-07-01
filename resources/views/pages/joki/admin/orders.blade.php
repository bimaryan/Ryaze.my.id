@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50 relative">
        <!-- Header -->
        <div class="p-5 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="shrink-0 w-11 h-11 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg">
                    <i class="fa-solid fa-clipboard-list text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Daftar Pesanan Masuk</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Kelola dan pantau semua pesanan joki dari klien.</p>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <!-- Tabel Pesanan -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th scope="col" class="px-6 py-4">ID Pesanan</th>
                                <th scope="col" class="px-6 py-4">Klien</th>
                                <th scope="col" class="px-6 py-4">Nama Proyek</th>
                                <th scope="col" class="px-6 py-4 text-center">Status</th>
                                <th scope="col" class="px-6 py-4 text-center">Deadline</th>
                                <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($orders as $order)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-indigo-600">{{ $order->order_number }}</td>
                                    <td class="px-6 py-4 font-medium text-slate-800 flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs uppercase">
                                            {{ substr($order->client->name ?? 'U', 0, 1) }}
                                        </div>
                                        {{ $order->client->name ?? 'Klien Terhapus' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-slate-800">{{ $order->project_name }}</p>
                                        <p class="text-xs text-slate-400 mt-0.5">{{ $order->tech_stack }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($order->status == 'pending')
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-600 border border-amber-200">Pending</span>
                                        @elseif($order->status == 'progress')
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600 border border-blue-200">Progress
                                                ({{ $order->progress }}%)
                                            </span>
                                        @elseif($order->status == 'review')
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-600 border border-purple-200">Review</span>
                                        @elseif($order->status == 'completed')
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-200">Selesai</span>
                                        @endif
                                    </td>
                                    <td
                                        class="px-6 py-4 text-center {{ \Carbon\Carbon::parse($order->deadline)->isPast() ? 'text-red-600 font-bold' : '' }}">
                                        {{ \Carbon\Carbon::parse($order->deadline)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('admin_joki.orders.edit', $order->hashid) }}"
                                            class="inline-block text-xs bg-indigo-600 text-white px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                                            Update Data
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">Belum ada data pesanan
                                        masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
