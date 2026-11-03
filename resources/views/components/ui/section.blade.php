@props(['title', 'description' => null, 'icon' => null, 'status' => null])

<div class="ui-card overflow-hidden">
    <div class="px-6 py-3.5 border-b border-[#E3EAF1] dark:border-[#2A4463] bg-[#F4F8FC] dark:bg-[#12233A]">
        <div class="flex items-start sm:items-center justify-between gap-3 flex-col sm:flex-row">
            <div class="min-w-0">
                <h3 class="font-mono text-[11px] font-semibold uppercase tracking-[0.2em] text-[#0F1A2A] dark:text-white flex items-center gap-2.5">
                    @if (! empty($icon))
                        <i class="{{ $icon }} text-[#1D5B96] dark:text-[#6BA8DC]"></i>
                    @endif
                    {{ $title }}
                </h3>
                @if (! empty($description))
                    <p class="ui-eyebrow-muted mt-1.5">{{ $description }}</p>
                @endif
            </div>
            <div class="flex items-center gap-4 shrink-0">
                @if (! empty($status))
                    <span class="ui-status"><span class="ui-status-dot"></span>{{ $status }}</span>
                @endif
                @if (isset($actions))
                    <div class="shrink-0">{{ $actions }}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="p-6">
        {{ $slot }}
    </div>
</div>
