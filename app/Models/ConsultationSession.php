<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasHashid;

class ConsultationSession extends Model
{
    use HasFactory, HasHashid;

    protected $fillable = [
        'token',
        'chat_history',
        'summary',
        'status',
        'user_id'
    ];

    protected $casts = [
        'chat_history' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
