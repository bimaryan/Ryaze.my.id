<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JokiOrder;
use App\Models\User;

class UserController extends Controller
{
    // Fungsi untuk halaman "Lihat Semua"
    public function index()
    {
        // Mengambil semua user, diurutkan dari yang terbaru, dan dipaginasi (10 per halaman)
        $users = User::latest()->paginate(10);

        return view('pages.admin.users.index', compact('users'));
    }

    // Fungsi untuk tombol "Ikon Mata" (Detail Profil)
    public function show($hashid)
    {
        $decoded = \Vinkla\Hashids\Facades\Hashids::decode($hashid);
        if (empty($decoded)) {
            abort(404);
        }
        $id = $decoded[0];

        $user = User::findOrFail($id);

        // Opsional: Jika ingin sekalian melihat pesanan joki milik user ini
        $jokiOrders = JokiOrder::where('client_id', $id)->latest()->get();

        return view('pages.admin.users.show', compact('user', 'jokiOrders'));
    }
}
