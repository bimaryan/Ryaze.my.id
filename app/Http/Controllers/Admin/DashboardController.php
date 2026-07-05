<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HostingProject;
use App\Models\HostingPayment;
use App\Models\HostingDatabase;
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

        // 2. Hitung Pesanan Joki (Aktif & Total)
        $activeJokiOrders = JokiOrder::whereIn('status', ['pending', 'progress', 'review'])->count();
        $totalJokiOrders = JokiOrder::count();

        // 3. Hitung Layanan Hosting (Aktif & Total)
        $activeHosting = HostingProject::where('status', 'active')->count();
        $totalHosting = HostingProject::count();

        // 4. Hitung Pendapatan Bulan Ini & Keseluruhan
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Joki Revenue
        $jokiRevenueMonth = JokiPayment::where('status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->sum('amount');
        $jokiRevenueTotal = JokiPayment::where('status', 'paid')->sum('amount');

        // Hosting Revenue
        $hostingRevenueMonth = HostingPayment::where('status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->sum('amount');
        $hostingRevenueTotal = HostingPayment::where('status', 'paid')->sum('amount');

        // Total Revenue
        $totalRevenueMonth = $jokiRevenueMonth + $hostingRevenueMonth;
        $totalRevenueTotal = $jokiRevenueTotal + $hostingRevenueTotal;

        // 5. Hitung Database & Storage
        $totalDatabases = HostingDatabase::count();
        $totalStorageMB = \App\Models\User::sum('hosting_storage_limit_mb');

        // 6. Ambil Data Terbaru (Recent Activities)
        $recentUsers = User::latest()->take(5)->get();
        
        $recentJokiOrders = JokiOrder::with('client', 'service')
            ->latest()
            ->take(5)
            ->get();
            
        $recentHostingProjects = HostingProject::with('client')
            ->latest()
            ->take(5)
            ->get();

        return view('pages.admin.index', compact(
            'totalUsers',
            'activeJokiOrders',
            'totalJokiOrders',
            'activeHosting',
            'totalHosting',
            'jokiRevenueMonth',
            'jokiRevenueTotal',
            'hostingRevenueMonth',
            'hostingRevenueTotal',
            'totalRevenueMonth',
            'totalRevenueTotal',
            'totalDatabases',
            'totalStorageMB',
            'recentUsers',
            'recentJokiOrders',
            'recentHostingProjects'
        ));
    }
}
