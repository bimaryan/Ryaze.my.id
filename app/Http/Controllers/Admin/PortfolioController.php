<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Vinkla\Hashids\Facades\Hashids;

class PortfolioController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Portfolio::latest();

            if ($request->has('status') && $request->status != '') {
                $query->where('is_active', $request->status);
            }

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('title_html', function($portfolio) {
                    $img = $portfolio->image_path 
                        ? '<img src="' . Storage::url($portfolio->image_path) . '" alt="' . $portfolio->title . '" class="w-12 h-12 object-cover rounded-lg border border-slate-200">' 
                        : '<div class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-400 border border-indigo-100"><i class="fa-solid fa-image"></i></div>';
                    
                    $link = $portfolio->link_preview ? '<a href="' . $portfolio->link_preview . '" target="_blank" class="text-[10px] text-indigo-500 hover:underline mt-0.5 font-bold"><i class="fa-solid fa-link mr-1"></i>Live Preview</a>' : '';

                    return '<div class="flex items-center gap-3">' . $img . '<div class="flex flex-col min-w-0"><span class="font-medium text-slate-800 truncate max-w-[250px]">' . $portfolio->title . '</span>' . $link . '</div></div>';
                })
                ->addColumn('tags_html', function($portfolio) {
                    if ($portfolio->tags) {
                        $tagsHtml = '<div class="flex flex-wrap gap-1">';
                        foreach($portfolio->tags as $tag) {
                            $tagsHtml .= '<span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-600 border border-slate-200">' . $tag . '</span>';
                        }
                        $tagsHtml .= '</div>';
                        return $tagsHtml;
                    }
                    return '<span class="text-slate-400 italic text-xs">Tanpa tag</span>';
                })
                ->addColumn('status_html', function($portfolio) {
                    return $portfolio->is_active 
                        ? '<span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase rounded">Aktif</span>' 
                        : '<span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold uppercase rounded">Draft</span>';
                })
                ->addColumn('created_at_formatted', function($portfolio) {
                    return \Carbon\Carbon::parse($portfolio->created_at)->translatedFormat('d M Y');
                })
                ->addColumn('action', function($portfolio) {
                    $toggleRoute = route('superadmin.portfolios.status.toggle', $portfolio->hashid);
                    $editRoute = route('superadmin.portfolios.edit', $portfolio->hashid);
                    $deleteRoute = route('superadmin.portfolios.destroy', $portfolio->hashid);
                    $csrf = csrf_field();
                    $methodPatch = method_field('PATCH');
                    $methodDelete = method_field('DELETE');

                    $toggleBtnClass = $portfolio->is_active ? 'text-emerald-500 bg-emerald-50 hover:bg-emerald-100' : 'text-slate-400 hover:text-emerald-500 hover:bg-emerald-50';
                    $toggleIcon = $portfolio->is_active ? 'fa-eye' : 'fa-eye-slash';
                    $toggleTitle = $portfolio->is_active ? 'Jadikan Draft' : 'Aktifkan';

                    return '
                        <div class="flex items-center justify-center gap-2">
                            <form action="'.$toggleRoute.'" method="POST" class="inline">
                                '.$csrf.' '.$methodPatch.'
                                <button type="submit" title="'.$toggleTitle.'" class="p-1.5 rounded-lg transition '.$toggleBtnClass.'">
                                    <i class="fa-solid '.$toggleIcon.'"></i>
                                </button>
                            </form>
                            <a href="'.$editRoute.'" class="p-1.5 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="'.$deleteRoute.'" method="POST" class="inline">
                                '.$csrf.' '.$methodDelete.'
                                <button type="button" onclick="confirmDelete(this)" class="p-1.5 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['title_html', 'tags_html', 'status_html', 'action'])
                ->make(true);
        }

        return view('pages.admin.portfolios.index');
    }

    public function create()
    {
        return view('pages.admin.portfolios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'tags' => 'nullable|string', // Comma separated
            'link_preview' => 'nullable|url',
            'link_github' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['image', 'tags']);
        
        if ($request->filled('tags')) {
            $tagsArray = array_map('trim', explode(',', $request->tags));
            $data['tags'] = $tagsArray;
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('portfolios', 'public');
            $data['image_path'] = $path;
        }

        $data['is_active'] = $request->has('is_active');

        Portfolio::create($data);

        return redirect()->route('superadmin.portfolios.index')->with('success', 'Portofolio berhasil ditambahkan.');
    }

    public function edit($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        
        $portfolio = Portfolio::findOrFail($decoded[0]);
        return view('pages.admin.portfolios.edit', compact('portfolio'));
    }

    public function update(Request $request, $hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        
        $portfolio = Portfolio::findOrFail($decoded[0]);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'tags' => 'nullable|string',
            'link_preview' => 'nullable|url',
            'link_github' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['image', 'tags']);
        
        if ($request->filled('tags')) {
            $tagsArray = array_map('trim', explode(',', $request->tags));
            $data['tags'] = $tagsArray;
        } else {
            $data['tags'] = null;
        }

        if ($request->hasFile('image')) {
            if ($portfolio->image_path) {
                Storage::disk('public')->delete($portfolio->image_path);
            }
            $path = $request->file('image')->store('portfolios', 'public');
            $data['image_path'] = $path;
        }

        $data['is_active'] = $request->has('is_active');

        $portfolio->update($data);

        return redirect()->route('superadmin.portfolios.index')->with('success', 'Portofolio berhasil diperbarui.');
    }

    public function destroy($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        
        $portfolio = Portfolio::findOrFail($decoded[0]);

        if ($portfolio->image_path) {
            Storage::disk('public')->delete($portfolio->image_path);
        }

        $portfolio->delete();

        return redirect()->route('superadmin.portfolios.index')->with('success', 'Portofolio berhasil dihapus.');
    }

    public function toggleStatus($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        
        $portfolio = Portfolio::findOrFail($decoded[0]);
        $portfolio->update(['is_active' => !$portfolio->is_active]);

        $msg = $portfolio->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Portofolio berhasil {$msg}.");
    }
}
