@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Domains"
            subtitle="Hubungkan domain milik Anda ke project Ryaze."
            icon="fa-solid fa-globe"
            iconColor="purple">
            <x-slot:actions>
                @php
                    // Custom domain & subdomain hanya untuk paket Pro & Business.
                    $customDomainCheck = Auth::user()->canManageCustomDomain();
                    $upgradeMessage = $customDomainCheck['message'];
                @endphp
                <a href="{{ route('user_hosting.projects') }}"
                    class="ui-btn ui-btn-md ui-btn-secondary">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    Ke Project
                </a>
                @if ($customDomainCheck['allowed'])
                    <button type="button" onclick="document.getElementById('addSubdomainModal').classList.remove('hidden')"
                        class="ui-btn ui-btn-md ui-btn-secondary">
                        <i class="fa-solid fa-sitemap text-[10px]"></i>
                        Tambah Subdomain
                    </button>
                    <button type="button" onclick="document.getElementById('addDomainModal').classList.remove('hidden')"
                        class="ui-btn ui-btn-md ui-btn-primary">
                        <i class="fa-solid fa-plus"></i>
                        Tambah Domain
                    </button>
                @else
                    <button type="button" onclick="Swal.fire('Fitur Terkunci', @js($upgradeMessage), 'warning')"
                        class="ui-btn ui-btn-md ui-btn-secondary" disabled>
                        <i class="fa-solid fa-lock text-[10px]"></i>
                        Tambah Domain
                    </button>
                    <a href="{{ route('user_hosting.subscription') }}"
                        class="ui-btn ui-btn-md ui-btn-primary">
                        <i class="fa-solid fa-arrow-up text-[10px]"></i>
                        Upgrade Paket
                    </a>
                @endif
            </x-slot:actions>
        </x-ui.page-header>

        <div class="space-y-6">

            {{-- INFO PAKET --}}
            @unless ($customDomainCheck['allowed'])
                <div class="bg-amber-50 dark:bg-amber-500/10 rounded-2xl border border-amber-200 dark:border-amber-500/30 p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                    <span class="shrink-0 w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <h2 class="font-bold text-amber-900 dark:text-amber-200">Custom domain butuh paket Pro atau Business</h2>
                        <p class="text-xs text-amber-800 dark:text-amber-300 mt-1 leading-relaxed">
                            Paket {{ ucfirst(Auth::user()->getActivePlan() ?? 'free') }} Anda belum mendukung custom domain maupun subdomain.
                            Upgrade untuk pasang domain milik Anda sendiri (tokoanda.com) dan subdomainnya.
                        </p>
                    </div>
                    <a href="{{ route('user_hosting.subscription') }}"
                        class="shrink-0 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-arrow-up text-[10px]"></i>
                        Lihat Paket
                    </a>
                </div>
            @endunless

            {{-- PETUNJUK SINGKAT --}}
            <div class="ui-card p-6 min-w-0">
                <h2 class="font-bold text-slate-800 dark:text-slate-100 mb-1">Dua Jenis Hostname</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Satu project boleh dilayani banyak hostname sekaligus.</p>
                <ol class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach([
                        ['Custom Domain', 'Domain milik Anda sendiri (tokoanda.com). Butuh ganti nameserver, butuh 1-24 jam untuk aktif.'],
                        ['Subdomain Anda', 'Subdomain dari custom domain Anda sendiri (blog.tokoanda.com). Langsung aktif begitu dibuat, tanpa ganti nameserver.'],
                    ] as $step)
                        <li class="flex gap-3">
                            <span class="shrink-0 w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 text-xs font-bold flex items-center justify-center">
                                <i class="fa-solid fa-check text-[9px]"></i>
                            </span>
                            <span>
                                <span class="block text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $step[0] }}</span>
                                <span class="block text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">{{ $step[1] }}</span>
                            </span>
                        </li>
                    @endforeach
                </ol>
                <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        Untuk custom domain: masukkan domain, Cloudflare otomatis membuatkan zone, lalu arahkan nameserver domain Anda ke Cloudflare.
                        Setelah aktif, Anda bisa langsung membuat subdomain dari domain tersebut.
                    </p>
                </div>
            </div>

            {{-- DAFTAR HOSTNAME --}}
            <x-ui.card>
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100">Hostname Anda</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider font-semibold">
                                <th class="px-6 py-4">Hostname</th>
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

                                    $typeBadge = match ($domain->type) {
                                        \App\Models\HostingDomain::TYPE_SUBDOMAIN => ['bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300', 'Subdomain'],
                                        \App\Models\HostingDomain::TYPE_PLATFORM => ['bg-violet-100 dark:bg-violet-500/20 text-violet-700 dark:text-violet-300', 'Subdomain Platform'],
                                        default => ['bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300', 'Custom Domain'],
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('user_hosting.domains.show', $domain->hashid) }}"
                                           class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-2">
                                            <i class="fa-solid {{ $domain->isChild() ? 'fa-sitemap' : 'fa-globe' }} text-xs"></i>
                                            {{ $domain->domain_name }}
                                        </a>
                                        <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                            <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold {{ $typeBadge[0] }}">{{ $typeBadge[1] }}</span>
                                            @if ($domain->parent)
                                                <span class="text-[11px] text-slate-400 dark:text-slate-500">dari {{ $domain->parent->domain_name }}</span>
                                            @endif
                                        </div>
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
                                               class="ui-chip">
                                                Kelola
                                            </a>
                                            <form action="{{ route('user_hosting.domains.destroy', $domain->hashid) }}" method="POST" class="inline"
                                                  onsubmit="event.preventDefault(); swConfirm('Hapus {{ $domain->domain_name }}?', '{{ $domain->isChild() ? 'DNS record' : 'Zone Cloudflare' }} dan routing hostname ini akan dihapus permanen.').then(r => { if(r.isConfirmed) f.submit(); }); return false;">
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
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada hostname. Tambahkan custom domain dulu untuk mulai membuat subdomain.</p>
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
    <div id="addDomainModal" class="ui-modal-overlay hidden z-50">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-lg my-8">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <h3 class="ui-modal-title">Tambah Custom Domain</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Domain milik Anda sendiri, mis. tokoanda.com</p>
                </div>
                <button type="button" onclick="document.getElementById('addDomainModal').classList.add('hidden')"
                        class="ui-icon-btn ui-icon-btn-neutral">
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
                               class="ui-field ui-field-sm">
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
                                class="ui-field ui-field-sm">
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
                            class="ui-btn ui-btn-md ui-btn-secondary">
                        Batal
                    </button>
                    <button type="submit" class="ui-btn ui-btn-md ui-btn-primary">
                        <i class="fa-solid fa-arrow-right-to-bracket mr-1.5"></i> Tambah &amp; Lihat NS
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL TAMBAH SUBDOMAIN --}}
    <div id="addSubdomainModal" class="ui-modal-overlay hidden z-50">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-lg my-8">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <h3 class="ui-modal-title">Tambah Subdomain</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Aktif seketika, tanpa ganti nameserver</p>
                </div>
                <button type="button" onclick="document.getElementById('addSubdomainModal').classList.add('hidden')"
                        class="ui-icon-btn ui-icon-btn-neutral">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('user_hosting.domains.subdomain.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-5">
                    @if ($rootDomains->isEmpty())
                        <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded-xl px-4 py-3">
                            <p class="text-xs text-amber-800 dark:text-amber-300 leading-relaxed">
                                Anda belum punya custom domain aktif. Tambah domain dulu dan selesaikan perubahan nameserver —
                                subdomain hanya bisa dibuat dari domain milik Anda sendiri.
                            </p>
                        </div>
                    @else
                        <div>
                            <label for="parent_id" class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">
                                Domain Induk <span class="text-red-500">*</span>
                            </label>
                            <select name="parent_id" id="parent_id" required
                                    class="ui-field ui-field-sm">
                                @foreach($rootDomains as $rd)
                                    <option value="{{ $rd->id }}">{{ $rd->domain_name }}</option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1.5">Hanya domain yang sudah aktif di Cloudflare.</p>
                            @error('parent_id')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="label_own" class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">
                                Label Subdomain <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="label" id="label_own" required placeholder="blog"
                                   pattern="[a-zA-Z0-9]([a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?"
                                   class="ui-field ui-field-sm">
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1.5">
                                Cukup tulis satu bagian saja — sisanya dilengkapi otomatis. SSL dari wildcard Cloudflare.
                            </p>
                            @error('label')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    @include('pages.hosting.user.domains.partials.project-select')
                </div>
                <div class="p-6 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('addSubdomainModal').classList.add('hidden')"
                            class="ui-btn ui-btn-md ui-btn-secondary">
                        Batal
                    </button>
                    <button type="submit" @disabled($rootDomains->isEmpty())
                            class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-sitemap mr-1.5"></i> Buat Subdomain
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection