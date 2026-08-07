<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Password diambil dari env agar tidak pernah tertulis plaintext di repo.
     * Default hanya untuk dev; WAJIB diset di .env produksi.
     */
    public function run(): void
    {
        $default = 'Password123!';

        $users = [
            [
                'name' => 'Bima Ryan Alfarizi',
                'email' => 'superadmin@ryaze.my.id',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make(env('SEED_SUPERADMIN_PASSWORD', $default)),
                'role' => 'superadmin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Admin Joki',
                'email' => 'admin.joki@ryaze.my.id',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make(env('SEED_ADMIN_JOKI_PASSWORD', $default)),
                'role' => 'admin_joki',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Admin Hosting',
                'email' => 'admin.hosting@ryaze.my.id',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make(env('SEED_ADMIN_HOSTING_PASSWORD', $default)),
                'role' => 'admin_hosting',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Dea',
                'email' => 'dea@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make(env('SEED_USER_JOKI_PASSWORD', $default)),
                'role' => 'user_joki',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Client Hosting',
                'email' => 'client.hosting@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make(env('SEED_USER_HOSTING_PASSWORD', $default)),
                'role' => 'user_hosting',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Insert semua data array ke tabel users
        User::insert($users);
    }
}
