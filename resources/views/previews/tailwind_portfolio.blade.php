<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio - Portfolio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: { primary: '#6366f1' }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');
        .glass { background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .blob { animation: float 6s ease-in-out infinite; }
        @keyframes float { 0% { transform: translateY(0px) scale(1); } 50% { transform: translateY(-20px) scale(1.05); } 100% { transform: translateY(0px) scale(1); } }
    </style>
</head>
<body class="bg-slate-950 text-slate-300 font-sans antialiased selection:bg-primary selection:text-white overflow-x-hidden">
    
    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="#" class="text-2xl font-bold text-white tracking-tight">Portfolio<span class="text-primary">.</span></a>
                <div class="hidden md:flex space-x-8">
                    <a href="#home" class="text-sm font-medium text-white hover:text-primary transition">Home</a>
                    <a href="#about" class="text-sm font-medium text-slate-400 hover:text-white transition">About</a>
                    <a href="#projects" class="text-sm font-medium text-slate-400 hover:text-white transition">Projects</a>
                    <a href="#contact" class="text-sm font-medium text-slate-400 hover:text-white transition">Contact</a>
                </div>
                <a href="#contact" class="hidden md:inline-flex items-center justify-center px-5 py-2.5 bg-primary/10 text-primary hover:bg-primary hover:text-white rounded-full text-sm font-semibold transition-all">Let's Talk</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 min-h-screen flex items-center">
        <div class="absolute top-1/4 -left-20 w-96 h-96 bg-primary/20 rounded-full mix-blend-screen filter blur-3xl opacity-50 blob"></div>
        <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-purple-500/20 rounded-full mix-blend-screen filter blur-3xl opacity-50 blob" style="animation-delay: 2s;"></div>
        
        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-800/50 border border-slate-700 text-sm font-medium text-slate-300 mb-6">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span> Available for work
                    </div>
                    <h1 class="text-5xl lg:text-7xl font-extrabold text-white tracking-tight mb-6 leading-tight">
                        Hi, I'm <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-purple-400">Portfolio</span>
                    </h1>
                    <p class="text-xl text-slate-400 mb-8 max-w-xl leading-relaxed">
                        A passionate Full Stack Developer & UI/UX Designer specializing in building exceptional digital experiences.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="#projects" class="px-8 py-4 bg-primary hover:bg-indigo-500 text-white rounded-full font-semibold transition-all hover:shadow-[0_0_20px_rgba(99,102,241,0.4)] hover:-translate-y-1">View My Work</a>
                        <a href="https://github.com" target="_blank" class="px-8 py-4 bg-slate-800 hover:bg-slate-700 text-white rounded-full font-semibold transition-all flex items-center gap-2 border border-slate-700 hover:-translate-y-1">
                            <i class="fa-brands fa-github text-xl"></i> Github
                        </a>
                    </div>
                </div>
                <div class="relative hidden lg:block">
                    <div class="w-full aspect-square max-w-md mx-auto relative">
                        <div class="absolute inset-0 bg-gradient-to-tr from-primary to-purple-500 rounded-3xl transform rotate-6 opacity-50 blur-lg"></div>
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Profile" class="w-full h-full object-cover rounded-3xl relative z-10 shadow-2xl border border-slate-800">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services / Skills -->
    <section id="about" class="py-24 bg-slate-900 border-y border-slate-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">What I Do</h2>
                <p class="text-slate-400 max-w-2xl mx-auto">I craft high-performance, beautifully designed web applications from concept to deployment.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Skill 1 -->
                <div class="bg-slate-800/50 p-8 rounded-2xl border border-slate-700 hover:border-primary/50 transition-colors group">
                    <div class="w-14 h-14 bg-slate-800 rounded-xl flex items-center justify-center text-primary text-2xl mb-6 shadow-inner group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">UI/UX Design</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Designing intuitive and modern interfaces with Figma, focusing on user experience and accessibility.</p>
                </div>
                <!-- Skill 2 -->
                <div class="bg-slate-800/50 p-8 rounded-2xl border border-slate-700 hover:border-primary/50 transition-colors group">
                    <div class="w-14 h-14 bg-slate-800 rounded-xl flex items-center justify-center text-purple-400 text-2xl mb-6 shadow-inner group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-code"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Frontend Dev</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Building responsive and interactive web applications using React, Vue, TailwindCSS, and Next.js.</p>
                </div>
                <!-- Skill 3 -->
                <div class="bg-slate-800/50 p-8 rounded-2xl border border-slate-700 hover:border-primary/50 transition-colors group">
                    <div class="w-14 h-14 bg-slate-800 rounded-xl flex items-center justify-center text-emerald-400 text-2xl mb-6 shadow-inner group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-database"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Backend & API</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Developing robust backend systems and RESTful APIs with Node.js, Express, Laravel, and PostgreSQL.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Projects -->
    <section id="projects" class="py-24">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">Featured Projects</h2>
                    <p class="text-slate-400">Some of my recent work.</p>
                </div>
                <a href="#" class="hidden sm:inline-flex text-primary hover:text-white font-medium items-center gap-2 transition">View all <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Project 1 -->
                <div class="group relative rounded-3xl overflow-hidden bg-slate-900 border border-slate-800">
                    <div class="aspect-video w-full overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Project 1" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <a href="#" class="px-6 py-3 bg-white text-slate-900 rounded-full font-bold transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 hover:scale-105">View Live Demo</a>
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="flex gap-2 mb-4">
                            <span class="px-3 py-1 text-xs font-medium bg-primary/10 text-primary rounded-full border border-primary/20">React</span>
                            <span class="px-3 py-1 text-xs font-medium bg-slate-800 text-slate-300 rounded-full border border-slate-700">Tailwind</span>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-2">E-Commerce Dashboard</h3>
                        <p class="text-slate-400 mb-6">A comprehensive admin panel for managing e-commerce stores with real-time analytics.</p>
                    </div>
                </div>
                
                <!-- Project 2 -->
                <div class="group relative rounded-3xl overflow-hidden bg-slate-900 border border-slate-800">
                    <div class="aspect-video w-full overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Project 2" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <a href="#" class="px-6 py-3 bg-white text-slate-900 rounded-full font-bold transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 hover:scale-105">View Live Demo</a>
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="flex gap-2 mb-4">
                            <span class="px-3 py-1 text-xs font-medium bg-purple-500/10 text-purple-400 rounded-full border border-purple-500/20">Next.js</span>
                            <span class="px-3 py-1 text-xs font-medium bg-slate-800 text-slate-300 rounded-full border border-slate-700">Stripe</span>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-2">SaaS Landing Page</h3>
                        <p class="text-slate-400 mb-6">A high-converting landing page for a B2B SaaS startup with integrated payment flows.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-24 bg-slate-900 border-t border-slate-800">
        <div class="max-w-4xl mx-auto px-6 lg:px-8 text-center">
            <h2 class="text-3xl lg:text-5xl font-bold text-white mb-6">Let's build something great together.</h2>
            <p class="text-slate-400 text-lg mb-10">I'm currently open for new opportunities and freelance projects. Whether you have a question or just want to say hi, I'll try my best to get back to you!</p>
            <a href="mailto:hello@example.com" class="inline-flex items-center gap-3 px-8 py-4 bg-primary hover:bg-indigo-500 text-white rounded-full font-bold text-lg transition-all hover:shadow-[0_0_20px_rgba(99,102,241,0.4)] hover:-translate-y-1">
                <i class="fa-regular fa-envelope"></i> Say Hello
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-10 border-t border-slate-800 text-center">
        <div class="flex justify-center gap-6 mb-6">
            <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-primary transition"><i class="fa-brands fa-twitter"></i></a>
            <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-primary transition"><i class="fa-brands fa-github"></i></a>
            <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-primary transition"><i class="fa-brands fa-linkedin-in"></i></a>
            <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-primary transition"><i class="fa-brands fa-dribbble"></i></a>
        </div>
        <p class="text-slate-500 text-sm">&copy; 2026 Portfolio. Designed with TailwindCSS.</p>
    </footer>

</body>
</html>