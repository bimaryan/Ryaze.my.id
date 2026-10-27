@props(['title', 'description' => null, 'icon' => 'cube', 'iconColor' => 'indigo'])

<div
    class="p-4 sm:p-5 bg-white rounded-2xl shadow-sm flex flex-wrap items-center justify-between border border-slate-200/80 dark:border-slate-800 mb-6 gap-4 dark:bg-slate-900">
    <div class="flex items-center gap-4">
        @if (isset($iconSlot))
            {{ $iconSlot }}
        @endif
        <div>
            <p class="text-lg font-semibold">{{ $title }}</p>
            @if (isset($subtitle))
                <div class="mt-0.5">
                    {{ $subtitle }}
                </div>
            @endif
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <p class="text-sm">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
    </div>
</div>
