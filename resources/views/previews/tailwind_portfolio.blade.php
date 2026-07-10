<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Tailwind Portfolio - Personal Portfolio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-200 antialiased selection:bg-indigo-500 selection:text-white">
    <nav class="sticky top-0 z-50 backdrop-blur-md bg-slate-900/80 border-b border-slate-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="font-bold text-xl text-white tracking-tight">Preview Tailwind Portfolio<span class="text-indigo-500">.</span></div>
                <div class="flex space-x-8">
                    <a href="#about" class="text-sm font-medium hover:text-white transition-colors">About</a>
                    <a href="#projects" class="text-sm font-medium hover:text-white transition-colors">Projects</a>
                    <a href="#contact" class="text-sm font-medium hover:text-white transition-colors">Contact</a>
                </div>
            </div>
        </div>
    </nav>
    <main>
        <section id="hero" class="py-20 lg:py-32 flex flex-col items-center text-center px-4">
            <div class="w-24 h-24 bg-gradient-to-tr from-indigo-500 to-purple-500 rounded-full mb-6 p-1">
                <div class="w-full h-full bg-slate-800 rounded-full border-4 border-slate-900"></div>
            </div>
            <h1 class="text-5xl lg:text-7xl font-extrabold text-white tracking-tight mb-6">Creative Developer</h1>
            <p class="text-xl text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed">I build exceptional and accessible digital experiences for the web. Specialized in modern frontend frameworks.</p>
            <div class="flex gap-4">
                <a href="#contact" class="bg-indigo-600 hover:bg-indigo-500 text-white px-8 py-3 rounded-full font-semibold transition-all">Get in touch</a>
                <a href="#projects" class="bg-slate-800 hover:bg-slate-700 text-white px-8 py-3 rounded-full font-semibold transition-all">View Work</a>
            </div>
        </section>
    </main>
</body>
</html>