<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware otorisasi untuk area Pewarta.
 *
 * Memastikan user yang mengakses route pewarta sudah login DAN berperan
 * `pewarta`. Jika tidak, diarahkan ke halaman login dengan pesan error.
 */
class PewartaMiddleware
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
        if (!Auth::check() || Auth::user()->role !== 'pewarta') {
            return redirect()->route('login')->withErrors(['username' => 'Anda harus login sebagai Pewarta.']);
        }

        return $next($request);
    }
}
