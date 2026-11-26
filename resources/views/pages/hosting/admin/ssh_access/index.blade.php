@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header title="Akses SSH Klien" icon="key" iconColor="indigo">
            <x-slot:actions>
                <button type="button" onclick="document.getElementById('createSshModal').classList.remove('hidden')"
                    class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2 rounded-lg transition-colors shadow-sm text-sm">
                    <i class="fa-solid fa-plus mr-2"></i> Buat Akses SSH
                </button>
            </x-slot:actions>
        </x-ui.page-header>

        @if (session('success'))
            <div class="ui-panel mb-5 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10 rounded-lg">
                <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="ui-panel mb-5 px-4 py-3 text-sm text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30 bg-rose-50 dark:bg-rose-500/10 rounded-lg">
                <i class="fa-solid fa-circle-exclamation mr-2"></i>{{ session('error') }}
            </div>
        @endif

        @if (session('ssh_command'))
            <div class="mb-6">
            <x-ui.section title="Kredensial baru — simpan sekarang" icon="fa-solid fa-key" class="mb-6">
                <div class="space-y-4">
                    @foreach (explode("\n", (string) session('ssh_command')) as $i => $line)
                        <div>
                            <p class="ui-eyebrow mb-1.5">Terminal {{ $i + 1 }} — {{ $i === 0 ? 'listener tunnel (biarkan menyala)' : 'SSH / SFTP' }}</p>
                            <pre class="overflow-x-auto rounded-lg bg-[#0F1A2A] px-4 py-3 text-xs text-emerald-300">{{ $line }}</pre>
                        </div>
                    @endforeach
                    <div>
                        <p class="ui-eyebrow mb-1.5">Username akun panel</p>
                        <pre class="overflow-x-auto rounded-lg bg-[#0F1A2A] px-4 py-3 text-xs text-emerald-300">{{ session('ssh_user') }}</pre>
                    </div>
                    <div>
                        <p class="ui-eyebrow mb-1.5">Password random (tampil sekali)</p>
                        <pre class="overflow-x-auto rounded-lg bg-[#0F1A2A] px-4 py-3 text-xs text-amber-300">{{ session('ssh_password') }}</pre>
                    </div>
                </div>
            </x-ui.section>
            </div>
        @endif

        <div class="mb-6">
        <x-ui.section title="Akses dari Windows (PuTTY)" icon="fa-brands fa-windows" class="mb-6"
            description="PuTTY tidak terhubung langsung ke Cloudflare Access; jalankan listener cloudflared di PC, lalu arahkan PuTTY ke 127.0.0.1.">
            <ol class="list-decimal pl-5 text-xs text-[#525252] dark:text-white/70 space-y-1.5">
                <li>Letakkan <code>cloudflared.exe</code> dan <code>putty.exe</code> satu folder dengan paket klien.</li>
                <li>Login pakai <strong>username akun panel</strong> dan <strong>password random</strong> dari panel — tidak perlu kunci / PuTTYgen.</li>
                <li>Double-click <code>putty.bat</code>, atau manual: Host <code>127.0.0.1</code>, Port sesuai tabel, lalu masukkan username &amp; password.</li>
                <li>SFTP: pakai <strong>WinSCP</strong> (protokol SFTP, host/port sama).</li>
            </ol>
        </x-ui.section>
        </div>

        <x-ui.table>
            <x-slot:head>
                <th class="px-6 py-4">Klien / Project</th>
                <th class="px-6 py-4">Paket</th>
                <th class="px-6 py-4">Docker IP &amp; Port</th>
                <th class="px-6 py-4">Perintah Koneksi</th>
                <th class="px-6 py-4 text-center">Status</th>
                <th class="px-6 py-4 text-right">Aksi</th>
            </x-slot:head>

            @forelse ($accesses as $access)
                <tr class="hover:bg-slate-50 dark:bg-slate-800/50 dark:hover:bg-slate-700/40 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800 dark:text-slate-100">{{ $access->client?->name ?? '-' }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $access->project?->project_name ?? $access->slug }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <x-ui.badge variant="primary">{{ strtoupper($access->package) }}</x-ui.badge>
                    </td>
                    <td class="px-6 py-4 font-mono text-xs text-slate-600 dark:text-slate-300">
                        {{ $access->docker_ip }}:22
                        <div class="text-[10px] text-slate-400">publik :{{ $access->host_port }}</div>
                    </td>
                    <td class="px-6 py-4 font-mono text-[10px] text-slate-600 dark:text-slate-300">
                        <div class="font-sans text-[9px] font-bold uppercase tracking-wider text-indigo-500 dark:text-indigo-300">Terminal 1 — listener</div>
                        <pre class="whitespace-pre-wrap break-all">cloudflared access tcp --hostname {{ $access->tunnel_hostname ?: 'ssh-'.$access->slug.'.ryz.my.id' }} --listener 127.0.0.1:{{ $access->host_port }}</pre>
                        <div class="mt-1 font-sans text-[9px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-300">Terminal 2 — {{ strtoupper($access->package) }}</div>
                        <pre>@if ($access->package === 'sftp')
