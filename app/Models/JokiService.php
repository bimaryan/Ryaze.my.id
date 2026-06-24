<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JokiService extends Model
{
    use \App\Traits\HasHashid;

    // Melindungi tabel dari mass assignment, hanya kolom ini yang boleh diisi
    protected $fillable = [
        'name',
        'slug',
        'description',
        'base_price',
        'is_active',
    ];

    // Memastikan tipe data kembaliannya sesuai
    protected $casts = [
        'base_price' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke JokiOrder: Satu layanan bisa dimiliki oleh banyak pesanan
     */
    public function orders(): HasMany
    {
        return $this->hasMany(JokiOrder::class, 'service_id');
    }
}
