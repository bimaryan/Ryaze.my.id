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
     * Ambil daftar versi PHP yang tersedia (full patch version) dari:
     * - Docker images yang sudah ada (1panel-php:x.y.z)
     * - 1Panel API (runtime yang terdaftar)
     * - (Opsional) daftar versi yang bisa diinstall dari App Store
     */
    public function availableVersions(Request $request, string $hashid): JsonResponse
    {
        $project = $this->getProject($hashid);
        if (! $project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        // Ambil daftar runtime PHP dari 1Panel API
        $apiVersions = $this->getPhpVersionsFrom1PanelApi();

        // Jika API gagal, gunakan fallback manual (versi yang umum)
        if (empty($apiVersions)) {
            $apiVersions = ['8.4.6', '8.4.3', '8.3.20', '8.3.16', '8.2.28', '8.2.27', '8.2.20'];
        }

        // Urutkan dari tertinggi ke terendah
        usort($apiVersions, version_compare);
        $apiVersions = array_reverse($apiVersions);

        $versions = [];
        foreach ($apiVersions as $fullVer) {
            $minor = implode('.', array_slice(explode('.', $fullVer), 0, 2));
            // Cek apakah versi ini sedang berjalan di 1Panel? Kita tidak perlu deteksi container karena kita hanya sync dengan API.
            // Anggap semua versi dari API adalah "installed" (karena sudah ada runtime di 1Panel)
            $isInstalled = true;
            $isRunning = true; // atau cek status dari API jika ada field 'status'

            $versions[] = [
                'version' => $fullVer,
                'minor' => $minor,
                'installed' => $isInstalled,
                'running' => $isRunning,
                'full_version' => $fullVer,
                'current' => ($project->php_version === $fullVer),
            ];
        }

        return response()->json([
            'versions' => $versions,
            'project_version' => $project->php_version,
            'running_count' => count($versions),
        ]);
    }

    /**
     * Install versi PHP baru (jika belum ada) melalui 1Panel API atau Docker fallback.
     */
    public function install(Request $request, string $hashid): JsonResponse
    {
        $request->validate([
            'version' => 'required|string|regex:/^\d+\.\d+\.\d+$/',
        ]);

        $project = $this->getProject($hashid);
        if (! $project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        $fullVersion = $request->version;
        $panelUrl = rtrim(env('PANEL_URL'), '/');
        $apiKey = env('PANEL_API_TOKEN');

        if (! $apiKey || ! $panelUrl) {
            return response()->json([
                'error' => 'PANEL_API_TOKEN atau PANEL_URL tidak dikonfigurasi. Silakan install PHP manual melalui 1Panel.',
            ], 422);
        }

        // Jika versi sudah ada (dari daftar runtime), langsung switch
        $existingVersions = $this->getPhpVersionsFrom1PanelApi();
        if (in_array($fullVersion, $existingVersions)) {
            return $this->switchVersionInternal($project, $fullVersion);
        }

        // Install via 1Panel API
        return $this->installVia1PanelApi($project, $fullVersion, $panelUrl, $apiKey);
    }

    /**
     * Switch project ke versi PHP yang sudah terinstall.
     */
    public function switchVersion(Request $request, string $hashid): JsonResponse
    {
        $request->validate([
            'version' => 'required|string|regex:/^\d+\.\d+\.\d+$/',
        ]);

        $project = $this->getProject($hashid);
        if (! $project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        $fullVersion = $request->version;

        if (! $this->isPhpVersionInstalled($fullVersion)) {
            return response()->json(['error' => "PHP {$fullVersion} belum terinstall. Install dulu."], 422);
        }

        return $this->switchVersionInternal($project, $fullVersion);
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
     * Ambil semua versi PHP dari Docker images dengan pattern "1panel-php:x.y.z"
     */
    private function getDockerPhpVersions(): array
    {
        $output = shell_exec("docker images --format '{{.Tag}}' 2>/dev/null | grep -E '^[0-9]+\.[0-9]+\.[0-9]+$' | sort -V | uniq");
        if (! $output) {
            return [];
        }

        return array_filter(explode("\n", trim($output)));
    }

    /**
     * Ambil daftar PHP runtime dari 1Panel API (GET /api/v1/runtimes?type=php)
     */
    private function getPhpVersionsFrom1PanelApi(): array
    {
        $apiKey = env('PANEL_API_TOKEN');
        $panelUrl = rtrim(env('PANEL_URL'), '/');
        if (! $apiKey || ! $panelUrl) {
            return [];
        }

        try {
            $response = $this->call1PanelApi('get', '/api/v1/runtimes?type=php');
            if ($response->successful()) {
                $data = $response->json();
                // 1Panel API biasanya mengembalikan { "code":200, "data": [...] }
                $runtimes = $data['data'] ?? [];
                $versions = [];
                foreach ($runtimes as $rt) {
                    if (isset($rt['version']) && preg_match('/^\d+\.\d+\.\d+$/', $rt['version'])) {
                        $versions[] = $rt['version'];
                    }
                }

                return $versions;
            }
        } catch (\Exception $e) {
            Log::warning('Gagal ambil versi dari 1Panel API: '.$e->getMessage());
        }

        return [];
    }

    /**
     * Fallback list versi PHP (patch terbaru untuk setiap minor)
     */
    private function getFallbackPhpVersions(): array
    {
        return [
            '8.1.30',
            '8.2.28',
            '8.3.20',
            '8.4.6',
        ];
    }

    /**
     * Cek apakah container dengan image 1panel-php:version sedang running
     */
    private function isContainerRunning(string $fullVersion): bool
    {
        $output = shell_exec("docker ps --filter 'ancestor=1panel-php:{$fullVersion}' --format '{{.Names}}' 2>/dev/null");

        return ! empty(trim($output));
    }

    /**
     * Cek apakah Docker image sudah ada (meskipun container tidak running)
     */
    private function isImageExists(string $fullVersion): bool
    {
        $output = shell_exec("docker images -q 1panel-php:{$fullVersion} 2>/dev/null");

        return ! empty(trim($output));
    }

    /**
     * Dapatkan nama container (opsional, untuk info)
     */
    private function getContainerName(string $fullVersion): ?string
    {
        $output = shell_exec("docker ps --filter 'ancestor=1panel-php:{$fullVersion}' --format '{{.Names}}' 2>/dev/null | head -1");

        return trim($output) ?: null;
    }

    /**
     * Pengecekan apakah versi PHP sudah terinstall (image ada atau container running)
     */
    private function isPhpVersionInstalled(string $fullVersion): bool
    {
        return $this->isImageExists($fullVersion) || $this->isContainerRunning($fullVersion);
    }

    /**
     * Core method untuk switch versi dan catat deployment
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
     * Install PHP via 1Panel API (membuat runtime baru)
     */
    private function installVia1PanelApi(HostingProject $project, string $fullVersion, string $panelUrl, string $apiKey): JsonResponse
    {
        try {
            $response = $this->call1PanelApi('post', '/api/v1/runtimes', [
                'name' => "php{$fullVersion}",
                'type' => 'php',
                'version' => $fullVersion,
                'source' => 'appstore',
            ]);

            // Cek sukses: status code 200 atau 201
            if ($response->successful()) {
                $deployment = $project->deployments()->create([
                    'status' => 'building',
                    'build_logs' => "> Mengirim request ke 1Panel API untuk membuat runtime PHP {$fullVersion}...\n> Status: ".$response->status()."\n> Tunggu hingga runtime aktif, lalu Redeploy project.",
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "PHP {$fullVersion} sedang diinstall melalui 1Panel.",
                    'deployment_id' => $deployment->id,
                    'log_url' => route('user_hosting.build_logs', $project->hashid),
                ]);
            } else {
                // API error
                Log::error('1Panel API error: '.$response->status().' - '.$response->body());

                return response()->json([
                    'error' => 'Gagal membuat runtime di 1Panel. Periksa koneksi dan API Key.',
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('1Panel API exception: '.$e->getMessage());

            return response()->json(['error' => 'Tidak dapat terhubung ke 1Panel API: '.$e->getMessage()], 500);
        }
    }

    /**
     * Fallback: Pull Docker image langsung (1panel-php:version)
     */
    private function installViaDocker(HostingProject $project, string $fullVersion): JsonResponse
    {
        // Cek apakah docker tersedia
        $dockerCheck = trim(shell_exec('which docker 2>/dev/null') ?? '');
        if (! $dockerCheck) {
            return response()->json([
                'error' => 'Docker tidak ditemukan. Install PHP manual melalui 1Panel > Websites > Runtimes.',
            ], 422);
        }

        // Jika image sudah ada, langsung switch
        if ($this->isImageExists($fullVersion)) {
            return $this->switchVersionInternal($project, $fullVersion);
        }

        // Pull image di background (async)
        $logFile = "/tmp/php_install_{$fullVersion}_".$project->id.'.log';
        $pullCmd = "nohup docker pull 1panel-php:{$fullVersion} > {$logFile} 2>&1 &";
        shell_exec($pullCmd);

        $deployment = $project->deployments()->create([
            'status' => 'building',
            'build_logs' => "> Pulling Docker image: 1panel-php:{$fullVersion}\n> Log: {$logFile}\n> Proses memakan waktu 1-5 menit.\n> Setelah selesai, lakukan Redeploy.",
        ]);

        // Background watcher untuk update status (opsional: bisa menggunakan queue job)
        $this->scheduleImageCheck($project, $fullVersion, $deployment->id);

        return response()->json([
            'success' => true,
            'message' => "Menarik image PHP {$fullVersion} dari Docker Hub (background).",
            'deployment_id' => $deployment->id,
            'log_url' => route('user_hosting.build_logs', $project->hashid),
        ]);
    }

    /**
     * Sederhana: jalankan script background untuk cek image dan update project
     */
    private function scheduleImageCheck(HostingProject $project, string $fullVersion, int $deploymentId): void
    {
        $script = "/tmp/check_php_{$fullVersion}_{$project->id}.sh";
        $content = "#!/bin/bash\n"
            ."for i in {1..60}; do\n"
            ."    sleep 10\n"
            ."    if docker images -q '1panel-php:{$fullVersion}' 2>/dev/null | grep -q .; then\n"
            ."        echo 'Image pulled successfully' >> /tmp/php_{$fullVersion}_{$project->id}.log\n"
            ."        exit 0\n"
            ."    fi\n"
            ."done\n"
            ."echo 'Pull timeout' >> /tmp/php_{$fullVersion}_{$project->id}.log\n";
        file_put_contents($script, $content);
        chmod($script, 0755);
        shell_exec("nohup bash {$script} > /dev/null 2>&1 &");
    }

    /**
     * Fungsi pemanggil API 1Panel dengan autentikasi header yang benar.
     * Digunakan untuk GET, POST, dll.
     */
    private function call1PanelApi(string $method, string $endpoint, array $data = []): Response
    {
        $apiKey = env('PANEL_API_TOKEN');
        $timestamp = time();
        $token = md5('1panel'.$apiKey.$timestamp);
        $url = rtrim(env('PANEL_URL'), '/').$endpoint;

        return Http::withHeaders([
            '1Panel-Token' => $token,
            '1Panel-Timestamp' => (string) $timestamp,
            'Content-Type' => 'application/json',
        ])->$method($url, $data);
    }
}
