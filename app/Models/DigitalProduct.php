<?php

namespace App\Models;

use App\Traits\HasHashid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalProduct extends Model
{
    use HasFactory, HasHashid;

    protected $table = 'digital_products';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'cover_path',
        'file_path',
        'file_name',
        'download_count',
        'is_active',
    ];

    protected $casts = [
        'price' => 'integer',
        'download_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function purchases()
    {
        return $this->hasMany(DigitalPurchase::class, 'product_id');
    }
}
