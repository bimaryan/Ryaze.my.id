@props(['title', 'value', 'icon', 'color' => 'emerald', 'subtitle' => null, 'href' => null])

@php
    $colorMap = [
        'emerald' => [
            'icon_box' => 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/50',
            'hover_border' => 'hover:border-emerald-400 dark:hover:border-emerald-500',
        ],
        'rose' => [
            'icon_box' => 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800/50',
            'hover_border' => 'hover:border-rose-400 dark:hover:border-rose-500',
        ],
        'sky' => [
            'icon_box' => 'bg-[#E8F0F9] text-[#1D5B96] border-[#C9D5E2] dark:bg-[#1D5B96]/20 dark:text-[#6BA8DC] dark:border-[#1D5B96]/40',
            'hover_border' => 'hover:border-[#1D5B96] dark:hover:border-[#6BA8DC]',
        ],
        'indigo' => [
            'icon_box' => 'bg-[#12233A] text-white border-[#1D5B96] dark:bg-[#1D5B96]/20 dark:text-[#6BA8DC] dark:border-[#1D5B96]/40',
            'hover_border' => 'hover:border-[#1D5B96] dark:hover:border-[#6BA8DC]',
        ],
        'amber' => [
            'icon_box' => 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/50',
            'hover_border' => 'hover:border-amber-400 dark:hover:border-amber-500',
        ],
        'violet' => [
            'icon_box' => 'bg-[#12233A] text-[#6BA8DC] border-[#1D5B96] dark:bg-[#1D5B96]/20 dark:text-[#6BA8DC] dark:border-[#1D5B96]/40',
            'hover_border' => 'hover:border-[#1D5B96] dark:hover:border-[#6BA8DC]',
        ],
        'blue' => [
            'icon_box' => 'bg-[#E8F0F9] text-[#1D5B96] border-[#C9D5E2] dark:bg-[#1D5B96]/20 dark:text-[#6BA8DC] dark:border-[#1D5B96]/40',
            'hover_border' => 'hover:border-[#1D5B96] dark:hover:border-[#6BA8DC]',
        ],
    ];
    $palette = $colorMap[$color] ?? $colorMap['emerald'];
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif
    class="ui-cell p-5 flex items-center gap-4 transition-colors hover:bg-[#FAFBFD] dark:hover:bg-[#0F1D30]">
    <div class="w-12 h-12 flex items-center justify-center shrink-0 border {{ $palette['icon_box'] }}">
        <i class="fa-solid {{ $icon }} text-lg"></i>
    </div>
    <div class="min-w-0 flex-1">
        <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-[#737373] dark:text-white/40 mb-1 truncate">{{ $title }}</p>
        <h3 class="ui-stat-value text-2xl truncate">{{ $value }}</h3>
        @if($subtitle)
            <p class="text-xs text-[#525252] dark:text-white/50 truncate mt-1">{{ $subtitle }}</p>
        @endif
    </div>
</{{ $tag }}>