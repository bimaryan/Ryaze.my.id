<?php

namespace Database\Seeders;

use App\Models\JokiService;
use Illuminate\Database\Seeder;

class JokiServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Tugas Akhir / Skripsi (Web)',
                'slug' => 'tugas-akhir-skripsi-web',
                'description' => 'Pembuatan sistem informasi untuk keperluan TA/Skripsi menggunakan Laravel/PHP.',
                'base_price' => 1500000,
                'is_active' => true,
            ],
            [
                'name' => 'Custom Web App (SaaS, ERP, POS)',
                'slug' => 'custom-web-app',
                'description' => 'Pembuatan aplikasi web kustom skala menengah hingga besar.',
                'base_price' => 3000000,
                'is_active' => true,
            ],
            [
                'name' => 'Website Company Profile',
                'slug' => 'website-company-profile',
                'description' => 'Pembuatan website profil perusahaan yang responsif dan elegan.',
                'base_price' => 800000,
                'is_active' => true,
            ],
            [
                'name' => 'Game Development (Unity)',
                'slug' => 'game-development-unity',
                'description' => 'Pembuatan game 2D/3D menggunakan Unity Engine.',
                'base_price' => 2000000,
                'is_active' => true,
            ],
            [
                'name' => 'Roblox Studio (Luau Scripting)',
                'slug' => 'roblox-studio-luau',
                'description' => 'Pembuatan sistem, simulasi, atau game di Roblox Studio.',
                'base_price' => 500000,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            JokiService::create($service);
        }
    }
}
