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

        $nginxConfig = "server {\n"
            . "    listen 80;\n"
            . "    server_name {$domainName};\n\n"
            . "    location / {\n"
            . "        proxy_pass http://127.0.0.1;\n"
            . "        proxy_set_header Host {$project->ryaze_domain};\n"
            . "        proxy_set_header X-Real-IP \$remote_addr;\n"
            . "        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;\n"
            . "        proxy_set_header X-Forwarded-Proto \$scheme;\n"
            . "    }\n"
            . "}\n";
            
        $configPath = "/etc/nginx/conf.d/custom_domains/{$domainName}.conf";
        
        // Buat folder jika belum ada dan tulis config
        shell_exec("sudo mkdir -p /etc/nginx/conf.d/custom_domains");
        $tmpFile = tempnam(sys_get_temp_dir(), 'nginx_');
        file_put_contents($tmpFile, $nginxConfig);
        shell_exec("sudo mv {$tmpFile} {$configPath}");
        shell_exec("sudo chown root:root {$configPath}");
        
        // Reload Nginx
        shell_exec("sudo systemctl reload nginx");

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
        $configPath = "/etc/nginx/conf.d/custom_domains/{$domainName}.conf";
        
        // Hapus konfigurasi Nginx dan SSL
        shell_exec("sudo rm -f {$configPath}");
        shell_exec("sudo certbot delete --cert-name {$domainName} --non-interactive 2>/dev/null");
        shell_exec("sudo systemctl reload nginx");

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
        
        // Eksekusi request SSL via Certbot Nginx plugin
        $output = shell_exec("sudo certbot --nginx -d {$domainName} --non-interactive --agree-tos -m admin@ryaze.my.id --redirect 2>&1");
        
        if (strpos($output, 'Congratulations') !== false || strpos($output, 'Successfully') !== false) {
            $domain->update([
                'ssl_status' => 'active'
            ]);
            return back()->with('success', 'Sertifikat SSL (Let\'s Encrypt) berhasil di-generate dan dipasang untuk ' . $domainName);
        }

        $domain->update([
            'ssl_status' => 'failed'
        ]);
        
        return back()->with('error', 'Gagal request SSL. Output: ' . substr($output, 0, 200));
    }
}
