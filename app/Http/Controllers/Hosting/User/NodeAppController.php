<?php

namespace App\Http\Controllers\Hosting\User;

use App\Jobs\DeployNodeApp;
use App\Models\HostingProject;
use App\Models\NodeApp;
use App\Services\NodeAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Halaman "Aplikasi" — pengalaman ala cPanel "Setup Node.js App":
 * buat aplikasi Node dari project framework 'node', panel yang mengelola
 * build/start/stop/restart melalui agent di container runtime ryaze-node.
 */
class NodeAppController extends \App\Http\Controllers\Controller
{
    public function __construct(protected NodeAppService $service) {}

    public function index()
    {
        $user = Auth::user();

        $apps = NodeApp::query()
            ->whereHas('project', fn ($q) => $q->where('user_id', $user->id))
            ->with('project:id,user_id,ryaze_domain,status')
            ->latest()
            ->get();

        $availableProjects = HostingProject::where('user_id', $user->id)
            ->where('framework', 'node')
            ->whereDoesntHave('nodeApp')
            ->get(['id', 'ryaze_domain', 'status']);

        $plan = $user->getActivePlan() ?? 'free';
        $limit = NodeApp::planLimit($plan);

        return view('pages.hosting.user.apps', [
            'apps' => $apps,
            'availableProjects' => $availableProjects,
            'plan' => $plan,
            'limit' => $limit,
            'runtimeAvailable' => NodeAppService::available(),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!NodeAppService::available()) {
            return back()->with('error', 'Node runtime belum tersedia. Hubungi admin.');
        }

        $validated = $request->validate([
            'project_hashid' => 'required|string',
            'entry' => 'nullable|string|max:255',
            'node_env' => 'required|in:production,development',
            'env_vars_raw' => 'nullable|string|max:5000',
        ]);

        $project = HostingProject::where('user_id', $user->id)
            ->where('framework', 'node')
            ->findByHashidOrFail($validated['project_hashid']);

        if ($project->nodeApp) {
            return back()->with('error', 'Project ini sudah punya aplikasi Node.');
        }

        // Batas jumlah aplikasi per paket
        $plan = $user->getActivePlan() ?? 'free';
        $current = NodeApp::whereHas('project', fn ($q) => $q->where('user_id', $user->id))->count();
        if ($current >= NodeApp::planLimit($plan)) {
            return back()->with('error', "Paket " . ucfirst($plan) . " membatasi " . NodeApp::planLimit($plan) . " aplikasi Node. Upgrade paket untuk menambah.");
        }

        $entry = $this->sanitizeEntry($validated['entry'] ?? null);
        if ($entry === false) {
            return back()->with('error', 'Entry file tidak valid (gunakan path relatif tanpa "..").');
        }

        $envVars = $this->parseEnvVars($validated['env_vars_raw'] ?? null);
        if ($envVars === false) {
            return back()->with('error', 'Format environment variable tidak valid. Gunakan KEY=VALUE per baris.');
        }

        $app = NodeApp::create([
            'hosting_project_id' => $project->id,
            'status' => 'stopped',
            'entry' => $entry,
            'node_env' => $validated['node_env'],
            'env_vars' => $envVars,
        ]);

        // Build + start berjalan di background (npm install bisa lama)
        DeployNodeApp::dispatch($app);

        return redirect()->route('user_hosting.apps.index')
            ->with('success', 'Aplikasi sedang di-build dan di-start di background. Status akan diperbarui otomatis.');
    }

    /**
     * Bikin project BARU + aplikasi Node sekaligus dari halaman Aplikasi
     * (tanpa lewat halaman Deploy Aplikasi). Deploy source code → build
     * (npm install) → start berjalan otomatis di background.
     */
    public function storeProject(Request $request)
    {
        $user = Auth::user();

        if (!NodeAppService::available()) {
            return back()->withInput()->with('error', 'Node runtime belum tersedia. Hubungi admin.');
        }

        // Kuota aplikasi Node per paket — cek SEBELUM bikin project
        $plan = $user->getActivePlan() ?? 'free';
        $current = NodeApp::whereHas('project', fn ($q) => $q->where('user_id', $user->id))->count();
        if ($current >= NodeApp::planLimit($plan)) {
            return back()->withInput()->with('error', "Paket " . ucfirst($plan) . " membatasi " . NodeApp::planLimit($plan) . " aplikasi Node. Upgrade paket untuk menambah.");
        }

        // Validasi konfigurasi aplikasi dulu (jangan sampai project sudah dibikin tapi config invalid)
        $entry = $this->sanitizeEntry($request->input('entry'));
        if ($entry === false) {
            return back()->withInput()->with('error', 'Entry file tidak valid (gunakan path relatif tanpa "..").');
        }

        $envVars = $this->parseEnvVars($request->input('env_vars_raw'));
        if ($envVars === false) {
            return back()->withInput()->with('error', 'Format environment variable tidak valid. Gunakan KEY=VALUE per baris.');
        }

        $request->validate(['node_env' => 'nullable|in:production,development']);

        // Bikin project (framework 'node') — deploy TIDAK di-dispatch di sini,
        // supaya record NodeApp sempat dibuat dulu lalu deploy nyambung ke build+start.
        $result = app(\App\Http\Controllers\Hosting\User\DashboardController::class)
            ->createProjectRecord($request, 'node', dispatchDeploy: false);

        if ($error = $result['error']) {
            return back()->withInput()->with('error', $error);
        }

        $project = $result['project'];

        $app = NodeApp::create([
            'hosting_project_id' => $project->id,
            'status' => 'stopped',
            'entry' => $entry,
            'node_env' => $request->input('node_env', 'production'),
            'env_vars' => $envVars,
        ]);

        // Baru jalankan deploy — AutoDeployProject akan nyambung ke DeployNodeApp
        \App\Jobs\AutoDeployProject::dispatch($project);

        return redirect()->route('user_hosting.apps.index')
            ->with('success', "Aplikasi Node '{$project->ryaze_domain}' dibuat! Deploy source code, build & start berjalan otomatis di background.");
    }

    public function start(string $hashid)
    {
        $app = $this->findOwnedApp($hashid);

        $result = $this->service->start($app);

        return back()->with($result['ok'] ? 'success' : 'error', $result['ok']
            ? 'Aplikasi berhasil di-start di port ' . ($result['data']['port'] ?? '?') . '.'
            : ($result['error'] ?? 'Start gagal.'));
    }

    public function stop(string $hashid)
    {
        $app = $this->findOwnedApp($hashid);

        $result = $this->service->stop($app);

        return back()->with($result['ok'] ? 'success' : 'error', $result['ok']
            ? 'Aplikasi berhasil di-stop.'
            : ($result['error'] ?? 'Stop gagal.'));
    }

    public function restart(string $hashid)
    {
        $app = $this->findOwnedApp($hashid);

        $result = $this->service->restart($app);

        return back()->with($result['ok'] ? 'success' : 'error', $result['ok']
            ? 'Aplikasi berhasil di-restart di port ' . ($result['data']['port'] ?? '?') . '.'
            : ($result['error'] ?? 'Restart gagal.'));
    }

    /**
     * Update konfigurasi aplikasi (entry file, NODE_ENV, env vars).
     * Berlaku setelah restart.
     */
    public function update(Request $request, string $hashid)
    {
        $app = $this->findOwnedApp($hashid);

        $validated = $request->validate([
            'entry' => 'nullable|string|max:255',
            'node_env' => 'required|in:production,development',
            'env_vars_raw' => 'nullable|string|max:5000',
        ]);

        $entry = $this->sanitizeEntry($validated['entry'] ?? null);
        if ($entry === false) {
            return back()->with('error', 'Entry file tidak valid (gunakan path relatif tanpa "..").');
        }

        $envVars = $this->parseEnvVars($validated['env_vars_raw'] ?? null);
        if ($envVars === false) {
            return back()->with('error', 'Format environment variable tidak valid. Gunakan KEY=VALUE per baris.');
        }

        $app->update([
            'entry' => $entry,
            'node_env' => $validated['node_env'],
            'env_vars' => $envVars,
        ]);

        $wasRunning = $app->fresh()->status === 'running';
        if ($wasRunning) {
            $this->service->restart($app);
            return back()->with('success', 'Konfigurasi tersimpan. Aplikasi di-restart untuk menerapkannya.');
        }

        return back()->with('success', 'Konfigurasi tersimpan. Akan berlaku saat aplikasi di-start.');
    }

    public function logs(Request $request, string $hashid)
    {
        $app = $this->findOwnedApp($hashid);

        // Log dibaca langsung dari volume bersama (lebih cepat daripada roundtrip agent)
        $projectDir = $this->service->projectDir($app->project);
        $lines = min(max((int) $request->query('lines', '200'), 50), 1000);

        return response()->json([
            'app_log' => $this->tailFile("{$projectDir}/.app.log", $lines),
            'build_log' => $this->tailFile("{$projectDir}/.build.log", $lines),
            'stored_build_log' => $app->last_build_log,
        ]);
    }

    public function destroy(string $hashid)
    {
        $app = $this->findOwnedApp($hashid);

        $this->service->stop($app);
        $app->delete();

        return back()->with('success', 'Aplikasi dihapus. Project tidak ikut terhapus.');
    }

    /**
     * Re-deploy: build ulang + start (mis. setelah mengganti dependency).
     */
    public function redeploy(string $hashid)
    {
        $app = $this->findOwnedApp($hashid);

        DeployNodeApp::dispatch($app);

        return back()->with('success', 'Re-deploy dimulai di background (build + start).');
    }

    // ── Helper ───────────────────────────────────────────────────────────────

    private function findOwnedApp(string $hashid): NodeApp
    {
        return NodeApp::whereHas('project', fn ($q) => $q->where('user_id', Auth::id()))
            ->findByHashidOrFail($hashid);
    }

    /**
     * Entry file: path relatif, tanpa "..", backslash dinormalkan.
     * Null = biarkan agent auto-detect. Return false = invalid.
     */
    private function sanitizeEntry(?string $entry): string|null|false
    {
        if ($entry === null) {
            return null;
        }

        $entry = str_replace('\\', '/', trim($entry));
        if ($entry === '' || $entry === '/') {
            return null;
        }

        if (str_contains($entry, '..') || str_starts_with($entry, '/') || strlen($entry) > 255) {
            return false;
        }

        return $entry;
    }

    /**
     * Parse textarea "KEY=VALUE" per baris menjadi array.
     * Return false bila format tidak valid.
     */
    private function parseEnvVars(?string $raw): array|null|false
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }

        $vars = [];
        $lines = preg_split('/\r?\n/', trim($raw));
        if (count($lines) > 50) {
            return false;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (!preg_match('/^([A-Za-z_][A-Za-z0-9_]*)=(.*)$/', $line, $m)) {
                return false;
            }

            $vars[$m[1]] = $m[2];
        }

        return $vars ?: null;
    }

    private function tailFile(string $path, int $lines): string
    {
        try {
            $content = file_get_contents($path);

            return implode("\n", array_slice(explode("\n", $content), -$lines));
        } catch (\Throwable $e) {
            return '';
        }
    }
}
