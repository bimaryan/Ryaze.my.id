<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasHashid;

class PromoEvent extends Model
{
    use HasHashid;
    protected $fillable = [
        'title',
        'description',
        'banner_image',
        'target_url',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function getBannerUrlAttribute()
    {
        return $this->banner_image ? \Illuminate\Support\Facades\Storage::url($this->banner_image) : null;
    }
}