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
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Article::with(['user', 'category'])->latest();

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('category')) {
                $decoded = Hashids::decode($request->category);
                if (!empty($decoded)) {
                    $query->where('category_id', $decoded[0]);
                }
            }

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('title_html', function($article) {
                    $img = $article->image_path 
                        ? '<img src="' . Storage::url($article->image_path) . '" alt="' . $article->title . '" class="w-12 h-12 object-cover rounded-lg border border-slate-200">' 
                        : '<div class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-400 border border-indigo-100"><i class="fa-solid fa-image"></i></div>';
                    
                    $category = $article->category ? '<a href="'.route('superadmin.articles.index', ['category' => $article->category->hashid]).'" class="text-[10px] text-indigo-500 hover:underline mt-0.5 font-bold"><i class="fa-solid fa-folder mr-1"></i>'.$article->category->name.'</a>' : '<span class="text-[10px] text-slate-400 mt-0.5"><i class="fa-solid fa-folder-open mr-1"></i>Tanpa Kategori</span>';

                    return '<div class="flex items-center gap-3">' . $img . '<div class="flex flex-col min-w-0"><span class="font-medium text-slate-800 truncate max-w-[250px]">' . $article->title . '</span>' . $category . '</div></div>';
                })
                ->addColumn('author', function($article) {
                    return '<div class="flex items-center gap-2"><div class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-[10px] uppercase border border-slate-200">'.substr($article->user->name, 0, 1).'</div><span class="text-xs font-medium text-slate-700">'.$article->user->name.'</span></div>';
                })
                ->addColumn('status_html', function($article) {
                    if ($article->status === 'published') {
                        return '<span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase rounded"><i class="fa-solid fa-check-circle mr-1"></i>Published</span>';
                    } elseif ($article->status === 'draft') {
                        return '<span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold uppercase rounded"><i class="fa-solid fa-file-lines mr-1"></i>Draft</span>';
                    } else {
                        return '<span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-bold uppercase rounded"><i class="fa-solid fa-box-archive mr-1"></i>Archived</span>';
                    }
                })
                ->addColumn('views', function($article) {
                    return '<span class="text-xs text-slate-600 font-medium"><i class="fa-regular fa-eye text-slate-400 mr-1"></i>' . number_format($article->views) . '</span>';
                })
                ->addColumn('created_at_formatted', function($article) {
                    return '<div class="text-xs text-slate-600"><div class="font-medium">'.\Carbon\Carbon::parse($article->created_at)->translatedFormat('d M Y').'</div><div class="text-slate-400">'.\Carbon\Carbon::parse($article->created_at)->translatedFormat('H:i').'</div></div>';
                })
                ->addColumn('action', function($article) {
                    $editRoute = route('superadmin.articles.edit', $article->hashid);
                    $deleteRoute = route('superadmin.articles.destroy', $article->hashid);
                    $csrf = csrf_field();
                    $methodDelete = method_field('DELETE');

                    return '
                        <div class="flex items-center justify-center gap-2">
                            <a href="'.$editRoute.'" class="p-1.5 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition" title="Edit Artikel">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="'.$deleteRoute.'" method="POST" class="inline">
                                '.$csrf.' '.$methodDelete.'
                                <button type="button" onclick="confirmDelete(this)" class="p-1.5 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition" title="Hapus Artikel">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['title_html', 'author', 'status_html', 'views', 'created_at_formatted', 'action'])
                ->make(true);
        }

        $categories = ArticleCategory::orderBy('name')->get();
        return view('pages.admin.articles.index', compact('categories'));
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
