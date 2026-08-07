<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ApkBuild extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'app_name',
        'app_url',
        'package_name',
        'icon_path',
        'theme_color',
        'background_color',
        'display_mode',
        'orientation',
        'version_name',
        'version_code',
        'enable_notifications',
        'fallback_type',
        'splash_fade_duration',
        'navigation_color',
        'keystore_alias',
        'keystore_password',
        'key_password',
        'status',
    ];

    protected $hidden = [
        'keystore_password',
        'key_password',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
