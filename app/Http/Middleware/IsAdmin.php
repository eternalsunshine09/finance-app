<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek 1: Apakah sudah login?
        // Cek 2: Apakah role-nya admin?
        if (Auth::check() && Auth::user()->role == 'admin') {
            return $next($request); // Silakan lewat
        }

        // Kalau bukan admin, tendang ke dashboard biasa dengan pesan error
        return redirect('/dashboard')->with('error', 'Akses Ditolak! Kamu bukan Admin.');
    }
}