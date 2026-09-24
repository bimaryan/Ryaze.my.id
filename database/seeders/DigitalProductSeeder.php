<?php

namespace Database\Seeders;

use App\Models\DigitalProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DigitalProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Template Landing Page Laravel',
                'slug' => 'template-landing-page-laravel',
                'description' => 'Starter landing page Laravel + Blade + Tailwind siap deploy. Sudah termasuk dark mode, section hero/services/contact, dan struktur kode yang rapi untuk dikembangkan jadi company profile.',
                'price' => 49000,
                'file_name' => 'template-landing-page-laravel.zip',
            ],
            [
                'name' => 'E-Book: Tips Full-Stack Laravel',
                'slug' => 'ebook-tips-fullstack-laravel',
                'description' => 'Kumpulan tips praktis full-stack development dengan Laravel — arsitektur folder, performance tuning, security checklist, dan pola integrasi payment gateway.',
                'price' => 29000,
                'file_name' => 'ebook-tips-fullstack-laravel.pdf',
            ],
            [
                'name' => 'Paket 50+ UI Component Tailwind',
                'slug' => 'paket-ui-component-tailwind',
                'description' => 'Kumpulan 50+ komponen UI modern berbasis Tailwind CSS: navbar, pricing table, testimonial, form, modal, dll. Copy-paste langsung dipakai di project apa pun.',
                'price' => 39000,
                'file_name' => 'paket-ui-component-tailwind.zip',
            ],
            [
                'name' => 'Skrip Anti-Bloat Windows 11',
                'slug' => 'skrip-anti-bloat-windows-11',
                'description' => 'PowerShell script untuk membersihkan Windows 11 dari bloatware bawaan, mematikan telemetry, dan mengoptimalkan performa — aman & bisa di-rollback.',
                'price' => 15000,
                'file_name' => 'skrip-anti-bloat-windows-11.zip',
            ],
            [
                'name' => 'Notion Template: Konten Creator',
                'slug' => 'notion-template-konten-creator',
                'description' => 'Template Notion untuk manajemen konten: content calendar, ide bank, SOP produksi, dan tracker performa — dipakai untuk multi-platform (YouTube, TikTok, IG).',
                'price' => 25000,
                'file_name' => 'notion-template-konten-creator.pdf',
            ],
        ];

        Storage::disk('local')->makeDirectory('digital-products/files');

        foreach ($products as $p) {
            $filePath = 'digital-products/files/' . $p['file_name'];
            if (!Storage::disk('local')->exists($filePath)) {
                Storage::disk('local')->put(
                    $filePath,
                    "Placeholder file untuk seeder: {$p['name']}\nGanti file asli lewat admin Produk Digital.\n"
                );
            }

            DigitalProduct::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'name' => $p['name'],
                    'description' => $p['description'],
                    'price' => $p['price'],
                    'file_path' => $filePath,
                    'file_name' => $p['file_name'],
                    'download_count' => 0,
                    'is_active' => true,
                ]
            );
        }
    }
}
