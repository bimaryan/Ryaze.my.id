<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentVerification extends Model
{
    protected $fillable = [
        'user_id', 'nim', 'pddikti_id', 'nama_input', 'nama_pddikti',
        'nama_pt', 'nama_prodi', 'jenjang', 'pddikti_status',
        'ip_address', 'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Ubah rawan: 1 NIM hanya boleh diklaim 1 akun selamanya.
     */
    public static function nimTaken(string $nim): bool
    {
        return self::where('nim', $nim)->exists();
    }
}
