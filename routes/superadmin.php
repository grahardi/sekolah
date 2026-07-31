<?php

use App\Http\Controllers\SuperAdmin\DashboardController;
use Illuminate\Support\Facades\Route;

// Panel super-admin: mengelola SELURUH portal (semua sekolah terdaftar),
// bukan cuma satu sekolah seperti Buku Induk. Dibatasi middleware
// 'super-admin' (cek kolom is_super_admin di tabel users).
Route::middleware(['web', 'auth', 'super-admin'])->prefix('admin-portal')->name('superadmin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/sekolah/{sekolah}', [DashboardController::class, 'show'])->name('sekolah.show');
});
