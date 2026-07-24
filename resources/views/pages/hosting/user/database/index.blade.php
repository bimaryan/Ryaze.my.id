@extends('index')

@section('content')
    <x-ui.page-layout>

        {{-- SweetAlert2 --}}
        {{-- Flash via SweetAlert --}}
        @if ($errors->any())
        <script nonce="{{ app('csp_nonce') }}">
            (function() {
                Swal.fire({
                    icon: 'error', title: 'Validasi Gagal',
                    html: '{!! implode('<br>', array_map('addslashes', $errors->all())) !!}',
                    confirmButtonColor: '#4F46E5', customClass: { popup: 'rounded-xl text-sm' }
                });
            })();
        </script>
        @endif

        {{-- Header --}}
        <x-ui.page-header 
            title="Manajemen Database" 
            subtitle="Kelola database MySQL untuk aplikasi Anda." 
            icon="fa-database" 
            iconColor="purple">
            <x-slot:actions>
                <button id="btn-open-create-modal" class="inline-flex justify-center items-center flex-shrink-0 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    + Buat Database
                </button>
            </x-slot:actions>
        </x-ui.page-header>

    {{-- Database Cards --}}
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
                <button data-action="{{ route('user_hosting.databases.destroy', $db->hashid) }}"
                    class="btn-delete-db text-slate-400 hover:text-rose-500 p-2 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Database">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
            </div>

            <div class="p-5 space-y-4">
                {{-- Host & Port --}}
                <div class="flex items-center justify-between p-3 rounded-xl border border-slate-100 bg-slate-50/50">
                    <div>
                        <span class="text-[11px] text-slate-400 font-bold uppercase tracking-wider block mb-0.5">Host & Port</span>
                        <code class="text-sm font-mono text-slate-700">{{ $db->host }}:{{ $db->port ?? 3306 }}</code>
                    </div>
                    <button class="text-slate-300 hover:text-indigo-500 p-1.5 rounded transition-colors btn-copy" data-copy="{{ $db->host }}:{{ $db->port ?? 3306 }}" title="Copy">
                        <i class="fa-regular fa-copy text-sm"></i>
                    </button>
                </div>

                {{-- Username & Password --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex flex-col border border-slate-100 rounded-xl p-3 bg-white relative group">
                        <span class="text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-1">Username</span>
                        <code class="text-sm font-mono text-slate-800 break-all" id="user-{{ $db->hashid }}">{{ $db->db_username }}</code>
                        <button class="absolute top-2 right-2 text-slate-300 hover:text-indigo-600 bg-white rounded p-1 opacity-0 group-hover:opacity-100 transition-opacity btn-copy" data-copy="{{ $db->db_username }}">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                    <div class="flex flex-col border border-slate-100 rounded-xl p-3 bg-white relative group">
                        <span class="text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-1">Password</span>
                        <input type="password" readonly value="{{ $db->db_password }}"
                            id="pass-{{ $db->hashid }}"
                            class="text-sm font-mono text-slate-800 bg-transparent outline-none w-full">
                        <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity bg-white">
                            <button class="text-slate-300 hover:text-slate-600 p-1 rounded btn-toggle-pass" data-target="pass-{{ $db->hashid }}">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                            <button class="text-slate-300 hover:text-indigo-600 p-1 rounded btn-copy" data-copy="{{ $db->db_password }}">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- REST API Settings --}}
                <div class="mt-3 grid grid-cols-1 lg:grid-cols-2 gap-3">
                    {{-- Endpoint URL --}}
                    <div class="flex flex-col border border-slate-100 rounded-xl p-3 bg-white relative group">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">API Endpoint</span>
                            <span class="text-[10px] text-emerald-500 font-bold bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100"><i class="fa-solid fa-bolt"></i> Auto REST</span>
                        </div>
                        <input type="text" readonly value="{{ url('/api/v1/db/' . $db->hashid) }}"
                            class="text-sm font-mono text-slate-800 bg-transparent outline-none w-full pr-12">
                        <div class="absolute bottom-2 right-2 bg-white">
                            <button class="text-slate-400 hover:text-indigo-600 p-1.5 rounded btn-copy" data-copy="{{ url('/api/v1/db/' . $db->hashid) }}" title="Copy URL">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    {{-- API Key --}}
                    <div class="flex flex-col border border-slate-100 rounded-xl p-3 bg-white relative group">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">REST API Key</span>
                        </div>
                        <input type="password" readonly value="{{ $db->api_key ?? 'Generate API Key dulu...' }}"
                            id="apikey-{{ $db->hashid }}"
                            class="text-sm font-mono text-slate-800 bg-transparent outline-none w-full pr-24">
                        <div class="absolute bottom-2 right-2 flex gap-1 bg-white">
                            <button onclick="event.preventDefault(); document.getElementById('gen-apikey-{{ $db->hashid }}').submit();" class="text-slate-400 hover:text-emerald-600 p-1.5 rounded" title="Regenerate API Key">
                                <i class="fa-solid fa-rotate-right"></i>
                            </button>
                            <button class="text-slate-400 hover:text-slate-600 p-1.5 rounded btn-toggle-pass" data-target="apikey-{{ $db->hashid }}" title="Toggle Visibility">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                            <button class="text-slate-400 hover:text-indigo-600 p-1.5 rounded btn-copy" data-copy="{{ $db->api_key }}" title="Copy API Key">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                        <form id="gen-apikey-{{ $db->hashid }}" action="{{ route('user_hosting.databases.apikey', $db->hashid) }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>

                <hr class="border-slate-100">

                {{-- phpMyAdmin auto-login via POST --}}
                <div class="bg-indigo-50/50 border border-indigo-100 p-4 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="text-sm text-indigo-900">
                        <strong>Manajemen Database</strong><br>
                        <span class="opacity-80 text-xs">Pilih aksi untuk database <code class="font-mono bg-indigo-100 px-1 rounded">{{ $db->db_name }}</code>.</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button onclick="openApiTesterModal('{{ url('/api/v1/db/' . $db->hashid) }}', '{{ $db->api_key }}')" class="bg-orange-500 text-white hover:bg-orange-600 transition-all text-xs font-bold py-2 px-3 rounded-2xl shadow-sm flex items-center gap-1.5 whitespace-nowrap">
                            <i class="fa-solid fa-flask"></i> Test API
                        </button>
                        <button onclick="openApiDocsModal('{{ url('/api/v1/db/' . $db->hashid) }}', '{{ $db->api_key }}')" class="bg-slate-800 text-white hover:bg-slate-900 transition-all text-xs font-bold py-2 px-3 rounded-2xl shadow-sm flex items-center gap-1.5 whitespace-nowrap">
                            <i class="fa-solid fa-code"></i> API Docs
                        </button>
                        <a href="{{ route('user_hosting.databases.export', $db->hashid) }}" data-pjax="0" target="_blank" class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-all text-xs font-bold py-2 px-3 rounded-2xl shadow-sm flex items-center gap-1.5 whitespace-nowrap">
                            <i class="fa-solid fa-download"></i> Export (.sql)
                        </a>
                        <button onclick="openImportModal('{{ $db->hashid }}', '{{ $db->db_name }}')" class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-all text-xs font-bold py-2 px-3 rounded-2xl shadow-sm flex items-center gap-1.5 whitespace-nowrap">
                            <i class="fa-solid fa-upload"></i> Import
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-16 text-center flex flex-col items-center">
            <div class="w-16 h-16 bg-slate-100 text-slate-300 rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-server text-3xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-700 mb-1">Belum ada Database</h3>
            <p class="text-slate-500 text-sm">Klik "Buat Database" untuk memulai.</p>
        </div>
        @endforelse
    </div>


