@props(['title', 'value', 'icon', 'color'])

<div
    class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-300">
    <div
        class="w-14 h-14 flex items-center justify-center rounded-lg bg-{{ $color }}-50 text-{{ $color }}-600">
        <i class="fa-solid {{ $icon }} text-2xl"></i>
    </div>
    <div>
        <p class="text-sm font-medium text-slate-500">{{ $title }}</p>
        <h3 class="text-2xl font-bold text-slate-800">{{ $value }}</h3>
    </div>
</div>
