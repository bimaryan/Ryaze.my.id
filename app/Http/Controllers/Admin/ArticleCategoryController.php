<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class ArticleCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ArticleCategory::withCount('articles')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->paginate(15)->withQueryString();
        return view('pages.admin.article-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('pages.admin.article-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:article_categories,name',
            'description' => 'nullable|string|max:500',
        ]);

        ArticleCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()->route('superadmin.article_categories.index')
            ->with('success', 'Kategori artikel berhasil ditambahkan.');
    }

    public function edit($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        
        $category = ArticleCategory::findOrFail($decoded[0]);
        return view('pages.admin.article-categories.edit', compact('category'));
    }

    public function update(Request $request, $hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        
        $category = ArticleCategory::findOrFail($decoded[0]);

        $request->validate([
            'name' => 'required|string|max:255|unique:article_categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()->route('superadmin.article_categories.index')
            ->with('success', 'Kategori artikel berhasil diperbarui.');
    }

    public function destroy($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        
        $category = ArticleCategory::findOrFail($decoded[0]);

        if ($category->articles()->count() > 0) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki artikel.');
        }

        $category->delete();

        return redirect()->route('superadmin.article_categories.index')
            ->with('success', 'Kategori artikel berhasil dihapus.');
    }
}
