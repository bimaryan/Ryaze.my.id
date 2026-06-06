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

        // Prefix agar nama DB dan User unik di server
        $prefix = 'ryz_'.Auth::id().'_';
        $cleanDbName = $prefix.strtolower(trim($request->db_name));
        $dbUsername = substr($cleanDbName, 0, 16); // MySQL user max 16-32 chars tergantung versi
        $dbPassword = Str::random(16);

        // 1. Dapatkan Password Root MySQL dari .env
        $rootPass = env('PANEL_MYSQL_ROOT_PASSWORD');
        if (! $rootPass) {
            return back()->with('error', 'Konfigurasi Root MySQL belum diatur oleh Admin.');
        }

        // 2. Siapkan perintah Docker untuk 1Panel-mysql-KZAi
        $sqlCommand = "CREATE DATABASE IF NOT EXISTS \`{$cleanDbName}\`; ".
                      "CREATE USER IF NOT EXISTS '{$dbUsername}'@'%' IDENTIFIED BY '{$dbPassword}'; ".
                      "GRANT ALL PRIVILEGES ON \`{$cleanDbName}\`.* TO '{$dbUsername}'@'%'; ".
                      'FLUSH PRIVILEGES;';

        $dockerCmd = "docker exec 1Panel-mysql-KZAi mysql -uroot -p'{$rootPass}' -e \"{$sqlCommand}\" 2>&1";

        // 3. Eksekusi ke Server Linux
        exec($dockerCmd, $output, $exitCode);

        if ($exitCode !== 0) {
            return back()->with('error', 'Gagal membuat database di server: '.implode(' ', $output));
        }

        // 4. Simpan ke Database Portal Ryaze jika sukses
        HostingDatabase::create([
            'user_id' => Auth::id(),
            'db_name' => $cleanDbName,
            'db_username' => $dbUsername,
            'db_password' => $dbPassword,
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

        // Hapus DB dan User dari Docker 1Panel
        $sqlCommand = "DROP DATABASE IF EXISTS \`{$database->db_name}\`; ".
                      "DROP USER IF EXISTS '{$database->db_username}'@'%'; ".
                      'FLUSH PRIVILEGES;';

        $dockerCmd = "docker exec 1Panel-mysql-KZAi mysql -uroot -p'{$rootPass}' -e \"{$sqlCommand}\" 2>&1";
        exec($dockerCmd);

        $database->delete();

        return back()->with('success', 'Database berhasil dihapus!');
    }
}
