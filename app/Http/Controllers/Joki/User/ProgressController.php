<?php

namespace App\Http\Controllers\Joki\User;

use App\Http\Controllers\Controller;
use App\Models\JokiOrder;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil orderan yang MASIH AKTIF
        $activeOrders = JokiOrder::where('client_id', $user->id)
            ->whereIn('status', ['pending', 'progress', 'review'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.joki.user.progress', compact('activeOrders'));
    }
}
