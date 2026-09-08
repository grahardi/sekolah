<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Manajemen User terpusat - satu tempat untuk seluruh sekolah, dipakai
// bersama oleh Buku Induk maupun Kepegawaian (bukan lagi digandakan per
// modul). Hanya admin sekolah yang bisa akses (middleware 'admin').
Route::middleware(['web', 'auth', 'admin'])->prefix('pengguna')->name('user.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/kartu-login', [UserController::class, 'kartuLogin'])->name('kartu-login');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
    Route::post('/{user}/login-as', [\App\Http\Controllers\LoginAsController::class, 'login'])->name('login-as');
});

Route::middleware(['web', 'auth', 'admin'])->prefix('pengguna/role')->name('role.')->group(function () {
    Route::get('/', [\App\Http\Controllers\RoleController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\RoleController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\RoleController::class, 'store'])->name('store');
    Route::get('/{role}/edit', [\App\Http\Controllers\RoleController::class, 'edit'])->name('edit');
    Route::put('/{role}', [\App\Http\Controllers\RoleController::class, 'update'])->name('update');
    Route::delete('/{role}', [\App\Http\Controllers\RoleController::class, 'destroy'])->name('destroy');
});
