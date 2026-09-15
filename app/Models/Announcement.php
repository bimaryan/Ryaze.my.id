<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasHashid;

class Announcement extends Model
{
    use HasHashid;

    protected $fillable = [
        'title',
        'content',
        'type',
        'audience',
        'is_active',
        'is_pinned',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_pinned' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Scope: only currently visible announcements
     */
    public function scopeVisible($query)
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
            });
    }

    /**
     * Scope: filter by audience
     */
    public function scopeForAudience($query, $audience)
    {
        return $query->where('audience', 'all')->orWhere('audience', $audience);
    }
}
