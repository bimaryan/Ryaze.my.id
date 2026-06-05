<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JokiRevision extends Model
{
    protected $fillable = ['order_id', 'revision_note', 'status', 'admin_reply'];

    public function order()
    {
        return $this->belongsTo(JokiOrder::class, 'order_id');
    }
}
