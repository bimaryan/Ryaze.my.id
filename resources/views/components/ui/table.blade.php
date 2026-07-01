<div
    class="glass-panel rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] {{ $attributes->get('class') }}">
    @if (isset($header))
        {{ $header }}
    @endif
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-slate-600">
            <thead
                class="bg-white/40 text-xs uppercase font-semibold text-slate-500 border-b border-white/40 backdrop-blur-sm">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                {{ $slot }}
            </tbody>
        </table>
    </div>
    @if (isset($pagination))
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $pagination }}
        </div>
    @endif
</div>
