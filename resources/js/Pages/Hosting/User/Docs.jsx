import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Docs() {
    return (
        <DashboardLayout title="Panduan & Dokumentasi">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-book-open text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Panduan & Dokumentasi</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Pelajari cara menggunakan seluruh fitur hosting Ryaze dengan optimal.</p>
                    </div>
                </div>
            </div>

            <div className="mt-6 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-6">
                <div className="space-y-8 text-[#333] dark:text-white leading-relaxed max-w-5xl">
                    {/* Intro */}
                    <div className="text-[#999] dark:text-white/40 text-sm leading-relaxed mb-4">
                        Selamat datang di dokumentasi resmi Hosting Ryaze! Panduan esensial untuk mendeploy, mengelola, dan menskalakan aplikasi modern Anda di atas infrastruktur berkinerja tinggi kami.
                    </div>

                    <hr className="border-slate-100 dark:border-[#1a1a2e]" />

                    {/* Section 1: Deploy */}
                    <section>
                        <h2 className="text-lg font-bold text-[#333] dark:text-white mb-3">1. Menambahkan & Deploy Proyek Baru</h2>
                        <p className="mb-4 text-[#666] dark:text-white/60 text-sm">
                            Sistem Ryaze mendukung <em>deployment</em> aplikasi secara otomatis melalui dua metode utama: penarikan kode dari <strong>Git Repository</strong> atau menggunakan <strong>Template Instan</strong> bawaan. Setiap aplikasi yang di-<em>deploy</em> akan diatur dalam direktori terpisah dan dikelola oleh sistem <em>process manager</em> kami (seperti PM2 untuk Node.js atau reverse proxy Nginx/OpenResty).
                        </p>
                        <div className="bg-[#fafafa] dark:bg-white/[0.02] rounded-lg p-4 border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="font-semibold text-[#333] dark:text-white text-sm mb-3">Langkah Deployment Otomatis</h3>
                            <ul className="space-y-3 text-[#666] dark:text-white/60 text-sm">
                                {[
                                    { num: '1', title: 'Pilih Metode:', desc: 'Gunakan opsi Git (masukkan URL Repositori publik/privat dan nama Branch) atau pilih Template kerangka kerja yang telah kami siapkan (misal: Laravel, Next.js, React).' },
                                    { num: '2', title: 'Tentukan Subdomain:', desc: 'Nama proyek Anda akan langsung menjadi alamat akses (contoh: jika nama proyek adalah app, maka web dapat diakses di app.ryaze.my.id).' },
                                    { num: '3', title: 'Instalasi Dependensi Otomatis:', desc: 'Saat Anda menekan Deploy, pekerja latar belakang Ryaze akan secara otomatis mengunduh kode Anda, menjalankan perintah instalasi paket (seperti npm install atau composer install), dan menyambungkannya ke Reverse Proxy.' },
                                ].map(item => (
                                    <li key={item.num} className="flex items-start">
                                        <span className="bg-slate-200 dark:bg-slate-700 text-[#333] dark:text-white rounded-full w-5 h-5 flex items-center justify-center text-[10px] font-bold mr-3 shrink-0 mt-0.5">{item.num}</span>
                                        <div><strong>{item.title}</strong> {item.desc}</div>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </section>

                    {/* Section 2: File Manager */}
                    <section>
                        <h2 className="text-lg font-bold text-[#333] dark:text-white mb-3">2. Manajemen Berkas (File Manager)</h2>
                        <p className="mb-4 text-[#666] dark:text-white/60 text-sm">
                            Anda tidak memerlukan aplikasi FTP pihak ketiga (seperti FileZilla). Ryaze telah menyediakan antarmuka <em>File Manager</em> yang terintegrasi penuh dengan sistem berkas proyek Anda di <em>server</em>.
                        </p>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div className="p-4 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg">
                                <h4 className="font-semibold text-[#333] dark:text-white text-sm mb-1">Code Editor In-Browser</h4>
                                <p className="text-xs text-[#999] dark:text-white/40 leading-relaxed">Anda dapat membuka, mengedit, dan menyimpan perubahan langsung pada file teks sumber kode Anda (seperti HTML, JS, PHP, atau konfigurasi) langsung dari dasbor web.</p>
                            </div>
                            <div className="p-4 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg">
                                <h4 className="font-semibold text-[#333] dark:text-white text-sm mb-1">Manajemen Aset (Upload/Delete)</h4>
                                <p className="text-xs text-[#999] dark:text-white/40 leading-relaxed">Unggah file pendukung, aset gambar, atau hapus direktori yang tidak diperlukan. Terdapat batasan ukuran maksimal 10MB per unggahan file melalui antarmuka web.</p>
                            </div>
                        </div>
                        <div className="flex items-start bg-[#fafafa] dark:bg-white/[0.02] p-4 rounded-lg border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <i className="fa-solid fa-lock text-[#999] dark:text-white/40 mt-1 mr-3 text-sm"></i>
                            <div>
                                <h4 className="text-sm font-semibold text-[#333] dark:text-white mb-1">Kebijakan Anti-Malware (Web Upload)</h4>
                                <p className="text-xs text-[#666] dark:text-white/60">Untuk mencegah kerentanan sistem akibat unggahan naskah berbahaya (misalnya <em>Web Shell</em>), kami secara tegas memblokir unggahan ekstensi <em>executable</em> tertentu (seperti <code>.php</code>, <code>.sh</code>, <code>.exe</code>) jika dilakukan melalui File Manager. Jika Anda perlu mengunggah file tersebut, Anda <strong>wajib</strong> mendorongnya melalui Git.</p>
                            </div>
                        </div>
                    </section>

                    {/* Section 3: Environment */}
                    <section>
                        <h2 className="text-lg font-bold text-[#333] dark:text-white mb-3">3. Konfigurasi Environment Variables (.env)</h2>
                        <p className="mb-3 text-[#666] dark:text-white/60 text-sm">
                            Setiap aplikasi modern memerlukan variabel lingkungan untuk menyimpan kredensial rahasia (koneksi basis data, kunci API, mode <em>debug</em>). Ryaze menyediakan tab khusus <strong>Pengaturan .env</strong> di halaman detail proyek Anda.
                        </p>
                        <p className="text-[#666] dark:text-white/60 text-sm">
                            Ketika Anda memodifikasi dan menyimpan nilai di tab ini, Ryaze akan secara otomatis menulis ulang file <code>.env</code> di <em>root</em> direktori aplikasi Anda. Perhatikan bahwa beberapa kerangka kerja (seperti Node.js) mungkin memerlukan <em>restart</em> manual dari Terminal agar dapat membaca variabel baru.
                        </p>
                    </section>

                    {/* Section 4: Settings */}
                    <section>
                        <h2 className="text-lg font-bold text-[#333] dark:text-white mb-3">4. Keamanan & Optimasi Trafik</h2>
                        <p className="mb-4 text-[#666] dark:text-white/60 text-sm">
                            Di bawah tab <strong>Settings</strong> proyek Anda, terdapat konfigurasi sakelar (<em>toggle</em>) yang secara langsung mengubah aturan <em>Routing</em> Nginx untuk aplikasi Anda.
                        </p>
                        <div className="space-y-3">
                            <div className="flex items-center p-3 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg">
                                <div className="flex-shrink-0 mr-4 text-[#999] dark:text-white/40"><i className="fa-solid fa-tools"></i></div>
                                <div>
                                    <h4 className="font-semibold text-[#333] dark:text-white text-sm">Maintenance Mode</h4>
                                    <p className="text-xs text-[#999] dark:text-white/40 mt-0.5">Jika diaktifkan, sistem akan mengembalikan halaman statis perbaikan 503 (Under Maintenance) kepada pengunjung. Sangat berguna saat Anda melakukan migrasi basis data besar.</p>
                                </div>
                            </div>
                            <div className="flex items-center p-3 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg">
                                <div className="flex-shrink-0 mr-4 text-[#999] dark:text-white/40"><i className="fa-solid fa-shield-halved"></i></div>
                                <div>
                                    <h4 className="font-semibold text-[#333] dark:text-white text-sm">Under Attack Mode (Rate Limiting)</h4>
                                    <p className="text-xs text-[#999] dark:text-white/40 mt-0.5">Berfungsi untuk membatasi jumlah permintaan berlebihan dari satu alamat IP dalam waktu singkat untuk memitigasi potensi <em>Denial of Service</em> (DDoS) kecil atau serangan bot.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Section 5: Web Terminal */}
                    <section>
                        <h2 className="text-lg font-bold text-[#333] dark:text-white mb-3">5. Web Terminal Cerdas</h2>
                        <p className="mb-4 text-[#666] dark:text-white/60 text-sm">
                            Terminal yang tertanam di dasbor Ryaze memungkinkan Anda menjalankan perintah operasional layaknya SSH, namun dengan antarmuka berbasis web. Terminal ini diawasi ketat oleh Sistem Pencegahan Intrusi kami.
                        </p>
                        <div className="bg-slate-900 text-slate-300 dark:text-slate-400 font-mono text-xs p-5 rounded-lg shadow-sm overflow-hidden relative">
                            <div className="flex gap-1.5 absolute top-3 left-3">
                                <div className="w-2.5 h-2.5 rounded-full bg-rose-500"></div>
                                <div className="w-2.5 h-2.5 rounded-full bg-amber-500"></div>
                                <div className="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                            </div>
                            <div className="mt-4">
                                <div className="text-slate-500 dark:text-slate-400 mb-2 border-b border-slate-700 pb-1"># Perintah Operasional yang Diizinkan (Whitelist):</div>
                                <div className="leading-relaxed text-emerald-400 dark:text-emerald-300">
                                    ls, cat, head, tail, wc, grep, find, echo, pwd, whoami, date, mkdir, touch, cp, mv, chmod, chown, nano, curl <br />
                                    git, php, composer, npm, npx, node, python, python3, pip, pip3
                                </div>
                            </div>
                        </div>
                        <p className="mt-3 text-xs text-[#999] dark:text-white/40">
                            <strong>Catatan Keamanan:</strong> Untuk menjaga kestabilan peladen induk, upaya menggunakan operator perangkaian ganda (seperti <code>&&</code> atau <code>|</code>), perpindahan keluar direktori kerja (<code>../</code>), atau membaca konfigurasi inti (<code>/etc/</code>) akan segera dibatalkan oleh pembungkus terminal kami.
                        </p>
                    </section>

                    {/* Section 6: Storage */}
                    <section>
                        <h2 className="text-lg font-bold text-[#333] dark:text-white mb-3">6. Manajemen Penyimpanan (Storage)</h2>
                        <p className="mb-4 text-[#666] dark:text-white/60 text-sm">
                            Kuota penyimpanan Anda dihitung secara <strong>akumulatif per akun pengguna</strong>, bukan per proyek. Artinya, Anda bebas mendirikan proyek sebanyak mungkin selama ruang kosong total Anda mencukupi.
                        </p>
                        <div className="bg-[#fafafa] dark:bg-white/[0.02] p-4 rounded-lg border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h4 className="font-semibold text-[#333] dark:text-white text-sm mb-2">Praktik Manajemen Kuota</h4>
                            <ul className="list-disc list-inside space-y-1.5 text-[#666] dark:text-white/60 text-sm">
                                <li>Jika penyimpanan Anda menyentuh angka 100%, upaya modifikasi basis data atau proses instalasi library pada proyek Anda (seperti <code>npm install</code>) akan mulai menolak untuk berfungsi (gagal).</li>
                                <li>Terapkan aturan pada file <code>.gitignore</code> Anda agar tidak menyertakan folder <code>vendor</code> (PHP), <code>node_modules</code> (Node.js), atau <code>venv</code> (Python). Sistem kami mampu merakit ulang komponen tersebut saat waktu <em>deployment</em>, sehingga menghemat konsumsi kuota mentah Anda.</li>
                                <li>Anda selalu dapat <strong>Meningkatkan Paket</strong> (Upgrade Storage) melalui saluran pembayaran terintegrasi <strong>Pakasir</strong> pada halaman Profil / Penyimpanan.</li>
                            </ul>
                        </div>
                    </section>

                    {/* Section 7: Live Preview */}
                    <section>
                        <h2 className="text-lg font-bold text-[#333] dark:text-white mb-3">7. Mode Development Server (Live Preview)</h2>
                        <p className="mb-3 text-[#666] dark:text-white/60 text-sm">
                            Ryaze memfasilitasi lingkungan <em>Live Preview</em> yang memungkinkan Anda mempublikasikan peladen pengembangan lokal (seperti <code>php artisan serve</code>, <code>npm run dev</code>, atau <code>npx vite</code>) sehingga dapat diakses secara publik.
                        </p>
                        <p className="text-[#666] dark:text-white/60 text-sm">
                            Mekanismenya sangat mudah: Klik ikon <strong>Nyalakan Server</strong> pada dasbor proyek Anda. Sistem kami akan membangkitkan pekerja di latar belakang dan menerbitkan URL acak (contoh: <code className="font-mono text-xs text-[#333] dark:text-white bg-slate-100 dark:bg-slate-700/50 px-1 py-0.5 rounded">devX.ryaze.my.id</code>). Segala perubahan yang Anda simpan melalui File Manager atau Web Terminal akan dipantulkan langsung ke URL pratinjau tersebut tanpa perlu merakit ulang aplikasi secara utuh.
                        </p>
                    </section>

                    {/* Section 8: Python */}
                    <section>
                        <h2 className="text-lg font-bold text-[#333] dark:text-white mb-3 flex items-center gap-2">
                            8. Panduan Khusus Deployment Python
                        </h2>
                        <p className="mb-5 text-[#666] dark:text-white/60 text-sm">
                            Infrastruktur Ryaze telah dioptimasi secara mendalam untuk mendukung deployment aplikasi berbasis Python seperti Flask, Django, FastAPI, hingga model Machine Learning. Kami menerapkan tingkat isolasi dan efisiensi sekelas <em>enterprise</em> agar aplikasi Anda berjalan stabil.
                        </p>
                        <div className="space-y-6">
                            <div>
                                <h4 className="font-semibold text-[#333] dark:text-white text-sm mb-1">1. Isolasi Virtual Environment Otomatis</h4>
                                <p className="text-sm text-[#666] dark:text-white/60 leading-relaxed">
                                    Untuk mencegah konflik versi library antar aplikasi, setiap proyek Python Anda diisolasi menggunakan <em>Virtual Environment</em> (<code className="font-mono bg-slate-100 dark:bg-slate-700/50 text-xs px-1 rounded">venv</code>). Saat Anda melakukan <em>deployment</em> pertama kali atau menekan tombol <em>Redeploy</em>, sistem kami akan secara otomatis membaca file <code className="font-mono bg-slate-100 dark:bg-slate-700/50 text-xs px-1 rounded">requirements.txt</code> dan menginstal seluruh dependensi Anda langsung ke dalam ruang isolasi proyek tersebut. Anda tidak perlu mengatur environment secara manual.
                                </p>
                            </div>
                            <div>
                                <h4 className="font-semibold text-[#333] dark:text-white text-sm mb-1">2. Web Terminal Auto-Alias</h4>
                                <p className="text-sm text-[#666] dark:text-white/60 leading-relaxed">
                                    Jika Anda perlu menginstal library tambahan secara manual di tengah proses pengembangan, Anda dapat langsung menggunakan fitur Web Terminal. Cukup ketikkan perintah standar seperti <code className="font-mono text-xs bg-slate-100 dark:bg-slate-700/50 px-1 rounded text-[#7c3aed] dark:text-[#a78bfa]">pip install nama_library</code> atau menjalankan script dengan <code className="font-mono text-xs bg-slate-100 dark:bg-slate-700/50 px-1 rounded text-[#7c3aed] dark:text-[#a78bfa]">python script.py</code>. Tembok keamanan cerdas kami akan secara otomatis menerjemahkan dan mengarahkan perintah tersebut ke dalam ekosistem <em>venv</em> proyek Anda (berubah menjadi <code className="font-mono text-xs bg-slate-100 dark:bg-slate-700/50 px-1 rounded">venv/bin/pip</code>). Sistem ini menjamin 100% keamanan server global.
                                </p>
                            </div>
                            <div>
                                <h4 className="font-semibold text-[#333] dark:text-white text-sm mb-1">3. Pre-compiled Data Science Library</h4>
                                <p className="text-sm text-[#666] dark:text-white/60 leading-relaxed">
                                    Menginstal library komputasi berat (seperti Numpy, Pandas, atau OpenCV) dari <em>source code</em> di lingkungan Linux bisa memakan waktu hingga puluhan menit dan memonopoli resource CPU. Untuk mengatasi ini, Ryaze telah menginjeksi <em>pre-compiled binaries</em> bawaan sistem untuk library Data Science populer (<strong>Numpy, Pandas, dan Scikit-Learn</strong>). Hal ini membuat proses instalasi environment Anda menjadi instan dan menjaga server tetap ringan.
                                </p>
                            </div>
                            <div className="pt-4 border-t border-slate-100 dark:border-[#1a1a2e]">
                                <h4 className="font-semibold text-[#333] dark:text-white text-sm mb-2">4. Aturan Binding PORT Dinamis (Kritis & Wajib)</h4>
                                <p className="text-sm text-[#666] dark:text-white/60 mb-3 leading-relaxed">
                                    Arsitektur jaringan Ryaze menggunakan sistem <em>Dynamic Port Allocation</em> yang dipadukan dengan Nginx Reverse Proxy. Oleh karena itu, aplikasi Python Anda <strong>TIDAK DIIZINKAN</strong> untuk mengikat port secara statis/hardcode (misalnya: <code>port 5000</code> atau <code>8000</code>). Aplikasi Anda <strong>wajib</strong> membaca nomor port yang dibagikan oleh sistem melalui variabel lingkungan <code className="font-mono text-xs bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-500/40 px-1 rounded">PORT</code>. Jika hal ini tidak dipatuhi, aplikasi Anda tidak akan bisa diakses dari luar (mengalami 502 Bad Gateway).
                                </p>
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div className="bg-slate-900 text-slate-300 dark:text-slate-400 font-mono text-xs p-4 rounded-lg shadow-sm">
                                        <div className="text-slate-500 dark:text-slate-400 mb-2 border-b border-slate-700 pb-1"># Contoh implementasi di Flask</div>
                                        <div className="leading-relaxed mt-2">
                                            <span className="text-fuchsia-400 dark:text-fuchsia-300">import</span> os<br />
                                            <span className="text-fuchsia-400 dark:text-fuchsia-300">from</span> flask <span className="text-fuchsia-400 dark:text-fuchsia-300">import</span> Flask<br /><br />
                                            app = Flask(__name__)<br /><br />
                                            <span className="text-fuchsia-400 dark:text-fuchsia-300">if</span> __name__ == <span className="text-emerald-400 dark:text-emerald-300">'__main__'</span>:<br />
                                            &nbsp;&nbsp;&nbsp;&nbsp;<span className="text-sky-400 dark:text-sky-300">port</span> = <span className="text-yellow-200">int</span>(os.environ.get(<span className="text-emerald-400 dark:text-emerald-300">'PORT'</span>, <span className="text-orange-300">8080</span>))<br />
                                            &nbsp;&nbsp;&nbsp;&nbsp;app.run(host=<span className="text-emerald-400 dark:text-emerald-300">'0.0.0.0'</span>, port=port)
                                        </div>
                                    </div>
                                    <div className="bg-slate-900 text-slate-300 dark:text-slate-400 font-mono text-xs p-4 rounded-lg shadow-sm">
                                        <div className="text-slate-500 dark:text-slate-400 mb-2 border-b border-slate-700 pb-1"># Contoh implementasi di FastAPI / Uvicorn</div>
                                        <div className="leading-relaxed mt-2">
                                            <span className="text-fuchsia-400 dark:text-fuchsia-300">import</span> os<br />
                                            <span className="text-fuchsia-400 dark:text-fuchsia-300">import</span> uvicorn<br />
                                            <span className="text-fuchsia-400 dark:text-fuchsia-300">from</span> fastapi <span className="text-fuchsia-400 dark:text-fuchsia-300">import</span> FastAPI<br /><br />
                                            app = FastAPI()<br /><br />
                                            <span className="text-fuchsia-400 dark:text-fuchsia-300">if</span> __name__ == <span className="text-emerald-400 dark:text-emerald-300">'__main__'</span>:<br />
                                            &nbsp;&nbsp;&nbsp;&nbsp;<span className="text-sky-400 dark:text-sky-300">port</span> = <span className="text-yellow-200">int</span>(os.environ.get(<span className="text-emerald-400 dark:text-emerald-300">'PORT'</span>, <span className="text-orange-300">8080</span>))<br />
                                            &nbsp;&nbsp;&nbsp;&nbsp;uvicorn.run(app, host=<span className="text-emerald-400 dark:text-emerald-300">'0.0.0.0'</span>, port=port)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <hr className="border-slate-100 dark:border-[#1a1a2e] my-6" />

                    <div className="text-center">
                        <p className="text-[#999] dark:text-white/40 text-xs">Masih mengalami kendala? Silakan hubungi tim dukungan teknis kami.</p>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
