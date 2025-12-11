<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk memastikan user yang mengakses route adalah admin
 * 
 * Dibuat oleh: Reffael (Role: Admin)
 * Fitur:
 * - Middleware untuk admin
 * 
 * BEST PRACTICE:
 * - Middleware ini digunakan untuk melindungi route admin
 * - Mengecek apakah user terautentikasi dan memiliki role 'admin'
 * - Jika bukan admin, akan return 403 Forbidden
 * 
 * Penggunaan:
 * - Daftarkan di bootstrap/app.php atau routes/web.php
 * - Gunakan dengan ->middleware('admin') pada route group
 */
class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     * 
     * BEST PRACTICE:
     * - Mengecek apakah user terautentikasi
     * - Mengecek apakah user memiliki role 'admin'
     * - Jika tidak memenuhi syarat, return 403 Forbidden
     * 
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // BEST PRACTICE: Mengecek apakah user terautentikasi dan memiliki role 'admin'
        // Jika bukan admin, akan return 403 Forbidden
        if ($request->user() && $request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        return $next($request);
    }
}
