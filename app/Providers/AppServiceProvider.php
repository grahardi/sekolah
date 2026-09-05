<?php

namespace App\Providers;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Paginator::defaultView('vendor.pagination.custom');

        // FIX BUG LAMA & FATAL: $isDemoReadonly dipakai di BANYAK Blade view
        // (@elseif($isDemoReadonly)) tapi SEBELUMNYA gak pernah didefinisikan
        // di manapun - selalu undefined/null/false, jadi branch itu gak
        // pernah kepakai. Akun demo (role=admin) otomatis lolos ke branch
        // admin PENUH tanpa batasan (bisa tambah/edit/hapus/import/export).
        // Pakai View::composer (bukan View::share+closure, closure selalu
        // truthy) biar dievaluasi pas render, auth state udah pasti siap.
        View::composer('*', function ($view) {
            $view->with('isDemoReadonly', auth()->check() && (auth()->user()->sekolah?->is_demo ?? false));
        });

        // Log Aktivitas: rekam login/logout/registrasi otomatis lewat event
        // bawaan Laravel (dipicu Auth::attempt/logout/Registered di Breeze
        // maupun controller custom kita).
        $catat = function (string $event, $user) {
            ActivityLog::create([
                'user_id' => $user?->id,
                'sekolah_id' => $user?->sekolah_id,
                'nama_snapshot' => $user?->name,
                'email_snapshot' => $user?->email,
                'event' => $event,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ]);
        };

        Event::listen(Login::class, fn (Login $e) => $catat('login', $e->user));
        Event::listen(Logout::class, fn (Logout $e) => $catat('logout', $e->user));
        Event::listen(Registered::class, fn (Registered $e) => $catat('registrasi', $e->user));
    }
}
