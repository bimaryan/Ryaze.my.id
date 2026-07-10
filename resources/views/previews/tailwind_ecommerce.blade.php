<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Tailwind Ecommerce - E-Commerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-900">
    <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <span class="text-2xl font-black text-pink-600 tracking-tighter">Shop<span class="text-gray-900">App</span></span>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="#" class="border-pink-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Home</a>
                    <a href="#" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Katalog</a>
                    <a href="#" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Promo</a>
                </div>
            </div>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-pink-600 rounded-3xl overflow-hidden shadow-xl mb-16 relative">
            <div class="px-8 py-16 sm:px-12 sm:py-24 relative z-10">
                <h1 class="text-4xl sm:text-5xl font-extrabold text-white tracking-tight mb-4">Koleksi Musim Panas 2026</h1>
                <p class="text-pink-100 text-lg sm:text-xl max-w-2xl mb-8">Diskon hingga 50% untuk produk terpilih. Belanja sekarang sebelum kehabisan!</p>
                <a href="#" class="inline-block bg-white text-pink-600 font-bold px-8 py-3 rounded-full hover:bg-gray-50 transition shadow-md">Belanja Sekarang</a>
            </div>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Produk Terbaru</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="group">
                <div class="w-full aspect-[4/5] bg-gray-200 rounded-2xl overflow-hidden mb-4 relative">
                    <img src="https://placehold.co/400x500/e2e8f0/64748b?text=Product+1" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <span class="absolute top-3 left-3 bg-white px-2 py-1 text-xs font-bold uppercase rounded text-gray-900 shadow-sm">Baru</span>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">Sepatu Sneakers Klasik</h3>
                <p class="text-lg font-bold text-gray-900">Rp 450.000</p>
            </div>
            <div class="group">
                <div class="w-full aspect-[4/5] bg-gray-200 rounded-2xl overflow-hidden mb-4 relative">
                    <img src="https://placehold.co/400x500/e2e8f0/64748b?text=Product+2" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">Kemeja Flanel Premium</h3>
                <p class="text-lg font-bold text-gray-900">Rp 250.000</p>
            </div>
        </div>
    </main>
</body>
</html>