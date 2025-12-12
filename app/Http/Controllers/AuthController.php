<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi Input
        $validation = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Attempt Login
        if (Auth::attempt($validation)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // --- LOGIKA REDIRECT PINTAR ---

            if ($user->role === 'admin') {
                $redirectUrl = route('dashboard-admin');
            } else if ($user->role === 'driver') {
                $redirectUrl = route('dashboard-driver');
            } else {
                // url()->previous() mengambil HTTP Referer (halaman saat ini di browser)
                $fallbackUrl = url()->previous();

                // Ambil URL intended, jika null gunakan fallback (previous)
                $redirectUrl = redirect()->intended($fallbackUrl)->getTargetUrl();

                // Jangan sampai redirect ke halaman login itu sendiri (looping)
                if ($redirectUrl == route('login') || $redirectUrl == url('/login')) {
                    $redirectUrl = route('landing-page');
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil! Mengalihkan...',
                'user' => $user,
                'redirect_url' => $redirectUrl // URL dinamis dikirim ke JS
            ]);
        }

        return response()->json([
            'message' => 'Email atau password yang Anda masukkan salah.',
        ], 401);
    }

    public function register(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            // Custom messages jika diperlukan (opsional, karena JS sudah handle)
            'email.unique' => 'Email ini sudah terdaftar.',
        ]);

        // 2. Buat User Baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
        ]);

        // 3. Auto Login setelah Register
        Auth::login($user);
        $request->session()->regenerate();

        // 4. Return JSON Sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil! Selamat datang.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();


        return redirect()->route('landing-page');
    }
}
