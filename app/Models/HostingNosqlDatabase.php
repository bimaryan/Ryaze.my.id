<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HostingNosqlDatabase extends Model
{
    use \App\Traits\HasHashid;
    use HasFactory;

    protected $fillable = [
        'user_id', 'hashid', 'nosql_type', 'db_username', 'db_password', 'host', 'port', 'keyspace_prefix'
    ];

    protected static function boot()
    {
        parent::boot();
        static::created(function ($database) {
            $database->update(['hashid' => \Vinkla\Hashids\Facades\Hashids::encode($database->id)]);
        });
    }

    public function getDbPasswordAttribute($value): string
    {
        try {
            return \Illuminate\Support\Facades\Crypt::decryptString($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return (string) $value;
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
