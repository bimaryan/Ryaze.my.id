@extends('index')

@section('content')
    <x-ui.page-layout>
        <!-- Header -->
        <x-ui.page-header 
            title="Daftar Pesanan Masuk" 
            subtitle="Kelola dan pantau semua pesanan joki dari klien." 
            icon="fa-solid fa-clipboard-list" 
        />

        <div class="mt-6">
            <!-- Tabel Pesanan -->
            <x-ui.table>
                <x-slot:head>
                    <th scope="col" class="px-6 py-4">ID Pesanan</th>
                    <th scope="col" class="px-6 py-4">Klien</th>
                    <th scope="col" class="px-6 py-4">Nama Proyek</th>
                    <th scope="col" class="px-6 py-4 text-center">Status</th>
                    <th scope="col" class="px-6 py-4 text-center">Deadline</th>
                    <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                </x-slot:head>
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
            </x-ui.table>
        </div>
    </x-ui.page-layout>
@endsection
