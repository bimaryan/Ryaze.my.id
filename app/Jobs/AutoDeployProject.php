<?php

namespace App\Jobs;

use App\Models\HostingProject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AutoDeployProject implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public HostingProject $project;

    /** Jangan retry — deploy ulang harus manual via redeploy button. */
    public int $tries = 1;

    /** Timeout maksimum job (detik). Build NPM + composer bisa lama. */
    public int $timeout = 1800; // 30 menit untuk Laravel install

    public function __construct(HostingProject $project)
    {
        $this->project = $project;
    }

    public function handle(): void
    {
        $deploy = $this->project->deployments()->latest()->first();

        if (!$deploy) {
            Log::error("[AutoDeploy] Tidak ada deployment record untuk project #{$this->project->id}");
            $this->project->update(['status' => 'error']);
            return;
        }

        $deploy->update(['status' => 'building', 'build_logs' => '']);

        $baseDir = '/www/sites/hosting_clients';
        $subdomain = str_replace('.ryaze.my.id', '', $this->project->ryaze_domain);
        $projectDir = $baseDir . '/' . $subdomain;

        try {
            $this->log($deploy, '> Preparing deployment directory...');
            $this->exec("mkdir -p {$baseDir}", $deploy);

            $isTemplate = ($this->project->source_type === 'template') || (is_string($this->project->repo_source) && str_starts_with($this->project->repo_source, 'template:'));

            $this->log($deploy, "> Debug: source_type = " . ($this->project->source_type ?? 'NULL'));
            $this->log($deploy, "> Debug: repo_source = " . ($this->project->repo_source ?? 'NULL'));

            if ($isTemplate) {
                $this->log($deploy, "\n> ✅ Mode Template aktif!");
                $templateKey = str_replace('template:', '', $this->project->repo_source);
                $markerFile = "{$projectDir}/.ryaze-template";
                $alreadyScaffolded = file_exists($markerFile);

                if ($alreadyScaffolded) {
                    $this->log($deploy, "\n> [TEMPLATE] Source: {$templateKey}");
                    $this->log($deploy, '> Template already scaffolded. Preserving user files...');
                    $this->log($deploy, '> Re-running build step only...');
                } else {
                    $this->log($deploy, "\n> [TEMPLATE] Scaffolding: {$templateKey}");
                    $this->log($deploy, '> Generating starter files directly on server (no git required)...');

                    if (is_dir($projectDir)) {
                        $this->exec("rm -rf {$projectDir}", $deploy);
                    }
                    $this->exec("mkdir -p {$projectDir}", $deploy);

                    $this->scaffoldTemplate($templateKey, $projectDir, $deploy);

                    file_put_contents($markerFile, json_encode([
                        'template' => $templateKey,
                        'scaffolded' => now()->toISOString(),
                        'project_id' => $this->project->id,
                    ]));

                    $this->log($deploy, "> Template files ready in: {$projectDir}");
                }
            } else {
                $this->log($deploy, "\n> Checking repository status...");

                $this->exec("git config --global url.'https://github.com/'.insteadOf 'git@github.com:'", $deploy);
                $this->exec("git config --global url.'https://gitlab.com/'.insteadOf 'git@gitlab.com:'", $deploy);
                $this->exec("git config --global url.'https://bitbucket.org/'.insteadOf 'git@bitbucket.org:'", $deploy);
                $this->exec("git config --global core.askPass ''", $deploy);
                $this->exec("git config --global --add safe.directory '*'", $deploy);

                $repoUrl = $this->project->repo_source;
                if (preg_match('/^git@([^:]+):(.+?)(?:\.git)?$/', $repoUrl, $m)) {
                    $repoUrl = "https://{$m[1]}/{$m[2]}.git";
                    $this->log($deploy, "> [INFO] Converted SSH URL to HTTPS: {$repoUrl}");
                }

                $isRepo = is_dir("{$projectDir}/.git");

                if ($isRepo) {
                    $this->log($deploy, '> Repository found. Pulling latest changes...');
                    $this->exec("chown -R root:root {$projectDir}", $deploy);
                    $this->exec(
                        "cd {$projectDir} && git fetch --all && git reset --hard origin/{$this->project->branch}",
                        $deploy,
                        true
                    );
                } else {
                    if (is_dir($projectDir)) {
                        $this->log($deploy, '> Found stale directory (not a git repo). Cleaning up...');
                        $this->exec("rm -rf {$projectDir}", $deploy);
                    }

                    $this->log($deploy, '> Cloning repository...');
                    $this->exec(
                        "GIT_TERMINAL_PROMPT=0 git clone -b {$this->project->branch} {$repoUrl} {$projectDir}",
                        $deploy,
                        true
                    );
                }
            }

            $this->exec("chown -R www-data:www-data {$projectDir}", $deploy);
            $this->exec("find {$projectDir} -type d -not -path '*/node_modules*' -exec chmod 755 {} \;", $deploy);
            $this->exec("find {$projectDir} -type f -not -path '*/node_modules*' -exec chmod 644 {} \;", $deploy);
            $this->log($deploy, "> Permissions di-set: dir 755, file 644 (mengabaikan node_modules)");

            $framework = strtolower($this->project->framework);
            $this->log($deploy, "\n> Setting up " . strtoupper($framework) . " environment...");

            match ($framework) {
                'react', 'nextjs', 'vue', 'node' => $this->setupNodeFramework($deploy, $projectDir, $framework),
                'laravel' => $this->setupLaravel($deploy, $projectDir),
                'python' => $this->setupPython($deploy, $projectDir),
                'html', 'php' => $this->log($deploy, "> Native/Static project ({$framework}). No build step required."),
                default => $this->log($deploy, "> [WARNING] Framework '{$framework}' tidak dikenali. Melewati build step."),
            };

            $this->log($deploy, "\n> Applying final permissions to all generated files...");
            $this->exec("chown -R www-data:www-data {$projectDir} 2>/dev/null || true", $deploy);
            $this->exec("find {$projectDir} -type d -exec chmod 755 {} \; 2>/dev/null || true", $deploy);
            $this->exec("find {$projectDir} -type f -not -path '*/venv/bin/*' -not -path '*/node_modules/.bin/*' -exec chmod 644 {} \; 2>/dev/null || true", $deploy);

            if ($framework === 'laravel') {
                $this->exec("chmod -R 777 {$projectDir}/storage {$projectDir}/bootstrap/cache 2>/dev/null || true", $deploy);
                $this->log($deploy, "> Laravel storage & bootstrap/cache permissions di-set ke 777");
            }

            $this->log($deploy, "\n> Configuring Cloudflare DNS for {$this->project->ryaze_domain}...");
            if (!$this->createCloudflareDNS($deploy)) {
                throw new \RuntimeException('Gagal mengkonfigurasi Cloudflare DNS. Periksa API Token atau pengaturan Zone ID.');
            }

            $this->log($deploy, "\n> [SUCCESS] Deployment finished!\n> Live at: https://{$this->project->ryaze_domain}");

            $deploy->update([
                'status' => 'ready',
                'deployed_at' => now(),
            ]);
            $this->project->update(['status' => 'active']);
        } catch (\Throwable $e) {
            $message = $e->getMessage();
            $this->log($deploy, "\n> [FATAL ERROR] {$message}");
            Log::error("[AutoDeploy] Project #{$this->project->id} failed: {$message}", [
                'exception' => $e,
            ]);
            $this->markAsFailed($deploy);
        }
    }

    private function setupNodeFramework($deploy, string $projectDir, string $framework): void
    {
        $isLaravelInertia = file_exists("{$projectDir}/artisan")
            && file_exists("{$projectDir}/composer.json")
            && file_exists("{$projectDir}/package.json");

        if ($isLaravelInertia) {
            $this->log($deploy, '> Laravel+Inertia detected. Running composer install first...');
            $this->runComposerInstall($deploy, $projectDir);
            $this->setupLaravelEnv($deploy, $projectDir);
        }

        $this->log($deploy, '> Installing NPM dependencies...');
        $this->exec(
            "cd {$projectDir} && npm install --legacy-peer-deps 2>&1 || true",
            $deploy,
            false
        );

        // Berikan executable permission ke node_modules/.bin
        if (is_dir("{$projectDir}/node_modules/.bin")) {
            $this->exec("chmod -R +x {$projectDir}/node_modules/.bin 2>&1 || true", $deploy);
            $this->exec("chmod +x {$projectDir}/node_modules/.bin/* 2>/dev/null || true", $deploy);
            $this->exec("find {$projectDir}/node_modules -path '*/bin/*' -type f -exec chmod +x {} \; 2>/dev/null || true", $deploy);
            $this->log($deploy, "> Executable permission di-set untuk node_modules/.bin dan bin files");
        }

        if (in_array($framework, ['react', 'nextjs', 'vue'])) {
            $this->log($deploy, '> Running build script...');
            $this->exec("rm -rf {$projectDir}/dist {$projectDir}/build {$projectDir}/out 2>/dev/null || true", $deploy);
            $this->exec(
                "cd {$projectDir} && npm run build 2>&1 || true",
                $deploy,
                false
            );

            if ($isLaravelInertia) {
                $this->log($deploy, '> Laravel+Inertia: Vite output is at public/build. No file move needed.');
                $this->runLaravelPostSetup($deploy, $projectDir);
            } else {
                $this->log($deploy, '> Organizing build output...');
                $this->moveBuiltOutput($deploy, $projectDir);
            }
        }
    }

    private function setupLaravel($deploy, string $projectDir): void
    {
        $this->log($deploy, '> Laravel setup complete.');
    }

    private function setupLaravelEnv($deploy, string $projectDir): void
    {
        $envPath = "{$projectDir}/.env";
        $envExamplePath = "{$projectDir}/.env.example";

        if (!file_exists($envPath)) {
            if (file_exists($envExamplePath)) {
                $this->log($deploy, '> Creating .env from .env.example...');
                $this->exec("cp {$envExamplePath} {$envPath} && chmod 666 {$envPath}", $deploy, true);
            }
        }

        $this->exec("chown www-data:www-data {$envPath} && chmod 666 {$envPath} 2>/dev/null || true", $deploy);
    }

    private function runLaravelPostSetup($deploy, string $projectDir): void
    {
        $this->exec("chmod -R 777 {$projectDir}/storage {$projectDir}/bootstrap/cache 2>/dev/null || true", $deploy);
        $this->exec("chown -R www-data:www-data {$projectDir}/storage {$projectDir}/bootstrap/cache 2>/dev/null || true", $deploy);
    }

    private function runComposerInstall($deploy, string $projectDir): void
    {
        $composer = null;
        foreach (['/usr/local/bin/composer', '/usr/bin/composer'] as $path) {
            if (file_exists($path)) {
                $composer = $path;
                break;
            }
        }
        if (!$composer) {
            $composer = trim(shell_exec('which composer 2>/dev/null') ?? '');
        }
        if (!$composer) {
            $this->log($deploy, '> [WARNING] composer not found, skipping install.');
            return;
        }

        $this->log($deploy, "> Using composer: {$composer}");
        $this->exec(
            "cd {$projectDir} && {$composer} install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs 2>&1 || true",
            $deploy,
            false
        );
    }

    private function setupPython($deploy, string $projectDir): void
    {
        $this->log($deploy, '> Setting up Python virtual environment...');
        $this->exec("cd {$projectDir} && python3 -m venv venv", $deploy);
        $this->exec("chmod -R +x {$projectDir}/venv/bin 2>/dev/null || true", $deploy);
        
        if (file_exists("{$projectDir}/requirements.txt")) {
            $this->log($deploy, '> Mengoptimalkan requirements.txt');
            $reqFile = "{$projectDir}/requirements.txt";
            $reqs = file_get_contents($reqFile);
            
            // Hapus semua versi strict/spesifik (==, >=, <=) pada SEMUA library agar selalu mengunduh versi global yang kompatibel
            $reqs = preg_replace('/[=><~]+.*$/m', '', $reqs);
            
            // Hapus torchvision sesuai request user
            $reqs = preg_replace('/^torchvision.*$/m', '', $reqs);
            
            // Bersihkan baris kosong
            $reqs = preg_replace('/^\s*[\r\n]+/m', '', $reqs);
            
            file_put_contents($reqFile, trim($reqs));

            $this->log($deploy, '> Installing Python dependencies from requirements.txt...');
            $this->exec("cd {$projectDir} && venv/bin/python -m pip install --no-cache-dir -r requirements.txt 2>&1 || true", $deploy);
        }

        // Allocate a dynamic port for OpenResty reverse proxy (using dev_port)
        $port = null;
        for ($p = 8000; $p <= 9000; $p++) {
            $connection = @fsockopen('127.0.0.1', $p);
            if (!is_resource($connection)) {
                $port = $p;
                break;
            }
            if (is_resource($connection)) fclose($connection);
        }

        if (!$port) {
            $this->log($deploy, '> [ERROR] Tidak ada port yang tersedia untuk Python Server.');
            return;
        }

        // Ensure binaries installed via pip are executable
        $this->exec("chmod -R +x {$projectDir}/venv/bin 2>/dev/null || true", $deploy);

        // Kill existing process if any
        if ($this->project->dev_pid) {
            exec("kill -9 {$this->project->dev_pid} 2>/dev/null || true");
        }

        $this->log($deploy, "> Starting Python Server on port {$port}...");
        
        // Coba cari file entrypoint
        $entrypoint = 'app.py';
        if (file_exists("{$projectDir}/main.py")) $entrypoint = 'main.py';
        elseif (file_exists("{$projectDir}/server.py")) $entrypoint = 'server.py';
        elseif (file_exists("{$projectDir}/wsgi.py")) $entrypoint = 'wsgi.py';

        // Gunicorn disarankan untuk Flask/Django
        $hasGunicorn = file_exists("{$projectDir}/venv/bin/gunicorn");
        
        if ($hasGunicorn) {
            $module = str_replace('.py', '', $entrypoint);
            $command = "cd {$projectDir} && PORT={$port} nohup venv/bin/gunicorn {$module}:app -b 127.0.0.1:{$port} --workers 2 > {$projectDir}/.dev-server.log 2>&1 & echo $!";
        } else {
            // Fallback native python run (Pastikan app mendengarkan PORT dari environment)
            $command = "cd {$projectDir} && PORT={$port} FLASK_RUN_PORT={$port} nohup venv/bin/python {$entrypoint} > {$projectDir}/.dev-server.log 2>&1 & echo $!";
        }
        
        $pid = trim(shell_exec($command));

        if ($pid) {
            $this->log($deploy, "> Python server running on PID: {$pid} (Port: {$port})");
            
            $this->log($deploy, "> Menyiapkan PHP Reverse Proxy untuk OpenResty...");
            $proxyScript = <<<PHP
<?php
/**
 * Ryaze - Auto-generated PHP Reverse Proxy
 * Proxies traffic from OpenResty to the Python daemon.
 */
\$port = {$port};
\$host = '127.0.0.1';
\$path = \$_SERVER['REQUEST_URI'];
\$method = \$_SERVER['REQUEST_METHOD'];
\$headers = getallheaders();

\$url = "http://{\$host}:{\$port}{\$path}";

\$ch = curl_init(\$url);
curl_setopt(\$ch, CURLOPT_CUSTOMREQUEST, \$method);
curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt(\$ch, CURLOPT_HEADER, true);
curl_setopt(\$ch, CURLOPT_FOLLOWLOCATION, false);

\$reqHeaders = [];
foreach (\$headers as \$k => \$v) {
    if (strtolower(\$k) === 'host') continue;
    \$reqHeaders[] = "\$k: \$v";
}
curl_setopt(\$ch, CURLOPT_HTTPHEADER, \$reqHeaders);

if (\$method === 'POST' || \$method === 'PUT' || \$method === 'PATCH') {
    \$body = file_get_contents('php://input');
    if (empty(\$body) && !empty(\$_POST)) {
        // Fallback for multipart/form-data if php://input is empty
        \$postFields = \$_POST;
        if (!empty(\$_FILES)) {
            foreach (\$_FILES as \$key => \$file) {
                if (is_array(\$file['tmp_name'])) {
                    foreach (\$file['tmp_name'] as \$i => \$tmpName) {
                        if (\$file['error'][\$i] === UPLOAD_ERR_OK) {
                            \$postFields["{\$key}[\$i]"] = new \CURLFile(\$tmpName, \$file['type'][\$i], \$file['name'][\$i]);
                        }
                    }
                } else {
                    if (\$file['error'] === UPLOAD_ERR_OK) {
                        \$postFields[\$key] = new \CURLFile(\$file['tmp_name'], \$file['type'], \$file['name']);
                    }
                }
            }
        }
        curl_setopt(\$ch, CURLOPT_POSTFIELDS, \$postFields);
    } else {
        curl_setopt(\$ch, CURLOPT_POSTFIELDS, \$body);
    }
}

\$response = curl_exec(\$ch);
if (curl_errno(\$ch)) {
    http_response_code(502);
    echo "502 Bad Gateway - Python Application Server is down, crashed, or still starting up.";
    exit;
}

\$headerSize = curl_getinfo(\$ch, CURLINFO_HEADER_SIZE);
\$resHeaders = substr(\$response, 0, \$headerSize);
\$resBody = substr(\$response, \$headerSize);
\$httpCode = curl_getinfo(\$ch, CURLINFO_HTTP_CODE);

http_response_code(\$httpCode);

\$lines = explode("\\n", \$resHeaders);
foreach (\$lines as \$line) {
    \$line = trim(\$line);
    if (empty(\$line)) continue;
    if (strpos(strtolower(\$line), 'transfer-encoding:') === 0) continue;
    header(\$line, false);
}

echo \$resBody;
curl_close(\$ch);
PHP;
            file_put_contents("{$projectDir}/index.php", $proxyScript);
            file_put_contents("{$projectDir}/.port", $port);
            $this->exec("chown www-data:www-data {$projectDir}/.port", $deploy);
            
            $this->project->update([
                'dev_mode' => true,
                'dev_port' => $port,
                'dev_pid' => $pid
            ]);
        } else {
            $this->log($deploy, '> [ERROR] Gagal menjalankan server Python.');
        }

        $this->log($deploy, '> Python setup complete.');
    }

    private function moveBuiltOutput($deploy, string $projectDir): void
    {
        $outputDirName = null;
        foreach (['dist', 'build', 'out'] as $candidate) {
            if (is_dir("{$projectDir}/{$candidate}")) {
                $outputDirName = $candidate;
                break;
            }
        }

        if (!$outputDirName) {
            $this->log($deploy, '> [WARNING] Build output not found, skipping move.');
            return;
        }

        $outputDir = "{$projectDir}/{$outputDirName}";
        $this->log($deploy, "> Output folder found: {$outputDir}");

        // Hapus file lama di root terlebih dahulu sebelum memindahkan yang baru
        $this->log($deploy, "> Menghapus file build lama di root...");
        $this->exec("cd {$projectDir} && rm -f index.html 2>/dev/null || true", $deploy);
        $this->exec("cd {$projectDir} && rm -rf assets 2>/dev/null || true", $deploy);
        $this->exec("cd {$projectDir} && rm -f *.ico 2>/dev/null || true", $deploy);
        $this->exec("cd {$projectDir} && rm -f manifest.json 2>/dev/null || true", $deploy);

        // Copy file baru ke root
        $this->log($deploy, "> Memindahkan file build baru ke root...");
        $this->exec("cp -a {$outputDir}/. {$projectDir}/ 2>&1", $deploy, false);

        // Hapus direktori output
        $this->exec("rm -rf {$outputDir} 2>/dev/null || true", $deploy);

        if (!file_exists("{$projectDir}/index.html")) {
            $this->log($deploy, '> [WARNING] index.html not found after move.');
        } else {
            $this->log($deploy, '> Build output berhasil dipindahkan dan di-update!');
        }
    }

    private function createCloudflareDNS($deploy): bool
    {
        $domainName = $this->project->ryaze_domain;
        $zoneId = config('services.cloudflare.zone_id', env('CLOUDFLARE_ZONE_ID'));
        $apiToken = config('services.cloudflare.api_token', env('CLOUDFLARE_API_TOKEN'));
        $tunnelUrl = preg_replace('#^https?://#', '', rtrim(
            config('services.cloudflare.tunnel_url', env('CLOUDFLARE_TUNNEL_URL')),
            '/'
        ));

        if (!$zoneId || !$apiToken || !$tunnelUrl) {
            $this->log($deploy, '> [WARNING] Cloudflare credentials incomplete, skipping DNS setup.');
            return true;
        }

        $existing = Http::withToken($apiToken)
            ->get("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records", [
                'type' => 'CNAME',
                'name' => $domainName,
            ]);

        if ($existing->successful() && count($existing->json('result', [])) > 0) {
            $this->log($deploy, '> DNS record already exists. Skipping.');
            return true;
        }

        $response = Http::withToken($apiToken)
            ->post("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records", [
                'type' => 'CNAME',
                'name' => $domainName,
                'content' => $tunnelUrl,
                'ttl' => 1,
                'proxied' => true,
            ]);

        if ($response->successful()) {
            $this->log($deploy, '> DNS record created successfully!');
            return true;
        }

        $errorMessage = $response->json('errors.0.message', 'Unknown Cloudflare API Error');
        $this->log($deploy, "> [WARNING] Cloudflare DNS: {$errorMessage}");

        return true;
    }

    private function exec(string $command, $deploy, bool $throwOnError = false): string
    {
        $unsetEnv = 'unset APP_NAME APP_ENV APP_KEY APP_DEBUG APP_URL LOG_CHANNEL DB_CONNECTION DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD BROADCAST_DRIVER CACHE_DRIVER QUEUE_CONNECTION SESSION_DRIVER SESSION_LIFETIME REDIS_HOST REDIS_PASSWORD REDIS_PORT; ';

        $fullCommand = $unsetEnv . "({$command}) 2>&1; echo \"__EXIT_CODE__:$?\"";

        $raw = shell_exec($fullCommand) ?? '';

        $exitCode = 0;
        $output = $raw;

        if (preg_match('/\n?__EXIT_CODE__:(\d+)\s*$/', $raw, $matches)) {
            $exitCode = (int)$matches[1];
            $output = trim(substr($raw, 0, strrpos($raw, "\n__EXIT_CODE__:{$exitCode}")));
            if ($output === false) {
                $output = trim(str_replace($matches[0], '', $raw));
            }
        }

        $output = trim($output);

        if ($output !== '') {
            $this->log($deploy, $output);
        }

        if ($throwOnError && $exitCode !== 0) {
            $lines = array_filter(array_map('trim', explode("\n", $output)));
            $summary = implode(' | ', array_slice(array_values($lines), -3));
            throw new \RuntimeException("Command exited with code {$exitCode}: {$summary}");
        }

        return $output;
    }

    private function log($deploy, string $text): void
    {
        $deploy->refresh();
        $deploy->update([
            'build_logs' => $deploy->build_logs . "\n" . $text,
        ]);
    }

    private function markAsFailed($deploy): void
    {
        $deploy->update(['status' => 'failed']);
        $this->project->update(['status' => 'error']);
    }

    private function scaffoldTemplate(string $key, string $dir, $deploy): void
    {
        $projectName = $this->project->project_name;
        $domain = $this->project->ryaze_domain;

        match ($key) {
            'html_landing' => $this->scaffoldHtml($dir, $projectName, $domain),
            'php_basic' => $this->scaffoldPhp($dir, $projectName),
            'laravel_starter' => $this->scaffoldLaravel($dir, $projectName, $deploy),
            'laravel_starter_10' => $this->scaffoldLaravel($dir, $projectName, $deploy, '10'),
            'laravel_starter_11' => $this->scaffoldLaravel($dir, $projectName, $deploy, '11'),
            'laravel_starter_12' => $this->scaffoldLaravel($dir, $projectName, $deploy, '12'),
            'laravel_starter_13' => $this->scaffoldLaravel($dir, $projectName, $deploy, '13'),
            'react_starter' => $this->scaffoldReact($dir, $projectName),
            'nextjs_starter' => $this->scaffoldNextjs($dir, $projectName),
            'node_express' => $this->scaffoldNode($dir, $projectName),
            default => throw new \RuntimeException("Unknown template key: {$key}"),
        };
    }

    private function scaffoldHtml(string $dir, string $name, string $domain): void
    {
        file_put_contents("{$dir}/index.html", <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$name}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: white; border-radius: 20px; padding: 60px 50px; text-align: center; max-width: 500px; box-shadow: 0 25px 50px rgba(0,0,0,0.15); position: relative; }
        .badge { background: linear-gradient(135deg, #667eea, #764ba2); color: white; font-size: 12px; font-weight: 700; padding: 6px 16px; border-radius: 50px; display: inline-block; margin-bottom: 24px; letter-spacing: 1px; text-transform: uppercase; }
        h1 { font-size: 2.5rem; font-weight: 800; color: #1a1a2e; margin-bottom: 16px; }
        p { color: #6b7280; line-height: 1.8; margin-bottom: 32px; }
        .btn { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 14px 36px; border-radius: 12px; text-decoration: none; font-weight: 700; display: inline-block; transition: transform 0.2s, box-shadow 0.2s; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(102,126,234,0.4); }
        .domain { margin-top: 24px; font-size: 12px; color: #9ca3af; }
        .watermark { position: fixed; bottom: 20px; right: 20px; background: rgba(255,255,255,0.9); padding: 12px 20px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); font-size: 13px; }
        .watermark a { color: #667eea; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <span class="badge">🚀 Ryaze Hosting</span>
        <h1>{$name}</h1>
        <p>Website Anda sudah live! Edit file <code>index.html</code> ini lewat <strong>File Manager</strong> untuk mulai kustomisasi tampilan.</p>
        <a href="#" class="btn">Mulai Edit →</a>
        <p class="domain">🌐 {$domain}</p>
    </div>
    <div class="watermark">
        Power by <a href="https://ryaze.my.id" target="_blank">Ryaze.my.id</a> | Email: <a href="mailto:bimaryan046@gmail.com">bimaryan046@gmail.com</a>
    </div>
</body>
</html>
HTML
        );

        file_put_contents("{$dir}/style.css", "/* Tambahkan style kustom di sini */\n");
        file_put_contents("{$dir}/script.js", "/* Tambahkan script kustom di sini */\nconsole.log('Hello from {$name}!');\n");
        file_put_contents("{$dir}/.htaccess", "Options -Indexes\nRewriteEngine On\n");
    }

    private function scaffoldPhp(string $dir, string $name): void
    {
        @mkdir("{$dir}/app", 0755, true);
        @mkdir("{$dir}/public", 0755, true);
        @mkdir("{$dir}/views", 0755, true);

        file_put_contents("{$dir}/index.php", <<<'PHP'
<?php
header('Location: public/');
exit;
PHP
        );

        file_put_contents("{$dir}/app/router.php", <<<'PHP'
<?php
$routes = [
    '/' => fn() => view('home'),
];

$handler = $routes[$path] ?? fn() => view('404');
echo $handler();

function view(string $name): string {
    $file = __DIR__ . "/../views/{$name}.php";
    if (!file_exists($file)) return '<h1>404 Not Found</h1>';
    ob_start();
    include $file;
    return ob_get_clean();
}
PHP
        );

        file_put_contents("{$dir}/views/home.php", <<<PHP
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{$name}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; max-width: 700px; margin: 60px auto; padding: 20px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
        .card { background: white; border-radius: 20px; padding: 60px 50px; box-shadow: 0 25px 50px rgba(0,0,0,0.15); text-align: center; position: relative; }
        h1 { color: #1a1a2e; margin-bottom: 16px; font-size: 2.5rem; }
        p { color: #6b7280; line-height: 1.8; margin-bottom: 24px; }
        .watermark { position: fixed; bottom: 20px; right: 20px; background: rgba(255,255,255,0.9); padding: 12px 20px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); font-size: 13px; }
        .watermark a { color: #667eea; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🐘 {$name}</h1>
        <p>PHP Native app Anda sudah berjalan! Edit <code>views/home.php</code> untuk memulai.</p>
        <p><small>Struktur: MVC sederhana + Router dasar</small></p>
    </div>
    <div class="watermark">
        Power by <a href="https://ryaze.my.id" target="_blank">Ryaze.my.id</a> | Email: <a href="mailto:bimaryan046@gmail.com">bimaryan046@gmail.com</a>
    </div>
</body>
</html>
PHP
        );

        file_put_contents("{$dir}/views/404.php", "<h1>404 — Halaman tidak ditemukan</h1>");
        // .htaccess di root untuk redirect ke public/
        file_put_contents("{$dir}/.htaccess", <<<'HTACCESS'
Options -Indexes
RewriteEngine On
RewriteCond %{REQUEST_URI} !^/public/
RewriteRule ^(.*)$ public/$1 [L]
HTACCESS
        );
        // .htaccess di public/ untuk routing
        file_put_contents("{$dir}/public/.htaccess", <<<'HTACCESS'
Options -Indexes
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
HTACCESS
        );
        // Buat index.php ke public
        file_put_contents("{$dir}/public/index.php", <<<'PHP'
<?php
// Router sederhana
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
require_once __DIR__ . '/../app/router.php';
PHP
        );
    }

    private function scaffoldLaravel(string $dir, string $name, $deploy, string $version = '11'): void
    {
        $this->log($deploy, "🚀 Memasang Laravel v{$version} resmi...");

        // Cari composer path
        $composer = null;
        foreach (['/usr/local/bin/composer', '/usr/bin/composer'] as $path) {
            if (file_exists($path)) {
                $composer = $path;
                break;
            }
        }
        if (!$composer) {
            $composer = trim(shell_exec('which composer 2>/dev/null') ?? '');
        }
        if (!$composer) {
            throw new \RuntimeException('composer binary tidak ditemukan di server.');
        }

        $parentDir = dirname($dir);
        $baseName = basename($dir);
        $tmpDir = "{$parentDir}/.tmp_{$baseName}";

        // Hapus direktori project dan temp jika ada
        $this->exec("rm -rf {$dir} {$tmpDir} 2>/dev/null || true", $deploy);
        $this->exec("mkdir -p {$parentDir}", $deploy);

        // Tentukan versi Laravel
        $versionConstraint = match($version) {
            '10' => '10.*',
            '11' => '11.*',
            '12' => '12.*',
            '13' => '13.*',
            default => '11.*',
        };

        $this->log($deploy, "> Mengunduh Laravel v{$version} via composer...");
        $this->exec(
            "cd {$parentDir} && {$composer} create-project laravel/laravel .tmp_{$baseName} \"{$versionConstraint}\" --no-interaction --prefer-dist --no-progress 2>&1",
            $deploy,
            true
        );

        // Pindahkan dari temp ke direktori project
        $this->log($deploy, "> Memindahkan file ke direktori project...");
        $this->exec("mv {$tmpDir} {$dir}", $deploy);

        // Set permissions yang benar
        $this->log($deploy, "> Mengatur permissions...");
        $this->exec("cd {$dir} && chmod -R 775 storage bootstrap/cache 2>/dev/null || true", $deploy);
        $this->exec("cd {$dir} && chmod -R 777 storage bootstrap/cache 2>/dev/null || true", $deploy);

        // Generate APP_KEY
        $this->log($deploy, "> Generate APP_KEY...");
        $this->exec("cd {$dir} && php artisan key:generate --no-interaction 2>&1 || true", $deploy, false);

        // Tambahkan .htaccess di root untuk redirect ke public
        file_put_contents("{$dir}/.htaccess", <<<'PHP'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
PHP
        );

        $this->log($deploy, "✅ Laravel v{$version} resmi berhasil dipasang!");
    }

    private function scaffoldReact(string $dir, string $name): void
    {
        $safeName = preg_replace('/[^a-z0-9-]/', '-', strtolower($name));
        @mkdir($dir, 0755, true);
        @mkdir("{$dir}/src", 0755, true);

        file_put_contents("{$dir}/package.json", json_encode([
            'name' => $safeName,
            'version' => '1.0.0',
            'private' => true,
            'scripts' => ['dev' => 'vite', 'build' => 'vite build', 'preview' => 'vite preview'],
            'dependencies' => ['react' => '^18.3.1', 'react-dom' => '^18.3.1', 'react-router-dom' => '^6.26.0'],
            'devDependencies' => ['@vitejs/plugin-react' => '^4.3.1', 'vite' => '^5.4.2', 'tailwindcss' => '^3.4.10', 'postcss' => '^8.4.41', 'autoprefixer' => '^10.4.20'],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        file_put_contents("{$dir}/vite.config.js", <<<'JS'
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
export default defineConfig({ plugins: [react()], build: { outDir: 'dist' } })
JS
        );

        file_put_contents("{$dir}/tailwind.config.js", <<<'JS'
/** @type {import('tailwindcss').Config} */
export default { content: ["./index.html", "./src/**/*.{js,ts,jsx,tsx}"], theme: { extend: {} }, plugins: [] }
JS
        );

        file_put_contents("{$dir}/postcss.config.js", <<<'JS'
export default { plugins: { tailwindcss: {}, autoprefixer: {} } }
JS
        );

        file_put_contents("{$dir}/index.html", <<<HTML
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>{$name}</title></head>
<body><div id="root"></div><script type="module" src="/src/main.jsx"></script></body>
</html>
HTML
        );

        file_put_contents("{$dir}/src/main.jsx", <<<'JSX'
import React from 'react'
import ReactDOM from 'react-dom/client'
import { BrowserRouter } from 'react-router-dom'
import App from './App'
import './index.css'
ReactDOM.createRoot(document.getElementById('root')).render(<React.StrictMode><BrowserRouter><App /></BrowserRouter></React.StrictMode>)
JSX
        );

        file_put_contents("{$dir}/src/App.jsx", <<<PHP
import React from 'react'
import { Routes, Route, Link } from 'react-router-dom'

function Home() {
    return (
        <div className="min-h-screen bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center p-4">
            <div className="bg-white p-10 rounded-2xl shadow-2xl text-center max-w-md relative">
                <h1 className="text-4xl font-bold text-slate-800 mb-4">⚛️ {$name}</h1>
                <p className="text-slate-600 mb-8">React + Vite + TailwindCSS + React Router siap!</p>
                <Link to="/about" className="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition">Tentang</Link>
            </div>
            <div className="fixed bottom-5 right-5 bg-white/90 backdrop-blur-sm p-3 rounded-xl shadow-lg text-sm">
                Power by <a href="https://ryaze.my.id" target="_blank" className="text-purple-600 font-semibold hover:underline">Ryaze.my.id</a> | Email: <a href="mailto:bimaryan046@gmail.com" className="text-purple-600 font-semibold hover:underline">bimaryan046@gmail.com</a>
            </div>
        </div>
    )
}

function About() {
    return (
        <div className="min-h-screen bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center p-4">
            <div className="bg-white p-10 rounded-2xl shadow-2xl text-center max-w-md relative">
                <h1 className="text-4xl font-bold text-slate-800 mb-4">Tentang</h1>
                <p className="text-slate-600 mb-8">Ini adalah halaman tentang.</p>
                <Link to="/" className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">Kembali</Link>
            </div>
            <div className="fixed bottom-5 right-5 bg-white/90 backdrop-blur-sm p-3 rounded-xl shadow-lg text-sm">
                Power by <a href="https://ryaze.my.id" target="_blank" className="text-blue-600 font-semibold hover:underline">Ryaze.my.id</a> | Email: <a href="mailto:bimaryan046@gmail.com" className="text-blue-600 font-semibold hover:underline">bimaryan046@gmail.com</a>
            </div>
        </div>
    )
}

export default function App() {
    return <Routes><Route path="/" element={<Home />} /><Route path="/about" element={<About />} /></Routes>
}
PHP
        );

        file_put_contents("{$dir}/src/index.css", <<<'CSS'
@tailwind base; @tailwind components; @tailwind utilities;
body { margin: 0; font-family: 'Segoe UI', sans-serif; }
CSS
        );

        file_put_contents("{$dir}/.gitignore", "node_modules\ndist\n.env\n");
        file_put_contents("{$dir}/README.md", <<<MD
# {$name} - React + Vite

## Cara Update Tampilan:
1. Edit file di folder <code>src/</code> (misalnya <code>src/App.jsx</code>)
2. Jalankan <code>npm run build</code> di terminal
3. Tampilan akan otomatis ter-update!

## Struktur Folder:
- <code>src/main.jsx</code> - Entry point React
- <code>src/App.jsx</code> - Komponen utama (edit ini untuk ubah tampilan)
- <code>src/index.css</code> - File CSS (include Tailwind)
- <code>index.html</code> - File HTML utama
- <code>vite.config.js</code> - Konfigurasi Vite
- <code>tailwind.config.js</code> - Konfigurasi Tailwind

## Power by Ryaze.my.id
Email: bimaryan046@gmail.com
MD
        );
    }

    private function scaffoldNextjs(string $dir, string $name): void
    {
        $safeName = preg_replace('/[^a-z0-9-]/', '-', strtolower($name));
        @mkdir($dir, 0755, true);
        @mkdir("{$dir}/app", 0755, true);

        file_put_contents("{$dir}/package.json", json_encode([
            'name' => $safeName,
            'version' => '1.0.0',
            'private' => true,
            'scripts' => ['dev' => 'next dev', 'build' => 'next build', 'start' => 'next start'],
            'dependencies' => ['next' => '^14.2.5', 'react' => '^18.3.1', 'react-dom' => '^18.3.1'],
            'devDependencies' => ['tailwindcss' => '^3.4.10', 'postcss' => '^8.4.41', 'autoprefixer' => '^10.4.20'],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        file_put_contents("{$dir}/next.config.js", <<<'JS'
/** @type {import('next').NextConfig} */
const nextConfig = { output: 'export' }
module.exports = nextConfig
JS
        );

        file_put_contents("{$dir}/tailwind.config.js", <<<'JS'
/** @type {import('tailwindcss').Config} */
module.exports = { content: ["./app/**/*.{js,ts,jsx,tsx}"], theme: { extend: {} }, plugins: [] }
JS
        );

        file_put_contents("{$dir}/postcss.config.js", <<<'JS'
module.exports = { plugins: { tailwindcss: {}, autoprefixer: {} } }
JS
        );

        file_put_contents("{$dir}/app/page.js", <<<PHP
export default function Home() {
    return (
        <main className="min-h-screen bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center p-4">
            <div className="bg-white p-10 rounded-2xl shadow-2xl text-center max-w-md relative">
                <h1 className="text-4xl font-bold text-slate-800 mb-4">▲ {$name}</h1>
                <p className="text-slate-600 mb-8">Next.js + App Router + TailwindCSS siap!</p>
            </div>
            <div className="fixed bottom-5 right-5 bg-white/90 backdrop-blur-sm p-3 rounded-xl shadow-lg text-sm">
                Power by <a href="https://ryaze.my.id" target="_blank" className="text-cyan-600 font-semibold hover:underline">Ryaze.my.id</a> | Email: <a href="mailto:bimaryan046@gmail.com" className="text-cyan-600 font-semibold hover:underline">bimaryan046@gmail.com</a>
            </div>
        </main>
    )
}
PHP
        );

        file_put_contents("{$dir}/app/layout.js", <<<PHP
import './globals.css'
export const metadata = { title: '{$name}' }
export default function RootLayout({ children }) { return <html lang="id"><body>{children}</body></html> }
PHP
        );

        file_put_contents("{$dir}/app/globals.css", <<<'CSS'
@tailwind base; @tailwind components; @tailwind utilities;
body { margin: 0; font-family: 'Segoe UI', sans-serif; }
CSS
        );

        file_put_contents("{$dir}/.gitignore", "node_modules\n.next\nout\n.env\n");
        file_put_contents("{$dir}/README.md", <<<MD
# {$name} - Next.js

## Cara Update Tampilan:
1. Edit file di folder <code>app/</code> (misalnya <code>app/page.js</code>)
2. Jalankan <code>npm run build</code> di terminal
3. Tampilan akan otomatis ter-update!

## Struktur Folder:
- <code>app/page.js</code> - Halaman utama (edit ini untuk ubah tampilan)
- <code>app/layout.js</code> - Layout global
- <code>app/globals.css</code> - File CSS global (include Tailwind)
- <code>next.config.js</code> - Konfigurasi Next.js
- <code>tailwind.config.js</code> - Konfigurasi Tailwind

## Power by Ryaze.my.id
Email: bimaryan046@gmail.com
MD
        );
    }

    private function scaffoldNode(string $dir, string $name): void
    {
        $safeName = preg_replace('/[^a-z0-9-]/', '-', strtolower($name));
        @mkdir($dir, 0755, true);
        @mkdir("{$dir}/routes", 0755, true);
        @mkdir("{$dir}/controllers", 0755, true);
        @mkdir("{$dir}/models", 0755, true);
        @mkdir("{$dir}/middleware", 0755, true);
        @mkdir("{$dir}/public", 0755, true);

        file_put_contents("{$dir}/package.json", json_encode([
            'name' => $safeName,
            'version' => '1.0.0',
            'main' => 'index.js',
            'scripts' => ['start' => 'node index.js', 'dev' => 'node index.js'],
            'dependencies' => ['express' => '^4.19.2', 'jsonwebtoken' => '^9.0.2', 'bcryptjs' => '^2.4.3'],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        file_put_contents("{$dir}/index.js", <<<PHP
const express = require('express');
const path = require('path');
const app = express();
const PORT = process.env.PORT || 3000;

app.use(express.json());
// Serve static files from public directory
app.use(express.static(path.join(__dirname, 'public')));

// Auth routes
app.post('/api/login', (req, res) => {
    res.json({ message: 'Login endpoint', token: 'demo-jwt-token' });
});

// Protected routes
app.get('/api/users', (req, res) => {
    res.json({ name: '{$name}', message: 'Express.js + JWT Auth siap!' });
});

app.get('/', (req, res) => {
    res.json({
        name: '{$name}',
        message: 'REST API Express.js dengan struktur MVC, middleware auth JWT siap pakai!',
        power_by: 'Ryaze.my.id',
        email: 'bimaryan046@gmail.com',
        endpoints: ['GET /', 'POST /api/login', 'GET /api/users']
    });
});

app.listen(PORT, () => {
    console.log('{$name} listening on port ' + PORT);
});
PHP
        );

        file_put_contents("{$dir}/middleware/auth.js", <<<'JS'
const jwt = require('jsonwebtoken');
module.exports = (req, res, next) => { next(); };
JS
        );

        file_put_contents("{$dir}/.gitignore", "node_modules\n.env\n");
        file_put_contents("{$dir}/.env.example", "PORT=3000\nJWT_SECRET=your-secret-key\n");

        // Tambahkan index.html di public untuk menampilkan info jika diakses via browser
        file_put_contents("{$dir}/public/index.html", <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$name} - Node.js API</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #2c3e50 0%, #4ca1af 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: white; border-radius: 20px; padding: 60px 50px; text-align: center; max-width: 600px; box-shadow: 0 25px 50px rgba(0,0,0,0.2); }
        h1 { font-size: 2.5rem; font-weight: 800; color: #1a1a2e; margin-bottom: 16px; }
        p { color: #6b7280; line-height: 1.8; margin-bottom: 24px; }
        .endpoint { background: #f3f4f6; padding: 10px 20px; border-radius: 10px; margin: 8px 0; font-family: monospace; }
        .watermark { position: fixed; bottom: 20px; right: 20px; background: rgba(255,255,255,0.9); padding: 12px 20px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); font-size: 13px; }
        .watermark a { color: #2c3e50; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🚀 {$name}</h1>
        <p>Node.js Express API sudah berjalan!</p>
        <div>
            <p><strong>Endpoints:</strong></p>
            <div class="endpoint">GET / - Halaman ini</div>
            <div class="endpoint">POST /api/login - Login endpoint</div>
            <div class="endpoint">GET /api/users - Users endpoint</div>
        </div>
    </div>
    <div class="watermark">
        Power by <a href="https://ryaze.my.id" target="_blank">Ryaze.my.id</a> | Email: <a href="mailto:bimaryan046@gmail.com">bimaryan046@gmail.com</a>
    </div>
</body>
</html>
HTML
        );
    }
}
