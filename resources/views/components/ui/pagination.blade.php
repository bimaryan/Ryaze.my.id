{{--
    PAGINATION — membungkus paginator Laravel dengan gaya Ryaze.

        <x-ui.pagination :links="$users->links()" />

    $paginator adalah hasil ->links() (HTML, bukan objek).
--}}

@props([
    'links' => null,
    'info' => null,
])

@if ($links)
    <div {{ $attributes->only('class')->merge(['class' => 'ui-pagination']) }}>
        @if ($info)
            <p class="ui-pagination-info">{{ $info }}</p>
        @else
            <span></span>
        @endif

        <div class="[&>nav>svg]:hidden">
            {{ $links }}
        </div>
    </div>
@endif
