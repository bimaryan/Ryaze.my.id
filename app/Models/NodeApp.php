<?php

namespace App\Models;

use App\Traits\HasHashid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NodeApp extends Model
{
    use HasHashid;

    protected $fillable = [
        'hosting_project_id',
        'status',
        'entry',
        'port',
        'node_env',
        'env_vars',
        'last_error',
        'last_build_log',
        'restart_count',
        'started_at',
    ];

    protected $casts = [
        'env_vars' => 'array',
        'started_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(HostingProject::class, 'hosting_project_id');
    }

    /**
     * Batas jumlah aplikasi Node per paket langganan (dari Setting admin).
     * Format JSON: {"free":0,"starter":1,"pro":3,"business":10}
     */
    public static function planLimit(string $plan): int
    {
        $defaults = json_encode(['free' => 0, 'starter' => 1, 'pro' => 3, 'business' => 10]);
        $limits = json_decode(\App\Models\Setting::val('node_apps_limits', $defaults), true);

        return (int) ($limits[$plan] ?? 0);
    }
}
