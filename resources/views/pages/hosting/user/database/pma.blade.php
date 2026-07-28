@extends('index')

@section('content')
    <x-ui.page-layout>

        {{-- Header --}}
        <x-ui.page-header 
            title="Database Manager" 
            subtitle="Kelola tabel dan isi database Anda secara langsung melalui web client." 
            icon="fa-server" 
            iconColor="indigo">
        </x-ui.page-header>

    {{-- Tabs (MySQL vs PostgreSQL) --}}
    <div class="mb-6 border-b border-slate-200">
        <nav class="-mb-px flex gap-6" aria-label="Tabs">
            <button onclick="showDbManagerTab('mysql')" id="btn-tab-mysql"
                class="border-indigo-500 text-indigo-600 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">
                MySQL (phpMyAdmin)
            </button>
            <button onclick="showDbManagerTab('pgsql')" id="btn-tab-pgsql"
                class="border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors">
                PostgreSQL (pgAdmin)
            </button>
        </nav>
    </div>

    {{-- MySQL Tab --}}
    <div id="tab-mysql">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @forelse ($databases as $db)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
                {{-- Card Header --}}
                <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-4 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center">
                            <i class="fa-solid fa-database text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-base">{{ $db->db_name }}</h3>
                            <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">Active</span>
                        </div>
                    </div>
                </div>

                <div class="p-5 space-y-4">
                    {{-- phpMyAdmin auto-login via POST --}}
                    <div class="bg-indigo-50/50 border border-indigo-100 p-4 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="text-sm text-indigo-900">
                            <strong>Login Otomatis (MySQL)</strong><br>
                            <span class="opacity-80 text-xs">Klik tombol di samping untuk masuk ke phpMyAdmin database <code class="font-mono bg-indigo-100 px-1 rounded">{{ $db->db_name }}</code> tanpa harus mengetik password.</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <form method="POST" action="{{ rtrim(env('PMA_URL', '#'), '/') }}/index.php" target="_blank" class="shrink-0">
                                <input type="hidden" name="pma_username" value="{{ $db->db_username }}">
                                <input type="hidden" name="pma_password" value="{{ $db->db_password }}">
                                <input type="hidden" name="server" value="1">
                                <input type="hidden" name="pma_servername" value="{{ $db->host }}">
                                <button type="submit"
                                    class="bg-indigo-600 border border-indigo-600 text-white hover:bg-indigo-700 hover:border-indigo-700 transition-all text-xs font-bold py-2.5 px-4 rounded-xl shadow-sm flex items-center gap-2 whitespace-nowrap">
                                    <i class="fa-solid fa-server"></i>
                                    Buka phpMyAdmin
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-16 text-center flex flex-col items-center">
                <div class="w-16 h-16 bg-slate-100 text-slate-300 rounded-full flex items-center justify-center mb-4">
                    <i class="fa-solid fa-server text-3xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-700 mb-1">Belum ada Database MySQL</h3>
                <p class="text-slate-500 text-sm">Buat database MySQL terlebih dahulu di menu Database.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- PostgreSQL Tab --}}
    <div id="tab-pgsql" class="hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @forelse ($pgsqlDatabases as $db)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
                {{-- Card Header --}}
                <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-4 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center">
                            <i class="fa-solid fa-database text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-base">{{ $db->db_name }}</h3>
                            <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">Active</span>
                        </div>
                    </div>
                </div>

                <div class="p-5 space-y-4">
                    <div class="bg-blue-50/50 border border-blue-100 p-4 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="text-sm text-blue-900">
                            <strong>Login ke pgAdmin</strong><br>
                            <span class="opacity-80 text-xs">Gunakan kredensial database Anda untuk login ke antarmuka pgAdmin.</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ env('PGA_URL', 'https://pgadmin.ryaze.my.id') }}" target="_blank"
                                class="bg-blue-600 border border-blue-600 text-white hover:bg-blue-700 hover:border-blue-700 transition-all text-xs font-bold py-2.5 px-4 rounded-xl shadow-sm flex items-center gap-2 whitespace-nowrap">
                                <i class="fa-solid fa-server"></i>
                                Buka pgAdmin
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-16 text-center flex flex-col items-center">
                <div class="w-16 h-16 bg-slate-100 text-slate-300 rounded-full flex items-center justify-center mb-4">
                    <i class="fa-solid fa-server text-3xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-700 mb-1">Belum ada Database PostgreSQL</h3>
                <p class="text-slate-500 text-sm">Buat database PostgreSQL terlebih dahulu di menu Database.</p>
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
            
            const activeClass = "border-indigo-500 text-indigo-600 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium".split(' ');
            const inactiveClass = "border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors".split(' ');

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
