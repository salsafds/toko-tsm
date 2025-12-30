<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Tambahkan pengecekan status juga di showLoginForm
            if ($user->status !== 'aktif') { // sesuaikan dengan nilai aktif di DB kamu
                Auth::logout();
                return redirect()->route('login')
                    ->with('error_status', 'Akun anda sudah tidak aktif. Hubungi pihak terkait.');
            }

            return $this->redirectByRole($user->id_role);
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $credentials = $request->only('username', 'password');

        // Tambahkan remember me jika ada checkbox nanti
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // === PENGECEKAN STATUS USER ===
            if ($user->status !== 'aktif') { // ubah sesuai kolom & nilai di tabel users kamu
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error_status', 'Akun anda sudah tidak aktif. Hubungi pihak terkait.');
            }
            // ==============================

            return $this->redirectByRole($user->id_role);
        }

        return back()->withErrors([
            'login' => 'Username atau password salah.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectByRole(string $roleId)
    {
        return match ($roleId) {
            'R01' => redirect()->intended(route('dashboard-master')),
            'R02' => redirect()->intended(route('dashboard-admin')),
            default => redirect()->intended('/'),
        };
    }
}