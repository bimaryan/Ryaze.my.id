<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($currentCategory) ? $currentCategory->name . ' - ' : '' }}Blog - {{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze Portal' }}</title>
    @php
        $siteDescription = \App\Models\Setting::where('key', 'site_description')->value('value');
        $siteFavicon = \App\Models\Setting::where('key', 'site_favicon')->value('value');
        $gaId = \App\Models\Setting::where('key', 'google_analytics_id')->value('value');
        $siteLogo = \App\Models\Setting::where('key', 'site_logo')->value('value');
    @endphp
    
    <meta name="description" content="{{ $siteDescription ?? 'Artikel terbaru seputar teknologi, web development, dan tips hosting dari tim Ryaze.' }}">
    
    @if($siteFavicon)
        <link rel="icon" href="{{ asset('storage/' . $siteFavicon) }}">
    @endif

    @if($gaId)
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $gaId }}');
        </script>
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" nonce="{{ app('csp_nonce') ?? '' }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" nonce="{{ app('csp_nonce') ?? '' }}">
    <style nonce="{{ app('csp_nonce') ?? '' }}">
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white text-slate-800 antialiased">

    {{-- Navbar --}}
    <nav class="fixed top-0 z-50 w-full bg-white/90 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                    @if($siteLogo)
                        <img src="{{ asset('storage/' . $siteLogo) }}" alt="Logo" class="h-8 object-contain">
                    @else
                        <div class="bg-indigo-600 text-white rounded-md w-8 h-8 flex items-center justify-center">
                            <i class="fa-solid fa-code text-sm"></i>
                        </div>
                    @endif
                    <span class="text-xl font-bold tracking-tight text-indigo-600">{{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze Portal' }}</span>
                </a>
                <div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                    <a href="{{ url('/') }}" class="hover:text-indigo-600 transition-colors">Beranda</a>
                    <a href="{{ route('blog.index') }}" class="text-indigo-600 font-semibold">Blog</a>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        @php
                            $dashboardUrl = match (Auth::user()->role ?? '') {
                                'superadmin' => route('superadmin.dashboard'),
                                'admin_joki' => route('admin_joki.dashboard'),
                                'admin_hosting' => route('admin_hosting.dashboard'),
                                'user_joki' => route('user_joki.dashboard'),
                                'user_hosting' => route('user_hosting.dashboard'),
                                default => url('/'),
                            };
                        @endphp
                        <a href="{{ $dashboardUrl }}" class="text-sm font-semibold bg-indigo-600 text-white px-5 py-2 rounded-md hover:bg-indigo-700 transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors hidden sm:block">Masuk</a>
                        <a href="{{ route('register') }}" class="text-sm font-semibold bg-indigo-600 text-white px-5 py-2 rounded-md hover:bg-indigo-700 transition-colors">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Header --}}
    <section class="pt-28 pb-12 border-b border-slate-200 bg-slate-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 mb-2">
                @if(isset($currentCategory))
                    {{ $currentCategory->name }}
                @else
                    Blog
                @endif
            </h1>
            <p class="text-slate-500 text-sm">
                @if(isset($currentCategory))
                    {{ $currentCategory->description ?: 'Artikel dalam kategori ' . $currentCategory->name }}
                @else
                    Tulisan terbaru seputar teknologi, development, dan tips dari tim Ryaze.
                @endif
            </p>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-12">
        <div class="flex flex-col md:flex-row gap-8 lg:gap-12">
            {{-- Main Content --}}
            <div class="flex-1 min-w-0 order-2 md:order-1">
                {{-- Featured Article --}}
                @if(isset($featured) && $featured && !request('search') && !isset($currentCategory))
                    <a href="{{ route('blog.show', $featured->slug) }}" class="block mb-12 group">
                        <div class="border border-slate-200 rounded-xl overflow-hidden hover:border-indigo-300 transition-colors">
                            @if($featured->cover_image)
                                <div class="h-64 md:h-80 overflow-hidden bg-slate-100">
                                    <img src="{{ Storage::url($featured->cover_image) }}" alt="{{ $featured->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                </div>
                            @endif
                            <div class="p-6 md:p-8 bg-white">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold uppercase rounded">Sorotan</span>
                                    @if($featured->category)
                                        <span class="text-xs text-indigo-600 font-medium">{{ $featured->category->name }}</span>
                                    @endif
                                </div>
                                <h2 class="text-2xl font-bold text-slate-900 mb-2 group-hover:text-indigo-600 transition-colors">{{ $featured->title }}</h2>
                                <p class="text-slate-500 text-sm line-clamp-2 mb-4">{{ $featured->excerpt ?: Str::limit(strip_tags($featured->body), 200) }}</p>
                                <div class="flex items-center gap-4 text-xs text-slate-400">
                                    <span>{{ $featured->user->name ?? 'Admin' }}</span>
                                    <span>{{ $featured->published_at?->format('d M Y') }}</span>
                                    <span>{{ $featured->reading_time }} menit baca</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endif

                {{-- Article Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($articles as $article)
                        <a href="{{ route('blog.show', $article->slug) }}" class="group border border-slate-200 rounded-xl overflow-hidden hover:border-indigo-300 transition-colors bg-white flex flex-col">
                            @if($article->cover_image)
                                <div class="h-44 overflow-hidden bg-slate-100">
                                    <img src="{{ Storage::url($article->cover_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>
                            @endif
                            <div class="p-5 flex flex-col flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    @if($article->category)
                                        <span class="text-[10px] text-indigo-600 font-bold uppercase">{{ $article->category->name }}</span>
                                    @endif
                                </div>
                                <h3 class="font-bold text-slate-900 mb-2 group-hover:text-indigo-600 transition-colors line-clamp-2">{{ $article->title }}</h3>
                                <p class="text-slate-500 text-sm line-clamp-2 mb-4 flex-1">{{ $article->excerpt ?: Str::limit(strip_tags($article->body), 120) }}</p>
                                <div class="flex items-center gap-3 text-xs text-slate-400 mt-auto">
                                    <span>{{ $article->published_at?->format('d M Y') }}</span>
                                    <span>&middot;</span>
                                    <span>{{ $article->reading_time }} min</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full py-16 text-center border border-dashed border-slate-300 rounded-xl">
                            <i class="fa-solid fa-newspaper text-4xl text-slate-300 mb-3"></i>
                            <p class="text-slate-500 font-medium text-sm">Belum ada artikel yang dipublikasikan.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-8">{{ $articles->links() }}</div>
            </div>

            {{-- Sidebar --}}
            <aside class="w-full md:w-64 lg:w-72 shrink-0 space-y-6 order-1 md:order-2">
                {{-- Search --}}
                <form action="{{ route('blog.index') }}" method="GET" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari artikel..."
                        class="pl-10 pr-4 w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    <i class="fa-solid fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                </form>

                {{-- Categories --}}
                <div class="border border-slate-200 rounded-xl p-5 bg-white">
                    <h3 class="font-bold text-slate-800 text-sm mb-4">Kategori</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('blog.index') }}" class="text-sm font-medium {{ !isset($currentCategory) && !request('category') ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600' }} transition-colors">
                                Semua Artikel
                            </a>
                        </li>
                        @foreach($categories as $cat)
                            <li>
                                <a href="{{ route('blog.category', $cat->slug) }}" class="text-sm font-medium flex justify-between items-center {{ (isset($currentCategory) && $currentCategory->id == $cat->id) ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600' }} transition-colors">
                                    {{ $cat->name }}
                                    <span class="text-xs text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">{{ $cat->articles_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="bg-white border-t border-slate-200 py-8">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-medium text-slate-500">
            <p>&copy; {{ date('Y') }} {{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze Portal' }}. All rights reserved.</p>
            <p>Engineered by Bima Ryan.</p>
        </div>
    </footer>

    @include('components.hot-toast')
</body>
</html>
