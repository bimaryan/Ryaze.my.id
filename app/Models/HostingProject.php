<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class HostingProject extends Model
{
    protected $fillable = [
        'user_id', 'project_name', 'framework', 'repo_source',
        'branch', 'ryaze_domain', 'custom_domain', 'status',
        'php_version', 'maintenance_mode', 'force_https',
    ];

    protected $casts = [
        'maintenance_mode' => 'boolean',
        'force_https' => 'boolean',
    ];

    // Alias Hash ID untuk URL yang elegan
    public function getHashidAttribute()
    {
        return Hashids::encode($this->id);
    }

    // Pemilik Proyek
    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Riwayat Deploy
    public function deployments()
    {
        return $this->hasMany(HostingDeployment::class)->orderBy('created_at', 'desc');
    }

    // Variabel .env
    public function environments()
    {
        return $this->hasMany(HostingEnvironment::class);
    }

    // Info Tagihan/Langganan
    public function billing()
    {
        return $this->hasOne(HostingBilling::class);
    }
}
