<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentConfirmation extends Model
{
    use \App\Traits\HasHashid;

    public const STATUS_PENDING = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_REJECTED = 'rejected';

    public const GROUP_HOSTING = 'hosting';
    public const GROUP_JOKI = 'joki';
    public const GROUP_ALL = 'all';

    public const HOSTING_TYPES = [
        'hosting_subscription',
        'hosting_upgrade',
        'wallet_topup',
        'digital_product',
        'tip',
    ];

    public const JOKI_TYPES = ['joki_payment'];

    protected $fillable = [
        'order_number', 'order_type', 'amount', 'user_id', 'note',
        'channel', 'status', 'verified_by', 'verified_at', 'admin_notified_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'verified_at' => 'datetime',
        'admin_notified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /** Area admin yang menangani jenis order ini. */
    public function adminRole(): string
    {
        return match ($this->order_type) {
            'joki_payment' => 'admin_joki',
            default => 'admin_hosting',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_VERIFIED => 'ui-badge ui-badge-success',
            self::STATUS_REJECTED => 'ui-badge ui-badge-danger',
            default => 'ui-badge ui-badge-warning',
        };
    }
}
