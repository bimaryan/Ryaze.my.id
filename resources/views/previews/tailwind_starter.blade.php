<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tailwind CSS Starter - Tailwind CSS Starter</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { brand: '#4f46e5' }
                }
            }
        }
    </script>
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');</style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased flex flex-col min-h-screen">
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between h-16 items-center">
            <div class="font-bold text-xl text-brand flex items-center gap-2">
                <i class="fa-solid fa-code"></i> Tailwind CSS Starter
            </div>
            <div class="flex gap-4">
                <a href="#" class="text-sm font-medium text-slate-600 hover:text-brand transition">Documentation</a>
                <a href="#" class="text-sm font-medium text-slate-600 hover:text-brand transition">GitHub</a>
            </div>
        </div>
    </nav>
    <main class="flex-grow flex items-center justify-center p-6">
        <div class="max-w-3xl w-full bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">
            <div class="bg-gradient-to-br from-brand to-indigo-700 p-12 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-white/10 opacity-30" style="background-image: radial-gradient(white 1px, transparent 1px); background-size: 20px 20px;"></div>
                <i class="fa-brands fa-css3-alt text-6xl text-white mb-6 relative z-10 drop-shadow-md"></i>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-4 relative z-10 tracking-tight">Tailwind CSS Starter</h1>
                <p class="text-indigo-100 font-medium text-lg relative z-10 max-w-xl mx-auto">Proyek <strong>Tailwind CSS Starter</strong> Anda sudah siap digunakan! Tidak perlu repot dengan instalasi NPM atau build tools.</p>
            </div>
            <div class="p-8 sm:p-12">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="flex gap-4">
                        <div class="bg-indigo-50 w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 text-brand">
                            <i class="fa-solid fa-bolt text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 mb-2">Super Cepat</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">Menggunakan Tailwind CSS dari CDN. Langsung render dengan sempurna di semua perangkat.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="bg-indigo-50 w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 text-brand">
                            <i class="fa-solid fa-paintbrush text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 mb-2">Siap Dikustomisasi</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">Buka File Manager Anda, edit <code>index.html</code>, dan mulai tambahkan utility class Tailwind favorit Anda.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="bg-indigo-50 w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 text-brand">
                            <i class="fa-solid fa-icons text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 mb-2">FontAwesome Included</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">Lebih dari 2.000+ ikon gratis siap pakai. Cukup gunakan tag <code>&lt;i class="fa-solid fa-user"&gt;</code>.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="bg-indigo-50 w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 text-brand">
                            <i class="fa-solid fa-mobile-screen text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 mb-2">100% Responsif</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">Gunakan prefix seperti <code>md:</code>, <code>lg:</code>, dan <code>hover:</code> untuk membuat tampilan menakjubkan.</p>
                        </div>
                    </div>
                </div>
                
                    <div class="mt-12 text-center flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="https://tailwindcss.com/docs" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold py-3 px-8 rounded-xl transition-all hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-900/20">
                            Baca Dokumentasi <i class="fa-solid fa-arrow-right"></i>
                        </a>
                        <button onclick="document.getElementById('demoModal').classList.remove('hidden')" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-xl transition-all hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-600/20">
                            Buka Demo Modal <i class="fa-solid fa-up-right-from-square"></i>
                        </button>
                    </div>
                </div>
            </div>
        </main>
        <footer class="py-6 text-center text-sm text-slate-500">
            &copy; 2026 Tailwind CSS Starter. Powered by <a href="https://ryaze.my.id" class="font-semibold text-brand hover:underline">Ryaze Hosting</a>.
        </footer>

        <!-- Demo Modal (Sama persis dengan Portal) -->
        <div id="demoModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="document.getElementById('demoModal').classList.add('hidden')"></div>
            
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden relative z-10 transform transition-all flex flex-col max-h-[90vh]">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-cube text-indigo-600"></i> Komponen Modal
                    </h3>
                    <button type="button" onclick="document.getElementById('demoModal').classList.add('hidden')" class="text-slate-400 hover:text-red-500 hover:bg-red-50 w-8 h-8 rounded-lg flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                
                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto">
                    <div class="flex justify-center mb-6">
                        <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-3xl shadow-inner">
                            <i class="fa-solid fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h4 class="text-xl font-bold text-slate-900 mb-2">Desain Premium!</h4>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">
                            Modal ini dirancang agar terlihat sama persis dengan desain yang ada di halaman Portal Ryaze Hosting. Dilengkapi dengan backdrop blur, transisi lembut, dan tombol-tombol modern.
                        </p>
                    </div>
                    
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 mb-2">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-code text-slate-400"></i>
                            <div class="text-left text-sm">
                                <p class="font-semibold text-slate-800">Siap Pakai</p>
                                <p class="text-slate-500 text-xs">Salin kode ini untuk proyek Anda</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end gap-3 shrink-0">
                    <button type="button" onclick="document.getElementById('demoModal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-200 transition-colors">
                        Tutup
                    </button>
                    <button type="button" onclick="document.getElementById('demoModal').classList.add('hidden')" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-sm shadow-indigo-200 hover:-translate-y-0.5 transition-all">
                        Mengerti
                    </button>
                </div>
            </div>
        </div>
    </body>
    </html>