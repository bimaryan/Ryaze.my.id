@props(['title', 'value', 'icon', 'color' => 'emerald', 'subtitle' => null, 'href' => null])

@php
    $colorMap = [
        'emerald' => [
            'icon_box' => 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
            'hover_border' => 'hover:border-emerald-300 dark:hover:border-emerald-500/40',
        ],
        'rose' => [
            'icon_box' => 'bg-rose-50 text-rose-600 border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20',
            'hover_border' => 'hover:border-rose-300 dark:hover:border-rose-500/40',
        ],
        'sky' => [
            'icon_box' => 'bg-sky-50 text-sky-600 border-sky-100 dark:bg-sky-500/10 dark:text-sky-400 dark:border-sky-500/20',
            'hover_border' => 'hover:border-sky-300 dark:hover:border-sky-500/40',
        ],
        'indigo' => [
            'icon_box' => 'bg-indigo-50 text-indigo-600 border-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20',
            'hover_border' => 'hover:border-indigo-300 dark:hover:border-indigo-500/40',
        ],
        'amber' => [
            'icon_box' => 'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
            'hover_border' => 'hover:border-amber-300 dark:hover:border-amber-500/40',
        ],
        'violet' => [
            'icon_box' => 'bg-purple-50 text-purple-600 border-purple-100 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20',
            'hover_border' => 'hover:border-purple-300 dark:hover:border-purple-500/40',
        ],
        'blue' => [
            'icon_box' => 'bg-blue-50 text-blue-600 border-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20',
            'hover_border' => 'hover:border-blue-300 dark:hover:border-blue-500/40',
        ],
    ];
    $palette = $colorMap[$color] ?? $colorMap['emerald'];
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif
    class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4 transition-colors {{ $palette['hover_border'] }}">
    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 border {{ $palette['icon_box'] }}">
        <i class="fa-solid {{ $icon }} text-lg"></i>
    </div>
    <div class="min-w-0 flex-1">
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5 truncate">{{ $title }}</p>
        <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight truncate">{{ $value }}</h3>
        @if($subtitle)
            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate mt-1">{{ $subtitle }}</p>
        @endif
    </div>
</{{ $tag }}>