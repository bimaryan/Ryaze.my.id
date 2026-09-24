<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Vinkla\Hashids\Facades\Hashids;

class CertificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Certification::orderBy('sort_order');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('issuer', 'like', '%' . $search . '%');
            });
        }

        $certifications = $query->paginate(10)->withQueryString();
        return view('pages.admin.certifications.index', compact('certifications'));
    }

    public function create()
    {
        return view('pages.admin.certifications.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'issued_at' => 'required|date',
            'expired_at' => 'nullable|date|after_or_equal:issued_at',
            'credential_id' => 'nullable|string|max:255',
            'credential_url' => 'nullable|url|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['image', 'is_active', 'sort_order', 'color']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('certifications', 'public');
        }

        $data['color'] = $request->input('color', '#6366f1');
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->input('sort_order', 0);

        Certification::create($data);

        return redirect()->route('superadmin.certifications.index')->with('success', 'Sertifikat berhasil ditambahkan.');
    }

    public function edit($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $certification = Certification::findOrFail($decoded[0]);
        return view('pages.admin.certifications.edit', compact('certification'));
    }

    public function update(Request $request, $hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $certification = Certification::findOrFail($decoded[0]);

        $request->validate([
            'name' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'issued_at' => 'required|date',
            'expired_at' => 'nullable|date|after_or_equal:issued_at',
            'credential_id' => 'nullable|string|max:255',
            'credential_url' => 'nullable|url|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['image', 'is_active', 'sort_order', 'color']);

        if ($request->hasFile('image')) {
            if ($certification->image_path) {
                Storage::disk('public')->delete($certification->image_path);
            }
            $data['image_path'] = $request->file('image')->store('certifications', 'public');
        }

        $data['color'] = $request->input('color', '#6366f1');
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->input('sort_order', 0);

        $certification->update($data);

        return redirect()->route('superadmin.certifications.index')->with('success', 'Sertifikat berhasil diperbarui.');
    }

    public function destroy($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $certification = Certification::findOrFail($decoded[0]);

        if ($certification->image_path) {
            Storage::disk('public')->delete($certification->image_path);
        }

        $certification->delete();

        return redirect()->route('superadmin.certifications.index')->with('success', 'Sertifikat berhasil dihapus.');
    }

    public function toggleStatus($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $certification = Certification::findOrFail($decoded[0]);
        $certification->update(['is_active' => !$certification->is_active]);

        $msg = $certification->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Sertifikat berhasil {$msg}.");
    }
}
