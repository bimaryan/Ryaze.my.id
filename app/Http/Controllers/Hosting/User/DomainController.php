<?php

namespace App\Http\Controllers\Hosting\User;

use App\Http\Controllers\Controller;
use App\Models\HostingDomain;
use App\Models\HostingProject;
use App\Models\HostingDomainRedirect;
use App\Services\CloudflareTunnelService;
use App\Services\DomainRedirectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;
use Vinkla\Hashids\Facades\Hashids;

class DomainController extends Controller
{
    /** Pesan error terakhir dari helper validasi project. */
    private ?string $projectError = null;

    /** Pesan error terakhir dari helper validasi label. */
    private ?string $labelError = null;

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
            ->with(['project', 'parent'])
            ->latest()
            ->get();

        $projects = HostingProject::where('user_id', Auth::id())
            ->orderBy('project_name')
            ->get(['id', 'project_name', 'ryaze_domain', 'status']);

        // Hanya domain root aktif yang bisa dijadikan induk subdomain.
        $rootDomains = HostingDomain::where('user_id', Auth::id())
            ->where('type', HostingDomain::TYPE_CUSTOM)
            ->where('ssl_status', 'active')
            ->orderBy('domain_name')
            ->get(['id', 'domain_name']);

        return view('pages.hosting.user.domains.index', compact('domains', 'projects', 'rootDomains'));
    }

    /**
     * Daftarkan domain root baru ke Cloudflare. Project belum tentu ada — user
     * menyambungkannya nanti dari tab Domains di halaman project.
     */
    public function store(Request $request)
    {
        $check = $this->requireCustomDomainPlan();
        if ($check !== null) {
            return $check;
        }

        $request->validate([
            'domain_name' => ['required', 'string', 'max:255', 'unique:hosting_domains,domain_name'],
            'project_id' => ['nullable', 'exists:hosting_projects,id'],
        ], [
            'domain_name.unique' => 'Domain ini sudah didaftarkan di sistem.',
        ]);

        $domainName = HostingDomain::normalizeName($request->domain_name);

        if (! filter_var($domainName, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return back()->with('error', 'Format domain tidak valid. Contoh yang benar: tokoanda.com');
        }

        $projectId = $this->resolveProjectId($request->input('project_id'));
        if ($projectId === false) {
            return back()->with('error', $this->projectError ?: 'Project tersebut bukan milik Anda.');
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
            'type' => HostingDomain::TYPE_CUSTOM,
            'ssl_status' => 'pending',
            'cf_zone_id' => $createZoneRes->json('result.id'),
            'nameservers' => $createZoneRes->json('result.name_servers', []),
        ]);

        // Zone baru selalu berstatus pending — user harus mengganti nameserver
        // dulu, jadi belum ada routing yang perlu ditulis di sini.
        return redirect()
            ->route('user_hosting.domains.show', $domain->hashid)
            ->with('success', "Domain {$domainName} berhasil ditambahkan. Arahkan nameserver domain Anda ke Cloudflare agar bisa diverifikasi.");
    }

    /**
     * Buat subdomain dari domain root milik user sendiri, mis.
     * blog.tokoanda.com. Tidak butuh zone baru — mewarisi zone & status induk.
     */
    public function storeSubdomain(Request $request)
    {
        $check = $this->requireCustomDomainPlan();
        if ($check !== null) {
            return $check;
        }

        $request->validate([
            'label' => ['required', 'string', 'max:63'],
            'parent_id' => ['required', 'integer', 'exists:hosting_domains,id'],
            'project_id' => ['nullable', 'exists:hosting_projects,id'],
        ]);

        $parent = HostingDomain::where('user_id', Auth::id())
            ->where('type', HostingDomain::TYPE_CUSTOM)
            ->findOrFail($request->parent_id);

        if ($parent->ssl_status !== 'active') {
            return back()->with('error', "Domain {$parent->domain_name} belum aktif di Cloudflare. Selesaikan perubahan nameserver dulu sebelum membuat subdomain.");
        }

        $fqdn = $this->buildSubdomainName($request->label, $parent->domain_name);

        if ($fqdn === false) {
            return back()->with('error', $this->labelError ?: 'Label subdomain tidak valid.');
        }

        $projectId = $this->resolveProjectId($request->input('project_id'));
        if ($projectId === false) {
            return back()->with('error', $this->projectError ?: 'Project tersebut bukan milik Anda.');
        }

        if (! $this->assertHostnameAvailable($fqdn)) {
            return back()->with('error', "Subdomain {$fqdn} sudah dipakai. Pilih label lain.");
        }

        $domain = HostingDomain::create([
            'user_id' => Auth::id(),
            'project_id' => $projectId,
            'domain_name' => $fqdn,
            'type' => HostingDomain::TYPE_SUBDOMAIN,
            'parent_id' => $parent->id,
            'ssl_status' => 'active',
        ]);

        return $this->finishSubdomainCreation($domain, $projectId);
    }

    /** Halaman detail satu domain: instruksi nameserver + status. */
    public function show($hashid)
    {
        $domain = $this->findOwnedDomain($hashid);

        $children = $domain->isCustom()
            ? $domain->children()->with('project')->orderBy('domain_name')->get()
            : collect();

        return view('pages.hosting.user.domains.show', [
            'domain' => $domain,
            'children' => $children,
            'projects' => HostingProject::where('user_id', Auth::id())->orderBy('project_name')->get(['id', 'project_name']),
        ]);
    }

    /**
     * Sambungkan domain yang sudah terverifikasi ke sebuah project.
     * Dipakai dari tab Domains di halaman detail project.
     *
     * Satu project boleh dilayani banyak hostname. Yang dijaga hanya satu
     * hostname tidak boleh dilayani dua project — itu sudah otomatis karena
     * `hosting_domains.domain_name` unik.
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

        // Zona belum aktif = belum bisa dilayani.
        if ($domain->ssl_status !== 'active') {
            return back()->with('error', "Domain {$domain->domain_name} belum terverifikasi. Selesaikan dulu perubahan nameserver, lalu tekan \"Cek Status\".");
        }

        if (! $this->assertProjectHasRoom($project)) {
            return back()->with('error', $this->projectError ?: 'Project ini sudah punya hostname terlalu banyak.');
        }

        $domain->update(['project_id' => $project->id]);

        $extra = $this->syncRouting($domain);

        return back()->with('success', "Domain {$domain->domain_name} sekarang melayani project {$project->project_name}.".$extra);
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

        // Subdomain ikut terhapus — DNS record & routing-nya dibersihkan dulu.
        // Lewati langkah ini akan meninggalkan record CNAME dan file mapping
        // yang masih menunjuk project yang sudah tidak dilayani.
        $redirects = app(DomainRedirectService::class);
        foreach ($domain->children()->get() as $child) {
            $this->clearDns($child);
            $this->clearRouting($child);
            $redirects->removeFiles($child);
            $child->delete();
        }

        if ($domain->isCustom()) {
            // Cuma domain root yang punya zone sendiri.
            if ($domain->cf_zone_id) {
                Http::withToken(config('services.cloudflare.api_token'))
                    ->delete("https://api.cloudflare.com/client/v4/zones/{$domain->cf_zone_id}");
            }
        } else {
            // Subdomain tidak boleh menghapus zone — hanya record-nya.
            $this->clearDns($domain);
        }

        $this->clearRouting($domain);
        // Domain ikut hilang => file .redirects/<host> harus ikut hilang,
        // kalau tidak nginx akan terus mengarahkan hostname yang sudah tidak
        // ada di database.
        app(DomainRedirectService::class)->removeFiles($domain);
        $domain->delete();

        return redirect()->route('user_hosting.domains.index')->with('success', 'Domain berhasil dihapus dari sistem & Cloudflare.');
    }

    /**
     * Arahkan domain ini ke URL lain (ala Vercel).
     *
     * Satu domain hanya boleh punya satu aturan aktif, jadi simpan selalu
     * memakai updateOrCreate di service.
     */
    public function storeRedirect(Request $request, $hashid, DomainRedirectService $redirects)
    {
        $domain = $this->findOwnedDomain($hashid);

        if ($domain->project && $this->isViewerOnly($domain->project)) {
            return back()->with('error', 'Akses ditolak. Anda hanya berperan sebagai Viewer pada project ini.');
        }

        $data = $request->validate([
            'target' => ['required', 'string', 'max:255', 'url', 'not_regex:/\b(ryaze|ryz)\.my\.id\b/i'],
            'status_code' => ['required', 'integer', 'in:301,302,307,308'],
        ], [
            'target.url' => 'URL tujuan harus lengkap dengan http:// atau https://',
            'target.not_regex' => 'Tidak bisa mengarahkan domain ke domain Ryaze sendiri.',
            'status_code.in' => 'Kode redirect harus 301, 302, 307, atau 308.',
        ]);

        $target = rtrim($data['target'], '/');

        // Redirect ke hostname sendiri = loop, server akan 508.
        $own = array_map(fn ($h) => strtolower($h), $domain->hostnames());
        $targetHost = strtolower((string) parse_url($target, PHP_URL_HOST));
        if (in_array($targetHost, $own, true)) {
            return back()->with('error', 'URL tujuan tidak boleh mengarah ke domain ini sendiri.');
        }

        $redirect = $redirects->save($domain, $target, (int) $data['status_code']);

        return back()->with('success', "{$domain->domain_name} sekarang diarahkan ke {$redirect->target} ({$redirect->status_code}).");
    }

    /** Hapus aturan redirect — domain dilayani normal lagi. */
    public function destroyRedirect($hashid, DomainRedirectService $redirects)
    {
        $domain = $this->findOwnedDomain($hashid);

        if ($domain->project && $this->isViewerOnly($domain->project)) {
            return back()->with('error', 'Akses ditolak. Anda hanya berperan sebagai Viewer pada project ini.');
        }

        if (! $domain->redirect()->exists()) {
            return back()->with('error', 'Domain ini tidak punya aturan redirect.');
        }

        $redirects->delete($domain);

        return back()->with('success', "Redirect untuk {$domain->domain_name} dihapus. Domain dilayani normal lagi.");
    }

    /** Cek apakah nameserver sudah tersambung; kalau aktif, siapkan DNS + tunnel. */
    public function checkStatus($hashid)
    {
        $domain = $this->findOwnedDomain($hashid);

        // Subdomain tidak punya zone sendiri — status mengikuti domain induk
        // dan "Cek Status" hanya berarti pastikan ulang DNS record-nya.
        if ($domain->isChild()) {
            $root = $domain->rootDomain();

            if ($root->isCustom() && $root->ssl_status !== 'active') {
                return back()->with('error', "Domain induk {$root->domain_name} belum aktif. Selesaikan perubahan nameserver di halaman domain induk dulu.");
            }

            $domain->update(['ssl_status' => 'active']);

            $notes = $this->syncDns($domain);
            $extra = $domain->project_id ? $this->syncRouting($domain) : '';

            return back()->with('success', 'DNS record subdomain dipastikan ulang.'.($notes ?? '').$extra);
        }

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

            // Kalau sudah disambungkan ke project, langsung layanin.
            $notes = $this->syncDns($domain);
            $extra = $domain->project_id ? $this->syncRouting($domain) : '';

            return back()->with('success', 'Nameserver berhasil tersambung! DNS Record dibuat otomatis.'.($notes ?? '').$extra);
        }

        return back()->with('error', 'Nameserver belum tersambung. biasanya butuh propagasi hingga 24 jam setelah Anda mengubah NS di tempat pembelian domain.');
    }

    /* ---------------------------------------------------------------------
     | Helpers
     |------------------------------------------------------------------ */

    /**
     * Pembatasan paket: custom domain & subdomain hanya Pro & Business.
     * Mengembalikan response redirect kalau tidak boleh, null kalau boleh.
     */
    private function requireCustomDomainPlan()
    {
        $check = Auth::user()->canManageCustomDomain();

        return $check['allowed']
            ? null
            : back()->with('error', $check['message']);
    }

    /**
     * Pastikan project boleh ditulis user dan mengembalikan id-nya.
     * Mengembalikan false bila tidak boleh (pesan ada di $projectError).
     * String kosong / null berarti "tidak memilih project".
     */
    private function resolveProjectId($requestedId)
    {
        $this->projectError = null;

        if (blank($requestedId)) {
            return null;
        }

        $project = HostingProject::find($requestedId);

        if (! $project) {
            $this->projectError = 'Project tidak ditemukan.';

            return false;
        }

        if ($project->user_id !== Auth::id() && ! in_array(Auth::user()->role, ['superadmin', 'admin_hosting'], true)) {
            $this->projectError = 'Project tersebut bukan milik Anda.';

            return false;
        }

        if ($this->isViewerOnly($project)) {
            $this->projectError = 'Akses ditolak. Anda hanya berperan sebagai Viewer pada project ini.';

            return false;
        }

        return $project->id;
    }

    /**
     * Bentuk FQDN subdomain dari input user.
     *
     * Input "blog" dan "blog.tokoanda.com" sama-sama menghasilkan
     * "blog.tokoanda.com". Input bertingkat lebih dalam ditolak supaya user
     * tidak membuat "a.b.tokoanda.com" yang tidak terduga.
     *
     * @param  string  $parentDomain  Domain induk, mis. "tokoanda.com".
     */
    private function buildSubdomainName(string $input, string $parentDomain)
    {
        $this->labelError = null;

        $raw = HostingDomain::normalizeName($input);
        $suffix = '.'.$parentDomain;

        if (str_ends_with($raw, $suffix)) {
            $raw = substr($raw, 0, -strlen($suffix));
        }

        // Sisa setelah membuang domain induk harus benar-benar satu label.
        if (str_contains($raw, '.')) {
            $this->labelError = "Subdomain harus berada satu tingkat di bawah {$parentDomain}. Contoh: blog.{$parentDomain}";

            return false;
        }

        $label = HostingDomain::normalizeLabel($raw);

        if (! HostingDomain::isValidLabel($label)) {
            $this->labelError = 'Label subdomain tidak valid. Gunakan huruf, angka, atau tanda hubung (maksimal 63 karakter, tidak boleh diawali/diakhiri tanda hubung).';

            return false;
        }

        if (str_contains($parentDomain, '/') || str_contains($parentDomain, '..')) {
            $this->labelError = 'Domain induk tidak valid.';

            return false;
        }

        return $label.$suffix;
    }

    /** Cegah satu hostname dipakai dua kali di sistem. */
    private function assertHostnameAvailable(string $fqdn): bool
    {
        return ! HostingDomain::where('domain_name', $fqdn)->exists();
    }

    /** Batas jumlah hostname per project (config hosting.hostnames_per_project). */
    private function assertProjectHasRoom(HostingProject $project): bool
    {
        $limit = config('hosting.hostnames_per_project');

        if (! is_int($limit) || $limit <= 0) {
            return true;
        }

        $used = HostingDomain::where('project_id', $project->id)->count();

        if ($used < $limit) {
            return true;
        }

        $this->projectError = "Project {$project->project_name} sudah punya {$used} hostname (maksimal {$limit}).";

        return false;
    }

    /** Setelah subdomain tersimpan: buat DNS lalu layanin kalau sudah ada project. */
    private function finishSubdomainCreation(HostingDomain $domain, ?int $projectId)
    {
        $notes = $this->syncDns($domain);
        $extra = $projectId ? $this->syncRouting($domain) : '';

        $message = "Subdomain {$domain->domain_name} berhasil dibuat.";

        if ($projectId) {
            $message .= $extra;
        } else {
            $message .= ' Hubungkan ke project agar mulai dilayani.';
        }

        return redirect()
            ->route('user_hosting.domains.show', $domain->hashid)
            ->with('success', $message.($notes ?? ''));
    }

    /**
     * Buat (atau pastikan ulang) DNS record CNAME ke tunnel untuk semua
     * hostname milik domain ini. Mengembalikan pesan error nullable.
     */
    private function syncDns(HostingDomain $domain): ?string
    {
        $zoneId = $domain->zoneId();
        $tunnelUrl = config('services.cloudflare.tunnel_url');

        if (! $zoneId) {
            return ' Peringatan: zona Cloudflare tidak ditemukan, DNS record tidak dibuat.';
        }

        if (! $tunnelUrl) {
            return ' Peringatan: CLOUDFLARE_TUNNEL_URL belum dikonfigurasi, DNS record tidak dibuat.';
        }

        $apiToken = config('services.cloudflare.api_token');
        $failed = [];

        foreach ($domain->hostnames() as $hostname) {
            // Idempoten: record milik Ryaze yang sebelumnya dibuat untuk
            // hostname ini dibuang dulu, lalu dibuat ulang.
            $this->deleteTunnelRecord($zoneId, $hostname, $tunnelUrl);

            $res = Http::withToken($apiToken)->post("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records", [
                'type' => 'CNAME',
                'name' => $hostname,
                'content' => $tunnelUrl,
                'proxied' => true,
            ]);

            if (! $res->successful()) {
                $failed[] = $hostname.' ('.($res->json('errors.0.message') ?? 'error tidak diketahui').')';
            }
        }

        return $failed
            ? ' Peringatan: gagal membuat DNS record untuk '.implode(', ', $failed)
            : null;
    }

    /**
     * Hapus record CNAME milik Ryaze pada hostname tertentu.
     *
     * Sengaja TIDAK menghapus record lain milik user di zona yang sama — kalau
     * user punya MX untuk email di domain root, record itu harus utuh.
     */
    private function deleteTunnelRecord(string $zoneId, string $hostname, string $tunnelUrl): void
    {
        $apiToken = config('services.cloudflare.api_token');

        $res = Http::withToken($apiToken)->get("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records", [
            'name' => $hostname,
            'per_page' => 100,
        ]);

        if (! $res->successful()) {
            return;
        }

        foreach ((array) $res->json('result', []) as $record) {
            $isOurs = ($record['type'] ?? '') === 'CNAME'
                && ($record['content'] ?? '') === $tunnelUrl;

            if (! $isOurs) {
                continue;
            }

            Http::withToken($apiToken)->delete(
                "https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records/{$record['id']}"
            );
        }
    }

    /** Kebalikan syncDns(): hapus record CNAME milik Ryaze milik domain ini. */
    private function clearDns(HostingDomain $domain): void
    {
        $zoneId = $domain->zoneId();
        $tunnelUrl = config('services.cloudflare.tunnel_url');

        if (! $zoneId || ! $tunnelUrl) {
            return;
        }

        foreach ($domain->hostnames() as $hostname) {
            $this->deleteTunnelRecord($zoneId, $hostname, $tunnelUrl);
        }
    }

    /**
     * Tulis mapping nginx (.domains) + daftarkan ingress rule tunnel.
     * Dipanggil setiap kali status jadi aktif atau domain disambungkan ke project.
     */
    private function syncRouting(HostingDomain $domain): string
    {
        // Relasi project bisa basi: pada assign() project_id baru saja
        // berubah, sementara relasi sudah dimuat saat domain dicari. Muat ulang
        // dari DB supaya mapping tidak kelewat dan tidak menunjuk project lama.
        $project = $domain->load('project')->project;

        $folder = $this->projectFolder($project);

        if (! $folder) {
            return ' (Peringatan: project belum punya subdomain, mapping nginx dilewati.)';
        }

        $mapDir = hosting_clients_dir().'/.domains';
        if (! file_exists($mapDir)) {
            mkdir($mapDir, 0755, true);
        }

        foreach ($domain->hostnames() as $hostname) {
            if (file_put_contents("{$mapDir}/{$hostname}", $folder) === false) {
                Log::error("[Domain] Gagal menulis mapping nginx untuk {$hostname}");

                return ' (Peringatan: gagal menulis mapping nginx, periksa izin direktori .domains.)';
            }
        }

        $notes = ' Domain sudah serves project '.$project->project_name.'.';

        try {
            $tunnel = app(CloudflareTunnelService::class);

            if (! $tunnel->isConfigured()) {
                return $notes.' Cloudflare Tunnel belum dikonfigurasi, hostname belum didaftarkan di tunnel.';
            }

            $service = config('services.cloudflare_tunnel.service', 'http://localhost:80');
            $ok = true;

            foreach ($domain->hostnames() as $hostname) {
                if (! $tunnel->registerRoute($hostname, $service)) {
                    $ok = false;
                    Log::warning("[Domain] Gagal register tunnel route {$hostname}");
                }
            }

            return $notes.($ok
                ? ' Hostname terdaftar di Cloudflare Tunnel.'
                : ' Peringatan: hostname gagal didaftarkan di tunnel, coba lagi nanti.');
        } catch (Throwable $e) {
            Log::error("[Domain] Error register tunnel route {$domain->domain_name}: ".$e->getMessage());

            return $notes.' Peringatan: gagal mendaftarkan hostname di tunnel.';
        }
    }

    /** Kebalikan mapping nginx + cabut ingress rule tunnel. */
    private function clearRouting(HostingDomain $domain): void
    {
        $mapDir = hosting_clients_dir().'/.domains';

        foreach ($domain->hostnames() as $hostname) {
            $file = "{$mapDir}/{$hostname}";
            if (file_exists($file)) {
                @unlink($file);
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

    /**
     * Nama folder hosting milik sebuah project, mis. "toko" dari
     * toko.ryaze.my.id. Folder inilah yang dibaca nginx sebagai document root.
     *
     * Catatan: HostingProject tidak punya atribut `subdomain` — nama folder
     * selalu label pertama dari ryaze_domain.
     */
    private function projectFolder(?HostingProject $project): ?string
    {
        $ryazeDomain = (string) ($project?->ryaze_domain ?? '');
        $folder = explode('.', $ryazeDomain)[0];

        if ($folder === '') {
            return null;
        }

        // Folder ini dipakai untuk menyusun path — tolak apa pun yang bukan
        // label DNS sederhana supaya tidak ada path traversal.
        if (! HostingDomain::isValidLabel(strtolower($folder))) {
            Log::warning("[Domain] Nama folder project tidak valid: {$folder}");

            return null;
        }

        return $folder;
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

        return $query->with(['project', 'parent'])->findOrFail($decoded[0]);
    }
}