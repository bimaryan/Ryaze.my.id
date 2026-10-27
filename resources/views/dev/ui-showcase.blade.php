<x-ui.page-layout>
    <x-ui.page-header title="UI Showcase" description="Katalog komponen design-system dalam kondisi nyata (light & dark)."
        icon="fa-solid fa-swatchbook">
        <x-slot:actions>
            <x-ui.badge variant="accent">internal</x-ui.badge>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="space-y-6">

        {{-- BUTTONS --}}
        <div class="ui-card p-6 space-y-4">
            <h3 class="ui-modal-title">Button</h3>
            <div class="flex flex-wrap items-center gap-2">
                <span class="ui-btn ui-btn-md ui-btn-primary">Primary</span>
                <span class="ui-btn ui-btn-md ui-btn-secondary">Secondary</span>
                <span class="ui-btn ui-btn-md ui-btn-success">Success</span>
                <span class="ui-btn ui-btn-md ui-btn-danger">Danger</span>
                <span class="ui-btn ui-btn-md ui-btn-ghost">Ghost</span>
                <span class="ui-btn ui-btn-md ui-btn-link">Link</span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="ui-btn ui-btn-sm ui-btn-primary">Small</span>
                <span class="ui-btn ui-btn-md ui-btn-primary">Medium</span>
                <span class="ui-btn ui-btn-md ui-btn-primary ui-btn-icon"><i class="fa-solid fa-rocket"></i> Icon</span>
                <button class="ui-icon-btn ui-icon-btn-primary"><i class="fa-solid fa-pen"></i></button>
                <button class="ui-icon-btn ui-icon-btn-success"><i class="fa-solid fa-check"></i></button>
                <button class="ui-icon-btn ui-icon-btn-danger"><i class="fa-solid fa-trash-can"></i></button>
                <button class="ui-icon-btn ui-icon-btn-neutral"><i class="fa-solid fa-gear"></i></button>
            </div>
        </div>

        {{-- BADGE & PILL & CHIP --}}
        <div class="ui-card p-6 space-y-4">
            <h3 class="ui-modal-title">Badge / Pill / Chip</h3>
            <div class="flex flex-wrap gap-2 items-center">
                <x-ui.badge variant="primary">primary</x-ui.badge>
                <x-ui.badge variant="success">success</x-ui.badge>
                <x-ui.badge variant="warning">warning</x-ui.badge>
                <x-ui.badge variant="danger">danger</x-ui.badge>
                <x-ui.badge variant="neutral">neutral</x-ui.badge>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="ui-badge ui-badge-primary">ui-badge-primary</span>
                <span class="ui-badge ui-badge-success">ui-badge-success</span>
                <span class="ui-badge ui-badge-warning">ui-badge-warning</span>
                <span class="ui-badge ui-badge-danger">ui-badge-danger</span>
                <span class="ui-badge ui-badge-neutral">ui-badge-neutral</span>
                <span class="ui-badge ui-badge-info">ui-badge-info</span>
                <span class="ui-badge ui-badge-accent">ui-badge-accent</span>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="ui-pill ui-pill-active"><i class="fa-solid fa-inbox"></i> Semua</span>
                <span class="ui-pill">Arsip</span>
                <span class="ui-chip"><i class="fa-solid fa-pen"></i> Kelola</span>
                <span class="ui-code">v1.2.3</span>
            </div>
        </div>

        {{-- FORM ─────────────────────────────────────────── --}}
        <div class="ui-card p-6 space-y-4">
            <h3 class="ui-modal-title">Form</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="ui-label">Field normal</label>
                    <input type="text" class="ui-field" placeholder="Placeholder...">
                </div>
                <div>
                    <label class="ui-label">Field Small</label>
                    <input type="text" class="ui-field ui-field-sm" placeholder="Small...">
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-6">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer" checked>
                    <div class="peer ui-switch ui-switch-primary"></div>
                    <span class="ml-3 text-sm text-slate-600 dark:text-slate-300">Primary</span>
                </label>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer" checked>
                    <div class="peer ui-switch ui-switch-success"></div>
                    <span class="ml-3 text-sm text-slate-600 dark:text-slate-300">Success</span>
                </label>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer" checked>
                    <div class="peer ui-switch ui-switch-warning"></div>
                    <span class="ml-3 text-sm text-slate-600 dark:text-slate-300">Warning</span>
                </label>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer" checked>
                    <div class="peer ui-switch ui-switch-danger"></div>
                    <span class="ml-3 text-sm text-slate-600 dark:text-slate-300">Danger</span>
                </label>
            </div>
            <div class="space-y-1.5">
                <x-ui.choice name="demo_choice" value="a" label="Pilihan checkbox A" :checked="true" />
                <x-ui.choice name="demo_choice" value="b" label="Pilihan checkbox B" :checked="false" />
            </div>
        </div>

        {{-- TAB & PILL ──────────────────────────────────────--}}
        <div class="ui-card p-6 space-y-4">
            <h3 class="ui-modal-title">Tab</h3>
            <div class="flex flex-wrap gap-1">
                <span class="ui-tab ui-tab-active">Aktif</span>
                <span class="ui-tab">Satu</span>
                <span class="ui-tab">Dua</span>
                <span class="ui-pill ui-pill-active">pill aktif</span>
                <span class="ui-pill">pill</span>
            </div>
        </div>

        {{-- SECTION / CARD / EMPTY ───────────────────────── --}}
        <x-ui.section title="Section" description="Judul + deskripsi + slot actions." icon="fa-solid fa-layer-group">
            <x-slot:actions>
                <x-ui.badge variant="success">slot:actions</x-ui.badge>
            </x-slot:actions>
            <p class="text-sm text-slate-600 dark:text-slate-300">Isi section. Komponen ini dipakai halaman Info Update.</p>
        </x-ui.section>

        <div class="ui-card p-6">
            <x-ui.empty icon="fa-solid fa-inbox" title="Belum ada data" text="Contoh komponen empty state." />
        </div>

        {{-- SIDEBAR & IDE (part 2) ───────────────────────── --}}
        <div class="ui-card p-6 space-y-3">
            <h3 class="ui-modal-title">Sidebar &amp; IDE</h3>
            <div class="flex flex-wrap gap-2">
                <span class="ui-ide-activity ui-ide-activity-active"><i class="fa-solid fa-folder"></i></span>
                <span class="ui-ide-activity"><i class="fa-solid fa-magnifying-glass"></i></span>
                <span class="ui-ide-tab ui-ide-tab-active">Terminal</span>
                <span class="ui-ide-tab">Problems</span>
                <span class="ui-sidebar-label">label</span>
                <span class="ui-sidebar-link"><i class="fa-solid fa-gauge"></i> Link</span>
            </div>
        </div>

        {{-- TABLE & PAGINATION ──────────────────────────── --}}
        <div class="ui-card overflow-hidden">
            <x-ui.table>
                <tr>
                    <th class="px-6 py-3 text-left">Kolom</th>
                    <th class="px-6 py-3 text-right">Status</th>
                </tr>
                <tr>
                    <td class="px-6 py-3">Contoh baris</td>
                    <td class="px-6 py-3 text-right"><x-ui.badge variant="success">ok</x-ui.badge></td>
                </tr>
            </x-ui.table>
            <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                <p class="ui-pagination-info">Menampilkan 1 dari 1 data</p>
            </div>
        </div>

    </x-ui.page-layout>
