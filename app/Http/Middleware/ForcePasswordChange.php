<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    // SEMENTARA DIMATIKAN (masih tahap development) - ada laporan bug
    // soal alur force-ganti-password yg mengganggu. Set balik ke true
    // kalau sudah stabil & siap dipakai lagi.
    private const AKTIF = false;

    /**
     * Kalau akun ini masih pakai password hasil generate (belum pernah
     * diganti sendiri), paksa ke halaman ganti password dulu sebelum bisa
     * akses apapun - kecuali halaman ganti password itu sendiri & logout.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! self::AKTIF) {
            return $next($request);
        }

        $user = $request->user();

        if ($user && $user->is_password_generated && $user->password_plain) {
            $rutePengecualian = ['user.change-password', 'user.change-password.update', 'logout'];
            if (! in_array($request->route()?->getName(), $rutePengecualian)) {
                return redirect()->route('user.change-password')
                    ->with('warning', 'Demi keamanan, kamu wajib ganti password bawaan ini terlebih dahulu sebelum melanjutkan.');
            }
        }

        return $next($request);
    }
}
