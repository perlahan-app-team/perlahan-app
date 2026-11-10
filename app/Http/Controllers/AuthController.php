<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'masyarakat',
        ]);

        Auth::login($user);
        $user->last_login_at = now();
        $user->save();
        $request->session()->regenerate();

        return redirect('/masyarakat');
    }

    public function showLogin()
    {
        return view('masyarakat.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak sesuai.',
            ]);
        }

        $request->user()->forceFill(['last_login_at' => now()])->save();
        $request->session()->regenerate();

        return match (Auth::user()->role) {
            'admin' => redirect('/admin'),
            'petugas_lapangan' => redirect('/petugas'),
            default => redirect('/masyarakat'),
        };
    }

   public function logout(Request $request)
{
     Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('landing');
}
}
