<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class EducationController extends Controller
{
    public function index(Request $request)
    {
        $query = Education::orderBy('sort_order');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('degree', 'like', '%' . $search . '%')
                  ->orWhere('institution', 'like', '%' . $search . '%');
            });
        }

        $educations = $query->paginate(10)->withQueryString();
        return view('pages.admin.educations.index', compact('educations'));
    }

    public function create()
    {
        return view('pages.admin.educations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'period' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'tags' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['tags']);
        $data['tags'] = $request->filled('tags') ? array_map('trim', explode(',', $request->tags)) : null;
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->input('sort_order', 0);

        Education::create($data);

        return redirect()->route('superadmin.educations.index')->with('success', 'Pendidikan berhasil ditambahkan.');
    }

    public function edit($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $education = Education::findOrFail($decoded[0]);
        return view('pages.admin.educations.edit', compact('education'));
    }

    public function update(Request $request, $hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $education = Education::findOrFail($decoded[0]);

        $request->validate([
            'period' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'tags' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['tags']);
        $data['tags'] = $request->filled('tags') ? array_map('trim', explode(',', $request->tags)) : null;
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->input('sort_order', 0);

        $education->update($data);

        return redirect()->route('superadmin.educations.index')->with('success', 'Pendidikan berhasil diperbarui.');
    }

    public function destroy($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $education = Education::findOrFail($decoded[0]);
        $education->delete();

        return redirect()->route('superadmin.educations.index')->with('success', 'Pendidikan berhasil dihapus.');
    }

    public function toggleStatus($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $education = Education::findOrFail($decoded[0]);
        $education->update(['is_active' => !$education->is_active]);

        $msg = $education->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Pendidikan berhasil {$msg}.");
    }
}
