<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ArticleImport;
use App\Jobs\GenerateAiBlogArticle;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with(['user', 'category'])->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $decoded = Hashids::decode($request->category);
            if (!empty($decoded)) {
                $query->where('category_id', $decoded[0]);
            }
        }

        $articles = $query->paginate(15)->withQueryString();
        $categories = ArticleCategory::orderBy('name')->get();

        return view('pages.admin.articles.index', compact('articles', 'categories'));
    }

    public function create()
    {
        $categories = ArticleCategory::orderBy('name')->get();
        return view('pages.admin.articles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:article_categories,id',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'tags' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
        ]);

        $data = $request->except(['cover_image', 'tags']);
        $data['user_id'] = Auth::id();
        $data['is_featured'] = $request->has('is_featured');

        // Generate unique slug
        $slug = Str::slug($request->title);
        $originalSlug = $slug;
        $count = 1;
        while (Article::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }
        $data['slug'] = $slug;

        // Tags
        if ($request->filled('tags')) {
            $data['tags'] = array_map('trim', explode(',', $request->tags));
        }

        // Cover image
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('articles', 'public');
        }

        // Published at
        if ($request->status === 'published') {
            $data['published_at'] = now();
        }

        Article::create($data);

        return redirect()->route('superadmin.articles.index')
            ->with('success', 'Artikel berhasil dibuat.');
    }

    /** Queue generation so an admin request is never held open by the AI APIs. */
    public function generateAi(Request $request)
    {
        $data = $request->validate([
            'topic' => 'required|string|min:5|max:500',
            'category_id' => 'nullable|exists:article_categories,id',
            'publish' => 'nullable|boolean',
        ]);

        GenerateAiBlogArticle::dispatch(
            topic: $data['topic'],
            authorId: Auth::id(),
            categoryId: $data['category_id'] ?? null,
            publish: $request->boolean('publish'),
        );

        $message = $request->boolean('publish')
            ? 'Artikel AI beserta gambar sampul telah masuk antrean untuk dipublikasikan.'
            : 'Artikel AI beserta gambar sampul telah masuk antrean sebagai draft untuk direview.';

        return redirect()->route('superadmin.articles.index')->with('success', $message);
    }

    public function edit($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        
        $article = Article::findOrFail($decoded[0]);
        $categories = ArticleCategory::orderBy('name')->get();

        return view('pages.admin.articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, $hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        
        $article = Article::findOrFail($decoded[0]);

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:article_categories,id',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'tags' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
        ]);

        $data = $request->except(['cover_image', 'tags']);
        $data['is_featured'] = $request->has('is_featured');

        // Tags
        if ($request->filled('tags')) {
            $data['tags'] = array_map('trim', explode(',', $request->tags));
        } else {
            $data['tags'] = null;
        }

        // Cover image
        if ($request->hasFile('cover_image')) {
            if ($article->cover_image) {
                Storage::disk('public')->delete($article->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('articles', 'public');
        }

        // Published at
        if ($request->status === 'published' && $article->status !== 'published') {
            $data['published_at'] = now();
        }

        $article->update($data);

        return redirect()->route('superadmin.articles.index')
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        
        $article = Article::findOrFail($decoded[0]);

        if ($article->cover_image) {
            Storage::disk('public')->delete($article->cover_image);
        }

        $article->delete();

        return redirect()->route('superadmin.articles.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }

    public function toggleFeatured($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        
        $article = Article::findOrFail($decoded[0]);
        $article->update(['is_featured' => !$article->is_featured]);

        $msg = $article->is_featured ? 'ditandai sebagai sorotan' : 'dihapus dari sorotan';
        return back()->with('success', "Artikel berhasil {$msg}.");
    }

    public function toggleStatus($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        
        $article = Article::findOrFail($decoded[0]);

        if ($article->status === 'published') {
            $article->update(['status' => 'draft']);
            $msg = 'dikembalikan ke draft';
        } else {
            $article->update([
                'status' => 'published',
                'published_at' => $article->published_at ?? now(),
            ]);
            $msg = 'dipublikasikan';
        }

        return back()->with('success', "Artikel berhasil {$msg}.");
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('articles/body', 'public');
            return response()->json([
                'success' => true,
                'url' => Storage::url($path)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengunggah gambar'
        ], 400);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls|max:5120',
        ]);

        try {
            Excel::import(new ArticleImport, $request->file('file'));
            return redirect()->route('superadmin.articles.index')->with('success', 'Artikel berhasil diimpor!');
        } catch (\Exception $e) {
            return redirect()->route('superadmin.articles.index')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=template_import_artikel.csv',
            'Expires'             => '0',
            'Pragma'              => 'public'
        ];

        $columns = ['title', 'category', 'excerpt', 'body', 'tags', 'status'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            // Contoh data
            fputcsv($file, [
                'Contoh Judul Artikel Pertama',
                'Tutorial',
                'Ini adalah ringkasan singkat artikel.',
                '<p>Isi konten menggunakan tag HTML. Anda bisa <strong>menebalkan teks</strong> atau membuat list.</p>',
                'tips, laravel, web',
                'published'
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
