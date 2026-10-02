@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Panduan & Dokumentasi" 
            subtitle="Pelajari cara menggunakan seluruh fitur hosting Ryaze dengan optimal." 
            icon="fa-book-open" 
            iconColor="indigo">
        </x-ui.page-header>

        <div class="bg-white dark:bg-slate-800/60 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-6">
            
            <!-- Content Body -->
            <div class="space-y-8 text-slate-700 dark:text-slate-200 leading-relaxed max-w-5xl">

                <!-- Intro -->
                <div class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-4">
                    Selamat datang di dokumentasi resmi Hosting Ryaze! Panduan esensial untuk mendeploy, mengelola, dan menskalakan aplikasi modern Anda di atas infrastruktur berkinerja tinggi kami.
                </div>

                <hr class="border-slate-100 dark:border-slate-700">

                <!-- Section 1: Deploy -->
                <section>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-slate-50 mb-3">1. Menambahkan & Deploy Proyek Baru</h2>
                    <p class="mb-4 text-slate-600 dark:text-slate-300 text-sm">
                        Sistem Ryaze mendukung <i>deployment</i> aplikasi secara otomatis melalui dua metode utama: penarikan kode dari <strong>Git Repository</strong> atau menggunakan <strong>Template Instan</strong> bawaan. Setiap aplikasi yang di-<i>deploy</i> akan diatur dalam direktori terpisah dan dikelola oleh sistem <i>process manager</i> kami (seperti PM2 untuk Node.js atau reverse proxy Nginx/OpenResty).
                    </p>
                    
                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
                        <h3 class="font-semibold text-slate-900 dark:text-slate-50 text-sm mb-3">Langkah Deployment Otomatis</h3>
                        <ul class="space-y-3 text-slate-600 dark:text-slate-300 text-sm">
                            <li class="flex items-start">
                                <span class="bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-full w-5 h-5 flex items-center justify-center text-[10px] font-bold mr-3 shrink-0 mt-0.5">1</span>
                                <div>
                                    <strong>Pilih Metode:</strong> Gunakan opsi Git (masukkan URL Repositori publik/privat dan nama Branch) atau pilih Template kerangka kerja yang telah kami siapkan (misal: Laravel, Next.js, React).
                                </div>
                            </li>
                            <li class="flex items-start">
                                <span class="bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-full w-5 h-5 flex items-center justify-center text-[10px] font-bold mr-3 shrink-0 mt-0.5">2</span>
                                <div>
                                    <strong>Tentukan Subdomain:</strong> Nama proyek Anda akan langsung menjadi alamat akses (contoh: jika nama proyek adalah <code>app</code>, maka web dapat diakses di <code class="font-mono bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 px-1 py-0.5 rounded text-xs mx-1">app.ryaze.my.id</code>).
                                </div>
                            </li>
                            <li class="flex items-start">
                                <span class="bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-full w-5 h-5 flex items-center justify-center text-[10px] font-bold mr-3 shrink-0 mt-0.5">3</span>
                                <div>
                                    <strong>Instalasi Dependensi Otomatis:</strong> Saat Anda menekan <i>Deploy</i>, pekerja latar belakang Ryaze akan secara otomatis mengunduh kode Anda, menjalankan perintah instalasi paket (seperti <code>npm install</code> atau <code>composer install</code>), dan menyambungkannya ke <i>Reverse Proxy</i>.
                                </div>
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- Section 2: File Manager -->
                <section>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-slate-50 mb-3">2. Manajemen Berkas (File Manager)</h2>
                    <p class="mb-4 text-slate-600 dark:text-slate-300 text-sm">
                        Anda tidak memerlukan aplikasi FTP pihak ketiga (seperti FileZilla). Ryaze telah menyediakan antarmuka <i>File Manager</i> yang terintegrasi penuh dengan sistem berkas proyek Anda di <i>server</i>.
                    </p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div class="p-4 border border-slate-200 dark:border-slate-700 rounded-lg">
                            <h4 class="font-semibold text-slate-900 dark:text-slate-50 text-sm mb-1">Code Editor In-Browser</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Anda dapat membuka, mengedit, dan menyimpan perubahan langsung pada file teks sumber kode Anda (seperti HTML, JS, PHP, atau konfigurasi) langsung dari dasbor web.</p>
                        </div>
                        <div class="p-4 border border-slate-200 dark:border-slate-700 rounded-lg">
                            <h4 class="font-semibold text-slate-900 dark:text-slate-50 text-sm mb-1">Manajemen Aset (Upload/Delete)</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Unggah file pendukung, aset gambar, atau hapus direktori yang tidak diperlukan. Terdapat batasan ukuran maksimal 10MB per unggahan file melalui antarmuka web.</p>
                        </div>
                    </div>

                    <div class="flex items-start bg-slate-50 dark:bg-slate-800/60 p-4 rounded-lg border border-slate-200 dark:border-slate-700">
                        <i class="fa-solid fa-lock text-slate-400 dark:text-slate-500 mt-1 mr-3 text-sm"></i>
                        <div>
                            <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-50 mb-1">Kebijakan Anti-Malware (Web Upload)</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-300">Untuk mencegah kerentanan sistem akibat unggahan naskah berbahaya (misalnya <i>Web Shell</i>), kami secara tegas memblokir unggahan ekstensi <i>executable</i> tertentu (seperti <code>.php</code>, <code>.sh</code>, <code>.exe</code>) jika dilakukan melalui File Manager. Jika Anda perlu mengunggah file tersebut, Anda <strong>wajib</strong> mendorongnya melalui Git.</p>
                        </div>
                    </div>
                </section>

                <!-- Section 3: Environment -->
                <section>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-slate-50 mb-3">3. Konfigurasi Environment Variables (.env)</h2>
                    <p class="mb-3 text-slate-600 dark:text-slate-300 text-sm">
                        Setiap aplikasi modern memerlukan variabel lingkungan untuk menyimpan kredensial rahasia (koneksi basis data, kunci API, mode <i>debug</i>). Ryaze menyediakan tab khusus <strong>Pengaturan .env</strong> di halaman detail proyek Anda.
                    </p>
                    <p class="text-slate-600 dark:text-slate-300 text-sm">
                        Ketika Anda memodifikasi dan menyimpan nilai di tab ini, Ryaze akan secara otomatis menulis ulang file <code>.env</code> di <i>root</i> direktori aplikasi Anda. Perhatikan bahwa beberapa kerangka kerja (seperti Node.js) mungkin memerlukan <i>restart</i> manual dari Terminal agar dapat membaca variabel baru.
                    </p>
                </section>

                <!-- Section 4: Settings -->
                <section>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-slate-50 mb-3">4. Keamanan & Optimasi Trafik</h2>
                    <p class="mb-4 text-slate-600 dark:text-slate-300 text-sm">
                        Di bawah tab <strong>Settings</strong> proyek Anda, terdapat konfigurasi sakelar (<i>toggle</i>) yang secara langsung mengubah aturan <i>Routing</i> Nginx untuk aplikasi Anda.
                    </p>

                    <div class="space-y-3">
                        <div class="flex items-center p-3 border border-slate-200 dark:border-slate-700 rounded-lg">
                            <div class="flex-shrink-0 mr-4 text-slate-400 dark:text-slate-500"><i class="fa-solid fa-tools"></i></div>
                            <div>
                                <h4 class="font-semibold text-slate-900 dark:text-slate-50 text-sm">Maintenance Mode</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Jika diaktifkan, sistem akan mengembalikan halaman statis perbaikan 503 (Under Maintenance) kepada pengunjung. Sangat berguna saat Anda melakukan migrasi basis data besar.</p>
                            </div>
                        </div>
                        <div class="flex items-center p-3 border border-slate-200 dark:border-slate-700 rounded-lg">
                            <div class="flex-shrink-0 mr-4 text-slate-400 dark:text-slate-500"><i class="fa-solid fa-shield-halved"></i></div>
                            <div>
                                <h4 class="font-semibold text-slate-900 dark:text-slate-50 text-sm">Under Attack Mode (Rate Limiting)</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Berfungsi untuk membatasi jumlah permintaan berlebihan dari satu alamat IP dalam waktu singkat untuk memitigasi potensi <i>Denial of Service</i> (DDoS) kecil atau serangan bot.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 5: Web Terminal -->
                <section>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-slate-50 mb-3">5. Web Terminal Cerdas</h2>
                    <p class="mb-4 text-slate-600 dark:text-slate-300 text-sm">
                        Terminal yang tertanam di dasbor Ryaze memungkinkan Anda menjalankan perintah operasional layaknya SSH, namun dengan antarmuka berbasis web. Terminal ini diawasi ketat oleh Sistem Pencegahan Intrusi kami.
                    </p>

                    <div class="bg-slate-900 text-slate-300 dark:text-slate-400 font-mono text-xs p-5 rounded-lg shadow-sm overflow-hidden relative">
                        <div class="flex gap-1.5 absolute top-3 left-3">
                            <div class="w-2.5 h-2.5 rounded-full bg-rose-500"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-amber-500"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="text-slate-400 dark:text-slate-500 mb-2 border-b border-slate-700 pb-1"># Perintah Operasional yang Diizinkan (Whitelist):</div>
                            <div class="leading-relaxed text-emerald-400 dark:text-emerald-300">
                                ls, cat, head, tail, wc, grep, find, echo, pwd, whoami, date, mkdir, touch, cp, mv, rm, chmod, chown, nano, curl <br>
                                git, php, composer, npm, npx, node
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                        <strong>Catatan Keamanan:</strong> Untuk menjaga kestabilan peladen induk, upaya menggunakan operator perangkaian ganda (seperti <code>&&</code> atau <code>|</code>), perpindahan keluar direktori kerja (<code>../</code>), atau membaca konfigurasi inti (<code>/etc/</code>) akan segera dibatalkan oleh pembungkus terminal kami.
                    </p>
                </section>

                <!-- Section 6: Storage -->
                <section>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-slate-50 mb-3">6. Manajemen Penyimpanan (Storage)</h2>
                    <p class="mb-4 text-slate-600 dark:text-slate-300 text-sm">
                        Kuota penyimpanan Anda dihitung secara <strong>akumulatif per akun pengguna</strong>, bukan per proyek. Artinya, Anda bebas mendirikan proyek sebanyak mungkin selama ruang kosong total Anda mencukupi.
                    </p>
                    
                    <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-lg border border-slate-200 dark:border-slate-700">
                        <h4 class="font-semibold text-slate-900 dark:text-slate-50 text-sm mb-2">Praktik Manajemen Kuota</h4>
                        <ul class="list-disc list-inside space-y-1.5 text-slate-600 dark:text-slate-300 text-sm">
                            <li>Jika penyimpanan Anda menyentuh angka 100%, upaya modifikasi basis data atau proses instalasi library pada proyek Anda (seperti <code>npm install</code>) akan mulai menolak untuk berfungsi (gagal).</li>
                            <li>Terapkan aturan pada file <code>.gitignore</code> Anda agar tidak menyertakan folder <code>vendor</code> (PHP) atau <code>node_modules</code> (Node.js). Sistem kami mampu merakit ulang komponen tersebut saat waktu <i>deployment</i>, sehingga menghemat konsumsi kuota mentah Anda.</li>
                            <li>Anda selalu dapat <strong>Meningkatkan Paket</strong> (Upgrade Storage) melalui saluran pembayaran terintegrasi <strong>Pakasir</strong> pada halaman Profil / Penyimpanan.</li>
                        </ul>
                    </div>
                </section>

                <!-- Section 7: Live Preview -->
                <section>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-slate-50 mb-3">7. Mode Development Server (Live Preview)</h2>
                    <p class="mb-3 text-slate-600 dark:text-slate-300 text-sm">
                        Ryaze memfasilitasi lingkungan <i>Live Preview</i> yang memungkinkan Anda mempublikasikan peladen pengembangan lokal (seperti <code>php artisan serve</code>, <code>npm run dev</code>, atau <code>npx vite</code>) sehingga dapat diakses secara publik.
                    </p>
                    <p class="text-slate-600 dark:text-slate-300 text-sm">
                        Mekanismenya sangat mudah: Klik ikon <strong>Nyalakan Server</strong> pada dasbor proyek Anda. Sistem kami akan membangkitkan pekerja di latar belakang dan menerbitkan URL acak (contoh: <code class="font-mono text-xs text-slate-900 dark:text-slate-50 bg-slate-100 dark:bg-slate-700/50 px-1 py-0.5 rounded">devX.ryaze.my.id</code>). Segala perubahan yang Anda simpan melalui File Manager atau Web Terminal akan dipantulkan langsung ke URL pratinjau tersebut tanpa perlu merakit ulang aplikasi secara utuh.
                    </p>
                </section>

                <hr class="border-slate-100 dark:border-slate-700 my-6">
                
                <div class="text-center">
                    <p class="text-slate-500 dark:text-slate-400 text-xs">Masih mengalami kendala? Silakan hubungi tim dukungan teknis kami.</p>
                </div>

            </div>
        </div>
    </x-ui.page-layout>
@endsection
