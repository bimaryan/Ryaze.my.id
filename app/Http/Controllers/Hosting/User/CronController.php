<?php

namespace App\Http\Controllers\Hosting\User;

use App\Http\Controllers\Controller;
use App\Models\HostingProject;
use App\Models\HostingCron;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

class CronController extends Controller
{
    public function store(Request $request, $projectHashid)
    {
        $decoded = Hashids::decode($projectHashid);
        if (empty($decoded)) abort(404);

        $project = HostingProject::where('user_id', Auth::id())->findOrFail($decoded[0]);

        $request->validate([
            'command' => 'required|string|max:255',
            'schedule_expression' => 'required|string|max:100',
        ]);

        HostingCron::create([
            'project_id' => $project->id,
            'command' => trim($request->command),
            'schedule_expression' => trim($request->schedule_expression),
            'is_active' => true,
        ]);

        return back()->with('success', 'Cron Job berhasil ditambahkan dan akan dijalankan sesuai jadwal.');
    }

    public function destroy($hashid)
    {
        $cron = HostingCron::whereHas('project', function($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($hashid);

        $cron->delete();

        return back()->with('success', 'Cron Job berhasil dihapus.');
    }
}
