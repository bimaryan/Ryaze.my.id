<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokuCheckoutSession extends Model
{
    protected $fillable = [
        'order_id',
        'token_id',
        'payment_url',
        'expired_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
    ];
}
