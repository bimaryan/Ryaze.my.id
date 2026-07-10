<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce - Modern E-Commerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: { brand: '#0f172a', accent: '#f43f5e' }
                }
            }
        }
    </script>
    <style>@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');</style>
</head>
<body class="bg-white font-sans antialiased text-slate-900 selection:bg-accent selection:text-white overflow-x-hidden">
    <!-- Topbar -->
    <div class="bg-brand text-white text-xs font-semibold text-center py-2.5 tracking-wide">
        Diskon Hingga 50% untuk Pengguna Baru! Gunakan kode: <span class="font-black text-accent ml-1 px-2 py-0.5 bg-white/10 rounded">NEW50</span>
    </div>

    <!-- Navbar -->
    <nav class="bg-white border-b border-slate-100 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-2">
                    <div class="w-10 h-10 bg-accent rounded-xl flex items-center justify-center text-white text-xl shadow-lg shadow-accent/30">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                    <span class="text-2xl font-black tracking-tighter uppercase">Ecommerce</span>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-10">
                    <a href="#" class="text-slate-900 font-bold border-b-2 border-brand py-1">Pria</a>
                    <a href="#" class="text-slate-500 hover:text-slate-900 font-medium py-1 transition">Wanita</a>
                    <a href="#" class="text-slate-500 hover:text-slate-900 font-medium py-1 transition">Anak</a>
                    <a href="#" class="text-accent font-bold py-1 flex items-center gap-1 transition">Sale <i class="fa-solid fa-tag text-xs"></i></a>
                </div>

                <!-- Icons -->
                <div class="flex items-center gap-6">
                    <button class="text-slate-500 hover:text-brand transition text-xl"><i class="fa-solid fa-magnifying-glass"></i></button>
                    <button class="text-slate-500 hover:text-brand transition text-xl hidden sm:block"><i class="fa-regular fa-user"></i></button>
                    <button class="text-slate-500 hover:text-brand transition text-xl relative">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span class="absolute -top-2 -right-2 bg-accent text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center border-2 border-white shadow-sm">3</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <!-- Hero Section -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-slate-100 rounded-[2rem] overflow-hidden relative shadow-xl">
                <div class="absolute inset-0 bg-gradient-to-r from-slate-900/90 via-slate-900/50 to-transparent z-10"></div>
                <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Hero Banner" class="w-full h-[500px] object-cover object-center absolute inset-0">
                
                <div class="relative z-20 h-[500px] flex items-center px-8 md:px-16 w-full max-w-2xl">
                    <div>
                        <span class="bg-accent text-white text-xs font-bold uppercase px-4 py-1.5 rounded-full mb-6 inline-block tracking-wider shadow-lg shadow-accent/40">Koleksi Musim Panas 2026</span>
                        <h1 class="text-5xl sm:text-6xl md:text-7xl font-black text-white leading-[1.1] mb-6 tracking-tight">Tampil Gaya<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400">Sepanjang Hari</span></h1>
                        <p class="text-slate-300 text-lg md:text-xl mb-10 max-w-lg leading-relaxed">Temukan gaya terbaikmu dengan koleksi pakaian eksklusif kami. Desain premium dengan kenyamanan maksimal.</p>
                        <a href="#" class="inline-flex items-center gap-3 bg-white text-brand font-bold px-8 py-4 rounded-full hover:bg-slate-100 hover:scale-105 transition-all shadow-xl text-lg group">
                            Belanja Sekarang <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Product Grid -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="flex justify-between items-end mb-10">
                <h2 class="text-3xl font-black tracking-tight">Produk Terlaris</h2>
                <a href="#" class="font-bold text-slate-500 hover:text-brand flex items-center gap-2 group transition">
                    Lihat Semua <i class="fa-solid fa-arrow-right-long group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-12">
                <!-- Product 1 -->
                <div class="group relative cursor-pointer">
                    <div class="w-full aspect-[3/4] bg-slate-100 rounded-3xl overflow-hidden relative mb-5 shadow-sm group-hover:shadow-xl transition-shadow duration-500">
                        <img src="https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="T-Shirt" class="w-full h-full object-cover object-center group-hover:scale-110 transition duration-700">
                        <div class="absolute top-4 left-4 flex flex-col gap-2 z-10">
                            <span class="bg-white text-brand text-[10px] font-black tracking-widest px-3 py-1.5 rounded-md shadow-md uppercase">BARU</span>
                        </div>
                        <!-- Hover Overlay -->
                        <div class="absolute inset-0 bg-slate-900/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <button class="absolute bottom-5 left-1/2 -translate-x-1/2 bg-brand text-white w-[85%] py-3.5 rounded-2xl font-bold opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 hover:bg-slate-800 shadow-xl flex items-center justify-center gap-2 z-20">
                            <i class="fa-solid fa-cart-plus"></i> Masukkan Keranjang
                        </button>
                    </div>
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900 group-hover:text-accent transition truncate">Essential Premium T-Shirt</h3>
                            <p class="text-sm font-medium text-slate-500">Kapas Organik</p>
                        </div>
                    </div>
                    <p class="font-black text-xl text-slate-900 mt-2">Rp 250.000</p>
                </div>

                <!-- Product 2 -->
                <div class="group relative cursor-pointer">
                    <div class="w-full aspect-[3/4] bg-slate-100 rounded-3xl overflow-hidden relative mb-5 shadow-sm group-hover:shadow-xl transition-shadow duration-500">
                        <img src="https://images.unsplash.com/photo-1576995853123-5a10305d93c0?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Jeans" class="w-full h-full object-cover object-center group-hover:scale-110 transition duration-700">
                        <div class="absolute top-4 left-4 flex flex-col gap-2 z-10">
                            <span class="bg-accent text-white text-[10px] font-black tracking-widest px-3 py-1.5 rounded-md shadow-md uppercase">-20%</span>
                        </div>
                        <div class="absolute inset-0 bg-slate-900/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <button class="absolute bottom-5 left-1/2 -translate-x-1/2 bg-brand text-white w-[85%] py-3.5 rounded-2xl font-bold opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 hover:bg-slate-800 shadow-xl flex items-center justify-center gap-2 z-20">
                            <i class="fa-solid fa-cart-plus"></i> Masukkan Keranjang
                        </button>
                    </div>
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900 group-hover:text-accent transition truncate">Classic Denim Jeans</h3>
                            <p class="text-sm font-medium text-slate-500">Slim Fit</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <p class="font-black text-xl text-accent">Rp 480.000</p>
                        <p class="text-sm font-semibold text-slate-400 line-through">Rp 600.000</p>
                    </div>
                </div>

                <!-- Product 3 -->
                <div class="group relative cursor-pointer">
                    <div class="w-full aspect-[3/4] bg-slate-100 rounded-3xl overflow-hidden relative mb-5 shadow-sm group-hover:shadow-xl transition-shadow duration-500">
                        <img src="https://images.unsplash.com/photo-1591047139829-d91aecb6caea?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Jacket" class="w-full h-full object-cover object-center group-hover:scale-110 transition duration-700">
                        <div class="absolute inset-0 bg-slate-900/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <button class="absolute bottom-5 left-1/2 -translate-x-1/2 bg-brand text-white w-[85%] py-3.5 rounded-2xl font-bold opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 hover:bg-slate-800 shadow-xl flex items-center justify-center gap-2 z-20">
                            <i class="fa-solid fa-cart-plus"></i> Masukkan Keranjang
                        </button>
                    </div>
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900 group-hover:text-accent transition truncate">Waterproof Jacket</h3>
                            <p class="text-sm font-medium text-slate-500">Outerwear</p>
                        </div>
                    </div>
                    <p class="font-black text-xl text-slate-900 mt-2">Rp 750.000</p>
                </div>

                <!-- Product 4 -->
                <div class="group relative cursor-pointer">
                    <div class="w-full aspect-[3/4] bg-slate-100 rounded-3xl overflow-hidden relative mb-5 shadow-sm group-hover:shadow-xl transition-shadow duration-500">
                        <img src="https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Shoes" class="w-full h-full object-cover object-center group-hover:scale-110 transition duration-700">
                        <div class="absolute inset-0 bg-slate-900/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <button class="absolute bottom-5 left-1/2 -translate-x-1/2 bg-brand text-white w-[85%] py-3.5 rounded-2xl font-bold opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 hover:bg-slate-800 shadow-xl flex items-center justify-center gap-2 z-20">
                            <i class="fa-solid fa-cart-plus"></i> Masukkan Keranjang
                        </button>
                    </div>
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900 group-hover:text-accent transition truncate">Retro Sneakers</h3>
                            <p class="text-sm font-medium text-slate-500">Sepatu</p>
                        </div>
                    </div>
                    <p class="font-black text-xl text-slate-900 mt-2">Rp 890.000</p>
                </div>
            </div>
        </section>

        <!-- Banner -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 mb-10">
            <div class="bg-brand rounded-[2.5rem] p-10 md:p-16 text-center text-white flex flex-col items-center relative overflow-hidden shadow-2xl">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-accent/20 rounded-full blur-3xl"></div>
                <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl"></div>
                
                <i class="fa-solid fa-truck-fast text-5xl mb-6 text-accent relative z-10 drop-shadow-lg"></i>
                <h2 class="text-3xl md:text-5xl font-black mb-4 relative z-10 tracking-tight">Gratis Ongkir Seluruh Indonesia</h2>
                <p class="text-slate-300 text-lg mb-8 max-w-xl relative z-10">Minimal pembelanjaan Rp 500.000. Berlaku untuk semua produk tanpa syarat dan ketentuan tersembunyi.</p>
                <a href="#" class="bg-white text-brand font-bold px-10 py-4 rounded-full hover:bg-slate-100 transition hover:scale-105 shadow-xl relative z-10">Cek Info Detail</a>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-50 border-t border-slate-200 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center gap-2 mb-6">
                    <div class="w-8 h-8 bg-accent rounded-lg flex items-center justify-center text-white text-sm shadow-md">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                    <span class="text-xl font-black tracking-tighter uppercase text-slate-900">Ecommerce</span>
                </div>
                <p class="text-slate-500 max-w-sm mb-8 leading-relaxed font-medium">Toko baju online terpercaya dengan ribuan koleksi terbaru setiap minggunya. Kualitas premium, harga terjangkau.</p>
                <div class="flex gap-3">
                    <div class="w-12 h-8 bg-white shadow-sm border border-slate-200 rounded flex items-center justify-center text-brand"><i class="fa-brands fa-cc-visa text-xl"></i></div>
                    <div class="w-12 h-8 bg-white shadow-sm border border-slate-200 rounded flex items-center justify-center text-brand"><i class="fa-brands fa-cc-mastercard text-xl"></i></div>
                    <div class="w-12 h-8 bg-white shadow-sm border border-slate-200 rounded flex items-center justify-center text-brand"><i class="fa-brands fa-cc-paypal text-xl"></i></div>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 mb-6 text-lg">Bantuan</h4>
                <ul class="space-y-4 text-slate-500 font-medium">
                    <li><a href="#" class="hover:text-accent transition">Status Pesanan</a></li>
                    <li><a href="#" class="hover:text-accent transition">Pengembalian Barang</a></li>
                    <li><a href="#" class="hover:text-accent transition">Panduan Ukuran</a></li>
                    <li><a href="#" class="hover:text-accent transition">Hubungi CS Kami</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 mb-6 text-lg">Perusahaan</h4>
                <ul class="space-y-4 text-slate-500 font-medium">
                    <li><a href="#" class="hover:text-accent transition">Tentang Kami</a></li>
                    <li><a href="#" class="hover:text-accent transition">Karir</a></li>
                    <li><a href="#" class="hover:text-accent transition">Syarat & Ketentuan</a></li>
                    <li><a href="#" class="hover:text-accent transition">Kebijakan Privasi</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 border-t border-slate-200 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm font-semibold text-slate-500">
                &copy; 2026 Ecommerce. Hak Cipta Dilindungi.
            </p>
            <div class="flex gap-4">
                <a href="#" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-accent hover:border-accent transition shadow-sm"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-accent hover:border-accent transition shadow-sm"><i class="fa-brands fa-twitter"></i></a>
                <a href="#" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-accent hover:border-accent transition shadow-sm"><i class="fa-brands fa-facebook-f"></i></a>
            </div>
        </div>
    </footer>
</body>
</html>