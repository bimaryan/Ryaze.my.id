<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi untuk melihat riwayat pesanan klien
    public function clientOrders()
    {
        return $this->hasMany(JokiOrder::class, 'client_id');
    }

    // Relasi untuk melihat proyek apa saja yang sedang dikerjakan admin/dev
    public function workerOrders()
    {
        return $this->hasMany(JokiOrder::class, 'worker_id');
    }

    public function hostingProjects()
    {
        return $this->hasMany(HostingProject::class, 'user_id');
    }
}
