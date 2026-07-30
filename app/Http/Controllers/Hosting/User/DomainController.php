<?php

namespace App\Http\Controllers\Hosting\User;

use App\Http\Controllers\Controller;
use App\Models\HostingProject;
use App\Models\HostingDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

class DomainController extends Controller
{
    public function store(Request $request, $projectHashid)
    {
        $decoded = Hashids::decode($projectHashid);
        if (empty($decoded)) abort(404);

        $project = HostingProject::where(function($q) {
            $q->where('user_id', Auth::id())
              ->orWhereHas('teamMembers', function($sq) {
                  $sq->where('user_id', Auth::id());
              });
        })->findOrFail($decoded[0]);

        $request->validate([
            'domain_name' => 'required|string|max:255|unique:hosting_domains,domain_name',
        ], [
            'domain_name.unique' => 'Domain ini sudah didaftarkan di sistem.'
        ]);

        $domainName = strtolower(trim($request->domain_name));
        $domainName = preg_replace('#^https?://#', '', $domainName);
        HostingDomain::create([
            'project_id' => $project->id,
            'domain_name' => $domainName,
            'ssl_status' => 'pending',
        ]);

        $queuePath = storage_path('app/ssl_queue.json');
        $queue = [];
        if (file_exists($queuePath)) {
            $queue = json_decode(file_get_contents($queuePath), true) ?? [];
        }
        $queue[] = [
            'action' => 'add',
            'domain' => $domainName,
            'project_domain' => $project->ryaze_domain
        ];
        file_put_contents($queuePath, json_encode($queue));

        return back()->with('success', 'Custom Domain berhasil ditambahkan! Silakan arahkan DNS (CNAME/A Record) domain Anda ke server ini.');
    }

    public function destroy($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $domain = HostingDomain::whereHas('project', function($q) {
            $q->where('user_id', Auth::id())
              ->orWhereHas('teamMembers', function($sq) {
                  $sq->where('user_id', Auth::id());
              });
        })->findOrFail($decoded[0]);

        $projectHashid = $domain->project->hashid;
        $domainName = $domain->domain_name;
        $queuePath = storage_path('app/ssl_queue.json');
        $queue = [];
        if (file_exists($queuePath)) {
            $queue = json_decode(file_get_contents($queuePath), true) ?? [];
        }
        $queue[] = [
            'action' => 'delete',
            'domain' => $domainName
        ];
        file_put_contents($queuePath, json_encode($queue));

        $domain->delete();

        return redirect()->route('user_hosting.show', $projectHashid)->with('success', 'Custom Domain berhasil dihapus.');
    }

    public function requestSsl($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $domain = HostingDomain::whereHas('project', function($q) {
            $q->where('user_id', Auth::id())
              ->orWhereHas('teamMembers', function($sq) {
                  $sq->where('user_id', Auth::id());
              });
        })->findOrFail($decoded[0]);

        $domainName = $domain->domain_name;
        $domain->update([
            'ssl_status' => 'processing'
        ]);

        $queuePath = storage_path('app/ssl_queue.json');
        $queue = [];
        if (file_exists($queuePath)) {
            $queue = json_decode(file_get_contents($queuePath), true) ?? [];
        }
        $queue[] = [
            'action' => 'ssl',
            'domain' => $domainName,
            'project_domain' => $domain->project->ryaze_domain
        ];
        file_put_contents($queuePath, json_encode($queue));
        
        return back()->with('success', 'Permintaan SSL sedang diproses di latar belakang. Silakan refresh halaman ini dalam 1-2 menit.');
    }
}
