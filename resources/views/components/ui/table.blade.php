<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden {{ $attributes->get('class') }}">
    @if(isset($header))
        {{ $header }}
    @endif
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-600 whitespace-nowrap">
            <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500 border-b border-gray-200">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                {{ $slot }}
            </tbody>
        </table>
    </div>
    @if(isset($pagination))
        <div class="px-6 py-4 border-t border-gray-200 bg-white">
            {{ $pagination }}
        </div>
    @endif
</div>
