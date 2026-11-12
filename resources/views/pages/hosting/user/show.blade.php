@extends('index')

@section('content')
    <x-ui.page-layout>

        {{-- Alerts --}}
        {{-- ── Header Project ────────────────────────────────────────────────── --}}
        <x-ui.page-header 
            title="{{ $project->project_name }}">
            <x-slot:iconSlot>
                @php
                    $fwIcon = get_framework_icon($project->framework);
                @endphp
                <div class="shrink-0 w-12 h-12 border border-[#E3EAF1] dark:border-[#2A4463] flex items-center justify-center bg-[#F0F6FC] dark:bg-[#0F1D30] text-[#1D5B96] dark:text-[#6BA8DC]">
                    <i class="{{ $fwIcon }} text-2xl"></i>
                </div>
            </x-slot:iconSlot>
            <x-slot:subtitle>
                @php
                    $activeDomain = $project->domains()->where('ssl_status', 'active')->first();
                    $displayUrl = $activeDomain ? $activeDomain->domain_name : $project->ryaze_domain;
                @endphp
                <a href="https://{{ $displayUrl }}" target="_blank"
                    class="text-sm font-medium text-[#1D5B96] dark:text-[#6BA8DC] hover:underline flex items-center gap-1 mt-1">
                    {{ $displayUrl }}
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                </a>
            </x-slot:subtitle>
            <x-slot:actions>
                @php
                    $statusClass = match ($project->status) {
                        'active' => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300',
                        'building' => 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 animate-pulse',
                        'unpaid' => 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 font-bold',
                        default => 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300',
                    };
                    $statusIcon = match ($project->status) {
                        'active' => 'fa-circle-check',
                        'building' => 'fa-spinner fa-spin',
                        'unpaid' => 'fa-file-invoice-dollar',
                        default => 'fa-triangle-exclamation',
                    };
                @endphp
                <span
                    id="project-status-badge"
                    data-status="{{ $project->status }}"
                    class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 font-mono text-xs font-bold uppercase tracking-wider {{ $statusClass }}">
                    <i class="fa-solid {{ $statusIcon }}"></i>
                    <span id="project-status-text">{{ $project->status }}</span>
                </span>
                @if ($project->dev_mode && in_array($project->framework, ['react', 'nextjs', 'vue']))
                    <a href="https://dev{{ $project->dev_port }}.{{ ltrim(substr($project->ryaze_domain, strlen(explode('.', $project->ryaze_domain)[0])), '.') }}" target="_blank" class="inline-flex justify-center items-center bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/40 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 px-3 py-1.5 text-xs font-medium transition gap-1.5">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Preview
                    </a>
                @endif
                <a href="{{ route('user_hosting.projects') }}" class="ui-btn ui-btn-sm ui-btn-icon ui-btn-secondary" title="Kembali" aria-label="Kembali">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        {{-- Tab Navigation --}}
        {{-- overflow-x-auto supaya tab tidak saling dorok saat layar sempit;
             -mx-1.5 + px-1.5 supaya shadow tidak terpotong wrapper scroll. --}}
        <div class="mb-6 mt-6 bg-white dark:bg-[#0F1D30] border border-[#E3EAF1] dark:border-[#2A4463] -mx-1.5 px-1.5">
        <div class="flex flex-nowrap gap-1 overflow-x-auto p-1.5 snap-x snap-mandatory [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <button data-tab="overview" id="tab-overview" onclick="switchTab('overview')"
                class="ui-tab ui-tab-active">
                <i class="fa-solid fa-chart-simple"></i> <span>Overview</span>
            </button>
            <button data-tab="logs" id="tab-logs" onclick="switchTab('logs')"
                class="ui-tab">
                <i class="fa-solid fa-scroll"></i> <span>Build Logs</span>
            </button>
            <button data-tab="terminal" id="tab-terminal" onclick="switchTab('terminal')"
                class="ui-tab">
                <i class="fa-solid fa-terminal"></i> <span>Terminal</span>
            </button>
            <button data-tab="files" id="tab-files" onclick="switchTab('files')"
                class="ui-tab">
                <i class="fa-solid fa-folder-tree"></i> <span>Root Files</span>
            </button>
            <button data-tab="ide" id="tab-ide" onclick="switchTab('ide')"
                class="ui-tab">
                <i class="fa-solid fa-laptop-code"></i> <span>IDE VS Code</span>
            </button>
            <button data-tab="env" id="tab-env" onclick="switchTab('env')"
                class="ui-tab">
                <i class="fa-solid fa-key"></i> <span>.env</span>
            </button>
            <button data-tab="settings" id="tab-settings" onclick="switchTab('settings')"
                class="ui-tab">
                <i class="fa-solid fa-gears"></i> <span>Settings</span>
            </button>

            {{-- <button data-tab="email" id="tab-email"
                class="ui-tab">
                <i class="fa-solid fa-envelope"></i> <span>Email</span>
            </button> --}}
            <button data-tab="domains" id="tab-domains" onclick="switchTab('domains')"
                class="ui-tab">
                <i class="fa-solid fa-globe"></i> <span>Domains</span>
            </button>
            <button data-tab="crons" id="tab-crons" onclick="switchTab('crons')"
                class="ui-tab">
                <i class="fa-solid fa-clock"></i> <span>Cron Jobs</span>
            </button>
            <button data-tab="team" id="tab-team" onclick="switchTab('team')"
                class="ui-tab">
                <i class="fa-solid fa-users"></i> <span>Team Access</span>
            </button>
        </div>
        </div>

        {{-- TAB: OVERVIEW --}}
        <div id="panel-overview" class="tab-panel">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    @if ($project->status == 'active')
                        <div class="ui-panel overflow-hidden">
                            <div class="bg-[#F4F8FC] dark:bg-[#12233A] px-4 py-2.5 border-b border-[#E3EAF1] dark:border-[#2A4463] flex items-center gap-3">
                                <div class="flex gap-1.5">
                                    <div class="w-2 h-2 bg-rose-400"></div>
                                    <div class="w-2 h-2 bg-amber-400"></div>
                                    <div class="w-2 h-2 bg-emerald-400"></div>
                                </div>
                                <div
                                    class="ml-2 bg-white dark:bg-[#0F1D30] px-3 py-1 text-xs font-mono text-[#525252] dark:text-white/50 w-full max-w-md flex items-center gap-2 border border-[#E3EAF1] dark:border-[#2A4463]">
                                    <i class="fa-solid fa-lock text-[10px] text-emerald-600 dark:text-emerald-300"></i>
                                    https://{{ $displayUrl }}
                                </div>
                                <a href="https://{{ $displayUrl }}" target="_blank"
                                    class="ml-auto text-[#8A9BAE] dark:text-white/40 hover:text-[#2A6FAD] dark:hover:text-[#6BA8DC] transition-colors">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                </a>
                            </div>
                            <div class="w-full h-[450px] bg-[#F4F8FC] dark:bg-[#0F1D30] flex items-center justify-center relative">
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <i class="fa-solid fa-circle-notch fa-spin text-[#C9D5E2] dark:text-white/50 text-3xl"></i>
                                </div>
                                <iframe src="https://{{ $displayUrl }}?v={{ time() }}"
                                    class="w-full h-full border-0 relative z-10 bg-white dark:bg-[#0F1D30]"></iframe>
                            </div>
                            <div class="bg-amber-50 dark:bg-amber-500/10 border-t border-amber-100 dark:border-amber-500/30 px-4 py-3 flex items-start gap-3">
                                <i class="fa-solid fa-circle-info text-amber-500 dark:text-amber-400 mt-0.5"></i>
                                <p class="text-xs text-amber-700 dark:text-amber-300 leading-relaxed">
                                    <strong>Website belum muncul?</strong> Harap bersabar. Jika Anda baru saja mendeploy proyek ini, mungkin diperlukan waktu <strong>1-5 menit</strong> agar DNS menyebar (propagasi) ke seluruh dunia, dan sistem menerbitkan sertifikat SSL (HTTPS) Anda. Coba refresh halaman beberapa saat lagi.
                                </p>
                            </div>
                        </div>
                    @elseif ($project->status == 'unpaid')
                        @php
                            $unpaidPayment = \App\Models\HostingPayment::where('user_id', $project->user_id)
                                ->where('invoice_number', 'like', 'HST-INV-%')
                                ->where('status', 'unpaid')
                                ->first();
                        @endphp
                        <div class="ui-panel p-10 text-center">
                            <div class="w-16 h-16 bg-rose-100 dark:bg-rose-500/20 border border-rose-200 dark:border-rose-500/40 flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-file-invoice-dollar text-rose-500 dark:text-rose-400 text-3xl"></i>
                            </div>
                            <h3 class="ui-display text-lg  mb-2">Menunggu Pembayaran</h3>
                            <p class="text-[#525252] dark:text-white/50 mb-6 text-sm max-w-md mx-auto">Tagihan langganan akun hosting Anda belum dibayar. Deployment akan otomatis dimulai setelah Anda menyelesaikan pembayaran langganan.</p>
                            @if($unpaidPayment)
                                <button type="button" onclick="openPaymentModal({{ $unpaidPayment->amount }}, '{{ number_format($unpaidPayment->amount, 0, ',', '.') }}', '{{ $unpaidPayment->invoice_number }}')"
                                    class="inline-flex items-center justify-center gap-2 bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 px-6 transition-colors">
                                    <i class="fa-solid fa-credit-card"></i> Pilih Metode Pembayaran
                                </button>
                            @else
                                <p class="text-xs text-rose-500 dark:text-rose-400">Invoice tidak ditemukan. Harap hubungi Admin.</p>
                            @endif
                        </div>
                    @else
                        <div class="ui-panel p-12 text-center">
                            <i class="fa-solid fa-satellite-dish text-[#C9D5E2] dark:text-white/50 text-5xl mb-4"></i>
                            <p class="text-[#525252] dark:text-white/50 font-medium">Preview tersedia setelah deployment selesai.</p>
                        </div>
                    @endif
                </div>
                <div class="space-y-4">
                    <div class="ui-panel min-w-0">
                        <div class="ui-panel-head">
                            <span class="ui-eyebrow">Detail Deployment</span>
                        </div>
                        <div class="text-sm">
                            <div class="ui-spec-row">
                                <span class="ui-eyebrow-muted shrink-0">Source Repository</span>
                                @php
                                    $isUploadSource   = ($project->source_type === 'upload') || (is_string($project->repo_source) && str_starts_with($project->repo_source, 'upload:'));
                                    $isTemplateSource = ($project->source_type === 'template') || (is_string($project->repo_source) && str_starts_with($project->repo_source, 'template:'));
                                @endphp
                                @if($isUploadSource)
                                    <span class="font-semibold text-[#0F1A2A] dark:text-white flex items-center min-w-0 truncate">
                                        <i class="fa-solid fa-file-zipper mr-2 text-lg text-emerald-600 dark:text-emerald-300"></i>
                                        ZIP Upload &mdash; {{ basename(str_replace('upload:', '', $project->repo_source)) }}
                                    </span>
                                @elseif($isTemplateSource)
                                    <span class="font-semibold text-[#0F1A2A] dark:text-white flex items-center min-w-0 truncate">
                                        <i class="fa-solid fa-wand-magic-sparkles mr-2 text-lg text-[#1D5B96] dark:text-[#6BA8DC]"></i>
                                        Template &mdash; {{ ucwords(str_replace(['template:', '_', '-'], ['', ' ', ' '], (string) $project->repo_source)) }}
                                    </span>
                                @else
                                    <a href="{{ $project->repo_source }}" target="_blank"
                                        class="font-semibold text-[#0F1A2A] dark:text-white hover:text-[#2A6FAD] dark:hover:text-[#6BA8DC] flex items-center min-w-0 truncate">
                                        <i class="fa-brands fa-github mr-2 text-lg"></i>
                                        {{ str_replace('https://github.com/', '', $project->repo_source) }}
                                    </a>
                                @endif
                            </div>
                            <div class="ui-spec-row">
                                <span class="ui-eyebrow-muted shrink-0">Branch</span>
                                <span
                                    class="ui-badge ui-badge-neutral border font-mono">
                                    <i class="fa-solid fa-code-branch mr-1.5"></i> {{ $project->branch }}
                                </span>
                            </div>
                            <div class="ui-spec-row">
                                <span class="ui-eyebrow-muted shrink-0">Framework</span>
                                <span class="font-semibold text-[#0F1A2A] dark:text-white uppercase font-mono">{{ $project->framework }}</span>
                            </div>
                            <div class="ui-spec-row">
                                <span class="ui-eyebrow-muted shrink-0">Root Directory</span>
                                <span
                                    class="ui-badge ui-badge-neutral font-mono border block truncate">
                                    /{{ explode('.', $project->ryaze_domain)[0] }}
                                </span>
                            </div>

                            @if ($project->framework === 'node')
                                {{-- Info khusus framework Node: build = deploy, run = manual via terminal --}}
                                <div class="mx-6 mt-3 rounded-xl border border-amber-200 bg-amber-50 dark:border-amber-500/30 dark:bg-amber-500/10 p-4">
                                    <div class="flex items-center gap-2 text-amber-700 dark:text-amber-300 font-semibold text-xs uppercase tracking-wider mb-2">
                                        <i class="fa-brands fa-node-js"></i> Framework Node.js
                                    </div>
                                    <p class="text-[11px] leading-relaxed text-amber-700/90 dark:text-amber-200/80 mb-3">
                                        Deploy hanya menjalankan <span class="font-mono font-semibold">npm install</span> +
                                        <span class="font-mono font-semibold">npm run build</span>.
                                        Server Node <span class="font-semibold">tidak dijalankan otomatis</span> — jalankan sendiri lewat Terminal
                                        agar setup custom project-mu tetap berlaku dan tidak tersandung 502 Bad Gateway.
                                    </p>
                                    <div class="rounded-lg bg-[#0A1322] border border-[#2A4463] px-3 py-2.5 font-mono text-[11px] leading-relaxed text-[#C9D5E2] space-y-1">
                                        <div><span class="text-emerald-400">ryaze-start</span> <span class="text-[#8A9BAE]">[entry]</span> <span class="text-[#525252] dark:text-white/40"># port acak otomatis → domain utama</span></div>
                                        <div><span class="text-emerald-400">ryaze-stop</span> <span class="text-[#525252] dark:text-white/40"># matikan server</span></div>
                                        <div><span class="text-emerald-400">ryaze-status</span> <span class="text-[#525252] dark:text-white/40"># cek hidup/mati</span></div>
                                        <div><span class="text-emerald-400">ryaze-log</span> <span class="text-[#525252] dark:text-white/40"># tail log server (.app.log)</span></div>
                                    </div>
                                    <p class="text-[11px] leading-relaxed text-amber-700/90 dark:text-amber-200/80 mt-2.5">
                                        <span class="font-mono">ryaze-start</span> tanpa argumen akan auto-detect
                                        (<span class="font-mono">ecosystem.config.js</span> → <span class="font-mono">npm start</span> →
                                        <span class="font-mono">package.json main</span> → <span class="font-mono">server.js</span>/<span class="font-mono">index.js</span>/<span class="font-mono">app.js</span>).
                                        Bisa juga entry subfolder: <span class="font-mono">ryaze-start server/start.js</span>.
                                        Server berjalan detached — tetap hidup walau Terminal ditutup.
                                    </p>
                                </div>
                            @endif

                            <form action="{{ route('user_hosting.redeploy', $project->hashid) }}" method="POST"
                                class="px-6 py-4">
                                @csrf
                                <button type="submit"
                                    class="ui-btn ui-btn-md ui-btn-primary w-full">
                                    <i class="fa-solid fa-rotate"></i> Redeploy Sekarang
                                </button>
                            </form>

                        </div>
                    </div>

