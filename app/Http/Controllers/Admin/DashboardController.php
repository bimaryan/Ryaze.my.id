<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JokiOrder;
use App\Models\JokiPayment;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Hitung Total Pengguna
        $totalUsers = User::count();

        // 2. Hitung Pesanan Joki yang masih berjalan
        $activeJokiOrders = JokiOrder::whereIn('status', ['pending', 'progress', 'review'])->count();

        // 3. Hitung Layanan Hosting (Karena tabel hosting belum kita buat, kita set 0 dulu)
        $activeHosting = 0;

        // 4. Hitung Pendapatan Bulan Ini (Hanya yang statusnya 'paid')
        $totalRevenue = JokiPayment::where('status', 'paid')
            ->whereMonth('paid_at', Carbon::now()->month)
            ->whereYear('paid_at', Carbon::now()->year)
            ->sum('amount');

        // 5. Ambil 5 User terbaru yang mendaftar
        $recentUsers = User::latest()->take(5)->get();

        return view('pages.admin.index', compact(
            'totalUsers',
            'activeJokiOrders',
            'activeHosting',
            'totalRevenue',
            'recentUsers'
        ));
    }
}
