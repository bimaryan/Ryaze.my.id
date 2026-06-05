<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JokiPayment extends Model
{
    protected $fillable = [
        'order_id', 'invoice_number', 'payment_name', 'amount',
        'status', 'payment_method', 'snap_token', 'paid_at'
    ];

    protected $casts = [
        'amount' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(JokiOrder::class, 'order_id');
    }
}
