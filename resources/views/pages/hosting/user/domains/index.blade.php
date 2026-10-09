@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Domains"
            subtitle="Hubungkan domain milik Anda ke project Ryaze."
            icon="fa-solid fa-globe"
            iconColor="purple">
            <x-slot:actions>
                <a href="{{ route('user_hosting.projects') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800/60 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/40 border border-slate-200 dark:border-slate-700 text-sm font-medium rounded-lg transition-all shadow-sm">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    Ke Project
                </a>
                <button type="button" onclick="document.getElementById('addDomainModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white text-sm font-medium rounded-lg transition-all shadow-sm shadow-purple-500/20">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Domain
                </button>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="space-y-6">

            {{-- PETUNJUK SINGKAT --}}
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <h2 class="font-bold text-slate-800 dark:text-slate-100 mb-1">Cara Work-nya</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Tiga langkah, domain harus milik Anda sendiri (dibeli di registrar).</p>
                <ol class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach([
                        ['Tambah domain', 'Masukkan domain, Cloudflare otomatis dibuatkan zone dan sepasang nameserver.'],
                        ['Arahkan NS', 'Ganti nameserver domain di tempat Anda beli domain dengan NS yang kami tampilkan.'],
                        ['Sambungkan', 'Kalau sudah aktif, sambungkan domain ini ke project Anda.'],
                    ] as $i => $step)
                        <li class="flex gap-3">
                            <span class="shrink-0 w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 text-xs font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                            <span>
                                <span class="block text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $step[0] }}</span>
                                <span class="block text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">{{ $step[1] }}</span>
                            </span>
                        </li>
                    @endforeach
                </ol>
            </div>

            {{-- DAFTAR DOMAIN --}}
            <x-ui.card>
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100">Domain Anda</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider font-semibold">
                                <th class="px-6 py-4">Domain</th>
                                <th class="px-6 py-4">Project</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                            @forelse($domains as $domain)
                                @php
                                    $badge = match ($domain->ssl_status) {
                                        'active' => ['bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300', 'Aktif'],
                                        'failed' => ['bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300', 'Gagal'],
                                        default => ['bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300', 'Menunggu NS'],
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('user_hosting.domains.show', $domain->hashid) }}"
                                           class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-2">
                                            <i class="fa-solid fa-globe text-xs"></i>
                                            {{ $domain->domain_name }}
                                        </a>
                                        @if ($domain->ssl_status === 'active')
                                            <a href="https://{{ $domain->domain_name }}" target="_blank"
                                               class="block text-[11px] text-slate-400 hover:text-indigo-500 mt-0.5">Buka situs</a>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($domain->project)
                                            <a href="{{ route('user_hosting.show', $domain->project->hashid) }}"
                                               class="text-slate-700 dark:text-slate-200 font-medium hover:text-indigo-600 dark:hover:text-indigo-400">
                                                {{ $domain->project->project_name }}
                                            </a>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500 italic">Belum disambungkan</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-block px-2 py-1 rounded text-xs font-bold {{ $badge[0] }}">{{ $badge[1] }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('user_hosting.domains.show', $domain->hashid) }}"
                                               class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                                                Kelola
                                            </a>
                                            <form action="{{ route('user_hosting.domains.destroy', $domain->hashid) }}" method="POST" class="inline"
                                                  onsubmit="event.preventDefault(); swConfirm('Hapus domain?', 'Zone Cloudflare dan routing domain ini akan dihapus permanen.').then(r => { if(r.isConfirmed) f.submit(); }); return false;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-300 hover:bg-rose-100 dark:hover:bg-rose-500/20 transition">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center">
                                        <i class="fa-solid fa-globe text-3xl text-slate-300 dark:text-slate-600 mb-3"></i>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada domain. Klik "Tambah Domain" untuk memulai.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </div>
    </x-ui.page-layout>

    {{-- MODAL TAMBAH DOMAIN --}}
    <div id="addDomainModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-lg my-8">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Tambah Domain</h3>
                <button type="button" onclick="document.getElementById('addDomainModal').classList.add('hidden')"
                        class="text-slate-400 hover:text-rose-500 transition-colors p-2 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-500/10">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form action="{{ route('user_hosting.domains.create') }}" method="POST">
                @csrf
                <div class="p-6 space-y-5">
                    <div>
                        <label for="domain_name" class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">
                            Nama Domain <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="domain_name" id="domain_name" required
                               placeholder="tokosaya.com"
                               class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1.5">
                            Tanpa <code class="text-[10px]">https://</code>, tanpa path. Domain harus milik Anda dan belum dipakai di hosting lain.
                        </p>
                        @error('domain_name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="project_id" class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">
                            Sambungkan ke Project <span class="text-slate-400 font-normal">(opsional)</span>
                        </label>
                        <select name="project_id" id="project_id"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            <option value="">Nanti dulu</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}">{{ $p->project_name }} ({{ $p->ryaze_domain }})</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1.5">
                            Boleh dikosongkan — domain tetap bisa disambungkan nanti dari halaman Domains.
                        </p>
                        @error('project_id')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="p-6 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('addDomainModal').classList.add('hidden')"
                            class="px-5 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl hover:bg-slate-100 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-sm transition-all">
                        <i class="fa-solid fa-arrow-right-to-bracket mr-1.5"></i> Tambah &amp; Lihat NS
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection