<?php

namespace App\Models;

use App\Traits\HasHashid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory, HasHashid;

    protected $table = 'educations';

    protected $fillable = [
        'period',
        'degree',
        'institution',
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
