@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Panduan & Dokumentasi" 
            subtitle="Pelajari cara menggunakan seluruh fitur hosting Ryaze dengan optimal." 
            icon="fa-book-open" 
            iconColor="indigo">
        </x-ui.page-header>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
            
            <!-- Content Body -->
            <div class="space-y-16 text-slate-700 leading-relaxed max-w-5xl">

                <!-- Intro -->
                <div class="text-slate-500 text-lg leading-relaxed mb-6">
                    Selamat datang di dokumentasi resmi Hosting Ryaze! Panduan esensial untuk mendeploy, mengelola, dan menskalakan aplikasi modern Anda di atas infrastruktur berkinerja tinggi kami.
                </div>

                <hr class="border-slate-100">

                <!-- Section 1: Deploy -->
                <section>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 mb-5">1. Deploy Project</h2>
                    <p class="mb-5 text-slate-600 font-light">
                        Ryaze mendukung deployment otomatis dari Git maupun penggunaan template instan. Proses deployment berlangsung dalam hitungan detik menggunakan teknologi isolasi container yang aman.
                    </p>
                    
                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200">
                        <h3 class="font-semibold text-slate-900 mb-4">Langkah Deployment</h3>
                        <ul class="space-y-3 text-slate-600">
                            <li class="flex items-start">
                                <span class="bg-slate-200 text-slate-700 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-4 shrink-0 mt-0.5">1</span>
                                <span>Buka menu <strong>Dashboard Hosting</strong> lalu klik <strong>Deploy Proyek Baru</strong>.</span>
                            </li>
                            <li class="flex items-start">
                                <span class="bg-slate-200 text-slate-700 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-4 shrink-0 mt-0.5">2</span>
                                <span>Pilih metode <strong>Git Repository</strong> (untuk kode Anda sendiri) atau <strong>Template Instan</strong>.</span>
                            </li>
                            <li class="flex items-start">
                                <span class="bg-slate-200 text-slate-700 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-4 shrink-0 mt-0.5">3</span>
                                <span>Masukkan Nama Project yang akan menjadi subdomain (contoh: <code class="font-mono bg-white border border-slate-200 text-slate-800 px-1.5 py-0.5 rounded text-sm mx-1">app.ryaze.my.id</code>).</span>
                            </li>
                            <li class="flex items-start">
                                <span class="bg-slate-200 text-slate-700 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-4 shrink-0 mt-0.5">4</span>
                                <span>Klik <strong>Deploy Sekarang</strong> dan pantau log instalasi.</span>
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- Section 2: File Manager -->
                <section>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 mb-5">2. File Manager</h2>
                    <p class="mb-6 text-slate-600 font-light">
                        Akses dan ubah kode Anda secara langsung tanpa aplikasi FTP pihak ketiga. File manager Ryaze dilengkapi dengan editor kode mutakhir terintegrasi.
                    </p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                        <div class="p-5 border border-slate-200 rounded-2xl hover:border-slate-300 transition-colors">
                            <h4 class="font-semibold text-slate-900 mb-2">Code Editor</h4>
                            <p class="text-sm text-slate-500 leading-relaxed">Edit file teks, sesuaikan konfigurasi, dan tulis baris kode langsung dari browser Anda.</p>
                        </div>
                        <div class="p-5 border border-slate-200 rounded-2xl hover:border-slate-300 transition-colors">
                            <h4 class="font-semibold text-slate-900 mb-2">Quick Upload</h4>
                            <p class="text-sm text-slate-500 leading-relaxed">Unggah aset dan file pendukung dengan mudah (Maksimal 10MB per file).</p>
                        </div>
                    </div>

                    <div class="flex items-start bg-slate-50 p-5 rounded-2xl border border-slate-200">
                        <i class="fa-solid fa-lock text-slate-400 mt-1 mr-4"></i>
                        <div>
                            <h4 class="text-sm font-semibold text-slate-900 mb-1">Keamanan Server Terpusat</h4>
                            <p class="text-sm text-slate-600">Untuk mencegah kerentanan sistem, ekstensi <i>executable</i> tertentu (seperti .php, .sh) diblokir dari antarmuka unggah web. Harap gunakan <strong>Git</strong> untuk mendorong perubahan struktural.</p>
                        </div>
                    </div>
                </section>

                <!-- Section 3: Environment -->
                <section>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 mb-5">3. Environment Variables</h2>
                    <p class="mb-4 text-slate-600 font-light">
                        Simpan data rahasia seperti <i>API Keys</i>, *tokens*, dan kredensial basis data menggunakan antarmuka Environment Variable (.env) kami yang aman dan terenkripsi.
                    </p>
                    <p class="text-slate-600">
                        Buka tab <strong>Pengaturan .env</strong> pada detail project. Setiap perubahan yang Anda simpan akan langsung disuntikkan ke dalam <i>runtime</i> aplikasi Anda secara <i>real-time</i>.
                    </p>
                </section>

                <!-- Section 4: Settings -->
                <section>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 mb-5">4. Keamanan & Pengaturan</h2>
                    <p class="mb-6 text-slate-600 font-light">
                        Lindungi dan optimalkan kinerja proyek Anda hanya dengan mengaktifkan tuas konfigurasi di tab <strong>Settings</strong>.
                    </p>

                    <div class="space-y-4">
                        <div class="flex items-center p-4 border border-slate-200 rounded-2xl">
                            <div class="flex-shrink-0 mr-4 text-slate-400"><i class="fa-solid fa-tools text-xl"></i></div>
                            <div>
                                <h4 class="font-semibold text-slate-900">Maintenance Mode</h4>
                                <p class="text-sm text-slate-500 mt-1">Mengalihkan pengunjung ke halaman perbaikan statis saat Anda melakukan pembaruan krusial.</p>
                            </div>
                        </div>
                        <div class="flex items-center p-4 border border-slate-200 rounded-2xl">
                            <div class="flex-shrink-0 mr-4 text-slate-400"><i class="fa-solid fa-shield-halved text-xl"></i></div>
                            <div>
                                <h4 class="font-semibold text-slate-900">Under Attack Mode</h4>
                                <p class="text-sm text-slate-500 mt-1">Mengaktifkan pembatasan laju agresif (Rate-Limiting) untuk memitigasi serangan DDoS skala kecil dan lalu lintas bot yang berbahaya.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 5: Web Terminal -->
                <section>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 mb-5">5. Web Terminal</h2>
                    <p class="mb-6 text-slate-600 font-light">
                        Eksekusi perintah tingkat lanjut langsung dari peramban Anda. Terminal bawaan kami diisolasi dengan cermat untuk memastikan keamanan ekosistem.
                    </p>

                    <div class="bg-slate-900 text-slate-300 font-mono text-sm p-6 rounded-2xl shadow-sm overflow-hidden relative">
                        <!-- Terminal header -->
                        <div class="flex gap-2 absolute top-4 left-4">
                            <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        </div>
                        
                        <div class="mt-6">
                            <div class="text-slate-400 mb-3 border-b border-slate-700 pb-2"># Perintah yang didukung (Whitelist):</div>
                            <div class="leading-loose text-emerald-400">
                                ls, cat, grep, mkdir, rm, cp, mv, touch <br>
                                npm, node, npx, yarn <br>
                                composer, php artisan <br>
                                python, python3, pip, pip3 <br>
                                git, curl
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 6: Storage -->
                <section>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 mb-5">6. Manajemen Penyimpanan</h2>
                    <p class="mb-5 text-slate-600 font-light">
                        Penyimpanan dihitung secara <strong>Global per Akun</strong>. Anda bebas meluncurkan proyek tanpa batas selama kuota penyimpanan akun Anda masih mencukupi.
                    </p>
                    
                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200">
                        <h4 class="font-semibold text-slate-900 mb-3">Praktik Terbaik Efisiensi</h4>
                        <ul class="list-disc list-inside space-y-2 text-slate-600">
                            <li>Jangan *commit* pustaka dependensi (seperti <code class="font-mono text-sm bg-white border border-slate-200 px-1.5 py-0.5 rounded">node_modules</code> atau <code class="font-mono text-sm bg-white border border-slate-200 px-1.5 py-0.5 rounded">vendor</code>). Ryaze akan merakitnya secara otomatis.</li>
                            <li>Terapkan kebijakan rotasi atau penghapusan log berkala pada aplikasi Anda.</li>
                            <li>Kompres aset media statis sebelum tahap *deployment*.</li>
                        </ul>
                    </div>
                </section>

                <!-- Section 7: Live Preview -->
                <section>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 mb-5">7. Development Server</h2>
                    <p class="mb-4 text-slate-600 font-light">
                        Jalankan *development server* spesifik kerangka kerja (Vite, Next.js, Artisan Serve) di komputasi awan dan dapatkan tautan pratinjau instan.
                    </p>
                    <p class="text-slate-600">
                        Cukup klik tombol <strong>Dev Server (Play)</strong> pada dasbor proyek Anda. Server akan beroperasi di latar belakang, memberikan Anda URL iterasi instan (misal: <code class="font-mono text-sm text-slate-900 bg-slate-100 px-1.5 py-0.5 rounded">devX.ryaze.my.id</code>) tanpa perlu repot membangun ulang (rebuild) secara manual di setiap perubahan kode.
                    </p>
                </section>

                <!-- Section 8: Python -->
                <section>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 mb-5 flex items-center gap-2">
                        <i class="fa-brands fa-python text-amber-500"></i> Arsitektur Python
                    </h2>
                    <p class="mb-6 text-slate-600 font-light">
                        Ryaze merancang lingkungan komputasi khusus untuk mengeksekusi aplikasi Python, Flask, Django, hingga model Machine Learning dengan latensi ultra-rendah dan isolasi *enterprise*.
                    </p>

                    <div class="space-y-8">
                        <div>
                            <h4 class="font-semibold text-slate-900 mb-1">Virtual Environment Otomatis</h4>
                            <p class="text-sm text-slate-600">Selama siklus *deployment*, <i>Virtual Environment</i> (<code class="font-mono bg-slate-100 px-1 rounded">venv</code>) akan dibuat dan meresolusi <code class="font-mono bg-slate-100 px-1 rounded">requirements.txt</code> Anda sepenuhnya secara otonom.</p>
                        </div>
                        
                        <div>
                            <h4 class="font-semibold text-slate-900 mb-1">Inteligensi Terminal Auto-Alias</h4>
                            <p class="text-sm text-slate-600">Mengeksekusi <code class="font-mono bg-slate-100 px-1 rounded text-indigo-600">pip install X</code> di Web Terminal akan dicegat dengan aman dan diteruskan secara transparan ke dalam ruang lingkup <i>venv</i> proyek Anda.</p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-slate-900 mb-1">Pre-compiled Data Science Engine</h4>
                            <p class="text-sm text-slate-600">Kami menginjeksi <i>binaries</i> bawaan untuk <strong>Numpy, Pandas, dan Scikit-Learn</strong> untuk menghindari *overhead* kompilasi CPU yang sangat mahal pada arsitektur Alpine.</p>
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <h4 class="font-semibold text-slate-900 mb-3">Port Binding Kontrak (Kritis)</h4>
                            <p class="text-sm text-slate-600 mb-4">Layanan Python tidak diizinkan untuk mengikat port secara absolut. Proyek Anda <strong>harus</strong> mengekstrak nomor port melalui variabel lingkungan <code class="font-mono bg-slate-100 px-1 rounded">PORT</code> untuk mematuhi aturan <i>reverse proxy</i>.</p>
                            
                            <div class="bg-slate-900 text-slate-300 font-mono text-sm p-5 rounded-2xl shadow-sm">
                                <div class="text-slate-500 mb-2"># app.py (Contoh Flask)</div>
                                <div class="leading-relaxed">
                                    <span class="text-fuchsia-400">import</span> os<br><br>
                                    <span class="text-sky-400">port</span> = <span class="text-yellow-200">int</span>(os.environ.get(<span class="text-emerald-400">'PORT'</span>, <span class="text-orange-300">8080</span>))<br>
                                    app.run(host=<span class="text-emerald-400">'0.0.0.0'</span>, port=port)
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                
                <hr class="border-slate-100 my-8">
                
                <div class="text-center">
                    <p class="text-slate-500 text-sm">Masih mengalami kendala? Silakan hubungi tim dukungan teknis kami.</p>
                </div>

            </div>
        </div>
    </x-ui.page-layout>
@endsection
