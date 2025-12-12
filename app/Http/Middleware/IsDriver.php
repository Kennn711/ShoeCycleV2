<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsDriver
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek 1: Apakah sudah login?
        // Cek 2: Apakah role-nya driver?
        if (Auth::check() && Auth::user()->role === 'driver') {
            return $next($request); // Silakan masuk
        }

        // Jika bukan driver tendang ke halaman terakhir yang dikunjungi
        return back()->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
    }
}
