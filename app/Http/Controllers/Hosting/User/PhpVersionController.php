<?php

namespace App\Http\Controllers\Hosting\User;

use App\Http\Controllers\Controller;
use App\Jobs\InstallPhpVersion;
use App\Models\HostingProject;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class PhpVersionController extends Controller
{
    /**
     * Daftar versi PHP yang didukung beserta status availability-nya.
     * Ini dikembalikan ke frontend untuk ditampilkan di dropdown.
     */
    public function availableVersions(Request $request, string $hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $project = HostingProject::where('user_id', auth()->id())->findOrFail($decoded[0]);

        $supported = ['8.1', '8.2', '8.3', '8.4'];
        $versions = [];

        foreach ($supported as $v) {
            // Cek apakah versi sudah terinstall di server
            $binary = "/usr/bin/php{$v}";
            $altBin = trim(shell_exec("which php{$v} 2>/dev/null") ?? '');
            $exists = file_exists($binary) || ! empty($altBin);

            $versionStr = '';
            if ($exists) {
                $bin = $exists && file_exists($binary) ? $binary : $altBin;
                $versionStr = trim(shell_exec("{$bin} -r 'echo PHP_VERSION;' 2>/dev/null") ?? '');
            }

            $versions[] = [
                'version' => $v,
                'installed' => $exists,
                'full_version' => $versionStr ?: null,
                'current' => ($project->php_version === $v),
            ];
        }

        // Deteksi PHP aktif di sistem
        $activeBin = trim(shell_exec('which php 2>/dev/null') ?? '');
        $activeVer = trim(shell_exec('php -r "echo PHP_MAJOR_VERSION.\'.\'.PHP_MINOR_VERSION;" 2>/dev/null') ?? '');

        return response()->json([
            'versions' => $versions,
            'active_system' => $activeVer,
            'project_version' => $project->php_version,
        ]);
    }

    /**
     * Install atau switch versi PHP untuk project.
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

        // Buat deployment log entry
        $deployment = $project->deployments()->create([
            'status' => 'building',
            'build_logs' => "> Installing PHP {$phpVersion}...\n> Please wait, this may take a few minutes.",
        ]);

        // Dispatch job
        InstallPhpVersion::dispatch($project, $phpVersion, $deployment->id);

        return response()->json([
            'success' => true,
            'message' => "PHP {$phpVersion} installation queued.",
            'deployment_id' => $deployment->id,
            'log_url' => route('user_hosting.build_logs', $project->hashid),
        ]);
    }
}
