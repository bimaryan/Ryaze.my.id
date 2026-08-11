<x-public-layout
    title="{{ $article->seo_title }}"
    description="{{ $article->seo_description }}"
    body-class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased"
    og-image="{{ $article->cover_image ? asset(Storage::url($article->cover_image)) : '' }}"
    :links="[
        ['label' => 'Beranda', 'href' => url('/')],
        ['label' => 'Blog', 'href' => route('blog.index')],
    ]"
    :footer-compact="true">

    @push('head')
        <meta name="author" content="{{ $article->user->name ?? 'Ryaze' }}">

        <meta property="og:type" content="article">
        <meta property="og:url" content="{{ route('blog.show', $article->slug) }}">
        <meta property="article:published_time" content="{{ $article->published_at?->toIso8601String() }}">
        <meta property="article:author" content="{{ $article->user->name ?? 'Ryaze' }}">
        @if ($article->category)
            <meta property="article:section" content="{{ $article->category->name }}">
        @endif
        @if (is_array($article->tags))
            @foreach ($article->tags as $tag)
                <meta property="article:tag" content="{{ $tag }}">
            @endforeach
        @elseif (is_string($article->tags))
            <meta property="article:tag" content="{{ $article->tags }}">
        @endif

        {{-- JSON-LD Structured Data for Google --}}
        <script type="application/ld+json" nonce="{{ csp_nonce() }}">
        {
            "@@context": "https://schema.org",
            "@type": "Article",
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "{{ route('blog.show', $article->slug) }}"
            },
            "headline": "{{ $article->title }}",
            "description": "{{ $article->seo_description ?: ($article->excerpt ?: Str::limit(strip_tags($article->body), 160)) }}",
            "image": [
                "{{ $article->cover_image ? url(Storage::url($article->cover_image)) : '' }}"
            ],
            "datePublished": "{{ $article->published_at?->toIso8601String() }}",
            "dateModified": "{{ $article->updated_at?->toIso8601String() }}",
            "author": [{
                "@type": "Person",
                "name": "{{ $article->user->name ?? 'Ryaze' }}"
            }],
            "publisher": {
                "@type": "Organization",
                "name": "{{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze' }}",
                "logo": {
                    "@type": "ImageObject",
                    "url": "{{ url('/og-image.png') }}",
                    "width": 1200,
                    "height": 630
                }
            },
            "inLanguage": "id-ID"
        }
        </script>

        {{-- JSON-LD: BreadcrumbList --}}
        <script type="application/ld+json" nonce="{{ csp_nonce() }}">
        {
            "@@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [{
                "@type": "ListItem",
                "position": 1,
                "name": "Beranda",
                "item": "{{ url('/') }}"
            }, {
                "@type": "ListItem",
                "position": 2,
                "name": "Blog",
                "item": "{{ route('blog.index') }}"
            }@if($article->category), {
                "@type": "ListItem",
                "position": 3,
                "name": "{{ $article->category->name }}",
                "item": "{{ route('blog.category', $article->category->slug) }}"
            }@endif, {
                "@type": "ListItem",
                "position": {{ $article->category ? 4 : 3 }},
                "name": "{{ $article->title }}",
                "item": "{{ route('blog.show', $article->slug) }}"
            }]
        }
        </script>

        <style nonce="{{ csp_nonce() }}">
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
    @endpush

    {{-- Cover Image --}}
    @if($article->cover_image)
        <div class="pt-16 bg-slate-100 dark:bg-slate-700/50">
            <div class="max-w-5xl mx-auto">
                <img src="{{ Storage::url($article->cover_image) }}" alt="{{ $article->title }}" class="w-full max-h-[480px] object-cover">
            </div>
        </div>
    @endif

    {{-- Article Content --}}
    <article class="max-w-3xl mx-auto px-6 lg:px-8 {{ $article->cover_image ? 'pt-12' : 'pt-28' }} pb-16">
        {{-- Breadcrumb --}}
        <nav class="mb-8 text-xs text-slate-400 dark:text-slate-500 font-medium" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-1.5">
                <li><a href="{{ url('/') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 dark:hover:text-indigo-400 transition-colors">Beranda</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('blog.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 dark:hover:text-indigo-400 transition-colors">Blog</a></li>
                @if($article->category)
                    <li aria-hidden="true">/</li>
                    <li><a href="{{ route('blog.category', $article->category->slug) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 dark:hover:text-indigo-400 transition-colors">{{ $article->category->name }}</a></li>
                @endif
                <li aria-hidden="true">/</li>
                <li class="text-slate-600 dark:text-slate-300" aria-current="page">{{ Str::limit($article->title, 40) }}</li>
            </ol>
        </nav>

        {{-- Title --}}
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50 mb-4 leading-tight">{{ $article->title }}</h1>

        {{-- Meta --}}
        <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500 dark:text-slate-400 mb-8 pb-8 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs font-bold">
                    {{ strtoupper(substr($article->user->name ?? 'A', 0, 1)) }}
                </div>
                <span class="font-medium text-slate-700 dark:text-slate-200">{{ $article->user->name ?? 'Admin' }}</span>
            </div>
            <span class="text-slate-300 dark:text-slate-400">|</span>
            <span><i class="fa-regular fa-calendar mr-1"></i>{{ $article->published_at?->format('d M Y') }}</span>
            <span class="text-slate-300 dark:text-slate-400">|</span>
            <span><i class="fa-regular fa-clock mr-1"></i>{{ $article->reading_time }} menit baca</span>
            <span class="text-slate-300 dark:text-slate-400">|</span>
            <span><i class="fa-regular fa-eye mr-1"></i>{{ number_format($article->views_count) }}x dilihat</span>
        </div>

        {{-- Body --}}
        <div class="article-body text-base">
            {!! $article->body !!}
        </div>

        {{-- Tags --}}
        @if(is_array($article->tags) && count($article->tags) > 0)
            <div class="mt-12 pt-8 border-t border-slate-200 dark:border-slate-700">
                <div class="flex flex-wrap gap-2">
                    @foreach($article->tags as $tag)
                        <span class="px-3 py-1 bg-slate-100 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold rounded-full">#{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
        @elseif(is_string($article->tags) && !empty($article->tags))
            <div class="mt-12 pt-8 border-t border-slate-200 dark:border-slate-700">
                <div class="flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-slate-100 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold rounded-full">#{{ $article->tags }}</span>
                </div>
            </div>
        @endif
    </article>

    {{-- Related Articles --}}
    @if($related->count() > 0)
        <section class="bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-700 py-16">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <h2 class="text-xl font-bold text-slate-900 dark:text-slate-50 mb-8">Artikel Lainnya</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($related as $rel)
                        <a href="{{ route('blog.show', $rel->slug) }}" class="group border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden hover:border-indigo-300 transition-colors bg-white dark:bg-slate-800/60 flex flex-col">
                            @if($rel->cover_image)
                                <div class="h-40 overflow-hidden bg-slate-100 dark:bg-slate-700/50">
                                    <img src="{{ Storage::url($rel->cover_image) }}" alt="{{ $rel->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>
                            @endif
                            <div class="p-5 flex-1 flex flex-col">
                                @if($rel->category)
                                    <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-bold uppercase mb-1">{{ $rel->category->name }}</span>
                                @endif
                                <h3 class="font-bold text-slate-900 dark:text-slate-50 mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 dark:group-hover:text-indigo-400 transition-colors line-clamp-2 text-sm">{{ $rel->title }}</h3>
                                <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 mt-auto">
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
</x-public-layout>
