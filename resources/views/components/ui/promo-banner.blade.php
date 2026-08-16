@props(['class' => ''])

@php
    $activePromos = \App\Models\PromoEvent::where('is_active', true)
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->latest()
        ->get();
@endphp

@if($activePromos->count() > 0)
    <div class="{{ $class }} relative overflow-hidden rounded-2xl shadow-sm group hover:shadow-md transition-all duration-300" 
         x-data="{ activeSlide: 0, slides: {{ $activePromos->count() }}, timer: null }"
         x-init="if(slides > 1) { timer = setInterval(() => { activeSlide = activeSlide === slides - 1 ? 0 : activeSlide + 1 }, 5000) }">
        
        <div class="flex transition-transform duration-500 ease-in-out" :style="`transform: translateX(-${activeSlide * 100}%)`">
            @foreach($activePromos as $activePromo)
                <div class="w-full shrink-0">
                    <a href="{{ $activePromo->target_url ?? '#' }}" class="block relative w-full h-full hover:scale-[1.01] transition-transform">
                        @if($activePromo->banner_image)
                            <img src="{{ $activePromo->banner_url }}" class="w-full h-auto object-cover max-h-48" alt="{{ $activePromo->title }}">
                        @else
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-8 sm:py-10 text-center border border-indigo-400 dark:border-indigo-500/50 min-h-[192px] flex flex-col justify-center">
                                <h3 class="text-xl md:text-2xl font-bold text-white tracking-tight">{{ $activePromo->title }}</h3>
                                @if($activePromo->description)
                                    <p class="text-indigo-100 mt-2 text-sm max-w-2xl mx-auto">{{ $activePromo->description }}</p>
                                @endif
                            </div>
                        @endif
                    </a>
                </div>
            @endforeach
        </div>

        @if($activePromos->count() > 1)
            <!-- Dots Navigation -->
            <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-2 z-10">
                <template x-for="i in slides" :key="i">
                    <button @click="activeSlide = i - 1; clearInterval(timer); timer = setInterval(() => { activeSlide = activeSlide === slides - 1 ? 0 : activeSlide + 1 }, 5000)"
                            class="h-2 rounded-full transition-all duration-300 shadow-sm"
                            :class="activeSlide === i - 1 ? 'bg-white w-6' : 'bg-white/50 w-2 hover:bg-white/80'"
                            :aria-label="'Go to slide ' + i"></button>
                </template>
            </div>
            
            <!-- Arrows Navigation -->
            <button @click="activeSlide = activeSlide === 0 ? slides - 1 : activeSlide - 1; clearInterval(timer); timer = setInterval(() => { activeSlide = activeSlide === slides - 1 ? 0 : activeSlide + 1 }, 5000)" 
                    class="absolute left-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-black/20 hover:bg-black/40 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10 backdrop-blur-sm"
                    aria-label="Previous slide">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </button>
            <button @click="activeSlide = activeSlide === slides - 1 ? 0 : activeSlide + 1; clearInterval(timer); timer = setInterval(() => { activeSlide = activeSlide === slides - 1 ? 0 : activeSlide + 1 }, 5000)" 
                    class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-black/20 hover:bg-black/40 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10 backdrop-blur-sm"
                    aria-label="Next slide">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </button>
        @endif
    </div>
@else
    {{ $fallback ?? '' }}
@endif
