@props([
    'name' => 'icon',
    'value' => 'fa-graduation-cap',
    'label' => 'Icon',
])

@php
    $icons = [
        // Education
        'fa-graduation-cap', 'fa-school', 'fa-book-open', 'fa-book', 'fa-user-graduate', 'fa-chalkboard-user', 'fa-diploma', 'fa-library',
        // Work
        'fa-briefcase', 'fa-building', 'fa-building-columns', 'fa-laptop-code', 'fa-code', 'fa-rocket', 'fa-briefcase-medical',
        // Tech
        'fa-server', 'fa-display', 'fa-gears', 'fa-database', 'fa-cloud', 'fa-code-branch', 'fa-terminal', 'fa-hammer', 'fa-wrench', 'fa-microchip', 'fa-sitemap',
        // Communication
        'fa-envelope', 'fa-phone', 'fa-comment-dots', 'fa-comments', 'fa-headset',
        // Social
        'fa-globe', 'fa-link', 'fa-share-nodes', 'fa-qrcode',
        // Files
        'fa-file', 'fa-file-code', 'fa-file-lines', 'fa-folder-open', 'fa-download', 'fa-upload',
        // Charts
        'fa-chart-line', 'fa-chart-pie', 'fa-chart-bar', 'fa-graph-simple',
        // Shapes
        'fa-star', 'fa-heart', 'fa-fire', 'fa-bolt', 'fa-atom', 'fa-puzzle-piece', 'fa-cube', 'fa-cubes', 'fa-layer-group',
        // Misc
        'fa-check-double', 'fa-shield-halved', 'fa-award', 'fa-trophy', 'fa-medal', 'fa-ribbon', 'fa-lightbulb', 'fa-compass', 'fa-rocket', 'fa-satellite',
        'fa-cart-shopping', 'fa-bag-shopping', 'fa-store', 'fa-credit-card', 'fa-wallet',
        'fa-users', 'fa-user-group', 'fa-user-plus', 'fa-user-check',
        'fa-clock', 'fa-calendar', 'fa-calendar-check', 'fa-stopwatch',
        'fa-location-dot', 'fa-map', 'fa-map-pin', 'fa-directions',
        'fa-image', 'fa-camera', 'fa-video', 'fa-music', 'fa-palette',
        'fa-hammer', 'fa-screwdriver-wrench', 'fa-gears', 'fa-wand-magic-sparkles',
        'fa-dragon', 'fa-ghost', 'fa-hat-wizard', 'fa-mask',
    ];
    $uniqueIcons = array_values(array_unique($icons));
@endphp

<div x-data="{ selected: '{{ $value }}', open: false, search: '' }">
    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">{{ $label }}</label>
    <input type="hidden" name="{{ $name }}" :value="selected">

    <button type="button" @click="open = !open"
        class="w-full flex items-center gap-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
        <i :class="selected" class="text-indigo-600 dark:text-indigo-400 text-base w-5 text-center"></i>
        <span class="flex-1 text-left text-slate-600 dark:text-slate-300" x-text="selected"></span>
        <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform" :class="open && 'rotate-180'"></i>
    </button>

    <div x-show="open" x-cloak x-transition.origin.top
        class="absolute z-50 mt-2 w-full max-h-72 overflow-hidden bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl flex flex-col">
        
        <div class="p-2 border-b border-slate-100 dark:border-slate-700">
            <input type="text" x-model="search" placeholder="Cari icon..."
                class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-indigo-500/20 outline-none">
        </div>

        <div class="overflow-y-auto max-h-56 p-2 grid grid-cols-8 gap-1">
            @foreach($uniqueIcons as $icon)
                <button type="button"
                    @click="selected = '{{ $icon }}'; open = false"
                    :class="selected === '{{ $icon }}' ? 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 ring-2 ring-indigo-500/30' : 'hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400'"
                    class="flex items-center justify-center p-2 rounded-lg transition-all"
                    x-show="search === '' || '{{ $icon }}'.includes(search.toLowerCase().replace('fa-', ''))">
                    <i class="fa-solid {{ $icon }} text-sm"></i>
                </button>
            @endforeach
        </div>
    </div>

    <div @click="open = false" x-show="open" x-cloak class="fixed inset-0 z-40"></div>
</div>
