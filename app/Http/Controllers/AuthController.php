<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Kalau sudah login, langsung arahkan ke dashboard sesuai role
        if (Auth::check()) {
            $user = Auth::user();
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

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

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
