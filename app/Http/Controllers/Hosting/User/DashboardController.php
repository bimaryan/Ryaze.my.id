<?php

namespace App\Http\Controllers\Hosting\User;

use App\Http\Controllers\Controller;
use App\Jobs\AutoDeployProject;
use App\Models\HostingProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids; // Pastikan ini ter-import

class DashboardController extends Controller
{
    // Menampilkan halaman dashboard hosting klien
    public function index()
    {
        $projects = HostingProject::where('user_id', Auth::id())->latest()->get();

        return view('pages.hosting.user.index', compact('projects'));
    }

    // Menampilkan form deploy baru
    public function create()
    {
        return view('pages.hosting.user.create');
    }

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
        // Pastikan path ini sesuai dengan settingan Nginx/Docker 1Panel mas
        $envPath = "/www/sites/hosting_clients/{$subdomain}/.env";

        $envContent = '';
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);
        }

        return view('pages.hosting.user.show', compact('project', 'envContent'));
    }

    // Tambahkan method BARU ini di bawahnya:
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

        // Tulis isi textarea langsung ke file .env di server
        file_put_contents($envPath, $content);

        return back()->with('success', 'Environment variables berhasil disimpan!');
    }

    public function redeploy($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) {
            abort(404);
        }

        $project = HostingProject::where('user_id', Auth::id())->findOrFail($decoded[0]);

        // Set status ke building
        $project->update(['status' => 'building']);

        // Tambahkan log baru
        $project->deployments()->create([
            'status' => 'queued',
            'build_logs' => "> Memulai proses Redeploy manual...\n> Mengambil perubahan terbaru dari repository...",
        ]);

        // Jalankan Job lagi
        AutoDeployProject::dispatch($project);

        return back()->with('success', 'Redeploy berhasil dimulai! Silakan tunggu beberapa saat.');
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

        // Pastikan path ini sesuai dengan struktur root direktori mas
        $projectDir = "/www/sites/hosting_clients/{$subdomain}";

        $command = $request->input('command');

        if (empty($command)) {
            return response()->json(['output' => '', 'exit_code' => 0]);
        }

        // Susun perintah: Pindah ke folder project terlebih dahulu, baru jalankan command dari user.
        // Tambahkan 2>&1 agar error (stderr) juga tertangkap dan ditampilkan di terminal.
        $fullCommand = 'cd '.escapeshellarg($projectDir).' && '.$command.' 2>&1';

        // Eksekusi perintah shell
        exec($fullCommand, $outputArray, $exitCode);

        // Gabungkan array output menjadi string dengan baris baru
        $outputString = implode("\n", $outputArray);

        return response()->json([
            'output' => $outputString,
            'exit_code' => $exitCode,
        ]);
    }
}
