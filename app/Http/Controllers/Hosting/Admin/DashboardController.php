<?php

namespace App\Http\Controllers\Hosting\Admin;

use App\Http\Controllers\Controller;
use App\Models\HostingBilling;
use App\Models\HostingDatabase;
use App\Models\HostingDeployment;
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
            'pending_billing' => HostingBilling::where('status', 'unpaid')->count(),
            'building_now' => HostingProject::where('status', 'building')->count(),
            'action_required' => HostingProject::whereIn('status', ['unpaid', 'error', 'suspended'])->count(),
        ];

        return view('pages.hosting.admin.index', compact('stats'));
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

    /**
     * Aktifkan project (ubah status menjadi 'active').
     */
    public function activateProject(Request $request, $hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) {
            abort(404);
        }

        $project = HostingProject::findOrFail($decoded[0]);
        $project->update(['status' => 'active']);

        // Tandai billing terkait sebagai paid jika ada
        $project->billing?->update(['status' => 'paid']);

        return back()->with('success', "Project '{$project->project_name}' berhasil diaktifkan.");
    }

    /**
     * Suspend project.
     */
    public function suspendProject(Request $request, $hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) {
            abort(404);
        }

        $project = HostingProject::findOrFail($decoded[0]);
        $project->update(['status' => 'suspended']);

        return back()->with('success', "Project '{$project->project_name}' telah disuspend.");
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

        // Hapus folder server (opsional, sesuaikan path)
        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);
        $projectDir = "/www/sites/hosting_clients/{$subdomain}";
        if (is_dir($projectDir)) {
            exec('rm -rf '.escapeshellarg($projectDir));
        }

        $project->delete();

        return back()->with('success', "Project '{$projectName}' berhasil dihapus.");
    }
}
