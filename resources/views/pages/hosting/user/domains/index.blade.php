@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Domains"
            eyebrow="DNS & Nama Domain"
            status="AKTIF"
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
                <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                    <span class="shrink-0 w-10 h-10 bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center">
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
                        class="ui-btn shrink-0 bg-amber-500 text-white hover:bg-amber-600">
                        <i class="fa-solid fa-arrow-up text-[10px]"></i>
                        Lihat Paket
                    </a>
                </div>
            @endunless

            {{-- PETUNJUK SINGKAT --}}
            <div class="ui-panel">
                <div class="ui-panel-head border-b-0 bg-[#F4F8FC] dark:bg-[#12233A]">
                    <h2 class="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">Dua Jenis Hostname</h2>
                    <span class="text-xs text-[#8A9BAE] dark:text-white/40">Satu project boleh dilayani banyak hostname sekaligus.</span>
                </div>
                <ol class="grid grid-cols-1 md:grid-cols-2 gap-px bg-[#E3EAF1] dark:bg-[#2A4463] border-t border-[#E3EAF1] dark:border-[#2A4463]">
                    @foreach([
                        ['Custom Domain', 'Domain milik Anda sendiri (tokoanda.com). Butuh ganti nameserver, butuh 1-24 jam untuk aktif.'],
                        ['Subdomain Anda', 'Subdomain dari custom domain Anda sendiri (blog.tokoanda.com). Langsung aktif begitu dibuat, tanpa ganti nameserver.'],
                    ] as $step)
                        <li class="ui-cell-alt p-5 flex items-start gap-3">
                            <span class="ui-check-mark mt-1 shrink-0">&#10003;</span>
                            <span class="min-w-0">
                                <span class="block font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC]">{{ $step[0] }}</span>
                                <span class="block text-xs text-[#525252] dark:text-[#8A9BAE] mt-1.5 leading-relaxed">{{ $step[1] }}</span>
                            </span>
                        </li>
                    @endforeach
                </ol>
                <div class="px-6 py-4 border-t border-[#EDF2F7] dark:border-[#24384F] bg-[#FAFBFD] dark:bg-[#0F1D30]">
                    <p class="text-xs text-[#525252] dark:text-[#8A9BAE] leading-relaxed">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        Untuk custom domain: masukkan domain, Cloudflare otomatis membuatkan zone, lalu arahkan nameserver domain Anda ke Cloudflare.
                        Setelah aktif, Anda bisa langsung membuat subdomain dari domain tersebut.
                    </p>
                </div>
            </div>

            {{-- RINGKASAN HOSTNAME --}}
            <div class="ui-panel">
                <div class="ui-panel-head border-b-0 bg-[#F4F8FC] dark:bg-[#12233A]">
                    <span class="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">Ringkasan Hostname</span>
                    <span class="ui-status"><span class="ui-status-dot"></span>{{ $domains->count() }} Hostname</span>
                </div>
                <div class="ui-stat-strip grid-cols-2 md:grid-cols-4">
                    <div class="px-6 py-6">
                        <p class="ui-stat-value">{{ $domains->count() }}</p>
                        <p class="ui-stat-label">Total Hostname</p>
                    </div>
                    <div class="px-6 py-6">
                        <p class="ui-stat-value">{{ $domains->where('ssl_status', 'active')->count() }}</p>
                        <p class="ui-stat-label">SSL Aktif</p>
                    </div>
                    <div class="px-6 py-6">
                        <p class="ui-stat-value">{{ $domains->reject(fn ($d) => $d->ssl_status === 'active' || $d->ssl_status === 'failed')->count() }}</p>
                        <p class="ui-stat-label">Menunggu NS</p>
                    </div>
                    <div class="px-6 py-6">
                        <p class="ui-stat-value">{{ $domains->where('type', \App\Models\HostingDomain::TYPE_SUBDOMAIN)->count() }}</p>
                        <p class="ui-stat-label">Subdomain</p>
                    </div>
                </div>
            </div>

            {{-- DAFTAR HOSTNAME --}}
            <div class="ui-panel">
                <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                    <h3 class="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">Hostname Anda</h3>
                    <a href="{{ route('user_hosting.projects') }}" class="ui-arrow-link">
                        Ke Project <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
                <div class="divide-y divide-[#EDF2F7] dark:divide-[#24384F]">
                    @forelse($domains as $domain)
                        @php
                            $badge = match ($domain->ssl_status) {
                                'active' => ['bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/50', 'Aktif'],
                                'failed' => ['bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800/50', 'Gagal'],
                                default => ['bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/50', 'Menunggu NS'],
                            };

                            $typeBadge = match ($domain->type) {
                                \App\Models\HostingDomain::TYPE_SUBDOMAIN => ['bg-[#E8F0F9] dark:bg-[#1D5B96]/20 text-[#1D5B96] dark:text-[#6BA8DC]', 'Subdomain'],
                                \App\Models\HostingDomain::TYPE_PLATFORM => ['bg-[#E8F0F9] dark:bg-[#1D5B96]/20 text-[#1D5B96] dark:text-[#7FB4E2]', 'Subdomain Platform'],
                                default => ['bg-[#F0F6FC] dark:bg-[#12233A] text-[#525252] dark:text-white/70', 'Custom Domain'],
                            };
                        @endphp
                        <div class="px-6 py-4 flex flex-col lg:flex-row lg:items-center gap-4 hover:bg-[#E8F0F9] dark:hover:bg-[#1A2F49] transition-colors">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <span class="ui-icon-box"><i class="fa-solid {{ $domain->isChild() ? 'fa-sitemap' : 'fa-globe' }} text-xs"></i></span>
                                <div class="min-w-0">
                                    <a href="{{ route('user_hosting.domains.show', $domain->hashid) }}"
                                       class="font-semibold text-[#1D5B96] dark:text-[#6BA8DC] hover:text-[#2A6FAD] dark:hover:text-[#7FB4E2] truncate block">
                                        {{ $domain->domain_name }}
                                    </a>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <span class="inline-block px-2 py-0.5 border border-[#E3EAF1] dark:border-[#2A4463] font-mono text-[10px] uppercase tracking-[0.2em] {{ $typeBadge[0] }}">{{ $typeBadge[1] }}</span>
                                        @if ($domain->parent)
                                            <span class="font-mono text-[10px] uppercase tracking-wider text-[#8A9BAE] dark:text-white/45">dari {{ $domain->parent->domain_name }}</span>
                                        @endif
                                        @if ($domain->ssl_status === 'active')
                                            <a href="https://{{ $domain->domain_name }}" target="_blank"
                                               class="font-mono text-[10px] uppercase tracking-wider text-[#8A9BAE] hover:text-[#1D5B96] dark:text-white/45 dark:hover:text-[#6BA8DC]">Buka situs</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="lg:w-48 shrink-0 min-w-0">
                                @if ($domain->project)
                                    <a href="{{ route('user_hosting.show', $domain->project->hashid) }}"
                                       class="text-[#0F1A2A] dark:text-white/85 font-medium hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] truncate block">
                                        {{ $domain->project->project_name }}
                                    </a>
                                @else
                                    <span class="text-[#8A9BAE] dark:text-white/45 italic text-sm">Belum disambungkan</span>
                                @endif
                            </div>
                            <div class="lg:w-40 shrink-0">
                                <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-mono uppercase tracking-[0.2em] font-semibold border {{ $badge[0] }}">{{ $badge[1] }}</span>
                            </div>
                            <div class="flex justify-end lg:justify-start gap-2 shrink-0">
                                <a href="{{ route('user_hosting.domains.show', $domain->hashid) }}"
                                   class="ui-chip">
                                    Kelola
                                </a>
                                <form action="{{ route('user_hosting.domains.destroy', $domain->hashid) }}" method="POST" class="inline"
                                      onsubmit="event.preventDefault(); let f = this; swConfirm('Hapus {{ $domain->domain_name }}?', '{{ $domain->isChild() ? 'DNS record' : 'Zone Cloudflare' }} dan routing hostname ini akan dihapus permanen.').then(r => { if(r.isConfirmed) f.submit(); }); return false;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ui-btn ui-btn-sm ui-btn-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-20 text-center font-mono text-[12px] uppercase tracking-[0.2em] text-[#94A3B4] dark:text-white/30">
                            Belum ada hostname. Tambahkan custom domain dulu untuk mulai membuat subdomain.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </x-ui.page-layout>

    {{-- MODAL TAMBAH DOMAIN --}}
    <div id="addDomainModal" class="ui-modal-overlay hidden z-50">
        <div class="bg-white dark:bg-[#0F1D30] shadow-none w-full max-w-lg my-8">
            <div class="p-6 border-b border-[#EDF2F7] dark:border-[#2A4463] flex items-center justify-between">
                <div>
                    <h3 class="ui-modal-title">Tambah Custom Domain</h3>
                    <p class="text-xs text-[#525252] dark:text-[#8A9BAE] mt-0.5">Domain milik Anda sendiri, mis. tokoanda.com</p>
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
                        <label for="domain_name" class="block text-xs font-medium text-[#525252] dark:text-[#8A9BAE] mb-1">
                            Nama Domain <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="domain_name" id="domain_name" required
                               placeholder="tokosaya.com"
                               class="ui-field ui-field-sm">
                        <p class="text-[11px] text-[#525252] dark:text-[#8A9BAE] mt-1.5">
                            Tanpa <code class="text-[10px]">https://</code>, tanpa path. Domain harus milik Anda dan belum dipakai di hosting lain.
                        </p>
                        @error('domain_name')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="project_id" class="block text-xs font-medium text-[#525252] dark:text-[#8A9BAE] mb-1">
                            Sambungkan ke Project <span class="text-[#8A9BAE] font-normal">(opsional)</span>
                        </label>
                        <select name="project_id" id="project_id"
                                class="ui-field ui-field-sm">
                            <option value="">Nanti dulu</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}">{{ $p->project_name }} ({{ $p->ryaze_domain }})</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-[#525252] dark:text-[#8A9BAE] mt-1.5">
                            Boleh dikosongkan — domain tetap bisa disambungkan nanti dari halaman Domains.
                        </p>
                        @error('project_id')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="p-6 border-t border-[#EDF2F7] dark:border-[#2A4463] bg-[#F4F8FC] dark:bg-[#0F1D30] flex justify-end gap-3">
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
        <div class="bg-white dark:bg-[#0F1D30] shadow-none w-full max-w-lg my-8">
            <div class="p-6 border-b border-[#EDF2F7] dark:border-[#2A4463] flex items-center justify-between">
                <div>
                    <h3 class="ui-modal-title">Tambah Subdomain</h3>
                    <p class="text-xs text-[#525252] dark:text-[#8A9BAE] mt-0.5">Aktif seketika, tanpa ganti nameserver</p>
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
                        <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 px-4 py-3">
                            <p class="text-xs text-amber-800 dark:text-amber-300 leading-relaxed">
                                Anda belum punya custom domain aktif. Tambah domain dulu dan selesaikan perubahan nameserver —
                                subdomain hanya bisa dibuat dari domain milik Anda sendiri.
                            </p>
                        </div>
                    @else
                        <div>
                            <label for="parent_id" class="block text-xs font-medium text-[#525252] dark:text-[#8A9BAE] mb-1">
                                Domain Induk <span class="text-rose-500">*</span>
                            </label>
                            <select name="parent_id" id="parent_id" required
                                    class="ui-field ui-field-sm">
                                @foreach($rootDomains as $rd)
                                    <option value="{{ $rd->id }}">{{ $rd->domain_name }}</option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-[#525252] dark:text-[#8A9BAE] mt-1.5">Hanya domain yang sudah aktif di Cloudflare.</p>
                            @error('parent_id')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="label_own" class="block text-xs font-medium text-[#525252] dark:text-[#8A9BAE] mb-1">
                                Label Subdomain <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="label" id="label_own" required placeholder="blog"
                                   pattern="[a-zA-Z0-9]([a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?"
                                   class="ui-field ui-field-sm">
                            <p class="text-[11px] text-[#525252] dark:text-[#8A9BAE] mt-1.5">
                                Cukup tulis satu bagian saja — sisanya dilengkapi otomatis. SSL dari wildcard Cloudflare.
                            </p>
                            @error('label')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    @include('pages.hosting.user.domains.partials.project-select')
                </div>
                <div class="p-6 border-t border-[#EDF2F7] dark:border-[#2A4463] bg-[#F4F8FC] dark:bg-[#0F1D30] flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('addSubdomainModal').classList.add('hidden')"
                            class="ui-btn ui-btn-md ui-btn-secondary">
                        Batal
                    </button>
                    <button type="submit" @disabled($rootDomains->isEmpty())
                            class="ui-btn ui-btn-md ui-btn-primary">
                        <i class="fa-solid fa-sitemap mr-1.5"></i> Buat Subdomain
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection