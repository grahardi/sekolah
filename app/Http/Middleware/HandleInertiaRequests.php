<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            // Dipakai buat banner "Sedang login sebagai Guru X - Kembali ke Admin"
            // di PortalLayout, kalau admin lagi impersonate akun guru.
            'impersonating' => session('impersonating_admin_id') ? true : false,
        ];
    }
}
