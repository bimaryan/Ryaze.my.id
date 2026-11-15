<?php

namespace App\Support;

class ErrorPages
{
    public static function forStatus(int $status): ?array
    {
        return match ($status) {
            401 => [
                'code' => '401',
                'title' => 'Unauthorized',
                'message' => 'Autentikasi diperlukan.',
                'description' => 'Kamu belum masuk atau sesi kamu sudah berakhir. Masuk kembali untuk melanjutkan ke halaman ini.',
                'next' => 'Masuk kembali dengan akun yang memiliki akses.',
                'primary' => ['label' => 'Masuk', 'href' => '/login', 'icon' => 'fa-right-to-bracket', 'action' => 'link'],
                'secondary' => ['label' => 'Kembali', 'action' => 'back'],
            ],
            403 => [
                'code' => '403',
                'title' => 'Forbidden',
                'message' => 'Akses ditolak.',
                'description' => 'Akun kamu tidak punya izin untuk membuka area ini. Kalau kamu merasa ini keliru, hubungi administrator.',
                'next' => 'Gunakan akun dengan peran yang sesuai atau hubungi administrator.',
                'primary' => ['label' => 'Beranda', 'href' => '/', 'icon' => 'fa-arrow-right', 'action' => 'link'],
                'secondary' => ['label' => 'Kembali', 'action' => 'back'],
            ],
            404 => [
                'code' => '404',
                'title' => 'Not Found',
                'message' => 'Halaman tidak ditemukan.',
                'description' => 'URL ini tidak cocok dengan halaman mana pun. Mungkin alamatnya salah ketik, atau halaman sudah dipindahkan.',
                'next' => 'Periksa ejaan URL atau mulai lagi dari beranda.',
                'primary' => ['label' => 'Beranda', 'href' => '/', 'icon' => 'fa-arrow-right', 'action' => 'link'],
                'secondary' => ['label' => 'Kembali', 'action' => 'back'],
            ],
            419 => [
                'code' => '419',
                'title' => 'Page Expired',
                'message' => 'Sesi kedaluwarsa.',
                'description' => 'Token keamanan halaman ini sudah tidak berlaku. Muat ulang halaman, lalu kirim ulang formulir. Data kamu tidak hilang.',
                'next' => 'Muat ulang halaman sebelum mengirim formulir lagi.',
                'primary' => ['label' => 'Muat ulang', 'icon' => 'fa-rotate-right', 'action' => 'reload'],
                'secondary' => ['label' => 'Beranda', 'href' => '/', 'action' => 'link'],
            ],
            429 => [
                'code' => '429',
                'title' => 'Too Many Requests',
                'message' => 'Terlalu banyak permintaan.',
                'description' => 'Kamu mengakses halaman ini terlalu cepat. Tunggu beberapa detik, lalu coba lagi. Batas ini melindungi layanan dari lonjakan trafik.',
                'next' => 'Tunggu beberapa detik lalu coba lagi.',
                'primary' => ['label' => 'Coba lagi', 'icon' => 'fa-rotate-right', 'action' => 'reload'],
                'secondary' => ['label' => 'Beranda', 'href' => '/', 'action' => 'link'],
            ],
            500 => [
                'code' => '500',
                'title' => 'Server Error',
                'message' => 'Terjadi kesalahan server.',
                'description' => 'Ada yang gagal di sisi server saat memproses permintaan ini. Tim teknis sudah menerima catatannya dan sedang memeriksa.',
                'next' => 'Coba muat ulang beberapa saat lagi. Jika tetap gagal, hubungi tim Ryaze.',
                'primary' => ['label' => 'Beranda', 'href' => '/', 'icon' => 'fa-arrow-right', 'action' => 'link'],
                'secondary' => ['label' => 'Muat ulang', 'icon' => 'fa-rotate-right', 'action' => 'reload'],
            ],
            503 => [
                'code' => '503',
                'title' => 'Service Unavailable',
                'message' => 'Layanan sedang maintenance.',
                'description' => 'Sistem dijeda sementara untuk pemeliharaan terjadwal. Semua data aman dan layanan akan kembali seperti biasa.',
                'next' => 'Kembali lagi beberapa menit lagi.',
                'primary' => ['label' => 'Beranda', 'href' => '/', 'icon' => 'fa-arrow-right', 'action' => 'link'],
                'secondary' => ['label' => 'Muat ulang', 'icon' => 'fa-rotate-right', 'action' => 'reload'],
            ],
            default => null,
        };
    }

    public static function suspended(): array
    {
        return [
            'code' => 'SUSPENDED',
            'title' => 'Suspended',
            'message' => 'Layanan hosting ditangguhkan.',
            'description' => 'Website ini tidak dapat diakses karena layanan hostingnya sedang ditangguhkan. Biasanya karena tagihan yang belum lunas atau masa aktif paket yang sudah berakhir.',
            'next' => 'Selesaikan tagihan atau perpanjang paket lewat panel.',
            'primary' => ['label' => 'Masuk ke panel', 'href' => '/login', 'icon' => 'fa-right-to-bracket', 'action' => 'link'],
            'secondary' => ['label' => 'Beranda', 'href' => '/', 'action' => 'link'],
            'causes' => [
                ['icon' => 'fa-file-invoice', 'title' => 'Tagihan belum lunas', 'desc' => 'Langganan otomatis disuspend saat jatuh tempo terlewat.'],
                ['icon' => 'fa-calendar-xmark', 'title' => 'Masa aktif berakhir', 'desc' => 'Paket hosting perlu diperpanjang untuk melanjutkan layanan.'],
                ['icon' => 'fa-shield-halved', 'title' => 'Pelanggaran ketentuan', 'desc' => 'Konten atau aktivitas yang melanggar Syarat & Ketentuan.'],
            ],
        ];
    }
}
