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
                    <tr class="hover:bg-slate-50 dark:bg-slate-800/50 dark:hover:bg-slate-700/40 transition-colors">
                        <td class="px-6 py-4 font-bold text-indigo-600 dark:text-indigo-400">{{ $order->order_number }}</td>
                        <td class="px-6 py-4 font-medium text-slate-800 dark:text-slate-100 flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-xs uppercase">
                                {{ substr($order->client->name ?? 'U', 0, 1) }}
                            </div>
                            {{ $order->client->name ?? 'Klien Terhapus' }}
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800 dark:text-slate-100">{{ $order->project_name }}</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ $order->tech_stack }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if ($order->status == 'pending')
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-300 border border-amber-200 dark:border-amber-500/40">Pending</span>
                            @elseif($order->status == 'progress')
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-300 border border-blue-200 dark:border-blue-500/40">Progress
                                    ({{ $order->progress }}%)
                                </span>
                            @elseif($order->status == 'review')
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-300 border border-purple-200 dark:border-purple-500/40">Review</span>
                            @elseif($order->status == 'completed')
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/40">Selesai</span>
                            @endif
                        </td>
                        <td
                            class="px-6 py-4 text-center {{ \Carbon\Carbon::parse($order->deadline)->isPast() ? 'text-red-600 dark:text-red-300 font-bold' : '' }}">
                            {{ \Carbon\Carbon::parse($order->deadline)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin_joki.orders.edit', $order->hashid) }}"
                                class="w-8 h-8 mx-auto rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-600 hover:text-white transition-all duration-200 shadow-sm tooltip"
                                title="Kelola Pesanan">
                                <i class="fa-solid fa-gear"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">Belum ada data pesanan
                            masuk.</td>
                    </tr>
                @endforelse
                @if ($orders->hasPages())
                    <x-slot:pagination>
                        {{ $orders->links() }}
                    </x-slot:pagination>
                @endif
            </x-ui.table>
        </div>
    </x-ui.page-layout>
@endsection
