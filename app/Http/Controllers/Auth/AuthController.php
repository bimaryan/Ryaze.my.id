<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // Menampilkan halaman login + Generate Captcha
    public function loginindex(Request $request)
    {
        // Bikin angka random 1 sampai 10
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);

        // Simpan jawaban di session agar tidak bisa dimanipulasi dari HTML
        $request->session()->put('captcha_answer', $num1 + $num2);

        // Buat string pertanyaan untuk dikirim ke View
        $captcha_question = "$num1 + $num2 = ?";

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
        // 1. Validasi Input form dasar
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'captcha' => 'required|numeric',
        ], [
            'captcha.required' => 'Captcha wajib diisi.',
            'captcha.numeric' => 'Captcha harus berupa angka.',
        ]);

        // 2. Validasi Jawaban Captcha Sendiri
        if ($request->captcha != $request->session()->get('captcha_answer')) {
            return back()->withErrors([
                'captcha' => 'Jawaban Captcha salah. Silakan hitung ulang!',
            ])->onlyInput('email');
        }

        // Hapus session captcha setelah berhasil dijawab agar lebih aman
        $request->session()->forget('captcha_answer');

        // 3. Proses Autentikasi
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Mencegah Session Fixation Attack
            $request->session()->regenerate();

            // 4. Redirect spesifik berdasarkan Role
            $role = Auth::user()->role;

            return match ($role) {
                'superadmin' => redirect()->intended('/superadmin/dashboard'),
                'admin_joki' => redirect()->intended('/admin/joki/dashboard'),
                'admin_hosting' => redirect()->intended('/admin/hosting/dashboard'),
                'user_joki' => redirect()->intended('/user/joki/dashboard'),
                'user_hosting' => redirect()->intended('/user/hosting/dashboard'),
                default => redirect()->intended('/dashboard'),
            };
        }

        // Jika email/password salah
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
