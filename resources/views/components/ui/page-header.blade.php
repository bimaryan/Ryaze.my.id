@props(['title', 'description' => null, 'icon' => 'cube', 'iconColor' => 'indigo'])

<div
    class="p-4 bg-white rounded-lg shadow-sm flex items-center justify-between border border-slate-200 mb-6">
    <div>
        <p class="text-lg font-semibold text-indigo-600">{{ $title }}</p>
        @if (isset($subtitle))
            <div class="mt-0.5">
                {{ $subtitle }}
            </div>
        @endif
    </div>

    <div class="flex items-center gap-3">
        @if (isset($actions))
            <div class="flex gap-2 [&>*]:!px-2 [&>*]:!py-1">
                {{ $actions }}
            </div>
        @endif
        @if (request()->routeIs('*.dashboard'))
            <div
                class="hidden md:flex px-2 py-1 bg-slate-100 rounded-lg border border-slate-200 text-sm font-medium text-slate-600 items-center gap-2">
                <i class="fa-regular fa-calendar text-slate-400"></i>
                {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </div>
        @endif
    </div>
</div>
