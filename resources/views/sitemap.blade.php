{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach (['home', 'blog.index', 'updates.index', 'portfolio.index', 'consultation', 'whois.index', 'privacy', 'terms'] as $routeName)
    <url>
        <loc>{{ route($routeName) }}</loc>
    </url>
    @endforeach


    @foreach ($articles as $article)
    <url>
        <loc>{{ route('blog.show', $article->slug) }}</loc>
        <lastmod>{{ $article->updated_at->copy()->utc()->toAtomString() }}</lastmod>
    </url>
    @endforeach
    @foreach ($categories as $category)
    <url>
        <loc>{{ route('blog.category', $category->slug) }}</loc>
    </url>
    @endforeach
    @foreach ($updates as $update)
    <url>
        <loc>{{ route('updates.show', $update->slug) }}</loc>
        <lastmod>{{ $update->updated_at->copy()->utc()->toAtomString() }}</lastmod>
    </url>
    @endforeach
</urlset>
