@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- Header --}}
        <x-ui.page-header 
            title="Langganan Paket" 
            eyebrow="Paket & Langganan"
            status="AKTIF"
            subtitle="Pilih paket hosting terbaik untuk project Anda." 
            icon="fa-crown" 
            iconColor="indigo">
            <x-slot:actions>
                <a href="{{ route('user_hosting.dashboard') }}"
                    class="ui-btn ui-btn-md ui-btn-icon ui-btn-secondary" title="Kembali" aria-label="Kembali">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </x-slot:actions>
        </x-ui.page-header>


        @php
            $plans = \App\Models\User::hostingPlans();
            $colorMap = [
                'slate'  => ['bg' => 'bg-[#525252]', 'text' => 'text-[#525252] dark:text-white/70', 'btn' => 'bg-[#525252] hover:bg-[#3F3F3F] dark:hover:bg-[#6B7280]', 'badge' => 'bg-[#F0F6FC] dark:bg-[#0F1D30] text-[#0F1A2A] dark:text-white/85 border-[#E3EAF1] dark:border-[#2A4463]'],
                'indigo' => ['bg' => 'bg-[#1D5B96]', 'text' => 'text-[#1D5B96] dark:text-[#6BA8DC]', 'btn' => 'bg-[#1D5B96] hover:bg-[#164A7B] dark:hover:bg-[#2E7BC2]', 'badge' => 'bg-[#E8F0F9] dark:bg-[#1D5B96]/20 text-[#1D5B96] dark:text-[#6BA8DC] border-[#C9D5E2] dark:border-[#1D5B96]/40'],
                'violet' => ['bg' => 'bg-[#1D5B96]', 'text' => 'text-[#1D5B96] dark:text-[#7FB4E2]', 'btn' => 'bg-[#1D5B96] hover:bg-[#164A7B] dark:hover:bg-[#2E7BC2]', 'badge' => 'bg-[#E8F0F9] dark:bg-[#1D5B96]/20 text-[#1D5B96] dark:text-[#7FB4E2] border-[#C9D5E2] dark:border-[#1D5B96]/40'],
                'amber'  => ['bg' => 'bg-amber-500',  'text' => 'text-amber-600 dark:text-amber-300',  'btn' => 'bg-amber-500 hover:bg-amber-600',   'badge' => 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-500/40'],
            ];
            $hasActive = Auth::user()->hasActiveHostingSubscription();
            $activeBilling = $hasActive ? Auth::user()->hostingBillings()->where('status', 'active')->where('next_due_date', '>', now())->latest()->first() : null;
            $currentPlan = $activeBilling->plan ?? null;
        @endphp

        @if ($hasActive)
            @php $planColor = $colorMap[$plans[$currentPlan]['color'] ?? 'indigo']['badge'] ?? 'bg-[#E8F0F9] dark:bg-[#1D5B96]/20 text-[#1D5B96] dark:text-[#6BA8DC] border-[#C9D5E2] dark:border-[#1D5B96]/40'; @endphp
            <div class="ui-card mb-6 p-5 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="ui-icon-box w-10 h-10 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800/50">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div>
                        <p class="ui-eyebrow-muted mb-0.5">Langganan aktif</p>
                        <p class="text-sm font-bold text-[#0F1A2A] dark:text-white">Berlaku hingga {{ $activeBilling?->next_due_date?->format('d M Y') ?? '-' }}</p>
                    </div>
                </div>
                <span class="ui-badge {{ $planColor }}">
                    <i class="fa-solid fa-crown"></i> Paket {{ $plans[$currentPlan]['label'] ?? ucfirst($currentPlan) }}
                </span>
            </div>
        @endif

        <div class="mb-6" x-data="{ billingCycle: '{{ request('cycle') === 'yearly' ? 'yearly' : 'monthly' }}' }">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <p class="ui-eyebrow mb-1.5">Paket Hosting</p>
                    <h3 class="ui-display text-lg mb-1">Pilih Paket Hosting</h3>
                    <p class="text-sm text-[#525252] dark:text-[#8A9BAE]">
                        {{ $hasActive ? 'Upgrade paket untuk menambah limit storage dan project.' : 'Pilih paket yang sesuai kebutuhan Anda dan selesaikan pembayaran untuk mulai deploy project.' }}
                    </p>
                </div>
                
                <div class="flex items-center bg-[#F0F6FC] dark:bg-[#0F1D30] p-1 border border-[#E3EAF1] dark:border-[#2A4463]">
                    <button @click="billingCycle = 'monthly'" :class="{'bg-white dark:bg-[#0F1D30] shadow-none text-[#0F1A2A] dark:text-white': billingCycle === 'monthly', 'text-[#525252] dark:text-[#8A9BAE] hover:text-[#0F1A2A] dark:hover:text-[#C9D5E2]': billingCycle !== 'monthly'}" class="px-4 py-1.5 font-mono text-[11px] uppercase tracking-[0.2em] font-semibold transition-all">Bulanan</button>
                    <button @click="billingCycle = 'yearly'" :class="{'bg-white dark:bg-[#0F1D30] shadow-none text-[#0F1A2A] dark:text-white': billingCycle === 'yearly', 'text-[#525252] dark:text-[#8A9BAE] hover:text-[#0F1A2A] dark:hover:text-[#C9D5E2]': billingCycle !== 'yearly'}" class="px-4 py-1.5 font-mono text-[11px] uppercase tracking-[0.2em] font-semibold transition-all">Tahunan <span class="ui-badge ui-badge-success ml-1 text-[10px] px-1.5">HEMAT</span></button>
                </div>
            </div>

            <div class="ui-grid grid-cols-1 md:grid-cols-3">
                @foreach ($plans as $slug => $plan)
                    @if (!($plan['is_active'] ?? true))
                        @continue
                    @endif
                    @php 
                        $pricingM = \App\Models\User::getPlanPricing($slug, 'monthly'); 
                        $pricingY = \App\Models\User::getPlanPricing($slug, 'yearly'); 
                        $c = $colorMap[$plan['color']]; 
                        $isPopular = $slug === 'pro';
                        $isCurrent = $slug === $currentPlan;
                    @endphp
                    <div class="ui-cell relative {{ $isCurrent ? 'opacity-80' : '' }} overflow-hidden flex flex-col">
                        @if ($isPopular)
                            <div class="absolute top-0 inset-x-0 h-1 bg-[#1D5B96]"></div>
                        @endif
                        
                        @if ($isCurrent)
                            <div class="absolute top-3 right-3"><span class="ui-badge ui-badge-success text-[10px] border">SAAT INI</span></div>
                        @elseif ($isPopular)
                            <div class="absolute top-3 right-3"><span class="ui-badge ui-badge-accent text-[10px] border">POPULER</span></div>
                        @endif

                        <div class="p-6 flex-1 min-w-0">
                            <div class="w-10 h-10 {{ $c['bg'] }} flex items-center justify-center mb-4">
                                <i class="fa-solid fa-server text-white text-base"></i>
                            </div>
                            <p class="ui-eyebrow text-[11px] mb-1">{{ $plan['label'] }}</p>
                            
                            <div class="flex flex-col mb-4">
                                <template x-if="billingCycle === 'monthly'">
                                    <div>
                                        @if($pricingM['promo'] !== null)
                                            <span class="text-xs font-semibold text-[#8A9BAE] dark:text-white/45 line-through decoration-rose-500 decoration-2">Rp {{ number_format($pricingM['normal'], 0, ',', '.') }}</span>
                                        @endif
                                        <div class="flex items-baseline gap-1">
                                            <span class="ui-stat-value text-3xl {{ $c['text'] }} tabular-nums"> {{ number_format($pricingM['active'], 0, ',', '.') }}</span>
                                            <span class="text-xs text-[#8A9BAE] dark:text-white/45 font-normal">/bulan</span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="billingCycle === 'yearly'">
                                    <div>
                                        @if($pricingY['promo'] !== null)
                                            <span class="text-xs font-semibold text-[#8A9BAE] dark:text-white/45 line-through decoration-rose-500 decoration-2">Rp {{ number_format($pricingY['normal'], 0, ',', '.') }}</span>
                                        @endif
                                        <div class="flex items-baseline gap-1">
                                            <span class="ui-stat-value text-3xl {{ $c['text'] }} tabular-nums"> {{ number_format($pricingY['active'], 0, ',', '.') }}</span>
                                            <span class="text-xs text-[#8A9BAE] dark:text-white/45 font-normal">/tahun</span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <ul class="space-y-3 mt-5 pt-5 mb-6 border-t border-[#EDF2F7] dark:border-[#24384F]">
                                @foreach ($plan['features'] as $feat)
                                    <li class="ui-check">
                                        <span class="ui-check-mark">&#10003;</span> {{ $feat }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="px-6 pb-6">
                            @if ($isCurrent)
                                <button type="button" disabled class="ui-btn ui-btn-secondary w-full">
                                    Paket Aktif
                                </button>
                            @else
                                <form action="{{ route('user_hosting.billing.subscribe') }}" method="POST" class="space-y-2">
                                    @csrf
                                    <input type="hidden" name="plan" value="{{ $slug }}">
                                    <input type="hidden" name="billing_cycle" x-model="billingCycle">
                                    <input type="text" name="voucher_code" placeholder="Kode Voucher (Opsional)" class="ui-field ui-field-sm uppercase font-mono">
                                    <button type="submit" class="ui-btn w-full {{ $c['btn'] }} text-white">
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
