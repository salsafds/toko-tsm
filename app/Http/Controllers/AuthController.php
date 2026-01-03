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

            if ($user->status !== 'aktif') {
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

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Pengecekan status user
            if ($user->status !== 'aktif') { 
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error_status', 'Akun anda sudah tidak aktif. Hubungi pihak terkait.');
            }

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