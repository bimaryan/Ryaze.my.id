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

            clearstatcache();
            // Nginx OpenResty sudah memiliki fallback otomatis ke index.html untuk SPA
            // sehingga PHP SPA Proxy tidak lagi diperlukan.

            $this->log($deploy, "\n> Applying final permissions to all generated files...");
            $this->exec("chown -R www-data:www-data {$projectDir} 2>/dev/null || true", $deploy);
            $this->exec("find {$projectDir} -type d -not -path '*/node_modules*' -exec chmod 755 {} \; 2>/dev/null || true", $deploy);
            $this->exec("find {$projectDir} -type f -not -path '*/venv/bin/*' -not -path '*/node_modules*' -exec chmod 644 {} \; 2>/dev/null || true", $deploy);

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
        clearstatcache();
        if (is_dir("{$projectDir}/node_modules/.bin")) {
            $this->exec("chmod -R +x {$projectDir}/node_modules/.bin 2>&1 || true", $deploy);
            $this->exec("chmod +x {$projectDir}/node_modules/.bin/* 2>/dev/null || true", $deploy);
            $this->exec("find {$projectDir}/node_modules -path '*/bin/*' -type f -exec chmod +x {} \; 2>/dev/null || true", $deploy);
            $this->log($deploy, "> Executable permission di-set untuk node_modules/.bin dan bin files");
        } else {
            // Fallback: pastikan semua isi node_modules memiliki hak eksekusi jika .bin tidak terdeteksi
            $this->exec("chmod -R 755 {$projectDir}/node_modules 2>/dev/null || true", $deploy);
            $this->log($deploy, "> Executable permission fallback di-set untuk seluruh node_modules");
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
                $this->log($deploy, '> Memindahkan hasil build SPA ke root agar Nginx dapat melayani aset statis (/assets) dengan benar...');
                $this->exec("
                    if [ -d \"{$projectDir}/dist\" ]; then
                        cp -a {$projectDir}/dist/* {$projectDir}/ 2>/dev/null || true
                    elif [ -d \"{$projectDir}/build\" ]; then
                        cp -a {$projectDir}/build/* {$projectDir}/ 2>/dev/null || true
                    elif [ -d \"{$projectDir}/out\" ]; then
                        cp -a {$projectDir}/out/* {$projectDir}/ 2>/dev/null || true
                    fi
                ", $deploy);
                $this->log($deploy, '> Berhasil memindahkan output build ke root direktori.');
            }
        }
    }

    private function setupLaravel($deploy, string $projectDir): void
    {
        $this->exec("rm -f {$projectDir}/public/hot 2>/dev/null || true", $deploy);
        $this->log($deploy, "> Menghapus public/hot (jika ada) agar Laravel Vite menggunakan production build.");
        
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
        // Menghapus file public/hot peninggalan npm run dev di lokal agar Laravel membaca file build production
        $this->exec("rm -f {$projectDir}/public/hot 2>/dev/null || true", $deploy);
        $this->log($deploy, "> Menghapus public/hot (jika ada) agar Laravel menggunakan production build.");

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
        // Gunakan --system-site-packages agar venv bisa membaca library bawaan sistem (seperti scikit-learn versi Alpine)
        $this->exec("cd {$projectDir} && python3 -m venv --system-site-packages venv", $deploy);
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
            
            // Install build dependencies AND pre-compiled Alpine Python ML packages
            // Ini untuk menghindari kompilasi scikit-learn, numpy, pandas dari nol yang error di Alpine
            $this->exec("apk add --no-cache gcc g++ cmake make python3-dev py3-scikit-learn py3-numpy py3-scipy py3-pandas py3-joblib 2>/dev/null || true", $deploy);

            
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
            $this->log($deploy, "> Menyiapkan PHP Reverse Proxy untuk OpenResty (mengatasi isolasi Docker)...");
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
            'vue_starter' => $this->scaffoldVue($dir, $projectName),
            'nuxt_starter' => $this->scaffoldNuxt($dir, $projectName),
            'svelte_starter' => $this->scaffoldSvelte($dir, $projectName),
            'node_express' => $this->scaffoldNode($dir, $projectName),
            'ghost_cms' => $this->scaffoldGhost($dir, $projectName, $deploy),
            'wordpress' => $this->scaffoldWordpress($dir, $projectName, $deploy),
            'tailwind_starter' => $this->scaffoldTailwind($dir, $projectName),
            'tailwind_portfolio' => $this->scaffoldTailwindPortfolio($dir, $projectName),
            'tailwind_landing' => $this->scaffoldTailwindLanding($dir, $projectName),
            'tailwind_blog' => $this->scaffoldTailwindBlog($dir, $projectName),
            'tailwind_ecommerce' => $this->scaffoldTailwindEcommerce($dir, $projectName),
            'tailwind_admin' => $this->scaffoldTailwindAdmin($dir, $projectName),
            'tailwind_linkinbio' => $this->scaffoldTailwindLinkinbio($dir, $projectName),
            default => throw new \RuntimeException("Unknown template key: {$key}"),
        };
    }

    private function scaffoldTailwind(string $dir, string $name): void
    {
        @mkdir($dir, 0755, true);
        file_put_contents("{$dir}/index.html", <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$name} - Tailwind CSS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: '#0ea5e9',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">
        <div class="bg-gradient-to-br from-brand to-blue-600 p-8 text-center relative overflow-hidden">
            <div class="absolute inset-0 bg-white/10 opacity-50 pattern-dots"></div>
            <h1 class="text-3xl font-extrabold text-white mb-2 relative z-10">Tailwind CSS</h1>
            <p class="text-blue-100 font-medium relative z-10">Starter Template Siap Pakai!</p>
        </div>
        <div class="p-8">
            <div class="space-y-6">
                <div class="flex items-start gap-4">
                    <div class="bg-green-100 p-2 rounded-full flex-shrink-0 text-green-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-slate-600 text-sm leading-relaxed">Tanpa build atau <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-800">npm install</code>. Langsung edit HTML dan gunakan utility class Tailwind!</p>
                </div>
                <div class="flex items-start gap-4">
                    <div class="bg-green-100 p-2 rounded-full flex-shrink-0 text-green-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-slate-600 text-sm leading-relaxed">Sangat cocok untuk membuat prototipe desain dan landing page interaktif dengan cepat.</p>
                </div>
            </div>
            
            <div class="mt-8">
                <a href="#" class="block w-full text-center bg-brand hover:bg-blue-600 text-white font-semibold py-3.5 px-4 rounded-xl transition-all hover:-translate-y-0.5 hover:shadow-lg shadow-brand/30">
                    Mulai Mendesain
                </a>
            </div>
        </div>
        <div class="bg-slate-50 p-4 text-center text-xs text-slate-500 border-t border-slate-100">
            Powered by <span class="font-semibold text-slate-700">Ryaze.my.id</span>
        </div>
    </div>
</body>
</html>
HTML
        );
    }

    private function scaffoldTailwindPortfolio(string $dir, string $name): void
    {
        @mkdir($dir, 0755, true);
        file_put_contents("{$dir}/index.html", <<<HTML
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$name} - Personal Portfolio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-200 antialiased selection:bg-indigo-500 selection:text-white">
    <nav class="sticky top-0 z-50 backdrop-blur-md bg-slate-900/80 border-b border-slate-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="font-bold text-xl text-white tracking-tight">{$name}<span class="text-indigo-500">.</span></div>
                <div class="flex space-x-8">
                    <a href="#about" class="text-sm font-medium hover:text-white transition-colors">About</a>
                    <a href="#projects" class="text-sm font-medium hover:text-white transition-colors">Projects</a>
                    <a href="#contact" class="text-sm font-medium hover:text-white transition-colors">Contact</a>
                </div>
            </div>
        </div>
    </nav>
    <main>
        <section id="hero" class="py-20 lg:py-32 flex flex-col items-center text-center px-4">
            <div class="w-24 h-24 bg-gradient-to-tr from-indigo-500 to-purple-500 rounded-full mb-6 p-1">
                <div class="w-full h-full bg-slate-800 rounded-full border-4 border-slate-900"></div>
            </div>
            <h1 class="text-5xl lg:text-7xl font-extrabold text-white tracking-tight mb-6">Creative Developer</h1>
            <p class="text-xl text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed">I build exceptional and accessible digital experiences for the web. Specialized in modern frontend frameworks.</p>
            <div class="flex gap-4">
                <a href="#contact" class="bg-indigo-600 hover:bg-indigo-500 text-white px-8 py-3 rounded-full font-semibold transition-all">Get in touch</a>
                <a href="#projects" class="bg-slate-800 hover:bg-slate-700 text-white px-8 py-3 rounded-full font-semibold transition-all">View Work</a>
            </div>
        </section>
    </main>
</body>
</html>
HTML
        );
    }

    private function scaffoldTailwindLanding(string $dir, string $name): void
    {
        @mkdir($dir, 0755, true);
        file_put_contents("{$dir}/index.html", <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$name} - SaaS Landing Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-slate-800 antialiased">
    <header class="bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex justify-between items-center">
            <div class="font-black text-2xl text-indigo-600 tracking-tighter">{$name}</div>
            <nav class="hidden md:flex gap-8 font-medium text-slate-600">
                <a href="#" class="hover:text-indigo-600">Features</a>
                <a href="#" class="hover:text-indigo-600">Pricing</a>
                <a href="#" class="hover:text-indigo-600">Testimonials</a>
            </nav>
            <a href="#" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-semibold hover:bg-indigo-700 transition">Get Started</a>
        </div>
    </header>
    <main>
        <section class="py-24 text-center px-4 max-w-4xl mx-auto">
            <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight mb-8">Build faster. Scale infinitely.</h1>
            <p class="text-xl text-slate-500 mb-10 leading-relaxed">The ultimate platform for modern teams to collaborate, design, and ship products at lightning speed.</p>
            <div class="flex justify-center gap-4">
                <input type="email" placeholder="Enter your email" class="px-5 py-3 bg-slate-100 border-none rounded-lg w-64 focus:ring-2 focus:ring-indigo-600 outline-none">
                <button class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-indigo-700">Try for free</button>
            </div>
        </section>
    </main>
</body>
</html>
HTML
        );
    }

    private function scaffoldTailwindBlog(string $dir, string $name): void
    {
        @mkdir($dir, 0755, true);
        file_put_contents("{$dir}/index.html", <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$name} - Blog</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-stone-50 text-stone-800 antialiased font-serif">
    <header class="max-w-3xl mx-auto px-6 py-12 border-b border-stone-200">
        <h1 class="text-4xl font-black tracking-tight mb-2">{$name} Journal</h1>
        <p class="text-stone-500 italic">Thoughts on design, code, and life.</p>
    </header>
    <main class="max-w-3xl mx-auto px-6 py-12">
        <article class="mb-16">
            <div class="text-sm text-stone-400 mb-2 font-sans tracking-widest uppercase">October 12, 2026</div>
            <h2 class="text-3xl font-bold mb-4 hover:text-stone-600 cursor-pointer transition">The Art of Minimalism in UI Design</h2>
            <p class="text-lg leading-relaxed text-stone-600 mb-6">Minimalism isn't just about removing things; it's about making sure everything that remains has a clear purpose. In modern web development, this translates to faster load times and clearer user flows...</p>
            <a href="#" class="text-stone-900 font-bold border-b border-stone-900 pb-1 hover:text-stone-500 hover:border-stone-500 transition">Read article &rarr;</a>
        </article>
    </main>
</body>
</html>
HTML
        );
    }
    private function scaffoldTailwindEcommerce(string $dir, string $name): void
    {
        @mkdir($dir, 0755, true);
        file_put_contents("{$dir}/index.html", <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$name} - E-Commerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-900">
    <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <span class="text-2xl font-black text-pink-600 tracking-tighter">Shop<span class="text-gray-900">App</span></span>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="#" class="border-pink-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Home</a>
                    <a href="#" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Katalog</a>
                    <a href="#" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Promo</a>
                </div>
            </div>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-pink-600 rounded-3xl overflow-hidden shadow-xl mb-16 relative">
            <div class="px-8 py-16 sm:px-12 sm:py-24 relative z-10">
                <h1 class="text-4xl sm:text-5xl font-extrabold text-white tracking-tight mb-4">Koleksi Musim Panas 2026</h1>
                <p class="text-pink-100 text-lg sm:text-xl max-w-2xl mb-8">Diskon hingga 50% untuk produk terpilih. Belanja sekarang sebelum kehabisan!</p>
                <a href="#" class="inline-block bg-white text-pink-600 font-bold px-8 py-3 rounded-full hover:bg-gray-50 transition shadow-md">Belanja Sekarang</a>
            </div>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Produk Terbaru</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="group">
                <div class="w-full aspect-[4/5] bg-gray-200 rounded-2xl overflow-hidden mb-4 relative">
                    <img src="https://placehold.co/400x500/e2e8f0/64748b?text=Product+1" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <span class="absolute top-3 left-3 bg-white px-2 py-1 text-xs font-bold uppercase rounded text-gray-900 shadow-sm">Baru</span>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">Sepatu Sneakers Klasik</h3>
                <p class="text-lg font-bold text-gray-900">Rp 450.000</p>
            </div>
            <div class="group">
                <div class="w-full aspect-[4/5] bg-gray-200 rounded-2xl overflow-hidden mb-4 relative">
                    <img src="https://placehold.co/400x500/e2e8f0/64748b?text=Product+2" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">Kemeja Flanel Premium</h3>
                <p class="text-lg font-bold text-gray-900">Rp 250.000</p>
            </div>
        </div>
    </main>
</body>
</html>
HTML
        );
    }

    private function scaffoldTailwindAdmin(string $dir, string $name): void
    {
        @mkdir($dir, 0755, true);
        file_put_contents("{$dir}/index.html", <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$name} - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800 h-screen flex overflow-hidden">
    <aside class="bg-gray-900 w-64 flex-shrink-0 flex flex-col hidden md:flex">
        <div class="h-16 flex items-center px-6 border-b border-gray-800">
            <span class="text-white text-xl font-bold tracking-wider uppercase">Admin<span class="text-emerald-500">Panel</span></span>
        </div>
        <div class="flex-1 overflow-y-auto py-4">
            <nav class="px-3 space-y-1">
                <a href="#" class="bg-gray-800 text-white px-3 py-2 rounded-md flex items-center gap-3 text-sm font-medium">Dashboard</a>
                <a href="#" class="text-gray-300 hover:bg-gray-800 hover:text-white px-3 py-2 rounded-md flex items-center gap-3 text-sm font-medium">Users</a>
                <a href="#" class="text-gray-300 hover:bg-gray-800 hover:text-white px-3 py-2 rounded-md flex items-center gap-3 text-sm font-medium">Analytics</a>
            </nav>
        </div>
    </aside>
    <div class="flex-1 flex flex-col w-full h-full">
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6">
            <h1 class="text-xl font-bold text-gray-800">Dashboard Overview</h1>
        </header>
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <p class="text-sm font-medium text-gray-500">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">12,543</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800">Recent Transactions</h2>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
HTML
        );
    }

    private function scaffoldTailwindLinkinbio(string $dir, string $name): void
    {
        @mkdir($dir, 0755, true);
        file_put_contents("{$dir}/index.html", <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$name} - Link in Bio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 min-h-screen font-sans antialiased flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white/20 backdrop-blur-xl rounded-3xl p-8 border border-white/30 shadow-2xl">
        <div class="text-center mb-8">
            <div class="w-24 h-24 mx-auto bg-white p-1 rounded-full shadow-lg mb-4">
                <img src="https://ui-avatars.com/api/?name=Jane+Doe&background=random&size=128" alt="Profile" class="w-full h-full object-cover rounded-full">
            </div>
            <h1 class="text-2xl font-bold text-white mb-1">@janedoe</h1>
            <p class="text-indigo-100 text-sm">Designer & Content Creator 🎨✨</p>
        </div>
        <div class="space-y-4">
            <a href="#" class="block w-full bg-white text-indigo-900 text-center font-bold py-4 rounded-xl shadow-md hover:scale-105 hover:shadow-xl transition-all duration-300">My Portfolio Website</a>
            <a href="#" class="block w-full bg-white text-indigo-900 text-center font-bold py-4 rounded-xl shadow-md hover:scale-105 hover:shadow-xl transition-all duration-300">Latest YouTube Video 📺</a>
        </div>
    </div>
</body>
</html>
HTML
        );
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

    private function scaffoldWordpress(string $dir, string $name, $deploy): void
    {
        $this->log($deploy, "🚀 Mengunduh dan memasang WordPress terbaru...");

        $parentDir = dirname($dir);
        $baseName = basename($dir);
        $tmpDir = "{$parentDir}/.tmp_{$baseName}_wp";

        $this->exec("rm -rf {$tmpDir} 2>/dev/null || true", $deploy);
        $this->exec("mkdir -p {$tmpDir}", $deploy);

        $this->log($deploy, "> Mengunduh dan mengekstrak core WordPress...");
        $this->exec("cd {$tmpDir} && curl -sL https://wordpress.org/latest.tar.gz | tar xz 2>&1", $deploy, true);
        
        $this->exec("rm -rf {$dir} 2>/dev/null || true", $deploy);
        
        $this->log($deploy, "> Memindahkan file ke direktori project...");
        $this->exec("mv {$tmpDir}/wordpress {$dir}", $deploy, true);
        $this->exec("rm -rf {$tmpDir}", $deploy);
        
        $this->log($deploy, "✅ WordPress berhasil dipasang! Silakan buat database dan buka website untuk instalasi.");
    }

    private function scaffoldVue(string $dir, string $name): void
    {
        $safeName = preg_replace('/[^a-z0-9-]/', '-', strtolower($name));
        @mkdir($dir, 0755, true);
        @mkdir("{$dir}/src", 0755, true);

        file_put_contents("{$dir}/package.json", json_encode([
            'name' => $safeName,
            'version' => '1.0.0',
            'private' => true,
            'scripts' => ['dev' => 'vite', 'build' => 'vite build', 'preview' => 'vite preview'],
            'dependencies' => ['vue' => '^3.3.4'],
            'devDependencies' => ['@vitejs/plugin-vue' => '^4.2.3', 'vite' => '^4.4.5']
        ], JSON_PRETTY_PRINT));

        file_put_contents("{$dir}/vite.config.js", <<<JS
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
})
JS
        );

        file_put_contents("{$dir}/index.html", <<<HTML
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{$name}</title>
  </head>
  <body>
    <div id="app"></div>
    <script type="module" src="/src/main.js"></script>
  </body>
</html>
HTML
        );

        file_put_contents("{$dir}/src/main.js", <<<JS
import { createApp } from 'vue'
import App from './App.vue'

createApp(App).mount('#app')
JS
        );

        file_put_contents("{$dir}/src/App.vue", <<<VUE
<template>
  <div style="font-family: sans-serif; text-align: center; margin-top: 50px;">
    <h1 style="color: #42b883;">Welcome to Vue 3</h1>
    <p>Your Vue application is running successfully on Ryaze.</p>
  </div>
</template>
VUE
        );
    }

    private function scaffoldNuxt(string $dir, string $name): void
    {
        $safeName = preg_replace('/[^a-z0-9-]/', '-', strtolower($name));
        @mkdir($dir, 0755, true);

        file_put_contents("{$dir}/package.json", json_encode([
            'name' => $safeName,
            'private' => true,
            'type' => 'module',
            'scripts' => [
                'build' => 'nuxt build',
                'dev' => 'nuxt dev',
                'generate' => 'nuxt generate',
                'preview' => 'nuxt preview',
                'postinstall' => 'nuxt prepare'
            ],
            'dependencies' => [
                'nuxt' => '^3.12.0',
                'vue' => '^3.4.0',
                'vue-router' => '^4.4.0'
            ]
        ], JSON_PRETTY_PRINT));

        file_put_contents("{$dir}/app.vue", <<<VUE
<template>
  <div style="font-family: sans-serif; padding: 20px;">
    <h1 style="color: #00DC82;">Welcome to Nuxt 3</h1>
    <p>Your Nuxt SSR application is running successfully.</p>
  </div>
</template>
VUE
        );
        
        file_put_contents("{$dir}/nuxt.config.ts", <<<TS
export default defineNuxtConfig({
  compatibilityDate: '2024-04-03',
  devtools: { enabled: true }
})
TS
        );
        
        // Tambahkan index.html statis sebagai fallback sementara
        file_put_contents("{$dir}/index.html", <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuxt 3 Starter - {$name}</title>
    <style>
        body { font-family: sans-serif; background: #f0fdf4; color: #333; min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .card { background: white; border-radius: 20px; padding: 50px; text-align: center; box-shadow: 0 10px 30px rgba(0, 220, 130, 0.15); border-top: 5px solid #00DC82; max-width: 600px; }
        h1 { color: #00DC82; margin-bottom: 20px; font-size: 2.2rem; }
        p { color: #666; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Nuxt 3 Starter Siap!</h1>
        <p>File proyek Nuxt berhasil dibuat.</p>
        <p style="margin-top:20px; font-size:14px;">
            Silakan jalankan <strong>npm install</strong> & <strong>npm run build</strong> melalui Terminal, lalu jalankan server Nuxt Anda menggunakan fitur Server Node.
        </p>
    </div>
</body>
</html>
HTML
        );
    }

    private function scaffoldSvelte(string $dir, string $name): void
    {
        $safeName = preg_replace('/[^a-z0-9-]/', '-', strtolower($name));
        @mkdir($dir, 0755, true);
        @mkdir("{$dir}/src", 0755, true);
        @mkdir("{$dir}/src/routes", 0755, true);
        
        file_put_contents("{$dir}/package.json", json_encode([
            'name' => $safeName,
            'private' => true,
            'type' => 'module',
            'scripts' => [
                'dev' => 'vite dev',
                'build' => 'vite build',
                'preview' => 'vite preview'
            ],
            'devDependencies' => [
                '@sveltejs/adapter-auto' => '^3.0.0',
                '@sveltejs/kit' => '^2.0.0',
                '@sveltejs/vite-plugin-svelte' => '^3.0.0',
                'svelte' => '^4.2.7',
                'vite' => '^5.0.3'
            ]
        ], JSON_PRETTY_PRINT));

        file_put_contents("{$dir}/vite.config.js", <<<JS
import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';

export default defineConfig({
	plugins: [sveltekit()]
});
JS
        );

        file_put_contents("{$dir}/svelte.config.js", <<<JS
import adapter from '@sveltejs/adapter-auto';

export default {
	kit: {
		adapter: adapter()
	}
};
JS
        );

        file_put_contents("{$dir}/src/app.html", <<<HTML
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<link rel="icon" href="%sveltekit.assets%/favicon.png" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		%sveltekit.head%
	</head>
	<body data-sveltekit-preload-data="hover">
		<div style="display: contents">%sveltekit.body%</div>
	</body>
</html>
HTML
        );

        file_put_contents("{$dir}/src/routes/+page.svelte", <<<SVELTE
<h1 style="color: #ff3e00;">Welcome to SvelteKit</h1>
<p>Your fast, compiled Svelte app is running on Ryaze.</p>
SVELTE
        );
        
        // Tambahkan index.html sebagai fallback sementara (karena SvelteKit berjalan sebagai Node app)
        file_put_contents("{$dir}/index.html", <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SvelteKit Starter - {$name}</title>
    <style>
        body { font-family: sans-serif; background: #fff4f1; color: #333; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: white; border-radius: 20px; padding: 50px; text-align: center; box-shadow: 0 10px 30px rgba(255,62,0,0.15); border-top: 5px solid #ff3e00; }
        h1 { color: #ff3e00; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>SvelteKit Starter Siap!</h1>
        <p>File proyek SvelteKit berhasil dibuat.</p>
        <p style="margin-top:20px; font-size:14px; color:#666;">
            Karena ini berbasis Node.js, silakan jalankan <strong>npm install</strong> & <strong>npm run dev/build</strong> melalui Terminal,<br>atau aktifkan Server Node via panel Ryaze.
        </p>
    </div>
</body>
</html>
HTML
        );
    }

    private function scaffoldGhost(string $dir, string $name, $deploy): void
    {
        $this->log($deploy, "> Menyiapkan environment Ghost CMS (Placeholder)...");
        $safeName = preg_replace('/[^a-z0-9-]/', '-', strtolower($name));
        @mkdir($dir, 0755, true);
        
        file_put_contents("{$dir}/package.json", json_encode([
            'name' => $safeName,
            'version' => '1.0.0',
            'private' => true,
            'scripts' => [
                'start' => 'node index.js'
            ],
            'dependencies' => [
                'express' => '^4.18.2'
            ]
        ], JSON_PRETTY_PRINT));
        
        file_put_contents("{$dir}/index.js", <<<JS
const express = require('express');
const app = express();
app.get('/', (req, res) => res.send('<div style="font-family: sans-serif; text-align: center; margin-top: 50px;"><h1>Ghost CMS Starter</h1><p>Membutuhkan konfigurasi database MySQL & Ghost-CLI secara manual lewat terminal.</p><p>Silakan akses Terminal di panel proyek Anda.</p></div>'));
app.listen(process.env.PORT || 3000, () => console.log('Ghost placeholder running'));
JS
        );
        
        // Tambahkan index.html statis agar Nginx langsung menampilkan halaman
        file_put_contents("{$dir}/index.html", <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ghost CMS - {$name}</title>
    <style>
        body { font-family: sans-serif; background: #15171A; color: white; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #1C1F24; border-radius: 12px; padding: 50px; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.4); border: 1px solid #2D3139; max-width: 600px; }
        h1 { color: #fff; margin-bottom: 20px; font-size: 2.2rem; }
        p { color: #A1A1AA; line-height: 1.6; }
        code { background: #000; padding: 4px 8px; border-radius: 4px; color: #10B981; }
    </style>
</head>
<body>
    <div class="card">
        <h1>👻 Ghost CMS</h1>
        <p>File dasar instalasi sudah siap!</p>
        <p style="margin-bottom:20px;">Aplikasi Ghost membutuhkan koneksi database MySQL terpisah dan instalasi via <code>ghost-cli</code>.</p>
        <p style="font-size: 0.9rem;">Silakan buka <strong>Terminal</strong> dari panel Ryaze Anda untuk melanjutkan instalasi.</p>
    </div>
</body>
</html>
HTML
        );
    }
}
