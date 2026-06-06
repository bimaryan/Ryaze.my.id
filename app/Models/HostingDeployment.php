<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostingDeployment extends Model
{
    protected $fillable = [
        'hosting_project_id', 'commit_hash', 'commit_message',
        'build_logs', 'status', 'deployed_at'
    ];

    public function project()
    {
        return $this->belongsTo(HostingProject::class, 'hosting_project_id');
    }
}
