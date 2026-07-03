<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostingBilling extends Model
{
    use \App\Traits\HasHashid;

    protected $fillable = [
        'user_id', 'hosting_project_id', 'plan_name', 'amount',
        'billing_cycle', 'next_due_date', 'status'
    ];

    public function project()
    {
        return $this->belongsTo(HostingProject::class, 'hosting_project_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
