<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsNotGuru
{
    /**
     * Akun guru (role 'guru') untuk sementara CUMA boleh akses modul E-Rapor.
     * Kepegawaian & Buku Induk masih ditutup sampai ada penyesuaian lebih
     * lanjut. Role lain (admin/induk) tidak terpengaruh sama sekali.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role === 'guru') {
            abort(403, 'Akun guru untuk sementara cuma bisa akses modul E-Rapor.');
        }

        return $next($request);
    }
}
