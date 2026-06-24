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
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Bima Ryan Alfarizi',
                'email' => 'superadmin@ryaze.my.id',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('@Dearyz2329'),
                'role' => 'superadmin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Admin Joki',
                'email' => 'admin.joki@ryaze.my.id',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('@Dearyz2329'),
                'role' => 'admin_joki',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Admin Hosting',
                'email' => 'admin.hosting@ryaze.my.id',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('@Dearyz2329'),
                'role' => 'admin_hosting',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // [
            //     'name' => 'Dea',
            //     'email' => 'dea@gmail.com',
            //     'email_verified_at' => Carbon::now(),
            //     'password' => Hash::make('User123!@#'),
            //     'role' => 'user_joki',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            // [
            //     'name' => 'Client Hosting',
            //     'email' => 'client.hosting@gmail.com',
            //     'email_verified_at' => Carbon::now(),
            //     'password' => Hash::make('User123!@#'),
            //     'role' => 'user_hosting',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
        ];

        // Insert semua data array ke tabel users
        User::insert($users);
    }
}
