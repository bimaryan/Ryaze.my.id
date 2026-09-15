<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $announcements = $query->latest()->paginate(10)->withQueryString();

        return inertia('Admin/Announcements', ['announcements' => $announcements]);
    }

    public function create()
    {
        return inertia('Admin/AnnouncementCreate');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'nullable|string',
            'type'        => 'required|in:info,update,maintenance,warning',
            'audience'    => 'required|in:all,hosting,joki',
            'starts_at'   => 'nullable|date',
            'expires_at'  => 'nullable|date|after_or_equal:starts_at',
        ]);

        $data = $request->only(['title', 'content', 'type', 'audience', 'starts_at', 'expires_at']);
        $data['is_active'] = $request->has('is_active');
        $data['is_pinned'] = $request->has('is_pinned');

        Announcement::create($data);

        return redirect()->route('superadmin.announcements.index')->with('success', 'Informasi berhasil ditambahkan.');
    }

    public function edit($hashid)
    {
        $announcement = Announcement::findByHashidOrFail($hashid);

        return inertia('Admin/AnnouncementEdit', ['announcement' => $announcement]);
    }

    public function update(Request $request, $hashid)
    {
        $announcement = Announcement::findByHashidOrFail($hashid);

        $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'nullable|string',
            'type'        => 'required|in:info,update,maintenance,warning',
            'audience'    => 'required|in:all,hosting,joki',
            'starts_at'   => 'nullable|date',
            'expires_at'  => 'nullable|date|after_or_equal:starts_at',
        ]);

        $data = $request->only(['title', 'content', 'type', 'audience', 'starts_at', 'expires_at']);
        $data['is_active'] = $request->has('is_active');
        $data['is_pinned'] = $request->has('is_pinned');

        $announcement->update($data);

        return redirect()->route('superadmin.announcements.index')->with('success', 'Informasi berhasil diperbarui.');
    }

    public function destroy($hashid)
    {
        $announcement = Announcement::findByHashidOrFail($hashid);
        $announcement->delete();

        return back()->with('success', 'Informasi berhasil dihapus.');
    }

    public function toggleStatus($hashid)
    {
        $announcement = Announcement::findByHashidOrFail($hashid);
        $announcement->update(['is_active' => !$announcement->is_active]);

        return back()->with('success', 'Status informasi berhasil diubah.');
    }
}
