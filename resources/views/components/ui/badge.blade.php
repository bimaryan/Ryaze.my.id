@php
    $variant = $variant ?? 'neutral';

    $variantClasses = [
        'primary' => 'bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300',
        'success' => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300',
        'warning' => 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300',
        'danger'  => 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300',
        'neutral' => 'bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300',
    ][$variant] ?? 'bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-semibold whitespace-nowrap ' . $variantClasses]) }}>
    @if (! empty($icon))
        <i class="{{ $icon }}"></i>
    @endif
    {{ $slot }}
</span>
