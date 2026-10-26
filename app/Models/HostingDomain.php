<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostingDomain extends Model
{
    use HasFactory, \App\Traits\HasHashid;

    /** Domain root milik user sendiri, punya zone Cloudflare sendiri. */
    public const TYPE_CUSTOM = 'custom';

    /** Subdomain dari domain root milik user sendiri (blog.tokoanda.com). */
    public const TYPE_SUBDOMAIN = 'subdomain';

    /**
     * Subdomain dari domain platform (silk.ryz.my.id).
     *
     * Hanya untuk data yang sudah ada sebelum fitur subdomain diperluas —
     * user TIDAK bisa membuat type ini lagi. Tetap dipelihara supaya row
     * lama (yang tidak punya zone sendiri) tetap bisa dibaca, ditampilkan,
     * dan resolving DNS-nya memakai zone utama platform.
     */
    public const TYPE_PLATFORM = 'platform';

    public const TYPES = [self::TYPE_CUSTOM, self::TYPE_SUBDOMAIN, self::TYPE_PLATFORM];

    protected $fillable = [
        'user_id',
        'project_id',
        'domain_name',
        'type',
        'parent_id',
        'ssl_status',
        'cf_zone_id',
        'nameservers',
    ];

    protected $casts = [
        'nameservers' => 'array',
        'project_id' => 'integer',
        'user_id' => 'integer',
        'parent_id' => 'integer',
    ];

    /** Default untuk row lama yang belum disentuh. */
    protected $attributes = [
        'type' => self::TYPE_CUSTOM,
    ];

    public function project()
    {
        return $this->belongsTo(HostingProject::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Domain root tempat subdomain ini dibuat. */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** Subdomain yang dibuat dari domain root ini. */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Aturan redirect domain ini, kalau ada.
     * Dipakai di tab Domains untuk Redirect to Another Domain.
     */
    public function redirect()
    {
        return $this->hasOne(\App\Models\HostingDomainRedirect::class);
    }

    /** Domain yang belum dikaitkan ke project mana pun. */
    public function scopeUnassigned(Builder $query): Builder
    {
        return $query->whereNull('project_id');
    }

    public function isCustom(): bool
    {
        return $this->type === self::TYPE_CUSTOM;
    }

    public function isSubdomain(): bool
    {
        return $this->type === self::TYPE_SUBDOMAIN;
    }

    public function isPlatform(): bool
    {
        return $this->type === self::TYPE_PLATFORM;
    }

    /**
     * Hostname turunan: punya label di depan domain root dan tidak punya
     * zone Cloudflare sendiri.
     */
    public function isChild(): bool
    {
        return ! $this->isCustom();
    }

    /** Domain root yang jadi acuan zona & status aktif. */
    public function rootDomain(): ?self
    {
        return $this->parent ?? $this;
    }

    /**
     * Zona Cloudflare yang dipakai untuk membuat DNS record.
     * Subdomain mewarisi zona induknya; subdomain platform memakai zona utama.
     */
    public function zoneId(): ?string
    {
        if ($this->cf_zone_id) {
            return $this->cf_zone_id;
        }

        if ($this->isSubdomain()) {
            return $this->parent?->cf_zone_id;
        }

        if ($this->isPlatform()) {
            return config('services.cloudflare.zone_id');
        }

        return null;
    }

    /** Domain root yang menaungi hostname ini di sisi DNS. */
    public function zoneDomain(): ?string
    {
        if ($this->isCustom()) {
            return $this->domain_name;
        }

        if ($this->isSubdomain()) {
            return $this->parent?->domain_name;
        }

        foreach ((array) config('hosting.public_bases', []) as $base) {
            if ($this->domain_name === $base || str_ends_with($this->domain_name, '.'.$base)) {
                return $base;
            }
        }

        return null;
    }

    /** Label pertama, mis. "blog" dari blog.tokoanda.com. */
    public function label(): string
    {
        return explode('.', (string) $this->domain_name)[0];
    }

    /** Hostname turunan yang perlu dilayani nginx & didaftarkan ke tunnel. */
    public function hostnames(): array
    {
        // Subdomain hanya satu hostname — tidak ada www. otomatis, karena
        // www.blog.tokoanda.com bukan subdomain yang diminta user.
        if ($this->isChild()) {
            return [$this->domain_name];
        }

        return array_values(array_unique(array_filter([
            $this->domain_name,
            'www.'.$this->domain_name,
        ])));
    }

    /** Normalisasi input user: huruf kecil, tanpa skema, tanpa path/slash. */
    public static function normalizeName(string $domain): string
    {
        $domain = strtolower(trim($domain));
        $domain = preg_replace('#^[a-z]+://#', '', $domain) ?? $domain;
        $domain = explode('/', $domain)[0];
        $domain = rtrim($domain, '.');

        return trim($domain);
    }

    /**
     * Ambil label subdomain dari input user. Menerima "blog", "BLOG",
     * "https://blog.tokoanda.com/path", maupun "blog.tokoanda.com" — semuanya
     * dikembalikan sebagai "blog".
     *
     * Spasi di sekeliling dibuang supaya " blog " tetap jalan. Tanda hubung
     * di ujung TIDAK dibuang: "-blog" lebih baik ditolak dengan pesan jelas
     * daripada diam-diam menjadi "blog".
     */
    public static function normalizeLabel(string $input): string
    {
        $label = self::normalizeName($input);
        $label = explode('.', $label)[0];

        return trim($label, " \t\n\r\0\x0B");
    }

    /**
     * Label DNS yang valid: alphanumeric + tanda hubung, tidak boleh mulai/
     * berakhir dengan hubung, maksimal 63 karakter.
     */
    public static function isValidLabel(string $label): bool
    {
        return (bool) preg_match('/^[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?$/', $label);
    }
}