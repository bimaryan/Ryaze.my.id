<?php

namespace App\Http\Controllers\Joki\Admin;

use App\Http\Controllers\Controller;
use App\Models\JokiOrder;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Menghitung Statistik untuk Card
        $pendingOrders = JokiOrder::where('status', 'pending')->count();
        $progressOrders = JokiOrder::where('status', 'progress')->count();
        $reviewOrders = JokiOrder::where('status', 'review')->count();
        $completedOrders = JokiOrder::where('status', 'completed')
            ->whereMonth('updated_at', date('m')) // Selesai bulan ini
            ->count();

        // 2. Mengambil data antrean (Tabel)
        // Load relasi 'client' agar nama klien terbaca tanpa query N+1
        $queueOrders = JokiOrder::with('client')
            ->whereIn('status', ['pending', 'progress', 'review'])
            ->orderBy('deadline', 'asc') // Urutkan dari deadline terdekat
            ->get();

        // 3. Kirim data ke view
        return view('pages.joki.admin.index', compact(
            'pendingOrders',
            'progressOrders',
            'reviewOrders',
            'completedOrders',
            'queueOrders'
        ));
    }
}
