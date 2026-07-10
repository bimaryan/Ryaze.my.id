<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing - SaaS Landing Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { brand: '#2563eb', secondary: '#1e293b' }
                }
            }
        }
    </script>
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');</style>
</head>
<body class="bg-white text-slate-800 font-sans antialiased overflow-x-hidden">
    <!-- Navbar -->
    <header class="fixed top-0 w-full bg-white/80 backdrop-blur-md border-b border-slate-100 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="font-black text-2xl tracking-tighter flex items-center gap-2">
                <div class="w-8 h-8 bg-brand rounded-lg flex items-center justify-center text-white"><i class="fa-solid fa-cube"></i></div>
                Landing
            </div>
            <nav class="hidden md:flex gap-8 font-medium text-slate-500">
                <a href="#features" class="hover:text-slate-900 transition">Features</a>
                <a href="#testimonials" class="hover:text-slate-900 transition">Testimonials</a>
                <a href="#pricing" class="hover:text-slate-900 transition">Pricing</a>
            </nav>
            <div class="flex gap-4 items-center">
                <a href="#" class="hidden lg:block font-medium text-slate-600 hover:text-slate-900">Sign in</a>
                <a href="#" class="bg-slate-900 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-slate-800 transition shadow-lg shadow-slate-900/20">Get Started</a>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="pt-32 pb-20 lg:pt-48 lg:pb-32 px-4 text-center max-w-5xl mx-auto relative">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-brand/10 rounded-full blur-3xl -z-10"></div>
        <a href="#" class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-sm font-medium text-brand mb-8 hover:bg-blue-100 transition">
            <span class="bg-brand text-white text-[10px] uppercase font-bold px-2 py-0.5 rounded-full">New</span> Introducing AI Features <i class="fa-solid fa-arrow-right"></i>
        </a>
        <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-8 text-slate-900 leading-[1.1]">
            Build faster. <br class="hidden md:block">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand to-cyan-500">Scale infinitely.</span>
        </h1>
        <p class="text-xl text-slate-500 mb-10 max-w-2xl mx-auto leading-relaxed">
            The ultimate platform for modern teams to collaborate, design, and ship products at lightning speed. Start for free today.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="#" class="bg-brand text-white px-8 py-4 rounded-xl font-bold hover:bg-blue-700 transition shadow-xl shadow-brand/30 flex items-center justify-center gap-2 text-lg">
                Start your free trial <i class="fa-solid fa-arrow-right"></i>
            </a>
            <a href="#" class="bg-white text-slate-700 border border-slate-200 px-8 py-4 rounded-xl font-bold hover:bg-slate-50 transition flex items-center justify-center gap-2 text-lg">
                <i class="fa-solid fa-play"></i> Watch Demo
            </a>
        </div>
        
        <!-- Dashboard Mockup -->
        <div class="mt-20 relative mx-auto max-w-5xl">
            <div class="rounded-2xl border border-slate-200/50 bg-slate-50 p-2 shadow-2xl relative">
                <div class="absolute -top-4 -left-4 w-32 h-32 bg-blue-400 rounded-full mix-blend-multiply filter blur-2xl opacity-70"></div>
                <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-purple-400 rounded-full mix-blend-multiply filter blur-2xl opacity-70"></div>
                <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Dashboard" class="rounded-xl border border-slate-200 shadow-sm w-full relative z-10">
            </div>
        </div>
    </section>

    <!-- Trusted By -->
    <section class="py-10 border-y border-slate-100 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-6">Trusted by innovative teams worldwide</p>
            <div class="flex flex-wrap justify-center gap-8 md:gap-16 opacity-50 grayscale hover:grayscale-0 transition-all duration-500">
                <i class="fa-brands fa-aws text-4xl"></i>
                <i class="fa-brands fa-google text-4xl"></i>
                <i class="fa-brands fa-microsoft text-4xl"></i>
                <i class="fa-brands fa-stripe text-4xl"></i>
                <i class="fa-brands fa-figma text-4xl"></i>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-4">Everything you need to succeed</h2>
                <p class="text-lg text-slate-500 max-w-2xl mx-auto">Our platform provides all the tools you need to build, scale, and manage your projects efficiently.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="bg-slate-50 p-8 rounded-2xl border border-slate-100 hover:shadow-lg transition-shadow group">
                    <div class="w-14 h-14 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-center text-brand text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Lightning Fast</h3>
                    <p class="text-slate-500 leading-relaxed">Built on edge infrastructure to deliver content to your users in milliseconds, anywhere in the world.</p>
                </div>
                <div class="bg-slate-50 p-8 rounded-2xl border border-slate-100 hover:shadow-lg transition-shadow group">
                    <div class="w-14 h-14 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-center text-brand text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Enterprise Security</h3>
                    <p class="text-slate-500 leading-relaxed">Bank-grade encryption, role-based access control, and automated compliance out of the box.</p>
                </div>
                <div class="bg-slate-50 p-8 rounded-2xl border border-slate-100 hover:shadow-lg transition-shadow group">
                    <div class="w-14 h-14 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-center text-brand text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Real-time Analytics</h3>
                    <p class="text-slate-500 leading-relaxed">Gain deep insights into user behavior and system performance with our intuitive dashboards.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="py-24 bg-slate-50 border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-4">Simple, transparent pricing</h2>
                <p class="text-lg text-slate-500 max-w-2xl mx-auto">No hidden fees. No surprise charges. Choose the plan that fits your needs.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 max-w-4xl mx-auto gap-8">
                <!-- Basic Plan -->
                <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Starter</h3>
                    <p class="text-slate-500 text-sm mb-6">Perfect for individuals and small projects.</p>
                    <div class="mb-6">
                        <span class="text-4xl font-black text-slate-900">$0</span>
                        <span class="text-slate-500 font-medium">/month</span>
                    </div>
                    <a href="#" class="block w-full py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-center rounded-xl transition">Get Started</a>
                    <ul class="mt-8 space-y-4 text-sm text-slate-600">
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-green-500"></i> Up to 3 projects</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-green-500"></i> Community support</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-green-500"></i> 1GB Storage</li>
                    </ul>
                </div>
                
                <!-- Pro Plan -->
                <div class="bg-slate-900 p-8 rounded-3xl shadow-xl relative overflow-hidden text-white">
                    <div class="absolute top-0 right-0 bg-brand text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-3xl">POPULAR</div>
                    <h3 class="text-xl font-bold mb-2">Professional</h3>
                    <p class="text-slate-400 text-sm mb-6">For growing teams and businesses.</p>
                    <div class="mb-6">
                        <span class="text-4xl font-black">$29</span>
                        <span class="text-slate-400 font-medium">/month</span>
                    </div>
                    <a href="#" class="block w-full py-3 px-4 bg-brand hover:bg-blue-500 text-white font-bold text-center rounded-xl transition shadow-lg shadow-brand/30">Start 14-day Free Trial</a>
                    <ul class="mt-8 space-y-4 text-sm text-slate-300">
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-brand"></i> Unlimited projects</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-brand"></i> 24/7 Priority support</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-brand"></i> 100GB Storage</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-brand"></i> Advanced Analytics</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20 bg-brand">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-5xl font-bold text-white mb-6">Ready to transform your workflow?</h2>
            <p class="text-lg text-blue-100 mb-10">Join thousands of teams who are already building the future on our platform.</p>
            <a href="#" class="bg-white text-brand px-8 py-4 rounded-xl font-bold hover:bg-slate-50 transition shadow-lg text-lg inline-block">Get Started for Free</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-2 md:grid-cols-4 gap-8 mb-12">
            <div>
                <div class="font-black text-xl tracking-tighter flex items-center gap-2 mb-6 text-white">
                    <div class="w-6 h-6 bg-brand rounded flex items-center justify-center text-white text-xs"><i class="fa-solid fa-cube"></i></div>
                    Landing
                </div>
                <p class="text-slate-500 text-sm">Building the future of web development, one block at a time.</p>
            </div>
            <div>
                <h4 class="font-bold text-white mb-4">Product</h4>
                <ul class="space-y-3 text-sm text-slate-400">
                    <li><a href="#" class="hover:text-white transition">Features</a></li>
                    <li><a href="#" class="hover:text-white transition">Pricing</a></li>
                    <li><a href="#" class="hover:text-white transition">Changelog</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-white mb-4">Company</h4>
                <ul class="space-y-3 text-sm text-slate-400">
                    <li><a href="#" class="hover:text-white transition">About Us</a></li>
                    <li><a href="#" class="hover:text-white transition">Careers</a></li>
                    <li><a href="#" class="hover:text-white transition">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-white mb-4">Legal</h4>
                <ul class="space-y-3 text-sm text-slate-400">
                    <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-white transition">Terms of Service</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm text-slate-500">&copy; 2026 Landing Inc. All rights reserved.</p>
            <div class="flex gap-4 text-slate-500">
                <a href="#" class="hover:text-white transition"><i class="fa-brands fa-twitter"></i></a>
                <a href="#" class="hover:text-white transition"><i class="fa-brands fa-github"></i></a>
                <a href="#" class="hover:text-white transition"><i class="fa-brands fa-discord"></i></a>
            </div>
        </div>
    </footer>
</body>
</html>