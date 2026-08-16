<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PromoEventController extends Controller
{
    public function index()
    {
        $promos = PromoEvent::latest()->paginate(10);
        return view('pages.admin.promo_events.index', compact('promos'));
    }

    public function create()
    {
        return view('pages.admin.promo_events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_url' => 'nullable|url',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'banner_image' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('banner_image');
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('promos', 'public');
        }

        PromoEvent::create($data);

        return redirect()->route('admin.promo_events.index')->with('success', 'Promo event berhasil ditambahkan.');
    }

    public function edit(PromoEvent $promo_event)
    {
        return view('pages.admin.promo_events.edit', compact('promo_event'));
    }

    public function update(Request $request, PromoEvent $promo_event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_url' => 'nullable|url',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'banner_image' => 'nullable|image|max:3048',
        ]);

        $data = $request->except('banner_image');
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('banner_image')) {
            if ($promo_event->banner_image) {
                Storage::disk('public')->delete($promo_event->banner_image);
            }
            $data['banner_image'] = $request->file('banner_image')->store('promos', 'public');
        }

        $promo_event->update($data);

        return redirect()->route('admin.promo_events.index')->with('success', 'Promo event berhasil diperbarui.');
    }

    public function destroy(PromoEvent $promo_event)
    {
        if ($promo_event->banner_image) {
            Storage::disk('public')->delete($promo_event->banner_image);
        }
        $promo_event->delete();
        return back()->with('success', 'Promo event berhasil dihapus.');
    }
}