{{-- Konfigurasi Nginx Custom --}}
                        @php
                        $d = $project->ryaze_domain;
                        $s = explode('.', $d)[0];
                        $defaultNginxConf = str_replace(
                            ['__DOMAIN__', '__SUBDOMAIN__'],
                            [$d, $s],
                            <<<'NGINX_CONF'
server {
    listen 80;
    listen [::]:80;
    server_name __DOMAIN__;

    include mime.types;

    set $dynamic_root /www/sites/hosting_clients/__SUBDOMAIN__;
    if (-f /www/sites/hosting_clients/__SUBDOMAIN__/public/index.php) {
        set $dynamic_root /www/sites/hosting_clients/__SUBDOMAIN__/public;
    }
    if (-f /www/sites/hosting_clients/__SUBDOMAIN__/public/index.html) {
        set $dynamic_root /www/sites/hosting_clients/__SUBDOMAIN__/public;
    }
    if (-f /www/sites/hosting_clients/__SUBDOMAIN__/dist/index.html) {
        set $dynamic_root /www/sites/hosting_clients/__SUBDOMAIN__/dist;
    }
    if (-f /www/sites/hosting_clients/__SUBDOMAIN__/build/index.html) {
        set $dynamic_root /www/sites/hosting_clients/__SUBDOMAIN__/build;
    }

    root $dynamic_root;
    index index.php index.html index.htm;

    location ^~ /.well-known/acme-challenge {
        allow all;
        root /www/letsencrypt;
    }

    set_by_lua_block $app_port {
        local subdomain = "__SUBDOMAIN__"
        local file_path = "/www/sites/hosting_clients/" .. subdomain .. "/.port"
        local file = io.open(file_path, "r")
        if file then
            local port = file:read("*l")
            file:close()
            if port then
                port = port:gsub("%s+", "")
                if port ~= "" then
                    return port
                end
            end
        end
        return ""
    }

    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;

    error_page 418 = @app_proxy;

    location / {
        if ($app_port != "") {
            return 418;
        }
        try_files $uri $uri/ @framework_fallback;
    }

    location @framework_fallback {
        if (-f $document_root/index.php) {
            rewrite ^ /index.php?$query_string last;
        }
        if (-f $document_root/index.html) {
            rewrite ^ /index.html last;
        }
        return 404;
    }

    location ~ \.php$ {
        if ($app_port != "") {
            return 418;
        }
        try_files $uri =404;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTP_HOST $host;
    }

    location ~ .*\.(js|css|png|jpg|jpeg|gif|ico|bmp|swf|eot|svg|ttf|woff|woff2)$ {
        if ($app_port != "") {
            return 418;
        }
        try_files $uri $uri/ @framework_fallback;
        expires 30d;
        log_not_found off;
    }

    location @app_proxy {
        proxy_pass http://127.0.0.1:$app_port;
    }
}
NGINX_CONF
                        );
                        $nginxStatus = $project->nginx_status;
                    // PHP hanya memutuskan MAKNA (varian + ikon + label);
                    // kelasnya diambil dari design-system lewat <x-ui.badge>.
                    // Kalau warna ditulis manual di sini, setiap status baru
                    // wajib=dict warna baru lagi, dan hasilnya pasti tidak konsisten.
                    $nginxBadge = match ($nginxStatus) {
                        'pending' => ['warning', 'fa-clock', 'Diproses'],
                        'applied' => ['success', 'fa-circle-check', 'Aktif'],
                        'failed'  => ['danger',  'fa-circle-xmark', 'Gagal'],
                        'reset'   => ['neutral', 'fa-rotate-left', 'Default'],
                        default   => ['neutral', 'fa-minus', 'Belum diatur'],
                    };
                    @endphp
                    <div class="ui-panel mt-4">
                        <div class="ui-panel-head">
                            <h3 class="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC] flex items-center gap-2">
                                <i class="fa-solid fa-server"></i> Konfigurasi Nginx (OpenResty)
                            </h3>
                            <x-ui.badge :variant="$nginxBadge[0]" :icon="'fa-solid ' . $nginxBadge[1] . ($nginxStatus === 'pending' ? ' fa-spin' : '')"
                                class="ml-auto text-[10px] uppercase tracking-wider">
                                {{ $nginxBadge[2] }}
                            </x-ui.badge>
                        </div>
                        <div class="p-6">
                        <p class="text-xs text-[#525252] dark:text-white/50 mb-3">
                            Sesuaikan config server untuk
                            <code class="ui-code text-[10px]">{{ $d }}</code>.
                            Config diverifikasi <code class="ui-code text-[10px]">nginx -t</code> otomatis;
                            jika invalid, config lama langsung dipulihkan.
                        </p>

                        @if($project->nginx_status === 'failed' && $project->nginx_error)
                            <div class="mb-3 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/40 p-3">
                                <p class="text-[11px] font-bold text-rose-700 dark:text-rose-300 mb-1 flex items-center gap-1.5">
                                    <i class="fa-solid fa-triangle-exclamation"></i> nginx -t menolak config terakhir:
                                </p>
                                <pre class="text-[10px] text-rose-600 dark:text-rose-300 whitespace-pre-wrap break-all font-mono max-h-28 overflow-y-auto">{{ $project->nginx_error }}</pre>
                            </div>
                        @endif

                        <form action="{{ route('user_hosting.nginx.update', $project->hashid) }}" method="POST">
                            @csrf
                            <textarea name="nginx_config" rows="12" spellcheck="false"
                                class="ui-field font-mono text-[11px] leading-relaxed bg-[#0A1322] text-emerald-300 border-[#2A4463] focus:ring-[#1D5B96]/30 resize-y"
                                placeholder="# Contoh: server {&#10;    listen 80;&#10;    server_name {{ $d }};&#10;    ...&#10;}">{{ $project->nginx_custom }}</textarea>
                            <div class="flex flex-wrap items-center gap-2 mt-3">
                                <button type="submit"
                                    class="ui-btn ui-btn-md ui-btn-primary">
                                    <i class="fa-solid fa-paper-plane"></i> Simpan &amp; Terapkan
                                </button>
                                <button type="button" onclick="document.getElementById('nginx_example').classList.toggle('hidden')"
                                    class="ui-btn ui-btn-md ui-btn-secondary">
                                    <i class="fa-solid fa-eye"></i> Contoh Config
                                </button>
                                <button type="button" onclick="copyNginxExample()"
                                    class="ui-btn ui-btn-md ui-btn-secondary">
                                    <i class="fa-solid fa-copy"></i> Salin Contoh
                                </button>
                            </div>
                        </form>

                        <form action="{{ route('user_hosting.nginx.reset', $project->hashid) }}" method="POST" class="mt-2"
                            onsubmit="event.preventDefault(); let f = this; swConfirm('Reset Nginx?', 'Konfigurasi Nginx akan dikembalikan ke default.').then(res => { if(res.isConfirmed) f.submit(); }); return false;">
                            @csrf
                            <button type="submit"
                                class="ui-btn ui-btn-danger w-full">
                                <i class="fa-solid fa-rotate-left"></i> Kembalikan ke Default
                            </button>
                        </form>

                        <div id="nginx_example" class="hidden mt-3">
                            <pre id="nginx_example_content" class="text-[10px] font-mono text-[#C9D5E2] dark:text-white/50 bg-[#0A1322] border border-[#2A4463] p-3 whitespace-pre-wrap break-all max-h-72 overflow-y-auto">{{ $defaultNginxConf }}</pre>
                        </div>
                        </div>
                    </div>

                    {{-- QR Code Scan --}}
                    <div class="ui-card p-6 mt-4 text-center">
                        <h3 class="ui-eyebrow text-[11px] mb-4 border-b pb-2 text-sm flex items-center justify-center gap-2">
                            <i class="fa-solid fa-qrcode text-[#1D5B96] dark:text-[#6BA8DC]"></i> Scan QR Code
                        </h3>
                        <div class="flex justify-center">
                            <div class="p-2 bg-white dark:bg-[#0F1D30] border border-[#EDF2F7] dark:border-[#2A4463] shadow-none inline-block">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode('https://' . $displayUrl) }}" alt="QR Code" class="w-32 h-32" />
                            </div>
                        </div>
                        <p class="text-xs text-[#525252] dark:text-white/50 mt-3">Scan untuk membuka di HP Anda</p>
                    </div>

                    {{-- Resource Monitoring --}}
                    <div class="ui-card p-6 mt-4">
                        <h3 class="ui-eyebrow text-[11px] mb-4 border-b pb-2 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-chart-pie text-[#1D5B96] dark:text-[#6BA8DC]"></i> Statistik Penggunaan
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-[#F4F8FC] dark:bg-[#0F1D30] border border-[#EDF2F7] dark:border-[#2A4463] p-3">
                                <span class="block text-[#525252] dark:text-white/50 text-[10px] uppercase font-bold tracking-wider mb-1">Disk Usage</span>
                                <div class="flex items-end gap-2">
                                    <i class="fa-solid fa-hard-drive text-[#8A9BAE] dark:text-white/40 text-lg mb-0.5"></i>
                                    <span class="font-bold text-[#0F1A2A] dark:text-white text-lg">{{ $diskUsage ?? '0 MB' }}</span>
                                </div>
                            </div>
                            <div class="bg-[#F4F8FC] dark:bg-[#0F1D30] border border-[#EDF2F7] dark:border-[#2A4463] p-3">
                                <span class="block text-[#525252] dark:text-white/50 text-[10px] uppercase font-bold tracking-wider mb-1">Total Visitor</span>
                                <div class="flex items-end gap-2">
                                    <i class="fa-solid fa-users text-emerald-400 dark:text-emerald-300 text-lg mb-0.5"></i>
                                    <span class="font-bold text-[#0F1A2A] dark:text-white text-lg">{{ $visitorsCount ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-[10px] text-[#8A9BAE] dark:text-white/40 mt-3 flex items-center gap-1.5"><i class="fa-solid fa-circle-info"></i> Data visitor dan disk diupdate secara realtime.</p>
                    </div>
                </div>
            </div>

            {{-- CPU & RAM Usage Chart --}}
            <div class="ui-card mt-6 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="ui-eyebrow text-[11px] text-sm flex items-center gap-2">
                        <i class="fa-solid fa-microchip text-[#1D5B96] dark:text-[#6BA8DC]"></i> Server Resource Usage (Last 24h)
                    </h3>
                    <div class="text-xs text-[#525252] dark:text-white/50 flex items-center gap-2">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-[#1D5B96]"></span> CPU</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-400"></span> RAM</span>
                    </div>
                </div>
                <div id="resourceChart" class="w-full h-[250px]"></div>
            </div>
        </div>

        {{-- TAB: BUILD LOGS --}}
        <div id="panel-logs" class="tab-panel hidden">
            <div class="bg-[#0A1322] shadow-none border border-[#2A4463] overflow-hidden">
                <div
                    class="bg-[#0F1D30] px-4 py-3 flex flex-col sm:flex-row items-start sm:items-center gap-3 border-b border-[#2A4463]">
                    <div class="flex items-center gap-2 text-[#8A9BAE] dark:text-white/40 text-xs">
                        <i class="fa-solid fa-globe"></i>
                        <a id="website-log-link" href="https://{{ $project->ryaze_domain }}" target="_blank"
                            class="text-[#6BA8DC] hover:text-[#7FB4E2] truncate">
                            {{ $project->ryaze_domain }}
                        </a>
                    </div>
                    <div class="text-[#8A9BAE] dark:text-white/40 text-xs">
                        Status: <span id="build-log-status"
                            class="font-semibold text-[#E3EAF1]">{{ $project->status }}</span>
                    </div>
                    <div class="text-[#8A9BAE] dark:text-white/40 text-xs ml-auto">
                        <span
                            id="build-log-updated">{{ $project->deployments->first()?->created_at?->diffForHumans() ?? 'Initial Build' }}</span>
                    </div>
                </div>
                <div class="p-4 h-[500px] overflow-y-auto font-mono text-sm" id="build-log-container">
                    @if ($project->deployments->count() > 0)
                        <pre id="build-log-text" class="text-emerald-400 dark:text-emerald-300 whitespace-pre-wrap leading-relaxed">{{ $project->deployments->first()->build_logs }}</pre>
                        @if ($project->status == 'building')
                            <div id="build-log-pulse" class="mt-2 flex items-center text-[#8A9BAE] dark:text-white/40 animate-pulse">
                                <span class="mr-2">></span>
                                <span class="w-2 h-4 bg-[#C9D5E2] inline-block animate-ping"></span>
                            </div>
                        @endif
                    @else
                        <p class="text-[#525252] dark:text-white/50">Belum ada log deployment.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- TAB: TERMINAL --}}
        <div id="panel-terminal" class="tab-panel hidden">
            <div class="bg-[#0A1322] border border-[#2A4463] overflow-hidden shadow-none relative group">
                {{-- Terminal header --}}
                <div class="bg-[#0F1D30]/40 backdrop-blur-md border-b border-[#2A4463] px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex gap-1.5 group/dots">
                            <div class="w-3 h-3 rounded-full bg-rose-500/80 shadow-none group-hover/dots:bg-rose-500 transition-colors"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-500/80 shadow-none group-hover/dots:bg-amber-500 transition-colors"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500/80 shadow-none hover:bg-emerald-400 cursor-pointer transition-colors" data-action="clear-terminal" title="Clear"></div>
                        </div>
                        <div class="h-4 w-px bg-[#2A4463]/60"></div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-terminal text-emerald-400/80 text-xs shrink-0"></i>
                            <span class="text-[#8A9BAE]/80 text-[11px] font-mono tracking-wide truncate" id="terminal-cwd-display">
                                /{{ explode('.', $project->ryaze_domain)[0] }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button data-action="clear-terminal" class="text-[#525252] dark:text-white/50 hover:text-rose-400 transition-colors text-xs opacity-0 group-hover:opacity-100 duration-300" title="Clear Terminal (Ctrl+L)">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>

                {{-- Terminal output --}}
                <div id="terminal-output"
                    class="px-5 pt-5 pb-3 font-mono text-sm text-[#C9D5E2] dark:text-white/50 overflow-y-auto leading-relaxed cursor-text selection:bg-emerald-500/30 transition-all duration-300 scrollbar-thin scrollbar-thumb-slate-700 scrollbar-track-transparent"
                    style="height:420px;background:radial-gradient(circle at center, #12233A 0%, #0A1322 100%);" data-action="focus-terminal">
                    <div id="terminal-welcome" class="mb-5 select-none">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/20 mb-3 shadow-none">
                            <i class="fa-solid fa-terminal text-emerald-400/80 text-xs"></i>
                            <span class="text-emerald-400 dark:text-emerald-300 font-bold tracking-widest text-[10px] uppercase">Ryaze Cloud Terminal</span>
                        </div>
                        <div class="text-[#8A9BAE] text-xs flex items-center gap-2 font-sans tracking-wide">
                            Connected to <span class="text-[#C9D5E2] dark:text-white/50 font-medium bg-[#0F1D30]/50 px-2 py-0.5 rounded border border-[#2A4463]">{{ $project->project_name }}</span>
                        </div>
                        <div class="mt-2 text-[10px] leading-relaxed text-[#525252] dark:text-white/40 font-mono">
                            <span class="text-emerald-400/80">ryaze-start</span> jalankan server Node (port acak) &nbsp;·&nbsp;
                            <span class="text-emerald-400/80">ryaze-stop</span> matikan server &nbsp;·&nbsp;
                            <span class="text-emerald-400/80">ryaze-status</span> cek status &nbsp;·&nbsp;
                            <span class="text-emerald-400/80">ryaze-log</span> log server
                        </div>
                        <div class="w-full h-px bg-gradient-to-r from-emerald-500/20 via-[#2A4463]/20 to-transparent mt-4"></div>
                    </div>
                </div>

                {{-- Terminal input --}}
                <div class="flex items-center bg-[#0A1322] border-t border-[#2A4463] px-5 py-3.5 gap-3 relative overflow-hidden focus-within:bg-[#0F1D30] transition-colors duration-300 group/input">
                    <div class="absolute left-0 top-0 w-[3px] h-full bg-emerald-500/80 transform scale-y-0 group-focus-within/input:scale-y-100 transition-transform duration-300 ease-out origin-bottom shadow-none"></div>
                    <span id="terminal-prompt" class="font-mono text-sm font-bold select-none shrink-0 flex items-center gap-2">
                        <span class="text-[#6BA8DC]">{{ $project->ryaze_domain }}</span>
                        <i class="fa-solid fa-angle-right text-emerald-400/80 text-xs"></i>
                    </span>
                    <input type="text" id="terminal-input" autocomplete="off" autocorrect="off" autocapitalize="off"
                        spellcheck="false" placeholder="Enter command..."
                        class="flex-1 bg-transparent text-[#E3EAF1] font-mono text-sm outline-none placeholder-[#8A9BAE]/50 caret-emerald-400 min-w-0">
                    <button data-action="run-command"
                        class="ui-icon-btn ui-icon-btn-primary w-8 h-8 bg-[#12233A] hover:bg-[#1D5B96] text-white duration-300 shadow-none group/btn">
                        <i class="fa-solid fa-arrow-turn-down -rotate-90 text-xs group-hover/btn:translate-x-0.5 transition-transform"></i>
                    </button>
                </div>
            </div>
            
            <div class="flex items-center justify-between mt-4 px-1">
                <p class="text-[11px] text-[#8A9BAE] flex items-center gap-2 font-sans">
                    <i class="fa-solid fa-circle-info text-[#525252] dark:text-white/70"></i>
                    <span>Pro tip: Use <kbd class="bg-[#0F1D30]/80 border border-[#2A4463] text-[#C9D5E2] dark:text-white/50 px-1.5 py-0.5 rounded shadow-none text-[10px] mx-0.5 font-mono">↑</kbd> <kbd class="bg-[#0F1D30]/80 border border-[#2A4463] text-[#C9D5E2] dark:text-white/50 px-1.5 py-0.5 rounded shadow-none text-[10px] mx-0.5 font-mono">↓</kbd> for history and <kbd class="bg-[#0F1D30]/80 border border-[#2A4463] text-[#C9D5E2] dark:text-white/50 px-1.5 py-0.5 rounded shadow-none text-[10px] mx-0.5 font-mono">Ctrl+L</kbd> to clear</span>
                </p>
                <div class="flex items-center gap-1.5 text-[9px] font-mono text-[10px] uppercase tracking-[0.2em] px-2 py-1 text-emerald-400 bg-[#0A1322] border border-[#2A4463] shadow-none">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse shadow-none"></span> Session Active
                </div>
            </div>
        </div>

        {{-- TAB: FILE MANAGER --}}
        <div id="panel-files" class="tab-panel hidden relative">
            <div class="ui-card overflow-hidden relative">
                <div
                    class="px-4 py-3 border-b border-[#EDF2F7] dark:border-[#2A4463] bg-[#F4F8FC] dark:bg-[#0F1D30] flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2 min-w-0">
                        {{-- Semua tombol pakai data-action --}}
                        <button data-action="navigate-up"
                            class="text-[#525252] dark:text-white/50 hover:text-[#2A6FAD] dark:hover:text-[#6BA8DC] dark:hover:text-[#6BA8DC] transition-colors bg-white dark:bg-[#0F1D30] px-2 py-1.5 rounded border border-[#E3EAF1] dark:border-[#2A4463] shadow-none shrink-0"
                            title="Kembali">
                            <i class="fa-solid fa-level-up-alt fa-flip-horizontal"></i>
                        </button>
                        <div
                            class="text-sm font-mono text-[#525252] dark:text-white/70 bg-white dark:bg-[#0F1D30] px-3 py-1.5 rounded border border-[#E3EAF1] dark:border-[#2A4463] truncate max-w-xs">
                            <i class="fa-solid fa-server text-[#8A9BAE] dark:text-white/40 mr-1"></i>/<span id="current-path-display"
                                class="text-[#1D5B96] dark:text-[#6BA8DC] font-bold"></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button data-action="new-file"
                            class="ui-btn ui-btn-sm ui-btn-ghost">
                            <i class="fa-solid fa-file-circle-plus text-emerald-500 dark:text-emerald-400 mr-1"></i><span
                                class="hidden sm:inline">New File</span>
                        </button>
                        <button data-action="paste-file" id="fm-btn-paste"
                            class="hidden text-xs bg-[#E8F0F9] dark:bg-[#12233A] border border-[#C9D5E2] dark:border-[#2A4463] text-[#1D5B96] dark:text-[#6BA8DC] px-3 py-1.5 rounded hover:bg-[#E8F0F9] dark:hover:bg-[#1A2F49] transition-colors font-semibold">
                            <i class="fa-solid fa-paste mr-1"></i><span class="hidden sm:inline">Paste</span>
                        </button>
                        <button data-action="new-dir"
                            class="ui-btn ui-btn-sm ui-btn-ghost">
                            <i class="fa-solid fa-folder-plus text-amber-500 dark:text-amber-400 mr-1"></i><span class="hidden sm:inline">New
                                Folder</span>
                        </button>
                        <label class="ui-btn ui-btn-sm ui-btn-primary cursor-pointer">
                            <i class="fa-solid fa-cloud-arrow-up mr-1"></i><span class="hidden sm:inline">Upload</span>
                            {{-- onchange diganti data-action --}}
                            <input type="file" id="upload-input" class="hidden" data-action="upload-file">
                        </label>
                        <button data-action="refresh-files"
                            class="ui-btn ui-btn-sm ui-btn-ghost">
                            <i class="fa-solid fa-rotate-right"></i>
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto h-[500px] relative">
                    {{-- min-w: tanpa ini kolom "Aksi" (6 ikon) memaksa tabel melebar
                         melebihi container, lalu scroll horizontal ikut menggeser kolom
                         Nama sampai terpotong di sisi kiri. --}}
                    <table class="w-full min-w-[720px] text-sm text-left text-[#525252] dark:text-white/70 table-fixed">
                        <thead
                            class="bg-white dark:bg-[#0F1D30] text-xs uppercase font-semibold text-[#8A9BAE] dark:text-white/40 border-b border-[#EDF2F7] dark:border-[#2A4463] sticky top-0 z-10 shadow-none">
                            <tr>
                                <th class="px-6 py-3">Nama</th>
                                <th class="px-4 py-3 w-20 sm:w-24">Ukuran</th>
                                <th class="px-4 py-3 hidden md:table-cell w-28 lg:w-36">Diubah</th>
                                <th class="px-4 py-3 w-36 sm:w-44 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="file-manager-body" class="divide-y divide-[#EDF2F7] font-mono text-[13px]"></tbody>
                    </table>
                    <div id="file-manager-loader"
                        class="hidden absolute inset-0 bg-white/80 dark:bg-[#0F1D30]/80 flex items-center justify-center z-20">
                        <i class="fa-solid fa-circle-notch fa-spin text-3xl text-[#1D5B96] dark:text-[#6BA8DC]"></i>
                    </div>
                </div>

                {{-- File Editor Modal --}}
                <div id="file-editor-modal" class="hidden absolute inset-0 bg-[#0A1322] z-30 flex flex-col">
                    <div
                        class="px-4 py-3 border-b border-[#2A4463] bg-[#0F1D30] flex justify-between items-center text-white">
                        <div class="font-mono text-sm flex items-center gap-2 min-w-0">
                            <i class="fa-solid fa-file-code text-[#6BA8DC] shrink-0"></i>
                            <span id="editor-filename" class="truncate">filename.php</span>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            {{-- data-action menggantikan onclick --}}
                            <button data-action="close-editor"
                                class="px-3 py-1.5 bg-[#2A4463] hover:bg-[#2A4463] rounded text-xs transition font-semibold">Batal</button>
                            <button data-action="save-editor"
                                class="ui-btn ui-btn-sm ui-btn-primary">
                                <i class="fa-solid fa-save"></i> Simpan
                            </button>
                        </div>
                    </div>
                    <div id="monaco-editor-container" class="flex-1 min-w-0 w-full bg-[#0A1322] relative z-0"></div>
                    <textarea id="file-editor-textarea" spellcheck="false" class="hidden flex-1 min-w-0 w-full bg-[#0A1322] text-emerald-400 dark:text-emerald-300 font-mono text-sm p-4 outline-none resize-none leading-relaxed"></textarea>
                    <div id="editor-loader"
                        class="hidden absolute inset-0 bg-[#0A1322]/80 flex items-center justify-center z-40">
                        <i class="fa-solid fa-circle-notch fa-spin text-3xl text-[#1D5B96] dark:text-[#6BA8DC]"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB: IDE VS CODE --}}
        <div id="panel-ide" class="tab-panel hidden">
            <div id="ide-shell" class="flex h-[650px] bg-[#0A1322] overflow-hidden shadow-none border border-[#2A4463]">
                
                <!-- Activity Bar -->
                <div class="w-12 bg-[#12233A] flex flex-col items-center py-2 shrink-0 border-r border-[#0A1322] z-10">
                    <button class="ide-activity-btn ui-ide-activity ui-ide-activity-active" data-target="ide-sidebar-explorer" title="Explorer">
                        <i class="fa-regular fa-copy text-xl"></i>
                    </button>
                    <button class="ide-activity-btn ui-ide-activity" data-target="ide-sidebar-search" title="Search">
                        <i class="fa-solid fa-magnifying-glass text-xl"></i>
                    </button>
                    <button class="ide-activity-btn ui-ide-activity" data-target="ide-sidebar-extensions" title="Extensions">
                        <i class="fa-solid fa-cubes text-xl"></i>
                    </button>
                    <div class="mt-auto mb-2">
                        <button class="ide-activity-btn ui-ide-activity" data-target="ide-sidebar-settings" title="Settings / Themes">
                            <i class="fa-solid fa-gear text-xl"></i>
                        </button>
                        <button id="ide-ai-activity-btn" class="ide-activity-btn ui-ide-activity" data-target="ide-right-chat" title="Ryaze AI">
                            <i class="fa-brands fa-galactic-senate text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Left Sidebar (collapsible) -->
                <div id="ide-left-sidebar" class="w-64 bg-[#12233A] border-r border-[#2A4463] flex flex-col shrink-0 relative overflow-hidden transition-all duration-150">
                    <button id="ide-collapse-left" title="Collapse Sidebar (Ctrl+B)"
                        class="absolute top-2 right-2 z-30 w-6 h-6 flex items-center justify-center rounded hover:bg-[#0F1D30] text-[#525252] dark:text-white/50 hover:text-white transition-colors">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </button>
                    
                    <!-- Explorer View -->
                    <div id="ide-sidebar-explorer" class="ide-sidebar-view flex flex-col h-full">
                        <div class="px-4 py-3 text-[11px] font-bold text-[#8A9BAE] dark:text-white/40 uppercase tracking-wider border-b border-[#2A4463] flex justify-between items-center pr-10">
                            <span class="flex items-center gap-2"><i class="fa-solid fa-folder-open text-[#6BA8DC]"></i> Explorer</span>
                            <div class="flex gap-2">
                                <button data-action="ide-new-file" class="hover:text-white transition-colors" title="New File"><i class="fa-solid fa-file-medical"></i></button>
                                <button data-action="ide-new-dir" class="hover:text-white transition-colors" title="New Folder"><i class="fa-solid fa-folder-plus"></i></button>
                                <button data-action="ide-paste" id="ide-btn-paste" class="hidden text-[#6BA8DC] hover:text-[#7FB4E2] transition-colors" title="Paste"><i class="fa-solid fa-paste"></i></button>
                                <button data-action="ide-refresh" class="hover:text-white transition-colors" title="Refresh"><i class="fa-solid fa-rotate-right"></i></button>
                                <button data-action="ide-collapse" class="hover:text-white transition-colors" title="Collapse All"><i class="fa-solid fa-compress"></i></button>
                            </div>
                        </div>
                        <div class="px-3 py-2 bg-[#12233A] text-[#C9D5E2] text-xs font-mono border-b border-[#2A4463] flex items-center gap-2 truncate">
                            <i class="fa-solid fa-folder text-amber-500 dark:text-amber-400 shrink-0"></i>
                            <span class="truncate">{{ $project->ryaze_domain }}</span>
                        </div>
                        <div id="ide-sidebar-tree" class="flex-1 min-w-0 overflow-y-auto text-sm text-[#C9D5E2] py-2 font-mono" style="font-size: 13px;">
                            <!-- JS akan merender tree file -->
                        </div>
                    </div>

                    <!-- Search View -->
                    <div id="ide-sidebar-search" class="ide-sidebar-view hidden flex flex-col h-full">
                        <div class="px-4 py-3 text-[11px] font-bold text-[#8A9BAE] dark:text-white/40 uppercase tracking-wider border-b border-[#2A4463]">
                            <span>Search</span>
                        </div>
                        <div class="p-3">
                            <input type="text" id="ide-search-input" class="w-full bg-[#12233A] text-[#E3EAF1] border border-[#12233A] focus:border-[#1D5B96] rounded text-xs p-1.5 outline-none placeholder-[#8A9BAE] mb-2" placeholder="Search (Enter)">
                            <div class="text-[10px] text-[#525252] dark:text-white/50 flex gap-2">
                                <label class="flex items-center gap-1 cursor-pointer hover:text-[#E3EAF1]">
                                    <input type="checkbox" id="ide-search-case" class="accent-[#1D5B96] bg-[#12233A]"> Match Case
                                </label>
                            </div>
                        </div>
                        <div id="ide-search-results" class="flex-1 min-w-0 overflow-y-auto px-2 py-2 text-xs font-mono">
                            <div class="text-[#525252] dark:text-white/50 text-center mt-10">Ketik dan tekan Enter untuk mencari</div>
                        </div>
                    </div>

                    <!-- Settings / Themes View -->
                    <div id="ide-sidebar-settings" class="ide-sidebar-view hidden flex flex-col h-full">
                        <div class="px-4 py-3 text-[11px] font-bold text-[#8A9BAE] dark:text-white/40 uppercase tracking-wider border-b border-[#2A4463]">
                            <span>Settings</span>
                        </div>
                        <div class="p-4">
                            <label class="block text-xs font-semibold text-[#C9D5E2] dark:text-white/50 mb-2">Color Theme</label>
                            <select id="ide-theme-selector" class="w-full bg-[#12233A] text-[#E3EAF1] border border-[#12233A] rounded text-xs p-2 outline-none focus:border-[#1D5B96]">
                                <option value="vs-dark">Dark+ (default dark)</option>
                                <option value="vs">Light+ (default light)</option>
                                <option value="hc-black">High Contrast</option>
                                <option value="one-dark-pro">One Dark Pro</option>
                                <option value="dracula">Dracula</option>
                            </select>
                        </div>
                    </div>

                    <!-- Extensions View -->
                    <div id="ide-sidebar-extensions" class="ide-sidebar-view hidden flex flex-col h-full">
                        <div class="px-4 py-3 text-[11px] font-bold text-[#8A9BAE] dark:text-white/40 uppercase tracking-wider border-b border-[#2A4463]">
                            <span>Extensions</span>
                        </div>
                        <div class="p-4 flex-1 min-w-0 overflow-y-auto">
                            <p class="text-[10px] text-[#525252] dark:text-white/50 mb-3 leading-relaxed">Ekstensi tema — klik "Terapkan" untuk langsung mengubah tema editor.</p>
                            <div class="mb-4 flex gap-3 items-start">
                                <div class="w-10 h-10 rounded bg-[#282c34] border border-[#3e4451] shrink-0"></div>
                                <div class="w-full">
                                    <p class="text-sm text-[#E3EAF1] font-semibold leading-tight">One Dark Pro</p>
                                    <p class="text-[10px] text-[#8A9BAE] dark:text-white/40 mb-2">Theme · Atom One Dark</p>
                                    <button class="ui-btn ui-btn-sm ui-btn-primary w-full" onclick="applyIdeTheme('one-dark-pro', this)">Terapkan</button>
                                </div>
                            </div>
                            <div class="mb-4 flex gap-3 items-start">
                                <div class="w-10 h-10 rounded bg-[#282a36] border border-[#44475a] shrink-0"></div>
                                <div class="w-full">
                                    <p class="text-sm text-[#E3EAF1] font-semibold leading-tight">Dracula</p>
                                    <p class="text-[10px] text-[#8A9BAE] dark:text-white/40 mb-2">Theme · Dracula Official</p>
                                    <button class="ui-btn ui-btn-sm ui-btn-primary w-full" onclick="applyIdeTheme('dracula', this)">Terapkan</button>
                                </div>
                            </div>
                            <div class="mb-4 flex gap-3 items-start">
                                <div class="w-10 h-10 rounded bg-[#0A1322] border border-[#2A4463] shrink-0"></div>
                                <div class="w-full">
                                    <p class="text-sm text-[#E3EAF1] font-semibold leading-tight">Dark+ (default)</p>
                                    <p class="text-[10px] text-[#8A9BAE] dark:text-white/40 mb-2">Theme · Bawaan Monaco</p>
                                    <button class="ui-btn ui-btn-sm ui-btn-primary w-full" onclick="applyIdeTheme('vs-dark', this)">Terapkan</button>
                                </div>
                            </div>
                            <div class="mb-4 flex gap-3 items-start">
                                <div class="w-10 h-10 rounded bg-[#ffffff] border border-[#2A4463] shrink-0"></div>
                                <div class="w-full">
                                    <p class="text-sm text-[#E3EAF1] font-semibold leading-tight">Light+ (default)</p>
                                    <p class="text-[10px] text-[#8A9BAE] dark:text-white/40 mb-2">Theme · Bawaan Monaco</p>
                                    <button class="ui-btn ui-btn-sm ui-btn-primary w-full" onclick="applyIdeTheme('vs', this)">Terapkan</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ryaze AI Chat View -->
                    <div id="ide-sidebar-chat" class="ide-sidebar-view hidden flex flex-col h-full">
                        <div class="px-4 py-3 text-[11px] font-bold text-[#8A9BAE] dark:text-white/40 uppercase tracking-wider border-b border-[#2A4463]">
                            <i class="fa-brands fa-galactic-senate text-[#6BA8DC] mr-1"></i> <span>Ryaze AI v2.0</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center justify-center gap-3 text-[#525252] dark:text-white/50 p-6 text-center">
                            <i class="fa-brands fa-galactic-senate text-4xl text-[#1D5B96]/40"></i>
                            <p class="text-xs leading-relaxed">Ryaze AI sudah dipindah ke panel kanan.<br>Klik ikon <i class="fa-brands fa-galactic-senate text-[#C9D5E2] dark:text-white/50"></i> di bawah activity bar.</p>
                        </div>
                    </div>

                </div>
                <!-- Editor -->
                <div class="flex-1 flex flex-col relative bg-[#0A1322] min-w-0">
                    <div id="ide-tabs-bar" class="h-9 bg-[#12233A] flex items-stretch overflow-x-auto scrollbar-hide border-b border-[#0A1322] shrink-0">
                        <div class="flex items-center gap-2 px-4 text-xs text-[#525252] dark:text-white/50 font-mono"><i class="fa-solid fa-folder-open text-amber-500/70"></i> Ryaze IDE</div>
                    </div>
                    <div class="h-10 bg-[#12233A] flex items-center px-4 border-b border-[#0A1322] shrink-0 justify-between">
                        <div class="flex items-center gap-2 text-sm text-[#C9D5E2] font-mono min-w-0">
                            <i class="fa-solid fa-file-code text-[#6BA8DC] shrink-0"></i>
                            <span id="ide-current-filename" class="truncate">Pilih file...</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <button id="ide-panel-toggle" title="Toggle Panel (Ctrl+J)" class="text-[#525252] dark:text-white/50 hover:text-white transition-colors w-7 h-7 flex items-center justify-center rounded hover:bg-[#12233A]">
                                <i class="fa-solid fa-terminal text-sm"></i>
                            </button>
                            <button id="ide-zen-btn" title="Fullscreen (F11)" class="text-[#525252] dark:text-white/50 hover:text-white transition-colors w-7 h-7 flex items-center justify-center rounded hover:bg-[#12233A]">
                                <i class="fa-solid fa-expand text-sm"></i>
                            </button>
                            <button id="ide-popout-btn" title="Buka di jendela baru" class="text-[#525252] dark:text-white/50 hover:text-white transition-colors w-7 h-7 flex items-center justify-center rounded hover:bg-[#12233A]">
                                <i class="fa-solid fa-arrow-up-right-from-square text-sm"></i>
                            </button>
                            <button id="ide-save-btn" data-action="ide-save" class="ui-btn ui-btn-sm ui-btn-primary hidden">
                                <i class="fa-solid fa-save"></i> Simpan
                            </button>
                        </div>
                    </div>
                    <div id="ide-monaco-container" class="flex-1 min-w-0 relative w-full bg-[#0A1322]">
                        <div id="ide-empty-state" class="absolute inset-0 flex items-center justify-center flex-col gap-4 opacity-30">
                            <i class="fa-solid fa-laptop-code text-7xl text-[#525252] dark:text-white/50"></i>
                            <p class="text-[#8A9BAE] dark:text-white/40 font-mono">Pilih file dari explorer untuk mengedit</p>
                        </div>
                    </div>
                    <div id="ide-loader" class="hidden absolute inset-0 bg-[#0A1322]/80 flex items-center justify-center z-10">
                        <i class="fa-solid fa-circle-notch fa-spin text-3xl text-[#1D5B96] dark:text-[#6BA8DC]"></i>
                    </div>

                    <!-- Bottom Panel (VS Code-style) -->
                    <div id="ide-bottom-panel" class="h-56 bg-[#0A1322] border-t border-[#2A4463] flex flex-col shrink-0 hidden">
                        <div class="h-9 bg-[#12233A] border-b border-[#0A1322] flex items-stretch shrink-0 overflow-x-auto scrollbar-hide">
                            <button data-panel-tab="problems" class="ui-ide-tab ide-panel-tab">
                                <i class="fa-solid fa-circle-exclamation text-[10px]"></i> Problems
                                <span id="problems-count" class="font-mono text-[10px] border border-rose-500/40 text-rose-300 px-1.5 hidden">0</span>
                            </button>
                            <button data-panel-tab="output" class="ui-ide-tab ide-panel-tab">
                                <i class="fa-solid fa-rectangle-list text-[10px]"></i> Output
                            </button>
                            <button data-panel-tab="debug" class="ui-ide-tab ide-panel-tab">
                                <i class="fa-solid fa-bug text-[10px]"></i> Debug Console
                            </button>
                            <button data-panel-tab="terminal" class="ui-ide-tab ide-panel-tab">
                                <i class="fa-solid fa-terminal text-[10px]"></i> Terminal
                            </button>
                            <button data-panel-tab="ports" class="ui-ide-tab ide-panel-tab">
                                <i class="fa-solid fa-plug text-[10px]"></i> Ports
                            </button>
                            <div class="ml-auto flex items-center gap-0.5 px-1.5">
                                <button id="ide-term-new" title="New Terminal" class="ide-panel-action w-7 h-7 flex items-center justify-center rounded hover:bg-[#12233A] text-[#525252] dark:text-white/50 hover:text-white transition-colors"><i class="fa-solid fa-plus text-[11px]"></i></button>
                                <button id="ide-term-split" title="Split Terminal" class="ide-panel-action w-7 h-7 flex items-center justify-center rounded hover:bg-[#12233A] text-[#525252] dark:text-white/50 hover:text-white transition-colors"><i class="fa-solid fa-table-columns text-[11px]"></i></button>
                                <button id="ide-term-kill" title="Kill Terminal" class="ide-panel-action w-7 h-7 flex items-center justify-center rounded hover:bg-[#12233A] text-[#525252] dark:text-white/50 hover:text-white transition-colors"><i class="fa-solid fa-trash-can text-[11px]"></i></button>
                                <button id="ide-term-send-chat" title="Send Terminal to Chat" class="ide-panel-action w-7 h-7 flex items-center justify-center rounded hover:bg-[#12233A] text-[#525252] dark:text-white/50 hover:text-white transition-colors"><i class="fa-brands fa-galactic-senate text-[11px]"></i></button>
                                <button id="ide-panel-hide" title="Hide Panel" class="ide-panel-action w-7 h-7 flex items-center justify-center rounded hover:bg-[#12233A] text-[#525252] dark:text-white/50 hover:text-white transition-colors"><i class="fa-solid fa-chevron-down text-[11px]"></i></button>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0 relative overflow-hidden">
                            <div id="ide-panel-problems" class="ide-panel-view absolute inset-0 overflow-y-auto hidden"></div>
                            <div id="ide-panel-output" class="ide-panel-view absolute inset-0 overflow-y-auto hidden"></div>
                            <div id="ide-panel-debug" class="ide-panel-view absolute inset-0 hidden flex flex-col"></div>
                            <div id="ide-panel-terminal" class="ide-panel-view absolute inset-0 hidden flex flex-col">
                                <div id="ide-term-tabs" class="h-8 bg-[#0A1322] border-b border-[#2A4463] flex items-center gap-1 px-2 overflow-x-auto scrollbar-hide shrink-0"></div>
                                <div id="ide-term-area" class="flex-1 flex flex-col min-h-0"></div>
                            </div>
                            <div id="ide-panel-ports" class="ide-panel-view absolute inset-0 overflow-y-auto hidden"></div>
                        </div>
                    </div>
                </div>

                <!-- Right Panel: Ryaze AI (collapsible) -->
                <div id="ide-right-panel" class="w-80 bg-[#12233A] border-l border-[#2A4463] flex flex-col shrink-0 relative overflow-hidden transition-all duration-150">
                    <div id="ide-right-chat" class="ide-sidebar-view flex flex-col h-full relative">
                        <div class="px-4 py-3 text-[11px] font-bold text-[#8A9BAE] dark:text-white/40 uppercase tracking-wider border-b border-[#2A4463] flex justify-between items-center shrink-0">
                            <span class="flex items-center gap-2">
                                <i class="fa-brands fa-galactic-senate text-[#6BA8DC]"></i> Ryaze AI v2.0
                                <span class="text-[8px] bg-[#1D5B96]/20 text-[#6BA8DC] px-1.5 py-0.5 rounded font-semibold">GPT-OSS 120B</span>
                            </span>
                            <div class="flex items-center gap-1.5">
                                <button id="ide-chat-new" title="New Chat" class="text-[#8A9BAE] dark:text-white/40 hover:text-white transition-colors w-6 h-6 flex items-center justify-center rounded hover:bg-[#0F1D30]"><i class="fa-solid fa-plus text-[10px]"></i></button>
                                <button id="ide-chat-history" title="Riwayat Percakapan" class="text-[#525252] dark:text-white/50 hover:text-white transition-colors w-6 h-6 flex items-center justify-center rounded hover:bg-[#0F1D30]"><i class="fa-solid fa-clock-rotate-left text-[10px]"></i></button>
                                <button id="ide-chat-clear" title="Hapus percakapan ini" class="ui-icon-btn ui-icon-btn-danger w-6 h-6 hover:bg-[#0F1D30]"><i class="fa-solid fa-trash-can text-[10px]"></i></button>
                                <button id="ide-collapse-right" title="Tutup Panel AI" class="text-[#525252] dark:text-white/50 hover:text-white transition-colors w-6 h-6 flex items-center justify-center rounded hover:bg-[#0F1D30]"><i class="fa-solid fa-chevron-right text-[10px]"></i></button>
                            </div>
                        </div>
                        <div id="ide-chat-list" class="hidden absolute top-11 right-2 left-2 z-40 bg-[#12233A] border border-[#2A4463] shadow-none overflow-hidden max-h-64 overflow-y-auto"></div>
                        <div id="grok-chat-messages" class="flex-1 overflow-y-auto p-3 text-sm flex flex-col gap-3 font-sans">
                            <div class="bg-[#0F1D30] text-[#E3EAF1] p-2 self-start max-w-[90%] text-xs leading-relaxed">
                                Halo! Saya <b>Ryaze AI v2.0</b>. Bisa analisis bug, generate kode, atau <i>edit file project langsung</i> (buat, ubah, rename, hapus, append). Konteks file yang sedang dibuka otomatis terbaca.
                            </div>
                        </div>
                        <div class="px-3 pt-2 flex gap-1.5 flex-wrap shrink-0">
                            <button class="ide-chat-chip text-[9px] bg-[#0F1D30] hover:bg-[#1D5B96]/30 hover:text-white border-[#24384F]">Perbaiki kode</button>
                            <button class="ide-chat-chip text-[9px] bg-[#0F1D30] hover:bg-[#1D5B96]/30 hover:text-white border-[#24384F]">Analisis bug</button>
                            <button class="ide-chat-chip text-[9px] bg-[#0F1D30] hover:bg-[#1D5B96]/30 hover:text-white border-[#24384F]">Buat file</button>
                            <button class="ide-chat-chip text-[9px] bg-[#0F1D30] hover:bg-[#1D5B96]/30 hover:text-white border-[#24384F]">Jelaskan</button>
                        </div>
                        <div class="p-3 border-t border-[#2A4463] bg-[#12233A] shrink-0">
                            <div id="grok-chat-form" class="flex flex-col gap-2">
                                <textarea id="grok-chat-input" rows="2" class="w-full bg-[#12233A] text-white text-xs px-3 py-2 rounded outline-none border border-[#24384F] focus:border-[#1D5B96] resize-none" placeholder="Tanya Ryaze AI... (Enter untuk kirim, Shift+Enter untuk baris baru)"></textarea>
                                <button type="button" id="grok-chat-send-btn" class="ui-btn ui-btn-sm ui-btn-primary">
                                    <i class="fa-solid fa-paper-plane"></i> Kirim
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB: ENV --}}
        <div id="panel-env" class="tab-panel hidden">
            <div class="ui-card overflow-hidden max-w-3xl">
                <div class="px-6 py-4 border-b border-[#EDF2F7] dark:border-[#2A4463]">
                    <h3 class="ui-eyebrow text-[11px] flex items-center gap-2">
                        <i class="fa-solid fa-key text-amber-500 dark:text-amber-400"></i> Environment Variables
                    </h3>
                    <p class="text-xs text-[#525252] dark:text-white/50 mt-1">Format: <code
                            class="bg-[#F4F8FC] dark:bg-[#12233A] px-1 py-0.5 rounded text-rose-500 dark:text-rose-400">KUNCI=nilai</code>. Perubahan berlaku
                        setelah redeploy.</p>
                </div>
                <form action="{{ route('user_hosting.env.update', $project->hashid) }}" method="POST">
                    @csrf
                    <div class="bg-[#0A1322] border-b border-[#2A4463] p-1">
                        <textarea name="env_content" rows="18"
                            class="w-full bg-transparent text-emerald-400 dark:text-emerald-300 font-mono text-sm p-4 focus:outline-none resize-y"
                            placeholder="API_KEY=rahasia&#10;DB_HOST=127.0.0.1" spellcheck="false">{{ old('env_content', $envContent) }}</textarea>
                    </div>
                    <div class="px-6 py-4 bg-[#F4F8FC] dark:bg-[#0F1D30] flex justify-end">
                        <button type="submit"
                            class="ui-btn ui-btn-md ui-btn-primary">
                            <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan .env
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- TAB: SETTINGS --}}
        <div id="panel-settings" class="tab-panel hidden space-y-6">
            <div class="ui-card overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E3EAF1] dark:border-[#2A4463] bg-[#F4F8FC] dark:bg-[#0F1D30]">
                    <h3 class="ui-eyebrow text-[11px]">Konfigurasi Aplikasi</h3>
                    <p class="text-xs text-[#525252] dark:text-white/50">Atur parameter dasar environment project.</p>
                </div>
                <form action="{{ route('user_hosting.settings.update', $project->hashid) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="p-6 space-y-5">
                        <div
                            class="flex items-center justify-between p-4 border border-[#EDF2F7] dark:border-[#2A4463] bg-[#F4F8FC] dark:bg-[#0F1D30]/50">
                            <div>
                                <h4 class="text-sm font-semibold text-[#0F1A2A] dark:text-white/80">Maintenance Mode</h4>
                                <p class="text-xs text-[#525252] dark:text-white/50 mt-0.5">Tampilkan halaman "Under Maintenance" ke
                                    pengunjung.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" name="maintenance_mode" value="1" class="sr-only peer"
                                    {{ $project->maintenance_mode ?? false ? 'checked' : '' }}>
                                <div
                                    class="peer ui-switch ui-switch-warning">
                                </div>
                            </label>
                        </div>
                        <div
                            class="flex items-center justify-between p-4 border border-[#EDF2F7] dark:border-[#2A4463] bg-[#F4F8FC] dark:bg-[#0F1D30]/50">
                            <div>
                                <h4 class="text-sm font-semibold text-[#0F1A2A] dark:text-white/80">Force HTTPS</h4>
                                <p class="text-xs text-[#525252] dark:text-white/50 mt-0.5">Redirect semua traffic HTTP ke HTTPS secara
                                    otomatis.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" name="force_https" value="1" class="sr-only peer"
                                    {{ $project->force_https ?? true ? 'checked' : '' }}>
                                <div
                                    class="peer ui-switch ui-switch-success">
                                </div>
                            </label>
                        </div>
                        <div
                            class="flex items-center justify-between p-4 border border-rose-100 dark:border-rose-500/30 bg-rose-50/50 dark:bg-rose-500/10">
                            <div>
                                <h4 class="text-sm font-semibold text-rose-700 dark:text-rose-300">DDoS Protection (Rate Limit)</h4>
                                <p class="text-xs text-[#525252] dark:text-white/50 mt-0.5">Aktifkan limitasi koneksi ketat jika website Anda sedang diserang.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" name="is_under_attack" value="1" class="sr-only peer"
                                    {{ $project->is_under_attack ?? false ? 'checked' : '' }}>
                                <div
                                    class="peer ui-switch ui-switch-danger">
                                </div>
                            </label>
                        </div>
                        <div class="p-4 border border-[#EDF2F7] dark:border-[#2A4463] bg-[#F4F8FC] dark:bg-[#0F1D30]/50">
                            <div class="mb-3">
                                <h4 class="text-sm font-semibold text-[#0F1A2A] dark:text-white/80">Blokir IP (WAF)</h4>
                                <p class="text-xs text-[#525252] dark:text-white/50 mt-0.5">Masukkan daftar alamat IP yang ingin diblokir (satu IP per baris). Pengunjung dengan IP ini akan mendapatkan pesan 403 Forbidden.</p>
                            </div>
                            <textarea name="blocked_ips" rows="4" class="ui-field text-[#0F1A2A] dark:text-white/80 font-mono focus:outline-none" placeholder="192.168.1.1&#10;10.0.0.5" spellcheck="false">{{ old('blocked_ips', $wafContent) }}</textarea>
                        </div>
                    </div>
                    <div class="bg-[#F4F8FC] dark:bg-[#0F1D30] px-6 py-3 border-t border-[#E3EAF1] dark:border-[#2A4463] flex justify-end">
                        <button type="submit"
                            class="ui-btn ui-btn-md ui-btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Backup & Restore --}}
            <div class="ui-card overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E3EAF1] dark:border-[#2A4463] bg-[#F4F8FC] dark:bg-[#0F1D30]">
                    <h3 class="ui-eyebrow text-[11px] flex items-center gap-2">
                        <i class="fa-solid fa-box-archive text-[#1D5B96] dark:text-[#6BA8DC]"></i> Backup & Restore
                    </h3>
                    <p class="text-xs text-[#525252] dark:text-white/50 mt-1">Buat salinan proyek Anda atau pulihkan dari file zip.</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border border-[#E3EAF1] dark:border-[#2A4463] p-5 bg-[#F4F8FC] dark:bg-[#0F1D30]/50">
                        <h4 class="ui-eyebrow text-[11px] /80 mb-2">Buat Backup (Download)</h4>
                        <p class="text-xs text-[#525252] dark:text-white/50 mb-4">Fitur ini akan mengompresi seluruh file source code aplikasi Anda ke dalam format .zip dan langsung diunduh ke komputer Anda.</p>
                        <a href="{{ route('user_hosting.backup.download', $project->hashid) }}" class="inline-flex items-center gap-2 bg-[#0F1D30] hover:bg-[#0A1322] text-white font-semibold py-2 px-4 text-sm transition-colors w-full justify-center">
                            <i class="fa-solid fa-download"></i> Download Backup .ZIP
                        </a>
                    </div>
                    <div class="border border-[#E3EAF1] dark:border-[#2A4463] p-5 bg-[#F4F8FC] dark:bg-[#0F1D30]/50">
                        <h4 class="ui-eyebrow text-[11px] /80 mb-2">Restore Backup (Upload)</h4>
                        <p class="text-xs text-[#525252] dark:text-white/50 mb-4">Pilih file .zip untuk mengekstrak dan menimpa (overwrite) file yang ada saat ini di server Anda. Harap berhati-hati.</p>
                        <form action="{{ route('user_hosting.backup.upload', $project->hashid) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="flex gap-2">
                                <input type="file" name="backup_file" accept=".zip" required class="block w-full text-xs text-[#525252] dark:text-white/50 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-xs file:font-semibold file:bg-[#E8F0F9] file:text-[#2568A8] hover:file:bg-[#E8F0F9] border border-[#E3EAF1] dark:border-[#2A4463] cursor-pointer bg-white dark:bg-[#0F1D30]">
                                <button type="submit" class="ui-btn ui-btn-md ui-btn-primary shrink-0">
                                    <i class="fa-solid fa-upload"></i> Restore
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="ui-card border-rose-200 dark:border-rose-500/40 overflow-hidden">
                <div class="px-6 py-4 border-b border-rose-100 dark:border-rose-500/30 bg-rose-50/50 dark:bg-rose-500/10">
                    <h3 class="font-bold text-rose-600 dark:text-rose-300 flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation"></i> Danger Zone
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-[#525252] dark:text-white/70 mb-5">Tindakan di bawah bersifat destruktif dan tidak dapat
                        dibatalkan.</p>
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between p-4 border border-rose-100 dark:border-rose-500/30 bg-rose-50/30 dark:bg-rose-500/10 gap-4">
                        <div>
                            <h4 class="ui-eyebrow text-[11px] text-sm">Hapus Proyek</h4>
                            <p class="text-xs text-[#525252] dark:text-white/50 mt-0.5">Menghapus folder root, DNS Cloudflare, dan semua
                                record secara permanen.</p>
                        </div>
                        <form id="delete-form" action="{{ route('user_hosting.destroy', $project->hashid) }}"
                            method="POST" class="shrink-0">
                            @csrf @method('DELETE')
                            {{-- data-action menggantikan onclick --}}
                            <button type="button" data-action="confirm-delete"
                                class="ui-btn ui-btn-md ui-btn-danger w-full sm:w-auto">
                                <i class="fa-solid fa-trash-can"></i> Hapus Permanen
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        {{-- TAB: CRON JOBS --}}
        {{-- TAB: EMAIL --}}
        {{--
        <div id="panel-email" class="tab-panel hidden space-y-6">
            <div class="ui-card overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E3EAF1] dark:border-[#2A4463] bg-[#F4F8FC] dark:bg-[#0F1D30] flex justify-between items-center">
                    <div>
                        <h3 class="ui-eyebrow text-[11px]">Kelola Email</h3>
                        <p class="text-xs text-[#525252] dark:text-white/50">Buat email profesional dengan domain project ini.</p>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('user_hosting.emails.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        @csrf
                        <div class="md:col-span-1">
                            <label class="block text-xs font-bold text-[#0F1A2A] dark:text-white/80 mb-1.5">Alamat Email</label>
                            <div class="flex items-stretch">
                                <input type="text" name="prefix" placeholder="admin" required
                                    class="transition-all w-1/2 bg-[#F4F8FC] dark:bg-[#0F1D30] border border-[#E3EAF1] dark:border-[#2A4463]-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1D5B96]/20 focus:border-[#1D5B96] outline-none">
                                <span class="px-3 flex items-center border-y border-[#E3EAF1] dark:border-[#2A4463] bg-[#F4F8FC] dark:bg-[#12233A] text-[#525252] dark:text-white/50 text-sm font-bold">@</span>
                                <select name="domain" required class="transition-all w-1/2 bg-[#F4F8FC] dark:bg-[#0F1D30] border border-l-0 border-[#E3EAF1] dark:border-[#2A4463]-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1D5B96]/20 focus:border-[#1D5B96] outline-none">
                                    <option value="{{ $project->ryaze_domain }}">{{ $project->ryaze_domain }}</option>
                                    @foreach($project->domains as $d)
                                        <option value="{{ $d->domain_name }}">{{ $d->domain_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-xs font-bold text-[#0F1A2A] dark:text-white/80 mb-1.5">Password</label>
                            <input type="password" name="password" placeholder="Min. 8 karakter" required minlength="8"
                                class="ui-field">
                        </div>
                        <div class="md:col-span-1">
                            <button type="submit" class="ui-btn ui-btn-md ui-btn-primary w-full">
                                Tambah Email
                            </button>
                        </div>
                    </form>

                    <div class="mt-6 border border-[#E3EAF1] dark:border-[#2A4463] overflow-x-auto">
                        <table class="w-full text-sm text-left text-[#525252] dark:text-white/50 whitespace-nowrap min-w-[600px]">
                            <thead class="text-xs text-[#0F1A2A] dark:text-white/80 uppercase bg-[#F4F8FC] dark:bg-[#0F1D30] border-b border-[#E3EAF1] dark:border-[#2A4463]">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Alamat Email</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Quota</th>
                                    <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($projectEmails ?? [] as $email)
                                    <tr class="bg-white dark:bg-[#0F1D30] border-b border-[#EDF2F7] dark:border-[#2A4463] hover:bg-[#E8F0F9] dark:hover:bg-[#1A2F49]">
                                        <td class="px-6 py-4 font-semibold text-[#0F1A2A] dark:text-white">{{ $email->email_address }}</td>
                                        <td class="px-6 py-4">
                                            <span class="ui-badge ui-badge-success">Active</span>
                                        </td>
                                        <td class="px-6 py-4 text-xs font-medium text-[#0F1A2A] dark:text-white/80">{{ $email->quota_mb }} MB</td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end items-center gap-3">
                                                <a href="{{ rtrim(env('POSTE_IO_URL', 'https://mail.ryaze.my.id'), '/') }}/webmail" target="_blank" class="text-[#1D5B96] dark:text-[#6BA8DC] hover:text-[#2A6FAD] dark:hover:text-[#6BA8DC] dark:hover:text-[#7FB4E2] text-xs font-bold">
                                                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Webmail
                                                </a>
                                                <form action="{{ route('user_hosting.emails.destroy', $email->hashid) }}" method="POST" class="inline" onsubmit="event.preventDefault(); let f = this; swConfirm('Hapus Email?', 'Apakah Anda yakin ingin menghapus akun email ini?').then(res => { if(res.isConfirmed) f.submit(); }); return false;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-rose-600 dark:text-rose-300 hover:text-rose-800 dark:hover:text-rose-300 text-xs font-bold"><i class="fa-solid fa-trash"></i> Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-[#525252] dark:text-white/50">Belum ada akun email untuk project ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        --}}


        <div id="panel-crons" class="tab-panel hidden space-y-6">
            <div class="ui-card overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E3EAF1] dark:border-[#2A4463] bg-[#F4F8FC] dark:bg-[#0F1D30] flex justify-between items-center">
                    <div>
                        <h3 class="ui-eyebrow text-[11px]">Cron Jobs</h3>
                        <p class="text-xs text-[#525252] dark:text-white/50">Jadwalkan eksekusi command background.</p>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('user_hosting.crons.store', $project->hashid) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        @csrf
                        <div class="md:col-span-1">
                            <label class="block text-xs font-bold text-[#0F1A2A] dark:text-white/80 mb-1.5">Command</label>
                            <input type="text" name="command" placeholder="php artisan schedule:run" required
                                class="ui-field">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-xs font-bold text-[#0F1A2A] dark:text-white/80 mb-1.5">Schedule (Cron Expr)</label>
                            <input type="text" name="schedule_expression" placeholder="* * * * *" required value="* * * * *"
                                class="ui-field font-mono">
                        </div>
                        <div class="md:col-span-1">
                            <button type="submit" class="ui-btn ui-btn-md ui-btn-primary w-full">
                                Tambah Cron
                            </button>
                        </div>
                    </form>

                    <div class="mt-6 border border-[#E3EAF1] dark:border-[#2A4463] overflow-x-auto">
                        <table class="w-full text-sm text-left text-[#525252] dark:text-white/50 whitespace-nowrap min-w-[600px]">
                            <thead class="text-xs text-[#0F1A2A] dark:text-white/80 uppercase bg-[#F4F8FC] dark:bg-[#0F1D30] border-b border-[#E3EAF1] dark:border-[#2A4463]">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Command</th>
                                    <th scope="col" class="px-6 py-3">Schedule</th>
                                    <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($project->crons as $cron)
                                    <tr class="bg-white dark:bg-[#0F1D30] border-b border-[#EDF2F7] dark:border-[#2A4463] hover:bg-[#E8F0F9] dark:hover:bg-[#1A2F49]">
                                        <td class="px-6 py-4 font-mono text-xs text-[#0F1A2A] dark:text-white">{{ $cron->command }}</td>
                                        <td class="px-6 py-4 font-mono text-xs">{{ $cron->schedule_expression }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <form action="{{ route('user_hosting.crons.destroy', $cron->hashid) }}" method="POST" onsubmit="event.preventDefault(); let f = this; swConfirm('Hapus Cron Job?', 'Apakah Anda yakin ingin menghapus cron job ini?').then(res => { if(res.isConfirmed) f.submit(); }); return false;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-rose-600 dark:text-rose-300 hover:text-rose-800 dark:hover:text-rose-300 text-xs font-bold"><i class="fa-solid fa-trash"></i> Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-[#525252] dark:text-white/50">Belum ada cron job yang didaftarkan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB: DOMAINS --}}
        <div id="panel-domains" class="tab-panel hidden space-y-6">
            <div class="ui-card overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E3EAF1] dark:border-[#2A4463] bg-[#F4F8FC] dark:bg-[#0F1D30] flex flex-wrap justify-between items-center gap-3">
                    <div>
                        <h3 class="ui-eyebrow text-[11px]">Domain &amp; Subdomain</h3>
                        <p class="text-xs text-[#525252] dark:text-white/50">Semua hostname yang melayani project ini. Kelola daftarnya di halaman Domains.</p>
                    </div>
                    <a href="{{ route('user_hosting.domains.index') }}"
                        class="ui-btn ui-btn-md ui-btn-primary">
                        <i class="fa-solid fa-globe"></i>
                        Buka Halaman Domains
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#F4F8FC] dark:bg-[#0F1D30] border-b border-[#E3EAF1] dark:border-[#2A4463] text-[#525252] dark:text-white/50 text-xs uppercase tracking-wider font-semibold">
                                <th class="px-6 py-4">Domain</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Redirect</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E3EAF1] dark:divide-[#2A4463] text-sm">
                            @php
                                // Semua jenis hostname bisa dilayani project ini:
                                // custom domain maupun subdomain. Jangan saring
                                // berdasarkan cf_zone_id — subdomain memang tidak punya zone.
                                $linkedDomains = $project->domains()->with(['parent', 'redirect'])->latest()->get();
                                $redirectStatuses = \App\Models\HostingDomainRedirect::statusOptions();
                            @endphp
                            @forelse($linkedDomains as $d)
                                @php
                                    $isActiveDomain = $d->ssl_status === 'active';
                                @endphp
                                <tr class="hover:bg-[#E8F0F9] dark:hover:bg-[#1A2F49] transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-[#0F1A2A] dark:text-white flex items-center gap-2">
                                            <i class="fa-solid {{ $d->isChild() ? 'fa-sitemap' : 'fa-globe' }} {{ $d->isChild() ? 'text-[#2A6FAD] dark:text-[#6BA8DC]' : 'text-[#1D5B96] dark:text-[#6BA8DC]' }} text-xs"></i>
                                            {{ $d->domain_name }}
                                        </span>
                                        @if ($d->isChild())
                                            <span class="block text-[11px] text-[#8A9BAE] dark:text-white/40 mt-0.5">
                                                {{ \App\Models\HostingDomain::TYPE_PLATFORM === $d->type ? 'Subdomain platform' : 'Subdomain' }}
                                                @if ($d->parent)
                                                    &middot; dari {{ $d->parent->domain_name }}
                                                @endif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($isActiveDomain)
                                            <span class="ui-badge ui-badge-success inline-block">Aktif</span>
                                        @else
                                            <span class="ui-badge ui-badge-warning inline-block">Menunggu NS</span>
                                        @endif
                                    </td>
                                    {{-- Redirect to Another Domain (ala Vercel) --}}
                                    <td class="px-6 py-4" x-data="{ open: {{ $d->redirect ? 'true' : 'false' }} }">
                                        @if ($d->redirect)
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold uppercase{{ $d->redirect->isPermanent() ? 'ui-badge ui-badge-accent' : 'ui-badge ui-badge-info' }}">
                                                    {{ $d->redirect->kindLabel() }} ({{ $d->redirect->status_code }})
                                                </span>
                                                <span class="text-xs text-[#525252] dark:text-white/70 font-mono break-all">{{ $d->redirect->target }}</span>
                                            </div>
                                        @else
                                            <button type="button" x-on:click="open = true"
                                                class="ui-chip">
                                                <i class="fa-solid fa-arrow-right-arrow-left text-[10px]"></i> Redirect
                                            </button>
                                        @endif

                                        <div x-show="open" x-cloak class="mt-3 p-3 border border-[#E3EAF1] dark:border-[#2A4463] bg-[#F4F8FC] dark:bg-[#0F1D30]/40">
                                            <p class="text-xs font-semibold text-[#0F1A2A] dark:text-white/80 mb-2">Redirect to Another Domain</p>

                                            <form action="{{ route('user_hosting.domains.redirect.store', $d->hashid) }}" method="POST" class="space-y-2">
                                                @csrf
                                                <div>
                                                    <label class="block text-[11px] font-medium text-[#525252] dark:text-white/50 mb-1" for="target_{{ $d->id }}">Destination URL</label>
                                                    <input type="url" name="target" id="target_{{ $d->id }}" required
                                                           placeholder="https://toko-baru.com"
                                                           value="{{ old('target', $d->redirect->target ?? '') }}"
                                                           class="w-full bg-white dark:bg-[#0F1D30] border border-[#E3EAF1] dark:border-[#2A4463] px-3 py-2 text-xs text-[#0F1A2A] dark:text-white/80 focus:ring-2 focus:ring-[#1D5B96]/20 focus:border-[#1D5B96] outline-none transition">
                                                </div>
                                                <div>
                                                    <label class="block text-[11px] font-medium text-[#525252] dark:text-white/50 mb-1" for="status_{{ $d->id }}">Type</label>
                                                    <select name="status_code" id="status_{{ $d->id }}"
                                                            class="w-full bg-white dark:bg-[#0F1D30] border border-[#E3EAF1] dark:border-[#2A4463] px-3 py-2 text-xs text-[#0F1A2A] dark:text-white/80 focus:ring-2 focus:ring-[#1D5B96]/20 focus:border-[#1D5B96] outline-none transition">
                                                        @foreach ($redirectStatuses as $code => $label)
                                                            <option value="{{ $code }}" @selected((int) old('status_code', $d->redirect->status_code ?? 308) === $code)>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <p class="text-[10px] text-[#525252] dark:text-white/50 leading-relaxed">
                                                    <i class="fa-solid fa-circle-info mr-1"></i>
                                                    Permanent ikut ter-cache browser, jadi hanya pakai kalau domain lama benar-benar dipindah permanen.
                                                </p>
                                                <div class="flex items-center gap-2 pt-1">
                                                    <button type="submit" class="ui-btn ui-btn-sm ui-btn-primary">
                                                        <i class="fa-solid fa-check mr-1"></i> Simpan
                                                    </button>
                                                    @if ($d->redirect)
                                                        <button type="button" x-on:click="open = false"
                                                            class="ui-btn ui-btn-sm ui-btn-secondary">
                                                            Batal
                                                        </button>
                                                    @endif
                                                </div>
                                            </form>

                                            @if ($d->redirect)
                                                <form action="{{ route('user_hosting.domains.redirect.destroy', $d->hashid) }}" method="POST" class="pt-2 mt-2 border-t border-[#E3EAF1] dark:border-[#2A4463]"
                                                      onsubmit="event.preventDefault(); let f = this; swConfirm('Hapus redirect?', 'Domain ini akan dilayani normal lagi.').then(r => { if(r.isConfirmed) f.submit(); }); return false;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="ui-btn ui-btn-sm ui-btn-danger">
                                                        <i class="fa-solid fa-trash mr-1"></i> Hapus Redirect
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('user_hosting.domains.show', $d->hashid) }}"
                                                class="ui-chip">Kelola</a>
                                            <form action="{{ route('user_hosting.domains.detach', $d->hashid) }}" method="POST" class="inline"
                                                  onsubmit="event.preventDefault(); let f = this; swConfirm('Lepas dari project?', 'Domain tidak dilayani sampai disambungkan lagi.').then(r => { if(r.isConfirmed) f.submit(); }); return false;">
                                                @csrf
                                                <button type="submit" class="ui-btn ui-btn-sm ui-btn-danger">Lepas</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <i class="fa-solid fa-globe text-2xl text-[#C9D5E2] dark:text-white/30 mb-2"></i>
                                        <p class="text-sm text-[#525252] dark:text-white/50">Project ini belum punya custom domain.</p>
                                        <p class="text-xs text-[#8A9BAE] dark:text-white/40 mt-1">Tambahkan domain dulu di halaman Domains, lalu sambungkan ke project ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @php
                $freeDomains = \App\Models\HostingDomain::where('user_id', Auth::id())
                    ->whereNull('project_id')
                    ->where('ssl_status', 'active')
                    ->get();
            @endphp
            @if($freeDomains->isNotEmpty() && $project->user_id == Auth::id())
            <div class="ui-card overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E3EAF1] dark:border-[#2A4463] bg-[#F4F8FC] dark:bg-[#0F1D30]">
                    <h3 class="ui-eyebrow text-[11px]">Hostname Tersedia Milik Anda</h3>
                    <p class="text-xs text-[#525252] dark:text-white/50">Domain atau subdomain aktif yang belum dipakai project mana pun. Satu klik untuk menyambungkannya ke project ini.</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($freeDomains as $fd)
                        <form action="{{ route('user_hosting.domains.assign', $fd->hashid) }}" method="POST"
                              class="flex items-center gap-3 border border-[#E3EAF1] dark:border-[#2A4463] px-4 py-3 hover:border-[#C9D5E2] dark:hover:border-[#1D5B96] transition">
                            @csrf
                            <input type="hidden" name="project_id" value="{{ $project->id }}">
                            <i class="fa-solid {{ $fd->isChild() ? 'fa-sitemap text-[#2A6FAD] dark:text-[#6BA8DC]' : 'fa-globe text-[#1D5B96] dark:text-[#6BA8DC]' }}"></i>
                            <span class="flex-1 min-w-0 text-sm font-semibold text-[#0F1A2A] dark:text-white truncate">{{ $fd->domain_name }}</span>
                            <button type="submit" class="ui-btn ui-btn-sm ui-btn-primary">Sambungkan</button>
                        </form>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- TAB: TEAM ACCESS --}}
        <div id="panel-team" class="tab-panel hidden space-y-6">
            <div class="ui-card overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E3EAF1] dark:border-[#2A4463] bg-[#F4F8FC] dark:bg-[#0F1D30] flex justify-between items-center">
                    <div>
                        <h3 class="ui-eyebrow text-[11px]">Team Access & Collaborators</h3>
                        <p class="text-xs text-[#525252] dark:text-white/50">Undang anggota tim lain untuk berkolaborasi dalam proyek ini.</p>
                    </div>
                </div>
                <div class="p-6">
                    @if($project->user_id == Auth::id())
                    <form action="{{ route('user_hosting.team.invite', $project->hashid) }}" method="POST" class="flex flex-col sm:flex-row gap-4">
                        @csrf
                        <div class="flex-1 min-w-0 w-full sm:w-auto">
                            <input type="email" name="email" placeholder="Alamat email anggota baru" required
                                class="ui-field">
                        </div>
                        <select name="role" class="ui-field sm:w-auto">
                            <option value="viewer">Viewer (Hanya Lihat)</option>
                            <option value="editor">Editor (Bisa Ubah)</option>
                        </select>
                        <button type="submit" class="ui-btn ui-btn-md ui-btn-primary w-full sm:w-auto">
                            Undang Anggota
                        </button>
                    </form>
                    @else
                    <div class="bg-[#E8F0F9] dark:bg-[#12233A] text-[#1D5B96] dark:text-[#6BA8DC] p-4 text-sm mb-4">
                        <i class="fa-solid fa-info-circle mr-2"></i> Hanya pemilik project yang dapat mengundang anggota tim baru.
                    </div>
                    @endif

                    <div class="mt-6 border border-[#E3EAF1] dark:border-[#2A4463] overflow-x-auto">
                        <table class="w-full text-sm text-left text-[#525252] dark:text-white/50 whitespace-nowrap min-w-[600px]">
                            <thead class="text-xs text-[#0F1A2A] dark:text-white/80 uppercase bg-[#F4F8FC] dark:bg-[#0F1D30] border-b border-[#E3EAF1] dark:border-[#2A4463]">
                                <tr>
                                    <th scope="col" class="px-6 py-3">User</th>
                                    <th scope="col" class="px-6 py-3">Role</th>
                                    <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($project->teamMembers as $member)
                                    <tr class="bg-white dark:bg-[#0F1D30] border-b border-[#EDF2F7] dark:border-[#2A4463] hover:bg-[#E8F0F9] dark:hover:bg-[#1A2F49]">
                                        <td class="px-6 py-4 font-semibold text-[#0F1A2A] dark:text-white">
                                            {{ $member->name }} <br><span class="text-xs font-normal text-[#525252] dark:text-white/50">{{ $member->email }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($member->pivot->role == 'editor')
                                                <span class="ui-badge ui-badge-primary">Editor</span>
                                            @else
                                                <span class="ui-badge ui-badge-neutral">Viewer</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @if($project->user_id == Auth::id())
                                            <form action="{{ route('user_hosting.team.remove', [$project->hashid, $member->id]) }}" method="POST" onsubmit="event.preventDefault(); let f = this; swConfirm('Cabut Akses?', 'Apakah Anda yakin ingin mencabut akses pengguna ini?').then(res => { if(res.isConfirmed) f.submit(); }); return false;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="ui-btn ui-btn-sm ui-btn-danger"><i class="fa-solid fa-user-xmark"></i> Cabut Akses</button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-[#525252] dark:text-white/50">Belum ada anggota tim yang diundang.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>




    {{-- SweetAlert2 --}}
    <script nonce="{{ csp_nonce() }}" src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- ── SCRIPT 1: SweetAlert helpers ─────────────────────────────────────── --}}
    <script nonce="{{ csp_nonce() }}">
                function swAlert(icon, title, text = '') {
            return Swal.fire({
                icon,
                title,
                text,
                confirmButtonColor: '#1D5B96',
                customClass: {
                    popup: 'text-sm'
                }
            });
        }

        function swConfirm(title, text, icon = 'warning') {
            return Swal.fire({
                title,
                text,
                icon,
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#8A9BAE',
                confirmButtonText: 'Ya, lanjutkan',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'text-sm'
                }
            });
        }

        function swInput(title, inputPlaceholder) {
            return Swal.fire({
                title,
                input: 'text',
                inputPlaceholder,
                showCancelButton: true,
                confirmButtonColor: '#1D5B96',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'text-sm'
                },
                inputValidator: (v) => {
                    if (!v) return 'Nama tidak boleh kosong!';
                }
            });
        }

        function confirmDelete() {
            Swal.fire({
                title: 'Hapus Proyek Permanen?',
                text: 'Semua file server, database, dan record DNS akan dihapus. Ini tidak bisa kembali!',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#8A9BAE',
                confirmButtonText: 'Ya, Hapus Sekarang!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'text-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('delete-form').submit();
            });
        }
    </script>

    {{-- ── SCRIPT 2: Helpers & Build Log polling ────────────────────────────── --}}
    <script nonce="{{ csp_nonce() }}">
        function copyNginxExample() {
            var el = document.getElementById('nginx_example_content');
            if (!el) return;
            var text = el.textContent;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'success', title: 'Tersalin!', text: 'Config contoh disalin ke clipboard.', timer: 1500, showConfirmButton: false });
                    } else {
                        hotToast('Config contoh disalin ke clipboard.', 'success');
                    }
                });
            } else {
                hotToast('Salin manual: pilih teks contoh di bawah.', 'info');
            }
        }

        var fixUrl = u => window.location.protocol === 'https:' ?
            u.replace(/^http:\/\//i, 'https://') :
            u;

        function escapeHtml(text) {
            return String(text)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/\u001b\[0m/g, '</span>')
                .replace(/\u001b\[1m/g, '<span style="font-weight:700">')
                .replace(/\u001b\[31m/g, '<span style="color:#f87171">')
                .replace(/\u001b\[32m/g, '<span style="color:#4ade80">')
                .replace(/\u001b\[33m/g, '<span style="color:#facc15">')
                .replace(/\u001b\[34m/g, '<span style="color:#60a5fa">')
                .replace(/\u001b\[36m/g, '<span style="color:#22d3ee">')
                .replace(/\u001b\[[0-9;]*m/g, '');
        }

        var buildLogUrl = fixUrl('{{ route('user_hosting.build_logs', $project->hashid) }}');
        var buildLogText = document.getElementById('build-log-text');
        var buildLogStatus = document.getElementById('build-log-status');
        var buildLogUpdated = document.getElementById('build-log-updated');
        var websiteLogLink = document.getElementById('website-log-link');
        var buildLogPulse = document.getElementById('build-log-pulse');
        var buildLogInterval = null;

        // Badge status di header & panel Overview di-render server-side,
        // jadi harus di-toggle manual lewat JS saat status berubah.
        var STATUS_STYLE = {
            active:   ['bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300', 'fa-circle-check'],
            building: ['bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 animate-pulse', 'fa-spinner fa-spin'],
            unpaid:   ['bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 font-bold', 'fa-file-invoice-dollar'],
        };
        var STATUS_DEFAULT = ['bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300', 'fa-triangle-exclamation'];

        function applyProjectStatus(status) {
            if (!status) return;
            var badge = document.getElementById('project-status-badge');
            var text  = document.getElementById('project-status-text');
            var icon  = badge ? badge.querySelector('i') : null;
            var style = STATUS_STYLE[status] || STATUS_DEFAULT;

            if (badge && badge.dataset.status === status) return;

            if (badge) {
                badge.className = badge.className.replace(/\b(bg-|text-|animate-pulse|font-bold)[^\s]*/g, '').trim();
                badge.className += ' ' + style[0];
                badge.dataset.status = status;
            }
            if (icon) icon.className = 'fa-solid ' + style[1];
            if (text) text.textContent = status;

            // Muat ulang supaya panel Overview (iframe preview / pesan error)
            // ikut ter-render sesuai status terbaru.
            if (typeof swAlert === 'function') {
                swAlert(
                    status === 'active' ? 'success' : 'warning',
                    status === 'active' ? 'Deployment selesai' : 'Deployment berhenti',
                    'Status project: ' + status
                );
            }
            setTimeout(() => window.location.reload(), 1200);
        }

        function refreshBuildLogs() {
            fetch(buildLogUrl, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.ok ? r.json() : Promise.reject())
                .then(data => {
                    if (buildLogText && data.build_logs !== undefined)
                        buildLogText.innerHTML = escapeHtml(data.build_logs);
                    if (buildLogStatus)
                        buildLogStatus.textContent = data.status || '';
                    if (buildLogUpdated && data.last_updated)
                        buildLogUpdated.textContent = 'Updated: ' + data.last_updated;
                    if (websiteLogLink && data.website_url) {
                        websiteLogLink.href = data.website_url;
                        websiteLogLink.textContent = data.website_url.replace(/^https?:\/\//, '');
                    }
                    if (data.status !== 'building') {
                        if (buildLogPulse) buildLogPulse.style.opacity = '0';
                        if (buildLogInterval) {
                            clearInterval(buildLogInterval);
                            buildLogInterval = null;
                        }
                        applyProjectStatus(data.status);
                    }
                }).catch(() => {});
        }

        if ('{{ $project->status }}' === 'building') {
            refreshBuildLogs();
            buildLogInterval = setInterval(refreshBuildLogs, 2000);
        }
    </script>

    {{-- ── SCRIPT 3: Tab switching ──────────────────────────────────────────── --}}
    <script nonce="{{ csp_nonce() }}">
        window.switchTab = function(name) {
            const panel = document.getElementById('panel-' + name);
            if (!panel) {
                console.warn('[Ryaze] Tab "' + name + '" tidak punya panel, diabaikan.');
                return;
            }
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
            // Tab aktif ditandai dengan .ui-tab-active dari design-system.css,
            // bukan lagi rangkaian class Tailwind yang disSusun manual.
            document.querySelectorAll('.ui-tab[data-tab]').forEach(b => {
                b.classList.remove('ui-tab-active');
                b.setAttribute('aria-selected', 'false');
            });
            panel.classList.remove('hidden');
            const btn = document.getElementById('tab-' + name);
            if (btn) {
                btn.classList.add('ui-tab-active');
                btn.setAttribute('aria-selected', 'true');
            }
            if (name === 'terminal') setTimeout(() => document.getElementById('terminal-input')?.focus(), 80);
            
            // Auto-load file manager saat dibuka
            if (name === 'files') {
                if(typeof window.loadFileManager === 'function') window.loadFileManager();
                else console.error('loadFileManager not defined yet');
            }
            
            // Auto-load IDE saat pertama dibuka
            if (name === 'ide') {
                const ideTree = document.getElementById('ide-sidebar-tree');
                // check if it only contains the HTML comment
                if (ideTree && !ideTree.querySelector('div')) {
                    if(typeof window.loadIdeSidebar === 'function') window.loadIdeSidebar('');
                }
                // Auto-buka panel bawah (Terminal) saat IDE pertama kali dibuka
                if (typeof ideSetBottomPanel === 'function' && !window._idePanelAutoOpened) {
                    window._idePanelAutoOpened = true;
                    setTimeout(() => ideSetBottomPanel(true), 120);
                }
            }
        };

        // Dukungan URL hash: #ide langsung membuka tab IDE (untuk mode popout/new window)
        if (location.hash && location.hash.startsWith('#') && document.getElementById('panel-' + location.hash.slice(1))) {
            switchTab(location.hash.slice(1));
        }
    </script>

    {{-- ── SCRIPT 4: Terminal ───────────────────────────────────────────────── --}}
    <script nonce="{{ csp_nonce() }}">
        var termOut = document.getElementById('terminal-output');
        var termInput = document.getElementById('terminal-input');
        var termPrompt = document.getElementById('terminal-prompt');
        var cwdDisplay = document.getElementById('terminal-cwd-display');
        var termUrl = fixUrl('{{ route('user_hosting.terminal', $project->hashid) }}');
        var csrfToken = '{{ csrf_token() }}';
        @php $__projectRoot = str_replace(DIRECTORY_SEPARATOR, '/', hosting_clients_dir()) . '/' . explode('.', $project->ryaze_domain)[0]; @endphp
        var projectRoot = @json($__projectRoot);
        var projectSlug = '{{ $project->ryaze_domain }}';
        var projectDomainBase = '{{ ltrim(substr($project->ryaze_domain, strlen(explode('.', $project->ryaze_domain)[0])), '.') }}';

        var cmdHistory = [],
            histIdx = -1,
            currentCwd = projectRoot,
            running = false;

        function getPromptLabel() {
            return projectSlug;
        }

        function updatePrompt(cwd) {
            currentCwd = cwd;
            const relPath = cwd.startsWith(projectRoot) ? cwd.slice(projectRoot.length) : '';
            cwdDisplay.textContent = projectSlug + relPath;
            termPrompt.innerHTML =
                `<span class="text-[#6BA8DC]">${getPromptLabel()}</span><span class="text-[#8A9BAE] dark:text-white/40"> $</span>`;
        }

        function appendRaw(html) {
            termOut.insertAdjacentHTML('beforeend', html);
            termOut.scrollTop = termOut.scrollHeight;
        }

        async function runCommand() {
            if (running) return;
            const cmd = termInput.value.trim();
            if (!cmd) return;

            cmdHistory.unshift(cmd);
            if (cmdHistory.length > 100) cmdHistory.pop();
            histIdx = -1;

            appendRaw(
                `<div class="flex items-start gap-2 mb-0.5">` +
                `<span class="text-[#6BA8DC] select-none shrink-0">${escapeHtml(getPromptLabel(currentCwd))} $</span>` +
                `<span class="text-[#E3EAF1] break-all">${escapeHtml(cmd)}</span></div>`
            );
            termInput.value = '';
            running = true;

            const lid = 'ld-' + Date.now();
            appendRaw(`<div id="${lid}" class="text-[#525252] dark:text-white/70 animate-pulse">▌</div>`);

            try {
                const res = await fetch(termUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/x-ndjson'
                    },
                    body: JSON.stringify({
                        command: cmd,
                        term_id: 'main'
                    }),
                });
                
                document.getElementById(lid)?.remove();

                if (!res.ok) {
                    const errData = await res.json().catch(() => ({}));
                    appendRaw(`<div class="text-rose-400 dark:text-rose-300 mb-1">${escapeHtml(errData.error || 'Terjadi kesalahan sistem (HTTP ' + res.status + ')')}</div>`);
                    running = false;
                    termInput.focus();
                    return;
                }

                const reader = res.body.getReader();
                const decoder = new TextDecoder('utf-8');
                
                const preId = 'out-' + Date.now();
                appendRaw(`<pre id="${preId}" class="text-[#E3EAF1] whitespace-pre-wrap break-words mb-1 leading-relaxed"></pre>`);
                const preEl = document.getElementById(preId);
                
                let buffer = '';
                
                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;
                    
                    buffer += decoder.decode(value, { stream: true });
                    const lines = buffer.split('\n');
                    buffer = lines.pop(); // simpan sisa baris yang belum lengkap di buffer
                    
                    for (const line of lines) {
                        if (!line.trim()) continue;
                        try {
                            const data = JSON.parse(line);
                            if (data.cwd && data.cwd !== currentCwd) updatePrompt(data.cwd);
                            
                            if (data.error) {
                                appendRaw(`<div class="text-rose-400 dark:text-rose-300 mb-1">${escapeHtml(data.error)}</div>`);
                            }
                            if (data.output && data.output !== '') {
                                preEl.innerHTML += escapeHtml(data.output);
                                termOut.scrollTop = termOut.scrollHeight;
                            }
                            if (data.exit_code !== undefined && data.exit_code !== 0) {
                                preEl.classList.remove('text-[#E3EAF1]');
                                preEl.classList.add('text-rose-300');
                            }
                        } catch (e) {
                            console.error('Error parsing terminal stream JSON:', e, line);
                        }
                    }
                }
            } catch (err) {
                document.getElementById(lid)?.remove();
                appendRaw(`<div class="text-rose-400 dark:text-rose-300 mb-1">Network error: ${escapeHtml(err.message)}</div>`);
            }
            running = false;
            termInput.focus();
        }

        function clearTerminal() {
            Array.from(termOut.children)
                .filter(c => c.id !== 'terminal-welcome')
                .forEach(c => c.remove());
        }

        // Keyboard: Enter, ↑↓ history, Ctrl+L
        termInput.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                runCommand();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (histIdx < cmdHistory.length - 1) termInput.value = cmdHistory[++histIdx];
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                histIdx > 0 ?
                    (termInput.value = cmdHistory[--histIdx]) :
                    (histIdx = -1, termInput.value = '');
            } else if (e.ctrlKey && e.key === 'l') {
                e.preventDefault();
                clearTerminal();
            }
        });

        // Event listeners terminal — menggantikan semua onclick inline
        document.querySelectorAll('[data-action="clear-terminal"]').forEach(el => {
            el.addEventListener('click', clearTerminal);
        });
        document.getElementById('terminal-output').addEventListener('click', () => {
            document.getElementById('terminal-input').focus();
        });
        document.querySelector('[data-action="run-command"]')
            ?.addEventListener('click', runCommand);
    </script>

    {{-- ── SCRIPT 5: File Manager ───────────────────────────────────────────── --}}
    <script nonce="{{ csp_nonce() }}">
        var currentFolderPath = '';
        var currentEditingFile = '';

        var fileManagerUrl = fixUrl('{{ route('user_hosting.files', $project->hashid) }}');
        var fileReadUrl = fixUrl('{{ route('user_hosting.files.read', $project->hashid) }}');
        var fileSaveUrl = fixUrl('{{ route('user_hosting.files.save', $project->hashid) }}');
        var fileUploadUrl = fixUrl('{{ route('user_hosting.files.upload', $project->hashid) }}');
        var fileCreateUrl = fixUrl('{{ route('user_hosting.files.create', $project->hashid) }}');
        var fileDeleteUrl = fixUrl('{{ route('user_hosting.files.delete', $project->hashid) }}');
        var fileRenameUrl = fixUrl('{{ route('user_hosting.files.rename', $project->hashid) }}');
        var fileCopyUrl = fixUrl('{{ route('user_hosting.files.copy', $project->hashid) }}');
        var fileMoveUrl = fixUrl('{{ route('user_hosting.files.move', $project->hashid) }}');
        var fileDownloadUrl = fixUrl('{{ route('user_hosting.files.download', $project->hashid) }}');
        var ideChatUrl = fixUrl('{{ route('user_hosting.ide.chat', $project->hashid) }}');

        window.fileClipboard = null; // { type: 'copy'|'cut', path: '...', name: '...' }

        var PROTECTED_FILES = ['.suspended', '.htaccess', '.user.ini', '.maintenance'];
        var isProtected = name => PROTECTED_FILES.includes(name);

        function escHtmlJs(str) {
            return JSON.stringify(str || '').slice(1, -1).replace(/'/g, "\\'").replace(/"/g, '&quot;');
        }

        // ── Load direktori ─────────────────────────────────────────────────────
        function loadFileManager(path = '') {
            const loader = document.getElementById('file-manager-loader');
            const tbody = document.getElementById('file-manager-body');
            const pathEl = document.getElementById('current-path-display');

            loader.classList.remove('hidden');

            fetch(`${fileManagerUrl}?path=${encodeURIComponent(path)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.error) {
                        hotToast(data.error, 'error');
                        loader.classList.add('hidden');
                        return;
                    }

                    currentFolderPath = data.current_path;
                    pathEl.textContent = currentFolderPath || '(root)';
                    tbody.innerHTML = '';

                    if (!data.items.length) {
                        tbody.innerHTML = `<tr><td colspan="4" class="px-6 py-10 text-center text-[#8A9BAE] dark:text-white/40">
                            <i class="fa-regular fa-folder-open text-3xl mb-2 opacity-50 block"></i>Folder kosong
                        </td></tr>`;
                    } else {
                        data.items.forEach(item => {
                            const isDir = item.type === 'dir';
                            const locked = !isDir && isProtected(item.name);

                            const icon = isDir ?
                                '<i class="fa-solid fa-folder text-amber-400 dark:text-amber-300 text-lg"></i>' :
                                locked ?
                                '<i class="fa-solid fa-lock text-[#C9D5E2] dark:text-white/50 text-lg" title="File sistem"></i>' :
                                '<i class="fa-regular fa-file-lines text-[#8A9BAE] dark:text-white/40 text-lg"></i>';

                            // Tombol aksi — TIDAK ada onclick, pakai class + data-op
                            let actions = '';
                            if (locked) {
                                actions =
                                    `<span class="text-xs text-[#C9D5E2] dark:text-white/50 italic select-none px-1">sistem</span>`;
                            } else if (isDir) {
                                actions = `
                                    <button class="ui-btn ui-btn-md ui-btn-link file-action px-1" data-op="copy" title="Copy">
                                        <i class="fa-solid fa-copy"></i>
                                    </button>
                                    <button class="file-action text-[#6BA8DC] dark:text-[#7FB4E2] hover:text-[#2A6FAD] dark:hover:text-[#6BA8DC] px-1 transition-colors" data-op="cut" title="Cut">
                                        <i class="fa-solid fa-scissors"></i>
                                    </button>
                                    <button class="file-action text-amber-400 dark:text-amber-300 hover:text-amber-600 dark:hover:text-amber-400 px-1 transition-colors" data-op="rename" title="Rename">
                                        <i class="fa-solid fa-i-cursor"></i>
                                    </button>
                                    <button class="file-action text-rose-400 dark:text-rose-300 hover:text-rose-600 dark:hover:text-rose-400 px-1 transition-colors" data-op="delete" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>`;
                            } else {
                                actions = `
                                    <button class="file-action text-[#6BA8DC] dark:text-[#6BA8DC] hover:text-[#2A6FAD] dark:hover:text-[#6BA8DC] px-1 transition-colors" data-op="edit" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="ui-btn ui-btn-md ui-btn-link file-action px-1" data-op="copy" title="Copy">
                                        <i class="fa-solid fa-copy"></i>
                                    </button>
                                    <button class="file-action text-[#6BA8DC] dark:text-[#7FB4E2] hover:text-[#2A6FAD] dark:hover:text-[#6BA8DC] px-1 transition-colors" data-op="cut" title="Cut">
                                        <i class="fa-solid fa-scissors"></i>
                                    </button>
                                    <button class="file-action text-amber-400 dark:text-amber-300 hover:text-amber-600 dark:hover:text-amber-400 px-1 transition-colors" data-op="rename" title="Rename">
                                        <i class="fa-solid fa-i-cursor"></i>
                                    </button>
                                    <a href="${fileDownloadUrl}?path=${encodeURIComponent(item.path)}" target="_blank"
                                        class="text-emerald-400 dark:text-emerald-300 hover:text-emerald-600 dark:hover:text-emerald-400 px-1 transition-colors" title="Download">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                    <button class="file-action text-rose-400 dark:text-rose-300 hover:text-rose-600 dark:hover:text-rose-400 px-1 transition-colors" data-op="delete" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>`;
                            }

                            const tr = document.createElement('tr');
                            tr.className =
                                `hover:bg-[#E8F0F9] dark:bg-[#0F1D30]/50 dark:hover:bg-[#1A2F49] transition-colors group ${locked ? 'opacity-60' : ''}`;
                            // Simpan data di dataset — dipakai event delegation
                            tr.dataset.path = item.path;
                            tr.dataset.name = item.name;
                            tr.dataset.type = item.type;
                            tr.dataset.locked = locked ? '1' : '0';

                            tr.innerHTML = `
                                <td class="px-6 py-2.5 truncate max-w-0">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span class="shrink-0">${icon}</span>
                                        <a href="#" class="file-name-link font-semibold
                                            ${locked
                                                ? 'text-[#8A9BAE] dark:text-white/40 cursor-not-allowed'
                                                : 'text-[#525252] dark:text-white/70 hover:text-[#2A6FAD] dark:hover:text-[#6BA8DC] dark:hover:text-[#6BA8DC] cursor-pointer'} truncate">
                                            ${item.name}
                                        </a>
                                        ${locked ? '<span class="ui-badge ui-badge-neutral text-[10px] px-1.5 font-mono shrink-0">protected</span>' : ''}
                                    </div>
                                </td>
                                <td class="px-4 py-2.5 text-[#8A9BAE] dark:text-white/40 text-xs whitespace-nowrap">${item.size}</td>
                                <td class="px-4 py-2.5 text-[#8A9BAE] dark:text-white/40 text-xs hidden md:table-cell whitespace-nowrap">${item.modified}</td>
                                <td class="px-4 py-2.5 text-right whitespace-nowrap">${actions}</td>`;
                            tbody.appendChild(tr);
                        });
                    }

                    loader.classList.add('hidden');
                })
                .catch((err) => {
                    tbody.innerHTML = `<tr><td colspan="4" class="px-6 py-10 text-center text-rose-400">
                        <i class="fa-solid fa-triangle-exclamation text-3xl mb-2 opacity-70 block"></i>
                        Gagal memuat file: ${err && err.message ? err.message : 'Network error'}
                    </td></tr>`;
                    loader.classList.add('hidden');
                });
        }
        window.loadFileManager = loadFileManager;

        // ── Event delegation tbody — satu listener untuk semua baris dinamis ──
        document.getElementById('file-manager-body').addEventListener('click', e => {
            const tr = e.target.closest('tr[data-path]');
            if (!tr) return;

            const {
                path,
                name,
                type,
                locked
            } = tr.dataset;
            const isDir = type === 'dir';
            const isLocked = locked === '1';

            // Klik nama file/folder
            if (e.target.closest('.file-name-link')) {
                e.preventDefault();
                if (isLocked) swAlert('warning', 'File Terlindungi', 'File sistem ini tidak dapat diubah.');
                else if (isDir) loadFileManager(path);
                else openFileEditor(path, name);
                return;
            }

            // Klik tombol aksi (edit / delete)
            const btn = e.target.closest('.file-action[data-op]');
            if (!btn) return;

            if (btn.dataset.op === 'edit') openFileEditor(path, name);
            if (btn.dataset.op === 'rename') renameItem(path, name);
            if (btn.dataset.op === 'delete') deleteItem(path, name);
            if (btn.dataset.op === 'copy') copyOrCutItem(path, name, 'copy');
            if (btn.dataset.op === 'cut') copyOrCutItem(path, name, 'cut');
        });

        // ── Navigasi naik ──────────────────────────────────────────────────────
        function navigateUp() {
            if (!currentFolderPath) return;
            loadFileManager(currentFolderPath.split('/').slice(0, -1).join('/'));
        }

        function updatePasteButton() {
            const btn = document.getElementById('fm-btn-paste');
            const ideBtn = document.getElementById('ide-btn-paste');
            if (window.fileClipboard) {
                if (btn) btn.classList.remove('hidden');
                if (ideBtn) ideBtn.classList.remove('hidden');
            } else {
                if (btn) btn.classList.add('hidden');
                if (ideBtn) ideBtn.classList.add('hidden');
            }
        }

        function copyOrCutItem(path, name, action) {
            window.fileClipboard = { path, name, action };
            updatePasteButton();
            // Flash feedback
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
            Toast.fire({ icon: 'success', title: (action === 'copy' ? 'Disalin' : 'Dipotong') + ': ' + name });
        }

        async function doPasteItem(destinationPath) {
            if (!window.fileClipboard) return;
            
            const clipboard = window.fileClipboard;
            const url = clipboard.action === 'copy' ? fileCopyUrl : fileMoveUrl;

            const fd = new FormData();
            fd.append('source', clipboard.path);
            fd.append('destination', destinationPath);
            fd.append('_token', csrfToken);

            const r = await fetch(url, { method: 'POST', body: fd });
            const data = await r.json();

            if (data.error) {
                swAlert('error', 'Gagal', data.error);
                return;
            }

            if (clipboard.action === 'cut') {
                window.fileClipboard = null;
                updatePasteButton();
            }

            swAlert('success', 'Berhasil', 'File/folder berhasil di-' + (clipboard.action === 'copy' ? 'copy' : 'move'));
            
            // Reload views
            if(typeof window.loadFileManager === 'function') window.loadFileManager(currentFolderPath);
            if(typeof window.renderIdeTree === 'function') window.renderIdeTree();
        }

        // ── Buat file / folder baru ────────────────────────────────────────────
        async function promptCreateItem(type) {
            const label = type === 'dir' ? 'Folder' : 'File';
            const {
                value: name
            } = await swInput(`Buat ${label} Baru`, `Nama ${label}...`);
            if (!name) return;

            if (isProtected(name)) {
                swAlert('error', 'Nama Tidak Diizinkan',
                'Nama file tersebut adalah file sistem dan tidak bisa dibuat.');
                return;
            }

            fetch(fileCreateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    type,
                    name,
                    current_path: currentFolderPath
                })
            }).then(r => r.json()).then(data => {
                if (data.error) swAlert('error', 'Gagal', data.error);
                else {
                    hotToast(`${label} berhasil dibuat!`, 'success');
                    loadFileManager(currentFolderPath);
                }
            });
        }

        // ── Rename file / folder ───────────────────────────────────────────────
        async function renameItem(path, name) {
            if (isProtected(name)) {
                swAlert('warning', 'File Terlindungi', 'File sistem ini tidak dapat diubah.');
                return;
            }
            const { value: newName } = await Swal.fire({
                title: 'Rename',
                input: 'text',
                inputValue: name,
                inputLabel: 'Nama baru',
                showCancelButton: true,
                confirmButtonText: '<i class="fa-solid fa-check mr-1"></i> Rename',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#1D5B96',
                customClass: { popup: 'text-sm' },
                inputValidator: (v) => {
                    if (!v) return 'Nama tidak boleh kosong!';
                    if (v === name) return 'Nama sama dengan sebelumnya!';
                    if (/[^a-zA-Z0-9_.\-]/.test(v)) return 'Nama hanya boleh mengandung huruf, angka, titik, garis bawah, atau strip.';
                }
            });
            if (!newName) return;

            fetch(fileRenameUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ path, new_name: newName })
            }).then(r => r.json()).then(data => {
                if (data.error) swAlert('error', 'Gagal', data.error);
                else {
                    hotToast(`Berhasil direname ke "${newName}"!`, 'success');
                    loadFileManager(currentFolderPath);
                }
            });
        }

        // ── Hapus file / folder ────────────────────────────────────────────────
        async function deleteItem(path, name) {
            if (isProtected(name)) {
                swAlert('warning', 'File Terlindungi', 'File sistem ini tidak dapat dihapus.');
                return;
            }
            const result = await swConfirm('Hapus permanen?', `"${name}" akan dihapus dan tidak bisa dikembalikan.`);
            if (!result.isConfirmed) return;

            fetch(fileDeleteUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    path
                })
            }).then(r => r.json()).then(data => {
                if (data.error) swAlert('error', 'Gagal', data.error);
                else {
                    hotToast('Berhasil dihapus!', 'success');
                    loadFileManager(currentFolderPath);
                }
            });
        }

        // ── Upload file ────────────────────────────────────────────────────────
        function uploadFile(inputEl) {
            if (!inputEl.files.length) return;
            const fileName = inputEl.files[0].name;
            if (isProtected(fileName)) {
                swAlert('error', 'Upload Ditolak', 'Nama file tersebut adalah file sistem dan tidak bisa diupload.');
                inputEl.value = '';
                return;
            }

            const formData = new FormData();
            formData.append('file', inputEl.files[0]);
            formData.append('current_path', currentFolderPath);
            document.getElementById('file-manager-loader').classList.remove('hidden');

            fetch(fileUploadUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            }).then(r => r.json()).then(data => {
                inputEl.value = '';
                if (data.error) swAlert('error', 'Upload gagal', data.error);
                else {
                    hotToast('File berhasil diupload!', 'success');
                    loadFileManager(currentFolderPath);
                }
            }).catch(() => {
                document.getElementById('file-manager-loader').classList.add('hidden');
                swAlert('error', 'Upload gagal');
            });
        }

        // ── Buka editor ────────────────────────────────────────────────────────
        function openFileEditor(path, filename) {
            if (isProtected(filename)) {
                swAlert('warning', 'File Terlindungi', 'File sistem ini tidak dapat diubah.');
                return;
            }

            const modal = document.getElementById('file-editor-modal');
            const loader = document.getElementById('editor-loader');
            const textarea = document.getElementById('file-editor-textarea');
            currentEditingFile = path;
            document.getElementById('editor-filename').textContent = filename;
            modal.classList.remove('hidden');
            loader.classList.remove('hidden');
            textarea.value = '';

            fetch(`${fileReadUrl}?path=${encodeURIComponent(path)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.error) {
                        swAlert('error', 'Gagal baca file', data.error);
                        closeFileEditor();
                    } else {
                        if (typeof monaco !== 'undefined' && window.editor) {
                            let ext = filename.split('.').pop().toLowerCase();
                            let lang = 'plaintext';
                            if (ext === 'php') lang = 'php';
                            else if (ext === 'js') lang = 'javascript';
                            else if (ext === 'ts') lang = 'typescript';
                            else if (ext === 'html') lang = 'html';
                            else if (ext === 'css') lang = 'css';
                            else if (ext === 'json') lang = 'json';
                            else if (ext === 'env' || filename === '.env') lang = 'ini';
                            else if (ext === 'sql') lang = 'sql';
                            else if (ext === 'sh' || ext === 'bash') lang = 'shell';
                            else if (ext === 'yaml' || ext === 'yml') lang = 'yaml';
                            else if (ext === 'xml') lang = 'xml';

                            monaco.editor.setModelLanguage(window.editor.getModel(), lang);
                            window.editor.setValue(data.content);
                        } else {
                            textarea.value = data.content;
                        }
                    }
                    loader.classList.add('hidden');
                })
                .catch(() => {
                    swAlert('error', 'Gagal membaca file.');
                    closeFileEditor();
                });
        }

        function closeFileEditor() {
            document.getElementById('file-editor-modal').classList.add('hidden');
            currentEditingFile = '';
        }

        // ── Simpan file ────────────────────────────────────────────────────────
        function saveFileEditor() {
            if (isProtected(currentEditingFile.split('/').pop())) {
                swAlert('warning', 'File Terlindungi', 'File sistem ini tidak dapat disimpan.');
                return;
            }

            const loader = document.getElementById('editor-loader');
            const content = (typeof monaco !== 'undefined' && window.editor) 
                            ? window.editor.getValue() 
                            : document.getElementById('file-editor-textarea').value;
            loader.classList.remove('hidden');

            fetch(fileSaveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    path: currentEditingFile,
                    content
                })
            }).then(async r => {
                const text = await r.text();
                try {
                    const data = JSON.parse(text);
                    loader.classList.add('hidden');
                    if (data.error) swAlert('error', 'Gagal simpan', data.error);
                    else hotToast('File berhasil disimpan!', 'success');
                } catch (e) {
                    loader.classList.add('hidden');
                    swAlert('error', 'Response Error', 'Status: ' + r.status + ' Text: ' + text.substring(0, 100));
                }
            }).catch((err) => {
                loader.classList.add('hidden');
                swAlert('error', 'Network/Fetch Error', err.message);
            });
        }

        // ── Event listeners toolbar file manager — menggantikan onclick inline ──
        document.querySelector('[data-action="navigate-up"]')
            ?.addEventListener('click', navigateUp);

        document.querySelector('[data-action="new-file"]')
            ?.addEventListener('click', () => promptCreateItem('file'));

        document.querySelector('[data-action="paste-file"]')
            ?.addEventListener('click', () => doPasteItem(currentFolderPath));

        document.querySelector('[data-action="new-dir"]')
            ?.addEventListener('click', () => promptCreateItem('dir'));

        document.querySelector('[data-action="refresh-files"]')
            ?.addEventListener('click', () => loadFileManager(currentFolderPath));

        document.querySelector('[data-action="upload-file"]')
            ?.addEventListener('change', function() {
                uploadFile(this);
            });

        document.querySelector('[data-action="close-editor"]')
            ?.addEventListener('click', closeFileEditor);

        document.querySelector('[data-action="save-editor"]')
            ?.addEventListener('click', saveFileEditor);

        document.querySelector('[data-action="confirm-delete"]')
            ?.addEventListener('click', confirmDelete);

        // ── Load Monaco Editor ──────────────────────────────────────────────────
        if (typeof window._ryazeMonacoLoaded === 'undefined') {
            window._ryazeMonacoLoaded = true;
            var monacoScript = document.createElement('script');
            monacoScript.src = "https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs/loader.min.js";
            monacoScript.onload = function() {
                require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs' }});
                // Provide proxy to avoid worker cross-origin issues from CDN
                window.MonacoEnvironment = {
                    getWorkerUrl: function(workerId, label) {
                        return `data:text/javascript;charset=utf-8,${encodeURIComponent(`
                            self.MonacoEnvironment = { baseUrl: 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/' };
                            importScripts('https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs/base/worker/workerMain.js');
                        `)}`;
                    }
                };
                require(['vs/editor/editor.main'], function() {
                    if (typeof defineMonacoThemes !== 'undefined') defineMonacoThemes();
                    if (typeof themesDefined !== 'undefined') themesDefined = true;
                    if (window.editor) window.editor.dispose();
                    window.editor = monaco.editor.create(document.getElementById('monaco-editor-container'), {
                        value: "",
                        language: "php",
                        theme: localStorage.getItem('ryaze-ide-theme') || 'vs-dark',
                        automaticLayout: true,
                        minimap: { enabled: true },
                        fontSize: 13,
                        fontFamily: "'Fira Code', 'JetBrains Mono', 'Courier New', monospace"
                    });
                });
            };
            document.body.appendChild(monacoScript);
        } else {
            var checkMonacoReady = setInterval(function() {
                if (typeof monaco !== 'undefined' && document.getElementById('monaco-editor-container')) {
                    clearInterval(checkMonacoReady);
                    if (window.editor) window.editor.dispose();
                    if (typeof defineMonacoThemes !== 'undefined' && typeof themesDefined !== 'undefined' && !themesDefined) {
                        defineMonacoThemes();
                        themesDefined = true;
                    }
                    window.editor = monaco.editor.create(document.getElementById('monaco-editor-container'), {
                        value: "",
                        language: "php",
                        theme: localStorage.getItem('ryaze-ide-theme') || 'vs-dark',
                        automaticLayout: true,
                        minimap: { enabled: true },
                        fontSize: 13,
                        fontFamily: "'Fira Code', 'JetBrains Mono', 'Courier New', monospace"
                    });
                }
            }, 100);
        }

        // ── IDE Tab Logic ───────────────────────────────────────────────────────
        var ideEditorInstance = null;
        var ideCurrentPath = '';
        var ideEditingFile = '';
        var ideCurrentFilename = '';
        var ideLoading = false;
        var ideTabs = {};   // path -> { model, filename, dirty }
        var ideActiveTab = null;

        function ideFileIcon(filename) {
            const low = filename.toLowerCase();
            if (low === '.env') return 'fa-solid fa-key text-amber-400 dark:text-amber-300';
            const icons = {
                php: 'fa-brands fa-php text-[#6BA8DC]',
                js: 'fa-brands fa-js text-amber-400 dark:text-amber-300',
                jsx: 'fa-brands fa-react text-[#6BA8DC] dark:text-[#6BA8DC]',
                ts: 'fa-brands fa-js text-[#2A6FAD] dark:text-[#6BA8DC]',
                tsx: 'fa-brands fa-react text-[#6BA8DC] dark:text-[#6BA8DC]',
                vue: 'fa-brands fa-vuejs text-emerald-400 dark:text-emerald-300',
                html: 'fa-brands fa-html5 text-amber-400 dark:text-amber-300',
                css: 'fa-brands fa-css3 text-[#6BA8DC] dark:text-[#6BA8DC]',
                scss: 'fa-brands fa-sass text-rose-400 dark:text-rose-300',
                less: 'fa-brands fa-css3 text-[#6BA8DC] dark:text-[#6BA8DC]',
                json: 'fa-solid fa-code text-amber-400 dark:text-amber-300',
                md: 'fa-solid fa-file-lines text-[#8A9BAE] dark:text-white/40',
                txt: 'fa-solid fa-file-lines text-[#8A9BAE] dark:text-white/40',
                py: 'fa-brands fa-python text-[#6BA8DC] dark:text-[#6BA8DC]',
                sql: 'fa-solid fa-database text-emerald-400 dark:text-emerald-300',
                yml: 'fa-solid fa-gear text-[#8A9BAE] dark:text-white/40',
                yaml: 'fa-solid fa-gear text-[#8A9BAE] dark:text-white/40',
                lock: 'fa-solid fa-lock text-[#8A9BAE] dark:text-white/40',
                gitignore: 'fa-solid fa-file-shield text-[#8A9BAE] dark:text-white/40',
                editorconfig: 'fa-solid fa-sliders text-[#8A9BAE] dark:text-white/40',
                env: 'fa-solid fa-key text-amber-400 dark:text-amber-300'
            };
            return icons[low.split('.').pop()] || 'fa-regular fa-file text-[#8A9BAE] dark:text-white/40';
        }

        var ideFolderCache = {};      // path -> items (cache hasil fetch)
        var ideExpanded = new Set();  // folder yang sedang terbuka
        var ideSelectedPath = '';     // folder/file terakhir dipilih (konteks New File/Folder)

        async function ideFetchFolder(path) {
            if (ideFolderCache[path] !== undefined) return ideFolderCache[path];
            try {
                const r = await fetch(`${fileManagerUrl}?path=${encodeURIComponent(path)}`);
                const data = await r.json();
                ideFolderCache[path] = (data && !data.error && data.items) ? data.items : [];
            } catch (e) {
                ideFolderCache[path] = [];
            }
            return ideFolderCache[path];
        }

        async function renderIdeTree() {
            const treeEl = document.getElementById('ide-sidebar-tree');
            treeEl.innerHTML = '<div class="px-4 py-2 opacity-50 italic text-xs"><i class="fa-solid fa-spinner fa-spin mr-2"></i>Loading...</div>';
            const items = await ideFetchFolder('');
            treeEl.innerHTML = '';
            if (!items.length) {
                treeEl.innerHTML = '<div class="px-4 py-2 opacity-50 italic text-xs">Kosong</div>';
                return;
            }
            for (const item of items) await appendIdeRow(treeEl, item, 0);
        }

        async function appendIdeRow(treeEl, item, depth) {
            const isDir = item.type === 'dir';
            const row = document.createElement('div');
            row.className = 'ide-row group flex items-center gap-1.5 py-[3px] pr-2 cursor-pointer hover:bg-[#12233A] transition-colors whitespace-nowrap select-none w-full';
            row.style.paddingLeft = (depth * 14 + 4) + 'px';

            const actionsHtml = !isProtected(item.name) ? `
                <div class="hidden group-hover:flex items-center gap-2 ml-auto pl-2 bg-[#12233A] text-[11px] text-[#8A9BAE]">
                    <button class="hover:text-[#2A6FAD] transition-colors" onclick="event.stopPropagation(); copyOrCutItem('${escHtmlJs(item.path)}', '${escHtmlJs(item.name)}', 'copy')" title="Copy"><i class="fa-solid fa-copy"></i></button>
                    <button class="hover:text-[#2A6FAD] transition-colors" onclick="event.stopPropagation(); copyOrCutItem('${escHtmlJs(item.path)}', '${escHtmlJs(item.name)}', 'cut')" title="Cut"><i class="fa-solid fa-scissors"></i></button>
                </div>
            ` : '';

            if (isDir) {
                const expanded = ideExpanded.has(item.path);
                row.innerHTML =
                    `<span class="ide-chev w-[14px] text-center text-[9px] text-[#525252] dark:text-white/50 shrink-0">${expanded ? '&#9662;' : '&#9656;'}</span>` +
                    `<i class="fa-solid ${expanded ? 'fa-folder-open text-amber-400 dark:text-amber-300' : 'fa-folder text-amber-500 dark:text-amber-400'} text-[13px] shrink-0"></i>` +
                    `<span class="truncate text-[#C9D5E2] flex-1 min-w-0">${item.name}</span>` + actionsHtml;
                row.onclick = (e) => {
                    e.stopPropagation();
                    ideSelectedPath = item.path;
                    ideCurrentPath = item.path;
                    if (expanded) {
                        ideExpanded.delete(item.path);
                        renderIdeTree();
                    } else {
                        ideExpanded.add(item.path);
                        ideFetchFolder(item.path).then(() => renderIdeTree());
                    }
                };
            } else {
                const locked = isProtected(item.name);
                const icon = locked ? 'fa-solid fa-lock text-[#525252] dark:text-white/50' : ideFileIcon(item.name);
                row.innerHTML =
                    `<span class="ide-chev w-[14px] shrink-0"></span>` +
                    `<i class="${icon} text-[13px] shrink-0"></i>` +
                    `<span class="truncate ${locked ? 'opacity-50' : 'text-[#C9D5E2]'} flex-1 min-w-0">${item.name}</span>` + actionsHtml;
                row.onclick = (e) => {
                    e.stopPropagation();
                    if (locked) {
                        swAlert('warning', 'File Terlindungi', 'File ini tidak bisa diedit.');
                        return;
                    }
                    ideSelectedPath = item.path;
                    ideCurrentPath = item.path.split('/').slice(0, -1).join('/');
                    openIdeFile(item.path, item.name);
                };
            }
            treeEl.appendChild(row);

            if (isDir && ideExpanded.has(item.path)) {
                const children = await ideFetchFolder(item.path);
                for (const child of children) await appendIdeRow(treeEl, child, depth + 1);
            }
        }

        function loadIdeSidebar(path = '') {
            if (path) {
                let acc = '';
                path.split('/').filter(Boolean).forEach(p => {
                    acc = acc ? acc + '/' + p : p;
                    ideExpanded.add(acc);
                });
            }
            renderIdeTree();
        }
        window.loadIdeSidebar = loadIdeSidebar;

        function openIdeFile(path, filename) {
            if (ideTabs[path]) {
                activateIdeTab(path);
                return;
            }
            var loader = document.getElementById('ide-loader');
            var saveBtn = document.getElementById('ide-save-btn');
            const emptyState = document.getElementById('ide-empty-state');
            document.getElementById('ide-current-filename').textContent = filename;
            ideCurrentFilename = path;
            loader.classList.remove('hidden');

            fetch(`${fileReadUrl}?path=${encodeURIComponent(path)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.error) {
                        swAlert('error', 'Gagal', data.error);
                        loader.classList.add('hidden');
                        return;
                    }
                    emptyState.classList.add('hidden');
                    saveBtn.classList.remove('hidden');

                    if (!ideEditorInstance && typeof monaco !== 'undefined') {
                        ideEditorInstance = monaco.editor.create(document.getElementById('ide-monaco-container'), {
                            value: '',
                            theme: localStorage.getItem('ryaze-ide-theme') || 'vs-dark',
                            automaticLayout: true,
                            minimap: { enabled: true },
                            fontSize: 13,
                            fontFamily: "'Fira Code', 'JetBrains Mono', 'Courier New', monospace"
                        });
                    }

                    if (typeof monaco !== 'undefined' && ideEditorInstance) {
                        const model = monaco.editor.createModel(data.content, ideLangFor(filename));
                        ideTabs[path] = { model: model, filename: filename, dirty: false };
                        addIdeTab(path, filename);
                        model.onDidChangeContent(() => {
                            if (!ideTabs[path]) return;
                            ideTabs[path].dirty = true;
                            const el = document.querySelector(`#ide-tabs-bar .ide-tab[data-path="${path}"]`);
                            if (el) el.querySelector('.ide-tab-dirty').style.display = 'block';
                        });
                        activateIdeTab(path);
                    }
                    loader.classList.add('hidden');
                }).catch(() => {
                    swAlert('error', 'Error', 'Gagal membaca file');
                    loader.classList.add('hidden');
                });
        }

        function ideLangFor(filename) {
            const ext = filename.split('.').pop().toLowerCase();
            if (ext === 'php') return 'php';
            if (ext === 'js') return 'javascript';
            if (ext === 'ts') return 'typescript';
            if (ext === 'html') return 'html';
            if (ext === 'css') return 'css';
            if (ext === 'scss') return 'scss';
            if (ext === 'json') return 'json';
            if (ext === 'md') return 'markdown';
            if (ext === 'vue') return 'html';
            if (ext === 'py') return 'python';
            if (ext === 'env' || filename === '.env') return 'ini';
            if (ext === 'xml') return 'xml';
            if (ext === 'yml' || ext === 'yaml') return 'yaml';
            if (ext === 'sh' || ext === 'bash') return 'shell';
            if (ext === 'sql') return 'sql';
            return 'plaintext';
        }

        function addIdeTab(path, filename) {
            const bar = document.getElementById('ide-tabs-bar');
            const tab = document.createElement('div');
            tab.className = 'ide-tab flex items-center gap-2 px-3 py-2 text-xs text-[#C9D5E2] dark:text-white/50 border-r border-[#0A1322] cursor-pointer whitespace-nowrap shrink-0 select-none bg-[#12233A]';
            tab.dataset.path = path;
            tab.innerHTML = `
                <i class="fa-regular fa-file-code text-[#6BA8DC]"></i>
                <span class="max-w-[160px] truncate">${filename}</span>
                <span class="ide-tab-dirty hidden text-[8px] text-amber-400 dark:text-amber-300">●</span>
                <span class="ide-tab-close text-[#525252] dark:text-white/50 hover:text-white" title="Tutup"><i class="fa-solid fa-xmark text-[10px]"></i></span>
            `;
            tab.addEventListener('click', (e) => {
                if (e.target.closest('.ide-tab-close')) {
                    closeIdeTab(path);
                    return;
                }
                activateIdeTab(path);
            });
            bar.appendChild(tab);
            bar.scrollLeft = bar.scrollWidth;
        }

        function activateIdeTab(path) {
            if (!ideTabs[path] || !ideEditorInstance) return;
            ideActiveTab = path;
            ideEditingFile = path;
            ideEditorInstance.setModel(ideTabs[path].model);
            document.getElementById('ide-current-filename').textContent = ideTabs[path].filename;
            document.querySelectorAll('#ide-tabs-bar .ide-tab').forEach(t => {
                const active = t.dataset.path === path;
                t.style.backgroundColor = active ? '#0A1322' : '#12233A';
                t.style.borderTop = active ? '1px solid #6366f1' : '1px solid transparent';
            });
            if (typeof monaco !== 'undefined' && ideTabs[path].model) {
                monaco.editor.setModelLanguage(ideTabs[path].model, ideLangFor(ideTabs[path].filename));
            }
        }

        function closeIdeTab(path) {
            if (!ideTabs[path]) return;
            ideTabs[path].model.dispose();
            delete ideTabs[path];
            const el = document.querySelector(`#ide-tabs-bar .ide-tab[data-path="${path}"]`);
            if (el) el.remove();
            if (ideActiveTab === path) {
                const remaining = Object.keys(ideTabs);
                if (remaining.length > 0) {
                    activateIdeTab(remaining[remaining.length - 1]);
                } else {
                    ideActiveTab = null;
                    ideEditingFile = null;
                    document.getElementById('ide-current-filename').textContent = 'Pilih file...';
                    document.getElementById('ide-save-btn').classList.add('hidden');
                    document.getElementById('ide-empty-state').classList.remove('hidden');
                    if (ideEditorInstance) ideEditorInstance.setModel(null);
                }
            }
        }

        document.querySelector('[data-action="ide-refresh"]')?.addEventListener('click', () => {
            ideFolderCache = {};
            renderIdeTree();
        });
        document.querySelector('[data-action="ide-collapse"]')?.addEventListener('click', () => {
            ideExpanded.clear();
            ideSelectedPath = '';
            renderIdeTree();
        });
        document.querySelector('[data-action="ide-new-file"]')?.addEventListener('click', () => {
            promptCreateIdeItem('file');
        });
        document.querySelector('[data-action="ide-new-dir"]')?.addEventListener('click', () => {
            promptCreateIdeItem('dir');
        });
        document.querySelector('[data-action="ide-paste"]')?.addEventListener('click', () => {
            doPasteItem(ideCurrentPath);
        });

        async function promptCreateIdeItem(type) {
            const label = type === 'dir' ? 'Folder' : 'File';
            const { value: name } = await swInput(`Buat ${label} Baru`, `Nama ${label}...`);
            if (!name) return;

            if (isProtected(name)) {
                swAlert('error', 'Tidak Diizinkan', 'Nama file tersebut adalah file sistem.');
                return;
            }

            fetch(fileCreateUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ type, name, current_path: ideSelectedPath })
            }).then(r => r.json()).then(data => {
                if (data.error) swAlert('error', 'Gagal', data.error);
                else {
                    hotToast(`${label} berhasil dibuat!`, 'success');
                    if (ideSelectedPath) ideExpanded.add(ideSelectedPath);
                    delete ideFolderCache[ideSelectedPath];
                    renderIdeTree();
                }
            });
        }
        document.querySelector('[data-action="ide-save"]')?.addEventListener('click', () => {
            if (!ideActiveTab || !ideTabs[ideActiveTab] || !ideEditorInstance) return;
            var path = ideActiveTab;
            var loader = document.getElementById('ide-loader');
            loader.classList.remove('hidden');
            fetch(fileSaveUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ path: path, content: ideTabs[path].model.getValue() })
            }).then(r => r.json()).then(data => {
                loader.classList.add('hidden');
                if (data.error) swAlert('error', 'Gagal', data.error);
                else {
                    ideTabs[path].dirty = false;
                    const el = document.querySelector(`#ide-tabs-bar .ide-tab[data-path="${path}"]`);
                    if (el) el.querySelector('.ide-tab-dirty').style.display = 'none';
                    hotToast('File berhasil disimpan!', 'success');
                }
            }).catch(() => {
                loader.classList.add('hidden');
                swAlert('error', 'Error', 'Gagal menyimpan file');
            });
        });

        // Add IDE tab trigger logic
        document.getElementById('tab-ide')?.addEventListener('click', () => {
            if (document.getElementById('ide-sidebar-tree').children.length === 0) {
                loadIdeSidebar('');
            }
        });

        window.applyAiCode = function(b64) {
            if (ideActiveTab && ideTabs[ideActiveTab]) {
                const raw = decodeURIComponent(escape(atob(b64)));
                ideTabs[ideActiveTab].model.setValue(raw);
                hotToast('Kode berhasil diterapkan!', 'success');
            } else {
                swAlert('error', 'Editor tidak aktif', 'Buka file terlebih dahulu.');
            }
        };

        // ── Themes Logic ────────────────────────────────────────────────────────
        function applyIdeTheme(theme, btn) {
            if (typeof monaco === 'undefined') {
                hotToast('Editor belum dimuat, coba lagi beberapa detik lagi', 'warning');
                return;
            }
            if (!themesDefined) {
                defineMonacoThemes();
                themesDefined = true;
            }
            monaco.editor.setTheme(theme);
            localStorage.setItem('ryaze-ide-theme', theme);
            document.getElementById('ide-theme-selector').value = theme;
            hotToast('Tema diubah ke ' + theme, 'success');
        }
        window.applyIdeTheme = applyIdeTheme;

        function defineMonacoThemes() {
            if (typeof monaco === 'undefined') return;
            // One Dark Pro
            monaco.editor.defineTheme('one-dark-pro', {
                base: 'vs-dark', inherit: true,
                rules: [
                    { background: '282c34' },
                    { token: 'keyword', foreground: 'c678dd' },
                    { token: 'string', foreground: '98c379' },
                    { token: 'comment', foreground: '5c6370', fontStyle: 'italic' },
                    { token: 'number', foreground: 'd19a66' },
                    { token: 'type', foreground: 'e5c07b' },
                ],
                colors: {
                    'editor.background': '#282c34',
                    'editor.foreground': '#abb2bf',
                    'editorLineNumber.foreground': '#495162',
                    'editor.selectionBackground': '#3e4451',
                    'editor.lineHighlightBackground': '#2c313c'
                }
            });
            // Dracula
            monaco.editor.defineTheme('dracula', {
                base: 'vs-dark', inherit: true,
                rules: [
                    { background: '282a36' },
                    { token: 'keyword', foreground: 'ff79c6' },
                    { token: 'string', foreground: 'f1fa8c' },
                    { token: 'comment', foreground: '6272a4', fontStyle: 'italic' },
                    { token: 'number', foreground: 'bd93f9' },
                    { token: 'type', foreground: '8be9fd' },
                ],
                colors: {
                    'editor.background': '#282a36',
                    'editor.foreground': '#f8f8f2',
                    'editorLineNumber.foreground': '#6272a4',
                    'editor.selectionBackground': '#44475a',
                    'editor.lineHighlightBackground': '#44475a'
                }
            });
        }
        
        var themesDefined = false;
        document.getElementById('ide-theme-selector')?.addEventListener('change', (e) => {
            var theme = e.target.value;
            if (typeof monaco !== 'undefined') {
                if (!themesDefined) {
                    defineMonacoThemes();
                    themesDefined = true;
                }
                monaco.editor.setTheme(theme);
                localStorage.setItem('ryaze-ide-theme', theme);
                hotToast('Tema diubah ke ' + theme, 'success');
            }
        });

        // Terapkan tema tersimpan jika ada saat editor pertama kali dibuat
        var savedTheme = localStorage.getItem('ryaze-ide-theme');
        if (savedTheme) {
            document.getElementById('ide-theme-selector').value = savedTheme;
            // monaco.editor.setTheme(savedTheme) will be called after editor is created.
        }

        // ── Activity Bar Logic (VS Code-style toggle, kiri & kanan independen) ──
        var ideLeftOpen = true;
        var ideRightOpen = false;
        var ideZenMode = false;

        function ideSetActiveButton(btn) {
            document.querySelectorAll('.ide-activity-btn').forEach(b => {
                b.classList.remove('ui-ide-activity-active');
            });
            if (btn) btn.classList.add('ui-ide-activity-active');
        }

        function ideSetLeftSidebar(open) {
            ideLeftOpen = open;
            const sb = document.getElementById('ide-left-sidebar');
            sb.style.width = open ? '' : '0px';
            sb.style.borderRightWidth = open ? '' : '0px';
            if (ideEditorInstance) setTimeout(() => ideEditorInstance.layout(), 80);
        }

        function ideSetRightPanel(open) {
            ideRightOpen = open;
            const rp = document.getElementById('ide-right-panel');
            rp.style.width = open ? '' : '0px';
            rp.style.borderLeftWidth = open ? '' : '0px';
            if (ideEditorInstance) setTimeout(() => ideEditorInstance.layout(), 80);
        }

        document.querySelectorAll('.ide-activity-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.dataset.target;
                const isLeft = target !== 'ide-right-chat';

                if (isLeft) {
                    // Klik nama view yang sedang aktif = tutup sidebar (VS Code behavior)
                    if (ideLeftOpen && btn.classList.contains('border-[#1D5B96]')) {
                        ideSetLeftSidebar(false);
                        return;
                    }
                    ideSetLeftSidebar(true);
                    document.querySelectorAll('#ide-left-sidebar .ide-sidebar-view').forEach(v => v.classList.add('hidden'));
                    document.getElementById(target).classList.remove('hidden');
                    ideSetActiveButton(btn);
                } else {
                    // Klik AI = toggle panel kanan, tanpa menyentuh panel kiri
                    if (ideRightOpen) {
                        ideSetRightPanel(false);
                        ideSetActiveButton(null);
                        return;
                    }
                    ideSetRightPanel(true);
                    ideSetActiveButton(btn);
                }
            });
        });

        document.getElementById('ide-collapse-left')?.addEventListener('click', () => {
            ideSetLeftSidebar(false);
            ideSetActiveButton(null);
        });

        document.getElementById('ide-collapse-right')?.addEventListener('click', () => {
            ideSetRightPanel(false);
            ideSetActiveButton(null);
        });

        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
                e.preventDefault();
                ideSetLeftSidebar(!ideLeftOpen);
            }
        });

        // ── Zen / Fullscreen Mode (VS Code "Expand") ─────────────────────────────
        function ideSetZen(zen) {
            ideZenMode = zen;
            const shell = document.getElementById('ide-shell');
            const panel = document.getElementById('panel-ide');
            const zenBtn = document.getElementById('ide-zen-btn');
            if (zen) {
                shell.classList.add('ide-zen-shell');
                panel.classList.add('ide-zen-panel');
                document.body.style.overflow = 'hidden';
                zenBtn.innerHTML = '<i class="fa-solid fa-compress text-sm"></i>';
                zenBtn.title = 'Keluar Fullscreen (F11)';
            } else {
                shell.classList.remove('ide-zen-shell');
                panel.classList.remove('ide-zen-panel');
                document.body.style.overflow = '';
                zenBtn.innerHTML = '<i class="fa-solid fa-expand text-sm"></i>';
                zenBtn.title = 'Fullscreen (F11)';
            }
            if (ideEditorInstance) setTimeout(() => ideEditorInstance.layout(), 80);
        }

        document.getElementById('ide-zen-btn')?.addEventListener('click', () => ideSetZen(!ideZenMode));

        document.getElementById('ide-popout-btn')?.addEventListener('click', () => {
            window.open(location.href.split('#')[0] + '#ide', '_blank', 'noopener');
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'F11') {
                const idePanel = document.getElementById('panel-ide');
                if (idePanel && !idePanel.classList.contains('hidden')) {
                    e.preventDefault();
                    ideSetZen(!ideZenMode);
                }
            }
        });

        // ── Search Logic ────────────────────────────────────────────────────────
        var searchInput = document.getElementById('ide-search-input');
        var searchCase = document.getElementById('ide-search-case');
        var searchResults = document.getElementById('ide-search-results');
        var searchUrl = '{{ route("user_hosting.ide.search", $project->hashid) }}';

        searchInput?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                var query = searchInput.value.trim();
                if (!query) {
                    searchResults.innerHTML = '<div class="text-[#525252] dark:text-white/50 text-center mt-10">Ketik dan tekan Enter untuk mencari</div>';
                    return;
                }
                
                searchResults.innerHTML = '<div class="text-center mt-10 text-[#6BA8DC]"><i class="fa-solid fa-spinner fa-spin text-xl mb-2"></i><br>Mencari...</div>';
                
                fetch(searchUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ query: query, matchCase: searchCase.checked })
                }).then(r => r.json()).then(data => {
                    if (data.results && data.results.length > 0) {
                        let html = '';
                        let currentFile = '';
                        
                        data.results.forEach(res => {
                            if (res.type === 'name') {
                                const isFolder = res.content === '(folder)';
                                const icon = isFolder ? 'fa-solid fa-folder text-amber-500 dark:text-amber-400' : 'fa-regular fa-file-code';
                                html += `<div class="mt-2 mb-1 text-[11px] font-bold text-[#C9D5E2] dark:text-white/50 break-all cursor-pointer hover:text-[#2A6FAD] transition" onclick="${isFolder ? 'loadIdeSidebar(\'' + res.path + '\')' : 'openIdeFile(\'' + res.path + '\', \'' + res.path.split('/').pop() + '\')'}"><i class="${icon} mr-1"></i>${res.path}${isFolder ? ' <span class="text-[9px] text-amber-500/80 font-medium">(folder)</span>' : ''}</div>`;
                                return;
                            }
                            if (currentFile !== res.path) {
                                currentFile = res.path;
                                html += `<div class="mt-3 mb-1 text-[11px] font-bold text-[#C9D5E2] dark:text-white/50 break-all cursor-pointer hover:text-[#2A6FAD] transition" onclick="openIdeFile('${res.path}', '${res.path.split('/').pop()}')"><i class="fa-regular fa-file-code mr-1"></i>${res.path}</div>`;
                            }
                            // Highlight the matching text basically
                            const safeContent = res.content.replace(/</g, '&lt;').replace(/>/g, '&gt;');
                            html += `<div class="pl-4 py-1 hover:bg-[#12233A] cursor-pointer text-[#8A9BAE] dark:text-white/40 transition-colors flex gap-2" onclick="openIdeFile('${res.path}', '${res.path.split('/').pop()}'); setTimeout(() => { if(ideEditorInstance) { ideEditorInstance.revealLineInCenter(${res.line}); ideEditorInstance.setPosition({lineNumber: ${res.line}, column: 1}); } }, 500);">
                                <span class="text-[#525252] dark:text-white/70 shrink-0 w-6 text-right">${res.line}</span>
                                <span class="truncate">${safeContent}</span>
                            </div>`;
                        });
                        searchResults.innerHTML = html;
                    } else {
                        searchResults.innerHTML = '<div class="text-[#525252] dark:text-white/50 text-center mt-10">Tidak ditemukan hasil.</div>';
                    }
                }).catch(() => {
                    searchResults.innerHTML = '<div class="text-rose-500 dark:text-rose-400 text-center mt-10">Error melakukan pencarian.</div>';
                });
            }
        });

        // ── Groq AI Logic (Ryaze AI v2.0) ─────────────────────────────────────
        var ideChatListUrl = fixUrl('{{ route('user_hosting.ide.chats', $project->hashid) }}');
        var ideChatCreateUrl = fixUrl('{{ route('user_hosting.ide.chats.create', $project->hashid) }}');
        var ideChatMsgsUrl = fixUrl('{{ route('user_hosting.ide.chats.messages', [$project->hashid, '__CHAT__']) }}');
        var ideChatDelUrl = fixUrl('{{ route('user_hosting.ide.chats.delete', [$project->hashid, '__CHAT__']) }}');
        var ideChatId = null;

        function escChat(s) { return String(s).replace(/</g, '&lt;').replace(/>/g, '&gt;'); }

        function formatAiText(text) {
            return String(text || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/\n/g, '<br>')
                .replace(/```([a-z]*)(?:<br>)?([\s\S]*?)```/g, (match, lang, code) => {
                    let rawCode = code.replace(/<br>/g, '\n').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&');
                    let base64Code = btoa(unescape(encodeURIComponent(rawCode.trim())));
                    return `<div class="relative group my-2"><div class="absolute right-1 top-1 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity z-10"><button type="button" onclick="window.applyAiCode('${base64Code}')" class="ui-btn ui-btn-sm ui-btn-primary text-[9px]"><i class="fa-solid fa-wand-magic-sparkles"></i> Terapkan</button></div><pre class="bg-black/30 p-2 pt-6 rounded overflow-x-auto border border-[#24384F] text-[11px] relative"><code>${code}</code></pre></div>`;
                })
                .replace(/`([^`]+)`/g, '<code class="bg-black/30 px-1 py-0.5 rounded text-[11px] text-amber-300">$1</code>');
        }

        function ideChatWelcome() {
            document.getElementById('grok-chat-messages').innerHTML = `
                <div class="bg-[#0F1D30] text-[#E3EAF1] p-2 self-start max-w-[90%] text-xs leading-relaxed">
                    Halo! Saya <b>Ryaze AI v2.0</b> (GPT-OSS 120B). Bisa analisis bug, generate kode, atau <i>edit file project langsung</i>. Riwayat percakapan tersimpan otomatis.
                </div>
            `;
        }

        function ideRefreshChatList() {
            fetch(ideChatListUrl, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    const list = document.getElementById('ide-chat-list');
                    const chats = data.chats || [];
                    list.innerHTML = '';
                    if (!chats.length) {
                        list.innerHTML = '<div class="p-4 text-[#525252] dark:text-white/50 text-xs text-center">Belum ada percakapan.<br>Klik <i class="fa-solid fa-plus"></i> untuk memulai.</div>';
                        return;
                    }
                    chats.forEach(c => {
                        const row = document.createElement('div');
                        row.className = 'flex items-center gap-2 px-3 py-2 hover:bg-[#0F1D30] cursor-pointer transition-colors border-b border-[#0F1D30]' + (ideChatId && c.id === ideChatId ? ' bg-[#12233A]' : '');
                        row.innerHTML = `<div class="flex-1 min-w-0">
                            <div class="text-xs text-[#E3EAF1] truncate font-medium">${escChat(c.title)}</div>
                            <div class="text-[10px] text-[#525252] dark:text-white/50">${escChat(c.updated_at)} · ${c.messages} pesan</div>
                        </div>
                        <button class="ide-chat-del text-[#525252] dark:text-white/70 hover:text-rose-400 transition-colors shrink-0" title="Hapus"><i class="fa-solid fa-trash-can text-[10px]"></i></button>`;
                        row.querySelector('.ide-chat-del').addEventListener('click', (e) => {
                            e.stopPropagation();
                            ideDeleteChat(c.id);
                        });
                        row.addEventListener('click', () => {
                            ideLoadChat(c.id);
                            document.getElementById('ide-chat-list').classList.add('hidden');
                        });
                        list.appendChild(row);
                    });
                }).catch(() => {});
        }

        function ideLoadChat(id) {
            ideChatId = id;
            const mc = document.getElementById('grok-chat-messages');
            mc.innerHTML = '<div class="p-3 text-[#525252] dark:text-white/50 text-xs text-center"><i class="fa-solid fa-spinner fa-spin mr-2"></i>Memuat percakapan...</div>';
            fetch(ideChatMsgsUrl.replace('__CHAT__', id), { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    const msgs = data.messages || [];
                    mc.innerHTML = '';
                    if (!msgs.length) { ideChatWelcome(); return; }
                    msgs.forEach(m => {
                        if (m.role === 'user') {
                            mc.insertAdjacentHTML('beforeend', `<div class="bg-[#1D5B96] text-white p-2 self-end max-w-[90%] text-xs leading-relaxed shadow-none">${escChat(m.content)}</div>`);
                        } else {
                            mc.insertAdjacentHTML('beforeend', `<div class="bg-[#0F1D30] text-[#E3EAF1] p-2.5 self-start max-w-[95%] text-xs leading-relaxed border border-[#24384F] shadow-none mt-1"><b>Ryaze AI v2.0:</b><br>${formatAiText(m.content)}</div>`);
                        }
                    });
                    mc.scrollTop = mc.scrollHeight;
                    ideRefreshChatList();
                }).catch(() => { mc.innerHTML = '<div class="p-3 text-rose-400 dark:text-rose-300 text-xs">Gagal memuat percakapan.</div>'; });
        }

        function ideDeleteChat(id) {
            swConfirm('Hapus percakapan ini?', 'Pesan akan dihapus permanen.').then(res => {
                if (!res.isConfirmed) return;
                fetch(ideChatDelUrl.replace('__CHAT__', id), { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(() => {
                    if (ideChatId === id) {
                        ideChatId = null;
                        ideChatWelcome();
                    }
                    document.getElementById('ide-chat-list').classList.remove('hidden');
                    ideRefreshChatList();
                    hotToast('Percakapan dihapus', 'success');
                    }).catch(() => hotToast('Gagal menghapus', 'error'));
            });
        }

        function ideCreateChat() {
            fetch(ideChatCreateUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    ideChatId = data.chat_id;
                    ideChatWelcome();
                    document.getElementById('ide-chat-list').classList.add('hidden');
                    ideRefreshChatList();
                    hotToast('Percakapan baru dibuat', 'success');
                    document.getElementById('grok-chat-input').focus();
                }).catch(() => {});
        }

        document.querySelectorAll('.ide-chat-chip').forEach(chip => {
            chip.addEventListener('click', () => {
                const input = document.getElementById('grok-chat-input');
                input.value = chip.textContent.trim() + ': ' + (ideCurrentFilename || '');
                input.focus();
            });
        });

        document.getElementById('ide-chat-new')?.addEventListener('click', ideCreateChat);

        document.getElementById('ide-chat-history')?.addEventListener('click', () => {
            const list = document.getElementById('ide-chat-list');
            list.classList.toggle('hidden');
            if (!list.classList.contains('hidden')) ideRefreshChatList();
        });

        document.getElementById('ide-chat-clear')?.addEventListener('click', () => {
            if (ideChatId) ideDeleteChat(ideChatId);
            else { ideChatId = null; ideChatWelcome(); hotToast('Percakapan dikosongkan', 'success'); }
        });

        var sendGrokMessage = () => {
            const input = document.getElementById('grok-chat-input');
            const val = input.value.trim();
            if (!val) return;
            
            const messagesContainer = document.getElementById('grok-chat-messages');
            
            // Add User message
            messagesContainer.innerHTML += `
                <div class="bg-[#1D5B96] text-white p-2 self-end max-w-[90%] text-xs leading-relaxed shadow-none">
                    ${escChat(val)}
                </div>
            `;
            input.value = '';
            
            // Show loading
            const loaderId = 'grok-loader-' + Date.now();
            messagesContainer.innerHTML += `
                <div id="${loaderId}" class="bg-[#0F1D30] text-[#8A9BAE] dark:text-white/40 p-2 self-start max-w-[90%] text-xs mt-1 border border-[#24384F]">
                    <i class="fa-solid fa-ellipsis fa-fade"></i> Ryaze AI sedang berpikir...
                </div>
            `;
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            
            // Real API Call to Backend
            let contextData = '';
            if (ideActiveTab && ideTabs[ideActiveTab]) {
                contextData = `File: ${ideActiveTab}\n${ideTabs[ideActiveTab].model.getValue()}`;
            }

            fetch(ideChatUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: val, context: contextData, chat_id: ideChatId })
            })
            .then(res => res.json())
            .then(data => {
                const loader = document.getElementById(loaderId);
                if(loader) loader.remove();

                if (data.error) {
                    messagesContainer.innerHTML += `
                        <div class="bg-rose-900/50 text-rose-200 p-2.5 self-start max-w-[95%] text-xs leading-relaxed border border-rose-800 shadow-none mt-1">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i> ${escChat(data.error)}
                        </div>
                    `;
                } else {
                    if (data.chat_id) ideChatId = data.chat_id;
                    let replyText = data.reply || '';
                    
                    // Sembunyikan blok FILE_OPS dari tampilan (sudah dieksekusi server)
                    replyText = replyText.replace(/<<FILE_OPS>>[\s\S]*?<<END_FILE_OPS>>/g, '');
                    
                    // 1. Cek auto-replace (<<REPLACE_ALL>>)
                    const replaceMatch = replyText.match(/<<REPLACE_ALL>>([\s\S]*?)<<END_REPLACE>>/);
                    let autoReplaced = false;
                    if (replaceMatch && replaceMatch[1]) {
                        if (ideActiveTab && ideTabs[ideActiveTab]) {
                            ideTabs[ideActiveTab].model.setValue(replaceMatch[1].trim());
                            hotToast('File otomatis diperbarui oleh AI!', 'success');
                        }
                        replyText = replyText.replace(/<<REPLACE_ALL>>[\s\S]*?<<END_REPLACE>>/, '[[AUTO_REPLACED]]');
                        autoReplaced = true;
                    }

                    let formattedReply = formatAiText(replyText);
                    
                    if (autoReplaced) {
                        formattedReply = formattedReply.replace('[[AUTO_REPLACED]]', '<div class="text-emerald-400 dark:text-emerald-300 my-2 p-2 bg-emerald-900/20 rounded border border-emerald-800/50"><i class="fa-solid fa-check-circle"></i> Seluruh kode di editor telah diperbarui secara otomatis.</div>');
                    }

                    let opsHtml = '';
                    if (data.file_ops && data.file_ops.length > 0) {
                        data.file_ops.forEach(op => {
                            const icon = op.status === 'success' ? '<i class="fa-solid fa-circle-check text-emerald-400 dark:text-emerald-300"></i>'
                                : op.status === 'error' ? '<i class="fa-solid fa-circle-xmark text-rose-400 dark:text-rose-300"></i>'
                                : '<i class="fa-solid fa-circle-info text-[#6BA8DC] dark:text-[#6BA8DC]"></i>';
                            const color = op.status === 'success' ? 'text-emerald-300'
                                : op.status === 'error' ? 'text-rose-300'
                                : 'text-[#7FB4E2]';
                            opsHtml += `<div class="flex items-start gap-2 ${color}"><span class="shrink-0 mt-0.5">${icon}</span><span class="break-all"><b>${op.path || '(parse)'}</b> — ${op.message}</span></div>`;
                        });
                        opsHtml = `<div class="mt-2 p-2 bg-[#12233A] rounded border border-[#24384F] text-[11px] space-y-1">${opsHtml}</div>`;
                    }
                    
                    messagesContainer.innerHTML += `
                        <div class="bg-[#0F1D30] text-[#E3EAF1] p-2.5 self-start max-w-[95%] text-xs leading-relaxed border border-[#24384F] shadow-none mt-1">
                            <b>Ryaze AI v2.0:</b><br>
                            ${formattedReply}
                            ${opsHtml}
                        </div>
                    `;

                    if (data.file_ops && data.file_ops.length > 0) {
                        const anyChanged = data.file_ops.some(op => op.status === 'success');
                        if (anyChanged) {
                            loadIdeSidebar(ideCurrentPath);
                            hotToast('File/folder berhasil diubah oleh AI!', 'success');
                        }
                    }
                    ideRefreshChatList();
                }
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            })
            .catch(err => {
                const loader = document.getElementById(loaderId);
                if(loader) loader.remove();
                messagesContainer.innerHTML += `
                    <div class="bg-rose-900/50 text-rose-200 p-2.5 self-start max-w-[95%] text-xs leading-relaxed border border-rose-800 shadow-none mt-1">
                        <i class="fa-solid fa-plug-circle-xmark mr-1"></i> Gagal menghubungi server.
                    </div>
                `;
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            });
        };

        document.getElementById('grok-chat-send-btn')?.addEventListener('click', sendGrokMessage);
        
        document.getElementById('grok-chat-input')?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendGrokMessage();
            }
        });

        // Inisialisasi: muat percakapan terakhir, atau buat baru
        (function ideInitChat() {
            fetch(ideChatListUrl, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    const chats = data.chats || [];
                    if (chats.length) {
                        ideLoadChat(chats[0].id);
                    } else {
                        ideCreateChat();
                    }
                }).catch(() => {});
        })();

    </script>

    {{-- ── SCRIPT 6: Bottom Panel (Problems / Output / Debug / Terminal / Ports) ── --}}
    <script nonce="{{ csp_nonce() }}">
        var ideBottomOpen = false;
        var ideBottomTab = 'terminal';
        var ideTerminals = [];
        var ideActiveTerminal = null;
        var ideDebugTerminal = null;
        var ideTermSeq = 1;
        var ideProblems = [];
        var ideLogUrl = fixUrl('{{ route('user_hosting.ide.log', $project->hashid) }}');
        var ideLintUrl = fixUrl('{{ route('user_hosting.ide.lint', $project->hashid) }}');

        function ideEsc(s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }

        function ideSetBottomPanel(open) {
            ideBottomOpen = open;
            document.getElementById('ide-bottom-panel').classList.toggle('hidden', !open);
            if (open) ideActivatePanelTab(ideBottomTab);
            setTimeout(() => { if (ideEditorInstance) ideEditorInstance.layout(); }, 80);
        }

        function ideActivatePanelTab(tab) {
            ideBottomTab = tab;
            document.querySelectorAll('.ide-panel-tab').forEach(b => {
                b.classList.toggle('ui-ide-tab-active', b.dataset.panelTab === tab);
            });
            document.querySelectorAll('.ide-panel-view').forEach(v => v.classList.add('hidden'));
            document.getElementById('ide-panel-' + tab).classList.remove('hidden');
            if (tab === 'output') ideRefreshLogTail();
            if (tab === 'problems') ideRenderProblems();
            if (tab === 'debug') ideEnsureDebugTerminal();
            if (tab === 'terminal') ideRenderTermArea();
            if (tab === 'ports') ideRenderPorts();
        }

        document.getElementById('ide-panel-toggle')?.addEventListener('click', () => ideSetBottomPanel(!ideBottomOpen));
        document.getElementById('ide-panel-hide')?.addEventListener('click', () => ideSetBottomPanel(false));
        document.querySelectorAll('.ide-panel-tab').forEach(b => b.addEventListener('click', () => ideActivatePanelTab(b.dataset.panelTab)));
        document.addEventListener('keydown', e => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'j') { e.preventDefault(); ideSetBottomPanel(!ideBottomOpen); }
        });

        // ── Terminal instances ──────────────────────────────────────────────────
        function ideNewTerminal(name, opts = {}) {
            const id = opts.id || ('ide-term-' + (ideTermSeq++));
            const pane = document.createElement('div');
            pane.className = 'ide-term-pane flex-1 flex flex-col min-h-0 bg-[#0A1322] border-t border-[#0A1322]';
            pane.dataset.termId = id;
            pane.innerHTML = `
                <div class="h-7 bg-[#12233A] flex items-center px-3 text-[11px] text-[#8A9BAE] dark:text-white/40 shrink-0 border-b border-[#0A1322]">
                    <span class="text-emerald-400 dark:text-emerald-300 font-mono">${ideEsc(name)}</span>
                    <button class="ide-term-pane-kill ml-auto w-5 h-5 flex items-center justify-center rounded hover:bg-[#12233A] text-[#525252] dark:text-white/70 hover:text-white transition-colors" title="Kill Terminal"><i class="fa-solid fa-xmark text-[9px]"></i></button>
                </div>
                <div class="ide-term-out flex-1 min-w-0 overflow-y-auto p-2 font-mono text-[12px] leading-relaxed text-[#E3EAF1]"></div>
                <div class="flex items-center gap-2 px-2 pb-2 shrink-0">
                    <span class="ide-term-prompt text-[#6BA8DC] font-mono text-xs select-none whitespace-nowrap">${ideEsc(projectSlug)} $</span>
                    <input class="ide-term-input flex-1 min-w-0 bg-transparent text-[#E3EAF1] font-mono text-xs outline-none" placeholder="ketik perintah, lalu Enter..." autocomplete="off" spellcheck="false">
                </div>`;
            const t = {
                id, name, paneEl: pane, split: !!opts.split,
                outEl: pane.querySelector('.ide-term-out'),
                inputEl: pane.querySelector('.ide-term-input'),
                promptEl: pane.querySelector('.ide-term-prompt'),
                history: [], histIdx: -1, running: false, cwd: null
            };
            t.appendRaw = (html) => { t.outEl.insertAdjacentHTML('beforeend', html); t.outEl.scrollTop = t.outEl.scrollHeight; };
            t.updatePrompt = (cwd) => {
                t.cwd = cwd;
                const rel = cwd && cwd.startsWith(projectRoot) ? cwd.slice(projectRoot.length) : '';
                t.promptEl.innerHTML = `<span class="text-[#6BA8DC]">${ideEsc(projectSlug + rel)}</span><span class="text-[#8A9BAE] dark:text-white/40"> $</span>`;
            };
            t.run = async () => {
                if (t.running) return;
                const cmd = t.inputEl.value.trim();
                if (!cmd) return;
                t.history.unshift(cmd);
                if (t.history.length > 100) t.history.pop();
                t.histIdx = -1;
                t.appendRaw(`<div class="flex items-start gap-2 mb-0.5"><span class="text-[#6BA8DC] select-none shrink-0">${ideEsc(projectSlug)} $</span><span class="text-[#E3EAF1] break-all">${ideEsc(cmd)}</span></div>`);
                t.inputEl.value = '';
                t.running = true;
                const lid = 'tld-' + t.id + '-' + Date.now();
                t.appendRaw(`<div id="${lid}" class="text-[#525252] dark:text-white/70 animate-pulse">▌</div>`);
                try {
                    const res = await fetch(termUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: JSON.stringify({ command: cmd, term_id: t.id })
                    });
                    const data = await res.json();
                    document.getElementById(lid)?.remove();
                    if (data.cwd && data.cwd !== t.cwd) t.updatePrompt(data.cwd);
                    if (data.error) {
                        t.appendRaw(`<div class="text-rose-400 dark:text-rose-300 mb-1">${ideEsc(data.error)}</div>`);
                    } else if (data.output && data.output.trim() !== '') {
                        const cls = data.exit_code !== 0 ? 'text-rose-300' : 'text-[#E3EAF1]';
                        t.appendRaw(`<pre class="${cls} whitespace-pre-wrap break-words mb-1 leading-relaxed">${ideEsc(data.output)}</pre>`);
                        ideCaptureProblems(data.output, t.name);
                    }
                } catch (err) {
                    document.getElementById(lid)?.remove();
                    t.appendRaw(`<div class="text-rose-400 dark:text-rose-300 mb-1">Network error: ${ideEsc(err.message)}</div>`);
                }
                t.running = false;
                t.inputEl.focus();
            };
            t.inputEl.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') { e.preventDefault(); t.run(); }
                else if (e.key === 'ArrowUp') { e.preventDefault(); if (t.histIdx < t.history.length - 1) t.inputEl.value = t.history[++t.histIdx]; }
                else if (e.key === 'ArrowDown') { e.preventDefault(); t.histIdx > 0 ? (t.inputEl.value = t.history[--t.histIdx]) : (t.histIdx = -1, t.inputEl.value = ''); }
                else if (e.ctrlKey && e.key === 'l') { e.preventDefault(); Array.from(t.outEl.children).forEach(c => c.remove()); }
            });
            pane.addEventListener('click', () => t.inputEl.focus());
            pane.querySelector('.ide-term-pane-kill')?.addEventListener('click', (e) => { e.stopPropagation(); ideKillTerminal(t); });
            return t;
        }

        function ideKillTerminal(t) {
            const idx = ideTerminals.findIndex(x => x.id === t.id);
            if (idx === -1) return;
            ideTerminals.splice(idx, 1);
            t.paneEl.remove();
            if (ideActiveTerminal && ideActiveTerminal.id === t.id) ideActiveTerminal = ideTerminals[0] || null;
            ideRenderTermTabs();
            ideRenderTermArea();
        }

        function ideRenderTermTabs() {
            const wrap = document.getElementById('ide-term-tabs');
            wrap.innerHTML = '';
            if (!ideTerminals.length) {
                wrap.innerHTML = '<div class="text-[#525252] dark:text-white/70 text-[11px] px-2">Tidak ada terminal — klik <i class="fa-solid fa-plus"></i> untuk membuat</div>';
                return;
            }
            ideTerminals.forEach(t => {
                const chip = document.createElement('button');
                const active = ideActiveTerminal && ideActiveTerminal.id === t.id;
                chip.className = 'flex items-center gap-1.5 px-2.5 h-6 rounded text-[11px] font-mono transition-colors shrink-0 ' + (active ? 'bg-[#12233A] text-white' : 'text-[#525252] dark:text-white/50 hover:text-white hover:bg-[#12233A]');
                chip.innerHTML = `<span class="w-1.5 h-1.5 rounded-full ${t.running ? 'bg-amber-400' : 'bg-emerald-500'}"></span><span class="truncate max-w-[140px]">${ideEsc(t.name)}</span>`;
                chip.onclick = () => { ideActiveTerminal = t; ideRenderTermTabs(); ideRenderTermArea(); setTimeout(() => t.inputEl.focus(), 30); };
                wrap.appendChild(chip);
            });
        }

        function ideRenderTermArea() {
            const area = document.getElementById('ide-term-area');
            area.innerHTML = '';
            if (!ideTerminals.length || !ideActiveTerminal) return;
            ideTerminals.forEach(t => {
                if (t.id === ideActiveTerminal.id || t.split) area.appendChild(t.paneEl);
            });
        }

        function ideActivateTerminal(t) {
            ideActiveTerminal = t;
            ideRenderTermTabs();
            ideRenderTermArea();
            setTimeout(() => t.inputEl.focus(), 30);
        }

        document.getElementById('ide-term-new')?.addEventListener('click', () => {
            ideSetBottomPanel(true);
            ideActivatePanelTab('terminal');
            const t = ideNewTerminal('' + (ideTerminals.length + 1) + ': bash');
            ideTerminals.push(t);
            ideActivateTerminal(t);
        });

        document.getElementById('ide-term-split')?.addEventListener('click', () => {
            ideSetBottomPanel(true);
            ideActivatePanelTab('terminal');
            const t = ideNewTerminal('' + (ideTerminals.length + 1) + ': bash', { split: true });
            ideTerminals.push(t);
            ideActivateTerminal(t);
        });

        document.getElementById('ide-term-kill')?.addEventListener('click', () => {
            if (ideActiveTerminal) ideKillTerminal(ideActiveTerminal);
        });

        document.getElementById('ide-term-send-chat')?.addEventListener('click', () => {
            const t = ideActiveTerminal;
            if (!t) { hotToast('Tidak ada terminal aktif', 'error'); return; }
            const lines = (t.outEl.textContent || '').split('\n').filter(Boolean).slice(-50);
            const input = document.getElementById('grok-chat-input');
            input.value = 'Analisis output terminal ini:\n```\n' + lines.join('\n') + '\n```';
            if (!ideRightOpen) document.getElementById('ide-ai-activity-btn')?.click();
            input.focus();
            hotToast('Output terminal dikirim ke Ryaze AI', 'success');
        });

        // Terminal awal saat IDE dibuka pertama kali
        (function ideInitFirstTerminal() {
            const t = ideNewTerminal('1: bash');
            ideTerminals.push(t);
            ideActiveTerminal = t;
            ideRenderTermTabs();
            ideRenderTermArea();
        })();

        // ── Problems ────────────────────────────────────────────────────────────
        function ideCaptureProblems(text, source) {
            (text || '').split('\n').forEach(line => {
                const l = line.trim();
                if (!l) return;
                if (/(\berror\b|fatal|exception|parse error|syntax error|undefined variable|failed to open)/i.test(l)) {
                    const key = l.slice(0, 140);
                    if (!ideProblems.some(p => p.text === key)) {
                        ideProblems.push({ text: key, source: source || 'terminal' });
                    }
                }
            });
            ideRenderProblems();
        }

        function ideRenderProblems() {
            const host = document.getElementById('ide-panel-problems');
            const count = document.getElementById('problems-count');
            const n = ideProblems.length;
            if (n) { count.textContent = n; count.classList.remove('hidden'); } else { count.classList.add('hidden'); }
            host.innerHTML = `<div class="flex items-center gap-2 px-3 py-2 border-b border-[#2A4463] bg-[#12233A] sticky top-0 z-10">
                <button id="ide-problems-scan" class="ui-btn ui-btn-sm ui-btn-primary"><i class="fa-solid fa-broom mr-1"></i>Scan PHP</button>
                <button id="ide-problems-clear" class="ui-btn ui-btn-sm ui-btn-secondary"><i class="fa-solid fa-trash-can mr-1"></i>Clear</button>
            </div>`;
            if (!n) {
                host.insertAdjacentHTML('beforeend', '<div class="p-6 text-center text-[#525252] dark:text-white/70 text-xs"><i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-300 text-xl mb-2"></i><br>Tidak ada masalah yang terdeteksi.</div>');
            } else {
                ideProblems.forEach((p, i) => {
                    host.insertAdjacentHTML('beforeend', `<div class="flex items-start gap-2 px-3 py-1.5 border-b border-[#0F1D30] text-[11px]">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 dark:text-rose-400 mt-0.5"></i>
                        <div class="flex-1 min-w-0"><div class="text-rose-300 break-all font-mono">${ideEsc(p.text)}</div><div class="text-[#525252] dark:text-white/50 text-[10px]">${ideEsc(p.source)}</div></div>
                        <button data-idx="${i}" class="ide-problem-dismiss text-[#525252] dark:text-white/70 hover:text-white transition-colors shrink-0"><i class="fa-solid fa-xmark"></i></button>
                    </div>`);
                });
                host.querySelectorAll('.ide-problem-dismiss').forEach(b => b.addEventListener('click', () => {
                    ideProblems.splice(+b.dataset.idx, 1);
                    ideRenderProblems();
                }));
            }
            document.getElementById('ide-problems-scan')?.addEventListener('click', ideScanPhp);
            document.getElementById('ide-problems-clear')?.addEventListener('click', () => { ideProblems = []; ideRenderProblems(); });
        }

        function ideScanPhp() {
            const btn = document.getElementById('ide-problems-scan');
            if (!btn || btn.dataset.busy) return;
            btn.dataset.busy = '1';
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i>Scanning...';
            fetch(ideLintUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) { hotToast(data.error, 'error'); return; }
                ideProblems = (data.results || []).map(r => ({ text: r.error || 'Lint error', source: r.file }));
                ideRenderProblems();
                hotToast('Scan PHP selesai', 'success');
            })
            .catch(() => hotToast('Gagal scan PHP', 'error'))
            .finally(() => { btn.innerHTML = orig; delete btn.dataset.busy; });
        }

        // ── Output (laravel.log) ────────────────────────────────────────────────
        function ideRefreshLogTail() {
            const host = document.getElementById('ide-panel-output');
            host.innerHTML = '<div class="p-3 text-[#525252] dark:text-white/50 text-xs"><i class="fa-solid fa-spinner fa-spin mr-2"></i>Membaca log...</div>';
            fetch(ideLogUrl)
                .then(r => r.json())
                .then(data => {
                    host.innerHTML = `<div class="sticky top-0 z-10 flex items-center gap-2 px-3 py-2 border-b border-[#2A4463] bg-[#12233A]">
                        <span class="text-[10px] text-[#525252] dark:text-white/50 font-mono">laravel.log — 250 baris terakhir</span>
                        <button id="ide-log-refresh" class="ui-btn ui-btn-sm ui-btn-secondary ml-auto"><i class="fa-solid fa-rotate-right mr-1"></i>Refresh</button>
                    </div>`;
                    host.insertAdjacentHTML('beforeend', `<pre class="p-3 font-mono text-[11px] text-[#C9D5E2] dark:text-white/50 whitespace-pre-wrap break-words">${ideEsc(data.content || '')}</pre>`);
                    document.getElementById('ide-log-refresh')?.addEventListener('click', ideRefreshLogTail);
                })
                .catch(() => { host.innerHTML = '<div class="p-3 text-rose-400 dark:text-rose-300 text-xs">Gagal membaca log.</div>'; });
        }

        // ── Debug Console ───────────────────────────────────────────────────────
        function ideEnsureDebugTerminal() {
            const host = document.getElementById('ide-panel-debug');
            if (host.dataset.ready) return;
            host.dataset.ready = '1';
            const t = ideNewTerminal('Debug Console', { id: 'ide-term-debug' });
            ideDebugTerminal = t;
            host.appendChild(t.paneEl);
            t.paneEl.style.borderTop = 'none';
            t.appendRaw('<div class="text-[#525252] dark:text-white/50">Debug Console — ketik perintah untuk melihat error/debug output.</div>');
        }

        // ── Ports ───────────────────────────────────────────────────────────────
        function ideRenderPorts() {
            const host = document.getElementById('ide-panel-ports');
            const devPort = '{{ $project->dev_port ?? '' }}';
            const devActive = {{ $project->dev_mode ? 'true' : 'false' }};
            host.innerHTML = `<div class="px-3 py-2 border-b border-[#2A4463] bg-[#12233A] text-[10px] text-[#525252] dark:text-white/50 font-mono">Port aktif project — klik untuk membuka</div>
                <div class="p-3 grid grid-cols-2 gap-2">
                    <div class="border border-[#2A4463] rounded p-3 bg-[#0A1322]">
                        <div class="flex items-center gap-2 mb-2"><i class="fa-solid fa-globe text-[#6BA8DC]"></i><span class="text-[10px] text-[#525252] dark:text-white/50 font-mono">HTTPS / HTTP</span></div>
                        <div class="text-sm text-white font-mono">443 / 80</div>
                        <div class="text-[10px] text-[#525252] dark:text-white/50 truncate mb-2">${ideEsc(projectSlug)}</div>
                        <a href="https://${ideEsc(projectSlug)}" target="_blank" class="ui-btn ui-btn-sm ui-btn-primary inline-block"><i class="fa-solid fa-arrow-up-right-from-square mr-1"></i>Buka</a>
                    </div>
                    <div class="border border-[#2A4463] rounded p-3 bg-[#0A1322]">
                        <div class="flex items-center gap-2 mb-2"><i class="fa-solid fa-microchip text-emerald-400 dark:text-emerald-300"></i><span class="text-[10px] text-[#525252] dark:text-white/50 font-mono">Dev Server</span></div>
                        <div class="text-sm text-white font-mono">${devPort ? ideEsc(devPort) : '—'}</div>
                        <div class="mb-2"><span class="text-[9px] px-1.5 py-0.5 rounded-full ${devActive ? 'bg-emerald-600/30 text-emerald-300' : 'bg-[#2A4463] text-[#8A9BAE] dark:text-white/40'}">${devActive ? 'AKTIF' : 'NONAKTIF'}</span></div>
                        ${devActive && devPort ? `<a href="https://dev${ideEsc(devPort)}.${ideEsc(projectDomainBase)}" target="_blank" class="ui-btn ui-btn-sm ui-btn-success inline-block"><i class="fa-solid fa-arrow-up-right-from-square mr-1"></i>Buka</a>` : '<span class="text-[9px] text-[#525252] dark:text-white/70">Aktifkan dari tab Overview</span>'}
                    </div>
                </div>`;
        }

    </script>

    <style nonce="{{ csp_nonce() }}">
        .scrollbar-hide::-webkit-scrollbar {
            display: none
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none
        }

        /* ── IDE Zen Mode (fullscreen seperti VS Code) ── */
        #panel-ide.ide-zen-panel {
            position: fixed;
            inset: 0;
            z-index: 9999;
            padding: 0 !important;
            background: #0A1322;
        }
        #panel-ide.ide-zen-panel > .flex {
            height: 100vh !important;
            border-radius: 0 !important;
            border: none !important;
        }
        #panel-ide.ide-zen-panel .ide-zen-shell {
            border-radius: 0 !important;
        }

        /* Scrollbar gelap tipis untuk panel IDE */
        #ide-left-sidebar::-webkit-scrollbar,
        #ide-right-panel::-webkit-scrollbar,
        #grok-chat-messages::-webkit-scrollbar {
            width: 6px;
        }
        #ide-left-sidebar::-webkit-scrollbar-thumb,
        #ide-right-panel::-webkit-scrollbar-thumb,
        #grok-chat-messages::-webkit-scrollbar-thumb {
            background: #12233A;
            border-radius: 3px;
        }
        #ide-left-sidebar::-webkit-scrollbar-thumb:hover,
        #ide-right-panel::-webkit-scrollbar-thumb:hover,
        #grok-chat-messages::-webkit-scrollbar-thumb:hover {
            background: #4a4a4a;
        }
    </style>

    <!-- Payment Modal -->
    <div id="paymentModal" tabindex="-1" class="ui-modal-overlay hidden z-[100]">
        <div class="relative bg-white dark:bg-[#0F1D30] shadow-none w-full max-w-md overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b border-[#E3EAF1] dark:border-[#2A4463]">
                <h3 class="ui-modal-title">
                    Pilih Metode Pembayaran
                </h3>
                <button type="button" onclick="closePaymentModal()" class="text-[#8A9BAE] dark:text-white/40 hover:bg-[#E8F0F9] dark:hover:bg-[#2A4463] hover:text-[#0F1A2A] dark:hover:text-white text-sm w-8 h-8 flex justify-center items-center">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="text-center mb-6">
                    <p class="text-sm text-[#525252] dark:text-white/50 font-medium mb-1">Total Tagihan</p>
                    <div class="text-3xl font-black text-[#0F1A2A] dark:text-white">Rp <span id="modalPaymentAmount">0</span></div>
                    <p class="text-xs text-[#8A9BAE] dark:text-white/40 mt-1">Invoice: <span id="modalPaymentInvoice" class="font-mono"></span></p>
                </div>

                <!-- Option 1: QRIS Dinamis (Rekomendasi) -->
                <a id="btnQris" href="#" onclick="closePaymentModal()" class="flex items-center justify-between p-4 border-2 border-emerald-500/40 bg-emerald-500/5 hover:border-emerald-500 hover:bg-emerald-500/10 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center shrink-0 text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <i class="fa-solid fa-qrcode text-xl"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="ui-eyebrow text-[11px] text-sm">QRIS Dinamis</h4>
                                <span class="text-[10px] font-bold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 px-1.5 py-0.5 rounded">INSTAN</span>
                            </div>
                            <p class="text-xs text-[#525252] dark:text-white/50">Scan via m-Banking & e-Wallet (BCA, Mandiri, GoPay, OVO, Dana).</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-emerald-500"></i>
                </a>

                <!-- Option 2: DANA -->
                <div class="p-4 border-2 border-[#EDF2F7] dark:border-[#2A4463] space-y-3 mt-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-[#E8F0F9] dark:bg-[#12233A] flex items-center justify-center shrink-0 text-[#1D5B96] dark:text-[#6BA8DC]">
                            <i class="fa-solid fa-wallet text-xl"></i>
                        </div>
                        <div>
                            <h4 class="ui-eyebrow text-[11px] text-sm">Transfer DANA</h4>
                            <p class="text-xs text-[#525252] dark:text-white/50 font-mono text-lg font-bold mt-1 text-[#0F1A2A] dark:text-white/80">{{ \App\Models\Setting::val('payment_dana', '085157433395') }}</p>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-[#EDF2F7] dark:border-[#2A4463]">
                        <p class="text-[11px] text-[#525252] dark:text-white/50 leading-relaxed mb-3">
                            Setelah melakukan transfer, silakan kirim bukti pembayaran melalui WhatsApp untuk diverifikasi secara manual oleh Admin.
                        </p>
                        <a id="btnWA" href="#" target="_blank" onclick="closePaymentModal()" class="ui-btn ui-btn-success w-full">
                            <i class="fa-brands fa-whatsapp mr-1"></i> Konfirmasi ke Admin
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openPaymentModal(amount, formattedAmount, invoice) {
            document.getElementById('modalPaymentAmount').innerText = formattedAmount;
            document.getElementById('modalPaymentInvoice').innerText = invoice;
            
            let qrisUrl = "{{ url('/payment/qris') }}/" + encodeURIComponent(invoice);
            let btnQris = document.getElementById('btnQris');
            if (btnQris) btnQris.href = qrisUrl;

            let adminWa = @json(\App\Models\Setting::val('contact_whatsapp', ''));
            let waMessage = `Halo Admin, saya ingin konfirmasi pembayaran untuk Invoice *${invoice}* sebesar *Rp ${formattedAmount}* via DANA. Berikut lampiran buktinya:`;
            let waUrl = `https://wa.me/62${adminWa}?text=${encodeURIComponent(waMessage)}`;
            document.getElementById('btnWA').href = waUrl;

            document.getElementById('paymentModal').classList.remove('hidden');
        }
        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }
    </script>

    <!-- ApexCharts for Resource Monitoring -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        (function() {
            function renderChart() {
                // Polling for ApexCharts to ensure it's loaded before rendering (Fix for PJAX)
                if (typeof ApexCharts === 'undefined') {
                    setTimeout(renderChart, 100);
                    return;
                }
                
                // Generate dummy 24h data for CPU & RAM
                const cpuData = [];
                const ramData = [];
                const categories = [];
                let now = new Date();
                for(let i = 24; i >= 0; i--) {
                    let d = new Date(now.getTime() - (i * 60 * 60 * 1000));
                    categories.push(d.getHours() + ':00');
                    cpuData.push(Math.floor(Math.random() * (80 - 10 + 1)) + 10);
                    ramData.push(Math.floor(Math.random() * (90 - 40 + 1)) + 40);
                }

                var options = {
                    series: [{
                        name: 'CPU Usage (%)',
                        data: cpuData
                    }, {
                        name: 'RAM Usage (%)',
                        data: ramData
                    }],
                    chart: {
                        height: 250,
                        type: 'area',
                        toolbar: { show: false },
                        fontFamily: 'inherit'
                    },
                    colors: ['#6366f1', '#34d399'], // indigo-500, emerald-400
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 2 },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
                            opacityTo: 0.05,
                            stops: [0, 100]
                        }
                    },
                    xaxis: {
                        categories: categories,
                        labels: { style: { colors: '#94A3B4', fontSize: '10px' } },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        min: 0,
                        max: 100,
                        labels: { style: { colors: '#94A3B4', fontSize: '10px' } },
                    },
                    grid: {
                        borderColor: '#EDF2F7',
                        strokeDashArray: 4,
                        yaxis: { lines: { show: true } }
                    },
                    legend: { show: false },
                    tooltip: { theme: 'light' }
                };

                var chart = new ApexCharts(document.querySelector("#resourceChart"), options);
                chart.render();
            }
            
            renderChart();
        })();
    </script>
    </x-ui.page-layout>
@endsection
