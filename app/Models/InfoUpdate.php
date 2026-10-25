<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InfoUpdate extends Model
{
    use HasFactory, \App\Traits\HasHashid;

    public const CATEGORY_FEATURE = 'feature';
    public const CATEGORY_MAINTENANCE = 'maintenance';
    public const CATEGORY_ANNOUNCEMENT = 'announcement';

    public const CATEGORIES = [
        self::CATEGORY_FEATURE,
        self::CATEGORY_MAINTENANCE,
        self::CATEGORY_ANNOUNCEMENT,
    ];

    /** Label + warna badge per kategori (dipakai di form & daftar).
     *
     *  `variant` -> untuk <x-ui.badge>, warna diambil dari design system.
     *  `badge`   -> class HTML mentah, hanya untuk halaman lama yang
     *              belum memakai komponen badge.
     */
    public const CATEGORY_META = [
        self::CATEGORY_FEATURE => [
            'label' => 'Fitur Baru',
            'variant' => 'success',
            'badge' => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300',
            'icon' => 'fa-sparkles',
        ],
        self::CATEGORY_MAINTENANCE => [
            'label' => 'Maintenance',
            'variant' => 'warning',
            'badge' => 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300',
            'icon' => 'fa-screwdriver-wrench',
        ],
        self::CATEGORY_ANNOUNCEMENT => [
            'label' => 'Pengumuman',
            'variant' => 'primary',
            'badge' => 'bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300',
            'icon' => 'fa-bullhorn',
        ],
    ];

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_CRITICAL = 'critical';

    public const PRIORITIES = [
        self::PRIORITY_LOW,
        self::PRIORITY_NORMAL,
        self::PRIORITY_HIGH,
        self::PRIORITY_CRITICAL,
    ];

    public const PRIORITY_META = [
        self::PRIORITY_LOW => [
            'label' => 'Biasanya',
            'variant' => 'neutral',
            'badge' => 'bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300',
        ],
        self::PRIORITY_NORMAL => [
            'label' => 'Penting',
            'variant' => 'primary',
            'badge' => 'bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300',
        ],
        self::PRIORITY_HIGH => [
            'label' => 'Mendesak',
            'variant' => 'warning',
            'badge' => 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300',
        ],
        self::PRIORITY_CRITICAL => [
            'label' => 'Darurat',
            'variant' => 'danger',
            'badge' => 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300',
        ],
    ];

    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'description',
        'category',
        'version',
        'priority',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Slug adalah kolom NOT NULL UNIQUE yang selalu diturunkan dari judul.
     * Dipasang sebagai jaring pengaman supaya create() dari mana pun (seed,
     * tinker, controller) tidak pernah gagal karena lupa mengisinya.
     */
    protected static function booted(): void
    {
        static::saving(function (InfoUpdate $update) {
            if (blank($update->slug) && filled($update->title)) {
                $update->slug = static::uniqueSlug($update->title, $update->id);
            }
        });
    }

    /**
     * Info yang benar-benar tampil ke user: aktif DAN sudah sampai jadwalnya.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function (Builder $q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    /** Filter kategori; nilai.ngawur diabaikan supaya URL rusak tidak error. */
    public function scopeOfCategory(Builder $query, ?string $category): Builder
    {
        if ($category !== null && in_array($category, self::CATEGORIES, true)) {
            $query->where('category', $category);
        }

        return $query;
    }

    public function isPublished(): bool
    {
        return $this->is_active
            && ($this->published_at === null || $this->published_at->lessThanOrEqualTo(now()));
    }

    /** Immutable URL publik, bukan id yang bisajdipakai menebak-nebak. */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function categoryLabel(): string
    {
        return self::CATEGORY_META[$this->category]['label'] ?? Str::headline($this->category);
    }

    public function categoryBadgeClass(): string
    {
        return self::CATEGORY_META[$this->category]['badge'] ?? self::CATEGORY_META[self::CATEGORY_FEATURE]['badge'];
    }

    /** Varian <x-ui.badge> untuk kategori; fallback ke info bila tak dikenal. */
    public function categoryBadgeVariant(): string
    {
        return self::CATEGORY_META[$this->category]['variant'] ?? 'neutral';
    }

    /** Varian <x-ui.badge> untuk prioritas; fallback ke normal. */
    public function priorityBadgeVariant(): string
    {
        return self::PRIORITY_META[$this->priority]['variant'] ?? 'neutral';
    }

    public function categoryIcon(): string
    {
        return self::CATEGORY_META[$this->category]['icon'] ?? 'fa-circle-info';
    }

    public function priorityLabel(): string
    {
        return self::PRIORITY_META[$this->priority]['label'] ?? Str::headline($this->priority);
    }

    public function priorityBadgeClass(): string
    {
        return self::PRIORITY_META[$this->priority]['badge'] ?? self::PRIORITY_META[self::PRIORITY_NORMAL]['badge'];
    }

    /**
     * Slug unik dari judul. Menambah sufiks angka bila slug-nya sudah dipakai,
     * jadi dua update dengan judul sama tetap bisa disimpan.
     */
    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);

        if ($base === '') {
            $base = 'info-update';
        }

        $slug = Str::limit($base, 180, '');
        $candidate = $slug;
        $suffix = 2;

        while (static::where('slug', $candidate)
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
            ->exists()) {
            $candidate = $slug.'-'.$suffix;
            $suffix++;
        }

        return $candidate;
    }
}