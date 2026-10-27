<div class="ui-card overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60">
        <div class="flex items-start sm:items-center justify-between gap-3 flex-col sm:flex-row">
            <div>
                <h3 class="font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    @if (! empty($icon))
                        <i class="{{ $icon }} text-indigo-500 dark:text-indigo-400"></i>
                    @endif
                    {{ $title }}
                </h3>
                @if (! empty($description))
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $description }}</p>
                @endif
            </div>
            @if (isset($actions))
                <div class="shrink-0">{{ $actions }}</div>
            @endif
        </div>
    </div>
    <div class="p-6">
        {{ $slot }}
    </div>
</div>
