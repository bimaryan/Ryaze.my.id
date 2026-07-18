<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Vinkla\Hashids\Facades\Hashids;

class HostingDatabase extends Model
{
    use \App\Traits\HasHashid;

    use HasFactory;

    protected $fillable = [
        'user_id', 'hashid', 'api_key', 'db_name', 'db_username', 'db_password', 'host', 'port',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($database) {
            if (empty($database->api_key)) {
                $database->api_key = \Illuminate\Support\Str::random(40);
            }
        });
        static::created(function ($database) {
            $database->update(['hashid' => Hashids::encode($database->id)]);
        });
    }

    /**
     * Dekripsi password database saat diakses.
     * Fallback ke plain text jika nilai belum dienkripsi (data lama).
     */
    public function getDbPasswordAttribute($value): string
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Data lama yang belum dienkripsi — return as-is
            return $value;
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

