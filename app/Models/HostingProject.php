<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class HostingProject extends Model
{
    use \App\Traits\HasHashid;

    protected $fillable = [
        'user_id', 'project_name', 'framework', 'repo_source',
        'branch', 'source_type', 'ryaze_domain', 'custom_domain', 'status', 'maintenance_mode', 'force_https', 'storage_limit_mb', 'is_under_attack', 'dev_mode', 'dev_port', 'dev_pid',
    ];

    protected $casts = [
        'maintenance_mode' => 'boolean',
        'force_https' => 'boolean',
        'is_under_attack' => 'boolean',
        'dev_mode' => 'boolean',
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

    public function domains()
    {
        return $this->hasMany(HostingDomain::class, 'project_id');
    }

    public function crons()
    {
        return $this->hasMany(HostingCron::class, 'project_id');
    }
}
