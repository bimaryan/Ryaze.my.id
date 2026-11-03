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
                <div class="p-5 border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                    <h3 class="text-lg font-bold text-[#0F1A2A] dark:text-white">
                        <i class="fa-solid fa-layer-group text-[#1D5B96] mr-2"></i>Grup Skill
                    </h3>
                </div>
                <div class="p-5">
                    {{-- Add Group Form --}}
                    <form action="{{ route('superadmin.skills.group.store') }}" method="POST" class="mb-6 p-4 bg-[#F4F8FC] dark:bg-[#0D1828] ">
                        @csrf
                        <div class="mb-3">
                            <input type="text" name="label" placeholder="Nama Grup (Backend)" required
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44]  px-3 py-2 text-sm focus:ring-2 focus:border-[#1D5B96] outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="relative">
                                <x-ui.icon-picker name="icon" value="fa-layer-group" label="Icon" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Warna</label>
                                <input type="color" name="color" value="#1D5B96" class="w-full h-10 bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44]  px-2 py-1 cursor-pointer">
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-[#1D5B96] hover:bg-[#2A6FAD] text-white text-sm font-medium  py-2 transition-colors">
                            <i class="fa-solid fa-plus mr-1"></i> Tambah Grup
                        </button>
                    </form>

                    {{-- Group List --}}
                    <div class="space-y-3">
                        @forelse($skillGroups as $group)
                            <div class="flex items-center justify-between p-3 bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463] " x-data="{ edit: false }">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8  flex items-center justify-center" style="background: {{ $group->color }}18;">
                                        <i class="fa-solid {{ $group->icon }} text-xs" style="color: {{ $group->color }};"></i>
                                    </div>
                                    <div x-show="!edit">
                                        <div class="text-sm font-semibold text-[#0F1A2A] dark:text-white">{{ $group->label }}</div>
                                        <div class="text-xs text-[#737373]">{{ $group->skills->count() }} skill</div>
                                    </div>
                                    <form x-show="edit" action="{{ route('superadmin.skills.group.update', $group->hashid) }}" method="POST" class="flex items-center gap-2">
                                        @csrf @method('PUT')
                                        <input type="text" name="label" value="{{ $group->label }}" class="bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44]  px-2 py-1 text-sm w-32 focus:border-[#1D5B96]/20 outline-none">
                                        <input type="hidden" name="icon" value="{{ $group->icon }}">
                                        <input type="hidden" name="color" value="{{ $group->color }}">
                                        <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Simpan</button>
                                        <button type="button" @click="edit = false" class="text-xs text-[#94A3B4] hover:text-[#525252]">Batal</button>
                                    </form>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button @click="edit = !edit" class="ui-icon-btn ui-icon-btn-primary">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>
                                    <form action="{{ route('superadmin.skills.group.destroy', $group->hashid) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this)" class="ui-icon-btn ui-icon-btn-danger">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-[#94A3B4] text-center py-4">Belum ada grup skill.</p>
                        @endforelse
                    </div>
                </div>
            </x-ui.card>

            {{-- ═══ Tech Badges ═══ --}}
            <x-ui.card>
                <div class="p-5 border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                    <h3 class="text-lg font-bold text-[#0F1A2A] dark:text-white">
                        <i class="fa-solid fa-tags text-[#1D5B96] mr-2"></i>Tech Badges
                    </h3>
                </div>
                <div class="p-5">
                    {{-- Add Badge Form --}}
                    <form action="{{ route('superadmin.skills.badge.store') }}" method="POST" class="mb-6 p-4 bg-[#F4F8FC] dark:bg-[#0D1828] ">
                        @csrf
                        <div class="mb-3 relative">
                            <x-ui.icon-picker name="icon" value="fa-brands fa-laravel" label="Icon" />
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <input type="text" name="label" placeholder="Nama (Laravel)" required
                                class="bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44]  px-3 py-2 text-sm focus:ring-2 focus:border-[#1D5B96] outline-none">
                            <input type="color" name="color" value="#ef4444" class="h-10  cursor-pointer border border-[#C9D5E2] dark:border-[#1B2C44]">
                        </div>
                        <button type="submit" class="w-full bg-[#1D5B96] hover:bg-[#2A6FAD] text-white text-sm font-medium  py-2 transition-colors">
                            <i class="fa-solid fa-plus mr-1"></i> Tambah Badge
                        </button>
                    </form>

                    {{-- Badge List --}}
                    <div class="flex flex-wrap gap-2">
                        @forelse($techBadges as $badge)
                            <div class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463]  text-sm">
                                <i class="{{ $badge->icon }}" style="color: {{ $badge->color }};"></i>
                                <span class="text-[#0F1A2A] dark:text-white font-medium">{{ $badge->label }}</span>
                                <form action="{{ route('superadmin.skills.badge.destroy', $badge->hashid) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this)" class="ui-icon-btn ui-icon-btn-danger">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-[#94A3B4] text-center py-4 w-full">Belum ada tech badge.</p>
                        @endforelse
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- ═══ Skills per Group ═══ --}}
        @foreach($skillGroups as $group)
            <x-ui.card class="mt-6">
                <div class="p-5 border-b border-[#E3EAF1] dark:border-[#1B2C44] flex items-center justify-between">
                    <h3 class="text-lg font-bold text-[#0F1A2A] dark:text-white flex items-center gap-2">
                        <div class="w-8 h-8  flex items-center justify-center" style="background: {{ $group->color }}18;">
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
                            <label class="block text-xs font-medium text-[#737373] mb-1">Nama Skill</label>
                            <input type="text" name="name" placeholder="Contoh: Laravel / PHP" required
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44]  px-3 py-2 text-sm focus:ring-2 focus:border-[#1D5B96] outline-none">
                        </div>
                        <div class="w-24">
                            <label class="block text-xs font-medium text-[#737373] mb-1">%</label>
                            <input type="number" name="percentage" min="0" max="100" value="80" required
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44]  px-3 py-2 text-sm focus:ring-2 focus:border-[#1D5B96] outline-none">
                        </div>
                        <button type="submit" class="ui-icon-btn ui-icon-btn-success">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </form>

                    {{-- Skill List --}}
                    <div class="space-y-2">
                        @forelse($group->skills as $skill)
                            <div class="flex items-center justify-between p-3 bg-[#F4F8FC] dark:bg-[#0D1828] " x-data="{ edit: false }">
                                <div class="flex items-center gap-3 flex-1" x-show="!edit">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-[#0F1A2A] dark:text-white">{{ $skill->name }}</div>
                                        <div class="w-full bg-[#E3EAF1] dark:bg-[#1B2C44] rounded-full h-1.5 mt-1.5">
                                            <div class="h-1.5 rounded-full" style="width: {{ $skill->percentage }}%; background: {{ $group->color }};"></div>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold tabular-nums" style="color: {{ $group->color }};">{{ $skill->percentage }}%</span>
                                </div>
                                <form x-show="edit" action="{{ route('superadmin.skills.skill.update', $skill->hashid) }}" method="POST" class="flex items-center gap-2 flex-1">
                                    @csrf @method('PUT')
                                    <input type="text" name="name" value="{{ $skill->name }}" class="flex-1 bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44]  px-2 py-1 text-sm focus:border-[#1D5B96]/20 outline-none">
                                    <input type="number" name="percentage" value="{{ $skill->percentage }}" min="0" max="100" class="w-16 bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44]  px-2 py-1 text-sm text-center focus:border-[#1D5B96]/20 outline-none">
                                    <button type="submit" class="text-xs text-emerald-600 font-medium px-2">OK</button>
                                    <button type="button" @click="edit = false" class="text-xs text-[#94A3B4] px-1">✕</button>
                                </form>
                                <div class="flex items-center gap-1 ml-3" x-show="!edit">
                                    <button @click="edit = true" class="ui-icon-btn ui-icon-btn-primary">
                                        <i class="fa-solid fa-pen text-[10px]"></i>
                                    </button>
                                    <form action="{{ route('superadmin.skills.skill.destroy', $skill->hashid) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this)" class="ui-icon-btn ui-icon-btn-danger">
                                            <i class="fa-solid fa-trash text-[10px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-[#94A3B4] text-center py-3">Belum ada skill di grup ini.</p>
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
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) btn.closest('form').submit();
            });
        }
    </script>
@endsection
