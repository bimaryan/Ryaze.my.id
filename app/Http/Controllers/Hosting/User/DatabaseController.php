<?php

namespace App\Http\Controllers\Hosting\User;

use App\Http\Controllers\Controller;
use App\Models\HostingDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'db_password' => 'required|string|min:8|max:32',
        ], [
            'db_name.alpha_dash' => 'Nama database hanya boleh berisi huruf, angka, strip, dan underscore.',
            'db_username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip, dan underscore.',
            'db_password.min' => 'Password minimal 8 karakter.',
        ]);

        // 2. Terapkan Prefix (ryz_{id}_) agar tidak bentrok antar user di server MySQL
        $prefix = 'ryz_'.Auth::id().'_';
        $cleanDbName = $prefix.strtolower(trim($request->db_name));
        $cleanUsername = $prefix.strtolower(trim($request->db_username));
        $dbPassword = $request->db_password;

        // 3. Cek apakah nama database ini sudah ada (karena digabung prefix)
        if (HostingDatabase::where('db_name', $cleanDbName)->exists()) {
            return back()->with('error', 'Nama database "'.$cleanDbName.'" sudah digunakan.');
        }

        // Ambil konfigurasi dari .env
        $rootPass = env('PANEL_MYSQL_ROOT_PASSWORD');
        $mysqlHost = env('PANEL_MYSQL_HOST', '1Panel-mysql-KZAi');

        if (! $rootPass) {
            return back()->with('error', 'Konfigurasi Root MySQL belum diatur oleh Admin.');
        }

        try {
            $pdo = new \PDO("mysql:host={$mysqlHost};port=3306", 'root', $rootPass);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // 1. Buat Database
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$cleanDbName`");

            // 2. Buat User (Tanpa plugin khusus)
            $pdo->exec("CREATE USER IF NOT EXISTS '$cleanUsername'@'%' IDENTIFIED BY '$dbPassword'");

            // 3. Grant akses
            $pdo->exec("GRANT ALL PRIVILEGES ON `$cleanDbName`.* TO '$cleanUsername'@'%'");

            // 4. Flush agar user langsung dikenali
            $pdo->exec("FLUSH PRIVILEGES");

        } catch (\PDOException $e) {
            return back()->with('error', 'Gagal membuat database: ' . $e->getMessage());
        }

        // Simpan ke database portal Ryaze
        HostingDatabase::create([
            'user_id' => Auth::id(),
            'db_name' => $cleanDbName,
            'db_username' => $cleanUsername,
            'db_password' => $dbPassword,
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
}
