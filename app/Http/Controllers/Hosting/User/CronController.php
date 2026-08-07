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
    /** User berperan viewer pada project — tidak boleh menulis. */
    private function isViewerOnly(HostingProject $project): bool
    {
        if ($project->user_id === Auth::id()) {
            return false;
        }
        if (in_array(Auth::user()->role, ['superadmin', 'admin_hosting'], true)) {
            return false;
        }
        $member = $project->teamMembers()->wherePivot('user_id', Auth::id())->first();

        return $member && ($member->pivot->role ?? null) === 'viewer';
    }

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

        if ($this->isViewerOnly($project)) {
            return back()->with('error', 'Akses ditolak. Anda hanya berperan sebagai Viewer pada project ini.');
        }

        $request->validate([
            'command' => 'required|string|max:255',
            'schedule_expression' => 'required|string|max:100',
        ]);

        $command = trim($request->command);

        // ════ SECURITY: command cron harus dari allowlist & tanpa shell metachar ════
        $unsafeMeta = [';', '&', '`', '|', '<', '>', '$', '(', ')', '{', '}', '[', ']', '*', '?', '!', '~', '#', '"', "'", '\\', "\n"];
        foreach ($unsafeMeta as $meta) {
            if (str_contains($command, $meta)) {
                return back()->with('error', "Cron ditolak: karakter '{$meta}' tidak diizinkan (mencegah chaining/injection).");
            }
        }

        $allowedCronCommands = [
            'ls', 'cat', 'head', 'tail', 'wc', 'grep', 'find', 'echo', 'pwd', 'date',
            'php', 'composer', 'npm', 'npx', 'node', 'python', 'python3', 'pip', 'pip3',
            'mkdir', 'touch', 'cp', 'mv', 'rm', 'git', 'curl', 'source', 'chmod', 'chown',
            'tar', 'unzip', 'zip', 'clear', 'true', 'false',
        ];
        $firstWord = explode(' ', $command)[0];
        if (! in_array($firstWord, $allowedCronCommands, true)) {
            return back()->with('error', "Cron ditolak: command '{$firstWord}' tidak ada di daftar yang diizinkan.");
        }

        HostingCron::create([
            'project_id' => $project->id,
            'command' => $command,
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

        if ($this->isViewerOnly(HostingProject::findOrFail($cron->project_id))) {
            return back()->with('error', 'Akses ditolak. Anda hanya berperan sebagai Viewer pada project ini.');
        }

        $cron->delete();

        return back()->with('success', 'Cron Job berhasil dihapus.');
    }
}
