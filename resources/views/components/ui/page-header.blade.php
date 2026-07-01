@props([
    'title',
    'description' => null,
    'icon' => 'cube',
    'iconColor' => 'indigo'
])

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-5 border-b border-gray-200">
    <div class="flex items-center gap-4">
        @if(isset($iconSlot))
            {{ $iconSlot }}
        @else
            <div class="shrink-0 w-10 h-10 flex items-center justify-center bg-white border border-gray-200 text-{{ $iconColor }}-600 rounded-md shadow-sm">
                <i class="{{ $icon }} text-base"></i>
            </div>
        @endif
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $title }}</h1>
            @if(isset($subtitle))
                <div class="mt-1">{{ $subtitle }}</div>
            @elseif($description)
                <p class="text-sm text-gray-500 mt-1">{{ $description }}</p>
            @endif
        </div>
    </div>
    @if(isset($actions))
        <div class="flex gap-2">
            {{ $actions }}
        </div>
    @endif
</div>
