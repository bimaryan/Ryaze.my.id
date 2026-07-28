@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Panduan & Dokumentasi" 
            subtitle="Pelajari cara menggunakan seluruh fitur hosting Ryaze dengan optimal." 
            icon="fa-book-open" 
            iconColor="indigo">
        </x-ui.page-header>

        <div class="flex flex-col md:flex-row gap-8 items-start relative">
            
            <!-- Sidebar Table of Contents (Sticky on Desktop) -->
            <aside class="w-full md:w-64 flex-shrink-0 md:sticky top-24 bg-white md:bg-transparent rounded-2xl md:rounded-none shadow-sm md:shadow-none border border-slate-200 md:border-none p-4 md:p-0 z-10 mb-6 md:mb-0">
                <h3 class="font-bold text-slate-800 mb-4 px-2 flex items-center gap-2">
                    <i class="fa-solid fa-list-ul text-indigo-500"></i> Daftar Isi
                </h3>
                <nav class="flex flex-col space-y-1 overflow-y-auto max-h-[70vh] custom-scrollbar pr-2">
                    <a href="#deploy" class="text-sm font-medium text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 px-3 py-2.5 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-rocket w-4 text-center"></i> Deploy Project
                    </a>
                    <a href="#filemanager" class="text-sm font-medium text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 px-3 py-2.5 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fa-regular fa-folder-open w-4 text-center"></i> File Manager
                    </a>
                    <a href="#env" class="text-sm font-medium text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 px-3 py-2.5 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-sliders w-4 text-center"></i> Environment Variables
                    </a>
                    <a href="#settings" class="text-sm font-medium text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 px-3 py-2.5 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved w-4 text-center"></i> Keamanan & Settings
                    </a>
                    <a href="#terminal" class="text-sm font-medium text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 px-3 py-2.5 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-terminal w-4 text-center"></i> Web Terminal
                    </a>
                    <a href="#storage" class="text-sm font-medium text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 px-3 py-2.5 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-hard-drive w-4 text-center"></i> Manajemen Storage
                    </a>
                    <a href="#redeploy" class="text-sm font-medium text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 px-3 py-2.5 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-rotate w-4 text-center"></i> Redeploy Manual
                    </a>
                    <a href="#devserver" class="text-sm font-medium text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 px-3 py-2.5 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-laptop-code w-4 text-center"></i> Dev Server
                    </a>
                    <a href="#python" class="text-sm font-medium text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 px-3 py-2.5 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fa-brands fa-python w-4 text-center"></i> Aturan Khusus Python
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-10 w-full">
                
                <div class="prose prose-slate prose-indigo max-w-none">
                    
                    <div class="text-slate-500 text-lg leading-relaxed mb-10 pb-6 border-b border-slate-100">
                        Selamat datang di dokumentasi resmi Hosting Ryaze! Referensi lengkap untuk membantu Anda membangun, mendeploy, dan mengelola aplikasi modern dengan infrastruktur berkinerja tinggi kami.
                    </div>

                    <!-- Section 1 -->
                    <section id="deploy" class="mb-14 scroll-mt-28">
                        <h2 class="text-2xl font-bold text-slate-800 mb-4 flex items-center gap-3">
                            <i class="fa-solid fa-rocket text-indigo-500"></i> Menambahkan & Deploy Project
                        </h2>
                        <p class="text-slate-600 mb-4">Ryaze mendukung deployment otomatis dari Git maupun penggunaan template instan. Proses deployment berlangsung dalam hitungan detik menggunakan teknologi isolasi container.</p>
                        
                        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200">
                            <h3 class="font-bold text-slate-700 mb-3 text-sm">Langkah Deployment:</h3>
                            <ul class="space-y-2 text-slate-600 text-sm">
                                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-emerald-500 mt-1"></i> Buka menu <strong>Dashboard Hosting</strong> lalu klik <strong>Deploy Proyek Baru</strong>.</li>
                                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-emerald-500 mt-1"></i> Pilih metode <strong>Git Repository</strong> (untuk kode Anda sendiri) atau <strong>Template Instan</strong>.</li>
                                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-emerald-500 mt-1"></i> Masukkan <strong>Nama Project</strong> yang akan menjadi alamat subdomain (contoh: <code class="bg-slate-200 text-slate-700 px-1.5 py-0.5 rounded text-xs mx-1 font-mono">app-saya.ryaze.my.id</code>).</li>
                                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-emerald-500 mt-1"></i> Klik <strong>Deploy Sekarang</strong> dan pantau log instalasi secara real-time.</li>
                            </ul>
                        </div>
                    </section>

                    <!-- Section 2 -->
                    <section id="filemanager" class="mb-14 scroll-mt-28">
                        <h2 class="text-2xl font-bold text-slate-800 mb-4 flex items-center gap-3">
                            <i class="fa-regular fa-folder-open text-sky-500"></i> Mengelola File Manager
                        </h2>
                        <p class="text-slate-600 mb-4">Akses dan ubah kode Anda langsung dari browser. File manager Ryaze dilengkapi dengan code editor terintegrasi.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="border border-slate-200 rounded-xl p-4 flex items-start gap-3">
                                <div class="bg-indigo-100 text-indigo-600 p-2 rounded-lg"><i class="fa-solid fa-code"></i></div>
                                <div>
                                    <h4 class="font-bold text-sm text-slate-700">Code Editor</h4>
                                    <p class="text-xs text-slate-500 mt-1">Edit file teks langsung di browser tanpa aplikasi tambahan.</p>
                                </div>
                            </div>
                            <div class="border border-slate-200 rounded-xl p-4 flex items-start gap-3">
                                <div class="bg-indigo-100 text-indigo-600 p-2 rounded-lg"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                                <div>
                                    <h4 class="font-bold text-sm text-slate-700">Quick Upload</h4>
                                    <p class="text-xs text-slate-500 mt-1">Upload file pendukung maksimal 10MB per file dengan aman.</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-amber-50/50 border-l-4 border-amber-400 p-4 rounded-r-xl">
                            <div class="flex items-center gap-2 text-amber-800 font-bold mb-1 text-sm">
                                <i class="fa-solid fa-shield-cat"></i> Keamanan Server
                            </div>
                            <p class="text-sm text-amber-700">Untuk mencegah injeksi malware, kami memblokir upload ekstensi executable tertentu (seperti .php, .sh) via File Manager. Gunakan push via <strong>Git</strong> untuk file tersebut.</p>
                        </div>
                    </section>

                    <!-- Section 3 -->
                    <section id="env" class="mb-14 scroll-mt-28">
                        <h2 class="text-2xl font-bold text-slate-800 mb-4 flex items-center gap-3">
                            <i class="fa-solid fa-sliders text-emerald-500"></i> Environment Variables (.env)
                        </h2>
                        <p class="text-slate-600 mb-3">Amankan rahasia aplikasi Anda seperti API Keys dan kredensial Database melalui antarmuka Environment Variable.</p>
                        <ul class="list-disc list-inside text-sm text-slate-600 space-y-1.5 ml-2">
                            <li>Variabel ini dienkripsi dengan aman di server kami.</li>
                            <li>Buka tab <strong>Pengaturan .env</strong> pada dashboard project Anda.</li>
                            <li>Perubahan akan langsung diterapkan ke aplikasi Anda setelah disimpan (beberapa framework Node.js/Python mungkin memerlukan restart server).</li>
                        </ul>
                    </section>

                    <!-- Section 4 -->
                    <section id="settings" class="mb-14 scroll-mt-28">
                        <h2 class="text-2xl font-bold text-slate-800 mb-4 flex items-center gap-3">
                            <i class="fa-solid fa-shield-halved text-rose-500"></i> Keamanan & Settings
                        </h2>
                        <p class="text-slate-600 mb-4">Tingkatkan keamanan dan kontrol akses website Anda dengan satu klik di tab <strong>Settings</strong>.</p>
                        
                        <div class="space-y-3">
                            <div class="bg-white border border-slate-200 p-3 rounded-xl flex items-center gap-3 shadow-sm hover:border-indigo-300 transition-colors">
                                <div class="bg-slate-100 p-2 rounded-lg text-slate-600"><i class="fa-solid fa-tools w-5 text-center"></i></div>
                                <div>
                                    <h4 class="font-bold text-sm text-slate-700">Maintenance Mode</h4>
                                    <p class="text-xs text-slate-500">Mengalihkan pengunjung ke halaman perbaikan yang elegan saat Anda mengupdate sistem.</p>
                                </div>
                            </div>
                            <div class="bg-white border border-slate-200 p-3 rounded-xl flex items-center gap-3 shadow-sm hover:border-indigo-300 transition-colors">
                                <div class="bg-slate-100 p-2 rounded-lg text-slate-600"><i class="fa-solid fa-lock w-5 text-center"></i></div>
                                <div>
                                    <h4 class="font-bold text-sm text-slate-700">Force HTTPS</h4>
                                    <p class="text-xs text-slate-500">Memaksa semua lalu lintas menggunakan enkripsi SSL tingkat tinggi (otomatis terpasang).</p>
                                </div>
                            </div>
                            <div class="bg-white border border-slate-200 p-3 rounded-xl flex items-center gap-3 shadow-sm hover:border-indigo-300 transition-colors">
                                <div class="bg-slate-100 p-2 rounded-lg text-slate-600"><i class="fa-solid fa-bolt w-5 text-center"></i></div>
                                <div>
                                    <h4 class="font-bold text-sm text-slate-700">Under Attack Mode</h4>
                                    <p class="text-xs text-slate-500">Rate-limiting agresif untuk menangkis serangan bot atau DDoS skala kecil.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Section 5 -->
                    <section id="terminal" class="mb-14 scroll-mt-28">
                        <h2 class="text-2xl font-bold text-slate-800 mb-4 flex items-center gap-3">
                            <i class="fa-solid fa-terminal text-slate-700"></i> Web Terminal
                        </h2>
                        <p class="text-slate-600 mb-4">Ryaze menyediakan Cloud Terminal bagi developer untuk mengeksekusi perintah langsung di root aplikasi.</p>
                        
                        <div class="bg-slate-900 rounded-xl p-5 shadow-inner">
                            <div class="flex items-center gap-2 mb-3 border-b border-slate-700 pb-2">
                                <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                                <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                                <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                                <span class="text-slate-400 text-xs ml-2 font-mono">Allowed Commands</span>
                            </div>
                            <div class="font-mono text-sm text-emerald-400 leading-relaxed">
                                ls, cat, grep, mkdir, rm, cp, mv, touch <br>
                                npm, node, npx, yarn <br>
                                composer, php artisan ... <br>
                                python, python3, pip, pip3 <br>
                                git, curl
                            </div>
                        </div>
                        
                        <p class="text-sm text-slate-500 mt-3"><i class="fa-solid fa-circle-info text-sky-500 mr-1"></i> <strong>Sistem Keamanan:</strong> Perintah berbahaya, chain (&&), pipe (|), dan ekskalasi hak istimewa (sudo) akan diblokir otomatis oleh Firewall kami.</p>
                    </section>

                    <!-- Section 6 -->
                    <section id="storage" class="mb-14 scroll-mt-28">
                        <h2 class="text-2xl font-bold text-slate-800 mb-4 flex items-center gap-3">
                            <i class="fa-solid fa-hard-drive text-amber-500"></i> Manajemen Storage
                        </h2>
                        <p class="text-slate-600 mb-4">Kapasitas penyimpanan (Storage) dihitung secara <strong>Global per Akun</strong>. Anda dapat mendeploy berapapun project selama batas storage belum terpenuhi.</p>
                        
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                            <h3 class="font-bold text-slate-700 mb-2 flex items-center gap-2 text-sm">
                                <i class="fa-regular fa-lightbulb text-amber-500"></i> Praktik Terbaik Menghemat Storage
                            </h3>
                            <ul class="list-disc list-inside text-sm text-slate-600 space-y-1.5 ml-2">
                                <li><strong>Abaikan Vendor/Node Modules:</strong> Jangan *commit* folder <code class="font-mono text-xs bg-slate-200 px-1 rounded">node_modules</code> ke Git. Biarkan Ryaze menginstalnya secara optimal.</li>
                                <li><strong>Hapus Log Berlebihan:</strong> Pantau dan hapus file log di direktori <code class="font-mono text-xs bg-slate-200 px-1 rounded">storage/logs</code> Anda.</li>
                                <li><strong>Optimasi Aset:</strong> Kompres ukuran gambar dan video sebelum didorong ke server.</li>
                            </ul>
                        </div>
                    </section>

                    <!-- Section 7 -->
                    <section id="redeploy" class="mb-14 scroll-mt-28">
                        <h2 class="text-2xl font-bold text-slate-800 mb-4 flex items-center gap-3">
                            <i class="fa-solid fa-rotate text-blue-500"></i> Redeploy Manual
                        </h2>
                        <p class="text-slate-600">Ketika Anda melakukan pembaruan <i>(push)</i> kode baru ke repositori GitHub/GitLab Anda, Anda cukup masuk ke detail project dan menekan tombol <strong>Redeploy</strong>. Ryaze akan secara cerdas menarik perubahan terbaru tanpa waktu henti (Zero Downtime).</p>
                    </section>

                    <!-- Section 8 -->
                    <section id="devserver" class="mb-14 scroll-mt-28">
                        <h2 class="text-2xl font-bold text-slate-800 mb-4 flex items-center gap-3">
                            <i class="fa-solid fa-laptop-code text-teal-500"></i> Development Server (Live Preview)
                        </h2>
                        <p class="text-slate-600 mb-4">Ucapkan selamat tinggal pada localhost! Anda dapat menjalankan development server (seperti Vite, Next.js Dev, Artisan Serve) langsung di cloud.</p>
                        
                        <div class="bg-teal-50 border border-teal-100 rounded-xl p-4 flex items-start gap-4">
                            <div class="bg-teal-100 text-teal-600 p-2.5 rounded-full"><i class="fa-solid fa-play"></i></div>
                            <div>
                                <h4 class="font-bold text-sm text-teal-800">Cara Menggunakan:</h4>
                                <ol class="list-decimal list-inside text-sm text-teal-700 mt-2 space-y-1 ml-1">
                                    <li>Klik tombol <strong>Dev Server</strong> (ikon Play) di atas dashboard.</li>
                                    <li>Server pengembangan akan berjalan di background.</li>
                                    <li>Akses link unik yang diberikan (misal: <code class="font-mono font-bold bg-teal-100 px-1 rounded">devX.ryaze.my.id</code>).</li>
                                    <li>Lakukan perubahan file via File Manager/Terminal, web akan refresh seketika!</li>
                                </ol>
                            </div>
                        </div>
                    </section>

                    <!-- Section 9 -->
                    <section id="python" class="scroll-mt-28">
                        <h2 class="text-2xl font-bold text-slate-800 mb-4 flex items-center gap-3">
                            <i class="fa-brands fa-python text-yellow-500"></i> Panduan & Aturan Khusus Python
                        </h2>
                        <p class="text-slate-600 mb-4">Ryaze mendukung deployment tingkat lanjut untuk ekosistem Python (Flask, Django, FastAPI, AI/ML) dengan arsitektur isolasi kelas enterprise.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="border border-slate-200 rounded-xl p-4">
                                <h4 class="font-bold text-sm text-slate-700 flex items-center gap-2 mb-2">
                                    <i class="fa-solid fa-box text-indigo-500"></i> Isolasi Virtual Env (Venv)
                                </h4>
                                <p class="text-xs text-slate-500">Saat deploy, sistem otomatis membuat <code class="font-mono bg-slate-100 px-1 rounded">venv</code> dan menginstal <code class="font-mono bg-slate-100 px-1 rounded">requirements.txt</code>. Anda tidak perlu setup manual.</p>
                            </div>
                            
                            <div class="border border-slate-200 rounded-xl p-4">
                                <h4 class="font-bold text-sm text-slate-700 flex items-center gap-2 mb-2">
                                    <i class="fa-solid fa-magic text-fuchsia-500"></i> Terminal Auto-Alias
                                </h4>
                                <p class="text-xs text-slate-500">Jika Anda mengetik <code class="font-mono bg-slate-100 px-1 rounded text-indigo-600">pip install X</code> di Web Terminal, kami mengalihkannya diam-diam ke dalam <i>venv</i> Anda. 100% aman.</p>
                            </div>
                        </div>

                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 mb-5">
                            <h4 class="font-bold text-sm text-slate-700 flex items-center gap-2 mb-2">
                                <i class="fa-solid fa-rocket text-sky-500"></i> Pre-compiled Data Science Library
                            </h4>
                            <p class="text-sm text-slate-600">Untuk memastikan performa deployment yang optimal, kami telah menyediakan <i>pre-compiled binaries</i> (bawaan sistem) untuk library data science utama seperti <strong>Numpy, Pandas, dan Scikit-Learn</strong>. Proses instalasi environment Anda dijamin efisien tanpa membuat CPU bekerja keras.</p>
                        </div>

                        <div class="bg-rose-50/50 border-l-4 border-rose-500 p-5 rounded-r-xl">
                            <div class="flex items-center gap-2 text-rose-800 font-bold mb-2 text-sm">
                                <i class="fa-solid fa-triangle-exclamation"></i> Aturan Binding PORT (Sangat Penting)
                            </div>
                            <p class="text-sm text-rose-700 mb-3">Aplikasi Python Anda <strong>TIDAK BOLEH</strong> mengikat (binding) ke port statis seperti 5000. Anda <strong>wajib</strong> mendengarkan port dinamis yang diberikan dari Environment Variable <code class="font-mono font-bold bg-rose-100 px-1 rounded">PORT</code>.</p>
                            <div class="bg-rose-900 rounded-lg p-3 text-rose-200 font-mono text-xs overflow-x-auto">
                                # Contoh implementasi pada framework Flask:<br>
                                import os<br>
                                port = int(os.environ.get('PORT', 8080))<br>
                                app.run(host='0.0.0.0', port=port)
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </div>
        
        <script nonce="{{ csp_nonce() }}">
            // Script untuk highlight TOC otomatis saat scrolling (Mendukung PJAX)
            (function() {
                const initDocsScroll = function() {
                    const sections = document.querySelectorAll('section[id]');
                    const navLinks = document.querySelectorAll('aside nav a');
                    if (sections.length === 0) return;
                    
                    function highlightNav() {
                        let scrollY = window.scrollY;
                        
                        sections.forEach(current => {
                            const sectionHeight = current.offsetHeight;
                            const sectionTop = current.offsetTop - 150;
                            const sectionId = current.getAttribute('id');
                            
                            if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                                navLinks.forEach(link => {
                                    link.classList.remove('text-indigo-600', 'bg-indigo-50');
                                    link.classList.add('text-slate-600');
                                });
                                const activeLink = document.querySelector('aside nav a[href*=' + sectionId + ']');
                                if(activeLink) {
                                    activeLink.classList.remove('text-slate-600');
                                    activeLink.classList.add('text-indigo-600', 'bg-indigo-50');
                                }
                            }
                        });
                    }
                    
                    // Hapus event listener lama agar tidak menumpuk saat navigasi PJAX
                    if (window._docsScrollHandler) {
                        window.removeEventListener('scroll', window._docsScrollHandler);
                    }
                    window._docsScrollHandler = highlightNav;
                    window.addEventListener('scroll', window._docsScrollHandler);
                    highlightNav(); // Trigger pertama kali
                };
                
                // Eksekusi langsung (untuk PJAX)
                initDocsScroll();
            })();
        </script>
    </x-ui.page-layout>
@endsection
