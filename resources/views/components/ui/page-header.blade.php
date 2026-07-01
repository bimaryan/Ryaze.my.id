@props([
    'title',
    'description',
    'icon' => 'cube',
    'iconColor' => 'indigo'
])

<div class="p-5 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-4">
        <div class="shrink-0 w-11 h-11 flex items-center justify-center bg-{{ $iconColor }}-50 text-{{ $iconColor }}-600 rounded-lg">
            <i class="fa-solid fa-{{ $icon }} text-lg"></i>
        </div>
        <div>
            <h1 class="text-xl font-bold text-slate-800">{{ $title }}</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $description }}</p>
        </div>
    </div>
    @if(isset($actions))
        <div class="flex gap-2">
            {{ $actions }}
        </div>
    @endif
</div>
