<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'joki_order_id',
        'sender_id',
        'receiver_id',
        'message',
        'is_read',
    ];

    public function order()
    {
        return $this->belongsTo(JokiOrder::class, 'joki_order_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
