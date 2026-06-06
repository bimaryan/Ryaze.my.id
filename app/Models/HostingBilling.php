<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostingBilling extends Model
{
    protected $fillable = [
        'hosting_project_id', 'plan_name', 'amount',
        'billing_cycle', 'next_due_date', 'status'
    ];

    public function project()
    {
        return $this->belongsTo(HostingProject::class, 'hosting_project_id');
    }
}
