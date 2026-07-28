@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Panduan & Dokumentasi" 
            subtitle="Pelajari cara menggunakan seluruh fitur hosting Ryaze dengan optimal." 
            icon="fa-book-open" 
            iconColor="indigo">
        </x-ui.page-header>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            
            <!-- Content Body -->
            <div class="space-y-8 text-slate-700 leading-relaxed max-w-5xl">

                <!-- Intro -->
                <div class="text-slate-500 text-sm leading-relaxed mb-4">
                    Selamat datang di dokumentasi resmi Hosting Ryaze! Panduan esensial untuk mendeploy, mengelola, dan menskalakan aplikasi modern Anda di atas infrastruktur berkinerja tinggi kami.
                </div>

                <hr class="border-slate-100">

                <!-- Section 1: Deploy -->
                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3">1. Deploy Project</h2>
                    <p class="mb-4 text-slate-600 text-sm">
                        Ryaze mendukung deployment otomatis dari Git maupun penggunaan template instan. Proses deployment berlangsung dalam hitungan detik menggunakan teknologi isolasi container yang aman.
                    </p>
                    
                    <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                        <h3 class="font-semibold text-slate-900 text-sm mb-3">Langkah Deployment</h3>
                        <ul class="space-y-2 text-slate-600 text-sm">
                            <li class="flex items-start">
                                <span class="bg-slate-200 text-slate-700 rounded-full w-5 h-5 flex items-center justify-center text-[10px] font-bold mr-3 shrink-0 mt-0.5">1</span>
                                <span>Buka menu <strong>Dashboard Hosting</strong> lalu klik <strong>Deploy Proyek Baru</strong>.</span>
                            </li>
                            <li class="flex items-start">
                                <span class="bg-slate-200 text-slate-700 rounded-full w-5 h-5 flex items-center justify-center text-[10px] font-bold mr-3 shrink-0 mt-0.5">2</span>
                                <span>Pilih metode <strong>Git Repository</strong> (untuk kode Anda sendiri) atau <strong>Template Instan</strong>.</span>
                            </li>
                            <li class="flex items-start">
                                <span class="bg-slate-200 text-slate-700 rounded-full w-5 h-5 flex items-center justify-center text-[10px] font-bold mr-3 shrink-0 mt-0.5">3</span>
                                <span>Masukkan Nama Project yang akan menjadi subdomain (contoh: <code class="font-mono bg-white border border-slate-200 text-slate-800 px-1.5 py-0.5 rounded text-xs mx-1">app.ryaze.my.id</code>).</span>
                            </li>
                            <li class="flex items-start">
                                <span class="bg-slate-200 text-slate-700 rounded-full w-5 h-5 flex items-center justify-center text-[10px] font-bold mr-3 shrink-0 mt-0.5">4</span>
                                <span>Klik <strong>Deploy Sekarang</strong> dan pantau log instalasi.</span>
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- Section 2: File Manager -->
                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3">2. File Manager</h2>
                    <p class="mb-4 text-slate-600 text-sm">
                        Akses dan ubah kode Anda secara langsung tanpa aplikasi FTP pihak ketiga. File manager Ryaze dilengkapi dengan editor kode mutakhir terintegrasi.
                    </p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div class="p-4 border border-slate-200 rounded-lg hover:border-slate-300 transition-colors">
                            <h4 class="font-semibold text-slate-900 text-sm mb-1">Code Editor</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">Edit file teks, sesuaikan konfigurasi, dan tulis baris kode langsung dari browser Anda.</p>
                        </div>
                        <div class="p-4 border border-slate-200 rounded-lg hover:border-slate-300 transition-colors">
                            <h4 class="font-semibold text-slate-900 text-sm mb-1">Quick Upload</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">Unggah aset dan file pendukung dengan mudah (Maksimal 10MB per file).</p>
                        </div>
                    </div>

                    <div class="flex items-start bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <i class="fa-solid fa-lock text-slate-400 mt-1 mr-3 text-sm"></i>
                        <div>
                            <h4 class="text-sm font-semibold text-slate-900 mb-1">Keamanan Server Terpusat</h4>
                            <p class="text-xs text-slate-600">Untuk mencegah kerentanan sistem, ekstensi <i>executable</i> tertentu (seperti .php, .sh) diblokir dari antarmuka unggah web. Harap gunakan <strong>Git</strong> untuk mendorong perubahan struktural.</p>
                        </div>
                    </div>
                </section>

                <!-- Section 3: Environment -->
                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3">3. Environment Variables</h2>
                    <p class="mb-3 text-slate-600 text-sm">
                        Simpan data rahasia seperti <i>API Keys</i>, *tokens*, dan kredensial basis data menggunakan antarmuka Environment Variable (.env) kami yang aman dan terenkripsi.
                    </p>
                    <p class="text-slate-600 text-sm">
                        Buka tab <strong>Pengaturan .env</strong> pada detail project. Setiap perubahan yang Anda simpan akan langsung disuntikkan ke dalam <i>runtime</i> aplikasi Anda secara <i>real-time</i>.
                    </p>
                </section>

                <!-- Section 4: Settings -->
                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3">4. Keamanan & Pengaturan</h2>
                    <p class="mb-4 text-slate-600 text-sm">
                        Lindungi dan optimalkan kinerja proyek Anda hanya dengan mengaktifkan tuas konfigurasi di tab <strong>Settings</strong>.
                    </p>

                    <div class="space-y-3">
                        <div class="flex items-center p-3 border border-slate-200 rounded-lg">
                            <div class="flex-shrink-0 mr-4 text-slate-400"><i class="fa-solid fa-tools"></i></div>
                            <div>
                                <h4 class="font-semibold text-slate-900 text-sm">Maintenance Mode</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Mengalihkan pengunjung ke halaman perbaikan statis saat Anda melakukan pembaruan krusial.</p>
                            </div>
                        </div>
                        <div class="flex items-center p-3 border border-slate-200 rounded-lg">
                            <div class="flex-shrink-0 mr-4 text-slate-400"><i class="fa-solid fa-shield-halved"></i></div>
                            <div>
                                <h4 class="font-semibold text-slate-900 text-sm">Under Attack Mode</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Mengaktifkan pembatasan laju agresif (Rate-Limiting) untuk memitigasi serangan DDoS dan bot.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 5: Web Terminal -->
                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3">5. Web Terminal</h2>
                    <p class="mb-4 text-slate-600 text-sm">
                        Eksekusi perintah tingkat lanjut langsung dari peramban Anda. Terminal bawaan kami diisolasi dengan cermat untuk memastikan keamanan ekosistem.
                    </p>

                    <div class="bg-slate-900 text-slate-300 font-mono text-xs p-5 rounded-lg shadow-sm overflow-hidden relative">
                        <!-- Terminal header -->
                        <div class="flex gap-1.5 absolute top-3 left-3">
                            <div class="w-2.5 h-2.5 rounded-full bg-rose-500"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-amber-500"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="text-slate-400 mb-2 border-b border-slate-700 pb-1"># Perintah yang didukung (Whitelist):</div>
                            <div class="leading-relaxed text-emerald-400">
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
                    <h2 class="text-lg font-bold text-slate-900 mb-3">6. Manajemen Penyimpanan</h2>
                    <p class="mb-4 text-slate-600 text-sm">
                        Penyimpanan dihitung secara <strong>Global per Akun</strong>. Anda bebas meluncurkan proyek tanpa batas selama kuota penyimpanan akun Anda masih mencukupi.
                    </p>
                    
                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <h4 class="font-semibold text-slate-900 text-sm mb-2">Praktik Terbaik Efisiensi</h4>
                        <ul class="list-disc list-inside space-y-1.5 text-slate-600 text-sm">
                            <li>Jangan *commit* folder <code class="font-mono text-xs bg-white border border-slate-200 px-1 py-0.5 rounded">node_modules</code> atau <code class="font-mono text-xs bg-white border border-slate-200 px-1 py-0.5 rounded">vendor</code>. Ryaze akan merakitnya.</li>
                            <li>Terapkan kebijakan rotasi atau penghapusan log berkala pada aplikasi Anda.</li>
                            <li>Kompres aset media statis sebelum tahap *deployment*.</li>
                        </ul>
                    </div>
                </section>

                <!-- Section 7: Live Preview -->
                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3">7. Development Server</h2>
                    <p class="mb-3 text-slate-600 text-sm">
                        Jalankan *development server* spesifik kerangka kerja (Vite, Next.js, Artisan Serve) di komputasi awan dan dapatkan tautan pratinjau instan.
                    </p>
                    <p class="text-slate-600 text-sm">
                        Cukup klik tombol <strong>Dev Server (Play)</strong> pada dasbor proyek Anda. Server akan beroperasi di latar belakang, memberikan URL iterasi (misal: <code class="font-mono text-xs text-slate-900 bg-slate-100 px-1 py-0.5 rounded">devX.ryaze.my.id</code>) tanpa perlu membangun ulang (rebuild) secara manual.
                    </p>
                </section>

                <!-- Section 8: Python -->
                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <i class="fa-brands fa-python text-amber-500"></i> Panduan Khusus Deployment Python
                    </h2>
                    <p class="mb-5 text-slate-600 text-sm">
                        Infrastruktur Ryaze telah dioptimasi secara mendalam untuk mendukung deployment aplikasi berbasis Python seperti Flask, Django, FastAPI, hingga model Machine Learning. Kami menerapkan tingkat isolasi dan efisiensi sekelas *enterprise* agar aplikasi Anda berjalan stabil.
                    </p>

                    <div class="space-y-6">
                        <div>
                            <h4 class="font-semibold text-slate-900 text-sm mb-1">1. Isolasi Virtual Environment Otomatis</h4>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Untuk mencegah konflik versi library antar aplikasi, setiap proyek Python Anda diisolasi menggunakan <i>Virtual Environment</i> (<code class="font-mono bg-slate-100 text-xs px-1 rounded">venv</code>). Saat Anda melakukan <i>deployment</i> pertama kali atau menekan tombol <i>Redeploy</i>, sistem kami akan secara otomatis membaca file <code class="font-mono bg-slate-100 text-xs px-1 rounded">requirements.txt</code> dan menginstal seluruh dependensi Anda langsung ke dalam ruang isolasi proyek tersebut. Anda tidak perlu mengatur environment secara manual.
                            </p>
                        </div>
                        
                        <div>
                            <h4 class="font-semibold text-slate-900 text-sm mb-1">2. Web Terminal Auto-Alias</h4>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Jika Anda perlu menginstal library tambahan secara manual di tengah proses pengembangan, Anda dapat langsung menggunakan fitur Web Terminal. Cukup ketikkan perintah standar seperti <code class="font-mono text-xs bg-slate-100 px-1 rounded text-indigo-600">pip install nama_library</code> atau menjalankan script dengan <code class="font-mono text-xs bg-slate-100 px-1 rounded text-indigo-600">python script.py</code>. Tembok keamanan cerdas kami akan secara otomatis menerjemahkan dan mengarahkan perintah tersebut ke dalam ekosistem <i>venv</i> proyek Anda (berubah menjadi <code class="font-mono text-xs bg-slate-100 px-1 rounded">venv/bin/pip</code>). Sistem ini menjamin 100% keamanan server global.
                            </p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-slate-900 text-sm mb-1">3. Pre-compiled Data Science Library</h4>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Menginstal library komputasi berat (seperti Numpy, Pandas, atau OpenCV) dari <i>source code</i> di lingkungan Linux bisa memakan waktu hingga puluhan menit dan memonopoli resource CPU. Untuk mengatasi ini, Ryaze telah menginjeksi <i>pre-compiled binaries</i> bawaan sistem untuk library Data Science populer (<strong>Numpy, Pandas, dan Scikit-Learn</strong>). Hal ini membuat proses instalasi environment Anda menjadi instan dan menjaga server tetap ringan.
                            </p>
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <h4 class="font-semibold text-slate-900 text-sm mb-2">4. Aturan Binding PORT Dinamis (Kritis & Wajib)</h4>
                            <p class="text-sm text-slate-600 mb-3 leading-relaxed">
                                Arsitektur jaringan Ryaze menggunakan sistem <i>Dynamic Port Allocation</i> yang dipadukan dengan Nginx Reverse Proxy. Oleh karena itu, aplikasi Python Anda <strong>TIDAK DIIZINKAN</strong> untuk mengikat port secara statis/hardcode (misalnya: <code>port 5000</code> atau <code>8000</code>). Aplikasi Anda <strong>wajib</strong> membaca nomor port yang dibagikan oleh sistem melalui variabel lingkungan <code class="font-mono text-xs bg-amber-100 text-amber-800 border border-amber-200 px-1 rounded">PORT</code>. Jika hal ini tidak dipatuhi, aplikasi Anda tidak akan bisa diakses dari luar (mengalami 502 Bad Gateway).
                            </p>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="bg-slate-900 text-slate-300 font-mono text-xs p-4 rounded-lg shadow-sm">
                                    <div class="text-slate-500 mb-2 border-b border-slate-700 pb-1"># Contoh implementasi di Flask</div>
                                    <div class="leading-relaxed mt-2">
                                        <span class="text-fuchsia-400">import</span> os<br>
                                        <span class="text-fuchsia-400">from</span> flask <span class="text-fuchsia-400">import</span> Flask<br><br>
                                        app = Flask(__name__)<br><br>
                                        <span class="text-fuchsia-400">if</span> __name__ == <span class="text-emerald-400">'__main__'</span>:<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-sky-400">port</span> = <span class="text-yellow-200">int</span>(os.environ.get(<span class="text-emerald-400">'PORT'</span>, <span class="text-orange-300">8080</span>))<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;app.run(host=<span class="text-emerald-400">'0.0.0.0'</span>, port=port)
                                    </div>
                                </div>
                                <div class="bg-slate-900 text-slate-300 font-mono text-xs p-4 rounded-lg shadow-sm">
                                    <div class="text-slate-500 mb-2 border-b border-slate-700 pb-1"># Contoh implementasi di FastAPI / Uvicorn</div>
                                    <div class="leading-relaxed mt-2">
                                        <span class="text-fuchsia-400">import</span> os<br>
                                        <span class="text-fuchsia-400">import</span> uvicorn<br>
                                        <span class="text-fuchsia-400">from</span> fastapi <span class="text-fuchsia-400">import</span> FastAPI<br><br>
                                        app = FastAPI()<br><br>
                                        <span class="text-fuchsia-400">if</span> __name__ == <span class="text-emerald-400">'__main__'</span>:<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-sky-400">port</span> = <span class="text-yellow-200">int</span>(os.environ.get(<span class="text-emerald-400">'PORT'</span>, <span class="text-orange-300">8080</span>))<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;uvicorn.run(app, host=<span class="text-emerald-400">'0.0.0.0'</span>, port=port)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                
                <hr class="border-slate-100 my-6">
                
                <div class="text-center">
                    <p class="text-slate-500 text-xs">Masih mengalami kendala? Silakan hubungi tim dukungan teknis kami.</p>
                </div>

            </div>
        </div>
    </x-ui.page-layout>
@endsection
