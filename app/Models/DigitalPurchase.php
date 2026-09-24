<?php

namespace App\Models;

use App\Traits\HasHashid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalPurchase extends Model
{
    use HasFactory, HasHashid;

    protected $table = 'digital_purchases';

    protected $fillable = [
        'product_id',
        'order_id',
        'amount',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(DigitalProduct::class, 'product_id');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
