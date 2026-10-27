<label {{ $attributes->merge(['class' => 'flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300 cursor-pointer select-none']) }}>
    <input type="checkbox" name="{{ $name }}" value="{{ $value }}"
        {{ filter_var($checked ?? false, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}
        class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 dark:bg-slate-700 focus:ring-indigo-500/40 focus:ring-offset-0">
    <span>{{ $label }}</span>
</label>
