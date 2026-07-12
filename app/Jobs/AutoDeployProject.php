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
                $this->log($deploy, '> Mengecek tipe output Node.js (Static vs SSR)...');
                clearstatcache();
                $hasStatic = false;
                foreach (['dist', 'build', 'out'] as $staticDir) {
                    if (is_dir("{$projectDir}/{$staticDir}")) {
                        $hasStatic = true;
                        $this->log($deploy, "> Memindahkan hasil build statis ({$staticDir}) ke root agar Nginx dapat melayaninya...");
                        $this->exec("cp -a {$projectDir}/{$staticDir}/* {$projectDir}/ 2>/dev/null || true", $deploy);
                        $this->log($deploy, "> Berhasil memindahkan output build {$staticDir} ke root direktori.");
                        break;
                    }
                }

                // Jika tidak ada folder statis (atau frameworknya adalah 'node'), jalankan mode Server (SSR) dengan PM2
                if (!$hasStatic || $framework === 'node') {
                    $this->log($deploy, '> Output statis tidak ditemukan. Mengasumsikan aplikasi berjalan di mode Server (SSR/API)...');
                    
                    // Assign port
                    $port = null;
                    for ($p = 8000; $p <= 9000; $p++) {
                        $connection = @fsockopen('127.0.0.1', $p, $errCode, $errStr, 0.1);
                        if (!is_resource($connection)) {
                            $port = $p;
                            break;
                        }
                        if (is_resource($connection)) fclose($connection);
                    }

                    if (!$port) {
                        $this->log($deploy, '> [ERROR] Tidak ada port yang tersedia untuk Node Server.');
                        return;
                    }

                    $pm2Name = "prod_{$this->project->id}";
                    
                    // Kill existing process if any
                    $this->log($deploy, "> Menghentikan proses lama (jika ada)...");
                    $this->exec("npx -y pm2 delete {$pm2Name} 2>/dev/null || true", $deploy);
                    
                    if ($this->project->dev_pid) {
                        $this->exec("kill -9 {$this->project->dev_pid} 2>/dev/null || true", $deploy);
                        $this->exec("npx -y pm2 delete \"{$this->project->dev_pid}\" 2>/dev/null || true", $deploy);
                    }

                    $startCommand = "npm start";
                    if ($framework === 'node') {
                        // Jika ada ecosystem.config.js atau server.js, jalankan itu. Jika tidak, npm start
                        if (file_exists("{$projectDir}/ecosystem.config.js")) {
                            $startCommand = "ecosystem.config.js";
                        } elseif (file_exists("{$projectDir}/server.js")) {
                            $startCommand = "server.js";
                        } elseif (file_exists("{$projectDir}/index.js")) {
                            $startCommand = "index.js";
                        } elseif (file_exists("{$projectDir}/app.js")) {
                            $startCommand = "app.js";
                        }
                    }

                    $this->log($deploy, "> Starting Node/SSR Server on port {$port} via PM2...");
                    
                    // Menggunakan file ecosystem config untuk PM2 agar environment variables (PORT, HOSTNAME) 
                    // dijamin diteruskan ke dalam proses Node/Next.js dengan benar.
                    if ($startCommand === 'npm start') {
                        $ecoConfig = "module.exports = { apps: [{ name: '{$pm2Name}', script: 'npm', args: 'run start', env: { PORT: {$port}, HOSTNAME: '127.0.0.1' } }] };";
                        file_put_contents("{$projectDir}/.ryaze-pm2.js", $ecoConfig);
                        $pm2Cmd = "npx -y pm2 start .ryaze-pm2.js";
                    } else {
                        $pm2Cmd = "npx -y pm2 start {$startCommand} --name \"{$pm2Name}\"";
                    }
                    
                    $this->exec("cd {$projectDir} && PORT={$port} HOSTNAME=127.0.0.1 {$pm2Cmd}", $deploy);
                    
                    // Tunggu sebentar agar SSR server (misal Next.js) sempat bootup dan mendengarkan port
                    sleep(3);
                    
                    // Buat proxy script
                    $this->log($deploy, "> Menyiapkan PHP Reverse Proxy untuk OpenResty...");
                    $proxyScript = $this->generatePhpReverseProxy($port, 'Node.js Application Server');
                    file_put_contents("{$projectDir}/index.php", $proxyScript);
                    if (is_dir("{$projectDir}/public")) {
                        file_put_contents("{$projectDir}/public/index.php", $proxyScript);
                    }
                    file_put_contents("{$projectDir}/.port", $port);
                    $this->exec("chown www-data:www-data {$projectDir}/.port", $deploy);
                    
                    // Kita gunakan dev_mode = true untuk mengindikasikan ada server yang berjalan di background
                    // dan menyimpan ID PM2 di dev_pid
                    $this->project->update([
                        'dev_mode' => true,
                        'dev_port' => $port,
                        'dev_pid' => $pm2Name
                    ]);
                    
                    $this->log($deploy, "> Server SSR berjalan dengan PM2 (ID: {$pm2Name}) pada port {$port}.");
                }
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
            $proxyScript = $this->generatePhpReverseProxy($port, 'Python Application Server');
            file_put_contents("{$projectDir}/index.php", $proxyScript);
            if (is_dir("{$projectDir}/public")) {
                file_put_contents("{$projectDir}/public/index.php", $proxyScript);
            }
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
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$name} - Tailwind CSS Starter</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { brand: '#4f46e5' }
                }
            }
        }
    </script>
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');</style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased flex flex-col min-h-screen">
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between h-16 items-center">
            <div class="font-bold text-xl text-brand flex items-center gap-2">
                <i class="fa-solid fa-code"></i> {$name}
            </div>
            <div class="flex gap-4">
                <a href="#" class="text-sm font-medium text-slate-600 hover:text-brand transition">Documentation</a>
                <a href="#" class="text-sm font-medium text-slate-600 hover:text-brand transition">GitHub</a>
            </div>
        </div>
    </nav>
    <main class="flex-grow flex items-center justify-center p-6">
        <div class="max-w-3xl w-full bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">
            <div class="bg-gradient-to-br from-brand to-indigo-700 p-12 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-white/10 opacity-30" style="background-image: radial-gradient(white 1px, transparent 1px); background-size: 20px 20px;"></div>
                <i class="fa-brands fa-css3-alt text-6xl text-white mb-6 relative z-10 drop-shadow-md"></i>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-4 relative z-10 tracking-tight">Tailwind CSS Starter</h1>
                <p class="text-indigo-100 font-medium text-lg relative z-10 max-w-xl mx-auto">Proyek <strong>{$name}</strong> Anda sudah siap digunakan! Tidak perlu repot dengan instalasi NPM atau build tools.</p>
            </div>
            <div class="p-8 sm:p-12">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="flex gap-4">
                        <div class="bg-indigo-50 w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 text-brand">
                            <i class="fa-solid fa-bolt text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 mb-2">Super Cepat</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">Menggunakan Tailwind CSS dari CDN. Langsung render dengan sempurna di semua perangkat.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="bg-indigo-50 w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 text-brand">
                            <i class="fa-solid fa-paintbrush text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 mb-2">Siap Dikustomisasi</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">Buka File Manager Anda, edit <code>index.html</code>, dan mulai tambahkan utility class Tailwind favorit Anda.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="bg-indigo-50 w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 text-brand">
                            <i class="fa-solid fa-icons text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 mb-2">FontAwesome Included</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">Lebih dari 2.000+ ikon gratis siap pakai. Cukup gunakan tag <code>&lt;i class="fa-solid fa-user"&gt;</code>.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="bg-indigo-50 w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 text-brand">
                            <i class="fa-solid fa-mobile-screen text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 mb-2">100% Responsif</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">Gunakan prefix seperti <code>md:</code>, <code>lg:</code>, dan <code>hover:</code> untuk membuat tampilan menakjubkan.</p>
                        </div>
                    </div>
                </div>
                
                    <div class="mt-12 text-center flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="https://tailwindcss.com/docs" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold py-3 px-8 rounded-xl transition-all hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-900/20">
                            Baca Dokumentasi <i class="fa-solid fa-arrow-right"></i>
                        </a>
                        <button onclick="document.getElementById('demoModal').classList.remove('hidden')" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-xl transition-all hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-600/20">
                            Buka Demo Modal <i class="fa-solid fa-up-right-from-square"></i>
                        </button>
                    </div>
                </div>
            </div>
        </main>
        <footer class="py-6 text-center text-sm text-slate-500">
            &copy; 2026 {$name}. Powered by <a href="https://ryaze.my.id" class="font-semibold text-brand hover:underline">Ryaze Hosting</a>.
        </footer>

        <!-- Demo Modal (Sama persis dengan Portal) -->
        <div id="demoModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="document.getElementById('demoModal').classList.add('hidden')"></div>
            
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden relative z-10 transform transition-all flex flex-col max-h-[90vh]">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-cube text-indigo-600"></i> Komponen Modal
                    </h3>
                    <button type="button" onclick="document.getElementById('demoModal').classList.add('hidden')" class="text-slate-400 hover:text-red-500 hover:bg-red-50 w-8 h-8 rounded-lg flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                
                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto">
                    <div class="flex justify-center mb-6">
                        <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-3xl shadow-inner">
                            <i class="fa-solid fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h4 class="text-xl font-bold text-slate-900 mb-2">Desain Premium!</h4>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">
                            Modal ini dirancang agar terlihat sama persis dengan desain yang ada di halaman Portal Ryaze Hosting. Dilengkapi dengan backdrop blur, transisi lembut, dan tombol-tombol modern.
                        </p>
                    </div>
                    
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 mb-2">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-code text-slate-400"></i>
                            <div class="text-left text-sm">
                                <p class="font-semibold text-slate-800">Siap Pakai</p>
                                <p class="text-slate-500 text-xs">Salin kode ini untuk proyek Anda</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end gap-3 shrink-0">
                    <button type="button" onclick="document.getElementById('demoModal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-200 transition-colors">
                        Tutup
                    </button>
                    <button type="button" onclick="document.getElementById('demoModal').classList.add('hidden')" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-sm shadow-indigo-200 hover:-translate-y-0.5 transition-all">
                        Mengerti
                    </button>
                </div>
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
    <title>{$name} - Portfolio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: { primary: '#6366f1' }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');
        .glass { background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .blob { animation: float 6s ease-in-out infinite; }
        @keyframes float { 0% { transform: translateY(0px) scale(1); } 50% { transform: translateY(-20px) scale(1.05); } 100% { transform: translateY(0px) scale(1); } }
    </style>
</head>
<body class="bg-slate-950 text-slate-300 font-sans antialiased selection:bg-primary selection:text-white overflow-x-hidden">
    
    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="#" class="text-2xl font-bold text-white tracking-tight">{$name}<span class="text-primary">.</span></a>
                <div class="hidden md:flex space-x-8">
                    <a href="#home" class="text-sm font-medium text-white hover:text-primary transition">Home</a>
                    <a href="#about" class="text-sm font-medium text-slate-400 hover:text-white transition">About</a>
                    <a href="#projects" class="text-sm font-medium text-slate-400 hover:text-white transition">Projects</a>
                    <a href="#contact" class="text-sm font-medium text-slate-400 hover:text-white transition">Contact</a>
                </div>
                <a href="#contact" class="hidden md:inline-flex items-center justify-center px-5 py-2.5 bg-primary/10 text-primary hover:bg-primary hover:text-white rounded-full text-sm font-semibold transition-all">Let's Talk</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 min-h-screen flex items-center">
        <div class="absolute top-1/4 -left-20 w-96 h-96 bg-primary/20 rounded-full mix-blend-screen filter blur-3xl opacity-50 blob"></div>
        <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-purple-500/20 rounded-full mix-blend-screen filter blur-3xl opacity-50 blob" style="animation-delay: 2s;"></div>
        
        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-800/50 border border-slate-700 text-sm font-medium text-slate-300 mb-6">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span> Available for work
                    </div>
                    <h1 class="text-5xl lg:text-7xl font-extrabold text-white tracking-tight mb-6 leading-tight">
                        Hi, I'm <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-purple-400">{$name}</span>
                    </h1>
                    <p class="text-xl text-slate-400 mb-8 max-w-xl leading-relaxed">
                        A passionate Full Stack Developer & UI/UX Designer specializing in building exceptional digital experiences.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="#projects" class="px-8 py-4 bg-primary hover:bg-indigo-500 text-white rounded-full font-semibold transition-all hover:shadow-[0_0_20px_rgba(99,102,241,0.4)] hover:-translate-y-1">View My Work</a>
                        <a href="https://github.com" target="_blank" class="px-8 py-4 bg-slate-800 hover:bg-slate-700 text-white rounded-full font-semibold transition-all flex items-center gap-2 border border-slate-700 hover:-translate-y-1">
                            <i class="fa-brands fa-github text-xl"></i> Github
                        </a>
                    </div>
                </div>
                <div class="relative hidden lg:block">
                    <div class="w-full aspect-square max-w-md mx-auto relative">
                        <div class="absolute inset-0 bg-gradient-to-tr from-primary to-purple-500 rounded-3xl transform rotate-6 opacity-50 blur-lg"></div>
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Profile" class="w-full h-full object-cover rounded-3xl relative z-10 shadow-2xl border border-slate-800">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services / Skills -->
    <section id="about" class="py-24 bg-slate-900 border-y border-slate-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">What I Do</h2>
                <p class="text-slate-400 max-w-2xl mx-auto">I craft high-performance, beautifully designed web applications from concept to deployment.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Skill 1 -->
                <div class="bg-slate-800/50 p-8 rounded-2xl border border-slate-700 hover:border-primary/50 transition-colors group">
                    <div class="w-14 h-14 bg-slate-800 rounded-xl flex items-center justify-center text-primary text-2xl mb-6 shadow-inner group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">UI/UX Design</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Designing intuitive and modern interfaces with Figma, focusing on user experience and accessibility.</p>
                </div>
                <!-- Skill 2 -->
                <div class="bg-slate-800/50 p-8 rounded-2xl border border-slate-700 hover:border-primary/50 transition-colors group">
                    <div class="w-14 h-14 bg-slate-800 rounded-xl flex items-center justify-center text-purple-400 text-2xl mb-6 shadow-inner group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-code"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Frontend Dev</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Building responsive and interactive web applications using React, Vue, TailwindCSS, and Next.js.</p>
                </div>
                <!-- Skill 3 -->
                <div class="bg-slate-800/50 p-8 rounded-2xl border border-slate-700 hover:border-primary/50 transition-colors group">
                    <div class="w-14 h-14 bg-slate-800 rounded-xl flex items-center justify-center text-emerald-400 text-2xl mb-6 shadow-inner group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-database"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Backend & API</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Developing robust backend systems and RESTful APIs with Node.js, Express, Laravel, and PostgreSQL.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Projects -->
    <section id="projects" class="py-24">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">Featured Projects</h2>
                    <p class="text-slate-400">Some of my recent work.</p>
                </div>
                <a href="#" class="hidden sm:inline-flex text-primary hover:text-white font-medium items-center gap-2 transition">View all <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Project 1 -->
                <div class="group relative rounded-3xl overflow-hidden bg-slate-900 border border-slate-800">
                    <div class="aspect-video w-full overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Project 1" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <a href="#" class="px-6 py-3 bg-white text-slate-900 rounded-full font-bold transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 hover:scale-105">View Live Demo</a>
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="flex gap-2 mb-4">
                            <span class="px-3 py-1 text-xs font-medium bg-primary/10 text-primary rounded-full border border-primary/20">React</span>
                            <span class="px-3 py-1 text-xs font-medium bg-slate-800 text-slate-300 rounded-full border border-slate-700">Tailwind</span>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-2">E-Commerce Dashboard</h3>
                        <p class="text-slate-400 mb-6">A comprehensive admin panel for managing e-commerce stores with real-time analytics.</p>
                    </div>
                </div>
                
                <!-- Project 2 -->
                <div class="group relative rounded-3xl overflow-hidden bg-slate-900 border border-slate-800">
                    <div class="aspect-video w-full overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Project 2" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <a href="#" class="px-6 py-3 bg-white text-slate-900 rounded-full font-bold transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 hover:scale-105">View Live Demo</a>
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="flex gap-2 mb-4">
                            <span class="px-3 py-1 text-xs font-medium bg-purple-500/10 text-purple-400 rounded-full border border-purple-500/20">Next.js</span>
                            <span class="px-3 py-1 text-xs font-medium bg-slate-800 text-slate-300 rounded-full border border-slate-700">Stripe</span>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-2">SaaS Landing Page</h3>
                        <p class="text-slate-400 mb-6">A high-converting landing page for a B2B SaaS startup with integrated payment flows.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-24 bg-slate-900 border-t border-slate-800">
        <div class="max-w-4xl mx-auto px-6 lg:px-8 text-center">
            <h2 class="text-3xl lg:text-5xl font-bold text-white mb-6">Let's build something great together.</h2>
            <p class="text-slate-400 text-lg mb-10">I'm currently open for new opportunities and freelance projects. Whether you have a question or just want to say hi, I'll try my best to get back to you!</p>
            <a href="mailto:hello@example.com" class="inline-flex items-center gap-3 px-8 py-4 bg-primary hover:bg-indigo-500 text-white rounded-full font-bold text-lg transition-all hover:shadow-[0_0_20px_rgba(99,102,241,0.4)] hover:-translate-y-1">
                <i class="fa-regular fa-envelope"></i> Say Hello
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-10 border-t border-slate-800 text-center">
        <div class="flex justify-center gap-6 mb-6">
            <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-primary transition"><i class="fa-brands fa-twitter"></i></a>
            <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-primary transition"><i class="fa-brands fa-github"></i></a>
            <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-primary transition"><i class="fa-brands fa-linkedin-in"></i></a>
            <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-primary transition"><i class="fa-brands fa-dribbble"></i></a>
        </div>
        <p class="text-slate-500 text-sm">&copy; 2026 {$name}. Designed with TailwindCSS.</p>
    </footer>

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
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$name} - SaaS Landing Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { brand: '#2563eb', secondary: '#1e293b' }
                }
            }
        }
    </script>
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');</style>
</head>
<body class="bg-white text-slate-800 font-sans antialiased overflow-x-hidden">
    <!-- Navbar -->
    <header class="fixed top-0 w-full bg-white/80 backdrop-blur-md border-b border-slate-100 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="font-black text-2xl tracking-tighter flex items-center gap-2">
                <div class="w-8 h-8 bg-brand rounded-lg flex items-center justify-center text-white"><i class="fa-solid fa-cube"></i></div>
                {$name}
            </div>
            <nav class="hidden md:flex gap-8 font-medium text-slate-500">
                <a href="#features" class="hover:text-slate-900 transition">Features</a>
                <a href="#testimonials" class="hover:text-slate-900 transition">Testimonials</a>
                <a href="#pricing" class="hover:text-slate-900 transition">Pricing</a>
            </nav>
            <div class="flex gap-4 items-center">
                <a href="#" class="hidden lg:block font-medium text-slate-600 hover:text-slate-900">Sign in</a>
                <a href="#" class="bg-slate-900 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-slate-800 transition shadow-lg shadow-slate-900/20">Get Started</a>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="pt-32 pb-20 lg:pt-48 lg:pb-32 px-4 text-center max-w-5xl mx-auto relative">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-brand/10 rounded-full blur-3xl -z-10"></div>
        <a href="#" class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-sm font-medium text-brand mb-8 hover:bg-blue-100 transition">
            <span class="bg-brand text-white text-[10px] uppercase font-bold px-2 py-0.5 rounded-full">New</span> Introducing AI Features <i class="fa-solid fa-arrow-right"></i>
        </a>
        <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-8 text-slate-900 leading-[1.1]">
            Build faster. <br class="hidden md:block">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand to-cyan-500">Scale infinitely.</span>
        </h1>
        <p class="text-xl text-slate-500 mb-10 max-w-2xl mx-auto leading-relaxed">
            The ultimate platform for modern teams to collaborate, design, and ship products at lightning speed. Start for free today.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="#" class="bg-brand text-white px-8 py-4 rounded-xl font-bold hover:bg-blue-700 transition shadow-xl shadow-brand/30 flex items-center justify-center gap-2 text-lg">
                Start your free trial <i class="fa-solid fa-arrow-right"></i>
            </a>
            <a href="#" class="bg-white text-slate-700 border border-slate-200 px-8 py-4 rounded-xl font-bold hover:bg-slate-50 transition flex items-center justify-center gap-2 text-lg">
                <i class="fa-solid fa-play"></i> Watch Demo
            </a>
        </div>
        
        <!-- Dashboard Mockup -->
        <div class="mt-20 relative mx-auto max-w-5xl">
            <div class="rounded-2xl border border-slate-200/50 bg-slate-50 p-2 shadow-2xl relative">
                <div class="absolute -top-4 -left-4 w-32 h-32 bg-blue-400 rounded-full mix-blend-multiply filter blur-2xl opacity-70"></div>
                <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-purple-400 rounded-full mix-blend-multiply filter blur-2xl opacity-70"></div>
                <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Dashboard" class="rounded-xl border border-slate-200 shadow-sm w-full relative z-10">
            </div>
        </div>
    </section>

    <!-- Trusted By -->
    <section class="py-10 border-y border-slate-100 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-6">Trusted by innovative teams worldwide</p>
            <div class="flex flex-wrap justify-center gap-8 md:gap-16 opacity-50 grayscale hover:grayscale-0 transition-all duration-500">
                <i class="fa-brands fa-aws text-4xl"></i>
                <i class="fa-brands fa-google text-4xl"></i>
                <i class="fa-brands fa-microsoft text-4xl"></i>
                <i class="fa-brands fa-stripe text-4xl"></i>
                <i class="fa-brands fa-figma text-4xl"></i>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-4">Everything you need to succeed</h2>
                <p class="text-lg text-slate-500 max-w-2xl mx-auto">Our platform provides all the tools you need to build, scale, and manage your projects efficiently.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="bg-slate-50 p-8 rounded-2xl border border-slate-100 hover:shadow-lg transition-shadow group">
                    <div class="w-14 h-14 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-center text-brand text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Lightning Fast</h3>
                    <p class="text-slate-500 leading-relaxed">Built on edge infrastructure to deliver content to your users in milliseconds, anywhere in the world.</p>
                </div>
                <div class="bg-slate-50 p-8 rounded-2xl border border-slate-100 hover:shadow-lg transition-shadow group">
                    <div class="w-14 h-14 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-center text-brand text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Enterprise Security</h3>
                    <p class="text-slate-500 leading-relaxed">Bank-grade encryption, role-based access control, and automated compliance out of the box.</p>
                </div>
                <div class="bg-slate-50 p-8 rounded-2xl border border-slate-100 hover:shadow-lg transition-shadow group">
                    <div class="w-14 h-14 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-center text-brand text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Real-time Analytics</h3>
                    <p class="text-slate-500 leading-relaxed">Gain deep insights into user behavior and system performance with our intuitive dashboards.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="py-24 bg-slate-50 border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-4">Simple, transparent pricing</h2>
                <p class="text-lg text-slate-500 max-w-2xl mx-auto">No hidden fees. No surprise charges. Choose the plan that fits your needs.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 max-w-4xl mx-auto gap-8">
                <!-- Basic Plan -->
                <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Starter</h3>
                    <p class="text-slate-500 text-sm mb-6">Perfect for individuals and small projects.</p>
                    <div class="mb-6">
                        <span class="text-4xl font-black text-slate-900">$0</span>
                        <span class="text-slate-500 font-medium">/month</span>
                    </div>
                    <a href="#" class="block w-full py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-center rounded-xl transition">Get Started</a>
                    <ul class="mt-8 space-y-4 text-sm text-slate-600">
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-green-500"></i> Up to 3 projects</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-green-500"></i> Community support</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-green-500"></i> 1GB Storage</li>
                    </ul>
                </div>
                
                <!-- Pro Plan -->
                <div class="bg-slate-900 p-8 rounded-3xl shadow-xl relative overflow-hidden text-white">
                    <div class="absolute top-0 right-0 bg-brand text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-3xl">POPULAR</div>
                    <h3 class="text-xl font-bold mb-2">Professional</h3>
                    <p class="text-slate-400 text-sm mb-6">For growing teams and businesses.</p>
                    <div class="mb-6">
                        <span class="text-4xl font-black">$29</span>
                        <span class="text-slate-400 font-medium">/month</span>
                    </div>
                    <a href="#" class="block w-full py-3 px-4 bg-brand hover:bg-blue-500 text-white font-bold text-center rounded-xl transition shadow-lg shadow-brand/30">Start 14-day Free Trial</a>
                    <ul class="mt-8 space-y-4 text-sm text-slate-300">
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-brand"></i> Unlimited projects</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-brand"></i> 24/7 Priority support</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-brand"></i> 100GB Storage</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-brand"></i> Advanced Analytics</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20 bg-brand">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-5xl font-bold text-white mb-6">Ready to transform your workflow?</h2>
            <p class="text-lg text-blue-100 mb-10">Join thousands of teams who are already building the future on our platform.</p>
            <a href="#" class="bg-white text-brand px-8 py-4 rounded-xl font-bold hover:bg-slate-50 transition shadow-lg text-lg inline-block">Get Started for Free</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-2 md:grid-cols-4 gap-8 mb-12">
            <div>
                <div class="font-black text-xl tracking-tighter flex items-center gap-2 mb-6 text-white">
                    <div class="w-6 h-6 bg-brand rounded flex items-center justify-center text-white text-xs"><i class="fa-solid fa-cube"></i></div>
                    {$name}
                </div>
                <p class="text-slate-500 text-sm">Building the future of web development, one block at a time.</p>
            </div>
            <div>
                <h4 class="font-bold text-white mb-4">Product</h4>
                <ul class="space-y-3 text-sm text-slate-400">
                    <li><a href="#" class="hover:text-white transition">Features</a></li>
                    <li><a href="#" class="hover:text-white transition">Pricing</a></li>
                    <li><a href="#" class="hover:text-white transition">Changelog</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-white mb-4">Company</h4>
                <ul class="space-y-3 text-sm text-slate-400">
                    <li><a href="#" class="hover:text-white transition">About Us</a></li>
                    <li><a href="#" class="hover:text-white transition">Careers</a></li>
                    <li><a href="#" class="hover:text-white transition">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-white mb-4">Legal</h4>
                <ul class="space-y-3 text-sm text-slate-400">
                    <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-white transition">Terms of Service</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm text-slate-500">&copy; 2026 {$name} Inc. All rights reserved.</p>
            <div class="flex gap-4 text-slate-500">
                <a href="#" class="hover:text-white transition"><i class="fa-brands fa-twitter"></i></a>
                <a href="#" class="hover:text-white transition"><i class="fa-brands fa-github"></i></a>
                <a href="#" class="hover:text-white transition"><i class="fa-brands fa-discord"></i></a>
            </div>
        </div>
    </footer>
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
    <title>{$name} - Blog & Journal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Merriweather', 'serif']
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400&display=swap');
    </style>
