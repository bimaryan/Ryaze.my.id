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

    public function updateRole(\Illuminate\Http\Request $request, $hashid)
    {
        $request->validate([
            'role' => 'required|in:superadmin,admin_hosting,admin_joki,user_hosting,user_joki'
        ]);

        $decoded = \Vinkla\Hashids\Facades\Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $user = User::findOrFail($decoded[0]);
        
        // Prevent changing own role if superadmin
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat mengubah role Anda sendiri.');
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', 'Role user berhasil diperbarui.');
    }

    public function toggleStatus($hashid)
    {
        $decoded = \Vinkla\Hashids\Facades\Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $user = User::findOrFail($decoded[0]);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menangguhkan akun Anda sendiri.');
        }

        $user->update([
            'status' => $user->status === 'active' ? 'suspended' : 'active'
        ]);

        $msg = $user->status === 'active' ? 'diaktifkan kembali' : 'ditangguhkan';
        return back()->with('success', "Akun user berhasil {$msg}.");
    }

    public function destroy($hashid)
    {
        $decoded = \Vinkla\Hashids\Facades\Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $user = User::findOrFail($decoded[0]);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Idealnya hapus relasi terkait (hosting, joki, dll) atau ubah onDelete('cascade') di database.
        $user->delete();

        return redirect()->route('superadmin.users.index')->with('success', 'Akun user berhasil dihapus permanen.');
    }
}
