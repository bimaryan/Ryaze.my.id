<?php

namespace App\Jobs;

use App\Models\ApkBuild;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class BuildApkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 900; // 15 menit untuk build Android

    protected ApkBuild $build;

    public function __construct(ApkBuild $build)
    {
        $this->build = $build;
    }

    public function handle(): void
    {
        $this->build->update(['status' => 'building']);
        $log = "[BUILD] Starting Web-to-APK for: {$this->build->app_name} ({$this->build->app_url})\n";

        $workDir = storage_path('app/private/apk_builds/' . $this->build->id);
        @mkdir($workDir, 0755, true);

        try {
            // ── 1. Tulis twa-manifest.json ────────────────────────────────
            $log .= "[INFO] Writing twa-manifest.json...\n";
            $host = parse_url($this->build->app_url, PHP_URL_HOST);
            $startPath = parse_url($this->build->app_url, PHP_URL_PATH) ?: '/';
            $packageParts = explode('.', $this->build->package_name);
            $appShortName = end($packageParts);

            // Resolusi ikon — gunakan upload user, fallback ke favicon website
            $iconUrl = $this->build->icon_path
                ? asset('storage/' . $this->build->icon_path)
                : "https://{$host}/favicon.ico";

            $manifest = [
                'packageId'         => $this->build->package_name,
                'host'              => $host,
                'name'              => $this->build->app_name,
                'launcherName'      => mb_substr($this->build->app_name, 0, 12),
                'display'           => 'standalone',
                'themeColor'        => '#000000',
                'navigationColor'   => '#000000',
                'backgroundColor'   => '#ffffff',
                'startUrl'          => $startPath,
                'iconUrl'           => $iconUrl,
                'maskableIconUrl'   => $iconUrl,
                'monochromeIconUrl' => $iconUrl,
                'appVersion'        => '1',
                'appVersionCode'    => 1,
                'shortcuts'         => [],
                'generatorApp'      => 'ryaze-apk-builder',
                'webManifestUrl'    => $this->build->app_url . '/manifest.json',
                'metaquest'         => false,
                'shareTarget'       => null,
                'additionalTrustedOrigins' => [],
                'retainedBundles'   => [],
                'features'          => [],
                'alphaDependencies' => ['enabled' => false],
                'enableNotifications' => false,
                'signingMode'       => 'none',
                'minSdkVersion'     => 19,
            ];

            file_put_contents($workDir . '/twa-manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));
            $log .= "[INFO] twa-manifest.json created.\n";

            // ── 2. Pre-konfigurasi Bubblewrap (hindari prompt interaktif) ──────────
            $log .= "[INFO] Pre-configuring Bubblewrap...\n";
            $homeDir = trim(shell_exec('echo $HOME') ?: '/root');
            $bubblewrapConfigDir = $homeDir . '/.bubblewrap';
            if (!is_dir($bubblewrapConfigDir)) {
                mkdir($bubblewrapConfigDir, 0755, true);
            }
            $jdkPath = env('JAVA_HOME', '/usr/lib/jvm/java-17-openjdk');
            $sdkPath = env('ANDROID_SDK_ROOT', '/opt/android-sdk');
            file_put_contents($bubblewrapConfigDir . '/llama.json', json_encode([
                'jdkPath'        => $jdkPath,
                'androidSdkPath' => $sdkPath,
            ]));
            $log .= "[INFO] Bubblewrap config written to {$bubblewrapConfigDir}/llama.json\n";

            // ── 3. Jalankan `bubblewrap build` ───────────────────────────
            $log .= "[CMD] Running: bubblewrap build --manifest=twa-manifest.json\n";

            $process = new Process(
                ['bubblewrap', 'build', '--manifest=twa-manifest.json'],
                $workDir,
                [
                    'JAVA_HOME' => env('JAVA_HOME', '/usr/lib/jvm/java-17-openjdk-amd64'),
                    'ANDROID_SDK_ROOT' => env('ANDROID_SDK_ROOT', '/opt/android-sdk'),
                    'PATH' => env('PATH', '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin')
                        . ':/opt/android-sdk/cmdline-tools/latest/bin'
                        . ':/opt/android-sdk/platform-tools'
                        . ':' . env('NODE_PATH', '/usr/bin'),
                ]
            );
            $process->setTimeout(840);

            $process->run(function ($type, $buffer) use (&$log) {
                $log .= $buffer;
            });

            if (!$process->isSuccessful()) {
                throw new \RuntimeException(
                    "Bubblewrap build failed (exit code {$process->getExitCode()}):\n" . $process->getErrorOutput()
                );
            }

            // ── 3. Cari hasil .apk ────────────────────────────────────────
            $log .= "\n[INFO] Searching for generated APK...\n";
            $apkFiles = glob($workDir . '/**/*.apk') ?: glob($workDir . '/*.apk');

            if (empty($apkFiles)) {
                throw new \RuntimeException('APK file not found after build. Check log for details.');
            }

            $apkSource = $apkFiles[0];
            $apkDest   = 'apks/' . $this->build->id . '.apk';

            Storage::disk('local')->put($apkDest, file_get_contents($apkSource));
            $log .= "[INFO] APK stored to: {$apkDest}\n";

            // ── 4. Bersihkan folder kerja ─────────────────────────────────
            $this->deleteDirectory($workDir);

            $this->build->update([
                'status'     => 'success',
                'apk_path'   => $apkDest,
                'log_output' => $log . "\n[BUILD] Completed successfully.",
            ]);

        } catch (\Exception $e) {
            $this->build->update([
                'status'     => 'failed',
                'log_output' => $log . "\n[ERROR] " . $e->getMessage(),
            ]);
        }
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = "$dir/$file";
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}