</head>
<body class="bg-stone-50 text-stone-900 antialiased font-sans">
    
    <!-- Header -->
    <header class="border-b border-stone-200 bg-white sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between h-20 items-center">
            <a href="#" class="font-serif font-black text-2xl tracking-tight">{$name}<span class="text-orange-500">.</span></a>
            <nav class="hidden md:flex gap-6 font-medium text-sm text-stone-600">
                <a href="#" class="hover:text-stone-900 transition">Design</a>
                <a href="#" class="hover:text-stone-900 transition">Technology</a>
                <a href="#" class="hover:text-stone-900 transition">Life</a>
                <a href="#" class="hover:text-stone-900 transition">About</a>
            </nav>
            <div class="flex items-center gap-4">
                <button class="w-10 h-10 rounded-full flex items-center justify-center text-stone-500 hover:bg-stone-100 transition"><i class="fa-solid fa-magnifying-glass"></i></button>
                <a href="#" class="bg-stone-900 text-white px-5 py-2 rounded-full text-sm font-semibold hover:bg-stone-800 transition shadow-md">Subscribe</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        <!-- Featured Post -->
        <article class="mb-20 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center group cursor-pointer">
            <div class="overflow-hidden rounded-2xl shadow-lg relative aspect-[4/3] lg:aspect-auto lg:h-[450px]">
                <img src="https://images.unsplash.com/photo-1499750310107-5fef28a66643?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" class="w-full h-full object-cover group-hover:scale-105 transition duration-700" alt="Featured">
                <div class="absolute inset-0 bg-stone-900/10 group-hover:bg-transparent transition duration-700"></div>
            </div>
            <div class="flex flex-col justify-center">
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-orange-600 font-bold text-xs tracking-widest uppercase">Design</span>
                    <span class="text-stone-400 text-sm">Oct 12, 2026</span>
                </div>
                <h1 class="text-4xl md:text-5xl font-serif font-bold mb-6 leading-tight group-hover:text-orange-600 transition">The Art of Minimalism in Modern UI Design</h1>
                <p class="text-lg text-stone-600 font-serif leading-relaxed mb-8">Minimalism isn't just about removing things; it's about making sure everything that remains has a clear purpose. In modern web development, this translates to faster load times and clearer user flows.</p>
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name=Alex+Carter&background=f97316&color=fff" class="w-10 h-10 rounded-full shadow-sm">
                    <div>
                        <p class="text-sm font-bold">Alex Carter</p>
                        <p class="text-xs text-stone-500">Lead Designer</p>
                    </div>
                </div>
            </div>
        </article>

        <div class="flex items-center justify-between border-b border-stone-200 pb-4 mb-10">
            <h2 class="text-2xl font-bold font-serif">Latest Articles</h2>
            <a href="#" class="text-sm font-bold text-orange-600 hover:text-orange-700 flex items-center gap-1">View All <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <!-- Grid Posts -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 mb-20">
            <!-- Article 1 -->
            <article class="group cursor-pointer">
                <div class="overflow-hidden rounded-xl mb-5 aspect-[4/3] shadow-md relative">
                    <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                </div>
                <span class="text-blue-600 font-bold text-xs tracking-widest uppercase mb-2 block">Technology</span>
                <h3 class="text-xl font-serif font-bold mb-3 leading-snug group-hover:text-blue-600 transition">Why you should learn Rust in 2026</h3>
                <p class="text-stone-600 line-clamp-3 font-serif text-sm leading-relaxed mb-4">Memory safety without garbage collection is just the beginning of why developers love Rust.</p>
                <p class="text-xs text-stone-400 font-medium">Oct 10, 2026 &middot; 5 min read</p>
            </article>

            <!-- Article 2 -->
            <article class="group cursor-pointer">
                <div class="overflow-hidden rounded-xl mb-5 aspect-[4/3] shadow-md relative">
                    <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                </div>
                <span class="text-green-600 font-bold text-xs tracking-widest uppercase mb-2 block">Life</span>
                <h3 class="text-xl font-serif font-bold mb-3 leading-snug group-hover:text-green-600 transition">Balancing remote work and mental health</h3>
                <p class="text-stone-600 line-clamp-3 font-serif text-sm leading-relaxed mb-4">Tips and strategies for maintaining boundaries when your office is also your living room.</p>
                <p class="text-xs text-stone-400 font-medium">Oct 08, 2026 &middot; 8 min read</p>
            </article>

            <!-- Article 3 -->
            <article class="group cursor-pointer">
                <div class="overflow-hidden rounded-xl mb-5 aspect-[4/3] shadow-md relative">
                    <img src="https://images.unsplash.com/photo-1618761714954-0b8cd0026356?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                </div>
                <span class="text-purple-600 font-bold text-xs tracking-widest uppercase mb-2 block">AI</span>
                <h3 class="text-xl font-serif font-bold mb-3 leading-snug group-hover:text-purple-600 transition">The future of generative AI in software development</h3>
                <p class="text-stone-600 line-clamp-3 font-serif text-sm leading-relaxed mb-4">How AI agents are changing the way we write, debug, and deploy code in production.</p>
                <p class="text-xs text-stone-400 font-medium">Oct 05, 2026 &middot; 12 min read</p>
            </article>
        </div>

        <!-- Newsletter -->
        <div class="bg-stone-900 rounded-3xl p-10 md:p-16 text-center text-white relative overflow-hidden shadow-2xl">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-orange-500/20 rounded-full blur-3xl"></div>
            
            <i class="fa-regular fa-envelope-open text-4xl text-orange-500 mb-6 relative z-10"></i>
            <h2 class="text-3xl font-serif font-bold mb-4 relative z-10">Get the latest articles in your inbox</h2>
            <p class="text-stone-400 mb-8 max-w-lg mx-auto relative z-10">Join 5,000+ subscribers who receive our weekly newsletter on design, code, and startups. No spam.</p>
            <form class="flex flex-col sm:flex-row gap-3 justify-center max-w-md mx-auto relative z-10">
                <input type="email" placeholder="Your email address" class="px-6 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent flex-grow transition">
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-xl font-bold transition shadow-lg shadow-orange-500/30">Subscribe</button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-stone-200 py-12 mt-10">
        <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="font-serif font-black text-xl tracking-tight">{$name}<span class="text-orange-500">.</span></div>
            <p class="text-stone-500 text-sm font-medium">&copy; 2026 {$name}. All rights reserved.</p>
            <div class="flex gap-4 text-stone-400">
                <a href="#" class="hover:text-stone-900 transition"><i class="fa-brands fa-twitter text-lg"></i></a>
                <a href="#" class="hover:text-stone-900 transition"><i class="fa-brands fa-github text-lg"></i></a>
                <a href="#" class="hover:text-stone-900 transition"><i class="fa-brands fa-dribbble text-lg"></i></a>
            </div>
        </div>
    </footer>
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
    <title>{$name} - Modern E-Commerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: { brand: '#0f172a', accent: '#f43f5e' }
                }
            }
        }
    </script>
    <style>@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');</style>
