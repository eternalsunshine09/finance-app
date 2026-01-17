<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. Tampilkan Form Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 2. Proses Login
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Cek ke Database (Magic-nya Laravel)
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Kalau sukses, masuk ke dashboard
            return redirect()->intended('dashboard');
        }

        // Kalau gagal, kembalikan ke form dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau password salah!',
        ])->onlyInput('email');
    }

    // 3. Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    // 4. Tampilkan Form Register
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // 5. Proses Register User Baru
    public function register(Request $request)
    {
        // A. Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email', // Email gak boleh kembar
            'password' => 'required|min:6|confirmed', // Password harus ada konfirmasinya
        ]);

        // B. Buat User Baru ke Database
        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'user', // Default pasti User, bukan Admin
        ]);

        // C. [PENTING] Otomatis Buatkan Dompet IDR & USD (Saldo 0)
        // Kalau ini gak ada, nanti error pas mau transaksi
        \App\Models\Wallet::create([
            'user_id' => $user->id,
            'currency' => 'IDR',
            'balance' => 0
        ]);
        
        \App\Models\Wallet::create([
            'user_id' => $user->id,
            'currency' => 'USD',
            'balance' => 0
        ]);

        // D. Langsung Login & Masuk Dashboard
        Auth::login($user);
        return redirect('dashboard');
    }
}