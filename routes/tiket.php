<?php

use App\Http\Controllers\TiketDukunganController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'admin'])->prefix('tiket')->name('tiket.')->group(function () {
    Route::get('/', [TiketDukunganController::class, 'index'])->name('index');
    Route::get('/buat', [TiketDukunganController::class, 'create'])->name('create');
    Route::post('/', [TiketDukunganController::class, 'store'])->name('store');
    Route::get('/{tiket}', [TiketDukunganController::class, 'show'])->name('show');
    Route::post('/{tiket}/balas', [TiketDukunganController::class, 'balas'])->name('balas');
});
