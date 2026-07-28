@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- Header --}}
        <x-ui.page-header 
            title="Riwayat Tagihan" 
            subtitle="Daftar lengkap transaksi dan status pembayaran hosting Anda." 
            icon="fa-file-invoice-dollar" 
            iconColor="emerald">
            <x-slot:actions>
                <a href="{{ route('user_hosting.dashboard') }}"
                    class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        @php
            $plans = \App\Models\User::hostingPlans();
            $colorMap = [
                'indigo' => ['bg' => 'bg-indigo-600', 'text' => 'text-indigo-600', 'btn' => 'bg-indigo-600 hover:bg-indigo-700', 'badge' => 'bg-indigo-100 text-indigo-700 border-indigo-200'],
                'violet' => ['bg' => 'bg-violet-600', 'text' => 'text-violet-600', 'btn' => 'bg-violet-600 hover:bg-violet-700', 'badge' => 'bg-violet-100 text-violet-700 border-violet-200'],
                'amber'  => ['bg' => 'bg-amber-500',  'text' => 'text-amber-600',  'btn' => 'bg-amber-500 hover:bg-amber-600',   'badge' => 'bg-amber-100 text-amber-700 border-amber-200'],
            ];
        @endphp

        @if (!Auth::user()->hasActiveHostingSubscription())
            <div class="mb-6">
                <h3 class="font-bold text-slate-800 mb-1 flex items-center gap-2 text-lg">
                    <i class="fa-solid fa-crown text-indigo-600"></i> Pilih Paket Hosting
                </h3>
                <p class="text-sm text-slate-500 mb-5">Pilih paket yang sesuai kebutuhan Anda dan selesaikan pembayaran untuk mulai deploy project.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    @foreach ($plans as $slug => $plan)
                        @php $price = \App\Models\User::getPlanPrice($slug); $c = $colorMap[$plan['color']]; $isPopular = $slug === 'pro'; @endphp
                        <div class="relative bg-white rounded-2xl border {{ $isPopular ? 'border-violet-400 shadow-lg shadow-violet-100' : 'border-slate-200 shadow-sm' }} overflow-hidden flex flex-col">
                            @if ($isPopular)
                                <div class="absolute top-0 inset-x-0 h-1 bg-violet-500"></div>
                                <div class="absolute top-3 right-3"><span class="text-[10px] font-bold bg-violet-100 text-violet-700 border border-violet-200 px-2 py-0.5 rounded-full">POPULER</span></div>
                            @endif
                            <div class="p-6 flex-1">
                                <div class="w-10 h-10 {{ $c['bg'] }} rounded-xl flex items-center justify-center mb-4">
                                    <i class="fa-solid fa-server text-white text-base"></i>
                                </div>
                                <h4 class="font-bold text-slate-800 text-lg mb-1">{{ $plan['label'] }}</h4>
                                <div class="flex items-baseline gap-1 mb-4">
                                    <span class="text-2xl font-bold {{ $c['text'] }}">Rp {{ number_format($price, 0, ',', '.') }}</span>
                                    <span class="text-xs text-slate-400 font-normal">/bulan</span>
                                </div>
                                <ul class="space-y-2 mb-6">
                                    @foreach ($plan['features'] as $feat)
                                        <li class="flex items-center gap-2 text-sm text-slate-600">
                                            <i class="fa-solid fa-check {{ $c['text'] }} text-xs w-4"></i> {{ $feat }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="px-6 pb-6">
                                <form action="{{ route('user_hosting.billing.subscribe') }}" method="POST" class="space-y-2">
                                    @csrf
                                    <input type="hidden" name="plan" value="{{ $slug }}">
                                    <input type="text" name="voucher_code" placeholder="Kode Voucher (Opsional)" class="uppercase font-mono w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                                    <button type="submit" class="w-full {{ $c['btn'] }} text-white font-bold px-4 py-2.5 rounded-xl shadow-sm transition-all text-sm">
                                        <i class="fa-solid fa-bolt mr-1"></i> Pilih {{ $plan['label'] }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            @php
                $activeBilling = Auth::user()->hostingBillings()->where('status', 'active')->where('next_due_date', '>', now())->latest()->first();
                $currentPlan = $activeBilling->plan ?? 'starter';
                $planColor = $colorMap[$plans[$currentPlan]['color'] ?? 'indigo']['badge'] ?? 'bg-indigo-100 text-indigo-700 border-indigo-200';
            @endphp
            <div class="mb-6 bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-800 text-sm">Langganan Aktif</p>
                        <p class="text-xs text-slate-500">Berlaku hingga {{ $activeBilling?->next_due_date?->format('d M Y') ?? '-' }}</p>
                    </div>
                </div>
                <span class="text-xs font-bold px-3 py-1 rounded-full border {{ $planColor }}">
                    <i class="fa-solid fa-crown mr-1"></i> Paket {{ $plans[$currentPlan]['label'] ?? ucfirst($currentPlan) }}
                </span>
            </div>
        @endif

        <x-ui.table>
            <x-slot:head>
                <th class="px-6 py-4">Invoice / Tanggal</th>
                <th class="px-6 py-4">Keterangan</th>
                <th class="px-6 py-4">Jumlah</th>
                <th class="px-6 py-4">Metode</th>
                <th class="px-6 py-4 text-center">Status</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </x-slot:head>
            @forelse ($billings as $bill)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800">{{ $bill->invoice_number }}</div>
                        <div class="text-xs text-slate-500 font-mono">{{ $bill->created_at->format('d M Y, H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 font-semibold text-slate-800">{{ $bill->project->project_name ?? 'Langganan Akun' }}</td>
                    <td class="px-6 py-4 font-mono font-medium">Rp{{ number_format($bill->amount, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">{{ $bill->payment_method ?? '-' }}</td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $statusClass = match ($bill->status) {
                                'paid' => 'bg-emerald-100 text-emerald-700',
                                'unpaid' => 'bg-amber-100 text-amber-700',
                                'failed' => 'bg-rose-100 text-rose-700',
                                default => 'bg-slate-100 text-slate-700',
                            };
                            $statusLabel = strtoupper($bill->status);
                        @endphp
                        <span class="text-xs font-bold px-2 py-1 rounded-full {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if ($bill->status === 'unpaid')
                            <div class="flex items-center justify-center gap-2">
                                <a href="https://app.pakasir.com/pay/{{ config('services.pakasir.slug', 'ryaze') }}/{{ $bill->amount }}?order_id={{ $bill->invoice_number }}" 
                                   target="_blank"
                                   class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-xs font-bold transition shadow-sm">
                                    <i class="fa-solid fa-credit-card"></i> Pakasir
                                </a>
                                <form action="{{ route('user_hosting.billing.pay_wallet') }}" method="POST" class="m-0 p-0 inline-block" onsubmit="return confirm('Bayar tagihan ini menggunakan Saldo Wallet Anda?')">
                                    @csrf
                                    <input type="hidden" name="invoice_number" value="{{ $bill->invoice_number }}">
                                    <button type="submit" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded text-xs font-bold transition shadow-sm">
                                        <i class="fa-solid fa-wallet"></i> Saldo
                                    </button>
                                </form>
                            </div>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada riwayat tagihan.</td>
                </tr>
            @endforelse
            <x-slot:pagination>
                {{ $billings->links() }}
            </x-slot:pagination>
        </x-ui.table>
    </x-ui.page-layout>
@endsection
