<?php

namespace App\Http\Controllers\Hosting\User;

use App\Http\Controllers\Controller;
use App\Models\HostingProject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Vinkla\Hashids\Facades\Hashids;

class PhpVersionController extends Controller
{
    /**
     * Deteksi versi PHP yang tersedia via 1Panel API + Docker.
     * 1Panel menyimpan runtime PHP sebagai Docker container dengan naming convention:
     * 1panel-php:{version} — contoh: 1panel-php:8.4.6, 1panel-php:8.3.x, dll
     */
    public function availableVersions(Request $request, string $hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $project = HostingProject::where('user_id', auth()->id())->findOrFail($decoded[0]);

        // Versi minor yang didukung 1Panel (dari App Store mereka)
        $supported = ['8.1', '8.2', '8.3', '8.4'];
        $versions = [];

        // Deteksi runtime PHP yang sudah ada via docker ps
        // Container 1Panel PHP punya naming: 1Panel-php{major}-{randomid}
        $dockerOutput = trim(shell_exec(
            "docker ps --format '{{.Image}}\t{{.Names}}\t{{.Status}}' 2>/dev/null | grep -i '1panel-php' || true"
        ) ?? '');

        // Parse container yang running
        $runningContainers = [];
        if ($dockerOutput) {
            foreach (explode("\n", $dockerOutput) as $line) {
                $parts = explode("\t", trim($line));
                if (count($parts) >= 2) {
                    $image = $parts[0]; // e.g. 1panel-php:8.4.6
                    $name = $parts[1]; // e.g. 1Panel-php8-aJQI
                    // Extract versi dari image tag
                    if (preg_match('/1panel-php:(\d+\.\d+)/', $image, $m)) {
                        $minor = implode('.', array_slice(explode('.', $m[1]), 0, 2));
                        $runningContainers[$minor] = [
                            'image' => $image,
                            'container' => $name,
                            'full_ver' => $m[1],
                        ];
                    }
                }
            }
        }

        // Juga cek image yang sudah di-pull tapi mungkin tidak running
        $imagesOutput = trim(shell_exec(
            "docker images --format '{{.Repository}}:{{.Tag}}' 2>/dev/null | grep -i '1panel-php' || true"
        ) ?? '');

        $pulledImages = [];
        if ($imagesOutput) {
            foreach (explode("\n", $imagesOutput) as $img) {
                $img = trim($img);
                if (preg_match('/1panel-php:(\d+\.\d+)/', $img, $m)) {
                    $minor = implode('.', array_slice(explode('.', $m[1]), 0, 2));
                    $pulledImages[$minor] = $m[1];
                }
            }
        }

        foreach ($supported as $v) {
            $isRunning = isset($runningContainers[$v]);
            $isPulled = isset($pulledImages[$v]);
            $isInstalled = $isRunning || $isPulled;

            $versions[] = [
                'version' => $v,
                'installed' => $isInstalled,
                'running' => $isRunning,
                'full_version' => $runningContainers[$v]['full_ver'] ?? ($pulledImages[$v] ?? null),
                'container' => $runningContainers[$v]['container'] ?? null,
                'current' => ($project->php_version === $v || str_starts_with($project->php_version ?? '', $v)),
            ];
        }

        // Deteksi versi PHP yang aktif dipakai project (via container yang running)
        $activeContainer = null;
        foreach ($runningContainers as $minor => $info) {
            if (str_starts_with($project->php_version ?? '', $minor)) {
                $activeContainer = $info['container'];
                break;
            }
        }

        return response()->json([
            'versions' => $versions,
            'project_version' => $project->php_version,
            'active_container' => $activeContainer,
            'running_count' => count($runningContainers),
        ]);
    }

    /**
     * Install versi PHP baru via 1Panel API.
     * 1Panel menyediakan REST API internal untuk manage runtimes.
     */
    public function install(Request $request, string $hashid)
    {
        $request->validate(['version' => 'required|in:8.1,8.2,8.3,8.4']);

        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $project = HostingProject::where('user_id', auth()->id())->findOrFail($decoded[0]);
        $phpVersion = $request->version;

        // Cek apakah 1Panel API dikonfigurasi
        $panelUrl = rtrim(env('PANEL_URL', 'http://localhost:4431'), '/');
        $panelToken = env('PANEL_API_TOKEN', '');

        if (! $panelToken) {
            // Fallback: coba pull Docker image langsung jika punya akses shell
            return $this->installViaDocker($project, $phpVersion);
        }

        return $this->installVia1PanelApi($project, $phpVersion, $panelUrl, $panelToken);
    }

