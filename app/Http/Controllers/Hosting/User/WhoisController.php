<?php

namespace App\Http\Controllers\Hosting\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WhoisController extends Controller
{
    /**
     * Tampilkan halaman WHOIS Lookup.
     */
    public function index()
    {
        return view('pages.hosting.user.whois');
    }

    /**
     * Cari informasi domain via RDAP (Registration Data Access Protocol).
     *
     * RDAP adalah pengganti resmi WHOIS dari ICANN yang berbasis JSON.
     * Bootstrap server https://rdap.org/domain/{domain} akan otomatis
     * me-redirect ke server RDAP yang otoritatif untuk TLD terkait,
     * jadi tidak perlu API key atau daftar server WHOIS per-TLD.
     */
    public function lookup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'domain' => 'required|string|min:3|max:253',
        ]);

        $domain = $this->normalizeDomain($validated['domain']);

        if ($domain === '' || ! $this->isValidDomain($domain)) {
            return response()->json([
                'success' => false,
                'message' => 'Format domain tidak valid. Contoh: example.com atau ryaze.my.id',
            ], 422);
        }

        try {
            $response = Http::withHeaders(['Accept' => 'application/rdap+json'])
                ->timeout(15)
                ->get("https://rdap.org/domain/{$domain}");

            // 404 dari RDAP = domain belum terdaftar (available)
            if ($response->status() === 404) {
                return response()->json([
                    'success' => true,
                    'registered' => false,
                    'domain' => $domain,
                    'message' => "Domain {$domain} terlihat belum terdaftar (available).",
                ]);
            }

            if (! $response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => "Server RDAP merespons dengan error (HTTP {$response->status()}). Coba lagi nanti.",
                ], 502);
            }

            $data = $response->json();

            return response()->json([
                'success' => true,
                'registered' => true,
                'domain' => $data['ldhName'] ?? strtoupper($domain),
                'whois' => $this->parseRdap($data),
                'dns' => $this->lookupDns($domain),
            ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung ke server RDAP. Periksa koneksi internet lalu coba lagi.',
            ], 504);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data domain: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Normalisasi input: buang protocol, path, www., spasi, dan lowercase.
     */
    private function normalizeDomain(string $input): string
    {
        $input = trim($input);
        $input = preg_replace('#^https?://#i', '', $input);
        $input = preg_replace('#^ftp://#i', '', $input);
        $input = ltrim($input, '@');

        // Ambil hanya bagian domain (sebelum path/query pertama)
        $parts = preg_split('~[/?#]~', $input);
        $input = $parts[0] ?? '';

        $input = Str::lower(trim($input, " \t\n\r\0\x0B."));
        $input = preg_replace('/^www\./', '', $input);

        return $input;
    }

    /**
     * Validasi nama domain sesuai RFC 1123 (label huruf/angka/hyphen,
     * max 63 char per label, keseluruhan max 253 char).
     */
    private function isValidDomain(string $domain): bool
    {
        if ($domain === '' || strlen($domain) > 253) {
            return false;
        }

        // Harus punya minimal satu titik (domain + TLD)
        if (! str_contains($domain, '.')) {
            return false;
        }

        foreach (explode('.', $domain) as $label) {
            if ($label === '' || strlen($label) > 63) {
                return false;
            }
            // Hanya boleh a-z, 0-9, dan hyphen (tidak di awal/akhir label)
            if (! preg_match('/^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/', $label)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Ekstrak field penting dari response RDAP menjadi struktur flat
     * yang siap ditampilkan di UI.
     */
    private function parseRdap(array $data): array
    {
        $events = collect($data['events'] ?? [])
            ->mapWithKeys(fn ($e) => [$e['eventAction'] => $e['eventDate'] ?? null])
            ->all();

        // Cari entity registrar & abuse contact di antara entities
        $registrar = null;
        $abuse = ['email' => null, 'phone' => null];

        foreach ($data['entities'] ?? [] as $entity) {
            $roles = $entity['roles'] ?? [];

            if (in_array('registrar', $roles, true)) {
                $registrar = $this->extractVcardField($entity, 'fn');
            }

            if (in_array('abuse', $roles, true)) {
                $abuse['email'] = $this->extractVcardField($entity, 'email');
                $abuse['phone'] = $this->extractVcardField($entity, 'tel');
            }
        }

        $nameservers = collect($data['nameservers'] ?? [])
            ->map(fn ($ns) => Str::lower($ns['ldhName'] ?? ''))
            ->filter()
            ->values()
            ->all();

        $secureDns = $data['secureDNS'] ?? [];

        return [
            'registrar' => $registrar,
            'abuse' => $abuse,
            'registered_at' => $events['registration'] ?? null,
            'expires_at' => $events['expiration'] ?? null,
            'updated_at' => $events['last changed'] ?? null,
            'status' => $data['status'] ?? [],
            'nameservers' => $nameservers,
            'dnssec' => [
                'enabled' => (bool) ($secureDns['delegationSigned'] ?? false),
                'key_data' => $secureDns['keyData'] ?? [],
            ],
        ];
    }

    /**
     * Ambil nilai field dari vcardArray entity RDAP.
     * Format: ["vcard", [ ["fn", [], "text", "MarkMonitor Inc."], ... ]]
     */
    private function extractVcardField(?array $entity, string $field): ?string
    {
        if (! $entity || ! isset($entity['vcardArray'][1])) {
            return null;
        }

        foreach ($entity['vcardArray'][1] as $vcard) {
            if (($vcard[0] ?? null) === $field) {
                return $vcard[3] ?? null;
            }
        }

        return null;
    }

    /**
     * Lookup record DNS (A, AAAA, MX, NS, TXT, CNAME) secara native
     * menggunakan resolver sistem. Gagal gracefully jika resolver tidak
     * merespons — info RDAP tetap ditampilkan.
     */
    private function lookupDns(string $domain): array
    {
        $records = [];
        $types = [
            'A' => DNS_A,
            'AAAA' => DNS_AAAA,
            'CNAME' => DNS_CNAME,
            'MX' => DNS_MX,
            'NS' => DNS_NS,
            'TXT' => DNS_TXT,
        ];

        foreach ($types as $label => $type) {
            $result = @dns_get_record($domain, $type);

            if ($result === false) {
                continue;
            }

            foreach ($result as $r) {
                $records[$label][] = match ($label) {
                    'MX' => isset($r['pri'])
                        ? ['host' => $r['target'] ?? '', 'priority' => $r['pri']]
                        : ['host' => $r['target'] ?? ''],
                    'CNAME', 'NS' => ['host' => $r['target'] ?? ''],
                    'TXT' => ['value' => $r['txt'] ?? ''],
                    default => ['ip' => $r['ip'] ?? ''],
                };
            }
        }

        return $records;
    }
}
