<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostingPayment extends Model
{
    use \App\Traits\HasHashid;

    protected $fillable = [
        'hosting_project_id', 'invoice_number', 'amount',
        'status', 'payment_method', 'paid_at'
    ];

    protected $casts = [
        'amount' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(HostingProject::class, 'hosting_project_id');
    }
}
