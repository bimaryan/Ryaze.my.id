<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $maintenanceMode = Setting::where('key', 'maintenance_mode')->value('value');
        } catch (\Exception $e) {
            // Jika database belum siap atau tabel belum ada, biarkan lolos
            $maintenanceMode = '0';
        }

        if ($maintenanceMode === '1') {
            // Pengecualian route yang masih bisa diakses saat maintenance
            if ($request->is('superadmin*') || 
                $request->is('admin*') || 
                $request->is('login') || 
                $request->is('logout') || 
                $request->is('api*') || // Allow API if necessary, e.g. webhooks
                $request->is('pakasir/webhook*')) {
                return $next($request);
            }

            // Jika API request ditolak, kembalikan json
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Service Unavailable (Maintenance Mode)'], 503);
            }

            // Render halaman maintenance khusus (kode status 503 Service Unavailable)
            return response()->view('pages.maintenance', [], 503);
        }

        return $next($request);
    }
}
