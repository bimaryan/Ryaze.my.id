<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdeChat extends Model
{
    use \App\Traits\HasHashid;

    protected $fillable = [
        'hosting_project_id', 'user_id', 'title',
    ];

    public function project()
    {
        return $this->belongsTo(HostingProject::class, 'hosting_project_id');
    }

    public function messages()
    {
        return $this->hasMany(IdeChatMessage::class)->orderBy('created_at');
    }
}
