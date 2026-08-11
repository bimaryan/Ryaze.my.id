<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm dark:bg-slate-800/60 dark:border-slate-700 {{ $attributes->get('class') }}">
    @if (isset($header))
        {{ $header }}
    @endif
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-slate-600 dark:text-slate-300">
            <thead class="bg-slate-50/80 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200 dark:bg-slate-800/80 dark:text-slate-400 dark:border-slate-700">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                {{ $slot }}
            </tbody>
        </table>
    </div>
    @if (isset($pagination))
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
            {{ $pagination }}
        </div>
    @endif
</div>