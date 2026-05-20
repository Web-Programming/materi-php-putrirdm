<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ==================== REGISTER ====================
    // Tampilkan form register
    public function registerForm()
    {
        return view('auth.register', ['title' => 'Daftar Akun']);
    }
    // Proses simpan user baru
    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);
        // Simpan user baru ke database
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // enkripsi password
        ]);
        // Redirect ke halaman login dengan pesan sukses
        return redirect('/login')->with('success', 'Akun berhasil dibuat. Silakan login.');
    }
    // ==================== LOGIN ====================
    // Tampilkan form login
    public function loginForm()
    {
        return view('auth.login', ['title' => 'Login']);
    }

    // Proses autentikasi pengguna
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Ambil kredensial dari request
        $credentials = $request->only('email', 'password');
        // Coba login dengan Auth::attempt()
        // Parameter kedua true = aktifkan fitur "Remember Me"
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Regenerasi session untuk mencegah Session Fixation Attack
            $request->session()->regenerate();
            // Redirect ke halaman yang dituju atau /dashboard
            return redirect()->intended('/dashboard');
        }
        // Jika gagal, kembali ke form login dengan pesan error
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email atau password salah.']);
    }
    
    // ==================== LOGOUT ====================
    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout(); // hapus sesi login
        // Invalidasi & regenerasi token session
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}