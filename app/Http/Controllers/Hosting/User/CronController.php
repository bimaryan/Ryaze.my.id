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

        $query = HostingProject::query();
        if (!in_array(Auth::user()->role, ['superadmin', 'admin_hosting'])) {
            $query->where(function($q) {
                $q->where('user_id', Auth::id())
                  ->orWhereHas('teamMembers', function($sq) {
                      $sq->where('user_id', Auth::id());
                  });
            });
        }
        $project = $query->findOrFail($decoded[0]);

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
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $query = HostingCron::whereHas('project', function($q) {
            if (!in_array(Auth::user()->role, ['superadmin', 'admin_hosting'])) {
                $q->where(function($sq) {
                    $sq->where('user_id', Auth::id())
                      ->orWhereHas('teamMembers', function($tsq) {
                          $tsq->where('user_id', Auth::id());
                      });
                });
            }
        });
        
        $cron = $query->findOrFail($decoded[0]);

        $cron->delete();

        return back()->with('success', 'Cron Job berhasil dihapus.');
    }
}
