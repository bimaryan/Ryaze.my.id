@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Panduan & Dokumentasi" 
            eyebrow="Dokumentasi Panel"
            subtitle="Pelajari cara menggunakan seluruh fitur hosting Ryaze dengan optimal."
            icon="fa-book-open">
        </x-ui.page-header>

        <div class="ui-panel p-6 sm:p-8">
            
            <!-- Content Body -->
            <div class="space-y-8 text-[#0F1A2A] dark:text-white/85 leading-relaxed max-w-5xl">

                <!-- Intro -->
                <div class="text-[#525252] dark:text-[#8A9BAE] text-sm leading-relaxed mb-4">
                    Selamat datang di dokumentasi resmi Hosting Ryaze! Panduan esensial untuk mendeploy, mengelola, dan menskalakan aplikasi modern Anda di atas infrastruktur berkinerja tinggi kami.
                </div>

                <hr class="border-[#EDF2F7] dark:border-[#2A4463]">

                <!-- Section 1: Deploy -->
                <section>
                    <h2 class="ui-display text-lg mb-3">1. Menambahkan & Deploy Proyek Baru</h2>
                    <p class="mb-4 text-[#525252] dark:text-white/70 text-sm">
                        Sistem Ryaze mendukung <i>deployment</i> aplikasi secara otomatis melalui dua metode utama: penarikan kode dari <strong>Git Repository</strong> atau mengunggah <strong>File ZIP</strong>. Setiap aplikasi yang di-<i>deploy</i> akan diatur dalam direktori terpisah dan dikelola oleh sistem <i>process manager</i> kami (seperti PM2 untuk Node.js atau reverse proxy Nginx/OpenResty).
                    </p>
                    
                    <div class="ui-panel overflow-hidden">
                        <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                            <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">Langkah Deployment Otomatis</span>
                            <span class="ui-status"><span class="ui-status-dot"></span> 3 Langkah</span>
                        </div>
                        <ul class="px-6 text-sm text-[#525252] dark:text-white/70">
                            <li class="flex items-start gap-3 py-4 border-b border-[#EDF2F7] dark:border-[#24384F]">
                                <span class="w-5 h-5 shrink-0 mt-0.5 flex items-center justify-center border border-[#E3EAF1] dark:border-[#2A4463] bg-[#F0F6FC] dark:bg-[#0F1D30] text-[#1D5B96] dark:text-[#6BA8DC] font-mono text-[10px] font-bold">1</span>
                                <div>
                                    <strong>Pilih Metode:</strong> Gunakan opsi Git (masukkan URL Repositori publik/privat dan nama Branch) atau opsi Upload (unggah arsip ZIP proyek Anda), lalu tentukan kerangka kerja yang sesuai (misal: Laravel, Next.js, React).
                                </div>
                            </li>
                            <li class="flex items-start gap-3 py-4 border-b border-[#EDF2F7] dark:border-[#24384F]">
                                <span class="w-5 h-5 shrink-0 mt-0.5 flex items-center justify-center border border-[#E3EAF1] dark:border-[#2A4463] bg-[#F0F6FC] dark:bg-[#0F1D30] text-[#1D5B96] dark:text-[#6BA8DC] font-mono text-[10px] font-bold">2</span>
                                <div>
                                    <strong>Tentukan Subdomain:</strong> Nama proyek Anda akan langsung menjadi alamat akses (contoh: jika nama proyek adalah <code>app</code>, maka web dapat diakses di <code class="font-mono bg-white dark:bg-[#0F1D30] border border-[#E3EAF1] dark:border-[#2A4463] text-[#0F1A2A] dark:text-white px-1 py-0.5 text-xs mx-1">app.ryaze.my.id</code>).
                                </div>
                            </li>
                            <li class="flex items-start gap-3 py-4">
                                <span class="w-5 h-5 shrink-0 mt-0.5 flex items-center justify-center border border-[#E3EAF1] dark:border-[#2A4463] bg-[#F0F6FC] dark:bg-[#0F1D30] text-[#1D5B96] dark:text-[#6BA8DC] font-mono text-[10px] font-bold">3</span>
                                <div>
                                    <strong>Instalasi Dependensi Otomatis:</strong> Saat Anda menekan <i>Deploy</i>, pekerja latar belakang Ryaze akan secara otomatis mengunduh kode Anda, menjalankan perintah instalasi paket (seperti <code>npm install</code> atau <code>composer install</code>), dan menyambungkannya ke <i>Reverse Proxy</i>.
                                </div>
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- Section 2: File Manager -->
                <section>
                    <h2 class="ui-display text-lg mb-3">2. Manajemen Berkas (File Manager)</h2>
                    <p class="mb-4 text-[#525252] dark:text-white/70 text-sm">
                        Anda tidak memerlukan aplikasi FTP pihak ketiga (seperti FileZilla). Ryaze telah menyediakan antarmuka <i>File Manager</i> yang terintegrasi penuh dengan sistem berkas proyek Anda di <i>server</i>.
                    </p>
                    
                    <div class="ui-grid grid-cols-1 sm:grid-cols-2 mb-4">
                        <div class="ui-cell p-4 sm:p-5">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-[#0F1A2A] dark:text-white text-sm">Code Editor In-Browser</h4>
                                <div class="ui-icon-box"><i class="fa-solid fa-code text-xs"></i></div>
                            </div>
                            <p class="text-xs text-[#525252] dark:text-white/50 leading-relaxed">Anda dapat membuka, mengedit, dan menyimpan perubahan langsung pada file teks sumber kode Anda (seperti HTML, JS, PHP, atau konfigurasi) langsung dari dasbor web.</p>
                        </div>
                        <div class="ui-cell-alt p-4 sm:p-5">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-[#0F1A2A] dark:text-white text-sm">Manajemen Aset (Upload/Delete)</h4>
                                <div class="ui-icon-box"><i class="fa-solid fa-folder-open text-xs"></i></div>
                            </div>
                            <p class="text-xs text-[#525252] dark:text-white/50 leading-relaxed">Unggah file pendukung, aset gambar, atau hapus direktori yang tidak diperlukan. Terdapat batasan ukuran maksimal 10MB per unggahan file melalui antarmuka web.</p>
                        </div>
                    </div>

                    <div class="ui-panel flex items-start gap-4 p-5">
                        <div class="ui-icon-box"><i class="fa-solid fa-lock text-xs"></i></div>
                        <div>
                            <h4 class="text-sm font-semibold text-[#0F1A2A] dark:text-white mb-1">Kebijakan Anti-Malware (Web Upload)</h4>
                            <p class="text-xs text-[#525252] dark:text-white/70">Untuk mencegah kerentanan sistem akibat unggahan naskah berbahaya (misalnya <i>Web Shell</i>), kami secara tegas memblokir unggahan ekstensi <i>executable</i> tertentu (seperti <code>.php</code>, <code>.sh</code>, <code>.exe</code>) jika dilakukan melalui File Manager. Jika Anda perlu mengunggah file tersebut, Anda <strong>wajib</strong> mendorongnya melalui Git.</p>
                        </div>
                    </div>
                </section>

                <!-- Section 3: Environment -->
                <section>
                    <h2 class="ui-display text-lg mb-3">3. Konfigurasi Environment Variables (.env)</h2>
                    <p class="mb-3 text-[#525252] dark:text-white/70 text-sm">
                        Setiap aplikasi modern memerlukan variabel lingkungan untuk menyimpan kredensial rahasia (koneksi basis data, kunci API, mode <i>debug</i>). Ryaze menyediakan tab khusus <strong>Pengaturan .env</strong> di halaman detail proyek Anda.
                    </p>
                    <p class="text-[#525252] dark:text-white/70 text-sm">
                        Ketika Anda memodifikasi dan menyimpan nilai di tab ini, Ryaze akan secara otomatis menulis ulang file <code>.env</code> di <i>root</i> direktori aplikasi Anda. Perhatikan bahwa aplikasi perlu dijalankan ulang agar membaca variabel baru — untuk aplikasi Node.js cukup tekan tombol <strong>Restart</strong> pada halaman Aplikasi Node.
                    </p>
                </section>

                <!-- Section 4: Settings -->
                <section>
                    <h2 class="ui-display text-lg mb-3">4. Keamanan & Optimasi Trafik</h2>
                    <p class="mb-4 text-[#525252] dark:text-white/70 text-sm">
                        Di bawah tab <strong>Settings</strong> proyek Anda, terdapat konfigurasi sakelar (<i>toggle</i>) yang secara langsung mengubah aturan <i>Routing</i> Nginx untuk aplikasi Anda.
                    </p>

                    <div class="ui-grid grid-cols-1">
                        <div class="ui-cell flex items-center p-4 sm:p-5 gap-4">
                            <div class="ui-icon-box shrink-0"><i class="fa-solid fa-tools text-xs"></i></div>
                            <div>
                                <h4 class="font-semibold text-[#0F1A2A] dark:text-white text-sm">Maintenance Mode</h4>
                                <p class="text-xs text-[#525252] dark:text-white/50 mt-0.5">Jika diaktifkan, sistem akan mengembalikan halaman statis perbaikan 503 (Under Maintenance) kepada pengunjung. Sangat berguna saat Anda melakukan migrasi basis data besar.</p>
                            </div>
                        </div>
                        <div class="ui-cell-alt flex items-center p-4 sm:p-5 gap-4">
                            <div class="ui-icon-box shrink-0"><i class="fa-solid fa-shield-halved text-xs"></i></div>
                            <div>
                                <h4 class="font-semibold text-[#0F1A2A] dark:text-white text-sm">Under Attack Mode (Rate Limiting)</h4>
                                <p class="text-xs text-[#525252] dark:text-white/50 mt-0.5">Berfungsi untuk membatasi jumlah permintaan berlebihan dari satu alamat IP dalam waktu singkat untuk memitigasi potensi <i>Denial of Service</i> (DDoS) kecil atau serangan bot.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 5: Web Terminal -->
                <section>
                    <h2 class="ui-display text-lg mb-3">5. Web Terminal Cerdas</h2>
                    <p class="mb-4 text-[#525252] dark:text-white/70 text-sm">
                        Terminal yang tertanam di dasbor Ryaze memungkinkan Anda menjalankan perintah operasional layaknya SSH, namun dengan antarmuka berbasis web. Terminal ini diawasi ketat oleh Sistem Pencegahan Intrusi kami.
                    </p>

                    <div class="ui-panel overflow-hidden">
                        <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                            <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">Web Terminal &middot; Whitelist</span>
                            <span class="ui-status"><span class="ui-status-dot"></span> Live</span>
                        </div>
                        <div class="p-5 sm:p-6 font-mono text-[12px] sm:text-[12.5px] leading-[1.9]">
                            <p class="text-[#737373] dark:text-white/35"># Perintah Operasional yang Diizinkan (Whitelist):</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-x-4">
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">ls</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">cat</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">head</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">tail</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">wc</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">grep</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">find</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">echo</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">pwd</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">whoami</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">date</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">mkdir</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">touch</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">cp</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">mv</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">rm</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">chmod</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">chown</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">nano</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">curl</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">git</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">php</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">composer</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">npm</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">npx</span></p>
                                <p><span class="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span class="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span class="text-[#0F1A2A] dark:text-white">node</span></p>
                            </div>
                            <p class="pt-2.5 mt-2.5 border-t border-[#E3EAF1] dark:border-[#2A4463]">
                                <span class="text-[#737373] dark:text-white/35">&#8594;</span>
                                <span class="inline-block w-[7px] h-[14px] bg-[#1D5B96] dark:bg-[#6BA8DC] align-middle ml-1 animate-pulse"></span>
                            </p>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-[#525252] dark:text-[#8A9BAE]">
                        <strong>Catatan Keamanan:</strong> Untuk menjaga kestabilan peladen induk, upaya menggunakan operator perangkaian ganda (seperti <code>&&</code> atau <code>|</code>), perpindahan keluar direktori kerja (<code>../</code>), atau membaca konfigurasi inti (<code>/etc/</code>) akan segera dibatalkan oleh pembungkus terminal kami.
                    </p>
                </section>

                <!-- Section 6: Storage -->
                <section>
                    <h2 class="ui-display text-lg mb-3">6. Manajemen Penyimpanan (Storage)</h2>
                    <p class="mb-4 text-[#525252] dark:text-white/70 text-sm">
                        Kuota penyimpanan Anda dihitung secara <strong>akumulatif per akun pengguna</strong>, bukan per proyek. Artinya, Anda bebas mendirikan proyek sebanyak mungkin selama ruang kosong total Anda mencukupi.
                    </p>
                    
                    <div class="ui-panel overflow-hidden">
                        <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                            <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">Praktik Manajemen Kuota</span>
                        </div>
                        <ul class="px-6">
                            <li class="flex items-start gap-3 py-3.5 text-sm text-[#525252] dark:text-white/70 border-b border-[#EDF2F7] dark:border-[#24384F]">
                                <span class="ui-check-mark mt-0.5">&#10003;</span>
                                <span>Jika penyimpanan Anda menyentuh angka 100%, upaya modifikasi basis data atau proses instalasi library pada proyek Anda (seperti <code>npm install</code>) akan mulai menolak untuk berfungsi (gagal).</span>
                            </li>
                            <li class="flex items-start gap-3 py-3.5 text-sm text-[#525252] dark:text-white/70 border-b border-[#EDF2F7] dark:border-[#24384F]">
                                <span class="ui-check-mark mt-0.5">&#10003;</span>
                                <span>Terapkan aturan pada file <code>.gitignore</code> Anda agar tidak menyertakan folder <code>vendor</code> (PHP) atau <code>node_modules</code> (Node.js). Sistem kami mampu merakit ulang komponen tersebut saat waktu <i>deployment</i>, sehingga menghemat konsumsi kuota mentah Anda.</span>
                            </li>
                            <li class="flex items-start gap-3 py-3.5 text-sm text-[#525252] dark:text-white/70">
                                <span class="ui-check-mark mt-0.5">&#10003;</span>
                                <span>Anda selalu dapat <strong>Meningkatkan Paket</strong> (Upgrade Storage) melalui saluran pembayaran terintegrasi <strong>QRIS</strong> pada halaman Profil / Penyimpanan.</span>
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- Section 7: Live Preview -->
                <section>
                    <h2 class="ui-display text-lg mb-3">7. Mode Development Server (Live Preview)</h2>
                    <p class="mb-3 text-[#525252] dark:text-white/70 text-sm">
                        Ryaze memfasilitasi lingkungan <i>Live Preview</i> yang memungkinkan Anda mempublikasikan peladen pengembangan lokal (seperti <code>php artisan serve</code>, <code>npm run dev</code>, atau <code>npx vite</code>) sehingga dapat diakses secara publik.
                    </p>
                    <p class="text-[#525252] dark:text-white/70 text-sm">
                        Mekanismenya sangat mudah: Klik ikon <strong>Nyalakan Server</strong> pada dasbor proyek Anda. Sistem kami akan membangkitkan pekerja di latar belakang dan menerbitkan URL acak (contoh: <code class="font-mono text-xs text-[#0F1A2A] dark:text-white bg-[#F0F6FC] dark:bg-[#0F1D30] px-1 py-0.5">devX.ryaze.my.id</code>). Segala perubahan yang Anda simpan melalui File Manager atau Web Terminal akan dipantulkan langsung ke URL pratinjau tersebut tanpa perlu merakit ulang aplikasi secara utuh.
                    </p>
                </section>

                <!-- Section 8: Aplikasi Node -->
                <section>
                    <h2 class="ui-display text-lg mb-3">8. Aplikasi Node.js (Managed Node App)</h2>
                    <p class="mb-4 text-[#525252] dark:text-white/70 text-sm">
                        Menu <strong>Aplikasi Node</strong> di sidebar Hosting adalah manajer aplikasi Node.js terkelola — mirip dengan <i>Setup Node.js App</i> di cPanel. Proses Node Anda berjalan di <i>runtime container</i> terpisah yang diawasi <strong>PM2</strong>, lengkap dengan manajemen <i>entry file</i>, <i>environment variables</i>, log <i>real-time</i>, dan kontrol Start/Stop/Restart dari panel.
                    </p>

                    <div class="ui-panel overflow-hidden mb-4">
                        <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                            <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">Dua Cara Membuat Aplikasi</span>
                            <span class="ui-status"><span class="ui-status-dot"></span> Aplikasi Node</span>
                        </div>
                        <div class="ui-grid grid-cols-1 sm:grid-cols-2">
                            <div class="ui-cell p-5">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-semibold text-[#0F1A2A] dark:text-white text-sm">Bikin Project + Aplikasi Baru</h4>
                                    <div class="ui-icon-box"><i class="fa-solid fa-plus text-xs"></i></div>
                                </div>
                                <p class="text-xs text-[#525252] dark:text-white/50 leading-relaxed">Isi subdomain dan sumber kode (Git/ZIP) — sistem membuat proyek hosting sekaligus aplikasi Node-nya dalam satu alur, termasuk <code>npm install</code> dan build otomatis.</p>
                            </div>
                            <div class="ui-cell-alt p-5">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-semibold text-[#0F1A2A] dark:text-white text-sm">Pakai Project yang Sudah Ada</h4>
                                    <div class="ui-icon-box"><i class="fa-solid fa-folder text-xs"></i></div>
                                </div>
                                <p class="text-xs text-[#525252] dark:text-white/50 leading-relaxed">Sudah punya proyek ter-deploy? Pilih proyeknya, tentukan <i>entry file</i> (mis. <code>server.js</code> atau <code>dist/index.js</code>), lalu simpan. Kode tidak diunduh ulang.</p>
                            </div>
                        </div>
                        <p class="px-5 py-3 text-xs text-[#525252] dark:text-white/50 border-t border-[#E3EAF1] dark:border-[#2A4463]">
                            <strong>Catatan:</strong> <i>Framework</i> Node tidak tersedia di halaman <strong>Deploy Aplikasi</strong>. Deployment di sana hanya menyiapkan berkas; untuk proyek Node, pembuatan aplikasi dilakukan dari halaman <strong>Aplikasi Node</strong>.
                        </p>
                    </div>

                    <h3 class="text-sm font-semibold text-[#0F1A2A] dark:text-white mb-2">Konfigurasi Aplikasi (tab Config)</h3>
                    <ul class="mb-4 px-1 space-y-2 text-sm text-[#525252] dark:text-white/70">
                        <li class="flex gap-2"><span class="ui-check-mark mt-0.5">&#10003;</span><span><strong>Entry File</strong> — file yang dieksekusi (mis. <code>server.js</code>), atau nama <i>script</i> NPM (mis. <code>start</code>). Kosongkan untuk deteksi otomatis.</span></li>
                        <li class="flex gap-2"><span class="ui-check-mark mt-0.5">&#10003;</span><span><strong>NODE_ENV</strong> — pilih <code>production</code> atau <code>development</code>.</span></li>
                        <li class="flex gap-2"><span class="ui-check-mark mt-0.5">&#10003;</span><span><strong>Environment Variables</strong> — pasangan kunci=nilai (mis. <code>DB_HOST=...</code>) yang disuntikkan ke proses setiap kali aplikasi dijalankan.</span></li>
                    </ul>

                    <div class="ui-panel overflow-hidden mb-4">
                        <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                            <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">Kontrol & Port Tetap (Sticky Port)</span>
                            <span class="ui-status"><span class="ui-status-dot"></span> Auto-recovery</span>
                        </div>
                        <ul class="px-6 text-sm text-[#525252] dark:text-white/70">
                            <li class="flex items-start gap-3 py-4 border-b border-[#EDF2F7] dark:border-[#24384F]">
                                <span class="w-5 h-5 shrink-0 mt-0.5 flex items-center justify-center border border-[#E3EAF1] dark:border-[#2A4463] bg-[#F0F6FC] dark:bg-[#0F1D30] text-[#1D5B96] dark:text-[#6BA8DC] font-mono text-[10px] font-bold">1</span>
                                <div><strong>Start / Stop / Restart / Re-deploy</strong> — semua kontrol lifecycle tersedia sebagai tombol di kartu aplikasi. Log stdout/stderr aplikasi dapat dibuka lewat tombol <i>Log</i>.</div>
                            </li>
                            <li class="flex items-start gap-3 py-4 border-b border-[#EDF2F7] dark:border-[#24384F]">
                                <span class="w-5 h-5 shrink-0 mt-0.5 flex items-center justify-center border border-[#E3EAF1] dark:border-[#2A4463] bg-[#F0F6FC] dark:bg-[#0F1D30] text-[#1D5B96] dark:text-[#6BA8DC] font-mono text-[10px] font-bold">2</span>
                                <div><strong>Port Tetap:</strong> port aplikasi dialokasi sekali lalu <strong>dipertahankan</strong> pada Stop/Start/Restart/Re-deploy berikutnya — selama port tersebut masih kosong. Domain Anda tidak berubah ketika aplikasi dihidupkan ulang.</div>
                            </li>
                            <li class="flex items-start gap-3 py-4">
                                <span class="w-5 h-5 shrink-0 mt-0.5 flex items-center justify-center border border-[#E3EAF1] dark:border-[#2A4463] bg-[#F0F6FC] dark:bg-[#0F1D30] text-[#1D5B96] dark:text-[#6BA8DC] font-mono text-[10px] font-bold">3</span>
                                <div><strong>Auto-recovery:</strong> monitor sistem memeriksa aplikasi setiap menit. Aplikasi yang <i>crash</i> dibangkitkan ulang otomatis dan pemiliknya menerima notifikasi bila gagal berjalan.</div>
                            </li>
                        </ul>
                    </div>

                    <div class="ui-panel flex items-start gap-4 p-5">
                        <div class="ui-icon-box"><i class="fa-solid fa-circle-info text-xs"></i></div>
                        <div>
                            <h4 class="text-sm font-semibold text-[#0F1A2A] dark:text-white mb-1">Saat Aplikasi Dihentikan</h4>
                            <p class="text-xs text-[#525252] dark:text-white/70">Aplikasi Node yang di-<i>stop</i> tidak lagi dilayani sebagai aplikasi — pengunjung akan melihat halaman <i>&ldquo;Aplikasi sedang tidak berjalan&rdquo;</i> (503), bukan build statis lama. Jalankan kembali kapan saja melalui tombol <i>Start</i>.</p>
                        </div>
                    </div>

                    <p class="mt-4 text-xs text-[#525252] dark:text-[#8A9BAE]">
                        <strong>Batas Aplikasi per Paket:</strong> jumlah aplikasi Node yang aktif mengikuti paket hosting Anda — <strong>Free: 0</strong> (runtime Node tidak tersedia), <strong>Starter: 1</strong>, <strong>Pro: 3</strong>, <strong>Business: 10</strong> aplikasi. Kuota aktif Anda tampil pada halaman Aplikasi Node.
                    </p>
                </section>

                <!-- Section 9: SSH & SFTP -->
                <section>
                    <h2 class="ui-display text-lg mb-3">9. Akses SSH &amp; SFTP</h2>
                    <p class="mb-6 text-[#525252] dark:text-white/70 text-sm">
                        Ingin mengedit file project langsung dari komputer sendiri? Gunakan akses SSH/SFTP. Anda bisa membuka terminal, atau mengupload file lewat aplikasi seperti WinSCP — tanpa perlu membuka port atau mengatur jaringan.
                    </p>

                    <h3 class="text-sm font-semibold text-[#0F1A2A] dark:text-white mb-2">Akses SSH atau SFTP?</h3>
                    <div class="grid gap-4 md:grid-cols-2 mb-6">
                        <div class="ui-panel p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fa-solid fa-terminal text-[#1D5B96] dark:text-[#6BA8DC]"></i>
                                <h4 class="font-semibold text-sm text-[#0F1A2A] dark:text-white">SSH (Terminal)</h4>
                            </div>
                            <p class="text-xs text-[#525252] dark:text-white/70">Untuk menjalankan perintah seperti git, npm, composer, atau php. Cocok untuk paket <strong>Pro/Business</strong>.</p>
                        </div>
                        <div class="ui-panel p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fa-solid fa-folder-open text-[#1D5B96] dark:text-[#6BA8DC]"></i>
                                <h4 class="font-semibold text-sm text-[#0F1A2A] dark:text-white">SFTP (File)</h4>
                            </div>
                            <p class="text-xs text-[#525252] dark:text-white/70">Untuk upload, download, dan edit file. Cocok untuk semua paket. Bisa memakai WinSCP.</p>
                        </div>
                    </div>

                    <h3 class="text-sm font-semibold text-[#0F1A2A] dark:text-white mb-2">Langkah 1 — Buat akses di panel</h3>
                    <ol class="list-decimal pl-5 text-sm text-[#525252] dark:text-white/70 space-y-2 mb-4">
                        <li>Buka menu <strong>Akses SSH</strong>.</li>
                        <li>Klik <strong>Buat Akses SSH</strong>, pilih project dan jenis akses.</li>
                        <li>Simpan <strong>username</strong>, <strong>password</strong>, <strong>hostname</strong>, dan <strong>port</strong> yang muncul. <strong>Password hanya ditampilkan sekali</strong> — jangan sampai terlewat.</li>
                    </ol>
                    <p class="text-xs text-[#525252] dark:text-white/70 mb-6">Username memakai username akun Ryaze Anda, password dibuat acak otomatis. Lupa password? Cabut akses lalu buat lagi.</p>

                    <h3 class="text-sm font-semibold text-[#0F1A2A] dark:text-white mb-2">Langkah 2 — Siapkan komputer Anda</h3>
                    <p class="text-xs text-[#525252] dark:text-white/70 mb-3">Unduh satu aplikasi kecil bernama <strong>Cloudflared</strong>. Aplikasi ini yang menghubungkan komputer Anda ke hosting.</p>
                    <ul class="list-disc pl-5 text-xs text-[#525252] dark:text-white/70 space-y-2 mb-3">
                        <li><strong>Windows:</strong> unduh <code>cloudflared.exe</code>, letakkan di satu folder bersama file dari panel.</li>
                        <li><strong>Mac:</strong> jalankan <code>brew install cloudflare/cloudflare/cloudflared</code></li>
                        <li><strong>Linux:</strong> unduh file <code>cloudflared</code>, lalu jalankan <code>chmod +x cloudflared</code></li>
                    </ul>
                    <p class="text-xs mb-6">
                        <a href="https://developers.cloudflare.com/cloudflare-one/connections/connect-networks/downloads/" target="_blank" rel="noopener" class="text-[#1D5B96] dark:text-[#6BA8DC] underline">Unduh Cloudflared di sini</a>
                    </p>

                    <h3 class="text-sm font-semibold text-[#0F1A2A] dark:text-white mb-2">Langkah 3 — Hubungkan</h3>
                    <div class="ui-panel p-5 mb-6 space-y-5 text-xs text-[#525252] dark:text-white/70">
                        <div>
                            <p class="font-semibold text-[#0F1A2A] dark:text-white mb-2">Windows (paling mudah)</p>
                            <ol class="list-decimal pl-5 space-y-2">
                                <li>Di folder paket dari panel, klik dua kali <code>putty.bat</code>.</li>
                                <li>Jendela PuTTY akan terbuka. Masukkan <strong>username</strong> dan <strong>password</strong> dari panel.</li>
                                <li>Selesai — Anda sudah masuk ke terminal project.</li>
                                <li>Untuk upload file, buka <strong>WinSCP</strong>: protokol <em>SFTP</em>, host <code>127.0.0.1</code>, port sesuai panel, lalu masukkan username dan password yang sama.</li>
                            </ol>
                        </div>
                        <div>
                            <p class="font-semibold text-[#0F1A2A] dark:text-white mb-2">Mac / Linux</p>
                            <ol class="list-decimal pl-5 space-y-2">
                                <li>Buka terminal, masuk ke folder paket, lalu jalankan <code>./ssh.sh</code> untuk terminal atau <code>./sftp.sh</code> untuk file.</li>
                                <li>Masukkan password saat diminta.</li>
                                <li>Selesai. Ketik <code>exit</code> atau <code>quit</code> untuk keluar.</li>
                            </ol>
                        </div>
                    </div>
                    <p class="text-xs text-[#525252] dark:text-white/70 mb-6">Semua perintah di halaman <strong>Akses SSH</strong> sudah siap dipakai — tinggal salin. Tidak perlu kunci SSH atau pengaturan tambahan.</p>

                    <h3 class="text-sm font-semibold text-[#0F1A2A] dark:text-white mb-2">Pertanyaan yang sering ditanya</h3>
                    <div class="ui-panel p-5 mb-6 space-y-4 text-xs text-[#525252] dark:text-white/70">
                        <div>
                            <p class="font-semibold text-[#0F1A2A] dark:text-white mb-1">Kenapa perlu aplikasi Cloudflared?</p>
                            <p>Karena koneksi hosting kami memakai jalur aman Cloudflare. Aplikasi kecil ini menjadi jembatan antara komputer Anda dan project.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-[#0F1A2A] dark:text-white mb-1">Kenapa tidak bisa pakai SSH biasa?</p>
                            <p>Akses SSH kami tidak menggunakan port publik, supaya lebih aman. Cukup pakai script atau tombol yang sudah disediakan di panel.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-[#0F1A2A] dark:text-white mb-1">Saya lupa password / sudah tidak bisa masuk?</p>
                            <p>Buka menu <strong>Akses SSH</strong>, klik <strong>Cabut</strong>, lalu <strong>Buat Akses SSH</strong> lagi. Kredensial baru akan dibuat. Project dan file Anda tidak berubah.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-[#0F1A2A] dark:text-white mb-1">Muncul pesan "Connection refused"?</p>
                            <p>Jalankan dulu <code>putty.bat</code> atau script <code>ssh.sh</code> — jembatannya belum aktif. Biarkan jendela itu terbuka selama Anda terhubung.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-[#0F1A2A] dark:text-white mb-1">Muncul "This service allows sftp connections only"?</p>
                            <p>Akses Anda bertipe SFTP. Gunakan WinSCP atau <code>./sftp.sh</code> untuk mengelola file.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-[#0F1A2A] dark:text-white mb-1">Bisa keluar dari folder project?</p>
                            <p>Tidak. Terminal langsung berada di folder project Anda, dan naik ke folder lain tidak diizinkan.</p>
                        </div>
                    </div>

                    <div class="ui-panel flex items-start gap-4 p-5">
                        <div class="ui-icon-box"><i class="fa-solid fa-lock text-xs"></i></div>
                        <div>
                            <h4 class="text-sm font-semibold text-[#0F1A2A] dark:text-white mb-1">Jaga kredensial Anda</h4>
                            <p class="text-xs text-[#525252] dark:text-white/70">Jangan bagikan username, password, atau file <code>cloudflared.conf</code> kepada orang lain. Setiap project punya akses terpisah.</p>
                        </div>
                    </div>
                </section>

                <hr class="border-[#EDF2F7] dark:border-[#2A4463] my-6">
                
                <div class="text-center">
                    <p class="text-[#525252] dark:text-[#8A9BAE] text-xs">Masih mengalami kendala? Silakan hubungi tim dukungan teknis kami.</p>
                </div>

            </div>
        </div>
    </x-ui.page-layout>
@endsection
