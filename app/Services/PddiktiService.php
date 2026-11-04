<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PddiktiService
{
    /** Situs resmi PDDIKTI — datanya dibaca dari endpoint JSON internal SPA-nya. */
    private const OFFICIAL_BASE = 'https://pddikti.kemdiktisaintek.go.id';

    /** Vonis ditahan sebentar supaya situs resmi tidak dihujani request yang sama. */
    private const VERDICT_CACHE_SECONDS = 1800;

    /** Label status yang berarti mahasiswa sudah tidak aktif. */
    private const INACTIVE_TOKENS = [
        'TIDAK AKTIF', 'NONAKTIF', 'LULUS', 'MUTASI', 'PINDAH', 'PUTUS',
        'DIKELUARKAN', 'DIBERHENTIKAN', 'BERHENTI', 'UNDUR', 'MENINGGAL', 'CUTI', 'DROP',
    ];

    /**
     * Base URL sumber data (bisa dialihkan lewat Settings tanpa deploy ulang).
     */
    public function baseUrl(): string
    {
        $base = trim((string) Setting::val('pddikti_api_base', self::OFFICIAL_BASE));

        return rtrim($base !== '' ? $base : self::OFFICIAL_BASE, '/');
    }

    /**
     * Normalisasi teks nama untuk pembandingan (huruf kecil, buang tanda baca).
     */
    public static function normalizeName(string $name): string
    {
        return trim(preg_replace('/\s+/', ' ', preg_replace('/[^a-z0-9]+/', ' ', strtolower($name)) ?? ''));
    }

    public static function normalizeNim(string $nim): string
    {
        return strtoupper(preg_replace('/[^a-z0-9]/i', '', $nim) ?? '');
    }

    /**
     * Seberapa cocok nama yang diinput dengan data PDDIKTI.
     * Lolos bila persentase similar_text >= 75% ATAU semua token nama input
     * ada di dalam nama PDDIKTI (agar gelar/awalan seperti "M." tidak gugur).
     */
    public static function nameMatches(string $input, string $official): bool
    {
        $a = self::normalizeName($input);
        $b = self::normalizeName($official);

        if ($a === '' || $b === '') {
            return false;
        }

        similar_text($a, $b, $percent);
        if ($percent >= 75) {
            return true;
        }

        $tokensA = collect(explode(' ', $a))->filter()->values();
        $tokensB = collect(explode(' ', $b))->filter()->values();

        return $tokensA->isNotEmpty()
            && $tokensA->every(fn (string $t) => $tokensB->contains(fn (string $tb) => Str::startsWith($tb, $t) || Str::startsWith($t, $tb)));
    }

    /**
     * Cari mahasiswa di PDDIKTI. Toleran terhadap beberapa bentuk response
     * karena situs resmi pernah mengubah struktur beberapa kali.
     *
     * @return array<int, array>|null daftar mahasiswa, atau NULL bila requestnya gagal
     */
    public function searchMhs(string $keyword): ?array
    {
        $keyword = trim($keyword);
        if ($keyword === '') {
            return [];
        }

        $json = $this->get('/api/pencarian/mhs/'.rawurlencode($keyword));
        if (! is_array($json)) {
            return null;
        }

        $data = $json['data'] ?? null;

        foreach ([$data['mahasiswa'] ?? null, $data['datamahasiswa'] ?? null, $data['data']['mahasiswa'] ?? null] as $list) {
            if (is_array($list) && $list !== []) {
                return array_values(array_filter($list, fn ($m) => is_array($m)));
            }
        }

        // Bentuk resmi saat ini: data langsung berupa list mahasiswa.
        $first = is_array($data) && $data !== [] ? reset($data) : null;
        if (is_array($first) && (array_key_exists('nim', $first) || array_key_exists('nama', $first))) {
            return array_values($data);
        }

        return [];
    }

    /**
     * Detail mahasiswa berdasarkan token id yang dikembalikan pencarian.
     */
    public function getStudentDetail(string $id): ?array
    {
        // Token id bersifat base64url: padding "=" harus dikirim literal. Versi
        // ter-encode (%3D%3D) ditolak situs resmi dengan 400 "invalid id".
        $json = $this->get('/api/detail/mhs/'.str_replace('%3D', '=', rawurlencode($id)));
        if (! is_array($json) || ! is_array($json['data'] ?? null)) {
            return null;
        }

        $data = $json['data'];
        if (is_array($data['mhs'] ?? null)) {
            $data = array_merge($data, $data['mhs']);
        } elseif (is_array($data['mahasiswa'] ?? null)) {
            $data = array_merge($data, $data['mahasiswa']);
        }

        return $data;
    }

    /**
     * Verifikasi mahasiswa aktif: NIM harus persis terdaftar di PDDIKTI,
     * statusnya bukan LULUS/nonaktif, dan nama yang diinput cocok.
     *
     * @return array{ok: bool, reason: string, message: string, record: array}
     */
    public function verify(string $nimInput, string $namaInput): array
    {
        $nim = self::normalizeNim($nimInput);

        if ($nim === '' || mb_strlen($nim) < 4) {
            return $this->failure('invalid_nim', 'Format NIM tidak valid. Masukkan NIM tanpa spasi atau tanda baca.');
        }

        // Versi key naik tiap logika verifikasi berubah, supaya verdict salah
        // yang tersimpan sebelum deploy tidak ikut dipakai lagi.
        $cacheKey = 'pddikti:verdict:v2:'.$nim.':'.md5(self::normalizeName($namaInput));
        $cached = Cache::get($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        $result = $this->runVerification($nim, $namaInput);

        // Kegagalan jaringan sengaja tidak di-cache agar user bisa langsung coba lagi.
        if ($result['reason'] !== 'api_error') {
            Cache::put($cacheKey, $result, self::VERDICT_CACHE_SECONDS);
        }

        return $result;
    }

    private function runVerification(string $nim, string $namaInput): array
    {
        $candidates = $this->searchMhs($nim);
        if ($candidates === null) {
            return $this->failure('api_error', 'Layanan verifikasi PDDIKTI sedang sibuk atau tidak dapat dihubungi. Coba lagi beberapa saat.');
        }

        // NIM hanya unik di dalam satu kampus: satu NIM yang sama bisa dipakai
        // puluhan mahasiswa beda kampus. Nama yang menentukan orang mana.
        $sameNim = collect($candidates)
            ->filter(fn ($m) => self::normalizeNim((string) ($m['nim'] ?? '')) === $nim)
            ->values();

        if ($sameNim->isEmpty()) {
            return $this->failure('not_found', 'NIM tidak ditemukan di PDDIKTI. Pastikan NIM benar dan kampus Anda sudah melaporkan data Anda.');
        }

        $people = $sameNim
            ->filter(fn ($m) => self::nameMatches($namaInput, (string) ($m['nama'] ?? '')))
            ->values();

        if ($people->isEmpty()) {
            // Nama mahasiswa lain sengaja tidak ditampilkan: data itu milik orang lain.
            return $this->failure('nama_mismatch', 'NIM dan nama tidak cocok dengan data PDDIKTI. Masukkan nama lengkap persis seperti yang tercatat di kampus Anda.');
        }

        // Orang yang sama bisa tercatat di beberapa kampus (pindah/alih bentuk).
        // Yang dicari adalah entri yang masih aktif, jadi periksa tiap kandidat
        // sampai ketemu, bukan cuma yang pertama.
        $inactive = null;
        $detailFailed = false;

        foreach ($people->take(5) as $person) {
            $pddiktiId = (string) ($person['id'] ?? '');
            $detail = $pddiktiId !== '' ? $this->getStudentDetail($pddiktiId) : null;

            if (! is_array($detail)) {
                $detailFailed = true;

                continue;
            }

            $status = $this->detectStatus($detail);
            $record = [
                'pddikti_id' => $pddiktiId ?: null,
                'nama_pddikti' => (string) ($detail['nama'] ?? $person['nama'] ?? ''),
                'nama_pt' => (string) ($detail['nama_pt'] ?? $detail['pt'] ?? $person['nama_pt'] ?? ''),
                'nama_prodi' => (string) ($detail['nama_prodi'] ?? $detail['prodi'] ?? $person['nama_prodi'] ?? ''),
                'jenjang' => (string) ($detail['nama_jenjang'] ?? $detail['jenjang'] ?? ''),
                'pddikti_status' => $status['label'],
            ];

            if ($status['active']) {
                return [
                    'ok' => true,
                    'reason' => 'verified',
                    'message' => 'Verifikasi mahasiswa aktif berhasil.',
                    'record' => $record,
                ];
            }

            $inactive = $inactive ?? $record;
        }

        if ($inactive) {
            return [
                'ok' => false,
                'reason' => 'not_active',
                'message' => "Status mahasiswa di PDDIKTI adalah \"{$inactive['pddikti_status']}\", sehingga tidak memenuhi syarat mahasiswa aktif.",
                'record' => $inactive,
            ];
        }

        // Tidak ada detail yang berhasil diambil: gagal saja daripada memberi promo
        // ke orang yang statusnya tidak diketahui.
        if ($detailFailed) {
            return $this->failure('api_error', 'Layanan verifikasi PDDIKTI sedang sibuk atau tidak dapat dihubungi. Coba lagi beberapa saat.');
        }

        return $this->failure('not_active', 'Data kampus Anda belum menampilkan status mahasiswa aktif. Hubungi bagian akademik kampus Anda.');
    }

    /**
     * @return array{ok: bool, reason: string, message: string, record: array}
     */
    private function failure(string $reason, string $message): array
    {
        return ['ok' => false, 'reason' => $reason, 'message' => $message, 'record' => []];
    }

    /**
     * Tebakan status berasal dari beberapa field umum. Situs resmi memakai
     * "status_saat_ini" berisi "<label>-<periode>", misalnya "Lulus-2024/2025 Genap".
     *
     * @return array{active: bool, label: string}
     */
    public function detectStatus(?array $detail): array
    {
        $active = ['label' => 'AKTIF', 'active' => true];

        if (! $detail) {
            return $active;
        }

        foreach (['status_saat_ini', 'status_kuliah', 'status_mahasiswa', 'mhs_status', 'status'] as $key) {
            $value = $detail[$key] ?? null;
            if (is_array($value)) {
                $value = $value['nama'] ?? null;
            }
            if (! is_string($value) || trim($value) === '') {
                continue;
            }

            $label = self::statusLabel($value);

            if (self::isInactiveLabel($label)) {
                return ['active' => false, 'label' => $label];
            }

            if (str_contains(self::compactLabel($label), 'AKTIF')) {
                return $active;
            }
        }

        $riwayat = $detail['riwayat_pendidikan'] ?? null;
        if (is_array($riwayat) && $riwayat !== []) {
            $last = collect($riwayat)->filter(fn ($r) => is_array($r))->last();
            $tanggalKeluar = (string) ($last['tanggal_keluar'] ?? '');
            if ($tanggalKeluar !== '' && $tanggalKeluar !== '0000-00-00') {
                return ['active' => false, 'label' => 'LULUS'];
            }
        }

        return $active;
    }

    /**
     * "Non-Aktif-2026/2027 Ganjil" -> "NON-AKTIF". Yang dibuang hanya bagian
     * periodenya (mulai tahun 4 digit), bukan semua tanda hubung: memotong di
     * strip pertama akan menyisakan "NON" yang tidak dikenali sebagai nonaktif.
     */
    private static function statusLabel(string $value): string
    {
        $label = trim((string) preg_replace('/-?\s*\d{4}[\/\-].*$/s', '', trim($value)), ' -');

        return strtoupper(trim((string) preg_replace('/\s+/', ' ', $label)));
    }

    /** Bandingkan tanpa spasi/strip supaya "NON-AKTIF" sama dengan "NONAKTIF". */
    private static function compactLabel(string $label): string
    {
        return str_replace(['-', '_', ' ', '/'], '', $label);
    }

    private static function isInactiveLabel(string $label): bool
    {
        $compact = self::compactLabel($label);

        foreach (self::INACTIVE_TOKENS as $token) {
            if ($token !== '' && str_contains($compact, self::compactLabel($token))) {
                return true;
            }
        }

        return false;
    }

    /**
     * GET ke endpoint JSON resmi dengan header mirip browser (situs menolak
     * request tanpa UA/Referer). Mengembalikan array JSON bila sukses,
     * NULL bila gagal (5xx, timeout, dsb).
     */
    private function get(string $path): ?array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36',
                'Accept' => 'application/json, text/plain, */*',
                'Accept-Language' => 'en-US,en;q=0.9,id;q=0.8',
                'Referer' => $this->baseUrl().'/pencarian-spesifik-mahasiswa',
            ])
                ->timeout(25)
                ->retry(2, 300, throw: false)
                ->get($this->baseUrl().$path);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('PDDIKTI API non-2xx', ['path' => $path, 'status' => $response->status()]);
        } catch (\Throwable $e) {
            Log::warning('PDDIKTI API error', ['path' => $path, 'error' => $e->getMessage()]);
        }

        return null;
    }
}
