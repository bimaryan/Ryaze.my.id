<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\DigitalProduct;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Portfolio;
use App\Models\Certification;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\SkillGroup;
use App\Models\TechBadge;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PortfolioApiController extends Controller
{
    private const RESOURCES = [
        'portfolios' => [
            'model' => Portfolio::class,
            'search' => ['title', 'description'],
            'order' => 'latest',
            'files' => ['image' => ['field' => 'image_path', 'dir' => 'portfolios', 'image' => true], 'certificate' => ['field' => 'certificate_path', 'dir' => 'portfolios/certificates']],
        ],
        'educations' => [
            'model' => Education::class,
            'search' => ['degree', 'institution'],
            'order' => 'sort_order',
        ],
        'experiences' => [
            'model' => Experience::class,
            'search' => ['role', 'company'],
            'order' => 'sort_order',
        ],
        'testimonials' => [
            'model' => Testimonial::class,
            'search' => ['name', 'role', 'company'],
            'order' => 'sort_order',
            'files' => ['avatar' => ['field' => 'avatar_path', 'dir' => 'testimonials/avatars', 'image' => true]],
        ],
        'certifications' => [
            'model' => Certification::class,
            'search' => ['name', 'issuer'],
            'order' => 'sort_order',
            'files' => ['image' => ['field' => 'image_path', 'dir' => 'certifications', 'image' => true]],
        ],
        'digital-products' => [
            'model' => DigitalProduct::class,
            'search' => ['name', 'slug'],
            'order' => 'latest',
            'files' => ['cover' => ['field' => 'cover_path', 'dir' => 'digital-products/covers', 'image' => true], 'file' => ['field' => 'file_path', 'dir' => 'digital-products/files']],
        ],
    ];

    // ─────────────────────────────────────────────────────────────
    // AUTH TOKEN (Sanctum)
    // ─────────────────────────────────────────────────────────────

    public function issueToken(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password) || $user->role !== 'superadmin') {
            return response()->json(['message' => 'Kredensial tidak valid.'], 401);
        }

        $token = $user->createToken('portfolio-api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => ['hashid' => $user->hashid, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role],
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // PUBLIC READ
    // ─────────────────────────────────────────────────────────────
    public function overview(): JsonResponse
    {
        $profile = [
            'name' => Setting::val('profile_name') ?? 'Bima Ryan Alfarizi',
            'title' => Setting::val('profile_title') ?? 'Full-Stack Developer & Software Engineer',
            'bio' => Setting::val('profile_bio'),
            'location' => Setting::val('profile_location') ?? 'Indonesia',
            'avatar' => $this->assetUrl(Setting::val('profile_avatar')),
            'github' => Setting::val('social_github'),
            'instagram' => Setting::val('social_instagram'),
            'linkedin' => Setting::val('social_linkedin'),
            'email' => Setting::val('contact_email'),
            'whatsapp' => Setting::val('contact_whatsapp'),
        ];

        $portfolios = Portfolio::where('is_active', true)->latest()->get();

        return response()->json([
            'profile' => $profile,
            'portfolios' => $this->decorate($portfolios->forPage(1, 6)->values(), ['image_path', 'certificate_path']),
            'total_portfolios' => $portfolios->count(),
            'all_tags' => collect($portfolios->pluck('tags'))->flatten()->filter()->unique()->sort()->values(),
            'educations' => $this->decorate(Education::where('is_active', true)->orderBy('sort_order')->get()),
            'experiences' => $this->decorate(Experience::where('is_active', true)->orderBy('sort_order')->get()),
            'skill_groups' => $this->skillTree(false),
            'tech_badges' => TechBadge::orderBy('sort_order')->get(),
            'testimonials' => $this->decorate(Testimonial::where('is_active', true)->orderBy('sort_order')->get(), ['avatar_path']),
            'certifications' => $this->decorate(Certification::where('is_active', true)->orderBy('sort_order')->get(), ['image_path']),
            'digital_products' => $this->decorate(DigitalProduct::where('is_active', true)->latest()->get(), ['cover_path']),
        ]);
    }

    public function indexPortfolios(Request $request): JsonResponse
    {
        $query = Portfolio::where('is_active', true)->latest();

        if ($tag = $request->query('tag')) {
            $query->whereJsonContains('tags', $tag);
        }

        return response()->json($this->decorate($query->get(), ['image_path', 'certificate_path']));
    }

    public function indexEducations(): JsonResponse
    {
        return response()->json($this->decorate(Education::where('is_active', true)->orderBy('sort_order')->get()));
    }

    public function indexExperiences(): JsonResponse
    {
        return response()->json($this->decorate(Experience::where('is_active', true)->orderBy('sort_order')->get()));
    }

    public function indexSkills(): JsonResponse
    {
        return response()->json([
            'skill_groups' => $this->skillTree(true),
            'tech_badges' => TechBadge::orderBy('sort_order')->get(),
        ]);
    }

    public function indexTestimonials(Request $request): JsonResponse
    {
        $query = Testimonial::where('is_active', true)->orderBy('sort_order');

        if ($min = (int) $request->query('min_rating')) {
            $query->where('rating', '>=', $min);
        }

        return response()->json($this->decorate($query->get(), ['avatar_path']));
    }

    public function indexCertifications(): JsonResponse
    {
        return response()->json($this->decorate(Certification::where('is_active', true)->orderBy('sort_order')->get(), ['image_path']));
    }

    public function indexDigitalProducts(): JsonResponse
    {
        return response()->json($this->decorate(
            DigitalProduct::where('is_active', true)->latest()->get(),
            ['cover_path'],
            hidden: ['file_path', 'file_name']
        ));
    }

    public function sendContact(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $message = ContactMessage::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        return response()->json([
            'message' => 'Pesan terkirim! Terima kasih telah menghubungi saya.',
            'data' => $message->only(['hashid', 'name', 'email', 'subject', 'created_at']),
        ], 201);
    }

    // ─────────────────────────────────────────────────────────────
    // ADMIN CRUD (5 resource generik)
    // ─────────────────────────────────────────────────────────────

    public function adminIndex(Request $request, string $resource): JsonResponse
    {
        $cfg = $this->resource($resource);
        $query = $cfg['model']::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($cfg, $search) {
                foreach ($cfg['search'] as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        if ($cfg['order'] === 'latest') {
            $query->latest();
        } else {
            $query->orderBy('sort_order')->orderBy('id');
        }

        $perPage = min(max((int) $request->query('per_page', 15), 1), 100);
        $rows = $query->paginate($perPage)->withQueryString();

        $data = $this->decorate(collect($rows->items()), array_keys($cfg['files'] ?? []));

        return response()->json(array_replace($rows->toArray(), ['data' => $data]));
    }

    public function adminStore(Request $request, string $resource): JsonResponse
    {
        $cfg = $this->resource($resource);
        $data = $request->validate($this->rules($resource, store: true));

        $this->normalize($data, $resource, store: true);
        $this->handleUploads($request, $cfg, $data);

        if ($resource === 'digital-products') {
            $data['slug'] = $this->uniqueSlug($request->input('name'));
        }

        $model = $cfg['model']::create($data);

        return response()->json([
            'message' => ucfirst(str_replace('-', ' ', $resource)) . ' berhasil ditambahkan.',
            'data' => $this->decorate($model, array_keys($cfg['files'] ?? [])),
        ], 201);
    }

    public function adminUpdate(Request $request, string $resource, string $hashid): JsonResponse
    {
        $cfg = $this->resource($resource);
        $model = $cfg['model']::findByHashidOrFail($hashid);
        $data = $request->validate($this->rules($resource, store: false));

        $this->normalize($data, $resource, store: false);
        $this->handleUploads($request, $cfg, $data, $model);

        if ($resource === 'digital-products' && $request->filled('name')) {
            $data['slug'] = $this->uniqueSlug(Str::slug($request->input('name')), $model->id);
        }

        $model->update($data);

        return response()->json([
            'message' => ucfirst(str_replace('-', ' ', $resource)) . ' berhasil diperbarui.',
            'data' => $this->decorate($model->fresh(), array_keys($cfg['files'] ?? [])),
        ]);
    }

    public function adminDestroy(string $resource, string $hashid): JsonResponse
    {
        $cfg = $this->resource($resource);
        $model = $cfg['model']::findByHashidOrFail($hashid);

        foreach ($cfg['files'] ?? [] as $specs) {
            if (!empty($model->{$specs['field']})) {
                Storage::disk('public')->delete($model->{$specs['field']});
            }
        }

        $model->delete();

        return response()->json(['message' => ucfirst(str_replace('-', ' ', $resource)) . ' berhasil dihapus.']);
    }

    public function adminToggleStatus(string $resource, string $hashid): JsonResponse
    {
        $cfg = $this->resource($resource);
        $model = $cfg['model']::findByHashidOrFail($hashid);
        $model->update(['is_active' => !$model->is_active]);

        return response()->json([
            'message' => ucfirst(str_replace('-', ' ', $resource)) . ' berhasil ' . ($model->is_active ? 'diaktifkan' : 'dinonaktifkan') . '.',
            'is_active' => $model->is_active,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // SKILL GROUPS
    // ─────────────────────────────────────────────────────────────

    public function indexSkillGroups(): JsonResponse
    {
        return response()->json([
            'skill_groups' => $this->skillTree(false),
        ]);
    }

    public function storeSkillGroup(Request $request): JsonResponse
    {
        $data = $request->validate([
            'label' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $group = SkillGroup::create($data);
        $group->load('skills');

        return response()->json(['message' => 'Grup skill berhasil ditambahkan.', 'data' => $group], 201);
    }

    public function updateSkillGroup(Request $request, string $hashid): JsonResponse
    {
        $group = SkillGroup::findByHashidOrFail($hashid);

        $data = $request->validate([
            'label' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $group->update($data);
        $group->load('skills');

        return response()->json(['message' => 'Grup skill berhasil diperbarui.', 'data' => $group->fresh()]);
    }

    public function destroySkillGroup(string $hashid): JsonResponse
    {
        $group = SkillGroup::findByHashidOrFail($hashid);
        $group->delete();

        return response()->json(['message' => 'Grup skill berhasil dihapus.']);
    }

    // ─────────────────────────────────────────────────────────────
    // SKILLS (nested)
    // ─────────────────────────────────────────────────────────────

    public function storeSkill(Request $request): JsonResponse
    {
        $data = $request->validate([
            'skill_group_id' => 'required|string',
            'name' => 'required|string|max:255',
            'percentage' => 'nullable|integer|min:0|max:100',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $group = SkillGroup::findByHashidOrFail($data['skill_group_id']);
        $skill = $group->skills()->create(collect($data)->except('skill_group_id')->all());
        $skill->setRelation('group', $group);

        return response()->json(['message' => 'Skill berhasil ditambahkan.', 'data' => $skill], 201);
    }

    public function updateSkill(Request $request, string $hashid): JsonResponse
    {
        $skill = Skill::findByHashidOrFail($hashid);

        $data = $request->validate([
            'skill_group_id' => 'nullable|string',
            'name' => 'required|string|max:255',
            'percentage' => 'nullable|integer|min:0|max:100',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if (isset($data['skill_group_id'])) {
            $data['skill_group_id'] = SkillGroup::findByHashidOrFail($data['skill_group_id'])->id;
        }

        $skill->update($data);

        return response()->json(['message' => 'Skill berhasil diperbarui.', 'data' => $skill->fresh()->load('group')]);
    }

    public function destroySkill(string $hashid): JsonResponse
    {
        Skill::findByHashidOrFail($hashid)->delete();

        return response()->json(['message' => 'Skill berhasil dihapus.']);
    }

    // ─────────────────────────────────────────────────────────────
    // TECH BADGES
    // ─────────────────────────────────────────────────────────────

    public function indexTechBadges(): JsonResponse
    {
        return response()->json(['tech_badges' => TechBadge::orderBy('sort_order')->get()]);
    }

    public function storeTechBadge(Request $request): JsonResponse
    {
        $data = $request->validate([
            'icon' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
        ]);

        return response()->json(['message' => 'Tech badge berhasil ditambahkan.', 'data' => TechBadge::create($data)], 201);
    }

    public function destroyTechBadge(string $hashid): JsonResponse
    {
        TechBadge::findByHashidOrFail($hashid)->delete();

        return response()->json(['message' => 'Tech badge berhasil dihapus.']);
    }

    // ─────────────────────────────────────────────────────────────
    // CONTACT MESSAGES (Pesan Masuk)
    // ─────────────────────────────────────────────────────────────

    public function indexContactMessages(Request $request): JsonResponse
    {
        $query = ContactMessage::query();

        if ($filter = $request->query('filter')) {
            match ($filter) {
                'unread' => $query->where('is_read', false),
                'read' => $query->where('is_read', true),
                default => null,
            };
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $perPage = min(max((int) $request->query('per_page', 15), 1), 100);

        return response()->json($query->latest()->paginate($perPage)->withQueryString());
    }

    public function showContactMessage(string $hashid): JsonResponse
    {
        $message = ContactMessage::findByHashidOrFail($hashid);

        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return response()->json(['data' => $message->fresh()]);
    }

    public function destroyContactMessage(string $hashid): JsonResponse
    {
        ContactMessage::findByHashidOrFail($hashid)->delete();

        return response()->json(['message' => 'Pesan berhasil dihapus.']);
    }

    // ─────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────

    private function resource(string $resource): array
    {
        $cfg = self::RESOURCES[$resource] ?? null;

        if (!$cfg) {
            abort(404, 'Resource tidak dikenal.');
        }

        return $cfg;
    }

    private function rules(string $resource, bool $store): array
    {
        $rules['portfolios'] = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'tags' => 'nullable',
            'link_preview' => 'nullable|url',
            'link_github' => 'nullable|url',
            'link_journal' => 'nullable|url',
            'link_copyright' => 'nullable|url',
            'is_active' => 'nullable|boolean',
        ];

        $rules['educations'] = [
            'period' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'tags' => 'nullable',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ];

        $rules['experiences'] = [
            'period' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'tags' => 'nullable',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ];

        $rules['testimonials'] = [
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ];

        $rules['certifications'] = [
            'name' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'issued_at' => 'required|date',
            'expired_at' => 'nullable|date',
            'credential_id' => 'nullable|string|max:255',
            'credential_url' => 'nullable|url',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ];

        $rules['digital-products'] = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|integer|min:0',
            'file_name' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];

        if ($store) {
            $rules['digital-products'] = array_merge(
                ['file' => 'required|file|min:0|max:102400', 'file_name' => 'required|string|max:255'],
                $rules['digital-products']
            );
        }

        return $rules[$resource];
    }

    private function normalize(array &$data, string $resource, bool $store): void
    {
        if (array_key_exists('tags', $data)) {
            $tags = $data['tags'];

            if (is_string($tags)) {
                $tags = array_map('trim', explode(',', $tags));
            }

            $data['tags'] = collect($tags)->filter()->isNotEmpty() ? [...$tags] : null;
        }

        if (array_key_exists('is_active', $data)) {
            $data['is_active'] = (bool) $data['is_active'];
        } elseif ($store) {
            $data['is_active'] = true;
        }

        if (in_array($resource, ['testimonials', 'certifications', 'educations', 'experiences'], true) && !isset($data['sort_order'])) {
            $data['sort_order'] = 0;
        }
    }

    private function handleUploads(Request $request, array $cfg, array &$data, ?object $model = null): void
    {
        foreach ($cfg['files'] ?? [] as $fileKey => $specs) {
            if ($request->hasFile($fileKey)) {
                if ($model && !empty($model->{$specs['field']})) {
                    Storage::disk('public')->delete($model->{$specs['field']});
                }

                $data[$specs['field']] = $request->file($fileKey)->store($specs['dir'], 'public');
            } elseif ($request->filled($fileKey) && str_starts_with((string) $request->input($fileKey), 'http')) {
                $data[$specs['field']] = $request->input($fileKey);
            }
        }
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (DigitalProduct::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . (++$i);
        }

        return $slug;
    }

    private function skillTree(bool $includeInactive): array
    {
        return SkillGroup::when(!$includeInactive, fn ($q) => $q->where('is_active', true))
            ->orderBy('sort_order')
            ->get()
            ->map(function (SkillGroup $group) use ($includeInactive) {
                $skills = $group->skills()
                    ->when(!$includeInactive, fn ($q) => $q->where('is_active', true))
                    ->get();

                return array_merge($group->toArray(), ['skills' => $skills]);
            })
            ->all();
    }

    private function decorate(iterable $models, array $fileFields = [], array $hidden = []): array
    {
        return collect($models)->map(function ($model) use ($fileFields, $hidden) {
            $array = $model instanceof \Illuminate\Database\Eloquent\Model ? $model->toArray() : (array) $model;

            foreach ($hidden as $field) {
                unset($array[$field]);
            }

            if ($model instanceof \Illuminate\Database\Eloquent\Model && method_exists($model, 'getHashidAttribute')) {
                $array['hashid'] = $model->hashid;
            }

            foreach ($fileFields as $field) {
                $urlField = str_replace('_path', '_url', $field);
                $array[$urlField] = !empty($array[$field]) ? $this->assetUrl($array[$field]) : null;
            }

            return $array;
        })->all();
    }

    private function assetUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return url(Storage::url($path));
    }
}
