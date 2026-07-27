@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Local Tunnels (Beta)"
            subtitle="Publikasikan project localhost Anda ke internet dengan mudah."
            icon="fa-network-wired"
            iconColor="purple">
            <x-slot:actions>
                <button type="button" onclick="document.getElementById('documentationModal').classList.remove('hidden')"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 text-sm font-medium rounded-lg transition-all shadow-sm">
                    <i class="fa-solid fa-book-open text-indigo-500"></i>
                    Dokumentasi
                </button>
                <button type="button" onclick="document.getElementById('createTunnelModal').classList.remove('hidden')"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white text-sm font-medium rounded-lg transition-all shadow-sm shadow-purple-500/20 group hover:shadow-md hover:shadow-purple-500/30">
                    <i class="fa-solid fa-plus text-purple-200 group-hover:text-white transition-colors"></i>
                    Buat Tunnel Baru
                </button>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="space-y-6">


            <x-ui.card>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-y border-slate-200 text-slate-500 text-xs uppercase tracking-wider font-semibold">
                                <th class="px-6 py-4">Nama & Subdomain</th>
                                <th class="px-6 py-4">Target Port</th>
                                <th class="px-6 py-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-sm">
                            @forelse($tunnels as $tunnel)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-800 mb-0.5">{{ $tunnel->name }}</div>
                                        <div class="text-xs text-slate-500 font-mono bg-slate-100 px-2 py-0.5 rounded inline-block border border-slate-200">
                                            https://{{ $tunnel->subdomain }}.ryaze.my.id
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            <i class="fa-solid fa-plug text-indigo-400"></i>
                                            localhost:{{ $tunnel->target_port }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2 justify-end">
                                            <a href="{{ route('user_hosting.tunnels.client', $tunnel->id) }}" target="_blank" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-100 hover:text-emerald-700 transition-colors tooltip" data-tip="Download Client (.php)">
                                                <i class="fa-brands fa-php"></i>
                                            </a>
                                            <a href="{{ route('user_hosting.tunnels.client_bat', $tunnel->id) }}" target="_blank" class="w-8 h-8 rounded-lg bg-cyan-50 text-cyan-600 flex items-center justify-center hover:bg-cyan-100 hover:text-cyan-700 transition-colors tooltip" data-tip="Download Client (.bat)">
                                                <i class="fa-solid fa-file-code"></i>
                                            </a>
                                            <button type="button" onclick="showInstruction('{{ $tunnel->subdomain }}')" class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center hover:bg-indigo-100 hover:text-indigo-700 transition-colors tooltip" data-tip="Cara Penggunaan">
                                                <i class="fa-solid fa-book"></i>
                                            </button>
                                            <form action="{{ route('user_hosting.tunnels.destroy', $tunnel->id) }}" method="POST"
                                            onsubmit="event.preventDefault(); let f = this; Swal.fire({title: 'Hapus Tunnel?', text: 'Apakah Anda yakin ingin menghapus tunnel ini? Subdomain akan dilepas permanen.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: '<i class=\'fa-solid fa-trash-can mr-1\'></i> Ya, Hapus', cancelButtonText: 'Batal', customClass: {popup: 'rounded-2xl text-sm'}}).then(res => { if(res.isConfirmed) f.submit(); }); return false;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100 hover:text-red-700 transition-colors tooltip" data-tip="Hapus Tunnel">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                                            <i class="fa-solid fa-network-wired text-2xl text-slate-400"></i>
                                        </div>
                                        <p class="font-medium text-slate-600">Belum ada tunnel aktif</p>
                                        <p class="text-sm mt-1">Buat tunnel baru untuk mempublikasikan localhost Anda.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </div>

        <!-- Modal Create Tunnel -->
        <div id="createTunnelModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4 transition-opacity duration-300">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transition-transform duration-300 transform">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800">Buat Tunnel Baru</h3>
                    <button type="button" onclick="document.getElementById('createTunnelModal').classList.add('hidden')" class="text-slate-400 hover:text-rose-500 transition-colors p-2 rounded-lg hover:bg-rose-50">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <form action="{{ route('user_hosting.tunnels.store') }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Tunnel</label>
                            <input type="text" name="name" required placeholder="Misal: Proyek React Next.js"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Subdomain</label>
                            <div class="flex items-center">
                                <input type="text" name="subdomain" required placeholder="my-app" pattern="[a-z0-9-]+" title="Hanya huruf kecil, angka, dan strip"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-l-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                                <div class="px-4 py-2.5 bg-slate-100 border border-l-0 border-slate-200 rounded-r-xl text-sm text-slate-500 font-medium">
                                    .ryaze.my.id
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1.5"><i class="fa-solid fa-info-circle mr-1"></i> Hanya huruf kecil, angka, dan tanda hubung (-).</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Target Port Localhost</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-plug"></i>
                                </div>
                                <input type="number" name="target_port" required value="8000" min="1" max="65535"
                                       class="w-full pl-10 pr-4 bg-slate-50 border border-slate-200 rounded-xl py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            </div>
                        </div>
                    </div>
                    <div class="p-6 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('createTunnelModal').classList.add('hidden')"
                                class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-sm transition-all">
                            Simpan Tunnel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Instruksi -->
        <div id="instructionModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4 transition-opacity duration-300">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden transition-transform duration-300 transform">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800">Cara Menjalankan Tunnel</h3>
                    <button type="button" onclick="document.getElementById('instructionModal').classList.add('hidden')" class="text-slate-400 hover:text-rose-500 transition-colors p-2 rounded-lg hover:bg-rose-50">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                    
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 font-bold flex items-center justify-center shrink-0">1</div>
                        <div>
                            <h4 class="font-semibold text-slate-800 mb-1">Download Client</h4>
                            <p class="text-sm text-slate-600">Klik ikon <i class="fa-brands fa-php text-emerald-600"></i> atau <i class="fa-solid fa-file-code text-cyan-600"></i> pada tabel untuk mengunduh versi PHP atau BAT.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 font-bold flex items-center justify-center shrink-0">2</div>
                        <div class="w-full">
                            <h4 class="font-semibold text-slate-800 mb-1">Jalankan Tunnel</h4>
                            <p class="text-sm text-slate-600 mb-3">Jika Anda mengunduh file <strong>.bat</strong>, cukup <strong>klik 2x (Double Click)</strong> file tersebut.<br>Namun, jika Anda menggunakan versi <strong>.php</strong>, jalankan perintah ini di terminal:</p>
                            <div class="relative group">
                                <pre class="bg-slate-800 text-slate-200 p-4 rounded-xl text-sm font-mono overflow-x-auto border border-slate-700"><code>php ryaze-tunnel-<span id="insSubdomainCode"></span>.php</code></pre>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 font-bold flex items-center justify-center shrink-0"><i class="fa-solid fa-check"></i></div>
                        <div>
                            <h4 class="font-semibold text-slate-800 mb-1">Selesai! Akses URL Public Anda</h4>
                            <p class="text-sm text-slate-600">Selama terminal Anda terbuka dan script berjalan, siapapun bisa mengakses localhost Anda melalui link: <br>
                            <a href="#" id="insLink" target="_blank" class="text-indigo-600 hover:underline font-medium mt-1 inline-block"></a></p>
                        </div>
                    </div>

                </div>
                <div class="p-6 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('instructionModal').classList.add('hidden')"
                            class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Dokumentasi -->
        <div id="documentationModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4 transition-opacity duration-300">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden transition-transform duration-300 transform">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0">
                    <h3 class="text-lg font-bold text-slate-800"><i class="fa-solid fa-book-open text-indigo-500 mr-2"></i> Dokumentasi & Cara Kerja Tunnel</h3>
                    <button type="button" onclick="document.getElementById('documentationModal').classList.add('hidden')" class="text-slate-400 hover:text-rose-500 transition-colors p-2 rounded-lg hover:bg-rose-50">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto space-y-8 flex-1">
                    
                    <!-- Section 1 -->
                    <div>
                        <h4 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-2 mb-4">1. Apa itu Ryaze Local Tunnel?</h4>
                        <p class="text-sm text-slate-600 leading-relaxed mb-3">
                            Ryaze Local Tunnel adalah layanan eksklusif yang memungkinkan Anda mempublikasikan aplikasi atau website yang berjalan di localhost komputer Anda ke internet secara instan, tanpa perlu ribet mengatur port forwarding router atau menyewa IP Publik. Fitur premium ini kami hadirkan <strong>100% GRATIS</strong> khusus untuk mempermudah alur kerja (workflow) seluruh client dan pengguna setia Ryaze!
                        </p>
                        <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 flex items-start gap-3">
                            <i class="fa-solid fa-lightbulb text-indigo-500 mt-0.5"></i>
                            <div>
                                <h5 class="text-sm font-semibold text-indigo-900 mb-1">Lebih Praktis dari ngrok!</h5>
                                <p class="text-sm text-indigo-800/80 leading-relaxed">
                                    Secara fungsi, layanan ini sangat mirip dengan <strong>ngrok</strong>. Bedanya, versi <strong>.bat</strong> kami dirancang jauh lebih instan. Anda tidak perlu repot mengetik perintah di terminal atau setup auth token. Cukup klik 2x file <code>.bat</code> yang diunduh, dan localhost Anda langsung online dengan perlindungan SSL (HTTPS) dari Ryaze!
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2 -->
                    <div>
                        <h4 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-2 mb-4">2. Alur Kerja (Bagaimana Ini Bekerja?)</h4>
                        <div class="relative pl-6 space-y-6 before:content-[''] before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-[2px] before:bg-indigo-100">
                            
                            <div class="relative">
                                <div class="absolute -left-[30px] top-0 w-6 h-6 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xs font-bold border-4 border-white shadow-sm">1</div>
                                <div class="font-semibold text-slate-800 mb-1">Pengunjung Mengakses Link Anda</div>
                                <p class="text-sm text-slate-600">Seseorang membuka link publik Anda (contoh: <code class="text-xs bg-slate-100 px-1 py-0.5 rounded text-slate-700">https://subdomain.ryaze.my.id</code>) di browser mereka.</p>
                            </div>

                            <div class="relative">
                                <div class="absolute -left-[30px] top-0 w-6 h-6 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xs font-bold border-4 border-white shadow-sm">2</div>
                                <div class="font-semibold text-slate-800 mb-1">Server Ryaze Meneruskan Request</div>
                                <p class="text-sm text-slate-600">Sistem Ryaze secara instan dan aman akan meneruskan permintaan (request) tersebut langsung ke terminal/CMD komputer Anda yang sedang menjalankan script client.</p>
                            </div>

                            <div class="relative">
                                <div class="absolute -left-[30px] top-0 w-6 h-6 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xs font-bold border-4 border-white shadow-sm">3</div>
                                <div class="font-semibold text-slate-800 mb-1">Script Mengakses Localhost</div>
                                <p class="text-sm text-slate-600">Script di komputer Anda secara otomatis akan memproses permintaan tersebut ke aplikasi localhost Anda (misal ke port 8000).</p>
                            </div>

                            <div class="relative">
                                <div class="absolute -left-[30px] top-0 w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold border-4 border-white shadow-sm"><i class="fa-solid fa-check"></i></div>
                                <div class="font-semibold text-slate-800 mb-1">Tampil di Browser Pengunjung</div>
                                <p class="text-sm text-slate-600">Setelah aplikasi localhost Anda merespon, script mengirimkan hasilnya kembali ke jaringan Ryaze dan langsung ditampilkan di layar pengunjung dengan sangat cepat.</p>
                            </div>

                        </div>
                    </div>

                    <!-- Section 3 -->
                    <div class="bg-blue-50/50 rounded-xl p-5 border border-blue-100">
                        <h4 class="text-sm font-bold text-blue-800 mb-2 flex items-center"><i class="fa-solid fa-circle-info mr-2"></i> Cara Penggunaan</h4>
                        <div class="space-y-4 mt-4">
                            <div class="flex items-start">
                                <span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-blue-100 text-blue-600 font-bold text-sm mr-2 shrink-0">1</span>
                                <div>
                                    <h4 class="font-medium text-slate-800">Download Client</h4>
                                    <p class="text-sm text-slate-600 mt-1">Klik tombol <strong>Download (.php)</strong> <i class="fa-brands fa-php text-emerald-600 mx-1"></i> atau <strong>Download (.bat)</strong> <i class="fa-solid fa-file-code text-cyan-600 mx-1"></i> pada baris tunnel yang Anda buat.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-blue-100 text-blue-600 font-bold text-sm mr-2 shrink-0">2</span>
                                <div>
                                    <h4 class="font-medium text-slate-800">Jalankan Client</h4>
                                    <p class="text-sm text-slate-600 mt-1">Jika menggunakan versi <strong>.bat</strong>, Anda hanya perlu klik 2x pada file tersebut. Jika menggunakan <strong>.php</strong>, jalankan perintah ini di terminal komputer Anda:</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50/50 rounded-xl p-5 border border-blue-100">
                        <h4 class="text-sm font-bold text-blue-800 mb-2 flex items-center"><i class="fa-solid fa-circle-info mr-2"></i> Limitasi & Catatan Penting</h4>
                        <ul class="list-disc list-outside pl-5 text-sm text-blue-700/80 space-y-1.5 marker:text-blue-400">
                            <li><strong>Timeout 30 Detik:</strong> Pastikan aplikasi localhost Anda merespon dalam waktu kurang dari 30 detik. Jika lebih dari itu, pengunjung akan melihat halaman error karena batas tunggu waktu maksimal telah tercapai.</li>
                            <li><strong>Tetap Buka Terminal:</strong> Selama Anda ingin link publik Anda dapat diakses, pastikan terminal atau CMD yang menjalankan script tidak ditutup. Jika ditutup, link akan otomatis berstatus Offline.</li>
                            <li><strong>Prasyarat:</strong> Pastikan Anda telah menginstall ekstensi <code class="bg-blue-100/50 px-1 rounded">php-curl</code> dan <code class="bg-blue-100/50 px-1 rounded">php-openssl</code> di komputer Anda.</li>
                        </ul>
                    </div>
                </div>

                <div class="p-6 border-t border-slate-100 bg-slate-50 flex justify-end shrink-0">
                    <button type="button" onclick="document.getElementById('documentationModal').classList.add('hidden')"
                            class="px-6 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
                        Tutup Dokumentasi
                    </button>
                </div>
            </div>
        </div>
    <script>
        function showInstruction(subdomain) {
            let el = document.getElementById('insSubdomain');
            if(el) el.innerText = subdomain;
            document.getElementById('insSubdomainCode').innerText = subdomain;
            document.getElementById('insLink').innerText = 'https://' + subdomain + '.ryaze.my.id';
            document.getElementById('insLink').href = 'https://' + subdomain + '.ryaze.my.id';
            document.getElementById('instructionModal').classList.remove('hidden');
        }
    </script>
    </x-ui.page-layout>
@endsection
