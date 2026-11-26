<?php

namespace App\Models;

use App\Traits\HasHashid;
use Illuminate\Database\Eloquent\Model;

class HostingSshAccess extends Model
{
    use HasHashid;

    protected $fillable = [
        'hosting_project_id',
        'user_id',
        'slug',
        'ssh_user',
        'package',
        'docker_ip',
        'host_port',
        'tunnel_hostname',
        'status',
        'private_key',
        'ssh_password',
        'last_output',
        'provisioned_at',
    ];

    protected $casts = [
        'host_port' => 'integer',
        'provisioned_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(HostingProject::class, 'hosting_project_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Kunci privat disimpan terenkripsi di DB. */
    public function getPrivateKeyAttribute($value)
    {
        return $value ? decrypt($value) : null;
    }

    public function setPrivateKeyAttribute($value)
    {
        $this->attributes['private_key'] = $value ? encrypt($value) : null;
    }

    /** Password SSH dienkripsi dengan APP_KEY. */
    public function getSshPasswordAttribute($value)
    {
        return $value ? decrypt($value) : null;
    }

    public function setSshPasswordAttribute($value)
    {
        $this->attributes['ssh_password'] = $value ? encrypt($value) : null;
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }
}
