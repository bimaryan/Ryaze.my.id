<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Vinkla\Hashids\Facades\Hashids;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $query = Testimonial::orderBy('sort_order');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('company', 'like', '%' . $search . '%')
                  ->orWhere('role', 'like', '%' . $search . '%');
            });
        }

        $testimonials = $query->paginate(10)->withQueryString();
        return view('pages.admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('pages.admin.testimonials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['avatar', 'is_active', 'sort_order']);

        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('testimonials', 'public');
        }

        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->input('sort_order', 0);

        Testimonial::create($data);

        return redirect()->route('superadmin.testimonials.index')->with('success', 'Testimoni berhasil ditambahkan.');
    }

    public function edit($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $testimonial = Testimonial::findOrFail($decoded[0]);
        return view('pages.admin.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, $hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $testimonial = Testimonial::findOrFail($decoded[0]);

        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['avatar', 'is_active', 'sort_order']);

        if ($request->hasFile('avatar')) {
            if ($testimonial->avatar_path) {
                Storage::disk('public')->delete($testimonial->avatar_path);
            }
            $data['avatar_path'] = $request->file('avatar')->store('testimonials', 'public');
        }

        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->input('sort_order', 0);

        $testimonial->update($data);

        return redirect()->route('superadmin.testimonials.index')->with('success', 'Testimoni berhasil diperbarui.');
    }

    public function destroy($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $testimonial = Testimonial::findOrFail($decoded[0]);

        if ($testimonial->avatar_path) {
            Storage::disk('public')->delete($testimonial->avatar_path);
        }

        $testimonial->delete();

        return redirect()->route('superadmin.testimonials.index')->with('success', 'Testimoni berhasil dihapus.');
    }

    public function toggleStatus($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $testimonial = Testimonial::findOrFail($decoded[0]);
        $testimonial->update(['is_active' => !$testimonial->is_active]);

        $msg = $testimonial->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Testimoni berhasil {$msg}.");
    }
}
