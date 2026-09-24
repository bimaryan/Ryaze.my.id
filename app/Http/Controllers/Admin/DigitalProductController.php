<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DigitalProduct;
use App\Models\DigitalPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class DigitalProductController extends Controller
{
    public function index(Request $request)
    {
        $query = DigitalProduct::latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        $products = $query->paginate(10)->withQueryString();
        return view('pages.admin.digital-products.index', compact('products'));
    }

    public function create()
    {
        return view('pages.admin.digital-products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:digital_products,slug',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:1000',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'file' => 'required|file|mimes:zip,rar,7z,pdf,xlsx,docx,json,csv,txt|max:51200',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['cover', 'file', 'is_active', 'slug']);
        $data['slug'] = $request->filled('slug')
            ? Str::slug($request->slug)
            : Str::slug($request->name) . '-' . Str::lower(Str::random(6));

        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('digital-products/covers', 'public');
        }

        $file = $request->file('file');
        $data['file_path'] = $file->store('digital-products/files', 'local');
        $data['file_name'] = $file->getClientOriginalName();

        $data['is_active'] = $request->has('is_active');

        DigitalProduct::create($data);

        return redirect()->route('superadmin.digital_products.index')->with('success', 'Produk digital berhasil ditambahkan.');
    }

    public function edit($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $product = DigitalProduct::findOrFail($decoded[0]);
        return view('pages.admin.digital-products.edit', compact('product'));
    }

    public function update(Request $request, $hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $product = DigitalProduct::findOrFail($decoded[0]);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:digital_products,slug,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'required|integer|min:1000',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'file' => 'nullable|file|mimes:zip,rar,7z,pdf,xlsx,docx,json,csv,txt|max:51200',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['cover', 'file', 'is_active', 'slug']);
        $data['slug'] = $request->filled('slug')
            ? Str::slug($request->slug)
            : $product->slug;

        if ($request->hasFile('cover')) {
            if ($product->cover_path) {
                Storage::disk('public')->delete($product->cover_path);
            }
            $data['cover_path'] = $request->file('cover')->store('digital-products/covers', 'public');
        }

        if ($request->hasFile('file')) {
            if ($product->file_path) {
                Storage::disk('local')->delete($product->file_path);
            }
            $file = $request->file('file');
            $data['file_path'] = $file->store('digital-products/files', 'local');
            $data['file_name'] = $file->getClientOriginalName();
        }

        $data['is_active'] = $request->has('is_active');

        $product->update($data);

        return redirect()->route('superadmin.digital_products.index')->with('success', 'Produk digital berhasil diperbarui.');
    }

    public function destroy($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $product = DigitalProduct::findOrFail($decoded[0]);

        if ($product->cover_path) {
            Storage::disk('public')->delete($product->cover_path);
        }
        if ($product->file_path) {
            Storage::disk('local')->delete($product->file_path);
        }

        $product->delete();

        return redirect()->route('superadmin.digital_products.index')->with('success', 'Produk digital berhasil dihapus.');
    }

    public function toggleStatus($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $product = DigitalProduct::findOrFail($decoded[0]);
        $product->update(['is_active' => !$product->is_active]);

        $msg = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Produk digital berhasil {$msg}.");
    }

    public function purchases(Request $request)
    {
        $query = DigitalPurchase::with('product')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $purchases = $query->paginate(15)->withQueryString();
        return view('pages.admin.digital-purchases.index', compact('purchases'));
    }
}
