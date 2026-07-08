<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    /**
     * Display a listing of backups.
     */
    public function index()
    {
        $backupDir = storage_path('app/admin_backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $files = File::files($backupDir);
        $backups = [];
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'zip') {
                $backups[] = [
                    'name' => $file->getFilename(),
                    'size' => round($file->getSize() / 1024 / 1024, 2),
                    'date' => date('Y-m-d H:i:s', $file->getMTime()),
                ];
            }
        }

        // Urutkan dari yang terbaru
        usort($backups, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return view('pages.admin.backup.index', compact('backups'));
    }

    /**
     * Generate a new full backup (DB + storage/app/projects).
     */
    public function create()
    {
        set_time_limit(300); // 5 minutes max

        try {
            $backupDir = storage_path('app/admin_backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $date = date('Y-m-d_H-i-s');
            $zipFilename = 'ryaze_backup_' . $date . '.zip';
            $zipPath = $backupDir . '/' . $zipFilename;
            $sqlFilename = 'database_' . $date . '.sql';
            $sqlPath = storage_path('app/temp/' . $sqlFilename);

            if (!is_dir(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            // Dump Database using pure PHP (ifsnop/mysqldump-php)
            $dbHost = env('DB_HOST', '127.0.0.1');
            $dbPort = env('DB_PORT', '3306');
            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');

            try {
                $dump = new \Ifsnop\Mysqldump\Mysqldump(
                    "mysql:host={$dbHost};port={$dbPort};dbname={$dbName}", 
                    $dbUser, 
                    $dbPass,
                    ['add-drop-table' => true]
                );
                $dump->start($sqlPath);
            } catch (\Exception $e) {
                throw new \Exception("Gagal melakukan dump database: " . $e->getMessage());
            }

            // Create ZIP
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                
                // Add DB dump
                if (file_exists($sqlPath)) {
                    $zip->addFile($sqlPath, 'database.sql');
                }

                // Add storage/app/projects folder if exists (User files)
                $projectsDir = storage_path('app/projects');
                if (is_dir($projectsDir)) {
                    $this->addFolderToZip($projectsDir, $zip, 'storage/app/projects');
                }
                
                // Also add storage/app/public if exists (Avatars etc)
                $publicDir = storage_path('app/public');
                if (is_dir($publicDir)) {
                    $this->addFolderToZip($publicDir, $zip, 'storage/app/public');
                }

                $zip->close();
            } else {
                throw new \Exception("Gagal membuat file ZIP backup.");
            }

            // Cleanup temp SQL
            if (file_exists($sqlPath)) {
                unlink($sqlPath);
            }

            return redirect()->route('superadmin.backup.index')->with('success', 'Backup berhasil dibuat: ' . $zipFilename);

        } catch (\Exception $e) {
            \Log::error('Backup Error: ' . $e->getMessage());
            return redirect()->route('superadmin.backup.index')->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    /**
     * Download backup ZIP
     */
    public function download($filename)
    {
        $path = storage_path('app/admin_backups/' . $filename);
        if (!file_exists($path)) {
            abort(404, 'File backup tidak ditemukan.');
        }

        return response()->download($path);
    }

    /**
     * Delete backup ZIP
     */
    public function destroy($filename)
    {
        $path = storage_path('app/admin_backups/' . $filename);
        if (file_exists($path)) {
            unlink($path);
            return redirect()->route('superadmin.backup.index')->with('success', 'File backup berhasil dihapus.');
        }

        return redirect()->route('superadmin.backup.index')->with('error', 'File backup tidak ditemukan.');
    }

    /**
     * Restore from uploaded ZIP
     */
    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:zip|max:512000' // Max 500MB
        ]);

        set_time_limit(300);

        try {
            $zipFile = $request->file('backup_file');
            $tempExtractDir = storage_path('app/temp/restore_' . time());
            
            if (!is_dir($tempExtractDir)) {
                mkdir($tempExtractDir, 0755, true);
            }

            $zip = new ZipArchive();
            if ($zip->open($zipFile->getPathname()) === true) {
                $zip->extractTo($tempExtractDir);
                $zip->close();
            } else {
                throw new \Exception("Gagal membuka file ZIP.");
            }

            // Restore Database if exists
            $sqlFile = $tempExtractDir . '/database.sql';
            if (file_exists($sqlFile)) {
                $dbHost = env('DB_HOST', '127.0.0.1');
                $dbPort = env('DB_PORT', '3306');
                $dbName = env('DB_DATABASE');
                try {
                    $sql = file_get_contents($sqlFile);
                    \Illuminate\Support\Facades\DB::unprepared($sql);
                } catch (\Exception $e) {
                    throw new \Exception("Gagal me-restore database: " . $e->getMessage());
                }
            }

            // Restore Storage App folders
            $restoredProjectsDir = $tempExtractDir . '/storage/app/projects';
            if (is_dir($restoredProjectsDir)) {
                File::copyDirectory($restoredProjectsDir, storage_path('app/projects'));
            }

            $restoredPublicDir = $tempExtractDir . '/storage/app/public';
            if (is_dir($restoredPublicDir)) {
                File::copyDirectory($restoredPublicDir, storage_path('app/public'));
            }

            // Cleanup
            File::deleteDirectory($tempExtractDir);

            return redirect()->route('superadmin.backup.index')->with('success', 'Sistem berhasil di-restore dari backup ZIP.');

        } catch (\Exception $e) {
            \Log::error('Restore Error: ' . $e->getMessage());
            return redirect()->route('superadmin.backup.index')->with('error', 'Gagal me-restore dari backup: ' . $e->getMessage());
        }
    }

    /**
     * Helper to add a folder recursively to ZIP
     */
    private function addFolderToZip($folder, &$zipFile, $exclusiveLength)
    {
        $handle = opendir($folder);
        while (false !== $f = readdir($handle)) {
            if ($f != '.' && $f != '..') {
                $filePath = "$folder/$f";
                // Remove prefix from file path before add to zip
                $localPath = substr($filePath, strpos($filePath, $exclusiveLength));

                if (is_file($filePath)) {
                    $zipFile->addFile($filePath, $localPath);
                } elseif (is_dir($filePath)) {
                    $zipFile->addEmptyDir($localPath);
                    $this->addFolderToZip($filePath, $zipFile, $exclusiveLength);
                }
            }
        }
        closedir($handle);
    }
}
