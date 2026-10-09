<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostingDomain extends Model
{
    use HasFactory, \App\Traits\HasHashid;

    protected $fillable = [
        'user_id',
        'project_id',
        'domain_name',
        'ssl_status',
        'cf_zone_id',
        'nameservers',
    ];

    protected $casts = [
        'nameservers' => 'array',
        'project_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(HostingProject::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Domain yang belum dikaitkan ke project mana pun. */
    public function scopeUnassigned(Builder $query): Builder
    {
        return $query->whereNull('project_id');
    }

    /** Normalisasi input user: huruf kecil, tanpa skema, tanpa path/slash. */
    public static function normalizeName(string $domain): string
    {
        $domain = strtolower(trim($domain));
        $domain = preg_replace('#^[a-z]+://#', '', $domain) ?? $domain;
        $domain = explode('/', $domain)[0];
        $domain = rtrim($domain, '.');

        return trim($domain);
    }

    /** Hostname turunan yang perlu didaftarkan ke tunnel. */
    public function hostnames(): array
    {
        return array_values(array_unique(array_filter([
            $this->domain_name,
            'www.'.$this->domain_name,
        ])));
    }
}