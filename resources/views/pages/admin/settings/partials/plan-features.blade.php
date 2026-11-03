@php
    $featuresKey = 'plan_'.$planSlug.'_features';
    $planData = \App\Models\User::hostingPlans()[$planSlug] ?? null;
    $allFeatures = $planData['features'] ?? [];

    // Dua baris pertama selalu Storage & Maks. Project (dibuat otomatis).
    $autoLines = array_slice($allFeatures, 0, 2);

    $current = collect($allFeatures)
        ->reject(fn ($feature) => \App\Models\User::isAutoManagedFeature($feature))
        ->values()
        ->all();

    $catalog = \App\Models\User::planFeatureOptions();
    $customLines = array_values(array_diff($current, $catalog));
@endphp

<div>
    <label class="block text-xs font-medium text-[#525252] dark:text-white/60 mb-1">Fitur</label>

    @if (! empty($autoLines))
        <p class="text-[10px] text-[#94A3B4] dark:text-white/35 mb-1">Diambil otomatis dari field di atas, bukan tombol:</p>
        <ul class="mb-2 space-y-0.5">
            @foreach ($autoLines as $line)
                <li class="text-[11px] leading-4 text-[#737373] dark:text-white/40 flex items-start gap-1.5">
                    <i class="fa-solid fa-wand-magic-sparkles text-[#6BA8DC] dark:text-[#1D5B96] mt-0.5"></i>
                    <span>{{ $line }}</span>
                </li>
            @endforeach
        </ul>
    @endif

    <div class="space-y-1.5 max-h-48 overflow-y-auto  border border-[#C9D5E2] dark:border-[#2A4463] bg-white dark:bg-[#0F1D30] p-3">
        <input type="hidden" name="{{ $featuresKey }}[]" value="">

        @foreach (\App\Models\User::planFeatureCatalog() as $group => $items)
            <p class="text-[10px] font-bold uppercase tracking-wider text-[#94A3B4] dark:text-white/35 pt-1 {{ $loop->first ? '' : 'border-t border-[#E3EAF1] dark:border-[#1B2C44] mt-1' }}">{{ $group }}</p>
            @foreach ($items as $item)
                <x-ui.choice name="{{ $featuresKey }}[]" :value="$item" :label="$item" :checked="in_array($item, $current, true)" />
            @endforeach
        @endforeach

        @if (! empty($customLines))
            <p class="text-[10px] font-bold uppercase tracking-wider text-[#94A3B4] dark:text-white/35 pt-1 border-t border-[#E3EAF1] dark:border-[#1B2C44] mt-1">Teks Lama</p>
            @foreach ($customLines as $item)
                <x-ui.choice name="{{ $featuresKey }}[]" :value="$item" :label="$item" :checked="true" />
            @endforeach
        @endif
    </div>
</div>
