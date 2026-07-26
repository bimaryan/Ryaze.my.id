@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Local Tunnels (Beta)"
            subtitle="Publikasikan project localhost Anda ke internet dengan mudah."
            icon="fa-network-wired"
            iconColor="purple">
            <x-slot:actions>
                <a href="{{ route('user_hosting.dashboard') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors border border-slate-200 shadow-sm group">
                    <i class="fa-solid fa-arrow-left text-slate-400 group-hover:text-slate-600 transition-colors"></i>
                    Kembali
                </a>
                <button type="button" onclick="document.getElementById('createTunnelModal').classList.remove('hidden')"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white text-sm font-medium rounded-lg transition-all shadow-sm shadow-purple-500/20 group hover:shadow-md hover:shadow-purple-500/30">
                    <i class="fa-solid fa-plus text-purple-200 group-hover:text-white transition-colors"></i>
                    Buat Tunnel Baru
                </button>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="space-y-6">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-check text-emerald-600"></i>
                    </div>
                    <div class="text-sm font-medium">{{ session('success') }}</div>
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-triangle-exclamation text-red-600"></i>
                    </div>
                    <ul class="text-sm font-medium list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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
                                            <form action="{{ route('user_hosting.tunnels.destroy', $tunnel->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus tunnel ini? Subdomain akan dilepas.')">
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
    </x-ui.page-layout>

    <!-- Modal Create Tunnel -->
    <div id="createTunnelModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4 opacity-100 transition-opacity">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden border border-slate-200">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-plus text-purple-500"></i> Buat Tunnel Baru
                </h3>
                <button type="button" onclick="document.getElementById('createTunnelModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form action="{{ route('user_hosting.tunnels.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Tunnel</label>
                        <input type="text" name="name" required placeholder="Misal: Proyek React Next.js"
                               class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Subdomain</label>
                        <div class="flex items-center">
                            <input type="text" name="subdomain" required placeholder="my-app" pattern="[a-z0-9-]+" title="Hanya huruf kecil, angka, dan strip"
                                   class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-l-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all">
                            <div class="px-4 py-2.5 bg-slate-100 border border-l-0 border-slate-200 rounded-r-xl text-sm text-slate-500 font-medium">
                                .ryaze.my.id
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1.5"><i class="fa-solid fa-info-circle mr-1"></i> Hanya huruf kecil, angka, dan tanda hubung (-).</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Target Port Localhost</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-plug"></i>
                            </div>
                            <input type="number" name="target_port" required value="8000" min="1" max="65535"
                                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all">
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" onclick="document.getElementById('createTunnelModal').classList.add('hidden')"
                            class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-medium rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white text-sm font-medium rounded-xl transition-all shadow-sm shadow-purple-500/20">
                        Simpan Tunnel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Instruksi -->
    <div id="instructionModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4 opacity-100 transition-opacity">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden border border-slate-200">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-book text-blue-500"></i> Cara Menjalankan Tunnel
                </h3>
                <button type="button" onclick="document.getElementById('instructionModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-6 space-y-6">
                
                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-bold flex items-center justify-center shrink-0">1</div>
                    <div>
                        <h4 class="font-semibold text-slate-800 mb-1">Download Client Script</h4>
                        <p class="text-sm text-slate-600">Klik ikon download (warna hijau) pada tabel untuk mengunduh file <code>ryaze-tunnel-<span id="insSubdomain"></span>.php</code></p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-bold flex items-center justify-center shrink-0">2</div>
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
                        <a href="#" id="insLink" target="_blank" class="text-blue-600 hover:underline font-medium mt-1 inline-block"></a></p>
                    </div>
                </div>

            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="document.getElementById('instructionModal').classList.add('hidden')"
                        class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-medium rounded-xl transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function showInstruction(subdomain) {
            document.getElementById('insSubdomain').innerText = subdomain;
            document.getElementById('insSubdomainCode').innerText = subdomain;
            document.getElementById('insLink').innerText = 'https://' + subdomain + '.ryaze.my.id';
            document.getElementById('insLink').href = 'https://' + subdomain + '.ryaze.my.id';
            document.getElementById('instructionModal').classList.remove('hidden');
        }
    </script>
    @endpush
@endsection
