<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use \App\Traits\HasHashid;

    protected $fillable = [
        'code',
        'discount_amount',
        'discount_percentage',
        'max_uses',
        'uses',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];
    
    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }
        
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        
        if ($this->max_uses !== null && $this->uses >= $this->max_uses) {
            return false;
        }
        
        return true;
    }
    
    public function calculateDiscount($amount)
    {
        if ($this->discount_percentage) {
            $discount = ($amount * $this->discount_percentage) / 100;
            return min($amount, $discount);
        }
        
        if ($this->discount_amount) {
            return min($amount, $this->discount_amount);
        }
        
        return 0;
    }
}
