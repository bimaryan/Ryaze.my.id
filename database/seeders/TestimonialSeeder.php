<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'name' => 'Rendi Hidayat',
                'role' => 'Klien Joki',
                'company' => 'SafetalkAI Mobile',
                'content' => 'Pesan aplikasi SafetalkAI versi mobile dan backend FastAPI-nya di sini. Komunikasi jelas, milestone tepat waktu, dan hasilnya sesuai brief. Recommended banget buat yang butuh developer cepat tapi tetap rapi kodenya.',
                'rating' => 5,
                'sort_order' => 1,
            ],
            [
                'name' => 'Laysha',
                'role' => 'Klien Joki',
                'company' => 'Safetalk Web',
                'content' => 'Joki pembuatan aplikasi web Safetalk berjalan mulus dari awal sampai deploy. Revisi cepat ditanggapi dan komunikasinya enak. Hasil akhirnya clean dan gampang dikembangkan lagi.',
                'rating' => 5,
                'sort_order' => 2,
            ],
            [
                'name' => 'Hisyam',
                'role' => 'Klien Joki',
                'company' => 'Top Up Game',
                'content' => 'Website top up game saya dikerjakan dari nol dan hasilnya memuaskan. Tampilan modern, proses pembayaran lancar, dan pengerjaannya sesuai deadline. Puas sama kerjanya!',
                'rating' => 5,
                'sort_order' => 3,
            ],
            [
                'name' => 'Fahreza',
                'role' => 'Klien Joki',
                'company' => 'E-Hajatan',
                'content' => 'Aplikasi web E-Hajatan dikerjakan dengan detail sampai fitur RSVP-nya jalan sempurna. Debugging cepat dan komunikatif. Terima kasih, project saya selesai tanpa drama.',
                'rating' => 5,
                'sort_order' => 4,
            ],
            [
                'name' => 'Agung',
                'role' => 'Klien Joki',
                'company' => 'Band & Cafe',
                'content' => 'Dipercaya bikin aplikasi web untuk manajemen band dan cafe saya. Paham kebutuhan bisnis, hasilnya profesional, dan support setelah serah terima juga oke. Bakal pesan lagi untuk project berikutnya.',
                'rating' => 5,
                'sort_order' => 5,
            ],
        ];

        foreach ($testimonials as $t) {
            Testimonial::updateOrCreate(
                ['name' => $t['name'], 'company' => $t['company']],
                $t + ['is_active' => true]
            );
        }
    }
}
