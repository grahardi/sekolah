<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    /**
     * Hanya izinkan user dengan role 'admin' melewati route ini.
     * User 'induk' akan ditolak dengan pesan yang jelas.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan tindakan ini. Hubungi admin sekolah.');
        }

        return $next($request);
    }
}
