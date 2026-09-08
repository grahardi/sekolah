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
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? array_merge(
                    $user->toArray(),
                    ['is_demo_sekolah' => $user->sekolah?->is_demo ?? false]
                ) : null,
            ],
            // Kalau user punya custom_role, kirim map modul yg boleh diakses
            // + status read-only-nya, dipakai PortalLayout buat sembunyikan
            // menu yg gak diizinkan & tampilkan indikator read-only.
            'customRolePermissions' => ($user && $user->custom_role_id && ! $user->isAdmin())
                ? $user->customRole?->permissionMap()
                : null,
            // Dipakai buat banner "Sedang login sebagai Guru X - Kembali ke Admin"
            // di PortalLayout, kalau admin lagi impersonate akun guru.
            'impersonating' => session('impersonating_admin_id') ? true : false,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