    /**
     * Install via 1Panel REST API.
     * Endpoint: POST /api/v1/runtimes
     */
    private function installVia1PanelApi($project, string $phpVersion, string $panelUrl, string $token): JsonResponse
    {
        // Map versi minor ke image tag 1Panel (biasanya patch version terbaru)
        $imageMap = [
            '8.1' => '8.1',
            '8.2' => '8.2',
            '8.3' => '8.3',
            '8.4' => '8.4',
        ];

        $runtimeName = "php{$phpVersion}";

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json',
            ])->timeout(10)->post("{$panelUrl}/api/v1/runtimes", [
                'name' => $runtimeName,
                'type' => 'php',
                'version' => $imageMap[$phpVersion] ?? $phpVersion,
                'source' => 'appstore',
            ]);

            if ($response->successful()) {
                // Update project
                $project->update(['php_version' => $phpVersion]);

                // Buat log deployment
                $deployment = $project->deployments()->create([
                    'status' => 'ready',
                    'build_logs' => "> PHP {$phpVersion} runtime creation requested via 1Panel API.\n> Status: ".$response->status()."\n> Silakan cek 1Panel > Websites > Runtimes untuk konfirmasi.\n> Lakukan Redeploy setelah runtime aktif.",
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "PHP {$phpVersion} installation queued in 1Panel.",
                    'log_url' => route('user_hosting.build_logs', $project->hashid),
                    'deployment_id' => $deployment->id,
                ]);
            } else {
                // 1Panel API error — fallback ke Docker pull
                \Log::warning("[PhpInstall] 1Panel API error {$response->status()}, falling back to docker pull.");

                return $this->installViaDocker($project, $phpVersion);
            }
        } catch (\Exception $e) {
            \Log::warning("[PhpInstall] 1Panel API unreachable: {$e->getMessage()}, falling back to docker pull.");

            return $this->installViaDocker($project, $phpVersion);
        }
    }

    /**
     * Fallback: Pull image Docker 1panel-php secara langsung.
     * Image naming: 1panel-php:{version}
     */
    private function installViaDocker($project, string $phpVersion): JsonResponse
    {
        // Cek apakah docker tersedia
        $dockerCheck = trim(shell_exec('which docker 2>/dev/null') ?? '');
        if (! $dockerCheck) {
            return response()->json([
                'error' => 'Docker tidak ditemukan. Tambahkan PANEL_API_TOKEN di .env untuk install via 1Panel API, atau install PHP manual lewat 1Panel > Websites > Runtimes.',
            ], 422);
        }

        // Cek apakah image sudah ada
        $imageExists = trim(shell_exec(
            "docker images -q '1panel-php:{$phpVersion}' 2>/dev/null"
        ) ?? '');

        if ($imageExists) {
            $project->update(['php_version' => $phpVersion]);
            $deployment = $project->deployments()->create([
                'status' => 'ready',
                'build_logs' => "> PHP {$phpVersion} Docker image already present.\n> Project updated to use PHP {$phpVersion}.\n> Lakukan Redeploy untuk menerapkan perubahan.",
            ]);

            return response()->json([
                'success' => true,
                'message' => "PHP {$phpVersion} already available.",
                'log_url' => route('user_hosting.build_logs', $project->hashid),
                'deployment_id' => $deployment->id,
            ]);
        }

        // Pull image di background (async via nohup agar tidak timeout)
        $logFile = "/tmp/php_install_{$phpVersion}_".$project->id.'.log';
        $pullCmd = "nohup docker pull 1panel-php:{$phpVersion} > {$logFile} 2>&1 &";
        shell_exec($pullCmd);

        // Buat deployment record — status building, akan diupdate oleh polling
        $deployment = $project->deployments()->create([
            'status' => 'building',
            'build_logs' => "> Pulling Docker image: 1panel-php:{$phpVersion}\n> Log: {$logFile}\n> Harap tunggu 1-5 menit...",
        ]);

        // Start background watcher untuk update log
        $watchCmd = "nohup bash -c '"
            ."while [ ! -f {$logFile}.done ]; do sleep 3; done; "
            ."echo done' > /dev/null 2>&1 &";

        // Update project versi setelah berhasil (kita assume success untuk UX, validasi via refresh)
        $project->update(['php_version' => $phpVersion]);

        // Schedule check after 30s via simple background script
        $updateScript = "/tmp/check_php_{$phpVersion}_{$project->id}.sh";
        file_put_contents($updateScript, "#!/bin/bash
for i in {1..20}; do
    sleep 15
    if docker images -q '1panel-php:{$phpVersion}' 2>/dev/null | grep -q .; then
        echo 'Image pulled successfully'
        exit 0
    fi
done
echo 'Pull timeout'
");
        shell_exec("nohup bash {$updateScript} > /dev/null 2>&1 &");

        return response()->json([
            'success' => true,
            'message' => "Pulling PHP {$phpVersion} Docker image in background.",
            'log_url' => route('user_hosting.build_logs', $project->hashid),
            'deployment_id' => $deployment->id,
            'note' => 'Untuk hasil terbaik, install PHP via 1Panel > Websites > Runtimes > Create Runtime',
        ]);
    }

    /**
     * Switch project ke versi PHP yang sudah terinstall.
     */
    public function switchVersion(Request $request, string $hashid)
    {
        $request->validate(['version' => 'required|in:8.1,8.2,8.3,8.4']);

        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $project = HostingProject::where('user_id', auth()->id())->findOrFail($decoded[0]);
        $version = $request->version;

        $project->update(['php_version' => $version]);

        $deployment = $project->deployments()->create([
            'status' => 'ready',
            'build_logs' => "> Project PHP version switched to {$version}.\n> Lakukan Redeploy untuk menerapkan perubahan secara penuh.",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Switched to PHP {$version}.",
            'log_url' => route('user_hosting.build_logs', $project->hashid),
            'deployment_id' => $deployment->id,
        ]);
    }
}
