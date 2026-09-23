<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class ExperienceController extends Controller
{
    public function index(Request $request)
    {
        $query = Experience::orderBy('sort_order');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('role', 'like', '%' . $search . '%')
                  ->orWhere('company', 'like', '%' . $search . '%');
            });
        }

        $experiences = $query->paginate(10)->withQueryString();
        return view('pages.admin.experiences.index', compact('experiences'));
    }

    public function create()
    {
        return view('pages.admin.experiences.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'period' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
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

        Experience::create($data);

        return redirect()->route('superadmin.experiences.index')->with('success', 'Pengalaman berhasil ditambahkan.');
    }

    public function edit($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $experience = Experience::findOrFail($decoded[0]);
        return view('pages.admin.experiences.edit', compact('experience'));
    }

    public function update(Request $request, $hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $experience = Experience::findOrFail($decoded[0]);

        $request->validate([
            'period' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
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

        $experience->update($data);

        return redirect()->route('superadmin.experiences.index')->with('success', 'Pengalaman berhasil diperbarui.');
    }

    public function destroy($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $experience = Experience::findOrFail($decoded[0]);
        $experience->delete();

        return redirect()->route('superadmin.experiences.index')->with('success', 'Pengalaman berhasil dihapus.');
    }

    public function toggleStatus($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $experience = Experience::findOrFail($decoded[0]);
        $experience->update(['is_active' => !$experience->is_active]);

        $msg = $experience->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Pengalaman berhasil {$msg}.");
    }
}
