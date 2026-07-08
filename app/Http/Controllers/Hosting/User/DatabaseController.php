<?php

namespace App\Http\Controllers\Hosting\User;

use App\Http\Controllers\Controller;
use App\Models\HostingDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Vinkla\Hashids\Facades\Hashids;

class DatabaseController extends Controller
{
    public function index()
    {
        $databases = HostingDatabase::where('user_id', Auth::id())->latest()->get();

        return view('pages.hosting.user.database.index', compact('databases'));
    }

    public function store(Request $request)
    {
        // 1. Validasi input manual dari user
        $request->validate([
            'db_name' => 'required|string|alpha_dash|max:15',
            'db_username' => 'required|string|alpha_dash|max:15',
            'db_password' => 'required|string|max:32',
        ], [
            'db_name.alpha_dash' => 'Nama database hanya boleh berisi huruf, angka, strip, dan underscore.',
            'db_username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip, dan underscore.',
        ]);

        // 2. Terapkan Prefix (ryz_{id}_) agar tidak bentrok antar user di server MySQL
        $prefix = 'ryz_'.Auth::id().'_';
        $cleanDbName = $prefix.strtolower(trim($request->db_name));
        $cleanUsername = $prefix.strtolower(trim($request->db_username));
        $dbPassword = $prefix.trim($request->db_password);

        // 3. Cek apakah nama database ini sudah ada (karena digabung prefix)
        if (HostingDatabase::where('db_name', $cleanDbName)->exists()) {
            return back()->with('error', 'Nama database "'.$cleanDbName.'" sudah digunakan.');
        }

        // Ambil konfigurasi dari .env
        $rootPass = config('services.panel_mysql.root_password');
        $mysqlHost = config('services.panel_mysql.host');

        if (! $rootPass) {
            return back()->with('error', 'Konfigurasi Root MySQL belum diatur oleh Admin.');
        }

        try {
            $pdo = new \PDO("mysql:host={$mysqlHost};port=3306", 'root', $rootPass);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // 1. Buat Database
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$cleanDbName`");

            // 2. Buat User — GUNAKAN PDO::quote() untuk mencegah SQL Injection pada password!
            $quotedPassword = $pdo->quote($dbPassword);
            $pdo->exec("CREATE USER IF NOT EXISTS '$cleanUsername'@'%' IDENTIFIED BY $quotedPassword");

            // 3. Grant akses
            $pdo->exec("GRANT ALL PRIVILEGES ON `$cleanDbName`.* TO '$cleanUsername'@'%'");

            // 4. Flush agar user langsung dikenali
            $pdo->exec('FLUSH PRIVILEGES');

        } catch (\PDOException $e) {
            return back()->with('error', 'Gagal membuat database: '.$e->getMessage());
        }

        // Simpan ke database portal Ryaze — encrypt password!
        HostingDatabase::create([
            'user_id' => Auth::id(),
            'db_name' => $cleanDbName,
            'db_username' => $cleanUsername,
            'db_password' => Crypt::encryptString($dbPassword),
            'host' => $mysqlHost,
        ]);

        return back()->with('success', 'Database '.$cleanDbName.' berhasil dibuat!');
    }

    public function destroy($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) {
            abort(404);
        }

        $database = HostingDatabase::where('user_id', Auth::id())->findOrFail($decoded[0]);

        $rootPass = env('PANEL_MYSQL_ROOT_PASSWORD');
        $mysqlHost = env('PANEL_MYSQL_HOST', '1Panel-mysql-KZAi');

        try {
            $pdo = new \PDO("mysql:host={$mysqlHost};port=3306", 'root', $rootPass);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $pdo->exec("DROP DATABASE IF EXISTS `$database->db_name`");
            $pdo->exec("DROP USER IF EXISTS '$database->db_username'@'%'");
            $pdo->exec('FLUSH PRIVILEGES');

        } catch (\PDOException $e) {
            \Log::error('Gagal hapus DB di server MySQL: '.$e->getMessage());
        }

        $database->delete();

        return back()->with('success', 'Database berhasil dihapus!');
    }

    public function pmaLogin($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) {
            abort(404);
        }

        $db = HostingDatabase::where('user_id', Auth::id())->findOrFail($decoded[0]);
        $pmaUrl = rtrim(config('services.pma.url', ''), '/');

        if (! $pmaUrl) {
            return back()->with('error', 'URL phpMyAdmin belum dikonfigurasi di .env (PMA_URL).');
        }

        try {
            // Step 1: GET phpMyAdmin untuk ambil token & cookie awal
            $ch = curl_init("{$pmaUrl}/index.php");
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => true,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_COOKIEFILE => '',   // enable cookie engine
                CURLOPT_COOKIEJAR => '',
            ]);
            $response1 = curl_exec($ch);
            $info1 = curl_getinfo($ch);

            // Ambil Set-Cookie dari response pertama
            $headerSize = $info1['header_size'];
            $headers1 = substr($response1, 0, $headerSize);
            $body1 = substr($response1, $headerSize);

            // Parse phpMyAdmin token dari form hidden input
            $token = '';
            if (preg_match('/name="token"\s+value="([^"]+)"/', $body1, $m)) {
                $token = $m[1];
            }
            // Fallback: cari di meta atau script
            if (! $token && preg_match('/token["\s:=]+([a-f0-9]{32})/', $body1, $m)) {
                $token = $m[1];
            }

            // Ambil cookies dari header pertama
            preg_match_all('/Set-Cookie:\s*([^;\r\n]+)/i', $headers1, $cookieMatches);
            $cookies = implode('; ', $cookieMatches[1]);

            // Step 2: POST login ke phpMyAdmin
            $postData = http_build_query([
                'pma_username' => $db->db_username,
                'pma_password' => $db->db_password,
                'server' => 1,
                'target' => 'index.php',
                'token' => $token,
            ]);

            curl_setopt_array($ch, [
                CURLOPT_URL => "{$pmaUrl}/index.php",
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_HEADER => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_HTTPHEADER => [
                    "Cookie: {$cookies}",
                    'Content-Type: application/x-www-form-urlencoded',
                    "Referer: {$pmaUrl}/index.php",
                ],
            ]);

            $response2 = curl_exec($ch);
            $info2 = curl_getinfo($ch);
            $headerSize2 = $info2['header_size'];
            $headers2 = substr($response2, 0, $headerSize2);

            curl_close($ch);

            // Ambil semua cookies dari response login
            preg_match_all('/Set-Cookie:\s*([^\r\n]+)/i', $headers2, $cookieMatches2);

            // Kirim cookies ke browser dan redirect ke phpMyAdmin
            $response = redirect("{$pmaUrl}/index.php?db={$db->db_name}");

            foreach ($cookieMatches2[1] as $cookieStr) {
                // Parse nama=nilai dan atribut
                $parts = array_map('trim', explode(';', $cookieStr));
                $nameVal = explode('=', array_shift($parts), 2);
                if (count($nameVal) < 2) {
                    continue;
                }

                [$cName, $cVal] = $nameVal;
                $cPath = '/';
                $cDomain = null;
                $cSecure = false;
                $cHttpOnly = false;

                foreach ($parts as $attr) {
                    $attrLower = strtolower($attr);
                    if (str_starts_with($attrLower, 'path=')) {
                        $cPath = substr($attr, 5);
                    }
                    if (str_starts_with($attrLower, 'domain=')) {
                        $cDomain = substr($attr, 7);
                    }
                    if ($attrLower === 'secure') {
                        $cSecure = true;
                    }
                    if ($attrLower === 'httponly') {
                        $cHttpOnly = true;
                    }
                }

                $response->withCookie(
                    cookie($cName, $cVal, 120, $cPath, $cDomain, $cSecure, $cHttpOnly)
                );
            }

            return $response;

        } catch (\Exception $e) {
            \Log::error('PMA auto-login gagal: '.$e->getMessage());

            // Fallback: buka phpMyAdmin biasa, user login manual
            return redirect("{$pmaUrl}/index.php")
                ->with('error', 'Auto-login gagal, silakan login manual.');
        }
    }

    public function export($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) {
            abort(404);
        }

        $db = HostingDatabase::where('user_id', Auth::id())->findOrFail($decoded[0]);

        $mysqlHost = config('services.panel_mysql.host');
        $rootPass = config('services.panel_mysql.root_password');

        $filename = $db->db_name . '_' . date('Ymd_His') . '.sql';
        $tempPath = storage_path('app/temp/' . $filename);

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        try {
            $dump = new \Ifsnop\Mysqldump\Mysqldump(
                "mysql:host={$mysqlHost};dbname={$db->db_name}",
                'root',
                $rootPass,
                ['add-drop-table' => true]
            );
            $dump->start($tempPath);
        } catch (\Exception $e) {
            \Log::error("mysqldump-php error: " . $e->getMessage());
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            return back()->with('error', 'Gagal mengekspor database: ' . $e->getMessage());
        }

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }

    public function import(Request $request, $hashid)
    {
        $request->validate([
            'sql_file' => 'required|file|max:51200', // max 50MB
        ]);

        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) {
            abort(404);
        }

        $db = HostingDatabase::where('user_id', Auth::id())->findOrFail($decoded[0]);

        $file = $request->file('sql_file');
        
        // Simple validation for sql file
        if ($file->getClientOriginalExtension() !== 'sql' && $file->getClientOriginalExtension() !== 'txt') {
            return back()->with('error', 'File harus berupa .sql atau .txt');
        }

        $mysqlHost = config('services.panel_mysql.host');
        $rootPass = config('services.panel_mysql.root_password');

        $tempPath = $file->path();

        try {
            $pdo = new \PDO("mysql:host={$mysqlHost};dbname={$db->db_name}", 'root', $rootPass);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            $sql = file_get_contents($tempPath);
            // Non-prepared statement is needed to execute multiple queries at once
            $pdo->exec($sql);
        } catch (\Exception $e) {
            \Log::error("mysql import error: " . $e->getMessage());
            return back()->with('error', 'Gagal mengimpor database: ' . $e->getMessage());
        }

        return back()->with('success', 'Database berhasil diimpor!');
    }
}
