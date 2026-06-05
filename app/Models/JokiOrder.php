<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JokiOrder extends Model
{
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
    ];

    protected $casts = [
        'progress' => 'integer',
        'price' => 'integer',
        'deadline' => 'date',
    ];

    /**
     * Relasi ke User (sebagai Klien yang memesan)
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Relasi ke User (sebagai Admin/Developer yang mengerjakan)
     */
    public function worker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    /**
     * Relasi ke JokiService (Layanan apa yang dipilih)
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(JokiService::class, 'service_id');
    }
}
