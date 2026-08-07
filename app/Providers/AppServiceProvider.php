<?php

namespace App\Providers;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Vite;
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
