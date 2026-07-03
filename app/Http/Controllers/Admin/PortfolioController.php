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
        $query = Portfolio::latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('title', 'like', '%' . $search . '%');
        }

        $portfolios = $query->paginate(10)->withQueryString();
        return view('pages.admin.portfolios.index', compact('portfolios'));
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
