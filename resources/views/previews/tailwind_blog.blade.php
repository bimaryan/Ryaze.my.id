<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Blog & Journal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Merriweather', 'serif']
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400&display=swap');
    </style>
</head>
<body class="bg-stone-50 text-stone-900 antialiased font-sans">
    
    <!-- Header -->
    <header class="border-b border-stone-200 bg-white sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between h-20 items-center">
            <a href="#" class="font-serif font-black text-2xl tracking-tight">Blog<span class="text-orange-500">.</span></a>
            <nav class="hidden md:flex gap-6 font-medium text-sm text-stone-600">
                <a href="#" class="hover:text-stone-900 transition">Design</a>
                <a href="#" class="hover:text-stone-900 transition">Technology</a>
                <a href="#" class="hover:text-stone-900 transition">Life</a>
                <a href="#" class="hover:text-stone-900 transition">About</a>
            </nav>
            <div class="flex items-center gap-4">
                <button class="w-10 h-10 rounded-full flex items-center justify-center text-stone-500 hover:bg-stone-100 transition"><i class="fa-solid fa-magnifying-glass"></i></button>
                <a href="#" class="bg-stone-900 text-white px-5 py-2 rounded-full text-sm font-semibold hover:bg-stone-800 transition shadow-md">Subscribe</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        <!-- Featured Post -->
        <article class="mb-20 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center group cursor-pointer">
            <div class="overflow-hidden rounded-2xl shadow-lg relative aspect-[4/3] lg:aspect-auto lg:h-[450px]">
                <img src="https://images.unsplash.com/photo-1499750310107-5fef28a66643?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" class="w-full h-full object-cover group-hover:scale-105 transition duration-700" alt="Featured">
                <div class="absolute inset-0 bg-stone-900/10 group-hover:bg-transparent transition duration-700"></div>
            </div>
            <div class="flex flex-col justify-center">
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-orange-600 font-bold text-xs tracking-widest uppercase">Design</span>
                    <span class="text-stone-400 text-sm">Oct 12, 2026</span>
                </div>
                <h1 class="text-4xl md:text-5xl font-serif font-bold mb-6 leading-tight group-hover:text-orange-600 transition">The Art of Minimalism in Modern UI Design</h1>
                <p class="text-lg text-stone-600 font-serif leading-relaxed mb-8">Minimalism isn't just about removing things; it's about making sure everything that remains has a clear purpose. In modern web development, this translates to faster load times and clearer user flows.</p>
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name=Alex+Carter&background=f97316&color=fff" class="w-10 h-10 rounded-full shadow-sm">
                    <div>
                        <p class="text-sm font-bold">Alex Carter</p>
                        <p class="text-xs text-stone-500">Lead Designer</p>
                    </div>
                </div>
            </div>
        </article>

        <div class="flex items-center justify-between border-b border-stone-200 pb-4 mb-10">
            <h2 class="text-2xl font-bold font-serif">Latest Articles</h2>
            <a href="#" class="text-sm font-bold text-orange-600 hover:text-orange-700 flex items-center gap-1">View All <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <!-- Grid Posts -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 mb-20">
            <!-- Article 1 -->
            <article class="group cursor-pointer">
                <div class="overflow-hidden rounded-xl mb-5 aspect-[4/3] shadow-md relative">
                    <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                </div>
                <span class="text-blue-600 font-bold text-xs tracking-widest uppercase mb-2 block">Technology</span>
                <h3 class="text-xl font-serif font-bold mb-3 leading-snug group-hover:text-blue-600 transition">Why you should learn Rust in 2026</h3>
                <p class="text-stone-600 line-clamp-3 font-serif text-sm leading-relaxed mb-4">Memory safety without garbage collection is just the beginning of why developers love Rust.</p>
                <p class="text-xs text-stone-400 font-medium">Oct 10, 2026 &middot; 5 min read</p>
            </article>

            <!-- Article 2 -->
            <article class="group cursor-pointer">
                <div class="overflow-hidden rounded-xl mb-5 aspect-[4/3] shadow-md relative">
                    <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                </div>
                <span class="text-green-600 font-bold text-xs tracking-widest uppercase mb-2 block">Life</span>
                <h3 class="text-xl font-serif font-bold mb-3 leading-snug group-hover:text-green-600 transition">Balancing remote work and mental health</h3>
                <p class="text-stone-600 line-clamp-3 font-serif text-sm leading-relaxed mb-4">Tips and strategies for maintaining boundaries when your office is also your living room.</p>
                <p class="text-xs text-stone-400 font-medium">Oct 08, 2026 &middot; 8 min read</p>
            </article>

            <!-- Article 3 -->
            <article class="group cursor-pointer">
                <div class="overflow-hidden rounded-xl mb-5 aspect-[4/3] shadow-md relative">
                    <img src="https://images.unsplash.com/photo-1618761714954-0b8cd0026356?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                </div>
                <span class="text-purple-600 font-bold text-xs tracking-widest uppercase mb-2 block">AI</span>
                <h3 class="text-xl font-serif font-bold mb-3 leading-snug group-hover:text-purple-600 transition">The future of generative AI in software development</h3>
                <p class="text-stone-600 line-clamp-3 font-serif text-sm leading-relaxed mb-4">How AI agents are changing the way we write, debug, and deploy code in production.</p>
                <p class="text-xs text-stone-400 font-medium">Oct 05, 2026 &middot; 12 min read</p>
            </article>
        </div>

        <!-- Newsletter -->
        <div class="bg-stone-900 rounded-3xl p-10 md:p-16 text-center text-white relative overflow-hidden shadow-2xl">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-orange-500/20 rounded-full blur-3xl"></div>
            
            <i class="fa-regular fa-envelope-open text-4xl text-orange-500 mb-6 relative z-10"></i>
            <h2 class="text-3xl font-serif font-bold mb-4 relative z-10">Get the latest articles in your inbox</h2>
            <p class="text-stone-400 mb-8 max-w-lg mx-auto relative z-10">Join 5,000+ subscribers who receive our weekly newsletter on design, code, and startups. No spam.</p>
            <form class="flex flex-col sm:flex-row gap-3 justify-center max-w-md mx-auto relative z-10">
                <input type="email" placeholder="Your email address" class="px-6 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent flex-grow transition">
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-xl font-bold transition shadow-lg shadow-orange-500/30">Subscribe</button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-stone-200 py-12 mt-10">
        <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="font-serif font-black text-xl tracking-tight">Blog<span class="text-orange-500">.</span></div>
            <p class="text-stone-500 text-sm font-medium">&copy; 2026 Blog. All rights reserved.</p>
            <div class="flex gap-4 text-stone-400">
                <a href="#" class="hover:text-stone-900 transition"><i class="fa-brands fa-twitter text-lg"></i></a>
                <a href="#" class="hover:text-stone-900 transition"><i class="fa-brands fa-github text-lg"></i></a>
                <a href="#" class="hover:text-stone-900 transition"><i class="fa-brands fa-dribbble text-lg"></i></a>
            </div>
        </div>
    </footer>
</body>
</html>