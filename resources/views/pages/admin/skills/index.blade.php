@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Skill & Tech Stack" 
            subtitle="Kelola grup skill, skill individual, dan tech badge untuk portfolio." 
            icon="fa-solid fa-code">
        </x-ui.page-header>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            {{-- ═══ Skill Groups ═══ --}}
            <x-ui.card>
                <div class="p-5 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">
                        <i class="fa-solid fa-layer-group text-indigo-500 mr-2"></i>Grup Skill
                    </h3>
                </div>
                <div class="p-5">
                    {{-- Add Group Form --}}
                    <form action="{{ route('superadmin.skills.group.store') }}" method="POST" class="mb-6 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        @csrf
                        <div class="mb-3">
                            <input type="text" name="label" placeholder="Nama Grup (Backend)" required
                                class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="relative">
                                <x-ui.icon-picker name="icon" value="fa-layer-group" label="Icon" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Warna</label>
                                <input type="color" name="color" value="#6366f1" class="w-full h-10 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1 cursor-pointer">
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg py-2 transition-colors">
                            <i class="fa-solid fa-plus mr-1"></i> Tambah Grup
                        </button>
                    </form>

                    {{-- Group List --}}
                    <div class="space-y-3">
                        @forelse($skillGroups as $group)
                            <div class="flex items-center justify-between p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl" x-data="{ edit: false }">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: {{ $group->color }}18;">
                                        <i class="fa-solid {{ $group->icon }} text-xs" style="color: {{ $group->color }};"></i>
                                    </div>
                                    <div x-show="!edit">
                                        <div class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $group->label }}</div>
                                        <div class="text-xs text-slate-500">{{ $group->skills->count() }} skill</div>
                                    </div>
                                    <form x-show="edit" action="{{ route('superadmin.skills.group.update', $group->hashid) }}" method="POST" class="flex items-center gap-2">
                                        @csrf @method('PUT')
                                        <input type="text" name="label" value="{{ $group->label }}" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1 text-sm w-32 focus:ring-2 focus:ring-indigo-500/20 outline-none">
                                        <input type="hidden" name="icon" value="{{ $group->icon }}">
                                        <input type="hidden" name="color" value="{{ $group->color }}">
                                        <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Simpan</button>
                                        <button type="button" @click="edit = false" class="text-xs text-slate-400 hover:text-slate-600">Batal</button>
                                    </form>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button @click="edit = !edit" class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg transition-colors">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>
                                    <form action="{{ route('superadmin.skills.group.destroy', $group->hashid) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this)" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg transition-colors">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-400 text-center py-4">Belum ada grup skill.</p>
                        @endforelse
                    </div>
                </div>
            </x-ui.card>

            {{-- ═══ Tech Badges ═══ --}}
            <x-ui.card>
                <div class="p-5 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">
                        <i class="fa-solid fa-tags text-violet-500 mr-2"></i>Tech Badges
                    </h3>
                </div>
                <div class="p-5">
                    {{-- Add Badge Form --}}
                    <form action="{{ route('superadmin.skills.badge.store') }}" method="POST" class="mb-6 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        @csrf
                        <div class="mb-3 relative">
                            <x-ui.icon-picker name="icon" value="fa-brands fa-laravel" label="Icon" />
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <input type="text" name="label" placeholder="Nama (Laravel)" required
                                class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                            <input type="color" name="color" value="#ef4444" class="h-10 rounded-lg cursor-pointer border border-slate-200 dark:border-slate-700">
                        </div>
                        <button type="submit" class="w-full bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg py-2 transition-colors">
                            <i class="fa-solid fa-plus mr-1"></i> Tambah Badge
                        </button>
                    </form>

                    {{-- Badge List --}}
                    <div class="flex flex-wrap gap-2">
                        @forelse($techBadges as $badge)
                            <div class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm">
                                <i class="{{ $badge->icon }}" style="color: {{ $badge->color }};"></i>
                                <span class="text-slate-700 dark:text-slate-200 font-medium">{{ $badge->label }}</span>
                                <form action="{{ route('superadmin.skills.badge.destroy', $badge->hashid) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this)" class="text-slate-400 hover:text-rose-500 ml-1">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-slate-400 text-center py-4 w-full">Belum ada tech badge.</p>
                        @endforelse
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- ═══ Skills per Group ═══ --}}
        @foreach($skillGroups as $group)
            <x-ui.card class="mt-6">
                <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: {{ $group->color }}18;">
                            <i class="fa-solid {{ $group->icon }} text-xs" style="color: {{ $group->color }};"></i>
                        </div>
                        {{ $group->label }}
                    </h3>
                </div>
                <div class="p-5">
                    {{-- Add Skill Form --}}
                    <form action="{{ route('superadmin.skills.skill.store') }}" method="POST" class="mb-4 flex items-end gap-3">
                        @csrf
                        <input type="hidden" name="skill_group_id" value="{{ $group->id }}">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Nama Skill</label>
                            <input type="text" name="name" placeholder="Contoh: Laravel / PHP" required
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                        </div>
                        <div class="w-24">
                            <label class="block text-xs font-medium text-slate-500 mb-1">%</label>
                            <input type="number" name="percentage" min="0" max="100" value="80" required
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                        </div>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg px-4 py-2 transition-colors">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </form>

                    {{-- Skill List --}}
                    <div class="space-y-2">
                        @forelse($group->skills as $skill)
                            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl" x-data="{ edit: false }">
                                <div class="flex items-center gap-3 flex-1" x-show="!edit">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $skill->name }}</div>
                                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 mt-1.5">
                                            <div class="h-1.5 rounded-full" style="width: {{ $skill->percentage }}%; background: {{ $group->color }};"></div>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold tabular-nums" style="color: {{ $group->color }};">{{ $skill->percentage }}%</span>
                                </div>
                                <form x-show="edit" action="{{ route('superadmin.skills.skill.update', $skill->hashid) }}" method="POST" class="flex items-center gap-2 flex-1">
                                    @csrf @method('PUT')
                                    <input type="text" name="name" value="{{ $skill->name }}" class="flex-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1 text-sm focus:ring-2 focus:ring-indigo-500/20 outline-none">
                                    <input type="number" name="percentage" value="{{ $skill->percentage }}" min="0" max="100" class="w-16 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1 text-sm text-center focus:ring-2 focus:ring-indigo-500/20 outline-none">
                                    <button type="submit" class="text-xs text-emerald-600 font-medium px-2">OK</button>
                                    <button type="button" @click="edit = false" class="text-xs text-slate-400 px-1">✕</button>
                                </form>
                                <div class="flex items-center gap-1 ml-3" x-show="!edit">
                                    <button @click="edit = true" class="p-1 text-slate-400 hover:text-indigo-600 rounded transition-colors">
                                        <i class="fa-solid fa-pen text-[10px]"></i>
                                    </button>
                                    <form action="{{ route('superadmin.skills.skill.destroy', $skill->hashid) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this)" class="p-1 text-slate-400 hover:text-rose-600 rounded transition-colors">
                                            <i class="fa-solid fa-trash text-[10px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-400 text-center py-3">Belum ada skill di grup ini.</p>
                        @endforelse
                    </div>
                </div>
            </x-ui.card>
        @endforeach
    </x-ui.page-layout>

    <script>
        function confirmDelete(btn) {
            Swal.fire({
                title: 'Hapus?',
                text: 'Data ini akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) btn.closest('form').submit();
            });
        }
    </script>
@endsection
