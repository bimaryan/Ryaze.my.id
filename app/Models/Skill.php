<?php

namespace App\Models;

use App\Traits\HasHashid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory, HasHashid;

    protected $table = 'skills';

    protected $fillable = [
        'skill_group_id',
        'name',
        'percentage',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'percentage' => 'integer',
        'is_active' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(SkillGroup::class, 'skill_group_id');
    }
}
