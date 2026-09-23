<?php

namespace App\Models;

use App\Traits\HasHashid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillGroup extends Model
{
    use HasFactory, HasHashid;

    protected $table = 'skill_groups';

    protected $fillable = [
        'label',
        'icon',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function skills()
    {
        return $this->hasMany(Skill::class)->orderBy('sort_order');
    }
}
