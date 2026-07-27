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
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('user_hosting.tunnels.client', $tunnel->id) }}" target="_blank" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-100 hover:text-emerald-700 transition-colors tooltip" data-tip="Download Client Script">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                            <button type="button" onclick="showInstruction('{{ $tunnel->subdomain }}')" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 hover:text-blue-700 transition-colors tooltip" data-tip="Cara Penggunaan">
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
                            <h4 class="font-semibold text-slate-800 mb-1">Download Client Script</h4>
                            <p class="text-sm text-slate-600">Klik ikon download (warna hijau) pada tabel untuk mengunduh file <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-800 font-mono text-xs">ryaze-tunnel-<span id="insSubdomain"></span>.php</code></p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 font-bold flex items-center justify-center shrink-0">2</div>
                        <div class="w-full">
                            <h4 class="font-semibold text-slate-800 mb-1">Jalankan di Terminal Local</h4>
                            <p class="text-sm text-slate-600 mb-3">Buka terminal / CMD di folder tempat Anda menyimpan file tersebut, lalu ketik perintah ini:</p>
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
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Ryaze Local Tunnel adalah layanan yang memungkinkan Anda mempublikasikan aplikasi atau website yang berjalan di localhost komputer Anda ke internet tanpa harus mengatur port forwarding pada router atau memiliki IP Publik. Fungsinya mirip dengan Ngrok, namun diintegrasikan langsung dengan akun Ryaze Anda.
                        </p>
                    </div>

                    <!-- Section 2 -->
                    <div>
                        <h4 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-2 mb-4">2. Komponen & Arsitektur</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 hover:border-indigo-100 transition-colors">
                                <div class="font-semibold text-slate-800 mb-1 flex items-center"><i class="fa-brands fa-cloudflare text-[#f38020] mr-2 text-lg"></i> Cloudflare DNS</div>
                                <p class="text-sm text-slate-600">Mengatur routing domain <code class="text-xs bg-slate-200 px-1 py-0.5 rounded text-slate-700">*.ryaze.my.id</code> ke server Ryaze dan menyediakan enkripsi SSL/HTTPS secara otomatis.</p>
                            </div>
                            <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 hover:border-indigo-100 transition-colors">
                                <div class="font-semibold text-slate-800 mb-1 flex items-center"><i class="fa-solid fa-server text-indigo-500 mr-2 text-lg"></i> OpenResty Proxy</div>
                                <p class="text-sm text-slate-600">Menerima trafik publik dari Cloudflare dan meneruskannya (forward) ke sistem Relay API internal Ryaze.</p>
                            </div>
                            <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 hover:border-indigo-100 transition-colors">
                                <div class="font-semibold text-slate-800 mb-1 flex items-center"><i class="fa-brands fa-laravel text-[#ff2d20] mr-2 text-lg"></i> Laravel Reverb (WebSockets)</div>
                                <p class="text-sm text-slate-600">Menjaga koneksi real-time antara server Ryaze dengan komputer Anda. Begitu ada request, server akan langsung memberitahu (broadcast) komputer Anda.</p>
                            </div>
                            <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 hover:border-indigo-100 transition-colors">
                                <div class="font-semibold text-slate-800 mb-1 flex items-center"><i class="fa-brands fa-php text-[#777bb3] mr-2 text-lg"></i> PHP CLI Client</div>
                                <p class="text-sm text-slate-600">Script yang Anda jalankan di terminal. Bertugas mendengarkan event WebSocket dan meneruskan HTTP request ke localhost Anda.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3 -->
                    <div>
                        <h4 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-2 mb-4">3. Alur Kerja (Bagaimana Data Mengalir)</h4>
                        <div class="relative pl-6 space-y-6 before:content-[''] before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-[2px] before:bg-indigo-100">
                            
                            <div class="relative">
                                <div class="absolute -left-[30px] top-0 w-6 h-6 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xs font-bold border-4 border-white shadow-sm">1</div>
                                <div class="font-semibold text-slate-800 mb-1">Pengunjung Mengakses URL</div>
                                <p class="text-sm text-slate-600">Seseorang membuka <code class="text-xs bg-slate-100 px-1 py-0.5 rounded text-slate-700">https://subdomain.ryaze.my.id</code> di browser mereka.</p>
                            </div>

                            <div class="relative">
                                <div class="absolute -left-[30px] top-0 w-6 h-6 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xs font-bold border-4 border-white shadow-sm">2</div>
                                <div class="font-semibold text-slate-800 mb-1">Server Menerima Request (Relay API)</div>
                                <p class="text-sm text-slate-600">Request masuk ke server Ryaze. Server kemudian membekukan (long-polling) HTTP request tersebut maksimal hingga 30 detik sambil menunggu respon dari komputer Anda.</p>
                            </div>

                            <div class="relative">
                                <div class="absolute -left-[30px] top-0 w-6 h-6 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xs font-bold border-4 border-white shadow-sm">3</div>
                                <div class="font-semibold text-slate-800 mb-1">Notifikasi via WebSocket</div>
                                <p class="text-sm text-slate-600">Secara instan, server mengirimkan detail request (Method, URL, Headers, Body) ke terminal Anda menggunakan koneksi WebSocket yang sudah terhubung.</p>
                            </div>

                            <div class="relative">
                                <div class="absolute -left-[30px] top-0 w-6 h-6 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xs font-bold border-4 border-white shadow-sm">4</div>
                                <div class="font-semibold text-slate-800 mb-1">Client Mengeksekusi Request ke Localhost</div>
                                <p class="text-sm text-slate-600">Script <code class="text-xs bg-slate-100 px-1 py-0.5 rounded text-slate-700">ryaze-tunnel.php</code> di terminal Anda menerima notifikasi tersebut, lalu melakukan HTTP cURL ke <code class="text-xs bg-slate-100 px-1 py-0.5 rounded text-slate-700">http://127.0.0.1:{Port}</code> sesuai dengan request aslinya.</p>
                            </div>

                            <div class="relative">
                                <div class="absolute -left-[30px] top-0 w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold border-4 border-white shadow-sm"><i class="fa-solid fa-check"></i></div>
                                <div class="font-semibold text-slate-800 mb-1">Upload Response & Tampilkan ke Pengunjung</div>
                                <p class="text-sm text-slate-600">Localhost Anda membalas request tersebut. Script kemudian meng-upload hasil balasan (HTML/JSON, Headers, Status Code) kembali ke server Ryaze, dan server menampilkannya ke browser pengunjung.</p>
                            </div>

                        </div>
                    </div>

                    <!-- Section 4 -->
                    <div class="bg-blue-50/50 rounded-xl p-5 border border-blue-100">
                        <h4 class="text-sm font-bold text-blue-800 mb-2 flex items-center"><i class="fa-solid fa-circle-info mr-2"></i> Limitasi & Catatan Penting</h4>
                        <ul class="list-disc list-outside pl-5 text-sm text-blue-700/80 space-y-1.5 marker:text-blue-400">
                            <li><strong>Timeout 30 Detik:</strong> Jika aplikasi localhost Anda membutuhkan waktu lebih dari 30 detik untuk merespon, server Ryaze akan memutuskan koneksi (Timeout 504) karena limitasi long-polling.</li>
                            <li><strong>Offline Detection:</strong> Script client mengirimkan "heartbeat" setiap ~60 detik. Jika Anda menutup terminal, dalam waktu 90 detik server akan menandai tunnel Anda sebagai "Offline" (503 Service Unavailable).</li>
                            <li><strong>Prasyarat:</strong> Pastikan Anda telah menginstall ekstensi <code class="bg-blue-100/50 px-1 rounded">php-curl</code> dan <code class="bg-blue-100/50 px-1 rounded">php-openssl</code> (WebSocket) di komputer Anda agar script berjalan lancar.</li>
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
            document.getElementById('insSubdomain').innerText = subdomain;
            document.getElementById('insSubdomainCode').innerText = subdomain;
            document.getElementById('insLink').innerText = 'https://' + subdomain + '.ryaze.my.id';
            document.getElementById('insLink').href = 'https://' + subdomain + '.ryaze.my.id';
            document.getElementById('instructionModal').classList.remove('hidden');
        }
    </script>
    </x-ui.page-layout>
@endsection
