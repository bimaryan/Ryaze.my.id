<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Inertia\Inertia;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Gagal login menggunakan Google. Silakan coba lagi.');
        }

        $user = User::where('email', $googleUser->email)->first();

        if ($user) {
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->id,
                    'provider' => 'google'
                ]);
            }
            // Ensure email is verified if it comes from Google
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }
        } else {
            $enableRegistration = \App\Models\Setting::where('key', 'enable_registration')->value('value');
            if (isset($enableRegistration) && $enableRegistration == '0') {
                return redirect()->route('login')->with('error', 'Pendaftaran akun baru ditutup sementara.');
            }

            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'provider' => 'google',
                'password' => bcrypt(Str::random(24)),
                'role' => null, // Role null indicates they need to choose a service
                'email_verified_at' => now(), // Auto verify for Google users
                'status' => 'active'
            ]);
        }

        if ($user->status === 'suspended') {
            return redirect('/login')->with('error', 'Akun Anda telah ditangguhkan. Silakan hubungi admin.');
        }

        Auth::login($user);

        if (!$user->role) {
            return redirect()->route('auth.select_service');
        }

        $request->session()->regenerate();

        $redirect = match ($user->role) {
            'superadmin' => '/superadmin/dashboard',
            'admin_joki' => '/admin/joki/dashboard',
            'admin_hosting' => '/admin/hosting/dashboard',
            'user_joki' => '/user/joki/dashboard',
            'user_hosting' => '/user/hosting/dashboard',
            default => '/dashboard',
        };

        return redirect()->intended($redirect);
    }

    public function showSelectService()
    {
        if (Auth::user()->role) {
            return redirect('/'); // Already has a role
        }

        return Inertia::render('Auth/SelectService', [
            'siteName' => \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze Portal',
        ]);
    }

    public function storeService(Request $request)
    {
        $request->validate([
            'role' => 'required|in:user_joki,user_hosting',
        ]);

        $user = Auth::user();

        if ($user->role) {
            return redirect('/');
        }

        $user->update([
            'role' => $request->role,
        ]);

        $redirect = match ($user->role) {
            'user_joki' => '/user/joki/dashboard',
            'user_hosting' => '/user/hosting/dashboard',
            default => '/dashboard',
        };

        return redirect()->intended($redirect);
    }
}
