<?php

namespace App\Http\Controllers\Hosting\User;

use App\Http\Controllers\Controller;
use App\Models\HostingDomain;
use App\Models\HostingProject;
use App\Services\CloudflareTunnelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;
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

    /**
     * Halaman Domains terpisah (ala Vercel): semua domain milik user, baik yang
     * sudah tersambung ke project maupun yang masih menunggu.
     */
    public function index()
    {
        $domains = HostingDomain::where('user_id', Auth::id())
            ->with('project')
            ->latest()
            ->get();

        $projects = HostingProject::where('user_id', Auth::id())
            ->orderBy('project_name')
            ->get(['id', 'project_name', 'ryaze_domain', 'status']);

        return view('pages.hosting.user.domains.index', compact('domains', 'projects'));
    }

    /**
     * Daftarkan domain baru ke Cloudflare. Project belum tentu ada — user
     * menyambungkannya nanti dari tab Domains di halaman project.
     */
    public function store(Request $request)
    {
        $request->validate([
            'domain_name' => ['required', 'string', 'max:255', 'unique:hosting_domains,domain_name'],
            'project_id' => ['nullable', 'exists:hosting_projects,id'],
        ], [
            'domain_name.unique' => 'Domain ini sudah didaftarkan di sistem.',
        ]);

        $domainName = HostingDomain::normalizeName($request->domain_name);

        if (! filter_var($domainName, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return back()->with('error', "Format domain tidak valid. Contoh yang benar: tokoanda.com");
        }

        // Project opsional — kalau diisi, pastikan boleh ditulis user.
        $projectId = null;
        if ($request->filled('project_id')) {
            $project = HostingProject::findOrFail($request->project_id);

            if ($project->user_id !== Auth::id() && ! in_array(Auth::user()->role, ['superadmin', 'admin_hosting'], true)) {
                return back()->with('error', 'Project tersebut bukan milik Anda.');
            }
            if ($this->isViewerOnly($project)) {
                return back()->with('error', 'Akses ditolak. Anda hanya berperan sebagai Viewer pada project ini.');
            }
            $projectId = $project->id;
        }

        $apiToken = config('services.cloudflare.api_token');
        $primaryZoneId = config('services.cloudflare.zone_id');

        // 1. Ambil Account ID dari zone utama
        $zoneInfoRes = Http::withToken($apiToken)->get("https://api.cloudflare.com/client/v4/zones/{$primaryZoneId}");
        if (! $zoneInfoRes->successful()) {
            return back()->with('error', 'Gagal memverifikasi akun Cloudflare.');
        }
        $accountId = $zoneInfoRes->json('result.account.id');

        // 1b. Jangan buat zone kalau domain sudah ada di akun ini
        $existing = Http::withToken($apiToken)->get('https://api.cloudflare.com/client/v4/zones', [
            'name' => $domainName,
            'account.id' => $accountId,
            'per_page' => 1,
        ]);

        if ($existing->successful() && count($existing->json('result', [])) > 0) {
            return back()->with('error', "Domain {$domainName} sudah terdaftar di akun Cloudflare ini. Hapus zone lamanya di dashboard Cloudflare, lalu coba lagi.");
        }

        // 2. Buat zone baru di Cloudflare
        $createZoneRes = Http::withToken($apiToken)->post('https://api.cloudflare.com/client/v4/zones', [
            'name' => $domainName,
            'account' => ['id' => $accountId],
            'type' => 'full',
        ]);

        if (! $createZoneRes->successful()) {
            $errorMsg = $createZoneRes->json('errors.0.message') ?? 'Unknown error';

            return back()->with('error', 'Gagal mendaftarkan domain di Cloudflare: '.$errorMsg);
        }

        $domain = HostingDomain::create([
            'user_id' => Auth::id(),
            'project_id' => $projectId,
            'domain_name' => $domainName,
            'ssl_status' => 'pending',
            'cf_zone_id' => $createZoneRes->json('result.id'),
            'nameservers' => $createZoneRes->json('result.name_servers', []),
        ]);

        return redirect()
            ->route('user_hosting.domains.show', $domain->hashid)
            ->with('success', "Domain {$domainName} berhasil ditambahkan. Arahkan nameserver domain Anda ke Cloudflare agar bisa diverifikasi.");
    }

    /** Halaman detail satu domain: instruksi nameserver + status. */
    public function show($hashid)
    {
        $domain = $this->findOwnedDomain($hashid);

        return view('pages.hosting.user.domains.show', [
            'domain' => $domain,
            'projects' => HostingProject::where('user_id', Auth::id())->orderBy('project_name')->get(['id', 'project_name']),
        ]);
    }

    /**
     * Sambungkan domain yang sudah terverifikasi ke sebuah project.
     * Dipakai dari tab Domains di halaman detail project.
     */
    public function assign(Request $request, $hashid)
    {
        $domain = $this->findOwnedDomain($hashid);

        $request->validate([
            'project_id' => ['required', 'exists:hosting_projects,id'],
        ]);

        $project = HostingProject::findOrFail($request->project_id);

        if ($project->user_id !== Auth::id() && ! in_array(Auth::user()->role, ['superadmin', 'admin_hosting'], true)) {
            return back()->with('error', 'Project tersebut bukan milik Anda.');
        }
        if ($this->isViewerOnly($project)) {
            return back()->with('error', 'Akses ditolak. Anda hanya berperan sebagai Viewer pada project ini.');
        }

        // Satu project satu domain (mapping nginx pakai file .domains/<host>).
        $alreadyUsed = HostingDomain::where('project_id', $project->id)
            ->whereKeyNot($domain->id)
            ->exists();

        if ($alreadyUsed) {
            return back()->with('error', "Project {$project->project_name} sudah punya custom domain lain. Lepaskan dulu domain sebelumnya.");
        }

        $domain->update(['project_id' => $project->id]);

        // Zona belum aktif = belum bisa dilayani.
        if ($domain->ssl_status !== 'active') {
            return back()->with('error', "Domain {$domain->domain_name} belum terverifikasi. Selesaikan dulu perubahan nameserver, lalu tekan \"Cek Status\".");
        }

        $this->syncRouting($domain);

        return back()->with('success', "Domain {$domain->domain_name} sekarang melayani project {$project->project_name}.");
    }

    /** Putuskan domain dari project (domain tetap disimpan di daftar Domains). */
    public function detach($hashid)
    {
        $domain = $this->findOwnedDomain($hashid);

        if (! $domain->project_id) {
            return back()->with('error', 'Domain ini belum tersambung ke project mana pun.');
        }

        if ($domain->project && $this->isViewerOnly($domain->project)) {
            return back()->with('error', 'Akses ditolak. Anda hanya berperan sebagai Viewer pada project ini.');
        }

        $domain->update(['project_id' => null]);
        $this->clearRouting($domain);

        return back()->with('success', "Domain {$domain->domain_name} dilepas dari project. Domain tetap tersimpan di daftar Domains.");
    }

    public function destroy($hashid)
    {
        $domain = $this->findOwnedDomain($hashid);

        $project = $domain->project;
        if ($project && $this->isViewerOnly($project)) {
            return back()->with('error', 'Akses ditolak. Anda hanya berperan sebagai Viewer pada project ini.');
        }

        $apiToken = config('services.cloudflare.api_token');

        if ($domain->cf_zone_id) {
            Http::withToken($apiToken)->delete("https://api.cloudflare.com/client/v4/zones/{$domain->cf_zone_id}");
        }

        $this->clearRouting($domain);
        $domain->delete();

        return redirect()->route('user_hosting.domains.index')->with('success', 'Domain berhasil dihapus dari sistem & Cloudflare.');
    }

    /** Cek apakah nameserver sudah tersambung; kalau aktif, siapkan DNS + tunnel. */
    public function checkStatus($hashid)
    {
        $domain = $this->findOwnedDomain($hashid);

        if (! $domain->cf_zone_id) {
            return back()->with('error', 'Zone ID tidak ditemukan.');
        }

        $apiToken = config('services.cloudflare.api_token');

        // Paksa Cloudflare cek ulang nameserver seketika
        Http::withToken($apiToken)->put("https://api.cloudflare.com/client/v4/zones/{$domain->cf_zone_id}/activation_check");
        sleep(2);

        $res = Http::withToken($apiToken)->get("https://api.cloudflare.com/client/v4/zones/{$domain->cf_zone_id}");

        if ($res->successful() && $res->json('result.status') === 'active') {
            $domain->update(['ssl_status' => 'active']);

            $tunnelUrl = config('services.cloudflare.tunnel_url');
            if ($tunnelUrl) {
                foreach (['@', 'www'] as $record) {
                    Http::withToken($apiToken)->post("https://api.cloudflare.com/client/v4/zones/{$domain->cf_zone_id}/dns_records", [
                        'type' => 'CNAME',
                        'name' => $record,
                        'content' => $tunnelUrl,
                        'proxied' => true,
                    ]);
                }
            }

            // Kalau sudah disambungkan ke project, langsung layanin.
            $extra = $domain->project_id
                ? $this->syncRouting($domain)
                : '';

            return back()->with('success', 'Nameserver berhasil tersambung! DNS Record dibuat otomatis.'.$extra);
        }

        return back()->with('error', 'Nameserver belum tersambung. biasanya butuh propagasi hingga 24 jam setelah Anda mengubah NS di tempat pembelian domain.');
    }

    /**
     * Tulis mapping nginx (.domains) + daftarkan ingress rule tunnel.
     * Dipanggil setiap kali status jadi aktif atau domain disambungkan ke project.
     */
    private function syncRouting(HostingDomain $domain): string
    {
        $subdomain = $domain->project?->subdomain;
        $notes = [];

        if (! $subdomain) {
            return ' (Peringatan: project belum punya subdomain, mapping nginx dilewati.)';
        }

        $mapDir = hosting_clients_dir().'/.domains';
        if (! file_exists($mapDir)) {
            mkdir($mapDir, 0755, true);
        }

        foreach ($domain->hostnames() as $hostname) {
            file_put_contents("{$mapDir}/{$hostname}", $subdomain);
        }

        $notes[] = 'Domain sudah serves project '.$domain->project->project_name.'.';

        try {
            $tunnel = app(CloudflareTunnelService::class);

            if (! $tunnel->isConfigured()) {
                $notes[] = ' Cloudflare Tunnel belum dikonfigurasi, hostname belum didaftarkan di tunnel.';

                return implode('', $notes);
            }

            $service = config('services.cloudflare_tunnel.service', 'http://localhost:80');
            $ok = true;

            foreach ($domain->hostnames() as $hostname) {
                if (! $tunnel->registerRoute($hostname, $service)) {
                    $ok = false;
                    Log::warning("[Domain] Gagal register tunnel route {$hostname}");
                }
            }

            $notes[] = $ok
                ? ' Hostname terdaftar di Cloudflare Tunnel.'
                : ' Peringatan: hostname gagal didaftarkan di tunnel, coba lagi nanti.';
        } catch (Throwable $e) {
            Log::error("[Domain] Error register tunnel route {$domain->domain_name}: ".$e->getMessage());
            $notes[] = ' Peringatan: gagal mendaftarkan hostname di tunnel.';
        }

        return implode('', $notes);
    }

    /** Kebalikan mapping nginx + cabut ingress rule tunnel. */
    private function clearRouting(HostingDomain $domain): void
    {
        $mapDir = hosting_clients_dir().'/.domains';

        foreach ($domain->hostnames() as $hostname) {
            if (file_exists("{$mapDir}/{$hostname}")) {
                @unlink("{$mapDir}/{$hostname}");
            }
        }

        try {
            $tunnel = app(CloudflareTunnelService::class);
            foreach ($domain->hostnames() as $hostname) {
                $tunnel->unregisterRoute($hostname);
            }
        } catch (Throwable $e) {
            Log::warning("[Domain] Gagal unregister tunnel route {$domain->domain_name}: ".$e->getMessage());
        }
    }

    /** Cuma bisa akses domain milik sendiri (kecuali admin). */
    private function findOwnedDomain($hashid): HostingDomain
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) {
            abort(404);
        }

        $query = HostingDomain::query();

        if (! in_array(Auth::user()->role, ['superadmin', 'admin_hosting'], true)) {
            $query->where('user_id', Auth::id());
        }

        return $query->with('project')->findOrFail($decoded[0]);
    }
}