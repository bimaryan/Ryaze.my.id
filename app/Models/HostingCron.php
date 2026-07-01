<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostingCron extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'command',
        'schedule_expression',
        'is_active',
        'last_run',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_run' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(HostingProject::class);
    }
}
