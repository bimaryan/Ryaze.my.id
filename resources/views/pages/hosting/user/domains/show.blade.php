@extends('index')

@section('content')
    @php
        use App\Models\HostingDomain;

        $isActive = $domain->ssl_status === 'active';
        $isChild = $domain->isChild();
        $parent = $domain->parent;
        $typeBadge = match ($domain->type) {
            HostingDomain::TYPE_SUBDOMAIN => ['bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300', 'Subdomain'],
            HostingDomain::TYPE_PLATFORM => ['bg-violet-100 dark:bg-violet-500/20 text-violet-700 dark:text-violet-300', 'Subdomain Platform'],
            default => ['bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300', 'Custom Domain'],
        };
    @endphp

    <x-ui.page-layout>
        <x-ui.page-header
            title="{{ $domain->domain_name }}"
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
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800/60 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/40 border border-slate-200 dark:border-slate-700 text-sm font-medium rounded-lg transition-all shadow-sm">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        Buka Situs
                    </a>
                @endif
            </x-slot:actions>
        </x-ui.page-header>

        <div class="mb-6 flex flex-wrap items-center gap-2">
            <span class="inline-block px-2 py-1 rounded text-xs font-bold {{ $typeBadge[0] }}">{{ $typeBadge[1] }}</span>
            @if ($parent)
                <a href="{{ route('user_hosting.domains.show', $parent->hashid) }}"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                    <i class="fa-solid fa-level-up text-[10px]"></i>
                    induk: {{ $parent->domain_name }}
                </a>
            @endif
            @if ($isChild)
                <span class="text-xs text-slate-500 dark:text-slate-400">
                    zone: {{ $domain->zoneDomain() }}
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- KOLOM KIRI: INSTRUKSI --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- STATUS --}}
                <div class="ui-card p-6 min-w-0">
                    <div class="flex items-start gap-4">
                        <span class="shrink-0 w-10 h-10 flex items-center justify-center{{ $isActive ? 'ui-badge ui-badge-success' : 'ui-badge ui-badge-warning' }}">
                            <i class="fa-solid {{ $isActive ? 'fa-circle-check' : 'fa-clock' }}"></i>
                        </span>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-slate-800 dark:text-slate-100">
                                @if ($isChild)
                                    Subdomain aktif
                                @elseif ($isActive)
                                    Domain aktif di Cloudflare
                                @else
                                    Menunggu perubahan nameserver
                                @endif
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
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

                {{-- NAMESERVER (khusus custom domain) --}}
                @if (! $isChild && ! $isActive && ! empty($domain->nameservers))
                    <div class="ui-card overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                            <h3 class="font-bold text-slate-800 dark:text-slate-100">Masukkan Nameserver Ini</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Dihasilkan Cloudflare dan spesifik untuk domain ini. Ganti nameserver lama di registrar Anda dengan kedua nilai di bawah.
                            </p>
                        </div>
                        <div class="p-6 space-y-3">
                            @foreach ($domain->nameservers as $ns)
                                <div class="flex items-center gap-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3">
                                    <i class="fa-solid fa-server text-slate-400 text-xs"></i>
                                    <code class="flex-1 min-w-0 text-sm font-mono text-slate-800 dark:text-slate-100 select-all">{{ $ns }}</code>
                                    <button type="button"
                                            onclick="navigator.clipboard.writeText('{{ $ns }}'); this.innerHTML='<i class=&quot;fa-solid fa-check text-emerald-500&quot;></i>'; setTimeout(() => { this.innerHTML='<i class=&quot;fa-solid fa-copy text-slate-400&quot;></i>'; }, 1500)"
                                            class="p-2 text-slate-400 hover:text-indigo-600 transition-colors" title="Copy">
                                        <i class="fa-solid fa-copy"></i>
                                    </button>
                                </div>
                            @endforeach
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                                <i class="fa-solid fa-circle-info mr-1"></i>
                                SSL otomatis dibuat Cloudflare setelah nameserver aktif — tidak perlu pasang sertifikat tambahan.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- SUBDOMAIN DARI DOMAIN INI --}}
                @if (! $isChild && $children->isNotEmpty())
                    <div class="ui-card overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex flex-wrap justify-between items-center gap-3">
                            <div>
                                <h3 class="font-bold text-slate-800 dark:text-slate-100">Subdomain ({{ $children->count() }})</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Hostname lain yang memakai domain ini sebagai induk.</p>
                            </div>
                            @php
                                    $canAddSubdomain = Auth::user()->canManageCustomDomain();
                                @endphp
                                @if ($canAddSubdomain['allowed'])
                                    <a href="#"
                                       onclick="event.preventDefault(); openSubdomainFor('{{ $domain->id }}', '{{ $domain->domain_name }}'); return false;"
                                       class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition">
                                        <i class="fa-solid fa-plus"></i>
                                        Tambah Subdomain
                                    </a>
                                @else
                                    <button type="button"
                                            onclick="Swal.fire('Fitur Terkunci', @js($canAddSubdomain['message']), 'warning')"
                                            class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 cursor-not-allowed transition">
                                        <i class="fa-solid fa-lock"></i>
                                        Tambah Subdomain
                                    </button>
                                @endif
                        </div>
                        <div class="divide-y divide-slate-200 dark:divide-slate-700">
                            @foreach ($children as $child)
                                <div class="px-6 py-3 flex items-center gap-3 hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors">
                                    <i class="fa-solid fa-sitemap text-sky-500 dark:text-sky-400 text-xs"></i>
                                    <a href="{{ route('user_hosting.domains.show', $child->hashid) }}"
                                       class="flex-1 min-w-0 text-sm font-semibold text-slate-800 dark:text-slate-100 hover:text-indigo-600 dark:hover:text-indigo-400 truncate">
                                        {{ $child->domain_name }}
                                    </a>
                                    @if ($child->project)
                                        <span class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $child->project->project_name }}</span>
                                    @else
                                        <span class="text-xs text-slate-400 dark:text-slate-500 italic">Belum disambungkan</span>
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
                <div class="ui-card overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="font-bold text-slate-800 dark:text-slate-100">Project</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Satu project boleh punya banyak hostname.</p>
                    </div>
                    <div class="p-6">
                        @if ($domain->project)
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Hostname ini melayani project:</p>
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-4">{{ $domain->project->project_name }}</p>
                            <a href="{{ route('user_hosting.show', $domain->project->hashid) }}"
                               class="inline-flex w-full items-center justify-center px-4 py-2 bg-slate-100 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200 text-sm font-semibold rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition mb-2">
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
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    Anda belum punya project. Buat project dulu sebelum menyambungkan hostname.
                                </p>
                            @elseif (! $isActive)
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    Verifikasi nameserver dulu. Setelah domain aktif, baru bisa disambungkan ke project.
                                </p>
                            @else
                                <form action="{{ route('user_hosting.domains.assign', $domain->hashid) }}" method="POST">
                                    @csrf
                                    <label for="assign_project" class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Pilih Project</label>
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

                <div class="ui-card overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="font-bold text-rose-600 dark:text-rose-400">Zona Berbahaya</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-3 leading-relaxed">
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
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-lg my-8">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <h3 class="ui-modal-title">Subdomain Baru</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">dari <span id="childParentName" class="font-mono"></span></p>
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
                        <label for="child_label" class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">
                            Label Subdomain <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="label" id="child_label" required placeholder="blog"
                               pattern="[a-zA-Z0-9]([a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?"
                               class="ui-field ui-field-sm">
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1.5">
                            Hasilnya: <span id="childPreview" class="font-mono text-slate-600 dark:text-slate-300">—</span>
                        </p>
                    </div>

                    @include('pages.hosting.user.domains.partials.project-select', ['idSuffix' => '_child'])
                </div>
                <div class="p-6 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 flex justify-end gap-3">
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