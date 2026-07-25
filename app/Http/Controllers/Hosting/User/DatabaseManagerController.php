<?php

namespace App\Http\Controllers\Hosting\User;

use App\Http\Controllers\Controller;
use App\Models\HostingDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;
use PDO;
use PDOException;

class DatabaseManagerController extends Controller
{
    /**
     * Tampilkan halaman manager untuk database tertentu (Menampilkan daftar tabel)
     */
    public function index($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $database = HostingDatabase::where('user_id', Auth::id())->findOrFail($decoded[0]);

        try {
            $pdo = $this->getPdoConnection($database);
            
            // Get all tables
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

            return view('pages.hosting.user.database.manager', compact('database', 'tables'));

        } catch (PDOException $e) {
            return back()->with('error', 'Gagal terhubung ke database: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan isi data (baris) dari suatu tabel
     */
    public function showTable(Request $request, $hashid, $tableName)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $database = HostingDatabase::where('user_id', Auth::id())->findOrFail($decoded[0]);

        try {
            $pdo = $this->getPdoConnection($database);
            
            // Validasi nama tabel (hindari SQL injection)
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (!in_array($tableName, $tables)) {
                abort(404, "Tabel tidak ditemukan.");
            }

            // Pagination params
            $page = max(1, (int) $request->query('page', 1));
            $limit = 50;
            $offset = ($page - 1) * $limit;

            // Get columns info
            $stmtCols = $pdo->query("SHOW COLUMNS FROM `$tableName`");
            $columns = $stmtCols->fetchAll(PDO::FETCH_ASSOC);
            
            // Get total count
            $stmtCount = $pdo->query("SELECT COUNT(*) FROM `$tableName`");
            $totalRows = (int) $stmtCount->fetchColumn();
            $totalPages = ceil($totalRows / $limit);

            // Get rows
            $stmtRows = $pdo->query("SELECT * FROM `$tableName` LIMIT $limit OFFSET $offset");
            $rows = $stmtRows->fetchAll(PDO::FETCH_ASSOC);

            return view('pages.hosting.user.database.manager_table', compact(
                'database', 'tables', 'tableName', 'columns', 'rows', 'page', 'totalPages', 'totalRows'
            ));

        } catch (PDOException $e) {
            return back()->with('error', 'Gagal mengeksekusi query: ' . $e->getMessage());
        }
    }

    /**
     * Jalankan Custom SQL Query
     */
    public function executeQuery(Request $request, $hashid)
    {
        $request->validate([
            'query' => 'required|string'
        ]);

        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $database = HostingDatabase::where('user_id', Auth::id())->findOrFail($decoded[0]);
        $query = trim($request->input('query'));

        try {
            $pdo = $this->getPdoConnection($database);
            
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            
            // Jika SELECT query, kembalikan hasil
            if (stripos($query, 'SELECT') === 0 || stripos($query, 'SHOW') === 0 || stripos($query, 'DESCRIBE') === 0) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return response()->json([
                    'success' => true,
                    'is_select' => true,
                    'data' => $results
                ]);
            } else {
                // INSERT, UPDATE, DELETE
                $affected = $stmt->rowCount();
                return response()->json([
                    'success' => true,
                    'is_select' => false,
                    'message' => "Query berhasil dieksekusi. $affected baris terpengaruh."
                ]);
            }

        } catch (PDOException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Helper untuk membuat koneksi PDO
     */
    private function getPdoConnection($database)
    {
        $dsn = "mysql:host={$database->host};dbname={$database->db_name};port=" . ($database->port ?? 3306) . ";charset=utf8mb4";
        $pdo = new PDO($dsn, $database->db_username, $database->db_password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        
        return $pdo;
    }
}
