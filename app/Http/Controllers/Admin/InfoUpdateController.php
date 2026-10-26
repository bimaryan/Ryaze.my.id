<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InfoUpdate;
use App\Services\InfoUpdateAiWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class InfoUpdateController extends Controller
{
    /**
     * Bantu admin menyusun judul + deskripsi dengan AI.
     * Endpoint ini hanya mengisi field di form, tidak menyimpan apa pun.
     */
    public function generateDescription(Request $request, InfoUpdateAiWriter $writer)
    {
        // Endpoint ini dipanggil dari fetch() dan hanya berguna sebagai JSON.
        // Konfigurasi exception di app me-render error validasi sebagai redirect
        // (shouldRenderJsonWhen hanya untuk prefix api/*), jadi tangkap manual
        // supaya pemanggil selalu dapat JSON.
        try {
            $data = $request->validate([
                'title' => 'nullable|string|max:255',
                'category' => ['nullable', Rule::in(InfoUpdate::CATEGORIES)],
                'brief' => 'nullable|string|max:1000',
                'with_title' => 'nullable|boolean',
            ], [
                'category.in' => 'Kategori info update tidak dikenal.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }

        try {
            $result = $writer->generate(
                $data['title'] ?? '',
                $data['category'] ?? InfoUpdate::CATEGORY_FEATURE,
                $data['brief'] ?? null,
                $request->boolean('with_title'),
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => $e instanceof \RuntimeException
                    ? $e->getMessage()
                    : 'Gagal membuat deskripsi AI. Coba lagi.',
            ], 422);
        }

        return response()->json($result);
    }

    public function index(Request $request)
    {
        $query = InfoUpdate::query()->with('author:id,name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && in_array($request->category, InfoUpdate::CATEGORIES, true)) {
            $query->where('category', $request->category);
        }

        $updates = $query->latest('published_at')->latest()->paginate(10)->withQueryString();

        return view('pages.admin.info_updates.index', compact('updates'));
    }

    public function create()
    {
        return view('pages.admin.info_updates.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        InfoUpdate::create($this->withSlug($data));

        return redirect()
            ->route('superadmin.info_updates.index')
            ->with('success', 'Info update berhasil disimpan.');
    }

    public function edit($hashid)
    {
        $infoUpdate = InfoUpdate::findByHashidOrFail($hashid);

        return view('pages.admin.info_updates.edit', compact('infoUpdate'));
    }

    public function update(Request $request, $hashid)
    {
        $infoUpdate = InfoUpdate::findByHashidOrFail($hashid);

        $data = $this->validatePayload($request);
        $data['slug'] = InfoUpdate::uniqueSlug($data['title'], $infoUpdate->id);

        $infoUpdate->update($data);

        return redirect()
            ->route('superadmin.info_updates.index')
            ->with('success', 'Info update berhasil diperbarui.');
    }

    public function destroy($hashid)
    {
        $infoUpdate = InfoUpdate::findByHashidOrFail($hashid);
        $infoUpdate->delete();

        return back()->with('success', 'Info update berhasil dihapus.');
    }

    public function toggleStatus($hashid)
    {
        $infoUpdate = InfoUpdate::findByHashidOrFail($hashid);
        $infoUpdate->update(['is_active' => ! $infoUpdate->is_active]);

        return back()->with('success', 'Status info update berhasil diubah.');
    }

    /** Rules yang dipakai bersama oleh store & update. */
    private function validatePayload(Request $request): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'category' => ['required', Rule::in(InfoUpdate::CATEGORIES)],
            'version' => 'nullable|string|max:32',
            'priority' => ['required', Rule::in(InfoUpdate::PRIORITIES)],
            'published_at' => 'nullable|date',
        ]);

        $data['author_id'] = Auth::id();
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    /** Slug selalu diturunkan dari judul terbaru supaya URL ikut berubah. */
    private function withSlug(array $data): array
    {
        $data['slug'] = InfoUpdate::uniqueSlug($data['title']);

        return $data;
    }
}