<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostingDomain extends Model
{
    use HasFactory, \App\Traits\HasHashid;

    protected $fillable = [
        'project_id',
        'domain_name',
        'ssl_status',
    ];

    public function project()
    {
        return $this->belongsTo(HostingProject::class);
    }
}
