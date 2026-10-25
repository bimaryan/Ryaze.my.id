<?php

namespace App\Http\Controllers\Update;

use App\Http\Controllers\Controller;
use App\Models\InfoUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class InfoUpdateController extends Controller
{
    /**
     * Halaman publik /updates. Hanya update yang aktif DAN sudah sampai
     * jadwal tayangnya yang muncul — superadmin bisa menyiapkan update
     * terjadwal tanpa langsung tayang.
     */
    public function index(Request $request)
    {
        $category = $request->query('kategori');

        $updates = InfoUpdate::published()
            ->ofCategory($category)
            ->with('author:id,name')
            ->latest('published_at')
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('Updates/Index', [
            'updates' => $this->paginatePayload($updates),
            'categories' => $this->categories(),
            'currentCategory' => $category,
        ]);
    }

    public function show(string $slug)
    {
        $update = InfoUpdate::published()
            ->with('author:id,name')
            ->where('slug', $slug)
            ->firstOrFail();

        $related = InfoUpdate::published()
            ->where('category', $update->category)
            ->whereKeyNot($update->id)
            ->latest('published_at')
            ->latest()
            ->take(3)
            ->get();

        return Inertia::render('Updates/Show', [
            'update' => $this->transform($update),
            'related' => $related->map(fn (InfoUpdate $u) => $this->transform($u))->values(),
            'categories' => $this->categories(),
        ]);
    }

    /**
     * Ubah paginator Eloquent menjadi payload yang consumed Blog/Index.jsx:
     * `data` untuk isi, sisanya untuk kontrol halaman.
     */
    private function paginatePayload($paginator): array
    {
        return [
            'data' => $paginator->getCollection()
                ->map(fn (InfoUpdate $u) => $this->transform($u))
                ->values(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'path' => $paginator->path(),
            'prev_page_url' => $paginator->previousPageUrl(),
            'next_page_url' => $paginator->nextPageUrl(),
            'total' => $paginator->total(),
        ];
    }

    /**
     * Bentuk data yang dikirim ke React. Badge/warna TIDAK ikut di sini —
     * kelasnya dipetakan di komponen JSX supaya Tailwind memindainya.
     */
    private function transform(InfoUpdate $update): array
    {
        $date = $update->published_at ?? $update->created_at;

        return [
            'id' => $update->id,
            'title' => $update->title,
            'slug' => $update->slug,
            'excerpt' => Str::limit($update->description, 220),
            'description' => $update->description,
            'category' => $update->category,
            'priority' => $update->priority,
            'version' => $update->version,
            'published_at' => $date?->translatedFormat('d M Y'),
            'published_at_long' => $date?->translatedFormat('d F Y'),
            'author' => $update->author?->name,
            'url' => route('updates.show', $update->slug),
        ];
    }

    /** Daftar kategori + jumlah update aktif per kategori. */
    private function categories(): array
    {
        $counts = InfoUpdate::published()
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        return collect(InfoUpdate::CATEGORIES)
            ->map(function (string $key) use ($counts) {
                $meta = InfoUpdate::CATEGORY_META[$key];

                return [
                    'key' => $key,
                    'label' => $meta['label'],
                    'icon' => $meta['icon'],
                    'count' => (int) ($counts[$key] ?? 0),
                ];
            })
            ->values()
            ->all();
    }
}