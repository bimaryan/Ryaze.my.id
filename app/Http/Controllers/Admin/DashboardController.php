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
        // Hanya hitung alokasi storage untuk user yang benar-benar memiliki layanan hosting
        $totalStorageMB = \App\Models\User::whereHas('hostingProjects')->sum('hosting_storage_limit_mb');

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

        // 7. Chart Data (Pie, Line, Bar)
        
        // 7a. Pie Chart: User Roles
        $rolesCount = User::selectRaw('role, count(*) as count')->groupBy('role')->pluck('count', 'role')->toArray();
        $chartUserRoles = [
            'labels' => array_keys($rolesCount),
            'series' => array_values($rolesCount)
        ];

        // 7b. Line Chart: User Registrations (Last 6 Months)
        $months = [];
        $registrations = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->startOfMonth()->subMonths($i);
            $months[] = $date->translatedFormat('M Y');
            $registrations[] = User::whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->count();
        }
        $chartUserRegistrations = [
            'labels' => $months,
            'series' => $registrations
        ];

        // 7c. Bar Chart: Revenue Last 6 Months (Joki vs Hosting)
        $jokiRev = [];
        $hostingRev = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->startOfMonth()->subMonths($i);
            $jokiRev[] = JokiPayment::where('status', 'paid')
                ->whereMonth('paid_at', $date->month)
                ->whereYear('paid_at', $date->year)
                ->sum('amount');
            $hostingRev[] = HostingPayment::where('status', 'paid')
                ->whereMonth('paid_at', $date->month)
                ->whereYear('paid_at', $date->year)
                ->sum('amount');
        }
        $chartRevenue = [
            'labels' => $months,
            'joki' => $jokiRev,
            'hosting' => $hostingRev
        ];

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
            'recentHostingProjects',
            'chartUserRoles',
            'chartUserRegistrations',
            'chartRevenue'
        ));
    }
}
