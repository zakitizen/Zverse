<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PewartaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'pewarta') {
            if (!session('pewarta_user_id')) {
                return redirect()->route('login')->withErrors(['username' => 'Anda harus login sebagai Pewarta.']);
            }
        }
        return $next($request);
    }
}
