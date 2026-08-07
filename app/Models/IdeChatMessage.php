<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdeChatMessage extends Model
{
    protected $fillable = [
        'ide_chat_id', 'role', 'content',
    ];

    public function chat()
    {
        return $this->belongsTo(IdeChat::class, 'ide_chat_id');
    }
}
