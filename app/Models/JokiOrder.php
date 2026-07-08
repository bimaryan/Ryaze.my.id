<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class JokiOrder extends Model
{
    use \App\Traits\HasHashid;

    protected $fillable = [
        'order_number',
        'client_id',
        'worker_id',
        'service_id',
        'project_name',
        'description',
        'tech_stack',
        'status',
        'progress',
        'price',
        'deadline',
        'repo_link',
        'demo_link',
        'rating',
        'review',
        'is_deployed_to_hosting',
    ];

    protected $casts = [
        'progress' => 'integer',
        'price' => 'integer',
        'deadline' => 'date',
    ];

    

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(JokiService::class, 'service_id');
    }

    // RELASI BARU
    public function payments(): HasMany
    {
        return $this->hasMany(JokiPayment::class, 'order_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(JokiMilestone::class, 'order_id');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(JokiRevision::class, 'order_id');
    }
}
