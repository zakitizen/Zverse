<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedaksiMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'redaksi') {
            if (!session('redaksi_user_id')) {
                return redirect()->route('login')->withErrors(['username' => 'Anda harus login sebagai Redaksi.']);
            }
        }
        return $next($request);
    }
}
