<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::published()->with(['user', 'category'])->latest('published_at');

        $isSearch = $request->filled('search');
        if ($isSearch) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $category = null;
        if ($request->filled('category')) {
            $category = ArticleCategory::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        $featured = Article::published()->featured()->with(['user', 'category'])->latest('published_at')->first();
        $articles = $query->paginate(9)->withQueryString();
        $categories = ArticleCategory::withCount(['articles' => function ($q) {
            $q->published();
        }])->get()->reject(fn ($c) => $c->articles_count < 1)->sortBy('name')->values();

        // Hasil pencarian internal di-noindex agar tidak membanjiri indeks
        // dengan URL kombinatorial (panduan Google: batasi URL tanpa batas).
        $canonical = $category ? route('blog.category', $category->slug) : route('blog.index');
        if (!$isSearch && $articles->currentPage() > 1) {
            $canonical .= '?page=' . $articles->currentPage();
        }

        return Inertia::render('Blog/Index', [
            'articles' => $articles,
            'categories' => $categories,
            'featured' => $featured,
            'currentCategory' => $category,
            'seoTitle' => ($category ? $category->name . ' - Blog' : 'Artikel & Insights - Blog') . ' | Ryaze',
            'seoDescription' => $category->description ?? 'Tutorial, tips, dan berita seputar web development, hosting, dan teknologi terbaru.',
            'canonical' => $canonical,
            'robots' => $isSearch ? 'noindex, follow' : 'index, follow',
        ]);
    }

    public function show($slug)
    {
        $article = Article::published()
            ->with(['user', 'category'])
            ->where('slug', $slug)
            ->firstOrFail();

        $article->increment('views_count');

        $related = collect();
        if ($article->category_id) {
            $related = Article::published()
                ->with(['user', 'category'])
                ->where('category_id', $article->category_id)
                ->where('id', '!=', $article->id)
                ->latest('published_at')
                ->take(3)
                ->get();
        }

        if ($related->count() < 3) {
            $excludeIds = $related->pluck('id')->push($article->id);
            $remaining = Article::published()
                ->with(['user', 'category'])
                ->whereNotIn('id', $excludeIds)
                ->latest('published_at')
                ->take(3 - $related->count())
                ->get();
            $related = $related->merge($remaining);
        }

        return Inertia::render('Blog/Show', [
            'article' => $article,
            'related' => $related,
            'popular' => Article::published()
                ->with(['user', 'category'])
                ->where('id', '!=', $article->id)
                ->orderByDesc('views_count')
                ->take(5)
                ->get(),
            'categories' => ArticleCategory::withCount(['articles' => function ($q) {
                $q->published();
            }])->get()->reject(fn ($c) => $c->articles_count < 1)->sortBy('name')->values(),
            'latestUpdates' => \App\Models\InfoUpdate::published()
                ->latest('published_at')
                ->take(3)
                ->get(),
            'seoTitle' => ($article->seo_title ?: $article->title) . ' | Ryaze News',
            'seoDescription' => Str::limit($article->seo_description ?: $article->excerpt ?: '', 160),
            'canonical' => route('blog.show', $article->slug),
            'ogType' => 'article',
            'ogImage' => $article->cover_image ? asset('storage/' . $article->cover_image) : asset('og-image.png'),
        ]);
    }

    public function category(Request $request, $slug)
    {
        $category = ArticleCategory::where('slug', $slug)->firstOrFail();

        $articles = Article::published()
            ->with(['user', 'category'])
            ->where('category_id', $category->id)
            ->latest('published_at')
            ->paginate(9);

        $categories = ArticleCategory::withCount(['articles' => function ($q) {
            $q->published();
        }])->get()->reject(fn ($c) => $c->articles_count < 1)->sortBy('name')->values();

        $canonical = route('blog.category', $category->slug);
        if ($articles->currentPage() > 1) {
            $canonical .= '?page=' . $articles->currentPage();
        }

        return Inertia::render('Blog/Index', [
            'articles' => $articles,
            'categories' => $categories,
            'featured' => null,
            'currentCategory' => $category,
            'seoTitle' => $category->name . ' - Blog | Ryaze',
            'seoDescription' => $category->description ?? 'Kumpulan artikel kategori ' . $category->name . ' dari blog Ryaze: tutorial, tips, dan berita teknologi.',
            'canonical' => $canonical,
            'robots' => 'index, follow',
        ]);
    }
}
