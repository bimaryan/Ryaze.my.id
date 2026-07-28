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
        $nosqlDatabases = \App\Models\HostingNosqlDatabase::where('user_id', Auth::id())->latest()->get();
        $pgsqlDatabases = \App\Models\HostingPgsqlDatabase::where('user_id', Auth::id())->latest()->get();
        
        return view('pages.hosting.user.database.index', compact('databases', 'nosqlDatabases', 'pgsqlDatabases'));
    }

    public function storeNosql(Request $request)
    {
        $existingDb = \App\Models\HostingNosqlDatabase::where('user_id', Auth::id())->first();
        $prefix = 'ryz_' . Auth::id() . '_';

        if ($existingDb) {
            $request->validate([
                'db_username' => 'required|string|max:15|regex:/^[A-Za-z0-9_]+$/',
            ]);
            $username = $prefix . $request->db_username;
            $password = \Illuminate\Support\Facades\Crypt::decryptString($existingDb->db_password);
        } else {
            $request->validate([
                'db_username' => 'required|string|max:15|regex:/^[A-Za-z0-9_]+$/',
                'db_password' => 'required|string|min:8'
            ]);
            $username = $prefix . $request->db_username;
            $password = $prefix . trim($request->db_password);
        }

        $redisHost = env('REDIS_HOST', '127.0.0.1');
        $redisPort = env('REDIS_PORT', 6379);

        // Jika Anda menggunakan Redis 6+ dengan ACL, Anda bisa menjalankan perintah ACL SETUSER di sini
        // $redis = new \Redis();
        // $redis->connect($redisHost, $redisPort);
        // $redis->auth(env('REDIS_PASSWORD'));
        // $redis->rawCommand('ACL', 'SETUSER', $username, 'on', '>'.$password, '~'.$username.'*', '+@all');

        \App\Models\HostingNosqlDatabase::create([
            'user_id' => Auth::id(),
            'nosql_type' => 'redis',
            'db_username' => $username, // Nama ACL user atau identitas
            'db_password' => \Illuminate\Support\Facades\Crypt::encryptString($password),
            'host' => $redisHost,
            'port' => $redisPort,
            'keyspace_prefix' => $username,
        ]);

        return back()->with('success', 'Database NoSQL (Redis) berhasil dibuat!');
    }

    public function destroyNosql($hashid)
    {
        $database = \App\Models\HostingNosqlDatabase::where('hashid', $hashid)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Di sini Anda bisa menambahkan logika ACL DELUSER jika diperlukan
        // $redis = new \Redis();
        // ...
        // $redis->rawCommand('ACL', 'DELUSER', $database->db_username);

        $database->delete();

        return back()->with('success', 'Database NoSQL (Redis) berhasil dihapus!');
    }

    public function pmaIndex()
    {
        $databases = HostingDatabase::where('user_id', Auth::id())->latest()->get();
        return view('pages.hosting.user.database.pma', compact('databases'));
    }

    public function store(Request $request)
    {
        $existingDb = HostingDatabase::where('user_id', Auth::id())->first();

        // 1. Validasi input manual dari user
        if ($existingDb) {
            $request->validate([
                'db_name' => 'required|string|alpha_dash|max:15',
            ], [
                'db_name.alpha_dash' => 'Nama database hanya boleh berisi huruf, angka, strip, dan underscore.',
            ]);

            $prefix = 'ryz_'.Auth::id().'_';
            $cleanDbName = $prefix.strtolower(trim($request->db_name));
            $cleanUsername = $existingDb->db_username;
            $dbPassword = $existingDb->db_password;
        } else {
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
        }

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
            
            // 2.1 Update password in case user already exists
            $pdo->exec("ALTER USER '$cleanUsername'@'%' IDENTIFIED BY $quotedPassword");

            // 3. Grant akses
            $pdo->exec("GRANT ALL PRIVILEGES ON `$cleanDbName`.* TO '$cleanUsername'@'%'");

            // 4. Flush agar user langsung dikenali
            $pdo->exec('FLUSH PRIVILEGES');

        } catch (\PDOException $e) {
            return back()->with('error', 'Gagal membuat database: '.$e->getMessage());
        }

        // Update password for other databases that might share this username
        HostingDatabase::where('db_username', $cleanUsername)->update([
            'db_password' => Crypt::encryptString($dbPassword),
        ]);

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
            
            // Check if there are other databases using this username
            $otherDatabasesUsingSameUsername = HostingDatabase::where('db_username', $database->db_username)
                ->where('id', '!=', $database->id)
                ->exists();
                
            if (!$otherDatabasesUsingSameUsername) {
                $pdo->exec("DROP USER IF EXISTS '$database->db_username'@'%'");
            }
            
            $pdo->exec('FLUSH PRIVILEGES');

        } catch (\PDOException $e) {
            \Log::error('Gagal hapus DB di server MySQL: '.$e->getMessage());
        }

        $database->delete();

        if (request()->expectsJson() || request()->header('Accept') === 'application/json') {
            return response()->json(['success' => true, 'message' => 'Database berhasil dihapus!']);
        }

        return back()->with('success', 'Database berhasil dihapus!');
    }

    public function generateApiKey($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) {
            abort(404);
        }

        $database = HostingDatabase::where('user_id', Auth::id())->findOrFail($decoded[0]);
        $database->api_key = \Illuminate\Support\Str::random(40);
        $database->save();

        return back()->with('success', 'API Key berhasil di-generate ulang!');
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
            
            if ($request->has('drop_tables') && $request->drop_tables == '1') {
                $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
                $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
                foreach ($tables as $table) {
                    $pdo->exec("DROP TABLE IF EXISTS `$table`");
                }
                $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
            }

            $sql = file_get_contents($tempPath);
            // Non-prepared statement is needed to execute multiple queries at once
            $pdo->exec($sql);
        } catch (\Exception $e) {
            \Log::error("mysql import error: " . $e->getMessage());
            
            $msg = $e->getMessage();
            if (str_contains($msg, '42S01') || str_contains($msg, 'already exists')) {
                return back()->with('error', 'Gagal: Tabel sudah ada di database. Silakan kosongkan database terlebih dahulu melalui phpMyAdmin (Drop semua tabel), lalu coba Import lagi.');
            }

            return back()->with('error', 'Gagal mengimpor database: ' . $msg);
        }

        return back()->with('success', 'Database berhasil diimpor!');
    }

    public function storePgsql(Request $request)
    {
        $existingDb = \App\Models\HostingPgsqlDatabase::where('user_id', Auth::id())->first();
        $prefix = 'ryz_' . Auth::id() . '_';

        if ($existingDb) {
            $request->validate([
                'db_username' => 'required|string|alpha_dash|max:15',
            ]);
            $cleanDbName = $prefix . strtolower(trim($request->db_username)); // For simplicity, in PG we'll make DB name same as username if they just want 1 DB per user, or allow multiple. Let's allow multiple by accepting db_name.
            // Wait, looking at NoSQL it used db_username. For PGSQL it's like MySQL.
        }

        // Let's implement full like MySQL
        if ($existingDb) {
            $request->validate([
                'db_name' => 'required|string|alpha_dash|max:15',
            ], [
                'db_name.alpha_dash' => 'Nama database hanya boleh berisi huruf, angka, strip, dan underscore.',
            ]);

            $cleanDbName = $prefix.strtolower(trim($request->db_name));
            $cleanUsername = $existingDb->db_username;
            $dbPassword = \Illuminate\Support\Facades\Crypt::decryptString($existingDb->db_password);
        } else {
            $request->validate([
                'db_name' => 'required|string|alpha_dash|max:15',
                'db_username' => 'required|string|alpha_dash|max:15',
                'db_password' => 'required|string|max:32',
            ], [
                'db_name.alpha_dash' => 'Nama database hanya boleh berisi huruf, angka, strip, dan underscore.',
                'db_username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip, dan underscore.',
            ]);

            $cleanDbName = $prefix.strtolower(trim($request->db_name));
            $cleanUsername = $prefix.strtolower(trim($request->db_username));
            $dbPassword = trim($request->db_password);
        }

        if (\App\Models\HostingPgsqlDatabase::where('db_name', $cleanDbName)->exists()) {
            return back()->with('error', 'Nama database "'.$cleanDbName.'" sudah digunakan.');
        }

        $pgHost = env('PANEL_PGSQL_HOST', '172.18.0.12');
        $pgPort = env('PANEL_PGSQL_PORT', '5432');
        $pgUser = env('PANEL_PGSQL_USER', 'Bimaryan');
        $pgPass = env('PANEL_PGSQL_PASSWORD', '@Bimaryan2329');

        try {
            $pdo = new \PDO("pgsql:host={$pgHost};port={$pgPort};dbname=postgres", $pgUser, $pgPass);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // 1. Create Role (User) if not exists
            $stmt = $pdo->prepare("SELECT 1 FROM pg_roles WHERE rolname = ?");
            $stmt->execute([$cleanUsername]);
            if (!$stmt->fetchColumn()) {
                // PDO prepare doesn't work for CREATE ROLE names
                $pdo->exec("CREATE ROLE \"{$cleanUsername}\" WITH LOGIN PASSWORD '{$dbPassword}'");
            } else {
                $pdo->exec("ALTER ROLE \"{$cleanUsername}\" WITH PASSWORD '{$dbPassword}'");
            }

            // 2. Create Database
            $stmt = $pdo->prepare("SELECT 1 FROM pg_database WHERE datname = ?");
            $stmt->execute([$cleanDbName]);
            if (!$stmt->fetchColumn()) {
                $pdo->exec("CREATE DATABASE \"{$cleanDbName}\" OWNER \"{$cleanUsername}\"");
            }

            // 3. Grant Privileges
            $pdo->exec("GRANT ALL PRIVILEGES ON DATABASE \"{$cleanDbName}\" TO \"{$cleanUsername}\"");

        } catch (\PDOException $e) {
            return back()->with('error', 'Gagal membuat database PostgreSQL: '.$e->getMessage());
        }

        // Simpan ke portal
        \App\Models\HostingPgsqlDatabase::create([
            'user_id' => Auth::id(),
            'db_name' => $cleanDbName,
            'db_username' => $cleanUsername,
            'db_password' => \Illuminate\Support\Facades\Crypt::encryptString($dbPassword),
            'host' => $pgHost,
            'port' => $pgPort,
        ]);

        return back()->with('success', 'Database PostgreSQL '.$cleanDbName.' berhasil dibuat!');
    }

    public function destroyPgsql($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $database = \App\Models\HostingPgsqlDatabase::where('user_id', Auth::id())->findOrFail($decoded[0]);

        $pgHost = env('PANEL_PGSQL_HOST', '172.18.0.12');
        $pgPort = env('PANEL_PGSQL_PORT', '5432');
        $pgUser = env('PANEL_PGSQL_USER', 'Bimaryan');
        $pgPass = env('PANEL_PGSQL_PASSWORD', '@Bimaryan2329');

        try {
            $pdo = new \PDO("pgsql:host={$pgHost};port={$pgPort};dbname=postgres", $pgUser, $pgPass);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // Cannot drop db if there are active connections. We can try to terminate them first.
            $pdo->exec("SELECT pg_terminate_backend(pg_stat_activity.pid) FROM pg_stat_activity WHERE pg_stat_activity.datname = '{$database->db_name}' AND pid <> pg_backend_pid()");
            
            $pdo->exec("DROP DATABASE IF EXISTS \"{$database->db_name}\"");

            // Check if user has other databases
            $otherDb = \App\Models\HostingPgsqlDatabase::where('db_username', $database->db_username)
                ->where('id', '!=', $database->id)
                ->exists();
                
            if (!$otherDb) {
                $pdo->exec("DROP ROLE IF EXISTS \"{$database->db_username}\"");
            }

        } catch (\PDOException $e) {
            \Log::error('Gagal hapus DB di server PostgreSQL: '.$e->getMessage());
        }

        $database->delete();
        return back()->with('success', 'Database PostgreSQL berhasil dihapus!');
    }
}
