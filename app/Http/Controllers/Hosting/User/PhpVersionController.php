<?php

namespace App\Http\Controllers\Hosting\User;

use App\Http\Controllers\Controller;
use App\Models\HostingProject;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;

class PhpVersionController extends Controller
{
    /**
     * Ambil daftar versi PHP yang tersedia dari 1Panel API
     */
    public function availableVersions(Request $request, string $hashid): JsonResponse
    {
        try {
            $project = $this->getProject($hashid);
            if (! $project) {
                return response()->json(['error' => 'Project not found'], 404);
            }

            // Ambil versi dari 1Panel API
            $versionsFromApi = $this->fetchPhpVersionsFrom1Panel();

            if (empty($versionsFromApi)) {
                // Jika API gagal, kita beri response error tapi tetap 200 agar frontend tidak 500
                return response()->json([
                    'error' => 'Tidak dapat mengambil daftar versi PHP dari 1Panel. Periksa API Key dan URL.',
                    'versions' => [],
                    'project_version' => $project->php_version,
                ]);
            }

            // Urutkan dari versi tertinggi ke terendah
            usort($versionsFromApi, 'version_compare');
            $versionsFromApi = array_reverse($versionsFromApi);

            $versions = [];
            foreach ($versionsFromApi as $fullVer) {
                $minor = implode('.', array_slice(explode('.', $fullVer), 0, 2));
                $versions[] = [
                    'version' => $fullVer,
                    'minor' => $minor,
                    'installed' => true, // karena diambil dari runtime 1Panel, pasti sudah terinstall
                    'running' => true,   // asumsikan running (bisa cek status dari API jika ada)
                    'full_version' => $fullVer,
                    'current' => ($project->php_version === $fullVer),
                ];
            }

            return response()->json([
                'versions' => $versions,
                'project_version' => $project->php_version,
                'running_count' => count($versions),
            ]);

        } catch (\Throwable $e) {
            Log::error('Error in availableVersions: '.$e->getMessage());

            return response()->json([
                'error' => 'Terjadi kesalahan server: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Install versi PHP (membuat runtime baru via 1Panel API)
     */
    public function install(Request $request, string $hashid): JsonResponse
    {
        try {
            $request->validate([
                'version' => 'required|string|regex:/^\d+\.\d+\.\d+$/',
            ]);

            $project = $this->getProject($hashid);
            if (! $project) {
                return response()->json(['error' => 'Project not found'], 404);
            }

            $fullVersion = $request->version;

            // Cek apakah versi sudah ada di 1Panel
            $existingVersions = $this->fetchPhpVersionsFrom1Panel();
            if (in_array($fullVersion, $existingVersions)) {
                // Langsung switch
                return $this->switchVersionInternal($project, $fullVersion);
            }

            // Kirim request ke 1Panel API untuk membuat runtime
            $result = $this->call1PanelApi('post', '/api/v1/runtimes', [
                'name' => "php{$fullVersion}",
                'type' => 'php',
                'version' => $fullVersion,
                'source' => 'appstore',
            ]);

            if ($result->successful()) {
                $deployment = $project->deployments()->create([
                    'status' => 'building',
                    'build_logs' => "> Mengirim request ke 1Panel API untuk membuat runtime PHP {$fullVersion}.\n> Status: ".$result->status()."\n> Tunggu beberapa saat hingga runtime aktif, lalu Redeploy project.",
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "PHP {$fullVersion} sedang diinstall melalui 1Panel.",
                    'deployment_id' => $deployment->id,
                    'log_url' => route('user_hosting.build_logs', $project->hashid),
                ]);
            } else {
                $errorBody = $result->body();
                Log::error("1Panel API install error: {$result->status()} - {$errorBody}");

                return response()->json([
                    'error' => "Gagal membuat runtime PHP. Response API: {$errorBody}",
                ], 500);
            }

        } catch (\Throwable $e) {
            Log::error('Install PHP error: '.$e->getMessage());

            return response()->json(['error' => 'Internal server error: '.$e->getMessage()], 500);
        }
    }

    /**
     * Switch versi PHP untuk project
     */
    public function switchVersion(Request $request, string $hashid): JsonResponse
    {
        try {
            $request->validate([
                'version' => 'required|string|regex:/^\d+\.\d+\.\d+$/',
            ]);

            $project = $this->getProject($hashid);
            if (! $project) {
                return response()->json(['error' => 'Project not found'], 404);
            }

            $fullVersion = $request->version;

            // Pastikan versi tersedia di 1Panel
            $existingVersions = $this->fetchPhpVersionsFrom1Panel();
            if (! in_array($fullVersion, $existingVersions)) {
                return response()->json(['error' => "PHP {$fullVersion} tidak tersedia di 1Panel. Install terlebih dahulu."], 422);
            }

            return $this->switchVersionInternal($project, $fullVersion);

        } catch (\Throwable $e) {
            Log::error('Switch PHP error: '.$e->getMessage());

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    // -------------------------------------------------------------------------
    // PRIVATE METHODS
    // -------------------------------------------------------------------------

    private function getProject(string $hashid): ?HostingProject
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) {
            return null;
        }

        return HostingProject::where('user_id', auth()->id())->find($decoded[0]);
    }

    /**
     * Ambil daftar versi PHP dari 1Panel API (GET /api/v1/runtimes?type=php)
     * Mengembalikan array versi (contoh: ['8.4.6', '8.4.3', ...])
     */
    private function fetchPhpVersionsFrom1Panel(): array
    {
        $apiKey = env('PANEL_API_TOKEN');
        $panelUrl = rtrim(env('PANEL_URL'), '/');
        if (! $apiKey || ! $panelUrl) {
            Log::warning('PANEL_API_TOKEN atau PANEL_URL tidak diset di .env');

            return [];
        }

        try {
            $response = $this->call1PanelApi('get', '/api/v1/runtimes?type=php');
            if (! $response->successful()) {
                Log::error('1Panel API response error: '.$response->status().' - '.$response->body());

                return [];
            }

            $data = $response->json();
            $runtimes = $data['data'] ?? [];
            $versions = [];
            foreach ($runtimes as $rt) {
                if (isset($rt['version']) && preg_match('/^\d+\.\d+\.\d+$/', $rt['version'])) {
                    $versions[] = $rt['version'];
                }
            }

            return array_unique($versions);
        } catch (\Throwable $e) {
            Log::error('Exception saat fetch versi dari 1Panel API: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Internal method untuk switch versi dan catat deployment
     */
    private function switchVersionInternal(HostingProject $project, string $fullVersion): JsonResponse
    {
        $oldVersion = $project->php_version;
        $project->update(['php_version' => $fullVersion]);

        $deployment = $project->deployments()->create([
            'status' => 'ready',
            'build_logs' => "> PHP version switched from {$oldVersion} to {$fullVersion}.\n> Lakukan Redeploy agar perubahan diterapkan penuh.",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Berhasil beralih ke PHP {$fullVersion}.",
            'deployment_id' => $deployment->id,
            'log_url' => route('user_hosting.build_logs', $project->hashid),
        ]);
    }

    /**
     * Low-level API caller untuk 1Panel dengan autentikasi yang benar
     */
    /**
     * Low-level API caller untuk 1Panel dengan autentikasi yang benar
     * Serta tambahan opsi verify false untuk self-signed certificate
     */
    private function call1PanelApi(string $method, string $endpoint, array $data = []): Response
    {
        $apiKey = env('PANEL_API_TOKEN');
        $timestamp = time();
        $token = md5('1panel'.$apiKey.$timestamp);
        $url = rtrim(env('PANEL_URL'), '/').$endpoint;

        Log::info("Calling 1Panel API: {$method} {$url}");
        Log::info("Timestamp: {$timestamp}, Token: {$token}");

        // Tambahkan opsi verify false untuk self-signed certificate (hanya untuk development)
        return Http::withHeaders([
            '1Panel-Token' => $token,
            '1Panel-Timestamp' => (string) $timestamp,
            'Content-Type' => 'application/json',
        ])->withOptions([
            'timeout' => 30,
        ])->$method($url, $data);
    }
}
