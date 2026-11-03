@extends('index')

@section('content')
    <x-ui.page-layout>

        {{-- Header --}}
        <x-ui.page-header
            title="Database Manager"
            eyebrow="Web Client"
            subtitle="Kelola tabel dan isi database Anda secara langsung melalui web client."
            icon="fa-server"
            iconColor="indigo">
        </x-ui.page-header>

    {{-- Tabs (MySQL vs PostgreSQL) --}}
    <div class="mb-6 border-b border-[#E3EAF1] dark:border-[#2A4463]">
        <nav class="-mb-px flex gap-6" aria-label="Tabs">
            <button onclick="showDbManagerTab('mysql')" id="btn-tab-mysql"
                class="border-[#1D5B96] dark:border-[#6BA8DC] text-[#1D5B96] dark:text-[#6BA8DC] whitespace-nowrap border-b-2 py-4 px-1 font-mono text-[11px] uppercase tracking-[0.2em] font-semibold">
                MySQL (phpMyAdmin)
            </button>
            <button onclick="showDbManagerTab('pgsql')" id="btn-tab-pgsql"
                class="border-transparent text-[#737373] dark:text-white/40 hover:border-[#C9D5E2] hover:text-[#0F1A2A] dark:hover:text-white whitespace-nowrap border-b-2 py-4 px-1 font-mono text-[11px] uppercase tracking-[0.2em] font-semibold transition-colors">
                PostgreSQL (pgAdmin)
            </button>
        </nav>
    </div>

    {{-- MySQL Tab --}}
    <div id="tab-mysql">
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            @forelse ($databases as $db)
            <div class="ui-panel p-5 sm:p-6 flex flex-col">
                {{-- Cell Head --}}
                <div class="flex items-center justify-between gap-3 mb-5">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="ui-icon-box w-10 h-10">
                            <i class="fa-solid fa-database"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-[#737373] dark:text-white/40 block">Database MySQL</span>
                            <h3 class="ui-eyebrow text-[11px] text-base truncate">{{ $db->db_name }}</h3>
                        </div>
                    </div>
                    <span class="ui-status shrink-0"><span class="ui-status-dot"></span>Active</span>
                </div>

                {{-- Koneksi: baris spesifikasi --}}
                <dl class="border border-[#E3EAF1] dark:border-[#2A4463] bg-[#FAFBFD] dark:bg-[#0F1D30] mb-5">
                    <div class="ui-spec-row">
                        <dt class="ui-eyebrow-muted shrink-0">Host</dt>
                        <dd class="font-mono text-[13px] text-[#0F1A2A] dark:text-white text-right">{{ $db->host }}</dd>
                    </div>
                    <div class="ui-spec-row">
                        <dt class="ui-eyebrow-muted shrink-0">Port</dt>
                        <dd class="font-mono text-[13px] text-[#0F1A2A] dark:text-white text-right">{{ $db->port }}</dd>
                    </div>
                    <div class="ui-spec-row">
                        <dt class="ui-eyebrow-muted shrink-0">User</dt>
                        <dd class="font-mono text-[13px] text-[#0F1A2A] dark:text-white text-right">{{ $db->db_username }}</dd>
                    </div>
                </dl>

                {{-- phpMyAdmin auto-login via POST --}}
                <div class="mt-auto bg-[#F0F6FC] dark:bg-[#0F1D30] border border-[#E3EAF1] dark:border-[#2A4463] p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="text-sm text-[#0F1A2A] dark:text-[#6BA8DC]">
                        <strong>Login Otomatis (MySQL)</strong><br>
                        <span class="text-xs text-[#525252] dark:text-white/50">Klik tombol di samping untuk masuk ke phpMyAdmin database <code class="font-mono bg-[#E8F0F9] dark:bg-[#12233A] border border-[#E3EAF1] dark:border-[#2A4463] px-1">{{ $db->db_name }}</code> tanpa harus mengetik password.</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <form method="POST" action="{{ rtrim(env('PMA_URL', '#'), '/') }}/index.php" target="_blank" class="shrink-0">
                            <input type="hidden" name="pma_username" value="{{ $db->db_username }}">
                            <input type="hidden" name="pma_password" value="{{ $db->db_password }}">
                            <input type="hidden" name="server" value="1">
                            <input type="hidden" name="pma_servername" value="{{ $db->host }}">
                            <button type="submit"
                                class="ui-btn ui-btn-md ui-btn-primary whitespace-nowrap">
                                <i class="fa-solid fa-server"></i>
                                Buka phpMyAdmin
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="ui-panel col-span-full px-6 py-20 text-center flex flex-col items-center justify-center">
                <div class="ui-icon-box w-10 h-10 mb-4">
                    <i class="fa-solid fa-server text-base"></i>
                </div>
                <p class="font-mono text-[12px] uppercase tracking-[0.2em] text-[#94A3B4] dark:text-white/30 mb-2">Belum ada Database MySQL</p>
                <p class="text-xs text-[#525252] dark:text-white/40">Buat database MySQL terlebih dahulu di menu Database.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- PostgreSQL Tab --}}
    <div id="tab-pgsql" class="hidden">
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            @forelse ($pgsqlDatabases as $db)
            <div class="ui-panel p-5 sm:p-6 flex flex-col">
                {{-- Cell Head --}}
                <div class="flex items-center justify-between gap-3 mb-5">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="ui-icon-box w-10 h-10">
                            <i class="fa-solid fa-database"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-[#737373] dark:text-white/40 block">Database PostgreSQL</span>
                            <h3 class="ui-eyebrow text-[11px] text-base truncate">{{ $db->db_name }}</h3>
                        </div>
                    </div>
                    <span class="ui-status shrink-0"><span class="ui-status-dot"></span>Active</span>
                </div>

                {{-- Koneksi: baris spesifikasi --}}
                <dl class="border border-[#E3EAF1] dark:border-[#2A4463] bg-[#FAFBFD] dark:bg-[#0F1D30] mb-5">
                    <div class="ui-spec-row">
                        <dt class="ui-eyebrow-muted shrink-0">Host</dt>
                        <dd class="font-mono text-[13px] text-[#0F1A2A] dark:text-white text-right">{{ $db->host }}</dd>
                    </div>
                    <div class="ui-spec-row">
                        <dt class="ui-eyebrow-muted shrink-0">Port</dt>
                        <dd class="font-mono text-[13px] text-[#0F1A2A] dark:text-white text-right">{{ $db->port }}</dd>
                    </div>
                    <div class="ui-spec-row">
                        <dt class="ui-eyebrow-muted shrink-0">User</dt>
                        <dd class="font-mono text-[13px] text-[#0F1A2A] dark:text-white text-right">{{ $db->db_username }}</dd>
                    </div>
                </dl>

                <div class="mt-auto bg-[#F0F6FC] dark:bg-[#0F1D30] border border-[#E3EAF1] dark:border-[#2A4463] p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="text-sm text-[#0F1A2A] dark:text-white">
                        <strong>Login ke pgAdmin</strong><br>
                        <span class="text-xs text-[#525252] dark:text-white/50">Gunakan kredensial database Anda untuk login ke antarmuka pgAdmin.</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ env('PGA_URL', 'https://pgadmin.ryaze.my.id') }}" target="_blank"
                            class="ui-btn ui-btn-md ui-btn-primary whitespace-nowrap">
                            <i class="fa-solid fa-server"></i>
                            Buka pgAdmin
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="ui-panel col-span-full px-6 py-20 text-center flex flex-col items-center justify-center">
                <div class="ui-icon-box w-10 h-10 mb-4">
                    <i class="fa-solid fa-server text-base"></i>
                </div>
                <p class="font-mono text-[12px] uppercase tracking-[0.2em] text-[#94A3B4] dark:text-white/30 mb-2">Belum ada Database PostgreSQL</p>
                <p class="text-xs text-[#525252] dark:text-white/40">Buat database PostgreSQL terlebih dahulu di menu Database.</p>
            </div>
            @endforelse
        </div>
    </div>

    <script nonce="{{ app('csp_nonce') ?? '' }}">
        function showDbManagerTab(tab) {
            const tabMysql = document.getElementById('tab-mysql');
            const tabPgsql = document.getElementById('tab-pgsql');
            const btnMysql = document.getElementById('btn-tab-mysql');
            const btnPgsql = document.getElementById('btn-tab-pgsql');

            const activeClass = "border-[#1D5B96] dark:border-[#6BA8DC] text-[#1D5B96] dark:text-[#6BA8DC] whitespace-nowrap border-b-2 py-4 px-1 font-mono text-[11px] uppercase tracking-[0.2em] font-semibold".split(' ');
            const inactiveClass = "border-transparent text-[#737373] dark:text-white/40 hover:border-[#C9D5E2] hover:text-[#0F1A2A] dark:hover:text-white whitespace-nowrap border-b-2 py-4 px-1 font-mono text-[11px] uppercase tracking-[0.2em] font-semibold transition-colors".split(' ');

            if (tab === 'mysql') {
                tabMysql.classList.remove('hidden');
                tabPgsql.classList.add('hidden');

                btnMysql.classList.remove(...inactiveClass);
                btnMysql.classList.add(...activeClass);

                btnPgsql.classList.remove(...activeClass);
                btnPgsql.classList.add(...inactiveClass);
            } else {
                tabMysql.classList.add('hidden');
                tabPgsql.classList.remove('hidden');

                btnPgsql.classList.remove(...inactiveClass);
                btnPgsql.classList.add(...activeClass);

                btnMysql.classList.remove(...activeClass);
                btnMysql.classList.add(...inactiveClass);
            }
        }
    </script>

    </x-ui.page-layout>
@endsection
