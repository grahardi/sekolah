<?php

use App\Http\Controllers\SuperAdmin\DashboardController;
use Illuminate\Support\Facades\Route;

// Panel super-admin: mengelola SELURUH portal (semua sekolah terdaftar),
// bukan cuma satu sekolah seperti Buku Induk. Dibatasi middleware
// 'super-admin' (cek kolom is_super_admin di tabel users).
Route::middleware(['web', 'auth', 'super-admin'])->prefix('admin-portal')->name('superadmin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/log-aktivitas', [DashboardController::class, 'logAktivitas'])->name('log-aktivitas');
    Route::get('/sekolah/{sekolah}', [DashboardController::class, 'show'])->name('sekolah.show');

    Route::get('/exo', [\App\Http\Controllers\SuperAdmin\ExoInstanceController::class, 'index'])->name('exo.index');
    Route::post('/exo', [\App\Http\Controllers\SuperAdmin\ExoInstanceController::class, 'store'])->name('exo.store');
    Route::put('/exo/{exoInstance}/license-key', [\App\Http\Controllers\SuperAdmin\ExoInstanceController::class, 'updateLicenseKey'])->name('exo.license-key');
    Route::put('/exo/{exoInstance}/db-creds', [\App\Http\Controllers\SuperAdmin\ExoInstanceController::class, 'updateDbCreds'])->name('exo.db-creds');
    Route::post('/exo/{exoInstance}/test-connection', [\App\Http\Controllers\SuperAdmin\ExoInstanceController::class, 'testConnection'])->name('exo.test-connection');
    Route::post('/exo/{exoInstance}/run', [\App\Http\Controllers\SuperAdmin\ExoInstanceController::class, 'run'])->name('exo.run');
    Route::delete('/exo/{exoInstance}', [\App\Http\Controllers\SuperAdmin\ExoInstanceController::class, 'destroy'])->name('exo.destroy');
});
