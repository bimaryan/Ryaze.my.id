<?php

namespace App\Http\Controllers\Joki\User;

use App\Http\Controllers\Controller;
use App\Models\JokiOrder;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 2. Ambil orderan yang SUDAH SELESAI atau DIBATALKAN (Riwayat)
        $historyOrders = JokiOrder::where('client_id', $user->id)
            ->whereIn('status', ['completed', 'canceled'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('pages.joki.user.riwayat', compact('historyOrders'));
    }
}
