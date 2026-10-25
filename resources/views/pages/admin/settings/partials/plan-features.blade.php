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
    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Fitur</label>

    @if (! empty($autoLines))
        <p class="text-[10px] text-slate-400 dark:text-slate-500 mb-1">Diambil otomatis dari field di atas, bukan tombol:</p>
        <ul class="mb-2 space-y-0.5">
            @foreach ($autoLines as $line)
                <li class="text-[11px] leading-4 text-slate-500 dark:text-slate-400 flex items-start gap-1.5">
                    <i class="fa-solid fa-wand-magic-sparkles text-indigo-400 dark:text-indigo-500 mt-0.5"></i>
                    <span>{{ $line }}</span>
                </li>
            @endforeach
        </ul>
    @endif

    <div class="space-y-1.5 max-h-48 overflow-y-auto rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/60 p-3">
        <input type="hidden" name="{{ $featuresKey }}[]" value="">

        @foreach (\App\Models\User::planFeatureCatalog() as $group => $items)
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 pt-1 {{ $loop->first ? '' : 'border-t border-slate-100 dark:border-slate-700 mt-1' }}">{{ $group }}</p>
            @foreach ($items as $item)
                <x-ui.choice name="{{ $featuresKey }}[]" :value="$item" :label="$item" :checked="in_array($item, $current, true)" />
            @endforeach
        @endforeach

        @if (! empty($customLines))
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 pt-1 border-t border-slate-100 dark:border-slate-700 mt-1">Teks Lama</p>
            @foreach ($customLines as $item)
                <x-ui.choice name="{{ $featuresKey }}[]" :value="$item" :label="$item" :checked="true" />
            @endforeach
        @endif
    </div>
</div>
