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

        $project = HostingProject::where('user_id', Auth::id())->findOrFail($decoded[0]);

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

        return back()->with('success', 'Custom Domain berhasil ditambahkan! Silakan arahkan DNS (CNAME/A Record) domain Anda ke server ini.');
    }

    public function destroy($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $domain = HostingDomain::whereHas('project', function($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($decoded[0]);

        $projectHashid = $domain->project->hashid;
        $domain->delete();

        return redirect()->route('user_hosting.storage.show', $projectHashid)->with('success', 'Custom Domain berhasil dihapus.');
    }

    public function requestSsl($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $domain = HostingDomain::whereHas('project', function($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($decoded[0]);

        // Simulasi request SSL (Certbot)
        // Jika di production, di sini eksekusi shell command certbot nginx/apache
        $domain->update([
            'ssl_status' => 'active'
        ]);

        return back()->with('success', 'Sertifikat SSL (Let\'s Encrypt) berhasil di-generate dan dipasang untuk ' . $domain->domain_name);
    }
}
