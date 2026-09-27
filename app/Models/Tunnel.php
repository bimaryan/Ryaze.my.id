<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tunnel extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'subdomain',
        'custom_domain',
        'secret',
        'target_port',
        'auth_username',
        'auth_password',
        'status',
        'last_connected_at',
    ];

    protected $casts = [
        'last_connected_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
