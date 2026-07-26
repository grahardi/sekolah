<?php

use App\Http\Controllers\SimulationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Landing page publik. Kalau sudah login, arahkan ke dashboard portal.
Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }

    return Inertia::render('Welcome', [
        'canLogin' => true,
        'canRegister' => true,
    ]);
})->name('welcome');

// Beranda portal setelah login.
Route::middleware(['web', 'auth'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

// Modul Laboratorium Interaktif - salah satu fitur di platform sekolah.co.id,
// tampil sebagai submenu "Lab Interaktif" di dalam PortalLayout.
Route::middleware(['web', 'auth'])->prefix('lab')->name('lab.')->group(function () {
    Route::get('/', [SimulationController::class, 'index'])->name('index');
    Route::get('/{slug}', [SimulationController::class, 'show'])->name('show');
});

// Modul lain didaftarkan dengan pola yang sama, lalu ditambahkan sebagai
// entri baru di MENU pada resources/js/Layouts/PortalLayout.jsx:
// require __DIR__.'/ujian.php';
// require __DIR__.'/buku-induk.php';
// require __DIR__.'/bk.php';
// require __DIR__.'/manajemen.php';

require __DIR__.'/auth.php';
