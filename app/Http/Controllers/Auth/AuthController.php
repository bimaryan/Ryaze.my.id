<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;

    private const LOCKOUT_MINUTES = 5;

    // Menampilkan halaman login + Generate Captcha
    public function loginindex(Request $request)
    {
        $num1 = rand(1, 10); // Perbesar range supaya susah di-brute
        $num2 = rand(1, 10);
        $operator = ['+', '-', '*'][rand(0, 2)]; // Variasikan operator

        $answer = match ($operator) {
            '+' => $num1 + $num2,
            '-' => $num1 - $num2,
            '*' => $num1 * $num2,
        };

        $request->session()->put('captcha_answer', $answer);
        $request->session()->put('captcha_token', Str::random(32)); // token anti-replay

        $captcha_question = "$num1 $operator $num2 = ?";

        return view('pages.auth.login', compact('captcha_question'));
    }

    // Menampilkan halaman register
    public function registerindex(Request $request)
    {
        return view('pages.auth.register');
    }

    // Memproses data login
    public function loginProcess(Request $request)
    {
        // 1. Validasi dasar
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string',
            'captcha' => 'required|numeric',
        ], [
            'captcha.required' => 'Captcha wajib diisi.',
            'captcha.numeric' => 'Captcha harus berupa angka.',
        ]);

        // 2. Rate limiting berbasis IP + email (lawan fuzzing & brute force)
        $throttleKey = Str::lower($request->input('email')).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ])->onlyInput('email');
        }

        // 3. Validasi captcha
        if ((int) $request->captcha !== (int) $request->session()->get('captcha_answer')) {
            RateLimiter::hit($throttleKey, self::LOCKOUT_MINUTES * 60);
            $request->session()->forget(['captcha_answer', 'captcha_token']);

            return back()->withErrors([
                'captcha' => 'Jawaban Captcha salah.',
            ])->onlyInput('email');
        }

        $request->session()->forget(['captcha_answer', 'captcha_token']);

        // 4. Cek apakah user di-lock di level DB (opsional: double protection)
        $user = User::where('email', $request->email)->first();

        if ($user && $user->locked_until && now()->lessThan($user->locked_until)) {
            $remaining = now()->diffInMinutes($user->locked_until);

            return back()->withErrors([
                'email' => "Akun terkunci sementara. Coba lagi dalam {$remaining} menit.",
            ])->onlyInput('email');
        }

        // 5. Autentikasi
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Reset counter & catat login sukses
            RateLimiter::clear($throttleKey);
            Auth::user()->update([
                'login_attempts' => 0,
                'locked_until' => null,
                'last_login_ip' => $request->ip(),
                'last_login_at' => now(),
            ]);

            return match (Auth::user()->role) {
                'superadmin' => redirect()->intended('/superadmin/dashboard'),
                'admin_joki' => redirect()->intended('/admin/joki/dashboard'),
                'admin_hosting' => redirect()->intended('/admin/hosting/dashboard'),
                'user_joki' => redirect()->intended('/user/joki/dashboard'),
                'user_hosting' => redirect()->intended('/user/hosting/dashboard'),
                default => redirect()->intended('/dashboard'),
            };
        }

        // 6. Login gagal — increment attempt di DB juga
        RateLimiter::hit($throttleKey, self::LOCKOUT_MINUTES * 60);

        if ($user) {
            $attempts = $user->login_attempts + 1;
            $user->update([
                'login_attempts' => $attempts,
                'locked_until' => $attempts >= self::MAX_ATTEMPTS
                    ? now()->addMinutes(self::LOCKOUT_MINUTES)
                    : null,
            ]);
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // Memproses data register
    public function registerProcess(Request $request)
    {
        // 1. Validasi Input form register (Password diperkuat)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:user_joki,user_hosting',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)        // Minimal 8 karakter
                    ->letters()         // Harus mengandung huruf
                    ->mixedCase()       // Harus mengandung huruf besar dan kecil (A-Z, a-z)
                    ->numbers()         // Harus mengandung angka (0-9)
                    ->symbols()         // Harus mengandung simbol (@, #, $, dll)
                    ->uncompromised(),   // (Opsional) Cek apakah password ini pernah bocor di database hacker global
            ],
        ], [
            'email.unique' => 'Email ini sudah terdaftar.',
            'role.in' => 'Pilihan layanan tidak valid.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            // Custom pesan error untuk password yang kuat
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.letters' => 'Password harus mengandung setidaknya satu huruf.',
            'password.mixed_case' => 'Password harus mengandung huruf besar dan huruf kecil.',
            'password.numbers' => 'Password harus mengandung setidaknya satu angka.',
            'password.symbols' => 'Password harus mengandung setidaknya satu simbol (!, @, #, dst).',
            'password.uncompromised' => 'Password ini terlalu umum dan tidak aman. Silakan gunakan password lain.',
        ]);

        // 2. Simpan User Baru ke Database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // 3. Auto-Login setelah berhasil register
        Auth::login($user);

        // 4. Redirect sesuai role yang dipilih saat daftar
        if ($user->role === 'user_joki') {
            return redirect()->intended('/user/joki/dashboard');
        } else {
            return redirect()->intended('/user/hosting/dashboard');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        // Mencegah Session Fixation Attack
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
