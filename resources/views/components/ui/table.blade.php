<div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm dark:bg-slate-900 dark:border-slate-800 {{ $attributes->get('class') }}">
    @if (isset($header))
        {{ $header }}
    @endif
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-slate-600 dark:text-slate-300">
            <thead class="bg-slate-50/75 text-xs uppercase font-semibold text-slate-500 border-b border-slate-100 dark:bg-slate-800/40 dark:text-slate-400 dark:border-slate-800">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                {{ $slot }}
            </tbody>
        </table>
    </div>
    @if (isset($pagination))
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
            {{ $pagination }}
        </div>
    @endif
</div>