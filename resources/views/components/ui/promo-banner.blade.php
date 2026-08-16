@props(['class' => ''])

@php
    $activePromo = \App\Models\PromoEvent::where('is_active', true)
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->latest()
        ->first();
@endphp

@if($activePromo)
    <div class="{{ $class }}">
        <a href="{{ $activePromo->target_url ?? '#' }}" class="block relative rounded-2xl overflow-hidden shadow-sm group hover:shadow-md transition-all duration-300 hover:scale-[1.01]">
            @if($activePromo->banner_image)
                <img src="{{ $activePromo->banner_url }}" class="w-full h-auto object-cover max-h-48" alt="{{ $activePromo->title }}">
            @else
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-8 sm:py-10 text-center border border-indigo-400 dark:border-indigo-500/50">
                    <h3 class="text-xl md:text-2xl font-bold text-white tracking-tight">{{ $activePromo->title }}</h3>
                    @if($activePromo->description)
                        <p class="text-indigo-100 mt-2 text-sm max-w-2xl mx-auto">{{ $activePromo->description }}</p>
                    @endif
                </div>
            @endif
        </a>
    </div>
@else
    {{ $fallback ?? '' }}
@endif
