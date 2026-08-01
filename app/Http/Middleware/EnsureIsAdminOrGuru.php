<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdminOrGuru
{
    public function handle(Request $request, Closure $next): Response
    {
        $role = $request->user()?->role;

        if (! in_array($role, ['admin', 'guru'])) {
            abort(403, 'Halaman ini khusus untuk admin sekolah atau guru.');
        }

        return $next($request);
    }
}
