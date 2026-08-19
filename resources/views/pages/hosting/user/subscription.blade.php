@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- Header --}}
        <x-ui.page-header 
            title="Langganan Paket" 
            subtitle="Pilih paket hosting terbaik untuk project Anda." 
            icon="fa-crown" 
            iconColor="indigo">
            <x-slot:actions>
                <a href="{{ route('user_hosting.dashboard') }}"
                    class="inline-flex justify-center items-center bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-200 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>


        @php
            $plans = \App\Models\User::hostingPlans();
            $colorMap = [
                'slate'  => ['bg' => 'bg-slate-600', 'text' => 'text-slate-600 dark:text-slate-300', 'btn' => 'bg-slate-600 hover:bg-slate-700 dark:hover:bg-slate-600', 'badge' => 'bg-slate-100 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700'],
                'indigo' => ['bg' => 'bg-indigo-600', 'text' => 'text-indigo-600 dark:text-indigo-400', 'btn' => 'bg-indigo-600 hover:bg-indigo-700', 'badge' => 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-500/40'],
                'violet' => ['bg' => 'bg-violet-600', 'text' => 'text-violet-600 dark:text-violet-300', 'btn' => 'bg-violet-600 hover:bg-violet-700', 'badge' => 'bg-violet-100 dark:bg-violet-500/20 text-violet-700 dark:text-violet-300 border-violet-200 dark:border-violet-500/40'],
                'amber'  => ['bg' => 'bg-amber-500',  'text' => 'text-amber-600 dark:text-amber-300',  'btn' => 'bg-amber-500 hover:bg-amber-600',   'badge' => 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-500/40'],
            ];
            $hasActive = Auth::user()->hasActiveHostingSubscription();
            $activeBilling = $hasActive ? Auth::user()->hostingBillings()->where('status', 'active')->where('next_due_date', '>', now())->latest()->first() : null;
            $currentPlan = $activeBilling->plan ?? null;
        @endphp

        @if ($hasActive)
            @php $planColor = $colorMap[$plans[$currentPlan]['color'] ?? 'indigo']['badge'] ?? 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-500/40'; @endphp
            <div class="mb-6 bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-500/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-300"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-800 dark:text-slate-100 text-sm">Langganan Aktif</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Berlaku hingga {{ $activeBilling?->next_due_date?->format('d M Y') ?? '-' }}</p>
                    </div>
                </div>
                <span class="text-xs font-bold px-3 py-1 rounded-full border {{ $planColor }}">
                    <i class="fa-solid fa-crown mr-1"></i> Paket {{ $plans[$currentPlan]['label'] ?? ucfirst($currentPlan) }}
                </span>
            </div>
        @endif

        <div class="mb-6">
            <h3 class="font-bold text-slate-800 dark:text-slate-100 mb-1 flex items-center gap-2 text-lg">
                <i class="fa-solid fa-crown text-indigo-600 dark:text-indigo-400"></i> Pilih Paket Hosting
            </h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-5">
                {{ $hasActive ? 'Upgrade paket untuk menambah limit storage dan project.' : 'Pilih paket yang sesuai kebutuhan Anda dan selesaikan pembayaran untuk mulai deploy project.' }}
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach ($plans as $slug => $plan)
                    @if (!($plan['is_active'] ?? true) && $currentPlan !== $slug)
                        @continue
                    @endif
                    @php 
                        $pricing = \App\Models\User::getPlanPricing($slug); 
                        $c = $colorMap[$plan['color']]; 
                        $isPopular = $slug === 'pro';
                        $isCurrent = $slug === $currentPlan;
                    @endphp
                    <div class="relative bg-white dark:bg-slate-800/60 rounded-2xl border {{ $isPopular ? 'border-violet-400 shadow-lg shadow-violet-100' : 'border-slate-200 dark:border-slate-700 shadow-sm' }} {{ $isCurrent ? 'opacity-80' : '' }} overflow-hidden flex flex-col">
                        @if ($isPopular)
                            <div class="absolute top-0 inset-x-0 h-1 bg-violet-500"></div>
                        @endif
                        
                        @if ($isCurrent)
                            <div class="absolute top-3 right-3"><span class="text-[10px] font-bold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/40 px-2 py-0.5 rounded-full">SAAT INI</span></div>
                        @elseif ($isPopular)
                            <div class="absolute top-3 right-3"><span class="text-[10px] font-bold bg-violet-100 dark:bg-violet-500/20 text-violet-700 dark:text-violet-300 border border-violet-200 dark:border-violet-500/40 px-2 py-0.5 rounded-full">POPULER</span></div>
                        @endif

                        <div class="p-6 flex-1">
                            <div class="w-10 h-10 {{ $c['bg'] }} rounded-xl flex items-center justify-center mb-4">
                                <i class="fa-solid fa-server text-white text-base"></i>
                            </div>
                            <h4 class="font-bold text-slate-800 dark:text-slate-100 text-lg mb-1">{{ $plan['label'] }}</h4>
                            <div class="flex flex-col mb-4">
                                @if($pricing['promo'] !== null)
                                    <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 line-through decoration-rose-500 decoration-2">Rp {{ number_format($pricing['normal'], 0, ',', '.') }}</span>
                                @endif
                                <div class="flex items-baseline gap-1">
                                    <span class="text-2xl font-bold {{ $c['text'] }}">Rp {{ number_format($pricing['active'], 0, ',', '.') }}</span>
                                    <span class="text-xs text-slate-400 dark:text-slate-500 font-normal">/bulan</span>
                                </div>
                            </div>
                            <ul class="space-y-2 mb-6">
                                @foreach ($plan['features'] as $feat)
                                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                                        <i class="fa-solid fa-check {{ $c['text'] }} text-xs w-4"></i> {{ $feat }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="px-6 pb-6">
                            @if ($isCurrent)
                                <button type="button" disabled class="w-full bg-slate-100 dark:bg-slate-700/50 text-slate-400 dark:text-slate-500 font-bold px-4 py-2.5 rounded-xl text-sm cursor-not-allowed">
                                    Paket Aktif
                                </button>
                            @else
                                <form action="{{ route('user_hosting.billing.subscribe') }}" method="POST" class="space-y-2">
                                    @csrf
                                    <input type="hidden" name="plan" value="{{ $slug }}">
                                    <input type="text" name="voucher_code" placeholder="Kode Voucher (Opsional)" class="uppercase font-mono w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                                    <button type="submit" class="w-full {{ $c['btn'] }} text-white font-bold px-4 py-2.5 rounded-xl shadow-sm transition-all text-sm">
                                        <i class="fa-solid fa-bolt mr-1"></i> {{ $hasActive ? 'Upgrade ke ' . $plan['label'] : 'Pilih ' . $plan['label'] }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        
    </x-ui.page-layout>
@endsection
