<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    /**
     * Helper to get a setting value by key.
     * Caches the value forever until updated.
     */
    public static function val($key, $default = null)
    {
        return \Illuminate\Support\Facades\Cache::rememberForever('setting_' . $key, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Helper to set a setting value by key.
     */
    public static function setVal($key, $value)
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        
        \Illuminate\Support\Facades\Cache::forget('setting_' . $key);
        
        return $setting;
    }
}
