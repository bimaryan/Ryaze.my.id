@props([
    'updates' => [],
    'title' => 'Info Update',
    'description' => 'Pengumuman, fitur baru, dan catatan maintenance Ryaze.',
    'emptyText' => 'Belum ada info update untuk saat ini.',
])

{{--
    KARTU INFO UPDATE untuk sisi pengguna.

    Admin managing lewat InfoUpdateController (CRUD + toggle), tapi
    tidak ada endpoint untuk user — jadi data di sini dikirim dari
    controller masing-masing dashboard memakai:

        InfoUpdate::published()
            ->orderByDesc('priority')
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

    `scopePublished` di model sudah+jaring pengaman+ — info yang
    belum aktif atau belum masuk jadwal tayang tidak pernah tampil,
    jadi komponen ini tidak perlu memfilter ulang.

    Warna kategori & prioritas datang dari model (CATEGORY_META /
    PRIORITY_META) supaya halaman tidak menulis ulang palet warna.

    CATATAN: accessor di model WAJIB dipanggil dengan `()` --
    `{{ $update->categoryLabel() }}`, bukan `{{ $update->categoryLabel }}`.
    Eloquent::isRelation() memakai method_exists(), jadi akses sebagai
    property membuat Eloquent mengira itu relationship dan melempar
    "categoryLabel must return a relationship instance".

        <x-ui.info-update-card :updates="$infoUpdates" />
--}}

<x-ui.card class="p-6">
    <x-ui.section title="{{ $title }}" description="{{ $description }}"
        icon="fa-solid fa-bullhorn">
        <x-slot:actions>
            <x-ui.badge variant="neutral">{{ count($updates) }}</x-ui.badge>
        </x-slot:actions>
    </x-ui.section>

    @forelse ($updates as $update)
        <article @class([
            'group relative flex gap-3 rounded-xl border border-slate-100 p-4 transition-colors',
            'hover:border-indigo-200 hover:bg-slate-50/60',
            'dark:border-slate-700/60 dark:hover:border-indigo-500/30 dark:hover:bg-slate-800/40',
            'not-last:mb-3',
        ])>
            <span @class([
                'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl',
                'bg-slate-100 text-slate-500',
                'dark:bg-slate-700/50 dark:text-slate-400',
            ])>
                {{-- Ikon dari model belum memakai prefix; tambahkan di sini supaya
                     halaman tidak perlu mengulang `fa-solid`. --}}
                <i class="fa-solid {{ $update->categoryIcon() }}"></i>
            </span>

            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <h3 class="min-w-0 flex-1 truncate text-sm font-semibold text-slate-800 dark:text-slate-100">
                        {{ $update->title }}
                    </h3>

                    @if ($update->version)
                        <span class="ui-code shrink-0">v{{ $update->version }}</span>
                    @endif
                </div>

                @if ($update->description)
                    <p class="mt-1.5 line-clamp-2 text-sm text-slate-500 dark:text-slate-400">
                        {{ $update->description }}
                    </p>
                @endif

                <div class="mt-2.5 flex flex-wrap items-center gap-2">
                    <x-ui.badge :variant="$update->categoryBadgeVariant()">
                        {{ $update->categoryLabel() }}
                    </x-ui.badge>

                    <x-ui.badge :variant="$update->priorityBadgeVariant()">
                        {{ $update->priorityLabel() }}
                    </x-ui.badge>

                    <time class="text-xs text-slate-400 dark:text-slate-500"
                        datetime="{{ $update->published_at?->toIso8601String() }}">
                        {{ $update->published_at?->translatedFormat('d F Y') ?? 'Belum dijadwalkan' }}
                    </time>
                </div>
            </div>
        </article>
    @empty
        <x-ui.empty icon="fa-solid fa-bell-slash" title="Tidak ada info update"
            text="{{ $emptyText }}" />
    @endforelse
</x-ui.card>