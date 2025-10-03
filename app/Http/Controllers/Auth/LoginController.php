<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $adminEmail = env('ADMIN_EMAIL');
        $adminPassword = env('ADMIN_PASSWORD');

        if ($adminEmail && $adminPassword && strcasecmp($credentials['email'], $adminEmail) === 0) {
            User::updateOrCreate(
                ['email' => $adminEmail],
                [
                    'name'     => env('ADMIN_NAME', 'Administrator'),
                    'password' => Hash::make($adminPassword),
                ]
            );
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt([
            'email'    => $credentials['email'],
            'password' => $credentials['password'],
        ], $remember)) {
            $request->session()->regenerate();

            $request->session()->forget('is_admin');

            if ($adminEmail && strcasecmp(Auth::user()->email, $adminEmail) === 0) {
                $request->session()->put('is_admin', true);
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
        }

        $request->session()->forget('is_admin');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}