@props(['title', 'value', 'icon', 'color'])

<div
    class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between group transition-all duration-300 hover:shadow-md hover:border-{{ $color }}-200 hover:-translate-y-1 relative overflow-hidden">
    <div
        class="absolute -right-4 -top-4 w-24 h-24 bg-{{ $color }}-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0">
    </div>
    <div class="relative z-10 flex justify-between items-start">
        <div>
            <p class="text-sm font-medium text-slate-500 mb-1">{{ $title }}</p>
            <h3 class="text-3xl font-bold text-slate-800">{{ $value }}</h3>
        </div>
        <div
            class="w-12 h-12 flex items-center justify-center rounded-xl bg-{{ $color }}-100 text-{{ $color }}-600 group-hover:bg-{{ $color }}-600 group-hover:text-white transition-colors duration-300">
            <i class="fa-solid {{ $icon }} text-xl"></i>
        </div>
    </div>
</div>
