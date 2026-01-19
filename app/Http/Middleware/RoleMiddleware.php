<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // 1. Cek apakah user login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. Cek apakah role user sesuai dengan yang diminta (misal 'admin')
        if (Auth::user()->role !== $role) {
            // Jika bukan admin, tendang ke halaman home user biasa atau 403 Forbidden
            return redirect('/wallet')->with('error', 'Anda tidak memiliki akses admin.');
        }

        return $next($request);
    }
}