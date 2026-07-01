<?php

namespace App\Http\Controllers\Hosting\User;

use App\Http\Controllers\Controller;
use App\Models\HostingProject;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

class StorageController extends Controller
{
    /**
     * Hitung ukuran folder secara rekursif via du -sb (cepat, kernel-level).
     */
    private function getFolderSize(string $path): int
    {
        if (! is_dir($path)) {
            return 0;
        }
        $output = trim(shell_exec('du -sb '.escapeshellarg($path)." 2>/dev/null | awk '{print $1}'") ?? '0');

        return (int) $output;
    }

    private function formatBytes(int $bytes, int $decimals = 1): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = (int) floor(log($bytes, 1024));

        return round($bytes / pow(1024, $i), $decimals).' '.$units[$i];
    }

    /**
     * Halaman storage overview untuk semua project user.
     */
    public function index()
    {
        $projects = HostingProject::where('user_id', Auth::id())->latest()->get();

        $totalUsed = 0;
        $totalLimit = 0;
        $items = [];

        foreach ($projects as $project) {
            $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);
            $projectDir = "/www/sites/hosting_clients/{$subdomain}";
            $used = $this->getFolderSize($projectDir);
            $totalUsed += $used;

        $totalLimit = (Auth::user()->hosting_storage_limit_mb ?? 1024) * 1024 * 1024;

        foreach ($items as &$item) {
            $item['percent'] = $totalLimit > 0 ? min(100, round(($item['used_bytes'] / $totalLimit) * 100, 1)) : 0;
        }

        // Sort by usage descending
        usort($items, fn ($a, $b) => $b['used_bytes'] <=> $a['used_bytes']);

        return view('pages.hosting.user.storage', [
            'items' => $items,
            'total_used' => $totalUsed,
            'total_human' => $this->formatBytes($totalUsed),
            'limit_bytes' => $totalLimit,
            'limit_human' => $this->formatBytes($totalLimit),
            'percent' => $totalLimit > 0 ? min(100, round(($totalUsed / $totalLimit) * 100, 1)) : 0,
        ]);
    }

    /**
     * Detail storage satu project (breakdown per subfolder).
     */
    public function show($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) {
            abort(404);
        }

        $project = HostingProject::where('user_id', Auth::id())->findOrFail($decoded[0]);
        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);
        $projectDir = "/www/sites/hosting_clients/{$subdomain}";
        $limit = (Auth::user()->hosting_storage_limit_mb ?? 1024) * 1024 * 1024;

        $totalUsed = $this->getFolderSize($projectDir);

        // Breakdown subfolder langsung (1 level)
        $breakdown = [];
        if (is_dir($projectDir)) {
            $entries = scandir($projectDir);
            foreach ($entries as $entry) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
                $fullPath = $projectDir.'/'.$entry;
                $size = $this->getFolderSize($fullPath);
                $breakdown[] = [
                    'name' => $entry,
                    'size' => $size,
                    'human' => $this->formatBytes($size),
                    'is_dir' => is_dir($fullPath),
                    'percent' => $totalUsed > 0 ? round(($size / $totalUsed) * 100, 1) : 0,
                ];
            }
            usort($breakdown, fn ($a, $b) => $b['size'] <=> $a['size']);
        }

        return view('pages.hosting.user.storage_detail', [
            'project' => $project,
            'used_bytes' => $totalUsed,
            'used_human' => $this->formatBytes($totalUsed),
            'limit_bytes' => $limit,
            'limit_human' => $this->formatBytes($limit),
            'percent' => $limit > 0 ? min(100, round(($totalUsed / $limit) * 100, 1)) : 0,
            'breakdown' => $breakdown,
            'project_dir' => $projectDir,
        ]);
    }

    /**
     * Endpoint untuk membeli upgrade storage 2GB.
     */
    public function upgrade()
    {
        $user = Auth::user();

        if (($user->hosting_storage_limit_mb ?? 1024) >= 3072) {
            return back()->with('error', 'Anda sudah mencapai kapasitas maksimal saat ini (3GB).');
        }

        // Cek jika sudah ada invoice upgrade unpaid
        $existing = \App\Models\HostingPayment::where('user_id', $user->id)
            ->where('invoice_number', 'like', 'HST-UPG-%')
            ->where('status', 'unpaid')
            ->first();

        if ($existing) {
            return redirect()->route('user_hosting.storage')->with('success', 'Silakan lunasi tagihan upgrade storage Anda.');
        }

        $payment = \App\Models\HostingPayment::create([
            'user_id' => $user->id,
            'hosting_project_id' => null,
            'invoice_number' => 'HST-UPG-'. strtoupper(uniqid()),
            'amount' => 50000,
            'status' => 'unpaid',
        ]);

        $user->notify(new \App\Notifications\SystemNotification('Tagihan upgrade storage 2GB berhasil dibuat: ' . $payment->invoice_number, 'info'));

        return redirect()->route('user_hosting.storage')->with('success', 'Tagihan upgrade storage berhasil dibuat. Silakan bayar tagihan tersebut.');
    }

    /**
     * API endpoint — cek apakah project masih dalam batas storage.
     * Dipanggil dari AutoDeployProject sebelum clone/pull.
     */
    public function check(HostingProject $project): bool
    {
        $user = $project->user;
        $projects = HostingProject::where('user_id', $user->id)->get();
        $totalUsed = 0;

        foreach ($projects as $p) {
            $subdomain = str_replace('.ryaze.my.id', '', $p->ryaze_domain);
            $totalUsed += $this->getFolderSize("/www/sites/hosting_clients/{$subdomain}");
        }

        $limit = ($user->hosting_storage_limit_mb ?? 1024) * 1024 * 1024;

        return $totalUsed < $limit;
    }
}
