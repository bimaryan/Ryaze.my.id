<?php

namespace App\Models;

use App\Traits\HasHashid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory, HasHashid;

    protected $fillable = [
        'title',
        'description',
        'tags',
        'link_preview',
        'link_github',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_active' => 'boolean',
    ];
}
