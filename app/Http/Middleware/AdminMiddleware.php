<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $isAdmin = (bool) $request->session()->get('is_admin', false);

        if (!Auth::check() || !$isAdmin) {
            if (Auth::check()) {
                Auth::logout();
            }

            $request->session()->forget('is_admin');

            return redirect()->route('login')->withErrors([
                'email' => 'Anda harus login sebagai admin.',
            ]);
        }

        return $next($request);
    }
}