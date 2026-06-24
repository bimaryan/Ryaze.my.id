<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JokiMilestone extends Model
{
    use \App\Traits\HasHashid;

    protected $fillable = ['order_id', 'title', 'description', 'status', 'due_date'];

    protected $casts = [
        'due_date' => 'date'
    ];

    public function order()
    {
        return $this->belongsTo(JokiOrder::class, 'order_id');
    }
}
