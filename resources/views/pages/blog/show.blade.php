<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $siteFavicon = \App\Models\Setting::where('key', 'site_favicon')->value('value');
        $gaId = \App\Models\Setting::where('key', 'google_analytics_id')->value('value');
        $siteLogo = \App\Models\Setting::where('key', 'site_logo')->value('value');
        $siteName = \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze Portal';
    @endphp
    <title>{{ $article->seo_title }} - {{ $siteName }}</title>
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
    <meta name="description" content="{{ $article->seo_description }}">
    <meta name="author" content="{{ $article->user->name ?? 'Ryaze' }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ route('blog.show', $article->slug) }}">
    <meta property="og:title" content="{{ $article->seo_title }}">
    <meta property="og:description" content="{{ $article->seo_description }}">
    @if($article->cover_image)
        <meta property="og:image" content="{{ asset(Storage::url($article->cover_image)) }}">
    @endif
    <meta property="article:published_time" content="{{ $article->published_at?->toIso8601String() }}">
    <meta property="article:author" content="{{ $article->user->name ?? 'Ryaze' }}">
    @if($article->category)
        <meta property="article:section" content="{{ $article->category->name }}">
    @endif
    @if(is_array($article->tags))
        @foreach($article->tags as $tag)
            <meta property="article:tag" content="{{ $tag }}">
        @endforeach
    @elseif(is_string($article->tags))
        <meta property="article:tag" content="{{ $article->tags }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" nonce="{{ app('csp_nonce') ?? '' }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" nonce="{{ app('csp_nonce') ?? '' }}">
    <style nonce="{{ app('csp_nonce') ?? '' }}">
        body { font-family: 'Inter', sans-serif; }

        /* Prose-like styling for article body */
        .article-body { line-height: 1.8; color: #374151; }
        .article-body h1, .article-body h2, .article-body h3, .article-body h4 { color: #111827; font-weight: 700; margin-top: 2em; margin-bottom: 0.75em; }
        .article-body h2 { font-size: 1.5em; }
        .article-body h3 { font-size: 1.25em; }
        .article-body p { margin-bottom: 1.25em; }
        .article-body a { color: #4f46e5; text-decoration: underline; }
        .article-body a:hover { color: #3730a3; }
        .article-body ul, .article-body ol { padding-left: 1.5em; margin-bottom: 1.25em; }
        .article-body li { margin-bottom: 0.5em; }
        .article-body blockquote { border-left: 4px solid #e2e8f0; padding: 1em 1.5em; margin: 1.5em 0; color: #64748b; background: #f8fafc; border-radius: 0 8px 8px 0; }
        .article-body pre { background: #1e293b; color: #e2e8f0; padding: 1.25em; border-radius: 8px; overflow-x: auto; margin-bottom: 1.5em; font-size: 0.875em; }
        .article-body code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 0.875em; }
        .article-body pre code { background: none; padding: 0; }
        .article-body img { max-width: 100%; height: auto; border-radius: 8px; margin: 1.5em 0; }
        .article-body table { width: 100%; border-collapse: collapse; margin-bottom: 1.5em; }
        .article-body th, .article-body td { border: 1px solid #e2e8f0; padding: 0.75em 1em; text-align: left; }
        .article-body th { background: #f8fafc; font-weight: 600; }
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
                        <div class="bg-indigo-600 text-white rounded-md w-8 h-8 flex items-center justify-center"><i class="fa-solid fa-code text-sm"></i></div>
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

    {{-- Cover Image --}}
    @if($article->cover_image)
        <div class="pt-16 bg-slate-100">
            <div class="max-w-5xl mx-auto">
                <img src="{{ Storage::url($article->cover_image) }}" alt="{{ $article->title }}" class="w-full max-h-[480px] object-cover">
            </div>
        </div>
    @endif

    {{-- Article Content --}}
    <article class="max-w-3xl mx-auto px-6 lg:px-8 {{ $article->cover_image ? 'pt-12' : 'pt-28' }} pb-16">
        {{-- Breadcrumb --}}
        <nav class="mb-8 text-xs text-slate-400 font-medium">
            <a href="{{ route('blog.index') }}" class="hover:text-indigo-600 transition-colors">Blog</a>
            @if($article->category)
                <span class="mx-1.5">/</span>
                <a href="{{ route('blog.category', $article->category->slug) }}" class="hover:text-indigo-600 transition-colors">{{ $article->category->name }}</a>
            @endif
            <span class="mx-1.5">/</span>
            <span class="text-slate-600">{{ Str::limit($article->title, 40) }}</span>
        </nav>

        {{-- Title --}}
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 mb-4 leading-tight">{{ $article->title }}</h1>

        {{-- Meta --}}
        <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500 mb-8 pb-8 border-b border-slate-200">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">
                    {{ strtoupper(substr($article->user->name ?? 'A', 0, 1)) }}
                </div>
                <span class="font-medium text-slate-700">{{ $article->user->name ?? 'Admin' }}</span>
            </div>
            <span class="text-slate-300">|</span>
            <span><i class="fa-regular fa-calendar mr-1"></i>{{ $article->published_at?->format('d M Y') }}</span>
            <span class="text-slate-300">|</span>
            <span><i class="fa-regular fa-clock mr-1"></i>{{ $article->reading_time }} menit baca</span>
            <span class="text-slate-300">|</span>
            <span><i class="fa-regular fa-eye mr-1"></i>{{ number_format($article->views_count) }}x dilihat</span>
        </div>

        {{-- Body --}}
        <div class="article-body text-base">
            {!! $article->body !!}
        </div>

        {{-- Tags --}}
        @if(is_array($article->tags) && count($article->tags) > 0)
            <div class="mt-12 pt-8 border-t border-slate-200">
                <div class="flex flex-wrap gap-2">
                    @foreach($article->tags as $tag)
                        <span class="px-3 py-1 bg-slate-100 border border-slate-200 text-slate-600 text-xs font-semibold rounded-full">#{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
        @elseif(is_string($article->tags) && !empty($article->tags))
            <div class="mt-12 pt-8 border-t border-slate-200">
                <div class="flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-slate-100 border border-slate-200 text-slate-600 text-xs font-semibold rounded-full">#{{ $article->tags }}</span>
                </div>
            </div>
        @endif
    </article>

    {{-- Related Articles --}}
    @if($related->count() > 0)
        <section class="bg-slate-50 border-t border-slate-200 py-16">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <h2 class="text-xl font-bold text-slate-900 mb-8">Artikel Lainnya</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($related as $rel)
                        <a href="{{ route('blog.show', $rel->slug) }}" class="group border border-slate-200 rounded-xl overflow-hidden hover:border-indigo-300 transition-colors bg-white flex flex-col">
                            @if($rel->cover_image)
                                <div class="h-40 overflow-hidden bg-slate-100">
                                    <img src="{{ Storage::url($rel->cover_image) }}" alt="{{ $rel->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>
                            @endif
                            <div class="p-5 flex-1 flex flex-col">
                                @if($rel->category)
                                    <span class="text-[10px] text-indigo-600 font-bold uppercase mb-1">{{ $rel->category->name }}</span>
                                @endif
                                <h3 class="font-bold text-slate-900 mb-2 group-hover:text-indigo-600 transition-colors line-clamp-2 text-sm">{{ $rel->title }}</h3>
                                <div class="flex items-center gap-2 text-xs text-slate-400 mt-auto">
                                    <span>{{ $rel->published_at?->format('d M Y') }}</span>
                                    <span>&middot;</span>
                                    <span>{{ $rel->reading_time }} min</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

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
