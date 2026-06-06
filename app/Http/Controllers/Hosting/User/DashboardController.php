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

        return view('pages.hosting.user.show', compact('project'));
    }
}
