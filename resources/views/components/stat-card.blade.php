@props(['title', 'value', 'icon', 'color'])

@php
    // Kelas Tailwind TIDAK boleh dibangun dinamis (JIT tidak akan men-generate).
    // Selalu gunakan string lengkap dari map di bawah.
    $colorMap = [
        'emerald' => [
            'hover_border' => 'hover:border-emerald-200 dark:hover:border-emerald-500/40',
            'bg_deco' => 'bg-emerald-50 dark:bg-emerald-500/10',
            'icon_bg' => 'bg-emerald-100 dark:bg-emerald-500/20',
            'icon_text' => 'text-emerald-600 dark:text-emerald-300',
            'icon_hover_bg' => 'group-hover:bg-emerald-600',
        ],
        'rose' => [
            'hover_border' => 'hover:border-rose-200 dark:hover:border-rose-500/40',
            'bg_deco' => 'bg-rose-50 dark:bg-rose-500/10',
            'icon_bg' => 'bg-rose-100 dark:bg-rose-500/20',
            'icon_text' => 'text-rose-600 dark:text-rose-300',
            'icon_hover_bg' => 'group-hover:bg-rose-600',
        ],
        'sky' => [
            'hover_border' => 'hover:border-sky-200 dark:hover:border-sky-500/40',
            'bg_deco' => 'bg-sky-50 dark:bg-sky-500/10',
            'icon_bg' => 'bg-sky-100 dark:bg-sky-500/20',
            'icon_text' => 'text-sky-600 dark:text-sky-300',
            'icon_hover_bg' => 'group-hover:bg-sky-600',
        ],
        'indigo' => [
            'hover_border' => 'hover:border-indigo-200 dark:hover:border-indigo-500/40',
            'bg_deco' => 'bg-indigo-50 dark:bg-indigo-500/10',
            'icon_bg' => 'bg-indigo-100 dark:bg-indigo-500/20',
            'icon_text' => 'text-indigo-600 dark:text-indigo-300',
            'icon_hover_bg' => 'group-hover:bg-indigo-600',
        ],
        'amber' => [
            'hover_border' => 'hover:border-amber-200 dark:hover:border-amber-500/40',
            'bg_deco' => 'bg-amber-50 dark:bg-amber-500/10',
            'icon_bg' => 'bg-amber-100 dark:bg-amber-500/20',
            'icon_text' => 'text-amber-600 dark:text-amber-300',
            'icon_hover_bg' => 'group-hover:bg-amber-600',
        ],
        'violet' => [
            'hover_border' => 'hover:border-violet-200 dark:hover:border-violet-500/40',
            'bg_deco' => 'bg-violet-50 dark:bg-violet-500/10',
            'icon_bg' => 'bg-violet-100 dark:bg-violet-500/20',
            'icon_text' => 'text-violet-600 dark:text-violet-300',
            'icon_hover_bg' => 'group-hover:bg-violet-600',
        ],
    ];
    $palette = $colorMap[$color] ?? $colorMap['emerald'];
@endphp

<div
    class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between group transition-all duration-300 hover:shadow-md dark:bg-slate-800/60 dark:border-slate-700 {{ $palette['hover_border'] }} hover:-translate-y-1 relative overflow-hidden">
    <div
        class="absolute -right-4 -top-4 w-24 h-24 {{ $palette['bg_deco'] }} rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0">
    </div>
    <div class="relative z-10 flex justify-between items-start">
        <div>
            <p class="text-sm font-medium text-slate-500 mb-1 dark:text-slate-400">{{ $title }}</p>
            <h3 class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $value }}</h3>
        </div>
        <div
            class="w-12 h-12 flex items-center justify-center rounded-xl {{ $palette['icon_bg'] }} {{ $palette['icon_text'] }} {{ $palette['icon_hover_bg'] }} group-hover:text-white transition-colors duration-300">
            <i class="fa-solid {{ $icon }} text-xl"></i>
        </div>
    </div>
</div>