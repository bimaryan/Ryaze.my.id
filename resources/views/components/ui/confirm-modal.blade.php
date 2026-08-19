@props([
    'model'       => 'showConfirm',   // Alpine x-data variable name (string, not used directly — caller binds via x-show)
    'title'       => 'Konfirmasi',
    'description' => 'Apakah Anda yakin?',
    'icon'        => 'fa-solid fa-circle-exclamation',
    'iconColor'   => 'text-red-500',
    'iconBg'      => 'bg-red-50 dark:bg-red-900/30',
    'cancelLabel' => 'Batal',
    'confirmLabel'=> 'Ya, Lanjutkan',
    'confirmClass'=> 'bg-red-500 hover:bg-red-600 text-white',
    'onCancel'    => 'showConfirm = false',
    'onConfirm'   => '',
])

<div
    x-show="{{ $model }}"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display:none;">

    {{-- Overlay --}}
    <div
        class="absolute inset-0 bg-black/50"
        @click="{{ $onCancel }}">
    </div>

    {{-- Modal Card --}}
    <div
        x-show="{{ $model }}"
        class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl p-6 w-full max-w-sm border border-slate-200 dark:border-slate-700"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90">

        {{-- Icon --}}
        <div class="flex items-center justify-center w-14 h-14 rounded-2xl {{ $iconBg }} mx-auto mb-4">
            <i class="{{ $icon }} {{ $iconColor }} text-[22px]"></i>
        </div>

        {{-- Text --}}
        <h3 class="text-[17px] font-semibold text-slate-900 dark:text-white text-center mb-1">
            {{ $title }}
        </h3>
        <p class="text-[14px] text-slate-500 dark:text-slate-400 text-center mb-6">
            {{ $description }}
        </p>

        {{-- Custom slot (optional) --}}
        @if ($slot->isNotEmpty())
            <div class="mb-4">{{ $slot }}</div>
        @endif

        {{-- Buttons --}}
        <div class="flex gap-3">
            <button
                type="button"
                @click="{{ $onCancel }}"
                class="flex-1 h-11 rounded-xl border border-slate-200 dark:border-slate-700 text-[14px] font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                {{ $cancelLabel }}
            </button>
            <button
                type="button"
                @click="{{ $onConfirm }}"
                class="flex-1 h-11 rounded-xl text-[14px] font-medium transition-colors {{ $confirmClass }}">
                {{ $confirmLabel }}
            </button>
        </div>
    </div>
</div>
