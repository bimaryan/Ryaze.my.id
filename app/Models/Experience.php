<?php

namespace App\Models;

use App\Traits\HasHashid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory, HasHashid;

    protected $table = 'experiences';

    protected $fillable = [
        'period',
        'role',
        'company',
        'type',
        'location',
        'description',
        'icon',
        'color',
        'tags',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_active' => 'boolean',
    ];
}
