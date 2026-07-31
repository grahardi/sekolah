<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // Sengaja TIDAK pakai redirect()->intended(...) - kalau user tadinya
        // mencoba akses halaman Blade (mis. /buku-induk) sebelum diminta
        // login, "intended URL" akan menunjuk ke situ. Karena form login ini
        // POST lewat Inertia, redirect ke halaman Blade penuh bikin transisi
        // Inertia nyangkut (kelihatan seperti modal/popup sampai di-refresh).
        // Makanya selalu arahkan ke /dashboard (halaman Inertia) dulu -
        // user tinggal klik menu Buku Induk sendiri dari sana.
        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
