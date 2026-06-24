<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class HostingProject extends Model
{
    use \App\Traits\HasHashid;

    protected $fillable = [
        'user_id', 'project_name', 'framework', 'repo_source',
        'branch', 'ryaze_domain', 'custom_domain', 'status', 'maintenance_mode', 'force_https', 'storage_limit_mb',
    ];

    protected $casts = [
        'maintenance_mode' => 'boolean',
        'force_https' => 'boolean',
    ];

    // Alias Hash ID untuk URL yang elegan
    

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

    public function payments()
    {
        return $this->hasMany(HostingPayment::class)->orderBy('created_at', 'desc');
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
