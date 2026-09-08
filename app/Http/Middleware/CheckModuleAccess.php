<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccess
{
    public function handle(Request $request, Closure $next, string $modulKey): Response
    {
        $user = $request->user();

        if (! $user || $user->isAdmin() || ! $user->custom_role_id) {
            return $next($request);
        }

        if (! $user->bolehAksesModul($modulKey)) {
            abort(403, 'Anda tidak memiliki akses ke modul ini. Hubungi admin sekolah kalau ini keliru.');
        }

        if ($user->modulReadOnly($modulKey) && ! $request->isMethod('get') && ! $request->isMethod('head')) {
            if ($request->expectsJson() || $request->header('X-Inertia')) {
                return response()->json(['message' => 'Akses Anda ke modul ini baca-saja, tidak bisa mengubah data.'], 403);
            }
            return back()->with('error', 'Akses Anda ke modul ini baca-saja, tidak bisa mengubah data.');
        }

        return $next($request);
    }
}
