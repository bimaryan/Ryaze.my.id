<?php

namespace App\Http\Controllers\Hosting\User;

use App\Http\Controllers\Controller;
use App\Models\HostingProject;
use App\Models\HostingDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Vinkla\Hashids\Facades\Hashids;

class DomainController extends Controller
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

        $project = HostingProject::where(function($q) {
            $q->where('user_id', Auth::id())
              ->orWhereHas('teamMembers', function($sq) {
                  $sq->where('user_id', Auth::id());
              });
        })->findOrFail($decoded[0]);

        // Viewer hanya boleh membaca
        if ($this->isViewerOnly($project)) {
            return back()->with('error', 'Akses ditolak. Anda hanya berperan sebagai Viewer pada project ini.');
        }

        $request->validate([
            'domain_name' => 'required|string|max:255|unique:hosting_domains,domain_name',
        ], [
            'domain_name.unique' => 'Domain ini sudah didaftarkan di sistem.'
        ]);

        $domainName = strtolower(trim($request->domain_name));
        $domainName = preg_replace('#^https?://#', '', $domainName);

        $apiToken = env('CLOUDFLARE_API_TOKEN');
        $primaryZoneId = env('CLOUDFLARE_ZONE_ID');

        // 1. Get Account ID from Primary Zone
        $zoneInfoRes = Http::withToken($apiToken)->get("https://api.cloudflare.com/client/v4/zones/{$primaryZoneId}");
        if (!$zoneInfoRes->successful()) {
            return back()->with('error', 'Gagal memverifikasi akun Cloudflare.');
        }
        $accountId = $zoneInfoRes->json('result.account.id');

        // 2. Create New Zone
        $createZoneRes = Http::withToken($apiToken)->post("https://api.cloudflare.com/client/v4/zones", [
            'name' => $domainName,
            'account' => ['id' => $accountId],
            'type' => 'full'
        ]);

        if (!$createZoneRes->successful()) {
            $errorMsg = $createZoneRes->json('errors.0.message') ?? 'Unknown error';
            return back()->with('error', 'Gagal mendaftarkan domain di Cloudflare: ' . $errorMsg);
        }

        $newZoneId = $createZoneRes->json('result.id');
        $nameservers = $createZoneRes->json('result.name_servers');

        HostingDomain::create([
            'project_id' => $project->id,
            'domain_name' => $domainName,
            'ssl_status' => 'pending',
            'cf_zone_id' => $newZoneId,
            'nameservers' => $nameservers,
        ]);

        return back()->with('success', 'Domain berhasil didaftarkan! Silakan arahkan Nameserver domain Anda ke yang tertera di bawah ini.');
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

        if ($this->isViewerOnly($domain->project)) {
            return back()->with('error', 'Akses ditolak. Anda hanya berperan sebagai Viewer pada project ini.');
        }

        $projectHashid = $domain->project->hashid;
        
        $apiToken = env('CLOUDFLARE_API_TOKEN');

        if ($domain->cf_zone_id) {
            Http::withToken($apiToken)->delete("https://api.cloudflare.com/client/v4/zones/{$domain->cf_zone_id}");
        }

        $domainName = $domain->domain_name;
        $domain->delete();
        
        $mapDir = hosting_clients_dir() . '/.domains';
        if (file_exists("{$mapDir}/{$domainName}")) @unlink("{$mapDir}/{$domainName}");
        if (file_exists("{$mapDir}/www.{$domainName}")) @unlink("{$mapDir}/www.{$domainName}");

        return redirect()->route('user_hosting.show', $projectHashid)->with('success', 'Custom Domain berhasil dihapus dari sistem & Cloudflare.');
    }

    public function checkStatus($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $domain = HostingDomain::whereHas('project', function($q) {
            $q->where('user_id', Auth::id())
              ->orWhereHas('teamMembers', function($sq) {
                  $sq->where('user_id', Auth::id());
              });
        })->findOrFail($decoded[0]);

        if (!$domain->cf_zone_id) {
            return back()->with('error', 'Zone ID tidak ditemukan.');
        }

        $apiToken = env('CLOUDFLARE_API_TOKEN');
        
        // Force Cloudflare to re-check nameservers immediately
        Http::withToken($apiToken)->put("https://api.cloudflare.com/client/v4/zones/{$domain->cf_zone_id}/activation_check");
        sleep(2); // Wait a moment for Cloudflare to process

        $res = Http::withToken($apiToken)->get("https://api.cloudflare.com/client/v4/zones/{$domain->cf_zone_id}");

        if ($res->successful()) {
            $status = $res->json('result.status');
            if ($status === 'active') {
                $domain->update(['ssl_status' => 'active']);

                // Create CNAME records to tunnel
                $tunnelUrl = env('CLOUDFLARE_TUNNEL_URL');
                if ($tunnelUrl) {
                    // Create @ record
                    Http::withToken($apiToken)->post("https://api.cloudflare.com/client/v4/zones/{$domain->cf_zone_id}/dns_records", [
                        'type' => 'CNAME',
                        'name' => '@',
                        'content' => $tunnelUrl,
                        'proxied' => true
                    ]);
                    // Create www record
                    Http::withToken($apiToken)->post("https://api.cloudflare.com/client/v4/zones/{$domain->cf_zone_id}/dns_records", [
                        'type' => 'CNAME',
                        'name' => 'www',
                        'content' => $tunnelUrl,
                        'proxied' => true
                    ]);
                }

                // [FIX CUSTOM DOMAIN 1Panel] Tulis mapping subdomain
                $subdomain = $domain->project->subdomain;
                $mapDir = hosting_clients_dir() . '/.domains';
                if (!file_exists($mapDir)) {
                    mkdir($mapDir, 0755, true);
                }
                file_put_contents("{$mapDir}/{$domain->domain_name}", $subdomain);
                file_put_contents("{$mapDir}/www.{$domain->domain_name}", $subdomain);

                return back()->with('success', 'Nameserver berhasil tersambung! DNS Record telah dibuat otomatis.');
            }
        }

        return back()->with('error', 'Nameserver belum tersambung. Biasanya butuh waktu propagasi hingga 24 jam setelah Anda mengubah NS di tempat pembelian domain.');
    }
}
