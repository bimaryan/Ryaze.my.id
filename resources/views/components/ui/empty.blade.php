<div {{ $attributes->merge(['class' => 'text-center py-10']) }}>
    <div class="mx-auto w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
        <i class="{{ $icon ?? 'fa-solid fa-inbox' }} text-slate-400 dark:text-slate-500 text-lg"></i>
    </div>
    <p class="font-semibold text-slate-700 dark:text-slate-200 text-sm">{{ $title ?? 'Belum ada data' }}</p>
    @if (! empty($text))
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $text }}</p>
    @endif
    @if (isset($actions))
        <div class="mt-4">{{ $actions }}</div>
    @endif
</div>
