<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JokiOrder;
use App\Models\User;

class UserController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        if ($request->ajax()) {
            $query = User::latest();

            if ($request->has('role') && $request->role != '') {
                if ($request->role === 'admin') {
                    $query->whereIn('role', ['admin_joki', 'admin_hosting']);
                } else {
                    $query->where('role', $request->role);
                }
            }

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('avatar', function($user) {
                    return substr($user->name, 0, 1);
                })
                ->addColumn('role_badge', function($user) {
                    if ($user->role == 'user_joki') {
                        return '<span class="px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap bg-blue-50 text-blue-600 border border-blue-200">Jasa Joki Code</span>';
                    } elseif ($user->role == 'user_hosting') {
                        return '<span class="px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap bg-emerald-50 text-emerald-600 border border-emerald-200">App Deployment</span>';
                    } else {
                        return '<span class="px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap bg-purple-50 text-purple-600 border border-purple-200 uppercase">' . str_replace('_', ' ', $user->role) . '</span>';
                    }
                })
                ->addColumn('created_at_formatted', function($user) {
                    return $user->created_at->format('d M Y, H:i');
                })
                ->addColumn('action', function($user) {
                    $url = route('superadmin.users.show', \Vinkla\Hashids\Facades\Hashids::encode($user->id));
                    return '<div class="flex items-center justify-center gap-2">
                                <a href="' . $url . '" class="p-2 text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors" title="Lihat Detail Profil">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>';
                })
                ->rawColumns(['role_badge', 'action'])
                ->make(true);
        }

        return view('pages.admin.users.index');
    }

    // Fungsi untuk tombol "Ikon Mata" (Detail Profil)
    public function show($hashid)
    {
        $decoded = \Vinkla\Hashids\Facades\Hashids::decode($hashid);
        if (empty($decoded)) {
            abort(404);
        }
        $id = $decoded[0];

        $user = User::withCount(['clientOrders', 'hostingProjects'])->findOrFail($id);

        // Opsional: Jika ingin sekalian melihat pesanan joki milik user ini
        $jokiOrders = JokiOrder::where('client_id', $id)->latest()->get();
        $hostingProjects = \App\Models\HostingProject::where('user_id', $id)->latest()->get();

        return view('pages.admin.users.show', compact('user', 'jokiOrders', 'hostingProjects'));
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
