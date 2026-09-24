<?php

namespace App\Models;

use App\Traits\HasHashid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use HasFactory, HasHashid;

    protected $table = 'certifications';

    protected $fillable = [
        'name',
        'issuer',
        'issued_at',
        'expired_at',
        'credential_id',
        'credential_url',
        'image_path',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'expired_at' => 'date',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
