<?php

namespace App\Http\Controllers\Joki\Admin;

use App\Http\Controllers\Controller;
use App\Models\JokiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        $services = JokiService::latest()->get();
        return view('pages.joki.admin.services.index', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        JokiService::create($validated);

        return back()->with('success', 'Layanan joki berhasil ditambahkan!');
    }

    public function update(Request $request, $hashid)
    {
        $decoded = \Vinkla\Hashids\Facades\Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        $id = $decoded[0];

        $service = JokiService::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $service->update($validated);

        return back()->with('success', 'Layanan joki berhasil diperbarui!');
    }

    public function destroy($hashid)
    {
        $decoded = \Vinkla\Hashids\Facades\Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        $id = $decoded[0];

        $service = JokiService::findOrFail($id);
        
        // Cek jika layanan ini punya order, mungkin sebaiknya disembunyikan (is_active = false) saja alih-alih dihapus.
        if ($service->orders()->count() > 0) {
            $service->update(['is_active' => false]);
            return back()->with('success', 'Layanan ini memiliki riwayat pesanan, sehingga dinonaktifkan (bukan dihapus permanen).');
        }

        $service->delete();
        return back()->with('success', 'Layanan joki berhasil dihapus!');
    }
}