sftp -P {{ $access->host_port }} {{ $access->ssh_user }}@127.0.0.1
@else
ssh -p {{ $access->host_port }} {{ $access->ssh_user }}@127.0.0.1
@endif</pre>
                        <div class="font-sans text-[9px] text-slate-400">PuTTY → 127.0.0.1:{{ $access->host_port }} · user {{ $access->ssh_user }} · password dari kredensial</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $variant = match ($access->status) {
                                'active' => 'success',
                                'failed' => 'danger',
                                'revoked' => 'neutral',
                                default => 'warning',
                            };
                        @endphp
                        <x-ui.badge :variant="$variant">{{ ucfirst($access->status) }}</x-ui.badge>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <form action="{{ route('admin_hosting.ssh_access.destroy', $access->hashid) }}" method="POST" class="inline-block"
                            onsubmit="event.preventDefault(); let f = this; Swal.fire({title: 'Cabut Akses SSH?', text: 'Kunci & koneksi klien ini akan langsung tidak berlaku.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#e11d48', cancelButtonColor: '#8A9BAE', confirmButtonText: 'Ya, cabut!', cancelButtonText: 'Batal', customClass: {popup: 'text-sm'}}).then(res => { if (res.isConfirmed) f.submit(); }); return false;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-rose-600 dark:text-rose-300 bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-600 hover:text-white transition-all shadow-sm tooltip" title="Cabut Akses SSH">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-slate-400 dark:text-slate-500">Belum ada akses SSH.</td>
                </tr>
            @endforelse

            @if ($accesses->hasPages())
                <x-slot:pagination>
                    {{ $accesses->links() }}
                </x-slot:pagination>
            @endif
        </x-ui.table>

        <div id="createSshModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="ui-card w-full max-w-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E3EAF1] dark:border-[#2A4463] flex items-center justify-between">
                    <h3 class="font-mono text-[11px] font-bold uppercase tracking-[0.2em] text-[#0F1A2A] dark:text-white">Buat Akses SSH</h3>
                    <button type="button" onclick="document.getElementById('createSshModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin_hosting.ssh_access.store') }}" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="ui-eyebrow mb-1.5 block">Project</label>
                        <select name="project_hashid" required class="w-full rounded-lg border border-[#E3EAF1] dark:border-[#2A4463] bg-white dark:bg-[#12233A] px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30">
                            <option value="">-- pilih project --</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->hashid }}" @selected(old('project_hashid') === $project->hashid)>{{ $project->project_name }} ({{ $project->ryaze_domain }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="ui-eyebrow mb-1.5 block">Paket</label>
                        <select name="package" class="w-full rounded-lg border border-[#E3EAF1] dark:border-[#2A4463] bg-white dark:bg-[#12233A] px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30">
                            <option value="sftp" @selected(old('package', 'sftp') === 'sftp')>SFTP saja (chroot)</option>
                            <option value="shell" @selected(old('package') === 'shell')>SFTP + Shell</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="document.getElementById('createSshModal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100 bg-white dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 border border-slate-200 dark:border-slate-600 rounded-2xl shadow-sm transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-2xl shadow-sm transition-colors">Buat Akses</button>
                    </div>
                </form>
            </div>
        </div>
    </x-ui.page-layout>
@endsection