<div id="createDbModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(15,23,42,0.5)">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        {{-- Modal Header --}}
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                <i class="fa-solid fa-database text-indigo-500"></i> Buat Database Baru
            </h3>
            <button class="btn-close-modal"
                class="text-slate-400 hover:text-rose-500 transition-colors w-8 h-8 flex items-center justify-center rounded-lg hover:bg-rose-50">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('user_hosting.databases.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            {{-- Nama DB --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Nama Database <span class="text-rose-500">*</span>
                </label>
                <div class="flex rounded-xl overflow-hidden border border-slate-300 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                    <span class="inline-flex items-center px-3 bg-slate-100 text-slate-500 text-sm font-mono border-r border-slate-300 whitespace-nowrap">
                        ryz_{{ Auth::id() }}_
                    </span>
                    <input type="text" name="db_name" required pattern="[A-Za-z0-9_]+" placeholder="myapp" maxlength="15"
                        class="flex-1 font-mono w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                </div>
            </div>

            {{-- Username --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Username MySQL <span class="text-rose-500">*</span>
                </label>
                <div class="flex rounded-xl overflow-hidden border border-slate-300 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                    <span class="inline-flex items-center px-3 bg-slate-100 text-slate-500 text-sm font-mono border-r border-slate-300 whitespace-nowrap">
                        ryz_{{ Auth::id() }}_
                    </span>
                    <input type="text" name="db_username" required pattern="[A-Za-z0-9_]+" placeholder="user" maxlength="15"
                        class="flex-1 font-mono w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                </div>
            </div>

            {{-- Password --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-sm font-semibold text-slate-700">Password <span class="text-rose-500">*</span></label>
                    <button type="button" id="btn-generate-password" class="text-xs text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Generate
                    </button>
                </div>
                <div class="relative flex rounded-xl overflow-hidden border border-slate-300 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                    <span class="inline-flex items-center px-3 bg-slate-100 text-slate-500 text-sm font-mono border-r border-slate-300 whitespace-nowrap">
                        ryz_{{ Auth::id() }}_
                    </span>
                    <input type="text" name="db_password" id="modalPassword" required maxlength="32"
                        placeholder="Masukkan password kuat"
                        class="flex-1 pr-10 font-mono w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    <button type="button" id="btn-copy-modal-password" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-indigo-600" title="Copy">
                        <i class="fa-regular fa-copy"></i>
                    </button>
                </div>
                <p class="mt-1.5 text-[11px] text-slate-400">
                    <i class="fa-solid fa-shield-halved"></i> Simpan password ini. Otomatis ditambah prefix.
                </p>
            </div>

            {{-- Footer --}}
            <div class="pt-2 flex justify-end gap-3 border-t border-slate-100">
                <button type="button" class="btn-close-modal text-slate-600 bg-white border border-slate-300 rounded-xl px-5 py-2.5 text-sm font-medium hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-sm transition-all flex items-center gap-2">
                    <i class="fa-solid fa-server"></i> Buat Database
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Import Database --}}
<div id="importDbModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(15,23,42,0.5)">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        {{-- Modal Header --}}
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                <i class="fa-solid fa-upload text-indigo-500"></i> Import Database
            </h3>
            <button onclick="closeImportModal()" class="text-slate-400 hover:text-rose-500 transition-colors w-8 h-8 flex items-center justify-center rounded-lg hover:bg-rose-50">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="importForm" action="" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            
            <div class="bg-indigo-50/50 border border-indigo-100 p-4 rounded-xl text-sm text-indigo-900 mb-4">
                <p>Mengimpor file <strong>.sql</strong> ke database: <br><code id="importDbNameDisplay" class="font-mono bg-indigo-100 px-1 rounded font-bold"></code></p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    File SQL <span class="text-rose-500">*</span>
                </label>
                <input type="file" name="sql_file" accept=".sql,.txt" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="text-[11px] text-slate-500 mt-1 mb-3">Maksimal ukuran file: 50MB.</p>

                <label class="flex items-center gap-2 mt-4 cursor-pointer">
                    <input type="checkbox" name="drop_tables" value="1" class="rounded border-slate-300 text-rose-500 focus:ring-rose-500">
                    <span class="text-sm text-slate-700">Kosongkan database sebelum Import <br><span class="text-[11px] text-rose-500 font-semibold">(Bahaya: Menghapus seluruh tabel yang sudah ada)</span></span>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="closeImportModal()"
                    class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Upload & Import
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal API Docs --}}
<div id="apiDocsModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4" style="background:rgba(15,23,42,0.7)">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50 shrink-0">
            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                <i class="fa-solid fa-book-open text-indigo-500"></i> Dokumentasi API
            </h3>
            <button onclick="closeApiDocsModal()" class="text-slate-400 hover:text-rose-500 transition-colors w-8 h-8 flex items-center justify-center rounded-lg hover:bg-rose-50">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6 overflow-y-auto space-y-6">
            <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-xl text-sm text-indigo-900">
                <strong>REST API Siap Pakai!</strong><br>
                Anda bisa menggunakan Endpoint ini untuk melakukan operasi CRUD (Create, Read, Update, Delete) pada tabel apapun di database Anda secara langsung.
            </div>

            {{-- GET Request --}}
            <div>
                <h4 class="font-bold text-slate-700 mb-2 flex items-center gap-2">
                    <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-xs">GET</span> Membaca Data (Read)
                </h4>
                <p class="text-sm text-slate-600 mb-2">Contoh membaca semua baris dari tabel <code>users</code>:</p>
                <div class="bg-slate-800 rounded-xl p-4 relative group">
                    <pre class="text-xs font-mono overflow-x-auto"><code id="code-get" class="text-emerald-400"></code></pre>
                    <button class="absolute top-2 right-2 text-slate-400 hover:text-white p-1 rounded btn-copy opacity-0 group-hover:opacity-100 transition-opacity bg-slate-700/50" data-copy-target="code-get">
                        <i class="fa-regular fa-copy"></i>
                    </button>
                </div>
            </div>

            {{-- POST Request --}}
            <div>
                <h4 class="font-bold text-slate-700 mb-2 flex items-center gap-2">
                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs">POST</span> Menambah Data (Create)
                </h4>
                <div class="bg-slate-800 rounded-xl p-4 relative group">
                    <pre class="text-xs font-mono overflow-x-auto"><code id="code-post" class="text-blue-400"></code></pre>
                    <button class="absolute top-2 right-2 text-slate-400 hover:text-white p-1 rounded btn-copy opacity-0 group-hover:opacity-100 transition-opacity bg-slate-700/50" data-copy-target="code-post">
                        <i class="fa-regular fa-copy"></i>
                    </button>
                </div>
            </div>

            {{-- Javascript Fetch --}}
            <div>
                <h4 class="font-bold text-slate-700 mb-2 flex items-center gap-2">
                    <i class="fa-brands fa-js text-yellow-500"></i> Contoh Javascript (Fetch)
                </h4>
                <div class="bg-slate-800 rounded-xl p-4 relative group">
                    <pre class="text-xs font-mono overflow-x-auto"><code id="code-js" class="text-yellow-400"></code></pre>
                    <button class="absolute top-2 right-2 text-slate-400 hover:text-white p-1 rounded btn-copy opacity-0 group-hover:opacity-100 transition-opacity bg-slate-700/50" data-copy-target="code-js">
                        <i class="fa-regular fa-copy"></i>
                    </button>
                </div>
            </div>

            {{-- Postman --}}
            <div>
                <h4 class="font-bold text-slate-700 mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-rocket text-orange-500"></i> Penggunaan di Postman
                </h4>
                <div class="bg-orange-50/50 border border-orange-100 rounded-xl p-4 text-sm text-slate-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Buka aplikasi Postman dan buat Request baru.</li>
                        <li>Masukkan URL: <code class="bg-white border border-slate-200 px-1.5 py-0.5 rounded font-mono text-xs text-orange-600" id="postman-url"></code></li>
                        <li>Buka tab <strong>Headers</strong>, tambahkan Key <code class="font-bold bg-white border border-slate-200 px-1 rounded">x-api-key</code> dengan Value API Key Anda.</li>
                        <li>(Opsional) Jika POST/PUT, buka tab <strong>Body</strong> &rarr; pilih <strong>raw</strong> &rarr; pilih <strong>JSON</strong> untuk mengirim data.</li>
                    </ul>
                </div>
            </div>

            <div class="text-sm text-slate-500 border-t border-slate-100 pt-4">
                <i class="fa-solid fa-circle-info"></i> Ganti <code>users</code> pada URL dengan nama tabel Anda sendiri.
            </div>
        </div>
    </div>
</div>

{{-- Modal API Tester --}}
<div id="apiTesterModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4" style="background:rgba(15,23,42,0.8)">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col h-[90vh]">
        {{-- Header --}}
        <div class="px-4 py-3 border-b border-slate-200 flex justify-between items-center bg-slate-50 shrink-0">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-rocket text-orange-500"></i> API Tester
            </h3>
            <button onclick="closeApiTesterModal()" class="text-slate-400 hover:text-rose-500 transition-colors w-7 h-7 flex items-center justify-center rounded hover:bg-slate-200">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="flex-1 flex flex-col overflow-hidden bg-white">
            {{-- Top Bar (URL & Send) --}}
            <div class="p-4 border-b border-slate-200 bg-slate-50 shrink-0">
                <div class="flex gap-2">
                    <div class="flex-1 flex border border-slate-300 rounded overflow-hidden bg-white shadow-sm focus-within:border-orange-500 focus-within:ring-1 focus-within:ring-orange-500">
                        <select id="tester-method" class="bg-slate-100 border-r border-slate-300 px-3 py-2 text-sm font-bold text-slate-700 outline-none w-[100px] cursor-pointer text-emerald-600">
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                            <option value="PUT">PUT</option>
                            <option value="DELETE">DELETE</option>
                        </select>
                        <span id="tester-base-url" class="px-2 py-2 text-sm text-slate-400 font-mono border-r border-slate-200 whitespace-nowrap bg-slate-50 flex items-center hidden md:flex"></span>
                        <input type="text" id="tester-path" value="users" class="flex-1 px-3 py-2 text-sm font-mono outline-none w-full" placeholder="Enter request URL">
                    </div>
                    <button id="tester-send-btn" class="bg-orange-600 hover:bg-orange-700 text-white font-semibold px-6 py-2 rounded text-sm transition-colors flex items-center justify-center gap-2">
                        Send
                    </button>
                </div>
            </div>
            
            {{-- Main split area --}}
            <div class="flex-1 flex flex-col lg:flex-row overflow-hidden">
                {{-- Request Area --}}
                <div class="flex-1 flex flex-col border-b lg:border-b-0 lg:border-r border-slate-200">
                    {{-- Tabs --}}
                    <div class="flex border-b border-slate-200 bg-slate-50 px-2 shrink-0">
                        <button class="px-4 py-2 text-xs font-semibold text-orange-600 border-b-2 border-orange-500">Body</button>
                        <button class="px-4 py-2 text-xs font-semibold text-slate-400 hover:text-slate-500 cursor-not-allowed" title="Otomatis disisipkan x-api-key">Headers</button>
                    </div>
                    {{-- Body Content --}}
                    <div class="flex-1 p-0 relative">
                        <textarea id="tester-body" class="absolute inset-0 w-full h-full resize-none p-4 text-sm font-mono outline-none bg-slate-50 opacity-50" placeholder='{\n  "kolom": "nilai"\n}' disabled></textarea>
                    </div>
                </div>

                {{-- Response Area --}}
                <div class="flex-1 flex flex-col bg-slate-50">
                    {{-- Tabs & Status --}}
                    <div class="flex justify-between items-center border-b border-slate-200 bg-slate-100 px-2 shrink-0 h-[37px]">
                        <div class="flex h-full">
                            <button class="px-4 py-2 text-xs font-semibold text-slate-700 border-b-2 border-slate-400">Response</button>
                        </div>
                        <div class="flex items-center gap-3 text-xs pr-2 hidden" id="tester-status-container">
                            <span class="text-slate-500">Status: <span id="tester-status" class="font-bold"></span></span>
                            <span class="text-slate-500">Time: <span id="tester-time" class="font-bold text-emerald-600"></span></span>
                            <span class="text-slate-500">Size: <span id="tester-size" class="font-bold text-emerald-600"></span></span>
                        </div>
                    </div>
                    {{-- Response Content --}}
                    <div class="flex-1 p-0 relative bg-[#1e1e1e]">
                        <div id="tester-empty-state" class="absolute inset-0 flex flex-col items-center justify-center text-slate-400">
                            <i class="fa-regular fa-paper-plane text-4xl mb-3 opacity-20"></i>
                            <span class="text-sm">Hit Send to get a response</span>
                        </div>
                        <pre id="tester-response" class="absolute inset-0 w-full h-full overflow-auto p-4 text-xs font-mono text-emerald-400 hidden"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Form Delete (Hidden) --}}
