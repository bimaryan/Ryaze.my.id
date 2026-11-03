@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- ── USER HOSTING – Deploy Proyek Baru ───────────────────────── --}}
        <x-ui.page-header
            title="Deploy Proyek Baru"
            eyebrow="Panel Klien · Deploy"
            status="READY"
            subtitle="Impor repository Git atau unggah arsip ZIP proyek Anda."
            icon="fa-plus"
            iconColor="emerald">
            <x-slot:actions>
                <a href="{{ route('user_hosting.dashboard') }}"
                    class="ui-btn ui-btn-secondary">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="mx-auto mt-6">
            <form action="{{ route('user_hosting.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- ── STEP 1: Metode Deploy ── --}}
                <div class="bg-white dark:bg-[#0F1D30] p-6 border border-[#E3EAF1] dark:border-[#2A4463]">
                    <h3 class="ui-eyebrow text-[11px] mb-4 flex items-center gap-2.5">
                        <span class="w-6 h-6 bg-[#12233A] dark:bg-[#1D5B96] text-white flex items-center justify-center text-xs font-mono font-bold">1</span>
                        Pilih Metode Deploy
                    </h3>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <label class="relative cursor-pointer flex-1 group">
                            <input type="radio" name="source_type" value="repo" id="source_repo" class="peer hidden" checked>
                            <div class="h-full px-5 py-4 border border-[#E3EAF1] dark:border-[#2A4463] peer-checked:border-[#1D5B96] peer-checked:bg-[#E8F0F9] dark:peer-checked:bg-[#1D5B96]/20 dark:peer-checked:border-[#6BA8DC] hover:border-[#1D5B96] transition-all flex items-center gap-4">
                                <div class="w-11 h-11 bg-[#12233A] border border-[#1D5B96] flex items-center justify-center shrink-0">
                                    <i class="fa-brands fa-github text-xl text-white"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-[#0F1A2A] dark:text-white text-sm">Git Repository</p>
                                    <p class="text-[11px] font-mono text-[#8A9BAE] dark:text-white/40 mt-0.5">Clone dari repo Git Anda sendiri</p>
                                </div>
                                <div class="ml-auto shrink-0 w-5 h-5 border border-[#C9D5E2] dark:border-[#24384F] flex items-center justify-center transition-colors group-has-[:checked]:border-[#1D5B96] group-has-[:checked]:bg-[#1D5B96]">
                                    <div class="w-2 h-2 bg-white rounded-full opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></div>
                                </div>
                            </div>
                        </label>
                        <label class="relative cursor-pointer flex-1 group">
                            <input type="radio" name="source_type" value="upload" id="source_upload" class="peer hidden">
                            <div class="h-full px-5 py-4 border border-[#E3EAF1] dark:border-[#2A4463] peer-checked:border-[#1D5B96] peer-checked:bg-[#E8F0F9] dark:peer-checked:bg-[#1D5B96]/20 dark:peer-checked:border-[#6BA8DC] hover:border-[#1D5B96] transition-all flex items-center gap-4">
                                <div class="w-11 h-11 bg-[#12233A] border border-[#1D5B96] flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-file-zipper text-xl text-white"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-[#0F1A2A] dark:text-white text-sm flex items-center gap-2">
                                        Upload File ZIP
                                        <span class="border border-[#E3EAF1] dark:border-[#2A4463] text-[#737373] dark:text-white/45 font-mono text-[10px] uppercase tracking-[0.15em] font-bold px-1.5 py-0.5">New</span>
                                    </p>
                                    <p class="text-[11px] font-mono text-[#8A9BAE] dark:text-white/40 mt-0.5">Upload website langsung tanpa Git</p>
                                </div>
                                <div class="ml-auto shrink-0 w-5 h-5 border border-[#C9D5E2] dark:border-[#24384F] flex items-center justify-center transition-colors group-has-[:checked]:border-[#1D5B96] group-has-[:checked]:bg-[#1D5B96]">
                                    <div class="w-2 h-2 bg-white rounded-full opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- ── STEP 2a: Sumber Repository (jika pilih repo) ── --}}
                <div id="section_repo" class="bg-white dark:bg-[#0F1D30] p-6 border border-[#E3EAF1] dark:border-[#2A4463]">
                    <h3 class="ui-eyebrow text-[11px] mb-4 flex items-center gap-2.5">
                        <span class="w-6 h-6 bg-[#12233A] dark:bg-[#1D5B96] text-white flex items-center justify-center text-xs font-mono font-bold">2</span>
                        Sumber Repository
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-mono font-bold uppercase tracking-wider text-[#525252] dark:text-white/70 mb-1.5">URL Git Repository <span class="text-rose-500">*</span></label>
                            <input type="url" name="repo_source" id="input_repo_source"
                                placeholder="https://github.com/username/my-project"
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#2A4463] px-4 py-2.5 text-sm font-mono text-[#0F1A2A] dark:text-white focus:border-[#1D5B96] dark:focus:border-[#6BA8DC] outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-mono font-bold uppercase tracking-wider text-[#525252] dark:text-white/70 mb-1.5">Branch</label>
                            <input type="text" name="branch" id="input_branch" value="main"
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#2A4463] px-4 py-2.5 text-sm font-mono text-[#0F1A2A] dark:text-white focus:border-[#1D5B96] dark:focus:border-[#6BA8DC] outline-none transition">
                            <p class="text-[11px] font-mono text-[#8A9BAE] dark:text-white/40 mt-1">Cabang Git yang akan di-build (misal: main, master, atau production).</p>
                        </div>
                    </div>
                </div>


                {{-- ── STEP 2b: Upload ZIP (jika pilih upload) ── --}}
                <div id="section_upload" class="bg-white dark:bg-[#0F1D30] p-6 border border-[#E3EAF1] dark:border-[#2A4463] hidden">
                    <h3 class="ui-eyebrow text-[11px] mb-1 flex items-center gap-2.5">
                        <span class="w-6 h-6 bg-[#12233A] dark:bg-[#1D5B96] text-white flex items-center justify-center text-xs font-mono font-bold">2</span>
                        Unggah File ZIP
                    </h3>
                    <p class="text-xs font-mono text-[#8A9BAE] dark:text-white/40 mb-5 ml-8">Arsip ZIP berisi semua file website Anda (HTML, PHP, hasil build, dsb.). Diekstrak otomatis saat deploy.</p>

                    <div id="upload_zone"
                        class="relative border-2 border-dashed border-[#C9D5E2] dark:border-[#2A4463] hover:border-[#1D5B96] hover:bg-[#E8F0F9] dark:hover:bg-[#1A2F49] p-10 text-center transition-all cursor-pointer">
                        <input type="file" name="project_zip" id="input_project_zip" accept=".zip,application/zip,application/x-zip-compressed" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div id="upload_empty" class="pointer-events-none">
                            <div class="w-14 h-14 mx-auto bg-[#E8F0F9] dark:bg-[#1D5B96]/20 border border-[#C9D5E2] dark:border-[#1D5B96]/40 flex items-center justify-center mb-3 text-[#1D5B96] dark:text-[#6BA8DC]">
                                <i class="fa-solid fa-cloud-arrow-up text-xl"></i>
                            </div>
                            <p class="font-bold text-[#0F1A2A] dark:text-white text-sm font-mono uppercase tracking-wider">Pilih File ZIP</p>
                            <p class="text-[11px] font-mono text-[#8A9BAE] dark:text-white/40 mt-1">atau seret & lepas di sini &mdash; maks. 50 MB</p>
                        </div>
                        <div id="upload_selected" class="pointer-events-none hidden">
                            <div class="w-14 h-14 mx-auto bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center mb-3">
                                <i class="fa-solid fa-file-zipper text-xl text-emerald-600 dark:text-emerald-300"></i>
                            </div>
                            <p id="upload_file_name" class="font-bold text-[#0F1A2A] dark:text-white text-sm font-mono break-all px-4"></p>
                            <p id="upload_file_size" class="text-[11px] font-mono text-emerald-600 dark:text-emerald-400 font-medium mt-1"></p>
                        </div>
                    </div>
                </div>

                {{-- ── STEP 3: Konfigurasi Proyek ── --}}
                <div class="bg-white dark:bg-[#0F1D30] p-6 border border-[#E3EAF1] dark:border-[#2A4463]">
                    <h3 class="ui-eyebrow text-[11px] mb-4 flex items-center gap-2.5">
                        <span id="step_config_num" class="w-6 h-6 bg-[#12233A] dark:bg-[#1D5B96] text-white flex items-center justify-center text-xs font-mono font-bold">3</span>
                        Konfigurasi Proyek
                    </h3>

                    <div class="mb-5">
                        <label class="block text-xs font-mono font-bold uppercase tracking-wider text-[#525252] dark:text-white/70 mb-1.5">Nama Proyek & Domain <span class="text-rose-500">*</span></label>
                        <div class="flex flex-col sm:flex-row shadow-none">
                            <input type="text" name="project_name" required placeholder="my-awesome-app"
                                id="input_project_name"
                                class="flex-1 w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#2A4463] px-4 py-2.5 text-sm font-mono text-[#0F1A2A] dark:text-white focus:border-[#1D5B96] dark:focus:border-[#6BA8DC] outline-none transition">
                            
                            <select name="domain_extension" id="select_domain_ext" class="bg-[#F4F8FC] dark:bg-[#12233A] border border-[#C9D5E2] dark:border-[#2A4463] sm:border-l-0 px-4 py-2.5 text-xs font-mono uppercase text-[#0F1A2A] dark:text-white focus:border-[#1D5B96] dark:focus:border-[#6BA8DC] outline-none transition cursor-pointer">
                                <option value=".ryaze.my.id">.ryaze.my.id</option>
                                <option value=".ryz.my.id">.ryz.my.id</option>
                                <option value=".safetalkai.my.id">.safetalkai.my.id</option>
                                <option value=".ryaze.cloud">.ryaze.cloud</option>
                            </select>
                        </div>
                        <div class="mt-2 text-[11px] font-mono text-[#8A9BAE] dark:text-white/40 flex items-center">
                            <i class="fa-solid fa-link mr-1.5"></i> Preview: <span id="domain_preview" class="text-[#1D5B96] dark:text-[#6BA8DC] ml-1 font-mono font-bold">my-awesome-app.ryaze.my.id</span>
                        </div>
                    </div>

                    {{-- Framework (hanya tampil saat mode repo) --}}
                    <div id="framework_section">
                        <label class="block text-xs font-mono font-bold uppercase tracking-wider text-[#525252] dark:text-white/70 mb-3">Pilih Framework <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @php
                            $availableFrameworks = explode(',', \App\Models\Setting::val('available_frameworks', 'html,php,laravel,react,nextjs,node,vue'));
                            $availableFrameworks = array_map('trim', $availableFrameworks);
                            $frameworkIcons = [
                                'html' => ['icon' => 'fa-brands fa-html5', 'color' => 'text-orange-500', 'name' => 'HTML Statis'],
                                'php' => ['icon' => 'fa-brands fa-php', 'color' => 'text-[#1D5B96] dark:text-[#6BA8DC]', 'name' => 'PHP Native'],
                                'laravel' => ['icon' => 'fa-brands fa-laravel', 'color' => 'text-red-500', 'name' => 'Laravel'],
                                'react' => ['icon' => 'fa-brands fa-react', 'color' => 'text-sky-500', 'name' => 'React JS'],
                                'nextjs' => ['icon' => 'fa-brands fa-node-js', 'color' => 'text-[#0F1A2A] dark:text-white', 'name' => 'Next.js'],
                                'node' => ['icon' => 'fa-brands fa-node', 'color' => 'text-emerald-500', 'name' => 'Node.js'],
                                'vue' => ['icon' => 'fa-brands fa-vuejs', 'color' => 'text-emerald-500', 'name' => 'Vue JS'],
                            ];
                            @endphp

                            @foreach($availableFrameworks as $index => $fw)
                                @php
                                    $fwKey = strtolower($fw);
                                    $info = $frameworkIcons[$fwKey] ?? ['icon' => 'fa-solid fa-code', 'color' => 'text-[#525252]', 'name' => strtoupper($fw)];
                                @endphp
                                
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="framework" value="{{ $fw }}" class="peer hidden" {{ $index === 0 ? 'required' : '' }}>
                                    <div class="p-3 border border-[#E3EAF1] dark:border-[#2A4463] peer-checked:border-[#1D5B96] peer-checked:bg-[#E8F0F9] dark:peer-checked:bg-[#1D5B96]/20 dark:peer-checked:border-[#6BA8DC] hover:border-[#1D5B96] transition-all text-center">
                                        <i class="{{ $info['icon'] }} text-2xl {{ $info['color'] }} mb-1.5 block"></i>
                                        <p class="font-mono text-xs font-semibold text-[#0F1A2A] dark:text-white">{{ $info['name'] }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4">
                    <a href="{{ route('user_hosting.dashboard') }}" class="ui-btn ui-btn-secondary">Batal</a>
                    
                    @if (Auth::user()->hasActiveHostingSubscription())
                        <button type="submit" class="ui-btn ui-btn-primary">
                            <i class="fa-solid fa-rocket"></i> Deploy Sekarang
                        </button>
                    @else
                        <a href="{{ route('user_hosting.billing') }}" class="ui-btn ui-btn-primary">
                            <i class="fa-solid fa-credit-card"></i> Langganan Untuk Deploy
                        </a>
                    @endif
                </div>
            </form>
        </div>
        
<script nonce="{{ csp_nonce() }}">
(function() {
    const sectionRepo     = document.getElementById('section_repo');
    const frameworkSection= document.getElementById('framework_section');
    const repoUrl         = document.getElementById('input_repo_source');
    const projectNameInput= document.getElementById('input_project_name');
    const domainPreview   = document.getElementById('domain_preview');

    function updateDomainPreview() {
        var name = projectNameInput.value.trim().toLowerCase().replace(/[^a-z0-9-]/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
        if (!name) name = 'my-awesome-app';
        
        var ext = document.getElementById('select_domain_ext');
        var extVal = ext ? ext.value : '.ryaze.my.id';
        
        domainPreview.textContent = name + extVal;
    }

    projectNameInput.addEventListener('input', updateDomainPreview);
    var selectDomainExt = document.getElementById('select_domain_ext');
    if (selectDomainExt) {
        selectDomainExt.addEventListener('change', updateDomainPreview);
    }

    // --- Toggle antara mode Repo / Upload ---
    const sectionUpload  = document.getElementById('section_upload');
    const uploadInput    = document.getElementById('input_project_zip');
    const uploadZone     = document.getElementById('upload_zone');
    const uploadEmpty    = document.getElementById('upload_empty');
    const uploadSelected = document.getElementById('upload_selected');

    function updateUploadInfo() {
        if (!uploadInput.files || !uploadInput.files.length) {
            if (uploadEmpty) uploadEmpty.classList.remove('hidden');
            if (uploadSelected) uploadSelected.classList.add('hidden');
            return;
        }
        const file = uploadInput.files[0];
        const nameEl = document.getElementById('upload_file_name');
        const sizeEl = document.getElementById('upload_file_size');
        if (nameEl) nameEl.textContent = file.name;
        if (sizeEl) sizeEl.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
        if (uploadEmpty) uploadEmpty.classList.add('hidden');
        if (uploadSelected) uploadSelected.classList.remove('hidden');
    }

    if (uploadInput) {
        uploadInput.addEventListener('change', updateUploadInfo);
        ['dragover', 'dragenter'].forEach(evt => {
            uploadZone.addEventListener(evt, (e) => {
                e.preventDefault();
                uploadZone.classList.add('border-[#1D5B96]', 'bg-[#F4F8FC]', 'dark:bg-[#1D5B96]/10');
                uploadZone.classList.remove('border-[#C9D5E2]', 'dark:border-[#2A4463]');
            });
        });
        ['dragleave', 'drop'].forEach(evt => {
            uploadZone.addEventListener(evt, (e) => {
                e.preventDefault();
                uploadZone.classList.remove('border-[#1D5B96]', 'bg-[#F4F8FC]', 'dark:bg-[#1D5B96]/10');
            });
        });
        uploadZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length) {
                uploadInput.files = dt.files;
                updateUploadInfo();
            }
        });
    }

    document.querySelectorAll('input[name="source_type"]').forEach(radio => {
        radio.addEventListener('change', (e) => {
            const isUpload = e.target.value === 'upload';

            // Toggle visibility section
            if(sectionRepo) sectionRepo.classList.toggle('hidden', isUpload);
            if(sectionUpload) sectionUpload.classList.toggle('hidden', !isUpload);
            if(frameworkSection) frameworkSection.classList.remove('hidden');

            // repo_source hanya wajib pada mode repository
            if(repoUrl) {
                isUpload ? repoUrl.removeAttribute('required') : repoUrl.setAttribute('required', 'required');
            }
            const firstFramework = document.querySelector('input[name="framework"]');
            if (firstFramework) firstFramework.setAttribute('required', 'required');
        });
    });
})();
</script>

    </x-ui.page-layout>
@endsection
