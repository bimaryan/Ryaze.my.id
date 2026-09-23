<?php

namespace App\Models;

use App\Traits\HasHashid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechBadge extends Model
{
    use HasFactory, HasHashid;

    protected $table = 'tech_badges';

    protected $fillable = [
        'icon',
        'label',
        'color',
        'sort_order',
    ];
}
