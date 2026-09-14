<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CopyFileOrDirectory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $sourcePath;

    public string $destinationPath;

    public bool $isDirectory;

    public int $userId;

    public int $timeout = 600;

    public int $tries = 1;

    public function __construct(string $sourcePath, string $destinationPath, bool $isDirectory, int $userId)
    {
        $this->sourcePath = $sourcePath;
        $this->destinationPath = $destinationPath;
        $this->isDirectory = $isDirectory;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        try {
            if ($this->isDirectory) {
                File::copyDirectory($this->sourcePath, $this->destinationPath);
            } else {
                $parentDir = dirname($this->destinationPath);
                if (! is_dir($parentDir)) {
                    File::makeDirectory($parentDir, 0755, true);
                }
                File::copy($this->sourcePath, $this->destinationPath);
            }

            $user = User::find($this->userId);
            if ($user) {
                $baseName = basename($this->sourcePath);
                $user->notify(new SystemNotification(
                    "Penyalinan '{$baseName}' berhasil diselesaikan.",
                    'success'
                ));
            }
        } catch (\Throwable $e) {
            Log::error('[CopyFileOrDirectory] Failed: '.$e->getMessage(), [
                'source' => $this->sourcePath,
                'destination' => $this->destinationPath,
                'exception' => $e,
            ]);

            $user = User::find($this->userId);
            if ($user) {
                $baseName = basename($this->sourcePath);
                $user->notify(new SystemNotification(
                    "Penyalinan '{$baseName}' gagal: ".$e->getMessage(),
                    'error'
                ));
            }
        }
    }
}
