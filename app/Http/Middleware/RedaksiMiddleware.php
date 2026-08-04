<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware otorisasi untuk area Redaksi.
 *
 * Memastikan user yang mengakses route redaksi sudah login DAN berperan
 * `redaksi`. Jika tidak, diarahkan ke halaman login dengan pesan error.
 */
class RedaksiMiddleware
{
    /**
     * Memproses request masuk.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'redaksi') {
            return redirect()->route('login')->withErrors(['username' => 'Anda harus login sebagai Redaksi.']);
        }

        return $next($request);
    }
}
