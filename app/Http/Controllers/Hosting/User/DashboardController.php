<?php

namespace App\Http\Controllers\Hosting\User;

use App\Http\Controllers\Controller;
use App\Jobs\AutoDeployProject;
use App\Models\HostingBilling;
use App\Models\HostingProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;

class DashboardController extends Controller
{
    /**
     * File sistem yang tidak boleh dimodifikasi/dihapus oleh user.
     */
    private array $protectedFiles = ['.suspended', '.htaccess', '.user.ini', '.maintenance', '.rate_limit'];

    /**
     * Ekstensi file yang diblokir dari upload (mencegah web shell).
     */
    private array $blockedExtensions = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'php8', 'phps', 'phar',
        'cgi', 'pl', 'py', 'pyc', 'sh', 'bash', 'bat', 'cmd', 'com', 'exe',
        'asp', 'aspx', 'jsp', 'jspx', 'war', 'ear',
    ];

    /**
     * Command prefix yang diizinkan di web terminal.
     */
    private array $allowedCommands = [
        'ls', 'cat', 'head', 'tail', 'wc', 'grep', 'find', 'echo', 'pwd', 'whoami', 'date',
        'php', 'composer', 'npm', 'npx', 'node', 'python3', 'pip', 'pip3',
        'mkdir', 'touch', 'cp', 'mv', 'rm', 'git', 'curl', 'apk', 'source', 'chmod', 'clear', 'chown', 'sudo', 'nano'
    ];

    /**
     * Pola berbahaya yang diblokir di terminal (regex).
     */
    private array $blockedPatterns = [
        '/\.\.\//',                    // directory traversal
        '/\/etc\//',                   // system config access
        '/\/root\//',                  // root home
        '/\/var\/(?!www)/',            // /var (kecuali /var/www)
        '/\/proc\//',                  // proc filesystem
        '/\bsudo\b/',                  // privilege escalation
        '/\bsu\s/',                    // switch user
        '/\brm\s+-rf\s+\/(?!\S)/',     // rm -rf /
        '/\bwget\b/',                  // download executables
        '/\bcurl\b.*\|.*\bsh\b/',      // curl pipe to shell
        '/\bnc\b|\bnetcat\b/',         // reverse shell
        '/\beval\b/',                  // eval execution
        '/\$\(/',                      // command substitution
        '/`[^`]+`/',                   // backtick execution
        '/\bexport\b/',               // env manipulation
        '/\benv\b/',                  // env variables dump
        '/\bpasswd\b/',               // password file
        '/\bshadow\b/',               // shadow file
        '/\bcrontab\b/',              // cron manipulation
        '/\bkill\b/',                 // process kill
        '/\bkillall\b/',              // kill all processes
        '/\breboot\b/',               // system reboot
        '/\bshutdown\b/',             // system shutdown
        '/\bmysql\b/',                // direct mysql access
        '/\bsqlite3\b/',              // direct sqlite access
        '/\bpsql\b/',                 // direct postgres access
        '/\bphp\s+-r\b/',             // php inline code execution
        '/\bpython3\s+-c\b/',         // python inline code execution
        '/\bnode\s+-e\b/',            // node inline code execution
        '/\btinker\b/',               // php artisan tinker (interactive)
        '/\bssh\b/',                  // ssh connections
        '/\bftp\b/',                  // ftp connections
        '/\bscp\b/',                  // scp file transfer
        '/\brsync\b/',                // rsync file transfer
    ];
    // Menampilkan halaman dashboard hosting klien
    public function index()
    {
        // 1. Ambil SEMUA project untuk menghitung statistik yang akurat
        $allProjects = HostingProject::where('user_id', Auth::id())->latest()->get();

        // 2. Menghitung statistik berdasarkan KESELURUHAN data user
        $stats = [
            'active' => $allProjects->where('status', 'active')->count(),
            'unpaid' => $allProjects->where('status', 'unpaid')->count(),
            'tickets' => 0,
        ];

        // 3. Potong (Limit) hanya ambil 5 teratas untuk ditampilkan di tabel
        $projects = $allProjects->take(5);

        return view('pages.hosting.user.index', compact('projects', 'stats'));
    }

    // Menampilkan form deploy baru
    public function create()
    {
        return view('pages.hosting.user.create');
    }

    // Menampilkan daftar project
    public function projects()
    {
        $projects = HostingProject::where('user_id', Auth::id())->latest()->get();

        return view('pages.hosting.user.project', compact('projects'));
    }

    // Menampilkan halaman dokumentasi
    public function docs()
    {
        return view('pages.hosting.user.docs');
    }

    // Daftar template yang tersedia beserta metadata-nya
    private array $availableTemplates = [
        'html_landing'        => ['framework' => 'html'],
        'php_basic'           => ['framework' => 'php'],
        'laravel_starter'     => ['framework' => 'laravel'],
        'laravel_starter_10'  => ['framework' => 'laravel'],
        'laravel_starter_11'  => ['framework' => 'laravel'],
        'laravel_starter_12'  => ['framework' => 'laravel'],
        'laravel_starter_13'  => ['framework' => 'laravel'],
        'react_starter'       => ['framework' => 'react'],
        'nextjs_starter'      => ['framework' => 'nextjs'],
        'node_express'        => ['framework' => 'node'],
    ];

    // Memproses data dan memulai Deploy Otomatis
    public function store(Request $request)
    {
        $sourceType = $request->input('source_type', 'repo');
        \Illuminate\Support\Facades\Log::info('Store method called', [
            'source_type' => $sourceType,
            'all_input' => $request->all()
        ]);

        if ($sourceType === 'template') {
            // ── Mode Template ──────────────────────────────────────────────
            $request->validate([
                'template_key' => 'required|in:' . implode(',', array_keys($this->availableTemplates)),
                'project_name' => 'required|string|max:50|unique:hosting_projects,project_name',
            ]);

            $templateKey = $request->input('template_key');
            $template    = $this->availableTemplates[$templateKey];
            // Simpan key template di repo_source dengan prefix 'template:'
            // AutoDeploy akan membaca ini dan generate file langsung tanpa clone
            $repoSource  = 'template:' . $templateKey;
            $branch      = 'main';
            $framework   = $template['framework'];
        } else {
            // ── Mode Repository ────────────────────────────────────────────
            $request->validate([
                'repo_source'  => 'required|url',
                'project_name' => 'required|string|max:50|unique:hosting_projects,project_name',
                'framework'    => 'required|in:react,nextjs,python,html,laravel,node,php',
                'branch'       => 'required|string|max:50',
            ]);

            $repoSource = $request->input('repo_source');
            $branch     = $request->input('branch');
            $framework  = $request->input('framework');
        }

        $subdomain = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->project_name)));

        $user = Auth::user();
        $hasSubscription = $user->hasActiveHostingSubscription();
        $voucherMessage = null;
        $voucherFinalPrice = null;

        if (!$hasSubscription && $request->filled('voucher_code')) {
            $voucher = \App\Models\Voucher::where('code', strtoupper(trim($request->voucher_code)))->first();
            
            if (!$voucher || !$voucher->isValid()) {
                return back()->withInput()->with('error', 'Kode voucher tidak valid, kuota habis, atau sudah tidak berlaku.');
            }
            
            $basePrice = 10000;
            $voucherFinalPrice = $basePrice - $voucher->calculateDiscount($basePrice);
            
            $voucher->increment('uses');
            
            if ($voucherFinalPrice <= 0) {
                // Voucher 100% Gratis, langsung buat billing aktif
                \App\Models\HostingBilling::create([
                    'user_id' => $user->id,
                    'hosting_project_id' => null,
                    'plan_name' => 'Bulanan Rp 10.000',
                    'amount' => 0,
                    'billing_cycle' => 'monthly',
                    'status' => 'active',
                    'next_due_date' => now()->addMonth(),
                ]);
                $hasSubscription = true;
                $voucherMessage = 'Voucher berhasil digunakan! Langganan Anda aktif secara gratis untuk 1 bulan.';
                
                // Hapus invoice lama jika ada, karena sudah tercover voucher 100%
                \App\Models\HostingPayment::where('user_id', $user->id)
                    ->where('invoice_number', 'like', 'HST-INV-%')
                    ->where('status', 'unpaid')
                    ->delete();
                    
                // Buat invoice lunas untuk riwayat
                \App\Models\HostingPayment::create([
                    'user_id' => $user->id,
                    'hosting_project_id' => null,
                    'invoice_number' => 'HST-INV-'. strtoupper(uniqid()),
                    'amount' => 0,
                    'status' => 'paid',
                    'payment_method' => 'Voucher',
                    'paid_at' => now(),
                ]);
            } else {
                $voucherMessage = 'Voucher berhasil digunakan! Anda mendapatkan potongan harga.';
            }
        }

        $project = HostingProject::create([
            'user_id'      => $user->id,
            'project_name' => $request->project_name,
            'framework'    => $framework,
            'repo_source'  => $repoSource,
            'branch'       => $branch,
            'source_type'  => $sourceType,
            'ryaze_domain' => $subdomain.'.ryaze.my.id',
            'status'       => $hasSubscription ? 'building' : 'unpaid',
        ]);

        \Illuminate\Support\Facades\Log::info('Project created (' . ($hasSubscription ? 'Building' : 'Unpaid') . ')', [
            'id' => $project->id,
            'source_type' => $project->source_type,
            'repo_source' => $project->repo_source
        ]);

        if ($hasSubscription) {
            $isTemplate = $sourceType === 'template';
            $project->deployments()->create([
                'status'     => 'queued',
                'build_logs' => $isTemplate
                    ? "> Memulai deploy dari Template...\n> Mengambil template starter code..."
                    : "> Memulai proses Deploy awal...\n> Mengambil repository...",
            ]);
            AutoDeployProject::dispatch($project);
            
            $user->notify(new \App\Notifications\SystemNotification('Project Hosting Anda berhasil dibuat dan proses deployment telah dimulai.', 'info'));
            
            $successMsg = 'Project berhasil dibuat dan sedang dalam proses deployment!';
            if ($voucherMessage) $successMsg = $voucherMessage . ' ' . $successMsg;
            
            return redirect()->route('user_hosting.show', $project->hashid)->with('success', $successMsg);
        } else {
            // Cek apakah user sudah punya tagihan langganan yang belum dibayar
            $existingInvoice = \App\Models\HostingPayment::where('user_id', $user->id)
                ->where('invoice_number', 'like', 'HST-INV-%')
                ->where('status', 'unpaid')
                ->first();

            $invoiceAmount = isset($voucherFinalPrice) ? $voucherFinalPrice : 10000;

            if (!$existingInvoice) {
                \App\Models\HostingPayment::create([
                    'user_id' => $user->id,
                    'hosting_project_id' => null, // Invoice langganan akun
                    'invoice_number' => 'HST-INV-'. strtoupper(uniqid()),
                    'amount' => $invoiceAmount,
                    'status' => 'unpaid',
                ]);
            } else {
                if (isset($voucherFinalPrice) && $existingInvoice->amount != $invoiceAmount) {
                    // Update invoice yang sudah ada dengan harga baru dan generate nomor invoice baru agar sistem payment (misal Pakasir) tidak duplikat
                    $existingInvoice->update([
                        'amount' => $invoiceAmount,
                        'invoice_number' => 'HST-INV-'. strtoupper(uniqid())
                    ]);
                }
            }

            $user->notify(new \App\Notifications\SystemNotification('Project Hosting Anda berhasil dibuat. Silakan lakukan pembayaran tagihan langganan agar deployment dapat dimulai.', 'info'));
            
            $successMsg = 'Project berhasil dibuat. Silakan lakukan pembayaran langganan akun hosting Anda!';
            if ($voucherMessage) $successMsg = $voucherMessage . ' ' . $successMsg;
            
            return redirect()->route('user_hosting.show', $project->hashid)->with('success', $successMsg);
        }
    }

    public function show($hashed_id)
    {
        $project = $this->getValidProject($hashed_id, true);

        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);
        $projectDir = "/www/sites/hosting_clients/{$subdomain}";

        // Membaca file .env
        $envPath = $projectDir.'/.env';
        $envContent = '';
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);
        }

        return view('pages.hosting.user.show', compact('project', 'envContent'));
    }

    public function ideChat(Request $request, $hashid)
    {
        $project = $this->getValidProject($hashid);
        $message = $request->input('message');
        $context = $request->input('context'); // Current editing file context

        if (empty($message)) {
            return response()->json(['error' => 'Pesan tidak boleh kosong.'], 400);
        }

        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);
        $projectDir = "/www/sites/hosting_clients/{$subdomain}";
        $envPath = $projectDir . '/.env';
        $userApiKey = null;
        if (file_exists($envPath)) {
            $envLines = explode("\n", file_get_contents($envPath));
            foreach ($envLines as $line) {
                if (str_starts_with(trim($line), 'GROQ_API_KEY=')) {
                    $userApiKey = trim(explode('=', $line, 2)[1]);
                    $userApiKey = trim($userApiKey, "\"'");
                    break;
                }
            }
        }

        $groqApiKey = $userApiKey ?: env('GROQ_API_KEY');
        if (empty($groqApiKey)) {
            return response()->json(['error' => 'GROQ_API_KEY belum dikonfigurasi di server.'], 500);
        }

        $systemPrompt = "Kamu adalah Ryaze AI v1.0, asisten koding cerdas yang terintegrasi di dalam IDE Ryaze Hosting. Balas dalam bahasa Indonesia dengan gaya profesional, singkat, dan tepat sasaran. Jika pengguna menyertakan konteks kodenya, berikan analisis atau saran berdasarkan kode tersebut.\n\nJIKA PENGGUNA MEMINTA KAMU UNTUK MERUBAH ATAU MEMPERBAIKI KESELURUHAN KODE SECARA OTOMATIS (misal: 'perbaiki file ini', 'tulis ulang'), maka kamu WAJIB mengembalikan keseluruhan kode baru di dalam blok berikut:\n<<REPLACE_ALL>>\n[kode baru di sini]\n<<END_REPLACE>>\n\nJika pengguna hanya bertanya atau meminta cuplikan kode sebagian, gunakan markdown code block biasa (```).";
        
        $userMessage = $message;
        if (!empty($context)) {
            $userMessage = "Konteks file yang sedang saya buka:\n```\n" . $context . "\n```\n\nPertanyaan saya:\n" . $message;
        }

        try {
            $response = Http::withToken($groqApiKey)
                ->timeout(15)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.1-8b-instant',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['choices'][0]['message']['content'] ?? 'Tidak ada respons dari AI.';
                // Konversi markdown sederhana ke HTML bisa dilakukan di frontend, atau kita kembalikan plain markdown.
                return response()->json(['reply' => $reply]);
            } else {
                Log::error('Groq API Error: ' . $response->body());
                return response()->json(['error' => 'API Ryaze AI sedang bermasalah. Coba lagi nanti.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Groq Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan sistem saat menghubungi AI.'], 500);
        }
    }

    public function ideSearch(Request $request, $hashid)
    {
        $project = $this->getValidProject($hashid);
        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);
        $projectRootDir = realpath("/www/sites/hosting_clients/{$subdomain}");

        $query = $request->input('query', '');
        $matchCase = filter_var($request->input('matchCase', false), FILTER_VALIDATE_BOOLEAN);

        if (empty($query)) {
            return response()->json(['results' => []]);
        }

        if (strlen($query) > 100) {
            $query = substr($query, 0, 100);
        }

        $caseFlag = $matchCase ? '' : '-i';
        $escapedQuery = escapeshellarg($query);
        $escapedDir = escapeshellarg($projectRootDir);

        // Execute grep command to search text inside project directory, ignoring common big folders
        $cmd = "grep -rIn {$caseFlag} --exclude-dir={node_modules,vendor,.git,storage,.next} {$escapedQuery} {$escapedDir} | head -n 50";
        $output = shell_exec($cmd);

        $results = [];
        if ($output) {
            $lines = explode("\n", trim($output));
            foreach ($lines as $line) {
                if (empty($line)) continue;
                // Parse grep output: /full/path:line:content
                $parts = explode(':', $line, 3);
                if (count($parts) >= 3) {
                    $fullPath = $parts[0];
                    $lineNumber = $parts[1];
                    $content = $parts[2];
                    
                    // Ensure the result is within project scope
                    if (strpos($fullPath, $projectRootDir) === 0) {
                        $relativePath = str_replace($projectRootDir . '/', '', $fullPath);
                        $results[] = [
                            'path' => $relativePath,
                            'line' => $lineNumber,
                            'content' => mb_strimwidth(trim($content), 0, 120, '...')
                        ];
                    }
                }
            }
        }

        return response()->json(['results' => $results]);
    }

    public function ideGitStatus(Request $request, $hashid)
    {
        $project = $this->getValidProject($hashid);
        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);
        $projectRootDir = realpath("/www/sites/hosting_clients/{$subdomain}");

        // Check if git is initialized
        if (!is_dir($projectRootDir . '/.git')) {
            return response()->json(['error' => 'Git repository belum diinisialisasi di project ini.']);
        }

        $cmd = "cd " . escapeshellarg($projectRootDir) . " && git status -s";
        $output = shell_exec($cmd);

        $changes = [];
        if ($output) {
            $lines = explode("\n", trim($output));
            foreach ($lines as $line) {
                if (empty($line)) continue;
                $status = substr($line, 0, 2);
                $file = trim(substr($line, 2));
                $changes[] = [
                    'status' => trim($status),
                    'file' => $file
                ];
            }
        }

        return response()->json(['changes' => $changes]);
    }

    public function ideGitCommit(Request $request, $hashid)
    {
        $project = $this->getValidProject($hashid);
        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);
        $projectRootDir = realpath("/www/sites/hosting_clients/{$subdomain}");

        $msg = $request->input('message', 'Update');
        $msg = escapeshellarg($msg);

        $cmd = "cd " . escapeshellarg($projectRootDir) . " && git add . && git commit -m {$msg} 2>&1";
        $output = shell_exec($cmd);

        return response()->json(['message' => 'Commit berhasil', 'output' => $output]);
    }

    public function ideGitPull(Request $request, $hashid)
    {
        $project = $this->getValidProject($hashid);
        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);
        $projectRootDir = realpath("/www/sites/hosting_clients/{$subdomain}");

        $cmd = "cd " . escapeshellarg($projectRootDir) . " && git pull 2>&1";
        $output = shell_exec($cmd);

        return response()->json(['message' => 'Pull berhasil (Cek log untuk detail)', 'output' => $output]);
    }

    public function ideGitPush(Request $request, $hashid)
    {
        $project = $this->getValidProject($hashid);
        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);
        $projectRootDir = realpath("/www/sites/hosting_clients/{$subdomain}");

        $cmd = "cd " . escapeshellarg($projectRootDir) . " && git push 2>&1";
        $output = shell_exec($cmd);

        return response()->json(['message' => 'Push dijalankan (Cek log untuk detail)', 'output' => $output]);
    }

    // 2. Method API untuk navigasi folder
    public function getFiles(Request $request, $hashid)
    {
        $project = $this->getValidProject($hashid);
        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);
        $projectRootDir = realpath("/www/sites/hosting_clients/{$subdomain}");
        $requestPath = trim($request->input('path', ''), '/');

        $targetDir = $projectRootDir;
        if (! empty($requestPath)) {
            $targetDir = realpath($projectRootDir.'/'.$requestPath);
        }

        if ($targetDir === false || strpos($targetDir, $projectRootDir) !== 0) {
            return response()->json(['error' => 'Akses ditolak! Anda mencoba keluar dari root direktori.'], 403);
        }

        if (! is_dir($targetDir)) {
            return response()->json(['error' => 'Direktori tidak ditemukan.'], 404);
        }

        $items = scandir($targetDir);
        $directories = [];
        $files = [];

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $fullPath = $targetDir.'/'.$item;
            $isDir = is_dir($fullPath);

            $info = [
                'name' => $item,
                'type' => $isDir ? 'dir' : 'file',
                'size' => $isDir ? '-' : $this->formatBytesCustom(filesize($fullPath)),
                'modified' => date('d M Y H:i', filemtime($fullPath)),
                'path' => ! empty($requestPath) ? $requestPath.'/'.$item : $item,
            ];

            if ($isDir) {
                $directories[] = $info;
            } else {
                $files[] = $info;
            }
        }

        usort($directories, fn ($a, $b) => strcmp($a['name'], $b['name']));
        usort($files, fn ($a, $b) => strcmp($a['name'], $b['name']));

        return response()->json([
            'current_path' => $requestPath,
            'items' => array_merge($directories, $files),
        ]);
    }

    // 3. BARU: Method untuk membaca isi file
    public function readFile(Request $request, $hashid)
    {
        $project = $this->getValidProject($hashid);
        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);
        $projectRootDir = realpath("/www/sites/hosting_clients/{$subdomain}");

        $requestPath = trim($request->input('path', ''), '/');
        $targetFile = realpath($projectRootDir.'/'.$requestPath);

        // Validasi Anti-Traversal & pastikan itu adalah file (bukan folder)
        if ($targetFile === false || strpos($targetFile, $projectRootDir) !== 0 || is_dir($targetFile)) {
            return response()->json(['error' => 'File tidak valid atau akses ditolak.'], 403);
        }

        return response()->json(['content' => file_get_contents($targetFile)]);
    }

    // 4. BARU: Method untuk menyimpan file yang diedit
    public function saveFile(Request $request, $hashid)
    {
        \Log::info("saveFile route hit! hashid: {$hashid}, path: " . $request->input('path'));
        $project = $this->getValidProject($hashid);
        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);
        $projectRootDir = realpath("/www/sites/hosting_clients/{$subdomain}");

        $requestPath = trim($request->input('path', ''), '/');
        $targetFile = realpath($projectRootDir.'/'.$requestPath);

        if ($targetFile === false || strpos($targetFile, $projectRootDir) !== 0 || is_dir($targetFile)) {
            return response()->json(['error' => 'File tidak valid atau akses ditolak.'], 403);
        }

        // ── PROTEKSI ──
        if (in_array(basename($targetFile), $this->protectedFiles)) {
            return response()->json(['error' => 'File sistem ini tidak dapat diubah.'], 403);
        }

        $newContent = $request->input('content', '');
        $oldSize = file_exists($targetFile) ? filesize($targetFile) : 0;
        $newSize = strlen($newContent);

        if ($newSize > $oldSize) {
            if (!$this->checkDiskQuota($project, $newSize - $oldSize)) {
                return response()->json(['error' => 'Penyimpanan Penuh! Kuota disk Anda sudah habis.'], 403);
            }
        }

        @chmod($targetFile, 0666);
        $result = @file_put_contents($targetFile, $newContent);

        if ($result === false) {
            return response()->json(['error' => 'Gagal menyimpan file. Cek permission Linux.'], 500);
        }

        return response()->json(['success' => true]);
    }

    // Helper
    private function formatBytesCustom($size, $precision = 2)
    {
        if ($size > 0) {
            $size = (int) $size;
            $base = log($size) / log(1024);
            $suffixes = [' bytes', ' KB', ' MB', ' GB', ' TB'];

            return round(pow(1024, $base - floor($base)), $precision).$suffixes[floor($base)];
        }

        return $size.' bytes';
    }

    // --- 1. DOWNLOAD FILE ---
    public function downloadItem(Request $request, $hashid)
    {
        $project = $this->getValidProject($hashid);
        $targetPath = $this->getValidTargetPath($project, $request->input('path', ''));

        if (! $targetPath || is_dir($targetPath)) {
            abort(404);
        }

        return response()->download($targetPath);
    }

    // --- 2. HAPUS FILE / FOLDER ---
    public function deleteItem(Request $request, $hashid)
    {
        $project = $this->getValidProject($hashid);
        $targetPath = $this->getValidTargetPath($project, $request->input('path', ''));

        if (! $targetPath) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        // ── PROTEKSI FILE SISTEM ──────────────────────────────────────
        $basename = basename($targetPath);
        if (in_array($basename, $this->protectedFiles)) {
            return response()->json(['error' => 'File sistem ini tidak dapat dihapus.'], 403);
        }
        // ─────────────────────────────────────────────────────────────

        try {
            if (is_dir($targetPath)) {
                exec('rm -rf '.escapeshellarg($targetPath));
            } else {
                unlink($targetPath);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus: '.$e->getMessage()], 500);
        }
    }

    // --- 3. BUAT FILE / FOLDER BARU ---
    public function createItem(Request $request, $hashid)
    {
        $project = $this->getValidProject($hashid);
        $dirPath = $this->getValidTargetPath($project, $request->input('current_path', ''));

        if (! $dirPath || ! is_dir($dirPath)) {
            return response()->json(['error' => 'Direktori tujuan tidak valid.'], 403);
        }

        $type = $request->input('type'); // 'file' atau 'dir'
        $name = preg_replace('/[^a-zA-Z0-9_\.-]/', '', $request->input('name')); // Bersihkan nama file
        $targetPath = $dirPath.'/'.$name;

        if (file_exists($targetPath)) {
            return response()->json(['error' => 'Nama sudah digunakan.'], 400);
        }

        if (!$this->checkDiskQuota($project, 0)) {
            return response()->json(['error' => 'Penyimpanan Penuh! Kuota disk Anda sudah habis.'], 403);
        }

        if ($type === 'dir') {
            mkdir($targetPath, 0755);
        } else {
            touch($targetPath);
            chmod($targetPath, 0666);
        }

        return response()->json(['success' => true]);
    }

    // --- 4. UPLOAD FILE --- (HARDENED)
    public function uploadFile(Request $request, $hashid)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $project = $this->getValidProject($hashid);
        $dirPath = $this->getValidTargetPath($project, $request->input('current_path', ''));

        if (! $dirPath || ! is_dir($dirPath)) {
            return response()->json(['error' => 'Direktori tujuan tidak valid.'], 403);
        }

        $file = $request->file('file');

        if (!$this->checkDiskQuota($project, $file->getSize())) {
            return response()->json(['error' => 'Penyimpanan Penuh! Kuota disk Anda tidak cukup untuk mengupload file ini.'], 403);
        }

        // ════════ SECURITY: Block ekstensi berbahaya ════════
        $extension = strtolower($file->getClientOriginalExtension());
        if (in_array($extension, $this->blockedExtensions, true)) {
            return response()->json(['error' => "Tipe file '.{$extension}' tidak diizinkan demi keamanan server."], 403);
        }

        // Double-check MIME type untuk PHP files yang disamarkan
        $mimeType = $file->getMimeType();
        if (str_contains($mimeType, 'php') || str_contains($mimeType, 'x-httpd')) {
            return response()->json(['error' => 'Tipe MIME file tidak diizinkan.'], 403);
        }

        // Sanitize filename (hapus karakter aneh)
        $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientOriginalName());
        $file->move($dirPath, $safeName);

        return response()->json(['success' => true]);
    }

    // --- HELPER UNTUK KEAMANAN (ANTI-TRAVERSAL) ---
    private function getValidProject($hashid, $withDeployments = false)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) {
            abort(404, 'Project tidak ditemukan.');
        }

        $query = HostingProject::query();

        if ($withDeployments) {
            $query->with(['deployments' => function ($q) {
                $q->latest();
            }]);
        }

        if (!in_array(Auth::user()->role, ['superadmin', 'admin_hosting'])) {
            $query->where('user_id', Auth::id());
        }

        return $query->findOrFail($decoded[0]);
    }

    private function getValidTargetPath($project, $requestPath)
    {
        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);
        $projectRootDir = realpath("/www/sites/hosting_clients/{$subdomain}");

        // Gabungkan path
        $fullPath = $projectRootDir.'/'.trim($requestPath, '/');

        // Pengecekan realpath untuk Anti-Directory Traversal
        $realTarget = realpath($fullPath);

        // Jika file belum ada (kasus Create File), realpath akan false.
        // Kita izinkan jika parent directory-nya valid.
        if ($realTarget === false) {
            $parentDir = realpath(dirname($fullPath));
            if ($parentDir === false || strpos($parentDir, $projectRootDir) !== 0) {
                return false;
            }

            return $fullPath;
        }

        if (strpos($realTarget, $projectRootDir) !== 0) {
            return false;
        }

        return $realTarget;
    }

    // Memperbarui file .env
    public function updateEnv(Request $request, $hashid)
    {
        $project = $this->getValidProject($hashid);
        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);

        $envPath = "/www/sites/hosting_clients/{$subdomain}/.env";
        $content = $request->input('env_content', '');

        try {
            // Coba paksakan write permission jika file ada
            if (file_exists($envPath)) {
                @chmod($envPath, 0666);
            }

            // Tulis isi textarea langsung ke file .env di server dengan peredam error (@)
            $result = @file_put_contents($envPath, $content);

            if ($result === false) {
                // Jika web server masih ditolak Linux (Permission Denied)
                return back()->with('error', 'Gagal menyimpan .env! Pastikan folder project memiliki permission www-data (chown).');
            }

            return back()->with('success', 'Environment variables berhasil disimpan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // Start Dev Server (React/Next.js/Vite)
    public function startDevServer($hashid)
    {
        $project = $this->getValidProject($hashid);

        // Only allow React/Next.js/Vue/Python frameworks
        if (!in_array($project->framework, ['react', 'nextjs', 'vue', 'python'])) {
            return back()->with('error', 'Dev Server hanya tersedia untuk React, Next.js, Vue, dan Python!');
        }

        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);
        $projectDir = "/www/sites/hosting_clients/{$subdomain}";

        // Cari port yang tersedia dengan start random (3000-4000) untuk mem-bypass DNS negative caching
        $port = null;
        $startPort = rand(3000, 4000);
        
        // Cari dari startPort ke 4000
        for ($p = $startPort; $p <= 4000; $p++) {
            $connection = @fsockopen('127.0.0.1', $p, $errCode, $errStr, 0.1);
            if (!is_resource($connection)) {
                $port = $p;
                break;
            }
            fclose($connection);
        }
        
        // Jika belum ketemu, cari dari 3000 ke startPort
        if (!$port) {
            for ($p = 3000; $p < $startPort; $p++) {
                $connection = @fsockopen('127.0.0.1', $p, $errCode, $errStr, 0.1);
                if (!is_resource($connection)) {
                    $port = $p;
                    break;
                }
                fclose($connection);
            }
        }

        if (!$port) {
            return back()->with('error', 'Tidak ada port yang tersedia!');
        }

        // Hapus process lama jika ada (cleanup)
        if ($project->dev_pid) {
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
            if ($isWindows) {
                if (is_numeric($project->dev_pid)) {
                    exec("taskkill /F /T /PID {$project->dev_pid} 2>nul");
                } else {
                    exec("pm2 delete \"{$project->dev_pid}\" 2>nul");
                }
            } else {
                if (is_numeric($project->dev_pid)) {
                    exec("docker exec -u root 1Panel-php8-aJQI sh -c 'kill -9 {$project->dev_pid} 2>/dev/null || true'");
                } else {
                    exec("docker exec -u root 1Panel-php8-aJQI sh -c 'pm2 delete \"{$project->dev_pid}\" 2>/dev/null || true'");
                }
            }
        }

        // Mulai dev server menggunakan PM2
        $appName = "dev{$project->id}";
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $winProjectDir = $isWindows ? substr(base_path(), 0, 2) . str_replace('/', '\\', $projectDir) : $projectDir;

        $pm2Wrapper = function($cmd) use ($isWindows, $winProjectDir, $projectDir) {
            if ($isWindows) {
                return "cd /D \"{$winProjectDir}\" && {$cmd} 2>&1";
            }
            // PHP is already running inside the container (e.g. 1Panel-php8-aJQI), so just run it directly.
            // We assume pm2 and npm/python are installed globally inside the PHP container.
            return "cd {$projectDir} && {$cmd} 2>&1";
        };

        if ($project->framework === 'react' || $project->framework === 'vue') {
            shell_exec($pm2Wrapper("pm2 start npm --name \"{$appName}\" -- run dev -- --port {$port}"));
        } elseif ($project->framework === 'nextjs') {
            shell_exec($pm2Wrapper("pm2 start npm --name \"{$appName}\" -- run dev -- -p {$port}"));
        } elseif ($project->framework === 'python') {
            $entrypoint = 'app.py';
            if (file_exists("{$projectDir}/main.py")) $entrypoint = 'main.py';
            elseif (file_exists("{$projectDir}/server.py")) $entrypoint = 'server.py';
            elseif (file_exists("{$projectDir}/wsgi.py")) $entrypoint = 'wsgi.py';

            $hasGunicorn = file_exists("{$projectDir}/venv/bin/gunicorn");
            if ($hasGunicorn) {
                $module = str_replace('.py', '', $entrypoint);
                shell_exec($pm2Wrapper("PORT={$port} pm2 start venv/bin/gunicorn --name \"{$appName}\" -- {$module}:app -b 127.0.0.1:{$port} --workers 2"));
            } else {
                shell_exec($pm2Wrapper("PORT={$port} FLASK_RUN_PORT={$port} pm2 start venv/bin/python --name \"{$appName}\" -- {$entrypoint}"));
            }
        }
        
        $pid = $appName; // PM2 name as identifier

        if (!$pid) {
            return back()->with('error', 'Gagal memulai Dev Server!');
        }

        // Generate PHP Reverse Proxy to route dev*.ryaze.my.id to the Dev Server Port
        // This solves the issue where Laragon/OpenResty serves the default production site instead of the dev server.
        $proxyScript = <<<PHP
<?php
// Auto-generated Reverse Proxy for Ryaze Dev Server
\$port = {$port};
\$hostHeader = \$_SERVER['HTTP_HOST'] ?? '';

if (preg_match('/^dev\d+\./', \$hostHeader)) {
    \$path = \$_SERVER['REQUEST_URI'];
    \$method = \$_SERVER['REQUEST_METHOD'];
    \$headers = getallheaders();

    \$url = "http://127.0.0.1:{\$port}{\$path}";
    \$ch = curl_init(\$url);
    curl_setopt(\$ch, CURLOPT_CUSTOMREQUEST, \$method);
    curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(\$ch, CURLOPT_HEADER, true);

    \$reqHeaders = [];
    foreach (\$headers as \$k => \$v) {
        if (strtolower(\$k) === 'host') continue;
        \$reqHeaders[] = "\$k: \$v";
    }
    curl_setopt(\$ch, CURLOPT_HTTPHEADER, \$reqHeaders);

    if (\$method === 'POST' || \$method === 'PUT' || \$method === 'PATCH') {
        curl_setopt(\$ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
    }

    \$response = curl_exec(\$ch);
    if (curl_errno(\$ch)) {
        http_response_code(502);
        echo "502 Bad Gateway - Dev Server is still starting up or crashed. Please refresh in a few seconds.";
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
    exit;
} else {
    // Fallback: serve production index.html
    if (file_exists(__DIR__ . '/index.html')) {
        // Simple mime-type mapping for static assets
        \$ext = pathinfo(\$_SERVER['REQUEST_URI'], PATHINFO_EXTENSION);
        if (\$ext === 'css') header('Content-Type: text/css');
        if (\$ext === 'js') header('Content-Type: application/javascript');
        
        echo file_get_contents(__DIR__ . '/index.html');
    } else {
        echo "Produksi belum di-build!";
    }
}
PHP;
        file_put_contents("{$projectDir}/index.php", $proxyScript);
        file_put_contents("{$projectDir}/.port", $port);

        // Update project
        $project->update([
            'dev_mode' => true,
            'dev_port' => $port,
            'dev_pid' => $pid
        ]);

        // Create Cloudflare DNS for Dev Server
        $zoneId = config('services.cloudflare.zone_id', env('CLOUDFLARE_ZONE_ID'));
        $apiToken = config('services.cloudflare.api_token', env('CLOUDFLARE_API_TOKEN'));
        $tunnelUrl = preg_replace('#^https?://#', '', rtrim(config('services.cloudflare.tunnel_url', env('CLOUDFLARE_TUNNEL_URL')), '/'));
        
        \Illuminate\Support\Facades\Log::info("CF Vars: zone=$zoneId, token=$apiToken, tunnel=$tunnelUrl");
        
        if ($zoneId && $apiToken && $tunnelUrl) {
            $domainName = "dev{$port}.ryaze.my.id";
            $existing = Http::withToken($apiToken)->get("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records", ['type' => 'CNAME', 'name' => $domainName]);
            if ($existing->successful() && empty($existing->json('result'))) {
                $resp = Http::withToken($apiToken)->post("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records", [
                    'type'    => 'CNAME',
                    'name'    => $domainName,
                    'content' => $tunnelUrl,
                    'proxied' => true,
                    'ttl'     => 1,
                ]);
                \Illuminate\Support\Facades\Log::info("Cloudflare DevServer DNS created: " . $resp->body());
            } else {
                \Illuminate\Support\Facades\Log::info("Cloudflare DevServer DNS exists or error: " . $existing->body());
            }
        }

        return back()->with('success', "Dev Server berhasil dimulai di port {$port}!");
    }

    // Stop Dev Server
    public function stopDevServer($hashid)
    {
        $project = $this->getValidProject($hashid);
        
        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);
        $projectDir = "/www/sites/hosting_clients/{$subdomain}";

        if ($project->dev_pid) {
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
            if ($isWindows) {
                if (is_numeric($project->dev_pid)) {
                    exec("taskkill /F /T /PID {$project->dev_pid} 2>nul");
                } else {
                    exec("pm2 delete \"{$project->dev_pid}\" 2>nul");
                }
            } else {
                if (is_numeric($project->dev_pid)) {
                    exec("kill -9 {$project->dev_pid} 2>/dev/null || true");
                } else {
                    exec("pm2 delete \"{$project->dev_pid}\" 2>/dev/null || true");
                }
            }
        }
        
        // Hapus proxy script saat dev server dimatikan
        if (file_exists("{$projectDir}/index.php")) {
            @unlink("{$projectDir}/index.php");
        }

        $devPort = $project->dev_port;

        $project->update([
            'dev_mode' => false,
            'dev_port' => null,
            'dev_pid' => null
        ]);

        // Hapus Cloudflare DNS for Dev Server
        if ($devPort) {
            $this->deleteCloudflareDNS("dev{$devPort}.ryaze.my.id");
        }

        return back()->with('success', 'Dev Server berhasil dihentikan!');
    }

    // Memproses Redeploy
    public function redeploy($hashid)
    {
        $project = $this->getValidProject($hashid);

        $project->update(['status' => 'building']);

        $project->deployments()->create([
            'status' => 'queued',
            'build_logs' => "> Memulai proses Redeploy manual...\n> Mengambil perubahan terbaru dari repository...",
        ]);

        AutoDeployProject::dispatch($project);

        return back()->with('success', 'Redeploy berhasil dimulai! Silakan tunggu beberapa saat.');
    }

    // Mengambil build log terakhir untuk polling AJAX
    public function buildLogs($hashid)
    {
        try {
            $project = $this->getValidProject($hashid, true);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Project tidak ditemukan.'], 404);
        }

        $deployment = $project->deployments->first();

        return response()->json([
            'build_logs' => $deployment?->build_logs ?? '',
            'status' => $project->status,
            'deployment_status' => $deployment?->status,
            'website_url' => 'https://'.$project->ryaze_domain,
            'last_updated' => $deployment?->updated_at?->toDateTimeString(),
        ]);
    }

    // Memproses perintah dari Web Terminal (HARDENED)
    public function terminal(Request $request, $hashid)
    {
        try {
            $project = $this->getValidProject($hashid);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Project tidak ditemukan.'], 404);
        }
        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);

        $projectDir = "/www/sites/hosting_clients/{$subdomain}";
        $command = trim($request->input('command', ''));

        if (empty($command)) {
            return response()->json(['output' => '', 'exit_code' => 0]);
        }

        // ════════ SECURITY: Command Whitelist ════════
        $firstWord = explode(' ', $command)[0];

        // Izinkan 'php artisan ...' sebagai satu command prefix
        if ($firstWord === 'php' && str_starts_with($command, 'php artisan')) {
            // allowed
        } elseif (! in_array($firstWord, $this->allowedCommands, true)) {
            return response()->json([
                'output' => "⛔ Command '{$firstWord}' tidak diizinkan.\nCommand yang diizinkan: ".implode(', ', $this->allowedCommands),
                'exit_code' => 1,
            ]);
        }

        // ════════ SECURITY: Dangerous Pattern Blacklist ════════
        foreach ($this->blockedPatterns as $pattern) {
            if (preg_match($pattern, $command)) {
                Log::warning('[TERMINAL_BLOCKED] User '.Auth::id()." attempted: {$command}");

                return response()->json([
                    'output' => '⛔ Command mengandung pola berbahaya dan diblokir demi keamanan server.',
                    'exit_code' => 1,
                ]);
            }
        }

        // ════════ SECURITY: Block chained/piped & redirection commands ════════
        if (preg_match('/[;&|><]/', $command)) {
            return response()->json([
                'output' => '⛔ Chaining command (;, &&, ||, |) dan Redirection (>, <) tidak diizinkan.',
                'exit_code' => 1,
            ]);
        }
        // ══════════════════════════════════════════════════════════

        // ════════ MANTRA ANTI-BLEEDING ════════
        $unsetEnv = 'unset APP_NAME APP_ENV APP_KEY APP_DEBUG APP_URL LOG_CHANNEL DB_CONNECTION DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD BROADCAST_DRIVER CACHE_DRIVER QUEUE_CONNECTION SESSION_DRIVER SESSION_LIFETIME REDIS_HOST REDIS_PASSWORD REDIS_PORT; ';

        $fullCommand = $unsetEnv.'cd '.escapeshellarg($projectDir).' && '.$command.' 2>&1';
        // ══════════════════════════════════════

        exec($fullCommand, $outputArray, $exitCode);
        $outputString = implode("\n", $outputArray);

        return response()->json([
            'output' => $outputString,
            'exit_code' => $exitCode,
        ]);
    }

    public function billingHistory()
    {
        // Mengambil semua invoice/tagihan milik user yang sedang login
        $billings = \App\Models\HostingPayment::where('user_id', Auth::id())
            ->where('invoice_number', 'like', 'HST-INV-%')
            ->latest()
            ->paginate(15);

        return view('pages.hosting.user.billing', compact('billings'));
    }

    public function deleteProject(Request $request, $hashid)
    {
        $project = $this->getValidProject($hashid);
        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);
        $projectDir = "/www/sites/hosting_clients/{$subdomain}";

        // 1. Hapus Record DNS Cloudflare
        $this->deleteCloudflareDNS($project->ryaze_domain);

        // 2. Hapus Folder Root
        if (is_dir($projectDir)) {
            exec('rm -rf '.escapeshellarg($projectDir));
        }

        // 3. Hapus Record Database
        $projectName = $project->project_name;
        $project->delete();

        return redirect()->route('user_hosting.projects')->with('success', "Project '{$projectName}' berhasil dihapus sepenuhnya.");
    }

    private function deleteCloudflareDNS($domainName)
    {
        $zoneId = config('services.cloudflare.zone_id', env('CLOUDFLARE_ZONE_ID'));
        $apiToken = config('services.cloudflare.api_token', env('CLOUDFLARE_API_TOKEN'));

        if (!$zoneId || !$apiToken) return;

        // Cari Record ID
        $response = Http::withToken($apiToken)
            ->get("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records", [
                'type' => 'CNAME',
                'name' => $domainName,
            ]);

        if ($response->successful() && ! empty($response->json('result'))) {
            $recordId = $response->json('result.0.id');
            // Hapus Record
            Http::withToken($apiToken)->delete("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records/{$recordId}");
        }
    }

    public function updateSettings(Request $request, $hashid)
    {
        $project = $this->getValidProject($hashid);
        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);
        $projectDir = "/www/sites/hosting_clients/{$subdomain}";

        // Ambil data checkbox
        $maintenanceMode = $request->has('maintenance_mode');
        $forceHttps = $request->has('force_https');
        $underAttack = $request->has('is_under_attack');

        // 1. Terapkan Maintenance Mode (Membuat file .maintenance untuk dibaca Nginx)
        $maintenanceFile = "{$projectDir}/.maintenance";
        if ($maintenanceMode) {
            // Buat file penanda
            file_put_contents($maintenanceFile, "MAINTENANCE MODE ACTIVE\nFile ini digunakan oleh server Nginx sebagai penanda bahwa Maintenance Mode sedang aktif. Tolong jangan dihapus manual.");
            @chmod($maintenanceFile, 0666);
        } else {
            // Hapus file penanda jika dinonaktifkan
            if (file_exists($maintenanceFile)) {
                @unlink($maintenanceFile);
            }
        }

        // 1.5. Terapkan Rate Limit / Under Attack Mode (Membuat file .rate_limit untuk dibaca Nginx)
        $rateLimitFile = "{$projectDir}/.rate_limit";
        if ($underAttack) {
            file_put_contents($rateLimitFile, "UNDER ATTACK MODE ACTIVE\nFile ini digunakan oleh server Nginx sebagai penanda bahwa perlindungan Rate Limiting sedang aktif. Tolong jangan dihapus manual.");
            @chmod($rateLimitFile, 0666);
        } else {
            if (file_exists($rateLimitFile)) {
                @unlink($rateLimitFile);
            }
        }

        // 2. Simpan konfigurasi ke Database
        $project->update([
            'maintenance_mode' => $maintenanceMode,
            'force_https' => $forceHttps,
            'is_under_attack' => $underAttack,
        ]);

        // Catat di Logs
        $project->deployments()->create([
            'status' => 'ready',
            'build_logs' => "> Pengaturan aplikasi diperbarui.\n> Maintenance Mode: ".($maintenanceMode ? 'ON' : 'OFF')."\n> Force HTTPS: ".($forceHttps ? 'ON' : 'OFF')."\n> Under Attack Mode: ".($underAttack ? 'ON' : 'OFF'),
        ]);

        return back()->with('success', 'Konfigurasi aplikasi berhasil diperbarui!');
    }

    private function checkDiskQuota($project, $additionalBytes = 0)
    {
        $subdomain = str_replace('.ryaze.my.id', '', $project->ryaze_domain);
        $projectRootDir = realpath("/www/sites/hosting_clients/{$subdomain}");

        if (!$projectRootDir || !is_dir($projectRootDir)) {
            return true;
        }

        $output = shell_exec("du -sb " . escapeshellarg($projectRootDir) . " 2>/dev/null");
        $currentBytes = 0;
        if ($output) {
            $parts = explode("\t", trim($output));
            if (isset($parts[0])) {
                $currentBytes = (int) $parts[0];
            }
        }

        $totalBytes = $currentBytes + $additionalBytes;
        $limitBytes = ($project->storage_limit_mb ?? 1024) * 1024 * 1024;

        if ($totalBytes > $limitBytes) {
            return false;
        }

        return true;
    }
}
