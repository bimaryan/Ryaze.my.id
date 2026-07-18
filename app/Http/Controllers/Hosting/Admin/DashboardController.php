<?php

namespace App\Http\Controllers\Hosting\Admin;

use App\Http\Controllers\Controller;
use App\Models\HostingBilling;
use App\Models\HostingDatabase;
use App\Models\HostingDeployment;
use App\Models\HostingPayment;
use App\Models\HostingProject;
use App\Models\User;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_projects' => HostingProject::count(),
            'active_projects' => HostingProject::where('status', 'active')->count(),
            'total_clients' => User::whereHas('hostingProjects')->count(),
            'total_databases' => HostingDatabase::count(),
            'pending_billing' => HostingPayment::where('status', 'unpaid')->where('invoice_number', 'like', 'HST-INV-%')->count(),
            'building_now' => HostingProject::where('status', 'building')->count(),
            'action_required' => HostingProject::whereIn('status', ['unpaid', 'error', 'suspended'])->count(),
        ];
        // Charts Data
        // 1. Bar Chart: Proyek Baru (6 Bulan Terakhir)
        $months = [];
        $newProjects = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->startOfMonth()->subMonths($i);
            $months[] = $date->translatedFormat('M Y');
            $newProjects[] = HostingProject::whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->count();
        }
        $chartNewProjects = [
            'labels' => $months,
            'series' => $newProjects
        ];

        // 2. Pie Chart: Status Proyek
        $statusCount = HostingProject::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status')->toArray();
        $chartProjectStatus = [
            'labels' => array_keys($statusCount),
            'series' => array_values($statusCount)
        ];

        // 3. Line Chart: Tren Tagihan Terbayar (6 Bulan Terakhir)
        $paidBillings = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->startOfMonth()->subMonths($i);
            $paidBillings[] = HostingPayment::where('status', 'paid')
                ->whereMonth('paid_at', $date->month)
                ->whereYear('paid_at', $date->year)
                ->sum('amount');
        }
        $chartBillings = [
            'labels' => $months,
            'series' => $paidBillings
        ];

        return view('pages.hosting.admin.index', compact('stats', 'chartNewProjects', 'chartProjectStatus', 'chartBillings'));
    }

    // 2. Halaman Membutuhkan Tindakan
    public function pending()
    {
        $projects = HostingProject::with(['client', 'billing', 'deployments' => fn ($q) => $q->latest()->limit(1)])
            ->whereIn('status', ['unpaid', 'error', 'suspended'])
            ->latest()
            ->paginate(15);

        return view('pages.hosting.admin.pending', compact('projects'));
    }

    // 3. Halaman Deploy Terbaru
    public function deployments()
    {
        $deployments = HostingDeployment::with('project.client')
            ->latest()
            ->paginate(20);

        return view('pages.hosting.admin.deployments', compact('deployments'));
    }

    // 4. Halaman Semua Project
    public function projects()
    {
        $projects = HostingProject::with(['client', 'billing'])
            ->latest()
            ->paginate(15);

        return view('pages.hosting.admin.projects', compact('projects'));
    }

    // 5. Halaman Semua Database
    public function databases()
    {
        $databases = HostingDatabase::with('user')
            ->latest()
            ->paginate(15);
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();

        return view('pages.hosting.admin.databases', compact('databases', 'users'));
    }

    // 6. Halaman Storage (Penyimpanan Akun)
    public function storage()
    {
        $users = User::whereHas('hostingProjects')
            ->withCount('hostingProjects')
            ->orderBy('hosting_storage_limit_mb', 'desc')
            ->paginate(15);

        return view('pages.hosting.admin.storage', compact('users'));
    }

    public function updateStorage(Request $request, $hashid)
    {
        $request->validate([
            'storage_limit_mb' => 'required|integer|min:100',
        ]);

        $user = User::findOrFail(Hashids::decode($hashid)[0]);
        $user->update(['hosting_storage_limit_mb' => $request->storage_limit_mb]);

        return back()->with('success', "Limit penyimpanan klien '{$user->name}' berhasil diperbarui.");
    }

    public function storeDatabase(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'db_name' => 'required|string|alpha_dash|max:15',
            'db_username' => 'required|string|alpha_dash|max:15',
            'db_password' => 'required|string|max:32',
        ]);

        $user = User::findOrFail($request->user_id);
        
        $prefix = 'ryz_'.$user->id.'_';
        $cleanDbName = $prefix.strtolower(trim($request->db_name));
        $cleanUsername = $prefix.strtolower(trim($request->db_username));
        $dbPassword = $prefix.trim($request->db_password);

        if (HostingDatabase::where('db_name', $cleanDbName)->exists()) {
            return back()->with('error', 'Nama database "'.$cleanDbName.'" sudah digunakan.');
        }

        $rootPass = config('services.panel_mysql.root_password');
        $mysqlHost = config('services.panel_mysql.host');

        if (! $rootPass) {
            return back()->with('error', 'Konfigurasi Root MySQL belum diatur oleh Admin.');
        }

        try {
            $pdo = new \PDO("mysql:host={$mysqlHost};port=3306", 'root', $rootPass);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$cleanDbName`");
            $quotedPassword = $pdo->quote($dbPassword);
            $pdo->exec("CREATE USER IF NOT EXISTS '$cleanUsername'@'%' IDENTIFIED BY $quotedPassword");
            $pdo->exec("GRANT ALL PRIVILEGES ON `$cleanDbName`.* TO '$cleanUsername'@'%'");
            $pdo->exec('FLUSH PRIVILEGES');
        } catch (\PDOException $e) {
            return back()->with('error', 'Gagal membuat database: '.$e->getMessage());
        }

        HostingDatabase::create([
            'user_id' => $user->id,
            'db_name' => $cleanDbName,
            'db_username' => $cleanUsername,
            'db_password' => \Illuminate\Support\Facades\Crypt::encryptString($dbPassword),
            'host' => $mysqlHost,
        ]);

        return back()->with('success', 'Database '.$cleanDbName.' berhasil dibuat untuk klien!');
    }

    public function destroyDatabase($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $database = HostingDatabase::findOrFail($decoded[0]);

        $rootPass = config('services.panel_mysql.root_password');
        $mysqlHost = config('services.panel_mysql.host', '1Panel-mysql-KZAi');

        if ($rootPass) {
            try {
                $pdo = new \PDO("mysql:host={$mysqlHost};port=3306", 'root', $rootPass);
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $pdo->exec("DROP DATABASE IF EXISTS `$database->db_name`");
                $pdo->exec("DROP USER IF EXISTS '$database->db_username'@'%'");
                $pdo->exec('FLUSH PRIVILEGES');
            } catch (\PDOException $e) {
                \Log::error('Gagal hapus DB di server MySQL: '.$e->getMessage());
            }
        }

        $database->delete();
        return back()->with('success', 'Database berhasil dihapus!');
    }

    public function suspendProject(Request $request, $hashid)
    {
        $project = HostingProject::findOrFail(Hashids::decode($hashid)[0]);
        $project->update(['status' => 'suspended']);

        $subdomain = explode('.', $project->ryaze_domain)[0];
        $filePath = "/www/sites/hosting_clients/{$subdomain}/.suspended";
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $filePath = substr(base_path(), 0, 2) . str_replace('/', '\\', $filePath);
        }

        // Buat file marker agar Nginx 503
        touch($filePath);
        chmod($filePath, 0666);

        return back()->with('success', "Project '{$project->project_name}' telah disuspend.");
    }

    public function activateProject(Request $request, $hashid)
    {
        $project = HostingProject::findOrFail(Hashids::decode($hashid)[0]);
        $project->update(['status' => 'active']);

        $subdomain = explode('.', $project->ryaze_domain)[0];
        $filePath = "/www/sites/hosting_clients/{$subdomain}/.suspended";
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $filePath = substr(base_path(), 0, 2) . str_replace('/', '\\', $filePath);
        }

        // Hapus file marker agar Nginx kembali normal
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return back()->with('success', "Project '{$project->project_name}' berhasil diaktifkan.");
    }

    /**
     * Hapus project beserta relasi (cascade dari DB).
     */
    public function destroyProject(Request $request, $hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) {
            abort(404);
        }

        $project = HostingProject::findOrFail($decoded[0]);
        $projectName = $project->project_name;

        // Hapus Record DNS Cloudflare
        $this->deleteCloudflareDNS($project->ryaze_domain);

        // Hapus folder server (opsional, sesuaikan path)
        $subdomain = explode('.', $project->ryaze_domain)[0];
        $projectDir = "/www/sites/hosting_clients/{$subdomain}";
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $projectDir = substr(base_path(), 0, 2) . str_replace('/', '\\', $projectDir);
        }
        if (is_dir($projectDir)) {
            exec('rm -rf '.escapeshellarg($projectDir));
        }

        $project->delete();

        return back()->with('success', "Project '{$projectName}' berhasil dihapus.");
    }

    private function deleteCloudflareDNS($domainName)
    {
        $apiToken = config('services.cloudflare.api_token', env('CLOUDFLARE_API_TOKEN'));

        if (!$apiToken) return;

        $zoneName = explode('.', $domainName, 2)[1] ?? $domainName;
        $zoneId = config('services.cloudflare.zone_id', env('CLOUDFLARE_ZONE_ID'));
        
        $zoneReq = \Illuminate\Support\Facades\Http::withToken($apiToken)->get("https://api.cloudflare.com/client/v4/zones", ['name' => $zoneName]);
        if ($zoneReq->successful() && !empty($zoneReq->json('result'))) {
            $zoneId = $zoneReq->json('result.0.id');
        }

        if (!$zoneId) return;

        // Cari Record ID
        $response = \Illuminate\Support\Facades\Http::withToken($apiToken)
            ->get("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records", [
                'type' => 'CNAME',
                'name' => $domainName,
            ]);

        if ($response->successful() && ! empty($response->json('result'))) {
            $recordId = $response->json('result.0.id');
            // Hapus Record
            \Illuminate\Support\Facades\Http::withToken($apiToken)->delete("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records/{$recordId}");
        }
    }
}
