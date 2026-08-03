<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdminOrInduk
{
    public function handle(Request $request, Closure $next): Response
    {
        $role = $request->user()?->role;

        if (! in_array($role, ['admin', 'induk'])) {
            abort(403, 'Halaman ini khusus admin sekolah.');
        }

        return $next($request);
    }
}
