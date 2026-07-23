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

            // Resolusi ikon — gunakan upload user, fallback ke UI-Avatars API (garansi PNG valid)
            $iconUrl = $this->build->icon_path
                ? asset('storage/' . $this->build->icon_path)
                : "https://ui-avatars.com/api/?name=" . urlencode(mb_substr($this->build->app_name, 0, 1)) . "&size=512&background=000000&color=ffffff";

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

            // Deteksi path Java secara dinamis
            $javaSymlink = trim(shell_exec('which java 2>/dev/null') ?: '');
            $javaRealPath = $javaSymlink ? trim(shell_exec("readlink -f {$javaSymlink} 2>/dev/null") ?: $javaSymlink) : '';
            // Naiki ke JAVA_HOME (biasanya: .../bin/java -> ...)
            $jdkPath = $javaRealPath ? dirname(dirname($javaRealPath)) : env('JAVA_HOME', '/usr/lib/jvm/java-17-openjdk');
            $sdkPath = env('ANDROID_SDK_ROOT', '/opt/android-sdk');
            $log .= "[INFO] Detected JAVA_HOME: {$jdkPath}\n";
            $log .= "[INFO] Detected ANDROID_SDK_ROOT: {$sdkPath}\n";

            // Tulis config ke home dir user yang menjalankan PHP
            $homeDir = trim(shell_exec('echo $HOME 2>/dev/null') ?: '/tmp');
            if ($homeDir === '/' || !is_writable($homeDir)) {
                $homeDir = '/tmp'; // Fallback ke /tmp jika home tidak bisa ditulisi
            }
            
            $bubblewrapConfigDir = $homeDir . '/.bubblewrap';
            if (!is_dir($bubblewrapConfigDir)) {
                mkdir($bubblewrapConfigDir, 0755, true);
            }
            
            file_put_contents($bubblewrapConfigDir . '/config.json', json_encode([
                'jdkPath'        => $jdkPath,
                'androidSdkPath' => $sdkPath,
            ]));
            $log .= "[INFO] Bubblewrap config written to {$bubblewrapConfigDir}/config.json\n";

            $pathEnv = getenv('PATH') ?: '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin';
            $pathEnv .= ':/opt/android-sdk/cmdline-tools/latest/bin:/opt/android-sdk/platform-tools:' . $jdkPath . '/bin';

            // ── 2.5. Environment Preparation (Fix Alpine & Missing Tools) ────────
            $log .= "[INFO] Preparing Alpine environment (installing gcompat & platform-tools)...\n";
            $this->build->update(['log_output' => $log]);

            $prepCmd = "apk add --no-cache gcompat libstdc++ libgcc && sdkmanager \"platform-tools\" && ln -sfn /opt/android-sdk/cmdline-tools/latest /opt/android-sdk/tools";
            $prepProcess = Process::fromShellCommandline($prepCmd, $workDir, [
                'JAVA_HOME' => $jdkPath,
                'PATH'      => $pathEnv,
            ]);
            $prepProcess->setTimeout(300);
            $prepProcess->run(function($type, $buffer) use (&$log, &$lastUpdate) {
                $log .= $buffer;
                if (time() - $lastUpdate >= 2) {
                    $this->build->update(['log_output' => $log]);
                    $lastUpdate = time();
                }
            });
            $log .= "[INFO] Environment preparation finished.\n";

            // ── 3. Jalankan `bubblewrap doctor` untuk diagnosa ───────────
            $log .= "[CMD] Running: bubblewrap doctor\n";
            $doctorProcess = new Process(['bubblewrap', 'doctor'], $workDir, [
                'HOME'             => $homeDir,
                'JAVA_HOME'        => $jdkPath,
                'ANDROID_SDK_ROOT' => $sdkPath,
                'ANDROID_HOME'     => $sdkPath,
                'PATH'             => $pathEnv,
                'CI'               => 'true',
            ]);
            $doctorProcess->setTimeout(60);
            $doctorProcess->run(function($type, $buffer) use (&$log, &$lastUpdate) {
                $log .= $buffer;
                if (time() - $lastUpdate >= 2) {
                    $this->build->update(['log_output' => $log]);
                    $lastUpdate = time();
                }
            });

            // ── 4. Jalankan `bubblewrap build` ───────────────────────────
            $log .= "[CMD] Running: bubblewrap build --manifest=twa-manifest.json\n";

            $process = new Process(
                ['bubblewrap', 'build', '--manifest=twa-manifest.json'],
                $workDir,
                [
                    'HOME'             => $homeDir,
                    'JAVA_HOME'        => $jdkPath,
                    'ANDROID_SDK_ROOT' => $sdkPath,
                    'ANDROID_HOME'     => $sdkPath,
                    'PATH'             => $pathEnv,
                    'CI'               => 'true', // hint ke beberapa CLI agar non-interaktif
                ]
            );
            $process->setTimeout(840);
            // Karena SDK & JDK sekarang sudah valid, Bubblewrap tidak lagi bertanya path SDK.
            // Ia hanya bertanya: "would you like to regenerate your project? (Y/n)"
            // Kita harus jawab 'y' agar Bubblewrap me-generate folder proyek Androidnya.
            $process->setInput("y\n");

            $lastUpdate = time();
            $process->run(function ($type, $buffer) use (&$log, &$lastUpdate) {
                $log .= $buffer;
                // Update ke database setiap 2 detik agar tidak spam DB
                if (time() - $lastUpdate >= 2) {
                    $this->build->update(['log_output' => $log]);
                    $lastUpdate = time();
                }
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

