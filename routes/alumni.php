<?php

use App\Http\Controllers\AlumniController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'not_guru'])->prefix('alumni')->name('alumni.')->group(function () {
    Route::get('/', [AlumniController::class, 'index'])->name('index');

    Route::middleware('admin')->group(function () {
        // /create & /import-dapodik HARUS di atas /{siswa} kalau ada wildcard,
        // tapi di sini kita gak punya show/{siswa} route jadi aman urutan apapun.
        Route::get('/create', [AlumniController::class, 'create'])->name('create');
        Route::post('/', [AlumniController::class, 'store'])->name('store');
        Route::get('/import-dapodik', [AlumniController::class, 'showImportDapodik'])->name('import-dapodik.form');
        Route::post('/import-dapodik', [AlumniController::class, 'importDapodik'])->name('import-dapodik.process');
    });
});
