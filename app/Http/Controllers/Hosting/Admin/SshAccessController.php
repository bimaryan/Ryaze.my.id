<?php

namespace App\Http\Controllers\Hosting\Admin;

use App\Http\Controllers\Controller;
use App\Models\HostingProject;
use App\Models\HostingSshAccess;
use App\Services\SshClientService;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class SshAccessController extends Controller
{
    public function index()
    {
        $accesses = HostingSshAccess::with(['project', 'client'])
            ->latest()
            ->paginate(15);
        $projects = HostingProject::where('status', 'active')
            ->orderBy('project_name')
            ->get();

        return view('pages.hosting.admin.ssh_access.index', compact('accesses', 'projects'));
    }

    public function store(Request $request, SshClientService $service)
    {
        $data = $request->validate([
            'project_hashid' => 'required|string',
            'package' => 'required|in:sftp,shell',
        ]);

        $decoded = Hashids::decode($data['project_hashid']);
        abort_if(empty($decoded), 404);
        $project = HostingProject::findOrFail($decoded[0]);

        try {
            $access = $service->provision($project, $data['package']);
            if ($access->status === 'failed') {
                throw new \RuntimeException($access->last_output ?: 'Provisioning gagal.');
            }

            return back()
                ->with('success', "Akses SSH untuk {$project->project_name} berhasil dibuat.")
                ->with('ssh_user', $access->ssh_user)
                ->with('ssh_password', $access->ssh_password)
                ->with('ssh_command', $service->command($access));
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membuat akses SSH: '.$e->getMessage())->withInput();
        }
    }

    public function destroy($hashid, SshClientService $service)
    {
        $decoded = Hashids::decode($hashid);
        abort_if(empty($decoded), 404);
        $access = HostingSshAccess::findOrFail($decoded[0]);

        $ok = $service->revoke($access);

        return back()->with(
            $ok ? 'success' : 'error',
            $ok ? 'Akses SSH berhasil dicabut.' : 'Gagal mencabut: '.$access->last_output
        );
    }
}
