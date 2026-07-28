<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureActiveHostingSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Admin dan Superadmin bypass pengecekan ini
        if ($user && in_array($user->role, ['superadmin', 'admin_hosting'])) {
            return $next($request);
        }

        // Jika user belum berlangganan
        if ($user && !$user->hasActiveHostingSubscription()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda harus berlangganan paket hosting terlebih dahulu untuk mengakses fitur ini.'
                ], 403);
            }
            
            return redirect()->route('user_hosting.subscription')
                ->with('error', 'Silakan pilih paket langganan hosting terlebih dahulu untuk mengakses fitur ini.');
        }

        return $next($request);
    }
}
