<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class HostingPgsqlDatabase extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($database) {
            $database->hashid = Hashids::encode($database->id);
            $database->save();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
