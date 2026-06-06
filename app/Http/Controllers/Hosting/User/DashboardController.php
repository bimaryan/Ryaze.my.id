<?php

namespace App\Http\Controllers\Hosting\User;

use App\Http\Controllers\Controller;
use App\Jobs\AutoDeployProject;
use App\Models\HostingProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

class DashboardController extends Controller
{
    // Menampilkan halaman dashboard hosting klien
    public function index()
    {
        $projects = HostingProject::where('user_id', Auth::id())->latest()->get();

        // Menghitung statistik berdasarkan data user
        $stats = [
            'active' => $projects->where('status', 'active')->count(),
            'unpaid' => $projects->where('status', 'unpaid')->count(),
            'tickets' => 0,
        ];

        return view('pages.hosting.user.index', compact('projects', 'stats'));
    }

    // Menampilkan form deploy baru
    public function create()
    {
        return view('pages.hosting.user.create');
    }

    // Menampilkan daftar project
    public function projects()
    {
        $projects = HostingProject::where('user_id', Auth::id())->latest()->get();

        return view('pages.hosting.user.project', compact('projects'));
    }

    // Memproses data dan memulai Deploy Otomatis
    public function store(Request $request)
    {
        $request->validate([
            'repo_source' => 'required|url',
            'project_name' => 'required|string|max:50|unique:hosting_projects,project_name',
            'framework' => 'required|in:react,nextjs,python,html,laravel,node',
            'branch' => 'required|string|max:50',
        ]);

        $subdomain = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->project_name)));

        $project = HostingProject::create([
            'user_id' => Auth::id(),
            'project_name' => $request->project_name,
            'framework' => $request->framework,
            'repo_source' => $request->repo_source,
            'branch' => $request->branch,
            'ryaze_domain' => $subdomain.'.ryaze.my.id',
            'status' => 'building',
        ]);

        $project->deployments()->create([
            'status' => 'queued',
            'build_logs' => "> Initialize build pipeline...\n> Menunggu worker tersedia...\n> Mengambil repository dari ".$request->repo_source."\n> Branch: ".$request->branch."\n> Menyiapkan environment ".strtoupper($request->framework).'...',
        ]);

        // EKSEKUSI JOB OTOMATIS DISINI
        AutoDeployProject::dispatch($project);

        return redirect()->route('user_hosting.show', $project->hashid)->with('success', 'Deployment berhasil dimulai!');
    }

    // Menampilkan halaman Terminal & Log
    public function show($hashed_id)
    {
        $decoded = Hashids::decode($hashed_id);

        if (empty($decoded)) {
            abort(404);
        }

        $project = HostingProject::with(['deployments' => function ($query) {
            $query->latest();
        }])
            ->where('user_id', Auth::id())
            ->findOrFail($decoded[0]);

        // Membaca file .env klien secara langsung dari VPS
        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);
        $envPath = "/www/sites/hosting_clients/{$subdomain}/.env";

        $envContent = '';
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);
        }

        return view('pages.hosting.user.show', compact('project', 'envContent'));
    }

    // Memperbarui file .env
    public function updateEnv(Request $request, $hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) {
            abort(404);
        }

        $project = HostingProject::where('user_id', Auth::id())->findOrFail($decoded[0]);
        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);

        $envPath = "/www/sites/hosting_clients/{$subdomain}/.env";
        $content = $request->input('env_content', '');

        try {
            // Coba paksakan write permission jika file ada
            if (file_exists($envPath)) {
                @chmod($envPath, 0666);
            }

            // Tulis isi textarea langsung ke file .env di server dengan peredam error (@)
            $result = @file_put_contents($envPath, $content);

            if ($result === false) {
                // Jika web server masih ditolak Linux (Permission Denied)
                return back()->with('error', 'Gagal menyimpan .env! Pastikan folder project memiliki permission www-data (chown).');
            }

            return back()->with('success', 'Environment variables berhasil disimpan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: '.$e->getMessage());
        }
    }

    // Memproses Redeploy
    public function redeploy($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) {
            abort(404);
        }

        $project = HostingProject::where('user_id', Auth::id())->findOrFail($decoded[0]);

        $project->update(['status' => 'building']);

        $project->deployments()->create([
            'status' => 'queued',
            'build_logs' => "> Memulai proses Redeploy manual...\n> Mengambil perubahan terbaru dari repository...",
        ]);

        AutoDeployProject::dispatch($project);

        return back()->with('success', 'Redeploy berhasil dimulai! Silakan tunggu beberapa saat.');
    }

    // Mengambil build log terakhir untuk polling AJAX
    public function buildLogs($hashid)
    {
        $decoded = Hashids::decode($hashid);

        if (empty($decoded)) {
            return response()->json(['error' => 'Project tidak ditemukan.'], 404);
        }

        $project = HostingProject::with(['deployments' => function ($query) {
            $query->latest();
        }])
            ->where('user_id', Auth::id())
            ->findOrFail($decoded[0]);

        $deployment = $project->deployments->first();

        return response()->json([
            'build_logs' => $deployment?->build_logs ?? '',
            'status' => $project->status,
            'deployment_status' => $deployment?->status,
            'website_url' => 'https://'.$project->ryaze_domain,
            'last_updated' => $deployment?->updated_at?->toDateTimeString(),
        ]);
    }

    // Memproses perintah dari Web Terminal
    public function terminal(Request $request, $hashid)
    {
        $decoded = Hashids::decode($hashid);

        if (empty($decoded)) {
            return response()->json(['error' => 'Project tidak ditemukan.'], 404);
        }

        $project = HostingProject::where('user_id', Auth::id())->findOrFail($decoded[0]);
        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);

        $projectDir = "/www/sites/hosting_clients/{$subdomain}";
        $command = $request->input('command');

        if (empty($command)) {
            return response()->json(['output' => '', 'exit_code' => 0]);
        }

        // ════════ MANTRA ANTI-BLEEDING ════════
        // Menghapus paksa env portal dari memori shell agar artisan klien membaca dari .env mereka sendiri
        $unsetEnv = 'unset APP_NAME APP_ENV APP_KEY APP_DEBUG APP_URL LOG_CHANNEL DB_CONNECTION DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD BROADCAST_DRIVER CACHE_DRIVER QUEUE_CONNECTION SESSION_DRIVER SESSION_LIFETIME REDIS_HOST REDIS_PASSWORD REDIS_PORT; ';

        // Susun perintah dengan unset di depannya
        $fullCommand = $unsetEnv.'cd '.escapeshellarg($projectDir).' && '.$command.' 2>&1';
        // ══════════════════════════════════════

        exec($fullCommand, $outputArray, $exitCode);
        $outputString = implode("\n", $outputArray);

        return response()->json([
            'output' => $outputString,
            'exit_code' => $exitCode,
        ]);
    }
}