<form id="deleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>


<script nonce="{{ app('csp_nonce') }}">
    (function() {
    // ── Modal ──────────────────────────────────────────────────────────────────
    function openCreateModal() {
        document.getElementById('createDbModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeCreateModal() {
        document.getElementById('createDbModal').classList.add('hidden');
        document.body.style.overflow = '';
    }
    // Klik luar modal → tutup
    var createDbModalEl = document.getElementById('createDbModal');
    if (createDbModalEl) createDbModalEl.addEventListener('click', function(e) {
        if (e.target === this) closeCreateModal();
    });

    // ── Delete dengan SweetAlert ───────────────────────────────────────────────
    function confirmDelete(actionUrl) {
        Swal.fire({
            icon: 'warning',
            title: 'Hapus Database?',
            text: 'Semua tabel dan data akan terhapus permanen dan tidak bisa dikembalikan.',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: '<i class="fa-solid fa-trash-can mr-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-xl text-sm' }
        }).then(result => {
            if (result.isConfirmed) {
                // Gunakan fetch() langsung agar tidak di-intercept oleh Pjax
                var csrfToken = document.querySelector('meta[name="csrf-token"]') 
                    ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    : '{{ csrf_token() }}';
                
                fetch(actionUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                }).then(function(response) {
                    if (response.ok || response.redirected) {
                        window.location.reload();
                    } else {
                        response.json().then(function(data) {
                            Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message || 'Terjadi kesalahan.', customClass: { popup: 'rounded-xl text-sm' } });
                        }).catch(function() {
                            window.location.reload();
                        });
                    }
                }).catch(function() {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Tidak bisa menghubungi server.', customClass: { popup: 'rounded-xl text-sm' } });
                });
            }
        });
    }

    // ── Copy to clipboard dengan Toast ────────────────────────────────────────
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            hotToast('Berhasil disalin!', 'success');
        }).catch(() => {
            hotToast('Gagal menyalin', 'error');
        });
    }

    // ── Toggle password visibility ─────────────────────────────────────────────
    function togglePass(inputId, btn) {
        var input = document.getElementById(inputId);
        var icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    // ── Generate random password ───────────────────────────────────────────────
    function generatePassword() {
        var chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        var pass = '';
        for (var i = 0; i < 16; i++) pass += chars[Math.floor(Math.random() * chars.length)];
        document.getElementById('modalPassword').value = pass;
        hotToast('Password di-generate!', 'success');
    }

    // ── Copy password di modal ─────────────────────────────────────────────────
    function copyModalPassword() {
        var pass = document.getElementById('modalPassword').value;
        if (!pass) { hotToast('Password masih kosong', 'warning'); return; }
        navigator.clipboard.writeText(pass).then(() => {
            hotToast('Password disalin!', 'success');
        });
    }

    // ── CSP Compliant Event Listeners ──────────────────────────────────────────
    // Create Modal Open
    var btnOpenModal = document.getElementById('btn-open-create-modal');
    if (btnOpenModal) btnOpenModal.addEventListener('click', openCreateModal);

    // Create Modal Close
    [].forEach.call(document.querySelectorAll('.btn-close-modal'), function(btn) {
        btn.addEventListener('click', closeCreateModal);
    });

    // Delete DB
    [].forEach.call(document.querySelectorAll('.btn-delete-db'), function(btn) {
        btn.addEventListener('click', function(e) {
            confirmDelete(e.currentTarget.getAttribute('data-action'));
        });
    });

    // Copy
    [].forEach.call(document.querySelectorAll('.btn-copy'), function(btn) {
        btn.addEventListener('click', function(e) {
            copyToClipboard(e.currentTarget.getAttribute('data-copy'));
        });
    });

    // Toggle Password
    [].forEach.call(document.querySelectorAll('.btn-toggle-pass'), function(btn) {
        btn.addEventListener('click', function(e) {
            togglePass(e.currentTarget.getAttribute('data-target'), e.currentTarget);
        });
    });

    // Import Modal logic
    window.openImportModal = function(hashid, dbName) {
        document.getElementById('importDbNameDisplay').innerText = dbName;
        var importUrl = "{{ route('user_hosting.databases.import', ':id') }}".replace(':id', hashid);
        document.getElementById('importForm').action = importUrl;
        document.getElementById('importDbModal').classList.remove('hidden');
    };
    window.closeImportModal = function() {
        document.getElementById('importDbModal').classList.add('hidden');
    };

    // Generate Password
    var btnGenPass = document.getElementById('btn-generate-password');
    if (btnGenPass) btnGenPass.addEventListener('click', generatePassword);

    // Copy Modal Password
    var btnCopyModalPass = document.getElementById('btn-copy-modal-password');
    if (btnCopyModalPass) btnCopyModalPass.addEventListener('click', copyModalPassword);
    
    // API Docs Modal Logic
    window.openApiDocsModal = function(endpointUrl, apiKey) {
        if (!apiKey) apiKey = "API_KEY_ANDA";
        
        var curlGet = `curl -X GET "${endpointUrl}/records/users" \\
     -H "x-api-key: ${apiKey}"`;
        
        var curlPost = `curl -X POST "${endpointUrl}/records/users" \\
     -H "x-api-key: ${apiKey}" \\
     -H "Content-Type: application/json" \\
     -d '{"nama":"Bima", "email":"bima@example.com"}'`;

        var jsFetch = `fetch("${endpointUrl}/records/users", {
  method: "GET",
  headers: {
    "x-api-key": "${apiKey}"
  }
})
.then(res => res.json())
.then(data => console.log(data));`;

        document.getElementById('code-get').textContent = curlGet;
        document.getElementById('code-post').textContent = curlPost;
        document.getElementById('code-js').textContent = jsFetch;
        document.getElementById('postman-url').textContent = endpointUrl + "/records/users";
        
        document.getElementById('apiDocsModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };
    
    window.closeApiDocsModal = function() {
        document.getElementById('apiDocsModal').classList.add('hidden');
        document.body.style.overflow = '';
    };

    // Make copy button in modal work
    [].forEach.call(document.querySelectorAll('[data-copy-target]'), function(btn) {
        btn.addEventListener('click', function() {
            var targetId = this.getAttribute('data-copy-target');
            var text = document.getElementById(targetId).innerText;
            copyToClipboard(text);
        });
    });
    
    // API Tester Logic
    let currentApiKey = '';
    let currentEndpoint = '';

    window.openApiTesterModal = function(endpointUrl, apiKey) {
        currentApiKey = apiKey || '';
        currentEndpoint = endpointUrl;
        document.getElementById('tester-base-url').textContent = endpointUrl;
        
        document.getElementById('tester-empty-state').classList.remove('hidden');
        document.getElementById('tester-response').classList.add('hidden');
        document.getElementById('tester-status-container').classList.add('hidden');
        
        document.getElementById('apiTesterModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    window.closeApiTesterModal = function() {
        document.getElementById('apiTesterModal').classList.add('hidden');
        document.body.style.overflow = '';
    };

    var testerMethodEl = document.getElementById('tester-method');
    if (testerMethodEl) testerMethodEl.addEventListener('change', function(e) {
        var bodyInput = document.getElementById('tester-body');
        var methodColor = document.getElementById('tester-method');
        
        // Postman colors for select
        methodColor.classList.remove('text-emerald-600', 'text-orange-500', 'text-blue-500', 'text-rose-600');
        if (e.target.value === 'GET') methodColor.classList.add('text-emerald-600');
        else if (e.target.value === 'POST') methodColor.classList.add('text-orange-500');
        else if (e.target.value === 'PUT') methodColor.classList.add('text-blue-500');
        else if (e.target.value === 'DELETE') methodColor.classList.add('text-rose-600');
        
        if (e.target.value === 'GET' || e.target.value === 'DELETE') {
            bodyInput.disabled = true;
            bodyInput.classList.add('opacity-50', 'bg-slate-50');
            bodyInput.classList.remove('bg-white');
        } else {
            bodyInput.disabled = false;
            bodyInput.classList.remove('opacity-50', 'bg-slate-50');
            bodyInput.classList.add('bg-white');
        }
    });
    if (testerMethodEl) testerMethodEl.dispatchEvent(new Event('change'));

    var testerSendBtn = document.getElementById('tester-send-btn');
    if (testerSendBtn) testerSendBtn.addEventListener('click', async function() {
        var btn = this;
        var method = document.getElementById('tester-method').value;
        var path = document.getElementById('tester-path').value;
        var bodyStr = document.getElementById('tester-body').value;
        
        var responseBox = document.getElementById('tester-response');
        var emptyState = document.getElementById('tester-empty-state');
        var statusContainer = document.getElementById('tester-status-container');
        var statusBadge = document.getElementById('tester-status');
        var timeBadge = document.getElementById('tester-time');
        var sizeBadge = document.getElementById('tester-size');
        
        var url = currentEndpoint + path;
        
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        btn.disabled = true;
        
        emptyState.classList.add('hidden');
        responseBox.classList.remove('hidden');
        statusContainer.classList.add('hidden');
        
        responseBox.classList.remove('text-rose-400', 'text-emerald-400');
        responseBox.classList.add('text-slate-400');
        responseBox.textContent = "Sending request...";
        
        var startTime = performance.now();
        
        try {
            var options = {
                method: method,
                headers: {
                    'x-api-key': currentApiKey,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            };
            
            if ((method === 'POST' || method === 'PUT') && bodyStr) {
                options.body = bodyStr;
            }
            
            var res = await fetch(url, options);
            var data = await res.text();
            
            var endTime = performance.now();
            var duration = Math.round(endTime - startTime) + ' ms';
            var size = (new TextEncoder().encode(data).length / 1024).toFixed(2) + ' KB';
            
            statusContainer.classList.remove('hidden');
            statusBadge.textContent = res.status + ' ' + res.statusText;
            timeBadge.textContent = duration;
            sizeBadge.textContent = size;
            
            statusBadge.classList.remove('text-emerald-500', 'text-rose-500', 'text-orange-500');
            if (res.ok) {
                statusBadge.classList.add('text-emerald-500');
            } else if (res.status >= 500) {
                statusBadge.classList.add('text-rose-500');
            } else {
                statusBadge.classList.add('text-orange-500');
            }
            
            responseBox.classList.remove('text-slate-400', 'text-emerald-400', 'text-rose-400');
            if (res.ok) {
                responseBox.classList.add('text-emerald-400');
            } else {
                responseBox.classList.add('text-rose-400');
            }
            
            try {
                var json = JSON.parse(data);
                responseBox.textContent = JSON.stringify(json, null, 2);
            } catch(e) {
                responseBox.textContent = data;
            }
            
        } catch (error) {
            statusContainer.classList.remove('hidden');
            statusBadge.textContent = "Error";
            statusBadge.classList.add('text-rose-500');
            timeBadge.textContent = "-";
            sizeBadge.textContent = "-";
            
            responseBox.classList.remove('text-slate-400', 'text-emerald-400');
            responseBox.classList.add('text-rose-400');
            responseBox.textContent = "Failed to fetch: " + error.toString();
        }
        
        btn.innerHTML = 'Send';
        btn.disabled = false;
    });

    })();
</script>

    </x-ui.page-layout>
@endsection
