<x-public-layout
    title="{{ isset($currentCategory) ? $currentCategory->name . ' - ' : '' }}Blog"
    description="{{ \App\Models\Setting::where('key', 'site_description')->value('value') ?? 'Artikel terbaru seputar teknologi, web development, dan tips hosting dari tim Ryaze.' }}"
    body-class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased"
    :links="[
        ['label' => 'Beranda', 'href' => url('/')],
        ['label' => 'Blog', 'href' => route('blog.index'), 'active' => true],
    ]"
    :footer-compact="true">

    @push('head')
        <meta name="robots" content="index, follow, max-image-preview:large">
        <meta property="og:site_name" content="{{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze' }}">

        {{-- JSON-LD: CollectionPage + Breadcrumb --}}
        <script type="application/ld+json" nonce="{{ csp_nonce() }}">
        {
            "@@context": "https://schema.org",
            "@type": "CollectionPage",
            "name": "{{ isset($currentCategory) ? $currentCategory->name : 'Blog' }} - {{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze' }}",
            "description": "{{ isset($currentCategory) && $currentCategory->description ? $currentCategory->description : 'Artikel terbaru seputar web development, tips hosting, dan teknologi dari tim Ryaze.' }}",
            "url": "{{ url()->current() }}",
            "inLanguage": "id-ID"
        }
        </script>
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
            }@if(isset($currentCategory)), {
                "@type": "ListItem",
                "position": 3,
                "name": "{{ $currentCategory->name }}",
                "item": "{{ url()->current() }}"
            }@endif]
        }
        </script>

        <style nonce="{{ csp_nonce() }}">
            body { font-family: 'Inter', sans-serif; }
        </style>
    @endpush

    {{-- Header --}}
    <section class="pt-28 pb-12 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mb-2">
                @if(isset($currentCategory))
                    {{ $currentCategory->name }}
                @else
                    Blog
                @endif
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">
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
                        <div class="border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden hover:border-indigo-300 transition-colors">
                            @if($featured->cover_image)
                                <div class="h-64 md:h-80 overflow-hidden bg-slate-100 dark:bg-slate-700/50">
                                    <img src="{{ Storage::url($featured->cover_image) }}" alt="{{ $featured->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                </div>
                            @endif
                            <div class="p-6 md:p-8 bg-white dark:bg-slate-800/60">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 text-[10px] font-bold uppercase rounded">Sorotan</span>
                                    @if($featured->category)
                                        <span class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">{{ $featured->category->name }}</span>
                                    @endif
                                </div>
                                <h2 class="text-2xl font-bold text-slate-900 dark:text-slate-50 mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 dark:group-hover:text-indigo-400 transition-colors">{{ $featured->title }}</h2>
                                <p class="text-slate-500 dark:text-slate-400 text-sm line-clamp-2 mb-4">{{ $featured->excerpt ?: Str::limit(strip_tags($featured->body), 200) }}</p>
                                <div class="flex items-center gap-4 text-xs text-slate-400 dark:text-slate-500">
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
                        <a href="{{ route('blog.show', $article->slug) }}" class="group border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden hover:border-indigo-300 transition-colors bg-white dark:bg-slate-800/60 flex flex-col">
                            @if($article->cover_image)
                                <div class="h-44 overflow-hidden bg-slate-100 dark:bg-slate-700/50">
                                    <img src="{{ Storage::url($article->cover_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>
                            @endif
                            <div class="p-5 flex flex-col flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    @if($article->category)
                                        <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-bold uppercase">{{ $article->category->name }}</span>
                                    @endif
                                </div>
                                <h3 class="font-bold text-slate-900 dark:text-slate-50 mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 dark:group-hover:text-indigo-400 transition-colors line-clamp-2">{{ $article->title }}</h3>
                                <p class="text-slate-500 dark:text-slate-400 text-sm line-clamp-2 mb-4 flex-1">{{ $article->excerpt ?: Str::limit(strip_tags($article->body), 120) }}</p>
                                <div class="flex items-center gap-3 text-xs text-slate-400 dark:text-slate-500 mt-auto">
                                    <span>{{ $article->published_at?->format('d M Y') }}</span>
                                    <span>&middot;</span>
                                    <span>{{ $article->reading_time }} min</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full py-16 text-center border border-dashed border-slate-300 dark:border-slate-600 rounded-xl">
                            <i class="fa-solid fa-newspaper text-4xl text-slate-300 dark:text-slate-400 mb-3"></i>
                            <p class="text-slate-500 dark:text-slate-400 font-medium text-sm">Belum ada artikel yang dipublikasikan.</p>
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
                        class="pl-10 pr-4 w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    <i class="fa-solid fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-sm"></i>
                </form>

                {{-- Categories --}}
                <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-5 bg-white dark:bg-slate-800/60">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm mb-4">Kategori</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('blog.index') }}" class="text-sm font-medium {{ !isset($currentCategory) && !request('category') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 dark:hover:text-indigo-400' }} transition-colors">
                                Semua Artikel
                            </a>
                        </li>
                        @foreach($categories as $cat)
                            <li>
                                <a href="{{ route('blog.category', $cat->slug) }}" class="text-sm font-medium flex justify-between items-center {{ (isset($currentCategory) && $currentCategory->id == $cat->id) ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 dark:hover:text-indigo-400' }} transition-colors">
                                    {{ $cat->name }}
                                    <span class="text-xs text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-700/50 px-2 py-0.5 rounded-full">{{ $cat->articles_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</x-public-layout>
