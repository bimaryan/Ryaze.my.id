<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostingEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_address',
        'domain',
        'password',
        'quota_mb',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
