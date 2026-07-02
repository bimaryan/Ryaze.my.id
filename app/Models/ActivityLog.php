<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper static method to quickly record an activity.
     */
    public static function log($action, $description = null)
    {
        return self::create([
            'user_id' => auth()->id(), // can be null if not logged in
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip()
        ]);
    }
}
