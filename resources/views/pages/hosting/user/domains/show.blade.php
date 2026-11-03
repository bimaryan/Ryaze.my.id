@extends('index')

@section('content')
    @php
        use App\Models\HostingDomain;

        $isActive = $domain->ssl_status === 'active';
        $isChild = $domain->isChild();
        $parent = $domain->parent;
        $typeBadge = match ($domain->type) {
            HostingDomain::TYPE_SUBDOMAIN => ['bg-[#E8F0F9] dark:bg-[#1D5B96]/20 text-[#1D5B96] dark:text-[#6BA8DC]', 'Subdomain'],
            HostingDomain::TYPE_PLATFORM => ['bg-[#E8F0F9] dark:bg-[#1D5B96]/20 text-[#1D5B96] dark:text-[#7FB4E2]', 'Subdomain Platform'],
            default => ['bg-[#F0F6FC] dark:bg-[#12233A] text-[#525252] dark:text-white/70', 'Custom Domain'],
        };
    @endphp

    <x-ui.page-layout>
        <x-ui.page-header
            title="{{ $domain->domain_name }}"
            eyebrow="Pengaturan Domain"
            subtitle="{{ $isChild ? 'Subdomain aktif seketika, tanpa perlu ganti nameserver.' : 'Verifikasi nameserver lalu sambungkan ke project Anda.' }}"
            icon="fa-solid {{ $isChild ? 'fa-sitemap' : 'fa-globe' }}"
            iconColor="purple">
            <x-slot:actions>
                <a href="{{ route('user_hosting.domains.index') }}"
                    class="ui-btn ui-btn-md ui-btn-ghost">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    Semua Domain
                </a>
                @if ($isActive)
                    <a href="https://{{ $domain->domain_name }}" target="_blank"
                       class="ui-btn ui-btn-md ui-btn-secondary">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        Buka Situs
                    </a>
                @endif
            </x-slot:actions>
        </x-ui.page-header>

        <div class="mb-6 flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center px-3 py-1.5 border border-[#E3EAF1] dark:border-[#2A4463] bg-[#FAFBFD] dark:bg-[#0F1D30] font-mono text-[10px] uppercase tracking-[0.2em] {{ $typeBadge[0] }}">{{ $typeBadge[1] }}</span>
            @if ($parent)
                <a href="{{ route('user_hosting.domains.show', $parent->hashid) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-[#E3EAF1] dark:border-[#2A4463] bg-[#FAFBFD] dark:bg-[#0F1D30] font-mono text-[10px] uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] transition-colors">
                    <i class="fa-solid fa-level-up text-[10px]"></i>
                    induk: {{ $parent->domain_name }}
                </a>
            @endif
            @if ($isChild)
                <span class="inline-flex items-center px-3 py-1.5 border border-[#E3EAF1] dark:border-[#2A4463] bg-[#FAFBFD] dark:bg-[#0F1D30] font-mono text-[10px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/50">
                    zone: {{ $domain->zoneDomain() }}
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- KOLOM KIRI: INSTRUKSI --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- STATUS --}}
                <div class="ui-panel">
                    <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                        <span class="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">Status Hostname</span>
                        <span class="inline-flex items-center gap-1.5 font-mono text-[10px] uppercase tracking-widest {{ $isActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $isActive ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                            {{ $isActive ? 'AKTIF' : 'MENUNGGU' }}
                        </span>
                    </div>
                    <div class="p-6 min-w-0">
                        <div class="flex items-start gap-4">
                            <span class="shrink-0 w-10 h-10 flex items-center justify-center border {{ $isActive ? 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/50' : 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/50' }}">
                                <i class="fa-solid {{ $isActive ? 'fa-circle-check' : 'fa-clock' }}"></i>
                            </span>
                        <div class="flex-1 min-w-0">
                            <h3 class="ui-eyebrow text-[11px]">
                                @if ($isChild)
                                    Subdomain aktif
                                @elseif ($isActive)
                                    Domain aktif di Cloudflare
                                @else
                                    Menunggu perubahan nameserver
                                @endif
                            </h3>
                            <p class="text-xs text-[#525252] dark:text-[#8A9BAE] mt-1 leading-relaxed">
                                @if ($isChild)
                                    Hostname ini memakai zone <strong>{{ $domain->zoneDomain() }}</strong> milik
                                    @if ($parent)
                                        domain induk <strong>{{ $parent->domain_name }}</strong> — tidak ada nameserver yang perlu diganti lagi.
                                    @else
                                        Ryaze.
                                    @endif
                                    DNS record dan SSL dibuat otomatis.
                                @elseif ($isActive)
                                    Nameserver sudah tersambung. Di bawah ini domainnya sudah dilayani — Anda bisa menyambungkannya ke project kapan saja.
                                @else
                                    Ganti nameserver domain Anda di tempat pembelian domain dengan dua nameserver di bawah ini, lalu tekan "Cek Status".
                                    Propagasi biasanya memakan waktu 5 menit hingga 24 jam.
                                @endif
                            </p>
                            <form action="{{ route('user_hosting.domains.status', $domain->hashid) }}" method="POST" class="mt-4">
                                @csrf
                                <button type="submit" class="ui-btn ui-btn-md ui-btn-primary">
                                    <i class="fa-solid fa-rotate"></i>
                                    {{ $isChild ? 'Periksa Ulang' : 'Cek Status' }}
                                </button>
                            </form>
                        </div>
                        </div>
                    </div>
                </div>

                {{-- NAMESERVER (khusus custom domain) --}}
                @if (! $isChild && ! $isActive && ! empty($domain->nameservers))
                    <div class="ui-panel overflow-hidden">
                        <div class="ui-panel-head flex-col items-start gap-1.5 py-4 bg-[#F4F8FC] dark:bg-[#12233A]">
                            <h3 class="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">Masukkan Nameserver Ini</h3>
                            <p class="text-xs text-[#525252] dark:text-[#8A9BAE]">
                                Dihasilkan Cloudflare dan spesifik untuk domain ini. Ganti nameserver lama di registrar Anda dengan kedua nilai di bawah.
                            </p>
                        </div>
                        <div class="divide-y divide-[#EDF2F7] dark:divide-[#24384F]">
                            @foreach ($domain->nameservers as $ns)
                                <div class="flex items-center gap-3 bg-[#FAFBFD] dark:bg-[#0F1D30] px-6 py-3">
                                    <i class="fa-solid fa-server text-[#8A9BAE] text-xs"></i>
                                    <code class="flex-1 min-w-0 text-sm font-mono text-[#0F1A2A] dark:text-white select-all">{{ $ns }}</code>
                                    <button type="button"
                                            onclick="navigator.clipboard.writeText('{{ $ns }}'); this.innerHTML='<i class=&quot;fa-solid fa-check text-emerald-500&quot;></i>'; setTimeout(() => { this.innerHTML='<i class=&quot;fa-solid fa-copy text-[#8A9BAE]&quot;></i>'; }, 1500)"
                                            class="p-2 text-[#8A9BAE] hover:text-[#1D5B96] transition-colors" title="Copy">
                                        <i class="fa-solid fa-copy"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <div class="px-6 py-4 border-t border-[#EDF2F7] dark:border-[#24384F]">
                            <p class="text-[11px] text-[#525252] dark:text-[#8A9BAE] leading-relaxed">
                                <i class="fa-solid fa-circle-info mr-1"></i>
                                SSL otomatis dibuat Cloudflare setelah nameserver aktif — tidak perlu pasang sertifikat tambahan.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- SUBDOMAIN DARI DOMAIN INI --}}
                @if (! $isChild && $children->isNotEmpty())
                    <div class="ui-panel overflow-hidden">
                        <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                            <div class="min-w-0">
                                <h3 class="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">Subdomain ({{ $children->count() }})</h3>
                                <p class="text-xs text-[#8A9BAE] dark:text-white/40 mt-1">Hostname lain yang memakai domain ini sebagai induk.</p>
                            </div>
                            @php
                                    $canAddSubdomain = Auth::user()->canManageCustomDomain();
                                @endphp
                                @if ($canAddSubdomain['allowed'])
                                    <a href="#"
                                       onclick="event.preventDefault(); openSubdomainFor('{{ $domain->id }}', '{{ $domain->domain_name }}'); return false;"
                                       class="ui-btn ui-btn-sm ui-btn-primary shrink-0">
                                        <i class="fa-solid fa-plus"></i>
                                        Tambah Subdomain
                                    </a>
                                @else
                                    <button type="button"
                                            onclick="Swal.fire('Fitur Terkunci', @js($canAddSubdomain['message']), 'warning')"
                                            class="ui-btn ui-btn-sm ui-btn-secondary shrink-0 opacity-60 cursor-not-allowed">
                                        <i class="fa-solid fa-lock"></i>
                                        Tambah Subdomain
                                    </button>
                                @endif
                        </div>
                        <div class="divide-y divide-[#EDF2F7] dark:divide-[#24384F]">
                            @foreach ($children as $child)
                                <div class="px-6 py-3 flex items-center gap-3 hover:bg-[#E8F0F9] dark:hover:bg-[#1A2F49] transition-colors">
                                    <i class="fa-solid fa-sitemap text-[#1D5B96] dark:text-[#6BA8DC] text-xs"></i>
                                    <a href="{{ route('user_hosting.domains.show', $child->hashid) }}"
                                       class="flex-1 min-w-0 text-sm font-semibold text-[#0F1A2A] dark:text-white hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] truncate">
                                        {{ $child->domain_name }}
                                    </a>
                                    @if ($child->project)
                                        <span class="text-xs text-[#525252] dark:text-[#8A9BAE] truncate">{{ $child->project->project_name }}</span>
                                    @else
                                        <span class="text-xs text-[#8A9BAE] dark:text-white/45 italic">Belum disambungkan</span>
                                    @endif
                                    <form action="{{ route('user_hosting.domains.destroy', $child->hashid) }}" method="POST" class="inline"
                                          onsubmit="event.preventDefault(); let f = this; swConfirm('Hapus {{ $child->domain_name }}?', 'DNS record dan routing subdomain ini akan dihapus.').then(r => { if(r.isConfirmed) f.submit(); }); return false;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="ui-icon-btn ui-icon-btn-danger" title="Hapus">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- KOLOM KANAN: SAMBUNGKAN PROJECT --}}
            <div class="space-y-6">
                {{-- SPESIFIKASI: baris kunci → nilai ala lembar spesifikasi --}}
                <div class="ui-panel overflow-hidden">
                    <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                        <span class="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">Spesifikasi</span>
                        <span class="inline-flex items-center gap-1.5 font-mono text-[10px] uppercase tracking-widest {{ $isActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $isActive ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                            {{ $isActive ? 'LIVE' : 'PENDING' }}
                        </span>
                    </div>
                    <dl>
                        <div class="ui-spec-row">
                            <dt class="ui-eyebrow-muted shrink-0">Tipe</dt>
                            <dd class="text-sm font-medium text-[#0F1A2A] dark:text-white text-right">{{ $typeBadge[1] }}</dd>
                        </div>
                        <div class="ui-spec-row">
                            <dt class="ui-eyebrow-muted shrink-0">Status SSL</dt>
                            <dd class="font-mono text-[13px] text-[#0F1A2A] dark:text-white text-right">{{ $domain->ssl_status }}</dd>
                        </div>
                        @if ($parent)
                            <div class="ui-spec-row">
                                <dt class="ui-eyebrow-muted shrink-0">Domain Induk</dt>
                                <dd class="font-mono text-[13px] text-[#0F1A2A] dark:text-white text-right break-all">{{ $parent->domain_name }}</dd>
                            </div>
                        @endif
                        @if ($isChild)
                            <div class="ui-spec-row">
                                <dt class="ui-eyebrow-muted shrink-0">Zone</dt>
                                <dd class="font-mono text-[13px] text-[#0F1A2A] dark:text-white text-right break-all">{{ $domain->zoneDomain() }}</dd>
                            </div>
                        @endif
                        <div class="ui-spec-row">
                            <dt class="ui-eyebrow-muted shrink-0">Proyek</dt>
                            <dd class="text-sm font-medium text-[#0F1A2A] dark:text-white text-right">{{ $domain->project ? $domain->project->project_name : 'Belum disambungkan' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="ui-panel overflow-hidden">
                    <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                        <h3 class="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">Project</h3>
                        <p class="text-xs text-[#8A9BAE] dark:text-white/40">Satu project boleh punya banyak hostname.</p>
                    </div>
                    <div class="p-6">
                        @if ($domain->project)
                            <p class="text-xs text-[#525252] dark:text-[#8A9BAE] mb-1">Hostname ini melayani project:</p>
                            <p class="text-sm font-bold text-[#0F1A2A] dark:text-white mb-4">{{ $domain->project->project_name }}</p>
                            <a href="{{ route('user_hosting.show', $domain->project->hashid) }}"
                               class="inline-flex w-full items-center justify-center px-4 py-2 bg-[#F0F6FC] dark:bg-[#0F1D30] text-[#0F1A2A] dark:text-white/85 text-sm font-semibold hover:bg-[#E3EAF1] dark:hover:bg-[#1A2F49] transition mb-2">
                                Buka Project
                            </a>
                            <form action="{{ route('user_hosting.domains.detach', $domain->hashid) }}" method="POST"
                                  onsubmit="event.preventDefault(); let f = this; swConfirm('Lepas dari project?', 'Hostname tidak akan dilayani sampai disambungkan lagi.').then(r => { if(r.isConfirmed) f.submit(); }); return false;">
                                @csrf
                                <button type="submit" class="ui-btn ui-btn-md ui-btn-primary w-full">
                                    Lepas dari Project
                                </button>
                            </form>
                        @else
                            @if ($projects->isEmpty())
                                <p class="text-xs text-[#525252] dark:text-[#8A9BAE]">
                                    Anda belum punya project. Buat project dulu sebelum menyambungkan hostname.
                                </p>
                            @elseif (! $isActive)
                                <p class="text-xs text-[#525252] dark:text-[#8A9BAE]">
                                    Verifikasi nameserver dulu. Setelah domain aktif, baru bisa disambungkan ke project.
                                </p>
                            @else
                                <form action="{{ route('user_hosting.domains.assign', $domain->hashid) }}" method="POST">
                                    @csrf
                                    <label for="assign_project" class="block text-xs font-medium text-[#525252] dark:text-[#8A9BAE] mb-1">Pilih Project</label>
                                    <select name="project_id" id="assign_project" required
                                            class="ui-field ui-field-sm mb-3">
                                        @foreach($projects as $p)
                                            <option value="{{ $p->id }}">{{ $p->project_name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="ui-btn ui-btn-md ui-btn-primary w-full">
                                        <i class="fa-solid fa-link mr-1.5"></i> Sambungkan
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="ui-panel overflow-hidden">
                    <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                        <h3 class="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-rose-600 dark:text-rose-400">Zona Berbahaya</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-xs text-[#525252] dark:text-[#8A9BAE] mb-3 leading-relaxed">
                            @if ($isChild)
                                Menghapus hostname ini akan menghapus DNS record-nya dan mencabut routing di tunnel.
                                Domain induk <strong>{{ $domain->zoneDomain() }}</strong> tidak terpengaruh.
                            @else
                                Menghapus domain akan menghapus zone-nya di Cloudflare, semua DNS record, dan routing di tunnel.
                                @if ($children->isNotEmpty())
                                    <strong>{{ $children->count() }} subdomain</strong> ikut terhapus karena tidak bisa hidup tanpa domain induknya.
                                @endif
                                Tindakan ini tidak bisa dibatalkan.
                            @endif
                        </p>
                        <form action="{{ route('user_hosting.domains.destroy', $domain->hashid) }}" method="POST"
                              onsubmit="event.preventDefault(); let f = this; swConfirm('Hapus {{ $domain->domain_name }}?', '{{ $isChild ? 'DNS record dan routing' : 'Zone Cloudflare dan routing' }} akan dihapus permanen.').then(r => { if(r.isConfirmed) f.submit(); }); return false;">
                            @csrf @method('DELETE')
                            <button type="submit" class="ui-btn ui-btn-md ui-btn-danger w-full">
                                <i class="fa-solid fa-trash mr-1.5"></i> Hapus {{ $isChild ? 'Subdomain' : 'Domain' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-ui.page-layout>

    {{-- MODAL TAMBAH SUBDOMAIN (dipakai dari daftar subdomain) --}}
    <div id="childSubdomainModal" class="ui-modal-overlay hidden z-50">
        <div class="bg-white dark:bg-[#0F1D30] shadow-none w-full max-w-lg my-8">
            <div class="p-6 border-b border-[#EDF2F7] dark:border-[#2A4463] flex items-center justify-between">
                <div>
                    <h3 class="ui-modal-title">Subdomain Baru</h3>
                    <p class="text-xs text-[#525252] dark:text-[#8A9BAE] mt-0.5">dari <span id="childParentName" class="font-mono"></span></p>
                </div>
                <button type="button" onclick="document.getElementById('childSubdomainModal').classList.add('hidden')"
                        class="ui-icon-btn ui-icon-btn-neutral">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form action="{{ route('user_hosting.domains.subdomain.store') }}" method="POST">
                @csrf
                <input type="hidden" name="parent_id" id="childParentId">
                <div class="p-6 space-y-5">
                    <div>
                        <label for="child_label" class="block text-xs font-medium text-[#525252] dark:text-[#8A9BAE] mb-1">
                            Label Subdomain <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="label" id="child_label" required placeholder="blog"
                               pattern="[a-zA-Z0-9]([a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?"
                               class="ui-field ui-field-sm">
                        <p class="text-[11px] text-[#525252] dark:text-[#8A9BAE] mt-1.5">
                            Hasilnya: <span id="childPreview" class="font-mono text-[#525252] dark:text-white/70">—</span>
                        </p>
                    </div>

                    @include('pages.hosting.user.domains.partials.project-select', ['idSuffix' => '_child'])
                </div>
                <div class="p-6 border-t border-[#EDF2F7] dark:border-[#2A4463] bg-[#F4F8FC] dark:bg-[#0F1D30] flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('childSubdomainModal').classList.add('hidden')"
                            class="ui-btn ui-btn-md ui-btn-secondary">
                        Batal
                    </button>
                    <button type="submit" class="ui-btn ui-btn-md ui-btn-primary">
                        <i class="fa-solid fa-sitemap mr-1.5"></i> Buat Subdomain
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openSubdomainFor(parentId, parentName) {
                document.getElementById('childParentId').value = parentId;
                document.getElementById('childParentName').textContent = parentName;
                document.getElementById('child_label').value = '';
                document.getElementById('childPreview').textContent = parentName;
                document.getElementById('childSubdomainModal').classList.remove('hidden');
            }

            document.getElementById('child_label').addEventListener('input', function () {
                var label = this.value.trim();
                var base = document.getElementById('childParentName').textContent;
                document.getElementById('childPreview').textContent = label ? label + '.' + base : base;
            });
        </script>
    @endpush
@endsection