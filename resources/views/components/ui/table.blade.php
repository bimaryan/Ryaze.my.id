<div class="bg-white border border-[#E3EAF1] overflow-hidden shadow-none dark:bg-[#0F1D30] dark:border-[#2A4463] {{ $attributes->get('class') }}">
    @if (isset($header))
        {{ $header }}
    @endif
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-[#525252] dark:text-white/70">
            <thead class="bg-[#DDE9F5] text-[11px] font-semibold text-[#0F1A2A] border-b border-[#C2D3E4] dark:bg-[#193050] dark:text-white/60 dark:border-[#2A4463]">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E3EAF1] dark:divide-[#2A4463]">
                {{ $slot }}
            </tbody>
        </table>
    </div>
    @if (isset($pagination))
        <div class="px-6 py-4 border-t border-[#E3EAF1] dark:border-[#2A4463]">
            {{ $pagination }}
        </div>
    @endif
</div>