<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linkinbio - Link in Bio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap');
        .bg-animated {
            background: linear-gradient(-45deg, #4f46e5, #ec4899, #8b5cf6, #06b6d4);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .glass-card { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .btn-link { background: rgba(255, 255, 255, 0.95); }
        .btn-link:hover { transform: scale(1.02) translateY(-2px); background: #ffffff; }
    </style>
</head>
<body class="bg-animated min-h-screen font-sans antialiased flex flex-col items-center py-10 px-4 sm:px-6 selection:bg-white selection:text-pink-500">
    
    <!-- Share Button -->
    <div class="absolute top-6 right-6">
        <button class="w-10 h-10 rounded-full glass-card text-white flex items-center justify-center hover:bg-white/30 transition shadow-lg backdrop-blur-md">
            <i class="fa-solid fa-arrow-up-right-from-square"></i>
        </button>
    </div>

    <div class="w-full max-w-[440px] glass-card rounded-[3rem] p-8 sm:p-10 shadow-2xl relative mt-8">
        
        <!-- Profile Info -->
        <div class="text-center mb-10 relative">
            <div class="w-32 h-32 mx-auto bg-white/20 p-2 rounded-full shadow-2xl mb-6 relative backdrop-blur-sm border border-white/30">
                <img src="https://ui-avatars.com/api/?name=Creator+Name&background=random&size=200" alt="Profile" class="w-full h-full object-cover rounded-full">
                <!-- Verified Badge -->
                <div class="absolute bottom-2 right-2 bg-blue-500 text-white w-8 h-8 rounded-full border-[3px] border-white flex items-center justify-center text-[10px] shadow-lg">
                    <i class="fa-solid fa-check"></i>
                </div>
            </div>
            <h1 class="text-3xl font-black text-white mb-2 tracking-tight">@Linkinbio</h1>
            <p class="text-white/90 text-sm font-medium leading-relaxed max-w-xs mx-auto mb-6">
                Digital Creator & Developer 💻✨<br>Helping you build better websites.
            </p>
            
            <!-- Social Icons -->
            <div class="flex justify-center gap-3">
                <a href="#" class="w-11 h-11 rounded-full glass-card text-white flex items-center justify-center text-lg hover:bg-white hover:text-pink-500 transition-all hover:scale-110 shadow-md"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" class="w-11 h-11 rounded-full glass-card text-white flex items-center justify-center text-lg hover:bg-white hover:text-blue-400 transition-all hover:scale-110 shadow-md"><i class="fa-brands fa-twitter"></i></a>
                <a href="#" class="w-11 h-11 rounded-full glass-card text-white flex items-center justify-center text-lg hover:bg-white hover:text-red-500 transition-all hover:scale-110 shadow-md"><i class="fa-brands fa-youtube"></i></a>
                <a href="#" class="w-11 h-11 rounded-full glass-card text-white flex items-center justify-center text-lg hover:bg-white hover:text-black transition-all hover:scale-110 shadow-md"><i class="fa-brands fa-tiktok"></i></a>
                <a href="#" class="w-11 h-11 rounded-full glass-card text-white flex items-center justify-center text-lg hover:bg-white hover:text-blue-600 transition-all hover:scale-110 shadow-md"><i class="fa-brands fa-linkedin-in"></i></a>
            </div>
        </div>

        <!-- Links -->
        <div class="space-y-4">
            <a href="#" class="btn-link block w-full text-slate-800 text-center font-bold py-4 px-6 rounded-2xl shadow-xl transition-all duration-300 relative group overflow-hidden border border-white/50">
                <div class="absolute inset-0 w-0 bg-gradient-to-r from-pink-500/10 to-transparent transition-all duration-500 group-hover:w-full"></div>
                <div class="relative flex items-center justify-between z-10">
                    <div class="w-10 h-10 rounded-xl bg-pink-100 text-pink-500 flex items-center justify-center text-lg"><i class="fa-solid fa-globe"></i></div>
                    <span class="flex-1 px-4 text-lg">My Personal Website</span>
                    <div class="w-6 text-slate-300 group-hover:text-pink-500 transition"><i class="fa-solid fa-chevron-right text-sm"></i></div>
                </div>
            </a>
            
            <a href="#" class="btn-link block w-full text-slate-800 text-center font-bold py-4 px-6 rounded-2xl shadow-xl transition-all duration-300 relative group overflow-hidden border border-white/50">
                <div class="absolute inset-0 w-0 bg-gradient-to-r from-red-500/10 to-transparent transition-all duration-500 group-hover:w-full"></div>
                <div class="relative flex items-center justify-between z-10">
                    <div class="w-10 h-10 rounded-xl bg-red-100 text-red-500 flex items-center justify-center text-lg"><i class="fa-brands fa-youtube"></i></div>
                    <span class="flex-1 px-4 text-lg">Latest YouTube Video</span>
                    <div class="w-6 text-slate-300 group-hover:text-red-500 transition"><i class="fa-solid fa-chevron-right text-sm"></i></div>
                </div>
            </a>
            
            <a href="#" class="btn-link block w-full text-slate-800 text-center font-bold py-4 px-6 rounded-2xl shadow-xl transition-all duration-300 relative group overflow-hidden border border-white/50">
                <div class="absolute inset-0 w-0 bg-gradient-to-r from-blue-500/10 to-transparent transition-all duration-500 group-hover:w-full"></div>
                <div class="relative flex items-center justify-between z-10">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-500 flex items-center justify-center text-lg"><i class="fa-brands fa-discord"></i></div>
                    <span class="flex-1 px-4 text-lg">Join Discord Community</span>
                    <div class="w-6 text-slate-300 group-hover:text-blue-500 transition"><i class="fa-solid fa-chevron-right text-sm"></i></div>
                </div>
            </a>
            
            <a href="#" class="btn-link block w-full text-slate-800 text-center font-bold py-4 px-6 rounded-2xl shadow-xl transition-all duration-300 relative group overflow-hidden border border-white/50">
                <div class="absolute inset-0 w-0 bg-gradient-to-r from-yellow-500/10 to-transparent transition-all duration-500 group-hover:w-full"></div>
                <div class="relative flex items-center justify-between z-10">
                    <div class="w-10 h-10 rounded-xl bg-yellow-100 text-yellow-500 flex items-center justify-center text-lg"><i class="fa-solid fa-mug-hot"></i></div>
                    <span class="flex-1 px-4 text-lg">Buy me a Coffee</span>
                    <div class="w-6 text-slate-300 group-hover:text-yellow-500 transition"><i class="fa-solid fa-chevron-right text-sm"></i></div>
                </div>
            </a>
        </div>
    </div>
    
    <div class="mt-8 text-white/80 text-sm font-bold tracking-wide">
        Powered by <a href="#" class="hover:text-white underline decoration-white/30 hover:decoration-white transition">Ryaze Hosting</a>
    </div>
</body>
</html>