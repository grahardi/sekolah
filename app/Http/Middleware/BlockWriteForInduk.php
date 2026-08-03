<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockWriteForInduk
{
    /**
     * Role 'induk' (mis. akun demo read-only) boleh LIHAT semua halaman
     * seperti admin, tapi setiap request yg mengubah data (bukan GET/HEAD)
     * otomatis ditolak - jadi tidak perlu sembunyikan tombol satu-satu di
     * tiap view, cukup dijaga di sini.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role === 'induk' && ! $request->isMethod('get') && ! $request->isMethod('head')) {
            abort(403, 'Akun ini read-only (demo) - tidak bisa mengubah data.');
        }

        return $next($request);
    }
}
