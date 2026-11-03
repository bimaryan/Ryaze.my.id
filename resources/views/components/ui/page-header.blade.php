@props(['title', 'description' => null, 'icon' => 'cube', 'iconColor' => 'indigo', 'eyebrow' => null, 'status' => null])

<div
    class="p-4 sm:px-5 py-4 bg-white dark:bg-[#0F1D30] border border-[#E3EAF1] dark:border-[#2A4463] flex flex-wrap items-center justify-between mb-6 gap-4">
    <div class="flex items-center gap-4 min-w-0">
        @if (isset($iconSlot))
            {{ $iconSlot }}
        @endif
        <div class="min-w-0">
            @if (! empty($eyebrow))
                <p class="ui-eyebrow mb-1.5">{{ $eyebrow }}</p>
            @endif
            <p class="ui-display text-lg">{{ $title }}</p>
            @if (isset($subtitle))
                <div class="mt-0.5 text-xs font-mono text-[#525252] dark:text-white/50">
                    {{ $subtitle }}
                </div>
            @endif
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-4">
        @if (isset($actions))
            <div class="flex flex-wrap items-center gap-3 order-last sm:order-none">{{ $actions }}</div>
        @endif

        @if (! empty($status))
            <span class="ui-status"><span class="ui-status-dot"></span>{{ $status }}</span>
        @endif

        <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-[#8A9BAE] dark:text-white/40">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
    </div>
</div>
