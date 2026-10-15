<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class WebAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($credentials, true)) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
        }

        if (! auth()->user()->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors(['email' => 'Akun Anda dinonaktifkan. Hubungi administrator.'])->withInput();
        }

        $request->session()->regenerate();

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'auth.login',
            'ip' => $request->ip(),
        ]);

        return redirect()->route('dashboard');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|regex:/^(?=.*[A-Za-z])(?=.*\d).+$/|confirmed',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'username' => strtolower($validated['username']),
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->sendEmailVerificationNotification();
        Auth::login($user);

        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'action' => 'auth.register',
            'ip' => $request->ip(),
        ]);

        return redirect()->route('verification.notice');
    }

    public function logout(Request $request): RedirectResponse
    {
        if (auth()->check()) {
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'auth.logout',
                'ip' => $request->ip(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
