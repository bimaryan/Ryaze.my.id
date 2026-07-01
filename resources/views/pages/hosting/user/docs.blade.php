@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Panduan & Dokumentasi" 
            subtitle="Pelajari cara menggunakan seluruh fitur hosting Ryaze dengan optimal." 
            icon="fa-book" 
            iconColor="indigo">
        </x-ui.page-header>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8 space-y-8">

            <!-- Intro -->
            <div class="text-slate-600 leading-relaxed text-sm">
                Selamat datang di layanan Hosting Ryaze! Panduan ini dibuat untuk membantu Anda memahami dan menggunakan
                semua fitur yang tersedia dengan mudah dan aman.
            </div>

            <hr class="border-slate-100">

            <!-- Section 1 -->
            <div>
                <h2 class="text-lg font-bold text-slate-800 mb-3 flex items-center gap-2">
                    <span
                        class="bg-indigo-100 text-indigo-600 w-6 h-6 rounded-full flex items-center justify-center text-xs">1</span>
                    Menambahkan & Deploy Project Baru
                </h2>
                <p class="text-sm text-slate-600 mb-3">Anda dapat mendeploy aplikasi dengan dua cara: <strong>Git Repository</strong> atau <strong>Gunakan Template</strong> siap pakai.</p>
                <div class="bg-slate-50 rounded-lg p-4 border border-slate-100">
                    <h3 class="font-semibold text-slate-700 text-sm mb-2">Cara Deploy Project:</h3>
                    <ol class="list-decimal list-inside text-sm text-slate-600 space-y-1.5 ml-1">
                        <li>Masuk ke halaman <strong>Dashboard Hosting</strong> lalu klik tombol <strong>Deploy Proyek Baru</strong>.</li>
                        <li>Pilih metode deploy: 
                            <ul class="list-disc list-inside ml-5 mt-1 space-y-1">
                                <li><strong>Git Repository:</strong> Untuk clone dari repo Git Anda sendiri (masukkan URL dan Branch).</li>
                                <li><strong>Gunakan Template:</strong> Mulai instan tanpa Git. Pilih template seperti Laravel, Next.js, React, Node.js, atau HTML.</li>
                            </ul>
                        </li>
                        <li>Masukkan <strong>Nama Project</strong> (digunakan sebagai subdomain, contoh: <code class="bg-white px-1 py-0.5 rounded border border-slate-200">nama-project.ryaze.my.id</code>).</li>
                        <li>Klik <strong>Deploy Sekarang</strong>.</li>
                        <li>Sistem kami akan menyiapkan server untuk Anda secara instan.</li>
                    </ol>
                </div>
            </div>

            <!-- Section 2 -->
            <div>
                <h2 class="text-lg font-bold text-slate-800 mb-3 flex items-center gap-2">
                    <span
                        class="bg-indigo-100 text-indigo-600 w-6 h-6 rounded-full flex items-center justify-center text-xs">2</span>
                    Mengelola File & Folder (File Manager)
                </h2>
                <p class="text-sm text-slate-600 mb-3">Anda bisa mengelola file aplikasi langsung dari Dashboard tanpa
                    memerlukan aplikasi FTP tambahan.</p>
                <ul class="list-disc list-inside text-sm text-slate-600 space-y-1.5 ml-1 mb-4">
                    <li><strong>Lihat & Edit File:</strong> Buka file kode teks langsung di browser dan edit isinya.</li>
                    <li><strong>Buat File/Folder Baru:</strong> Tambahkan direktori atau file baru kapan pun.</li>
                    <li><strong>Upload File:</strong> Upload file dari komputer Anda (Maksimal 10MB per file).</li>
                    <li><strong>Hapus & Download:</strong> Unduh file penting atau hapus file yang tidak dibutuhkan.</li>
                </ul>
                <div
                    class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm text-amber-800 flex items-start gap-3">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5 text-amber-500"></i>
                    <p><strong>Perhatian:</strong> Demi keamanan server, kami memblokir upload untuk tipe file executable
                        tertentu (seperti .php, .sh, .exe) dari File Manager. Jika Anda membutuhkan file tersebut, silakan
                        <em>push</em> via Git.</p>
                </div>
            </div>

            <!-- Section 3 -->
            <div>
                <h2 class="text-lg font-bold text-slate-800 mb-3 flex items-center gap-2">
                    <span
                        class="bg-indigo-100 text-indigo-600 w-6 h-6 rounded-full flex items-center justify-center text-xs">3</span>
                    Pengaturan Lingkungan (Environment Variables / .env)
                </h2>
                <p class="text-sm text-slate-600 mb-3">Bagi aplikasi yang memerlukan pengaturan Environment (seperti Laravel
                    atau Node.js):</p>
                <ol class="list-decimal list-inside text-sm text-slate-600 space-y-1.5 ml-1">
                    <li>Buka halaman detail Project Anda.</li>
                    <li>Cari menu tab <strong>Pengaturan .env</strong>.</li>
                    <li>Di sana Anda dapat langsung melihat dan memperbarui variabel environment (seperti koneksi database
                        atau API Keys).</li>
                    <li>Klik <strong>Simpan</strong> dan pengaturan akan otomatis diperbarui.</li>
                </ol>
            </div>

            <!-- Section 4 -->
            <div>
                <h2 class="text-lg font-bold text-slate-800 mb-3 flex items-center gap-2">
                    <span
                        class="bg-indigo-100 text-indigo-600 w-6 h-6 rounded-full flex items-center justify-center text-xs">4</span>
                    Pengaturan Keamanan & Performa (Settings)
                </h2>
                <p class="text-sm text-slate-600 mb-3">Pada menu <strong>Settings</strong> di halaman project, Anda dapat
                    mengaktifkan fitur tambahan dengan satu klik:</p>
                <ul class="list-none text-sm text-slate-600 space-y-2">
                    <li class="flex gap-2"><i class="fa-solid fa-tools text-slate-400 mt-1"></i> <span><strong>Maintenance
                                Mode:</strong> Aktifkan saat Anda sedang melakukan perbaikan besar, pengunjung akan melihat
                            halaman "Sedang dalam Perbaikan".</span></li>
                    <li class="flex gap-2"><i class="fa-solid fa-lock text-slate-400 mt-1"></i> <span><strong>Force
                                HTTPS:</strong> Memaksa seluruh trafik menggunakan koneksi HTTPS yang aman.</span></li>
                    <li class="flex gap-2"><i class="fa-solid fa-shield-halved text-slate-400 mt-1"></i> <span><strong>Under
                                Attack Mode (Rate Limit):</strong> Aktifkan ini jika website Anda sedang mendapat serangan
                            spam. Fitur ini akan membatasi kecepatan akses pengunjung.</span></li>
                </ul>
            </div>

            <!-- Section 5 -->
            <div>
                <h2 class="text-lg font-bold text-slate-800 mb-3 flex items-center gap-2">
                    <span
                        class="bg-indigo-100 text-indigo-600 w-6 h-6 rounded-full flex items-center justify-center text-xs">5</span>
                    Menggunakan Web Terminal
                </h2>
                <p class="text-sm text-slate-600 mb-3">Kami menyediakan fitur <strong>Web Terminal</strong> bagi pengguna
                    tingkat lanjut untuk menjalankan perintah langsung di direktori project.</p>
                <div class="bg-slate-50 border border-slate-100 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-slate-700 text-sm mb-2">Perintah yang Diizinkan:</h3>
                    <div class="flex flex-wrap gap-2 text-xs">
                        <span class="px-2 py-1 bg-white border border-slate-200 rounded text-slate-600 font-mono">ls</span>
                        <span class="px-2 py-1 bg-white border border-slate-200 rounded text-slate-600 font-mono">cat</span>
                        <span
                            class="px-2 py-1 bg-white border border-slate-200 rounded text-slate-600 font-mono">grep</span>
                        <span
                            class="px-2 py-1 bg-white border border-slate-200 rounded text-slate-600 font-mono">mkdir</span>
                        <span class="px-2 py-1 bg-white border border-slate-200 rounded text-slate-600 font-mono">rm</span>
                        <span class="px-2 py-1 bg-white border border-slate-200 rounded text-slate-600 font-mono">npm</span>
                        <span
                            class="px-2 py-1 bg-white border border-slate-200 rounded text-slate-600 font-mono">node</span>
                        <span class="px-2 py-1 bg-white border border-slate-200 rounded text-slate-600 font-mono">php
                            artisan ...</span>
                        <span
                            class="px-2 py-1 bg-white border border-slate-200 rounded text-slate-600 font-mono">composer</span>
                        <span
                            class="px-2 py-1 bg-white border border-slate-200 rounded text-slate-600 font-mono">python3</span>
                    </div>
                </div>
                <div class="bg-rose-50 border border-rose-200 rounded-lg p-3 text-sm text-rose-800 flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 text-rose-500"></i>
                    <p><strong>Kebijakan Keamanan:</strong> Perintah yang bersifat merusak, mengakses direktori root,
                        mengeksekusi script tak terpercaya, atau membuka koneksi <em>reverse shell</em> akan secara otomatis
                        diblokir oleh sistem.</p>
                </div>
            </div>

            <!-- Section 6 -->
            <div>
                <h2 class="text-lg font-bold text-slate-800 mb-3 flex items-center gap-2">
                    <span
                        class="bg-indigo-100 text-indigo-600 w-6 h-6 rounded-full flex items-center justify-center text-xs">6</span>
                    Manajemen Kapasitas Penyimpanan (Storage)
                </h2>
                <p class="text-sm text-slate-600 mb-3">Kapasitas penyimpanan berlaku secara <strong>Global (per Akun)</strong>, bukan per project. Pantau total penggunaan di menu <strong>Penyimpanan / Storage</strong> di sidebar kiri.</p>
                <div class="bg-emerald-50 border border-emerald-100 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-emerald-800 text-sm mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-lightbulb text-emerald-500"></i> Tips Hemat Storage
                    </h3>
                    <ul class="list-disc list-inside text-sm text-emerald-700 space-y-1.5 ml-1">
                        <li>Jangan masukkan folder <code
                                class="bg-white px-1 py-0.5 rounded border border-emerald-200">node_modules</code> atau
                            <code class="bg-white px-1 py-0.5 rounded border border-emerald-200">vendor</code> ke Git.
                            Biarkan sistem menginstalnya otomatis saat deploy.</li>
                        <li>Bersihkan file log (misal: di <code
                                class="bg-white px-1 py-0.5 rounded border border-emerald-200">storage/logs/</code> pada
                            Laravel) secara berkala.</li>
                        <li>Hapus file backup lama yang tidak dipakai.</li>
                    </ul>
                </div>
                <p class="text-sm text-slate-500">Jika storage akun Anda penuh, deployment atau pembuatan project baru akan gagal. Anda bisa melakukan <strong>Upgrade Kapasitas Storage</strong> dan membayar secara otomatis menggunakan metode pembayaran <strong>Pakasir</strong> pada halaman Penyimpanan / Storage.</p>
            </div>

            <!-- Section 7 -->
            <div>
                <h2 class="text-lg font-bold text-slate-800 mb-3 flex items-center gap-2">
                    <span
                        class="bg-indigo-100 text-indigo-600 w-6 h-6 rounded-full flex items-center justify-center text-xs">7</span>
                    Melakukan Redeploy Manual
                </h2>
                <p class="text-sm text-slate-600">Jika Anda melakukan perubahan (push) baru ke repositori Git Anda dan
                    sistem belum ter-deploy secara otomatis:</p>
                <ol class="list-decimal list-inside text-sm text-slate-600 mt-2 space-y-1.5 ml-1">
                    <li>Buka halaman detail project.</li>
                    <li>Cari dan klik tombol <strong>Redeploy</strong>.</li>
                    <li>Sistem akan menarik kode terbaru dari repositori dan me-restart website Anda.</li>
                </ol>
            </div>

            <hr class="border-slate-100">

            <!-- Section 8 -->
            <div>
                <h2 class="text-lg font-bold text-slate-800 mb-3 flex items-center gap-2">
                    <span class="bg-indigo-100 text-indigo-600 w-6 h-6 rounded-full flex items-center justify-center text-xs">8</span>
                    Development Server (Live Preview)
                </h2>
                <p class="text-sm text-slate-600 mb-3">Anda bisa menjalankan <em>development server</em> (seperti Vite, Artisan Serve, atau Next.js Dev) langsung di cloud dan mengaksesnya secara real-time dari browser Anda.</p>
                <ol class="list-decimal list-inside text-sm text-slate-600 mt-2 space-y-1.5 ml-1 mb-3">
                    <li>Buka halaman detail project.</li>
                    <li>Di card bagian atas, temukan tombol <strong>Dev Server</strong> (ikon Play/Stop).</li>
                    <li>Klik <strong>Nyalakan Server</strong> dan sistem akan mengeksekusi script dev server Anda di background.</li>
                    <li>Klik link URL khusus yang muncul (contoh: <code class="text-indigo-600 bg-indigo-50 px-1 py-0.5 rounded border border-indigo-100">dev-1234.ryaze.my.id</code>) untuk melihat live preview web Anda tanpa perlu rebuild!</li>
                    <li>Jangan lupa klik <strong>Matikan Server</strong> jika sudah selesai ngoding.</li>
                </ol>
                <div class="bg-sky-50 border border-sky-200 rounded-lg p-3 text-sm text-sky-800 flex items-start gap-3">
                    <i class="fa-solid fa-circle-info mt-0.5 text-sky-500"></i>
                    <p><strong>Note:</strong> Fitur Dev Server sangat berguna dan menghemat waktu Anda saat ngoding langsung via Web Terminal atau File Manager. Anda tidak perlu rebuild web berkali-kali untuk melihat perubahannya.</p>
                </div>
            </div>

            <hr class="border-slate-100">

            <div class="text-center py-4">
                <p class="text-sm text-slate-500">Masih kebingungan? Hubungi Admin untuk bantuan teknis lebih lanjut.</p>
            </div>

        </div>
    </x-ui.page-layout>
@endsection
