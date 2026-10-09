@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="{{ $domain->domain_name }}"
            subtitle="Verifikasi nameserver lalu sambungkan ke project Anda."
            icon="fa-solid fa-globe"
            iconColor="purple">
            <x-slot:actions>
                <a href="{{ route('user_hosting.domains.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800/60 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/40 border border-slate-200 dark:border-slate-700 text-sm font-medium rounded-lg transition-all shadow-sm">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    Semua Domain
                </a>
                @if ($domain->ssl_status === 'active')
                    <a href="https://{{ $domain->domain_name }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800/60 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/40 border border-slate-200 dark:border-slate-700 text-sm font-medium rounded-lg transition-all shadow-sm">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        Buka Situs
                    </a>
                @endif
            </x-slot:actions>
        </x-ui.page-header>

        @php
            $isActive = $domain->ssl_status === 'active';
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- KOLOM KIRI: INSTRUKSI --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- STATUS --}}
                <div class="bg-white dark:bg-slate-800/60 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                    <div class="flex items-start gap-4">
                        <span class="shrink-0 w-10 h-10 rounded-xl flex items-center justify-center {{ $isActive ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400' }}">
                            <i class="fa-solid {{ $isActive ? 'fa-circle-check' : 'fa-clock' }}"></i>
                        </span>
                        <div class="flex-1">
                            <h3 class="font-bold text-slate-800 dark:text-slate-100">
                                {{ $isActive ? 'Domain aktif di Cloudflare' : 'Menunggu perubahan nameserver' }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                @if ($isActive)
                                    Nameserver sudah tersambung. Di bawah ini domainnya sudah dilayani — Anda bisa menyambungkannya ke project kapan saja.
                                @else
                                    Ganti nameserver domain Anda di tempat pembelian domain dengan dua nameserver di bawah ini, lalu tekan "Cek Status".
                                    Propagasi biasanya memakan waktu 5 menit hingga 24 jam.
                                @endif
                            </p>
                            <form action="{{ route('user_hosting.domains.status', $domain->hashid) }}" method="POST" class="mt-4">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                                    <i class="fa-solid fa-rotate"></i>
                                    Cek Status
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- NAMESERVER --}}
                @if (! $isActive && ! empty($domain->nameservers))
                    <div class="bg-white dark:bg-slate-800/60 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
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
                                    <code class="flex-1 text-sm font-mono text-slate-800 dark:text-slate-100 select-all">{{ $ns }}</code>
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
            </div>

            {{-- KOLOM KANAN: SAMBUNGKAN PROJECT --}}
            <div class="space-y-6">
                <div class="bg-white dark:bg-slate-800/60 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="font-bold text-slate-800 dark:text-slate-100">Project</h3>
                    </div>
                    <div class="p-6">
                        @if ($domain->project)
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Domain ini melayani project:</p>
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-4">{{ $domain->project->project_name }}</p>
                            <a href="{{ route('user_hosting.show', $domain->project->hashid) }}"
                               class="inline-flex w-full items-center justify-center px-4 py-2 bg-slate-100 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200 text-sm font-semibold rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition mb-2">
                                Buka Project
                            </a>
                            <form action="{{ route('user_hosting.domains.detach', $domain->hashid) }}" method="POST"
                                  onsubmit="event.preventDefault(); swConfirm('Lepas dari project?', 'Domain tidak akan dilayani sampai disambungkan lagi.').then(r => { if(r.isConfirmed) f.submit(); }); return false;">
                                @csrf
                                <button type="submit" class="inline-flex w-full items-center justify-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 text-sm font-semibold rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/40 transition">
                                    Lepas dari Project
                                </button>
                            </form>
                        @else
                            @if ($projects->isEmpty())
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    Anda belum punya project. Buat project dulu sebelum menyambungkan domain.
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
                                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition mb-3">
                                        @foreach($projects as $p)
                                            <option value="{{ $p->id }}">{{ $p->project_name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="inline-flex w-full items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                                        <i class="fa-solid fa-link mr-1.5"></i> Sambungkan
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800/60 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="font-bold text-rose-600 dark:text-rose-400">Zona Berbahaya</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-3 leading-relaxed">
                            Menghapus domain akan menghapus zone-nya di Cloudflare, semua DNS record, dan routing di tunnel. Tindakan ini tidak bisa dibatalkan.
                        </p>
                        <form action="{{ route('user_hosting.domains.destroy', $domain->hashid) }}" method="POST"
                              onsubmit="event.preventDefault(); swConfirm('Hapus domain?', 'Zone Cloudflare dan routing domain ini akan dihapus permanen.').then(r => { if(r.isConfirmed) f.submit(); }); return false;">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex w-full items-center justify-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                                <i class="fa-solid fa-trash mr-1.5"></i> Hapus Domain
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-ui.page-layout>
@endsection