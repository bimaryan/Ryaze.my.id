<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostingEnvironment extends Model
{
    protected $fillable = [
        'hosting_project_id', 'env_key', 'env_value'
    ];

    public function project()
    {
        return $this->belongsTo(HostingProject::class, 'hosting_project_id');
    }
}
