<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::published()->with(['user', 'category'])->latest('published_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

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
        }])->having('articles_count', '>', 0)->orderBy('name')->get();

        return view('pages.blog.index', compact('articles', 'categories', 'featured'));
    }

    public function show($slug)
    {
        $article = Article::published()
            ->with(['user', 'category'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment view count
        $article->increment('views_count');

        // Related articles (same category, exclude current)
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

        // Fallback: if not enough related, fill with latest
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

        return view('pages.blog.show', compact('article', 'related'));
    }

    public function category($slug)
    {
        $category = ArticleCategory::where('slug', $slug)->firstOrFail();

        $articles = Article::published()
            ->with(['user', 'category'])
            ->where('category_id', $category->id)
            ->latest('published_at')
            ->paginate(9);

        $categories = ArticleCategory::withCount(['articles' => function ($q) {
            $q->published();
        }])->having('articles_count', '>', 0)->orderBy('name')->get();

        return view('pages.blog.index', [
            'articles' => $articles,
            'categories' => $categories,
            'featured' => null,
            'currentCategory' => $category,
        ]);
    }
}
