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
        if (! $this->banner_image) {
            return null;
        }

        // Pakai asset() supaya mengikuti host/scheme request aktif (mis. https),
        // bukan APP_URL yang bisa masih http.
        return asset('storage/'.$this->banner_image);
    }
}