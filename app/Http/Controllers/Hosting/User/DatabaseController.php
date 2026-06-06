<?php

namespace App\Http\Controllers\Hosting\User;

use App\Http\Controllers\Controller;
use App\Models\HostingDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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
        $request->validate([
            'db_name' => 'required|string|alpha_dash|max:15|unique:hosting_databases,db_name',
        ]);

        $prefix = 'ryz_'.Auth::id().'_';
        $cleanDbName = $prefix.strtolower(trim($request->db_name));
        $dbUsername = substr($cleanDbName, 0, 16);
        $dbPassword = Str::random(16);

        // Ambil konfigurasi dari .env
        $rootPass = env('PANEL_MYSQL_ROOT_PASSWORD');
        $mysqlHost = env('PANEL_MYSQL_HOST', '1Panel-mysql-KZAi');

        if (! $rootPass) {
            return back()->with('error', 'Konfigurasi Root MySQL belum diatur oleh Admin.');
        }

        try {
            // Login ke MySQL Server 1Panel menggunakan PDO PHP
            $pdo = new \PDO("mysql:host={$mysqlHost};port=3306", 'root', $rootPass);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // Eksekusi pembuatan Database dan User
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$cleanDbName`");
            $pdo->exec("CREATE USER IF NOT EXISTS '$dbUsername'@'%' IDENTIFIED BY '$dbPassword'");
            $pdo->exec("GRANT ALL PRIVILEGES ON `$cleanDbName`.* TO '$dbUsername'@'%'");
            $pdo->exec('FLUSH PRIVILEGES');

        } catch (\PDOException $e) {
            // Jika gagal terhubung atau gagal eksekusi query
            return back()->with('error', 'Gagal membuat database: '.$e->getMessage());
        }

        // Simpan ke database portal Ryaze
        HostingDatabase::create([
            'user_id' => Auth::id(),
            'db_name' => $cleanDbName,
            'db_username' => $dbUsername,
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
            // Login ke MySQL Server
            $pdo = new \PDO("mysql:host={$mysqlHost};port=3306", 'root', $rootPass);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // Hapus Database dan User
            $pdo->exec("DROP DATABASE IF EXISTS `$database->db_name`");
            $pdo->exec("DROP USER IF EXISTS '$database->db_username'@'%'");
            $pdo->exec('FLUSH PRIVILEGES');

        } catch (\PDOException $e) {
            // Tetap hapus dari list portal meskipun di server aslinya mungkin sudah tidak ada
            \Log::error('Gagal hapus DB di server MySQL: '.$e->getMessage());
        }

        $database->delete();

        return back()->with('success', 'Database berhasil dihapus!');
    }
}
