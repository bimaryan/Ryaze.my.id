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
            . "    location /.well-known/acme-challenge/ {\n"
            . "        root /www/letsencrypt;\n"
            . "    }\n\n"
            . "    location / {\n"
            . "        proxy_pass http://127.0.0.1;\n"
            . "        proxy_set_header Host {$project->ryaze_domain};\n"
            . "        proxy_set_header X-Real-IP \$remote_addr;\n"
            . "        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;\n"
            . "        proxy_set_header X-Forwarded-Proto \$scheme;\n"
            . "    }\n"
            . "}\n";
            
        $configDir = "/opt/1panel/apps/openresty/openresty/conf/conf.d/custom_domains";
        $configPath = "{$configDir}/{$domainName}.conf";
        $webrootDir = "/opt/1panel/apps/openresty/openresty/www/letsencrypt";
        
        // Buat folder jika belum ada dan tulis config
        shell_exec("sudo mkdir -p {$configDir}");
        shell_exec("sudo mkdir -p {$webrootDir}");
        
        $tmpFile = tempnam(sys_get_temp_dir(), 'nginx_');
        file_put_contents($tmpFile, $nginxConfig);
        shell_exec("sudo mv {$tmpFile} {$configPath}");
        shell_exec("sudo chown root:root {$configPath}");
        
        // Reload Nginx via Docker 1Panel
        shell_exec("sudo docker exec 1panel-openresty nginx -s reload");

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
        $configDir = "/opt/1panel/apps/openresty/openresty/conf/conf.d/custom_domains";
        $configPath = "{$configDir}/{$domainName}.conf";
        $sslDirHost = "/opt/1panel/apps/openresty/openresty/www/ssl/{$domainName}";
        
        // Hapus konfigurasi Nginx dan folder SSL
        shell_exec("sudo rm -f {$configPath}");
        shell_exec("sudo rm -rf {$sslDirHost}");
        shell_exec("sudo certbot delete --cert-name {$domainName} --non-interactive 2>/dev/null");
        
        // Reload Nginx via Docker
        shell_exec("sudo docker exec 1panel-openresty nginx -s reload");

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
        $webrootDir = "/opt/1panel/apps/openresty/openresty/www/letsencrypt";
        
        // Eksekusi request SSL via Certbot webroot plugin
        $output = shell_exec("sudo certbot certonly --webroot -w {$webrootDir} -d {$domainName} --non-interactive --agree-tos -m admin@ryaze.my.id 2>&1");
        
        if (strpos($output, 'Congratulations') !== false || strpos($output, 'Successfully') !== false || strpos($output, 'Certificate not yet due for renewal') !== false) {
            
            // Salin certs ke folder www agar bisa dibaca Docker OpenResty
            $sslDirHost = "/opt/1panel/apps/openresty/openresty/www/ssl/{$domainName}";
            shell_exec("sudo mkdir -p {$sslDirHost}");
            shell_exec("sudo cp /etc/letsencrypt/live/{$domainName}/fullchain.pem {$sslDirHost}/fullchain.pem");
            shell_exec("sudo cp /etc/letsencrypt/live/{$domainName}/privkey.pem {$sslDirHost}/privkey.pem");
            
            // Update Nginx config to HTTPS
            $nginxConfig = "server {\n"
                . "    listen 80;\n"
                . "    server_name {$domainName};\n\n"
                . "    location /.well-known/acme-challenge/ {\n"
                . "        root /www/letsencrypt;\n"
                . "    }\n\n"
                . "    location / {\n"
                . "        return 301 https://\$host\$request_uri;\n"
                . "    }\n"
                . "}\n\n"
                . "server {\n"
                . "    listen 443 ssl http2;\n"
                . "    server_name {$domainName};\n\n"
                . "    ssl_certificate /www/ssl/{$domainName}/fullchain.pem;\n"
                . "    ssl_certificate_key /www/ssl/{$domainName}/privkey.pem;\n\n"
                . "    location / {\n"
                . "        proxy_pass http://127.0.0.1;\n"
                . "        proxy_set_header Host {$domain->project->ryaze_domain};\n"
                . "        proxy_set_header X-Real-IP \$remote_addr;\n"
                . "        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;\n"
                . "        proxy_set_header X-Forwarded-Proto \$scheme;\n"
                . "    }\n"
                . "}\n";
                
            $configDir = "/opt/1panel/apps/openresty/openresty/conf/conf.d/custom_domains";
            $configPath = "{$configDir}/{$domainName}.conf";
            
            $tmpFile = tempnam(sys_get_temp_dir(), 'nginx_');
            file_put_contents($tmpFile, $nginxConfig);
            shell_exec("sudo mv {$tmpFile} {$configPath}");
            shell_exec("sudo chown root:root {$configPath}");
            
            // Reload Nginx via Docker
            shell_exec("sudo docker exec 1panel-openresty nginx -s reload");

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
