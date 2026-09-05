<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockDemoWrites
{
    public function handle(Request $request, Closure $next): Response
    {
        $sekolah = $request->user()?->sekolah;

        if ($sekolah?->is_demo && ! $request->isMethod('get') && ! $request->isMethod('head')) {
            if ($request->expectsJson() || $request->header('X-Inertia')) {
                return response()->json(['message' => 'Fitur demo sedang ditutup - tidak bisa mengubah data.'], 403);
            }
            return back()->with('error', 'Fitur demo sedang ditutup - tidak bisa mengubah data.');
        }

        return $next($request);
    }
}
