<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Mengelola "Published application routes" (ingress rules) pada tunnel
 * Cloudflare yang remotely-managed (dikelola lewat dashboard Zero Trust).
 *
 * Saat project hosting di-deploy, aplikasi hanya membuat DNS CNAME ke tunnel.
 * Itu TIDAK cukup — tunnel harus punya ingress rule yang memetakan hostname
 * ke service (nginx). Tanpa rule tersebut, request sampai ke tunnel tapi tidak
 * diteruskan, sehingga web menampilkan 502 Bad Gateway.
 *
 * API: PUT /accounts/{account_id}/cfd_tunnel/{tunnel_id}/configurations
 * Token yang dipakai harus scope akun-level (Zero Trust/Tunnel), bukan token
 * zone-scoped biasa.
 */
class CloudflareTunnelService
{
    private const API_BASE = 'https://api.cloudflare.com/client/v4';

    private ?string $accountId;

    private ?string $tunnelId;

    private ?string $apiToken;

    public function __construct()
    {
        $this->accountId = config('services.cloudflare_tunnel.account_id');
        $this->tunnelId = config('services.cloudflare_tunnel.tunnel_id');
        $this->apiToken = config('services.cloudflare_tunnel.api_token');
    }

    /**
     * Cek apakah konfigurasi tunnel lengkap (token + account + tunnel id).
     */
    public function isConfigured(): bool
    {
        return ! empty($this->accountId) && ! empty($this->tunnelId) && ! empty($this->apiToken);
    }

    /**
     * Ambil konfigurasi ingress tunnel saat ini dari Cloudflare.
     *
     * @return array<int, array{hostname: ?string, service: string, originRequest?: array}>
     */
    public function getIngressRules(): ?array
    {
        if (! $this->isConfigured()) {
            Log::warning('[CloudflareTunnel] Konfigurasi tidak lengkap — abaikan manajemen route.');

            return null;
        }

        $resp = Http::withToken($this->apiToken)
            ->get("{$this->apiBasePath()}/configurations");

        if (! $resp->successful()) {
            Log::error('[CloudflareTunnel] Gagal mengambil konfigurasi tunnel', [
                'status' => $resp->status(),
                'body' => $resp->body(),
            ]);

            return null;
        }

        $ingress = $resp->json('result.config.ingress');

        return is_array($ingress) ? $ingress : [];
    }

    /**
     * Daftarkan (atau perbarui) ingress rule untuk hostname ke service target.
     *
     * @param  string  $hostname  Subdomain publik, mis. "app.ryaze.my.id".
     * @param  string  $service   URL origin, mis. "http://127.0.0.1:80".
     * @return bool True jika rule aktif untuk hostname tersebut.
     */
    public function registerRoute(string $hostname, string $service): bool
    {
        if (! $this->isConfigured()) {
            Log::warning("[CloudflareTunnel] Skip register route {$hostname} — konfigurasi tidak lengkap.");

            return false;
        }

        $ingress = $this->getIngressRules();
        if ($ingress === null) {
            return false;
        }

        // Hapus rule lama untuk hostname yang sama (update port/target).
        $ingress = array_values(array_filter(
            $ingress,
            fn ($rule): bool => ($rule['hostname'] ?? '') !== $hostname
        ));

        $newRule = ['hostname' => $hostname, 'service' => $service];

        // Sisipkan sebelum catch-all rule (rule terakhir tanpa hostname).
        $insertAt = count($ingress);
        for ($i = count($ingress) - 1; $i >= 0; $i--) {
            if (empty($ingress[$i]['hostname'])) {
                $insertAt = $i;
            } else {
                break;
            }
        }
        array_splice($ingress, $insertAt, 0, [$newRule]);

        return $this->putIngressRules($ingress, "register {$hostname} -> {$service}");
    }

    /**
     * Hapus ingress rule untuk hostname.
     */
    public function unregisterRoute(string $hostname): bool
    {
        if (! $this->isConfigured()) {
            Log::warning("[CloudflareTunnel] Skip unregister route {$hostname} — konfigurasi tidak lengkap.");

            return false;
        }

        $ingress = $this->getIngressRules();
        if ($ingress === null) {
            return false;
        }

        $filtered = array_values(array_filter(
            $ingress,
            fn ($rule): bool => ($rule['hostname'] ?? '') !== $hostname
        ));

        // Tidak ada yang berubah — tidak perlu PUT.
        if (count($filtered) === count($ingress)) {
            return true;
        }

        return $this->putIngressRules($filtered, "unregister {$hostname}");
    }

    /**
     * Simpan ulang daftar ingress rules ke Cloudflare.
     */
    private function putIngressRules(array $ingress, string $action): bool
    {
        // Cloudflare wajib punya minimal satu rule; pastikan ada catch-all.
        if (empty($ingress)) {
            $ingress = [['service' => 'http_status:404']];
        }

        $resp = Http::withToken($this->apiToken)
            ->put("{$this->apiBasePath()}/configurations", [
                'config' => ['ingress' => $ingress],
            ]);

        if (! $resp->successful()) {
            Log::error("[CloudflareTunnel] Gagal {$action}", [
                'status' => $resp->status(),
                'body' => $resp->body(),
            ]);

            return false;
        }

        Log::info("[CloudflareTunnel] Route updated: {$action}");

        return true;
    }

    private function apiBasePath(): string
    {
        return self::API_BASE."/accounts/{$this->accountId}/cfd_tunnel/{$this->tunnelId}";
    }
}
