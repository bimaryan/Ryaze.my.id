<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Ryaze</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <script>
        (function () {
            var stored = localStorage.getItem('ryaze-theme');
            if (stored === null) {
                stored = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            if (stored === 'dark') {
                document.documentElement.classList.add('dark');
                document.documentElement.style.colorScheme = 'dark';
            } else {
                document.documentElement.style.colorScheme = 'light';
            }
        })();
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .bg-grid {
            background-image: linear-gradient(to right, #f1f5f9 1px, transparent 1px),
                linear-gradient(to bottom, #f1f5f9 1px, transparent 1px);
            background-size: 40px 40px;
            background-position: center top;
        }
        .dark .bg-grid {
            background-image: linear-gradient(to right, #1e293b 1px, transparent 1px),
                linear-gradient(to bottom, #1e293b 1px, transparent 1px);
        }
    </style>
</head>
<body class="antialiased selection:bg-indigo-600 selection:text-white bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-50 bg-grid min-h-screen flex flex-col relative overflow-hidden">
    
    <!-- NAVBAR -->
    <x-public-nav :links="[
        ['label' => 'Tentang', 'href' => url('/#about')],
        ['label' => 'Layanan', 'href' => url('/#services')],
        ['label' => 'Portofolio', 'href' => url('/#portfolio')],
        ['label' => 'Blog', 'href' => route('blog.index')]
    ]" />

    <!-- CONTENT -->
    <main class="flex-grow flex items-center justify-center pt-24 pb-16 px-6">
        <div class="w-full max-w-2xl text-center">
            <!-- Decorative Icon -->
            <div class="w-20 h-20 bg-indigo-50 dark:bg-indigo-500/10 rounded-2xl flex items-center justify-center mx-auto mb-8 rotate-3">
                <i class="fa-solid fa-triangle-exclamation text-4xl text-indigo-500 dark:text-indigo-400 -rotate-3"></i>
            </div>
            
            <h1 class="text-7xl md:text-9xl font-black text-slate-900 dark:text-slate-50 tracking-tighter mb-4">
                @yield('code')
            </h1>
            
            <h2 class="text-2xl md:text-3xl font-bold text-slate-800 dark:text-slate-100 mb-4">
                @yield('message')
            </h2>
            
            <p class="text-base md:text-lg text-slate-500 dark:text-slate-400 mb-10 max-w-lg mx-auto leading-relaxed">
                @yield('description')
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ url('/') }}" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                    <i class="fa-solid fa-home"></i> Kembali ke Beranda
                </a>
                <button onclick="window.history.back()" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 bg-white dark:bg-slate-800/60 hover:bg-slate-50 dark:hover:bg-slate-700/40 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-bold py-3 px-6 rounded-lg transition-colors">
                    <i class="fa-solid fa-arrow-left"></i> Kembali Sebelumnya
                </button>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <x-public-footer />

    <script nonce="{{ csp_nonce() }}">
        window.ryazeToggleTheme = function (event) {
            const isDark = document.documentElement.classList.contains('dark');
            
            const toggleTheme = () => {
                var dark = document.documentElement.classList.toggle('dark');
                document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
                localStorage.setItem('ryaze-theme', dark ? 'dark' : 'light');
                document.dispatchEvent(new CustomEvent('theme:change', { detail: { dark: dark } }));
                document.querySelectorAll('[role="switch"][onclick*="ryazeToggleTheme"]').forEach(function(btn) {
                    btn.setAttribute('aria-checked', dark);
                });
            };

            if (!document.startViewTransition) {
                toggleTheme();
                return;
            }

            const x = event?.clientX ?? window.innerWidth / 2;
            const y = event?.clientY ?? window.innerHeight / 2;
            const endRadius = Math.hypot(Math.max(x, innerWidth - x), Math.max(y, innerHeight - y));

            const transition = document.startViewTransition(toggleTheme);

            transition.ready.then(() => {
                const clipPath = [
                    `circle(0px at ${x}px ${y}px)`,
                    `circle(${endRadius}px at ${x}px ${y}px)`
                ];
                
                document.documentElement.animate(
                    {
                        clipPath: isDark ? [...clipPath].reverse() : clipPath,
                    },
                    {
                        duration: 500,
                        easing: 'ease-in-out',
                        pseudoElement: isDark ? '::view-transition-old(root)' : '::view-transition-new(root)',
                    }
                );
            });
        };
    </script>
</body>
</html>
