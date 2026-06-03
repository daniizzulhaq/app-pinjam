<?php

namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
 
class IsKaryawan
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isKaryawan()) {
            abort(403, 'Akses ditolak.');
        }
 
        // Cek apakah akun aktif
        if (!auth()->user()->is_active) {
            auth()->logout();
            return redirect()->route('login')
                             ->with('error', 'Akun Anda telah dinonaktifkan. Hubungi admin.');
        }
 
        return $next($request);
    }
}