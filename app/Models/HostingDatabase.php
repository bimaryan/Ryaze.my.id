<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class HostingDatabase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'hashid', 'db_name', 'db_username', 'db_password', 'host', 'port'
    ];

    protected static function boot()
    {
        parent::boot();
        static::created(function ($database) {
            $database->update(['hashid' => Hashids::encode($database->id)]);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
