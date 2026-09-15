@props([
    'audience' => 'all',
    'class' => '',
])

@php
    $userRole = Auth::check() ? Auth::user()->role : null;
    $audienceFilter = $audience === 'all' ? ($userRole === 'user_joki' ? 'joki' : 'hosting') : $audience;

    $announcements = \App\Models\Announcement::visible()
        ->forAudience($audienceFilter)
        ->orderByDesc('is_pinned')
        ->latest()
        ->get();
@endphp

@if($announcements->count())
    <div {{ $attributes->merge(['class' => 'space-y-3 ' . $class]) }}>
        @foreach($announcements as $item)
            @php
                $typeStyles = [
                    'info' => [
                        'bg'   => 'bg-blue-50 dark:bg-blue-500/10',
                        'border' => 'border-blue-200 dark:border-blue-500/30',
                        'icon' => 'fa-circle-info',
                        'iconColor' => 'text-blue-500 dark:text-blue-400',
                        'title' => 'text-blue-800 dark:text-blue-200',
                        'text' => 'text-blue-700 dark:text-blue-300',
                        'close' => 'text-blue-400 hover:text-blue-600 dark:hover:text-blue-300',
                    ],
                    'update' => [
                        'bg'   => 'bg-emerald-50 dark:bg-emerald-500/10',
                        'border' => 'border-emerald-200 dark:border-emerald-500/30',
                        'icon' => 'fa-arrow-up',
                        'iconColor' => 'text-emerald-500 dark:text-emerald-400',
                        'title' => 'text-emerald-800 dark:text-emerald-200',
                        'text' => 'text-emerald-700 dark:text-emerald-300',
                        'close' => 'text-emerald-400 hover:text-emerald-600 dark:hover:text-emerald-300',
                    ],
                    'maintenance' => [
                        'bg'   => 'bg-amber-50 dark:bg-amber-500/10',
                        'border' => 'border-amber-200 dark:border-amber-500/30',
                        'icon' => 'fa-person-digging',
                        'iconColor' => 'text-amber-500 dark:text-amber-400',
                        'title' => 'text-amber-800 dark:text-amber-200',
                        'text' => 'text-amber-700 dark:text-amber-300',
                        'close' => 'text-amber-400 hover:text-amber-600 dark:hover:text-amber-300',
                    ],
                    'warning' => [
                        'bg'   => 'bg-rose-50 dark:bg-rose-500/10',
                        'border' => 'border-rose-200 dark:border-rose-500/30',
                        'icon' => 'fa-triangle-exclamation',
                        'iconColor' => 'text-rose-500 dark:text-rose-400',
                        'title' => 'text-rose-800 dark:text-rose-200',
                        'text' => 'text-rose-700 dark:text-rose-300',
                        'close' => 'text-rose-400 hover:text-rose-600 dark:hover:text-rose-300',
                    ],
                ];
                $s = $typeStyles[$item->type] ?? $typeStyles['info'];
            @endphp
            <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-2" class="rounded-xl border {{ $s['border'] }} {{ $s['bg'] }} p-4 flex items-start gap-3">
                <div class="flex-shrink-0 mt-0.5">
                    <i class="fa-solid {{ $s['icon'] }} {{ $s['iconColor'] }}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-sm {{ $s['title'] }}">
                        @if($item->is_pinned)
                            <i class="fa-solid fa-thumbtack text-amber-500 dark:text-amber-400 mr-1 text-[10px]"></i>
                        @endif
                        {{ $item->title }}
                    </h4>
                    @if($item->content)
                        <p class="text-xs {{ $s['text'] }} mt-1 leading-relaxed">{{ $item->content }}</p>
                    @endif
                    @if($item->expires_at)
                        <p class="text-[10px] {{ $s['text'] }} opacity-70 mt-2">
                            <i class="fa-regular fa-clock mr-1"></i>Berlaku hingga {{ $item->expires_at->translatedFormat('d M Y, H:i') }}
                        </p>
                    @endif
                </div>
                <button @click="show = false" class="flex-shrink-0 {{ $s['close'] }} transition-colors p-1">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        @endforeach
    </div>
@endif
