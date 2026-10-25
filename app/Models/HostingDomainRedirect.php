<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostingDomainRedirect extends Model
{
    protected $table = 'hosting_domain_redirects';

    protected $fillable = [
        'hosting_domain_id',
        'target',
        'status_code',
    ];

    protected $casts = [
        'status_code' => 'integer',
        'hosting_domain_id' => 'integer',
    ];

    /** Permanent = browser & cache boleh forgetting bahwa ini redirect. */
    public const PERMANENT_308 = 308;
    public const PERMANENT_301 = 301;
    public const TEMPORARY_307 = 307;
    public const TEMPORARY_302 = 302;

    public const STATUSES = [
        self::PERMANENT_308,
        self::PERMANENT_301,
        self::TEMPORARY_307,
        self::TEMPORARY_302,
    ];

    public function domain()
    {
        return $this->belongsTo(HostingDomain::class, 'hosting_domain_id');
    }

    public function isPermanent(): bool
    {
        return in_array($this->status_code, [self::PERMANENT_301, self::PERMANENT_308], true);
    }

    public function isTemporary(): bool
    {
        return in_array($this->status_code, [self::TEMPORARY_302, self::TEMPORARY_307], true);
    }

    public function kindLabel(): string
    {
        return $this->isPermanent() ? 'Permanent' : 'Temporary';
    }

    /** Opsi untuk dropdown di form. */
    public static function statusOptions(): array
    {
        return [
            self::PERMANENT_308 => 'Permanent (308)',
            self::PERMANENT_301 => 'Permanent (301)',
            self::TEMPORARY_307 => 'Temporary (307)',
            self::TEMPORARY_302 => 'Temporary (302)',
        ];
    }

    public static function isValidStatus(int $code): bool
    {
        return in_array($code, self::STATUSES, true);
    }
}