</head>
<body class="bg-white font-sans antialiased text-slate-900 selection:bg-accent selection:text-white overflow-x-hidden">
    <!-- Topbar -->
    <div class="bg-brand text-white text-xs font-semibold text-center py-2.5 tracking-wide">
        Diskon Hingga 50% untuk Pengguna Baru! Gunakan kode: <span class="font-black text-accent ml-1 px-2 py-0.5 bg-white/10 rounded">NEW50</span>
    </div>

    <!-- Navbar -->
    <nav class="bg-white border-b border-slate-100 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-2">
                    <div class="w-10 h-10 bg-accent rounded-xl flex items-center justify-center text-white text-xl shadow-lg shadow-accent/30">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                    <span class="text-2xl font-black tracking-tighter uppercase">{$name}</span>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-10">
                    <a href="#" class="text-slate-900 font-bold border-b-2 border-brand py-1">Pria</a>
                    <a href="#" class="text-slate-500 hover:text-slate-900 font-medium py-1 transition">Wanita</a>
                    <a href="#" class="text-slate-500 hover:text-slate-900 font-medium py-1 transition">Anak</a>
                    <a href="#" class="text-accent font-bold py-1 flex items-center gap-1 transition">Sale <i class="fa-solid fa-tag text-xs"></i></a>
                </div>

                <!-- Icons -->
                <div class="flex items-center gap-6">
                    <button class="text-slate-500 hover:text-brand transition text-xl"><i class="fa-solid fa-magnifying-glass"></i></button>
                    <button class="text-slate-500 hover:text-brand transition text-xl hidden sm:block"><i class="fa-regular fa-user"></i></button>
                    <button class="text-slate-500 hover:text-brand transition text-xl relative">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span class="absolute -top-2 -right-2 bg-accent text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center border-2 border-white shadow-sm">3</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <!-- Hero Section -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-slate-100 rounded-[2rem] overflow-hidden relative shadow-xl">
                <div class="absolute inset-0 bg-gradient-to-r from-slate-900/90 via-slate-900/50 to-transparent z-10"></div>
                <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Hero Banner" class="w-full h-[500px] object-cover object-center absolute inset-0">
                
                <div class="relative z-20 h-[500px] flex items-center px-8 md:px-16 w-full max-w-2xl">
                    <div>
                        <span class="bg-accent text-white text-xs font-bold uppercase px-4 py-1.5 rounded-full mb-6 inline-block tracking-wider shadow-lg shadow-accent/40">Koleksi Musim Panas 2026</span>
                        <h1 class="text-5xl sm:text-6xl md:text-7xl font-black text-white leading-[1.1] mb-6 tracking-tight">Tampil Gaya<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400">Sepanjang Hari</span></h1>
                        <p class="text-slate-300 text-lg md:text-xl mb-10 max-w-lg leading-relaxed">Temukan gaya terbaikmu dengan koleksi pakaian eksklusif kami. Desain premium dengan kenyamanan maksimal.</p>
                        <a href="#" class="inline-flex items-center gap-3 bg-white text-brand font-bold px-8 py-4 rounded-full hover:bg-slate-100 hover:scale-105 transition-all shadow-xl text-lg group">
                            Belanja Sekarang <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Product Grid -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="flex justify-between items-end mb-10">
                <h2 class="text-3xl font-black tracking-tight">Produk Terlaris</h2>
                <a href="#" class="font-bold text-slate-500 hover:text-brand flex items-center gap-2 group transition">
                    Lihat Semua <i class="fa-solid fa-arrow-right-long group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-12">
                <!-- Product 1 -->
                <div class="group relative cursor-pointer">
                    <div class="w-full aspect-[3/4] bg-slate-100 rounded-3xl overflow-hidden relative mb-5 shadow-sm group-hover:shadow-xl transition-shadow duration-500">
                        <img src="https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="T-Shirt" class="w-full h-full object-cover object-center group-hover:scale-110 transition duration-700">
                        <div class="absolute top-4 left-4 flex flex-col gap-2 z-10">
                            <span class="bg-white text-brand text-[10px] font-black tracking-widest px-3 py-1.5 rounded-md shadow-md uppercase">BARU</span>
                        </div>
                        <!-- Hover Overlay -->
                        <div class="absolute inset-0 bg-slate-900/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <button class="absolute bottom-5 left-1/2 -translate-x-1/2 bg-brand text-white w-[85%] py-3.5 rounded-2xl font-bold opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 hover:bg-slate-800 shadow-xl flex items-center justify-center gap-2 z-20">
                            <i class="fa-solid fa-cart-plus"></i> Masukkan Keranjang
                        </button>
                    </div>
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900 group-hover:text-accent transition truncate">Essential Premium T-Shirt</h3>
                            <p class="text-sm font-medium text-slate-500">Kapas Organik</p>
                        </div>
                    </div>
                    <p class="font-black text-xl text-slate-900 mt-2">Rp 250.000</p>
                </div>

                <!-- Product 2 -->
                <div class="group relative cursor-pointer">
                    <div class="w-full aspect-[3/4] bg-slate-100 rounded-3xl overflow-hidden relative mb-5 shadow-sm group-hover:shadow-xl transition-shadow duration-500">
                        <img src="https://images.unsplash.com/photo-1576995853123-5a10305d93c0?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Jeans" class="w-full h-full object-cover object-center group-hover:scale-110 transition duration-700">
                        <div class="absolute top-4 left-4 flex flex-col gap-2 z-10">
                            <span class="bg-accent text-white text-[10px] font-black tracking-widest px-3 py-1.5 rounded-md shadow-md uppercase">-20%</span>
                        </div>
                        <div class="absolute inset-0 bg-slate-900/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <button class="absolute bottom-5 left-1/2 -translate-x-1/2 bg-brand text-white w-[85%] py-3.5 rounded-2xl font-bold opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 hover:bg-slate-800 shadow-xl flex items-center justify-center gap-2 z-20">
                            <i class="fa-solid fa-cart-plus"></i> Masukkan Keranjang
                        </button>
                    </div>
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900 group-hover:text-accent transition truncate">Classic Denim Jeans</h3>
                            <p class="text-sm font-medium text-slate-500">Slim Fit</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <p class="font-black text-xl text-accent">Rp 480.000</p>
                        <p class="text-sm font-semibold text-slate-400 line-through">Rp 600.000</p>
                    </div>
                </div>

                <!-- Product 3 -->
                <div class="group relative cursor-pointer">
                    <div class="w-full aspect-[3/4] bg-slate-100 rounded-3xl overflow-hidden relative mb-5 shadow-sm group-hover:shadow-xl transition-shadow duration-500">
                        <img src="https://images.unsplash.com/photo-1591047139829-d91aecb6caea?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Jacket" class="w-full h-full object-cover object-center group-hover:scale-110 transition duration-700">
                        <div class="absolute inset-0 bg-slate-900/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <button class="absolute bottom-5 left-1/2 -translate-x-1/2 bg-brand text-white w-[85%] py-3.5 rounded-2xl font-bold opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 hover:bg-slate-800 shadow-xl flex items-center justify-center gap-2 z-20">
                            <i class="fa-solid fa-cart-plus"></i> Masukkan Keranjang
                        </button>
                    </div>
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900 group-hover:text-accent transition truncate">Waterproof Jacket</h3>
                            <p class="text-sm font-medium text-slate-500">Outerwear</p>
                        </div>
                    </div>
                    <p class="font-black text-xl text-slate-900 mt-2">Rp 750.000</p>
                </div>

                <!-- Product 4 -->
                <div class="group relative cursor-pointer">
                    <div class="w-full aspect-[3/4] bg-slate-100 rounded-3xl overflow-hidden relative mb-5 shadow-sm group-hover:shadow-xl transition-shadow duration-500">
                        <img src="https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Shoes" class="w-full h-full object-cover object-center group-hover:scale-110 transition duration-700">
                        <div class="absolute inset-0 bg-slate-900/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <button class="absolute bottom-5 left-1/2 -translate-x-1/2 bg-brand text-white w-[85%] py-3.5 rounded-2xl font-bold opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 hover:bg-slate-800 shadow-xl flex items-center justify-center gap-2 z-20">
                            <i class="fa-solid fa-cart-plus"></i> Masukkan Keranjang
                        </button>
                    </div>
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900 group-hover:text-accent transition truncate">Retro Sneakers</h3>
                            <p class="text-sm font-medium text-slate-500">Sepatu</p>
                        </div>
                    </div>
                    <p class="font-black text-xl text-slate-900 mt-2">Rp 890.000</p>
                </div>
            </div>
        </section>

        <!-- Banner -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 mb-10">
            <div class="bg-brand rounded-[2.5rem] p-10 md:p-16 text-center text-white flex flex-col items-center relative overflow-hidden shadow-2xl">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-accent/20 rounded-full blur-3xl"></div>
                <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl"></div>
                
                <i class="fa-solid fa-truck-fast text-5xl mb-6 text-accent relative z-10 drop-shadow-lg"></i>
                <h2 class="text-3xl md:text-5xl font-black mb-4 relative z-10 tracking-tight">Gratis Ongkir Seluruh Indonesia</h2>
                <p class="text-slate-300 text-lg mb-8 max-w-xl relative z-10">Minimal pembelanjaan Rp 500.000. Berlaku untuk semua produk tanpa syarat dan ketentuan tersembunyi.</p>
                <a href="#" class="bg-white text-brand font-bold px-10 py-4 rounded-full hover:bg-slate-100 transition hover:scale-105 shadow-xl relative z-10">Cek Info Detail</a>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-50 border-t border-slate-200 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center gap-2 mb-6">
                    <div class="w-8 h-8 bg-accent rounded-lg flex items-center justify-center text-white text-sm shadow-md">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                    <span class="text-xl font-black tracking-tighter uppercase text-slate-900">{$name}</span>
                </div>
                <p class="text-slate-500 max-w-sm mb-8 leading-relaxed font-medium">Toko baju online terpercaya dengan ribuan koleksi terbaru setiap minggunya. Kualitas premium, harga terjangkau.</p>
                <div class="flex gap-3">
                    <div class="w-12 h-8 bg-white shadow-sm border border-slate-200 rounded flex items-center justify-center text-brand"><i class="fa-brands fa-cc-visa text-xl"></i></div>
                    <div class="w-12 h-8 bg-white shadow-sm border border-slate-200 rounded flex items-center justify-center text-brand"><i class="fa-brands fa-cc-mastercard text-xl"></i></div>
                    <div class="w-12 h-8 bg-white shadow-sm border border-slate-200 rounded flex items-center justify-center text-brand"><i class="fa-brands fa-cc-paypal text-xl"></i></div>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 mb-6 text-lg">Bantuan</h4>
                <ul class="space-y-4 text-slate-500 font-medium">
                    <li><a href="#" class="hover:text-accent transition">Status Pesanan</a></li>
                    <li><a href="#" class="hover:text-accent transition">Pengembalian Barang</a></li>
                    <li><a href="#" class="hover:text-accent transition">Panduan Ukuran</a></li>
                    <li><a href="#" class="hover:text-accent transition">Hubungi CS Kami</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 mb-6 text-lg">Perusahaan</h4>
                <ul class="space-y-4 text-slate-500 font-medium">
                    <li><a href="#" class="hover:text-accent transition">Tentang Kami</a></li>
                    <li><a href="#" class="hover:text-accent transition">Karir</a></li>
                    <li><a href="#" class="hover:text-accent transition">Syarat & Ketentuan</a></li>
                    <li><a href="#" class="hover:text-accent transition">Kebijakan Privasi</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 border-t border-slate-200 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm font-semibold text-slate-500">
                &copy; 2026 {$name}. Hak Cipta Dilindungi.
            </p>
            <div class="flex gap-4">
                <a href="#" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-accent hover:border-accent transition shadow-sm"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-accent hover:border-accent transition shadow-sm"><i class="fa-brands fa-twitter"></i></a>
                <a href="#" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-accent hover:border-accent transition shadow-sm"><i class="fa-brands fa-facebook-f"></i></a>
            </div>
        </div>
    </footer>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { primary: '#4f46e5', sidebar: '#0f172a' }
                }
            }
        }
    </script>
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');</style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800 h-screen flex overflow-hidden selection:bg-primary selection:text-white">
    
    <!-- Sidebar -->
    <aside class="bg-sidebar w-72 flex-shrink-0 flex-col hidden lg:flex h-full shadow-2xl relative z-20">
        <div class="h-20 flex items-center px-8 border-b border-white/5 bg-black/10">
            <div class="flex items-center gap-3 text-white">
                <div class="w-9 h-9 bg-primary rounded-xl flex items-center justify-center shadow-lg shadow-primary/30">
                    <i class="fa-solid fa-bolt text-sm"></i>
                </div>
                <span class="text-xl font-bold tracking-tight">Admin<span class="text-slate-400 font-normal">Panel</span></span>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto py-8 flex flex-col gap-8 scrollbar-hide">
            <div class="px-6">
                <p class="px-3 text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-3">Overview</p>
                <nav class="space-y-1.5">
                    <a href="#" class="bg-primary/10 text-primary border border-primary/20 px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-semibold transition">
                        <i class="fa-solid fa-table-cells-large w-5 text-center"></i> Dashboard
                    </a>
                    <a href="#" class="text-slate-400 hover:bg-white/5 hover:text-white px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-medium transition">
                        <i class="fa-solid fa-chart-line w-5 text-center"></i> Analytics
                    </a>
                </nav>
            </div>
            
            <div class="px-6">
                <p class="px-3 text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-3">Management</p>
                <nav class="space-y-1.5">
                    <a href="#" class="text-slate-400 hover:bg-white/5 hover:text-white px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-medium transition flex justify-between">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-file-invoice-dollar w-5 text-center"></i> Orders
                        </div>
                        <span class="bg-primary text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow-sm">12</span>
                    </a>
                    <a href="#" class="text-slate-400 hover:bg-white/5 hover:text-white px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-medium transition">
                        <i class="fa-solid fa-box-open w-5 text-center"></i> Products
                    </a>
                    <a href="#" class="text-slate-400 hover:bg-white/5 hover:text-white px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-medium transition">
                        <i class="fa-solid fa-users w-5 text-center"></i> Customers
                    </a>
                </nav>
            </div>
            
            <div class="px-6 mt-auto pb-4">
                <nav class="space-y-1.5 border-t border-white/5 pt-6">
                    <a href="#" class="text-slate-400 hover:bg-white/5 hover:text-white px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-medium transition">
                        <i class="fa-solid fa-gear w-5 text-center"></i> Settings
                    </a>
                    <a href="#" class="text-slate-400 hover:bg-red-500/10 hover:text-red-400 px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-medium transition">
                        <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center"></i> Logout
                    </a>
                </nav>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col w-full h-full min-w-0 bg-slate-50">
        
        <!-- Top Header -->
        <header class="h-20 bg-white shadow-sm border-b border-slate-200 flex items-center justify-between px-6 lg:px-10 z-10 flex-shrink-0">
            <div class="flex items-center gap-6">
                <button class="lg:hidden text-slate-500 hover:text-slate-700 bg-slate-100 w-10 h-10 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <div class="relative hidden md:block">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" placeholder="Search orders, customers..." class="pl-11 pr-4 py-2.5 bg-slate-100 border-transparent rounded-xl text-sm font-medium focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none w-80 transition-all shadow-sm">
                </div>
            </div>
            
            <div class="flex items-center gap-5">
                <button class="relative text-slate-400 hover:text-primary transition w-10 h-10 rounded-xl hover:bg-primary/5 flex items-center justify-center">
                    <i class="fa-regular fa-bell text-xl"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                </button>
                <div class="h-8 w-px bg-slate-200 mx-1"></div>
                <div class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 p-1.5 rounded-xl transition pr-3">
                    <img src="https://ui-avatars.com/api/?name=Admin+User&background=4f46e5&color=fff&size=100" class="w-9 h-9 rounded-full shadow-sm border border-slate-200">
                    <div class="hidden md:block text-sm text-left">
                        <p class="font-bold text-slate-800 leading-none mb-1">Jane Doe</p>
                        <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Super Admin</p>
                    </div>
                    <i class="fa-solid fa-chevron-down text-xs text-slate-400 ml-2 hidden md:block"></i>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 lg:p-10">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Dashboard Overview</h1>
                    <p class="text-slate-500 mt-2 font-medium">Welcome back, Jane! Here's what's happening today.</p>
                </div>
                <div class="flex gap-3">
                    <button class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-4 py-2.5 rounded-xl text-sm font-semibold shadow-sm transition flex items-center gap-2">
                        <i class="fa-regular fa-calendar"></i> Last 30 Days <i class="fa-solid fa-chevron-down text-xs ml-1"></i>
                    </button>
                    <button class="bg-primary hover:bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-md shadow-primary/30 transition flex items-center gap-2">
                        <i class="fa-solid fa-download"></i> Export
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Card 1 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 relative overflow-hidden group hover:border-primary/30 transition">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl shadow-sm group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <span class="flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-100/80 px-2.5 py-1 rounded-lg">
                            <i class="fa-solid fa-arrow-trend-up"></i> +12.5%
                        </span>
                    </div>
                    <h3 class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-1">Total Users</h3>
                    <p class="text-3xl font-black text-slate-900">12,543</p>
                </div>
                
                <!-- Card 2 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 relative overflow-hidden group hover:border-primary/30 transition">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-xl shadow-sm group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-bag-shopping"></i>
                        </div>
                        <span class="flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-100/80 px-2.5 py-1 rounded-lg">
                            <i class="fa-solid fa-arrow-trend-up"></i> +8.2%
                        </span>
                    </div>
                    <h3 class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-1">Total Orders</h3>
                    <p class="text-3xl font-black text-slate-900">8,234</p>
                </div>
                
                <!-- Card 3 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 relative overflow-hidden group hover:border-primary/30 transition">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl shadow-sm group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-dollar-sign"></i>
                        </div>
                        <span class="flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-100/80 px-2.5 py-1 rounded-lg">
                            <i class="fa-solid fa-arrow-trend-up"></i> +24.1%
                        </span>
                    </div>
                    <h3 class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-1">Revenue</h3>
                    <p class="text-3xl font-black text-slate-900">$124.5K</p>
                </div>
                
                <!-- Card 4 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 relative overflow-hidden group hover:border-primary/30 transition">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl shadow-sm group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-eye"></i>
                        </div>
                        <span class="flex items-center gap-1.5 text-xs font-bold text-red-700 bg-red-100/80 px-2.5 py-1 rounded-lg">
                            <i class="fa-solid fa-arrow-trend-down"></i> -2.4%
                        </span>
                    </div>
                    <h3 class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-1">Page Views</h3>
                    <p class="text-3xl font-black text-slate-900">1.2M</p>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-6 border-b border-slate-200 flex justify-between items-center bg-white">
                    <h2 class="text-lg font-bold text-slate-900">Recent Transactions</h2>
                    <button class="text-primary text-sm font-bold hover:text-indigo-700 transition px-3 py-1.5 bg-primary/5 rounded-lg">View All Report</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 whitespace-nowrap">
                        <thead class="bg-slate-50/80 text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 rounded-tl-lg">Order ID</th>
                                <th class="px-6 py-4">Customer</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Amount</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr class="hover:bg-slate-50/80 transition group">
                                <td class="px-6 py-4 font-mono text-slate-900 font-medium">#ORD-001</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">JD</div>
                                        <span class="font-bold text-slate-900">John Doe</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-500">Oct 12, 2026</td>
                                <td class="px-6 py-4 font-bold text-slate-900">$129.00</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold border border-emerald-200 bg-emerald-50 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Completed
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="text-slate-400 hover:text-primary transition p-2 bg-slate-50 rounded-lg opacity-0 group-hover:opacity-100"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/80 transition group">
                                <td class="px-6 py-4 font-mono text-slate-900 font-medium">#ORD-002</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-xs">AS</div>
                                        <span class="font-bold text-slate-900">Alice Smith</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-500">Oct 12, 2026</td>
                                <td class="px-6 py-4 font-bold text-slate-900">$89.50</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold border border-amber-200 bg-amber-50 text-amber-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Processing
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="text-slate-400 hover:text-primary transition p-2 bg-slate-50 rounded-lg opacity-0 group-hover:opacity-100"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/80 transition group">
                                <td class="px-6 py-4 font-mono text-slate-900 font-medium">#ORD-003</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-bold text-xs">BJ</div>
                                        <span class="font-bold text-slate-900">Bob Johnson</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-500">Oct 11, 2026</td>
                                <td class="px-6 py-4 font-bold text-slate-900">$249.99</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold border border-red-200 bg-red-50 text-red-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Cancelled
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="text-slate-400 hover:text-primary transition p-2 bg-slate-50 rounded-lg opacity-0 group-hover:opacity-100"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <footer class="mt-10 text-center text-sm font-medium text-slate-400">
                &copy; 2026 {$name} Dashboard. Designed with TailwindCSS.
            </footer>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap');
        .bg-animated {
            background: linear-gradient(-45deg, #4f46e5, #ec4899, #8b5cf6, #06b6d4);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .glass-card { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .btn-link { background: rgba(255, 255, 255, 0.95); }
        .btn-link:hover { transform: scale(1.02) translateY(-2px); background: #ffffff; }
    </style>
</head>
<body class="bg-animated min-h-screen font-sans antialiased flex flex-col items-center py-10 px-4 sm:px-6 selection:bg-white selection:text-pink-500">
    
    <!-- Share Button -->
    <div class="absolute top-6 right-6">
        <button class="w-10 h-10 rounded-full glass-card text-white flex items-center justify-center hover:bg-white/30 transition shadow-lg backdrop-blur-md">
            <i class="fa-solid fa-arrow-up-right-from-square"></i>
        </button>
    </div>

    <div class="w-full max-w-[440px] glass-card rounded-[3rem] p-8 sm:p-10 shadow-2xl relative mt-8">
        
        <!-- Profile Info -->
        <div class="text-center mb-10 relative">
            <div class="w-32 h-32 mx-auto bg-white/20 p-2 rounded-full shadow-2xl mb-6 relative backdrop-blur-sm border border-white/30">
                <img src="https://ui-avatars.com/api/?name=Creator+Name&background=random&size=200" alt="Profile" class="w-full h-full object-cover rounded-full">
                <!-- Verified Badge -->
                <div class="absolute bottom-2 right-2 bg-blue-500 text-white w-8 h-8 rounded-full border-[3px] border-white flex items-center justify-center text-[10px] shadow-lg">
                    <i class="fa-solid fa-check"></i>
                </div>
            </div>
            <h1 class="text-3xl font-black text-white mb-2 tracking-tight">@{$name}</h1>
            <p class="text-white/90 text-sm font-medium leading-relaxed max-w-xs mx-auto mb-6">
                Digital Creator & Developer 💻✨<br>Helping you build better websites.
            </p>
            
            <!-- Social Icons -->
            <div class="flex justify-center gap-3">
                <a href="#" class="w-11 h-11 rounded-full glass-card text-white flex items-center justify-center text-lg hover:bg-white hover:text-pink-500 transition-all hover:scale-110 shadow-md"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" class="w-11 h-11 rounded-full glass-card text-white flex items-center justify-center text-lg hover:bg-white hover:text-blue-400 transition-all hover:scale-110 shadow-md"><i class="fa-brands fa-twitter"></i></a>
                <a href="#" class="w-11 h-11 rounded-full glass-card text-white flex items-center justify-center text-lg hover:bg-white hover:text-red-500 transition-all hover:scale-110 shadow-md"><i class="fa-brands fa-youtube"></i></a>
                <a href="#" class="w-11 h-11 rounded-full glass-card text-white flex items-center justify-center text-lg hover:bg-white hover:text-black transition-all hover:scale-110 shadow-md"><i class="fa-brands fa-tiktok"></i></a>
                <a href="#" class="w-11 h-11 rounded-full glass-card text-white flex items-center justify-center text-lg hover:bg-white hover:text-blue-600 transition-all hover:scale-110 shadow-md"><i class="fa-brands fa-linkedin-in"></i></a>
            </div>
        </div>

        <!-- Links -->
        <div class="space-y-4">
            <a href="#" class="btn-link block w-full text-slate-800 text-center font-bold py-4 px-6 rounded-2xl shadow-xl transition-all duration-300 relative group overflow-hidden border border-white/50">
                <div class="absolute inset-0 w-0 bg-gradient-to-r from-pink-500/10 to-transparent transition-all duration-500 group-hover:w-full"></div>
                <div class="relative flex items-center justify-between z-10">
                    <div class="w-10 h-10 rounded-xl bg-pink-100 text-pink-500 flex items-center justify-center text-lg"><i class="fa-solid fa-globe"></i></div>
                    <span class="flex-1 px-4 text-lg">My Personal Website</span>
                    <div class="w-6 text-slate-300 group-hover:text-pink-500 transition"><i class="fa-solid fa-chevron-right text-sm"></i></div>
                </div>
            </a>
            
            <a href="#" class="btn-link block w-full text-slate-800 text-center font-bold py-4 px-6 rounded-2xl shadow-xl transition-all duration-300 relative group overflow-hidden border border-white/50">
                <div class="absolute inset-0 w-0 bg-gradient-to-r from-red-500/10 to-transparent transition-all duration-500 group-hover:w-full"></div>
                <div class="relative flex items-center justify-between z-10">
                    <div class="w-10 h-10 rounded-xl bg-red-100 text-red-500 flex items-center justify-center text-lg"><i class="fa-brands fa-youtube"></i></div>
                    <span class="flex-1 px-4 text-lg">Latest YouTube Video</span>
                    <div class="w-6 text-slate-300 group-hover:text-red-500 transition"><i class="fa-solid fa-chevron-right text-sm"></i></div>
                </div>
            </a>
            
            <a href="#" class="btn-link block w-full text-slate-800 text-center font-bold py-4 px-6 rounded-2xl shadow-xl transition-all duration-300 relative group overflow-hidden border border-white/50">
                <div class="absolute inset-0 w-0 bg-gradient-to-r from-blue-500/10 to-transparent transition-all duration-500 group-hover:w-full"></div>
                <div class="relative flex items-center justify-between z-10">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-500 flex items-center justify-center text-lg"><i class="fa-brands fa-discord"></i></div>
                    <span class="flex-1 px-4 text-lg">Join Discord Community</span>
                    <div class="w-6 text-slate-300 group-hover:text-blue-500 transition"><i class="fa-solid fa-chevron-right text-sm"></i></div>
                </div>
            </a>
            
            <a href="#" class="btn-link block w-full text-slate-800 text-center font-bold py-4 px-6 rounded-2xl shadow-xl transition-all duration-300 relative group overflow-hidden border border-white/50">
                <div class="absolute inset-0 w-0 bg-gradient-to-r from-yellow-500/10 to-transparent transition-all duration-500 group-hover:w-full"></div>
                <div class="relative flex items-center justify-between z-10">
                    <div class="w-10 h-10 rounded-xl bg-yellow-100 text-yellow-500 flex items-center justify-center text-lg"><i class="fa-solid fa-mug-hot"></i></div>
                    <span class="flex-1 px-4 text-lg">Buy me a Coffee</span>
                    <div class="w-6 text-slate-300 group-hover:text-yellow-500 transition"><i class="fa-solid fa-chevron-right text-sm"></i></div>
                </div>
            </a>
        </div>
    </div>
    
    <div class="mt-8 text-white/80 text-sm font-bold tracking-wide">
        Powered by <a href="#" class="hover:text-white underline decoration-white/30 hover:decoration-white transition">Ryaze Hosting</a>
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
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: white; border-radius: 24px; padding: 60px 50px; text-align: center; max-width: 500px; box-shadow: 0 25px 50px rgba(0,0,0,0.15); position: relative; }
        .badge { background: linear-gradient(135deg, #667eea, #764ba2); color: white; font-size: 12px; font-weight: 700; padding: 6px 16px; border-radius: 50px; display: inline-block; margin-bottom: 24px; letter-spacing: 1px; text-transform: uppercase; }
        h1 { font-size: 2.5rem; font-weight: 900; color: #1a1a2e; margin-bottom: 16px; line-height: 1.2; }
        p { color: #6b7280; line-height: 1.8; margin-bottom: 32px; font-size: 1.1rem; }
        .btn { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 16px 40px; border-radius: 12px; text-decoration: none; font-weight: 700; display: inline-block; transition: all 0.3s ease; box-shadow: 0 10px 20px rgba(102,126,234,0.3); }
        .btn:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(102,126,234,0.4); }
        .domain { margin-top: 30px; font-size: 14px; color: #9ca3af; font-family: monospace; font-weight: 600; padding: 10px; background: #f3f4f6; border-radius: 8px; }
        .watermark { position: fixed; bottom: 20px; right: 20px; background: rgba(255,255,255,0.95); padding: 12px 20px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); font-size: 13px; font-weight: 500; backdrop-filter: blur(10px); }
        .watermark a { color: #667eea; text-decoration: none; font-weight: 700; transition: color 0.2s; }
        .watermark a:hover { color: #764ba2; }
    </style>
</head>
<body>
    <div class="card">
        <span class="badge">🚀 Ryaze Hosting</span>
        <h1>{$name}</h1>
        <p>Website Anda sudah live! Edit file <code>index.html</code> ini lewat <strong>File Manager</strong> untuk mulai kustomisasi tampilan.</p>
        <a href="#" class="btn">Mulai Mendesain →</a>
        <div class="domain">🌐 {$domain}</div>
    </div>
    <div class="watermark">
        Powered by <a href="https://ryaze.my.id" target="_blank">Ryaze.my.id</a>
    </div>
</body>
</html>
HTML
        );
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

    private function generatePhpReverseProxy(int $port, string $serverName): string
    {
        return <<<PHP
<?php
/**
 * Ryaze - Auto-generated PHP Reverse Proxy
 * Proxies traffic from OpenResty to the internal application daemon.
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
    echo "502 Bad Gateway - {$serverName} is down, crashed, or still starting up.";
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
    }
}
