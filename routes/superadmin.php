<?php

use App\Http\Controllers\SuperAdmin\DashboardController;
use Illuminate\Support\Facades\Route;

// Panel super-admin: mengelola SELURUH portal (semua sekolah terdaftar),
// bukan cuma satu sekolah seperti Buku Induk. Dibatasi middleware
// 'super-admin' (cek kolom is_super_admin di tabel users).
Route::middleware(['web', 'auth', 'super-admin'])->prefix('admin-portal')->name('superadmin.')->group(function () {

    Route::get('/tiket', [\App\Http\Controllers\SuperAdmin\TiketDukunganController::class, 'index'])->name('tiket.index');
    Route::get('/tiket/{tiket}', [\App\Http\Controllers\SuperAdmin\TiketDukunganController::class, 'show'])->name('tiket.show');
    Route::post('/tiket/{tiket}/balas', [\App\Http\Controllers\SuperAdmin\TiketDukunganController::class, 'balas'])->name('tiket.balas');

    Route::get('/showcase', [\App\Http\Controllers\SuperAdmin\ShowcaseController::class, 'index'])->name('showcase.index');
    Route::post('/showcase', [\App\Http\Controllers\SuperAdmin\ShowcaseController::class, 'store'])->name('showcase.store');
    Route::post('/showcase/{showcase}', [\App\Http\Controllers\SuperAdmin\ShowcaseController::class, 'update'])->name('showcase.update');
    Route::delete('/showcase/{showcase}', [\App\Http\Controllers\SuperAdmin\ShowcaseController::class, 'destroy'])->name('showcase.destroy');

    Route::get('/edit-pages', [\App\Http\Controllers\SuperAdmin\EditPagesController::class, 'index'])->name('edit-pages.index');
    Route::get('/edit-pages/{page}', [\App\Http\Controllers\SuperAdmin\EditPagesController::class, 'edit'])->name('edit-pages.edit');
    Route::put('/edit-pages/{page}', [\App\Http\Controllers\SuperAdmin\EditPagesController::class, 'update'])->name('edit-pages.update');

    Route::get('/modul-ajar', [\App\Http\Controllers\SuperAdmin\ModulAjarController::class, 'index'])->name('modul-ajar.index');
    Route::get('/modul-ajar/create', [\App\Http\Controllers\SuperAdmin\ModulAjarController::class, 'create'])->name('modul-ajar.create');
    Route::get('/modul-ajar/{modul}/edit', [\App\Http\Controllers\SuperAdmin\ModulAjarController::class, 'edit'])->name('modul-ajar.edit');
    Route::post('/modul-ajar', [\App\Http\Controllers\SuperAdmin\ModulAjarController::class, 'store'])->name('modul-ajar.store');
    Route::post('/modul-ajar/{modul}', [\App\Http\Controllers\SuperAdmin\ModulAjarController::class, 'update'])->name('modul-ajar.update');
    Route::delete('/modul-ajar/{modul}', [\App\Http\Controllers\SuperAdmin\ModulAjarController::class, 'destroy'])->name('modul-ajar.destroy');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/log-aktivitas', [DashboardController::class, 'logAktivitas'])->name('log-aktivitas');
    Route::get('/sekolah/{sekolah}', [DashboardController::class, 'show'])->name('sekolah.show');

    Route::get('/exo', [\App\Http\Controllers\SuperAdmin\ExoInstanceController::class, 'index'])->name('exo.index');
    Route::post('/exo/master-sql', [\App\Http\Controllers\SuperAdmin\ExoInstanceController::class, 'uploadMasterSql'])->name('exo.master-sql');
    Route::post('/exo', [\App\Http\Controllers\SuperAdmin\ExoInstanceController::class, 'store'])->name('exo.store');
    Route::put('/exo/{exoInstance}/license-key', [\App\Http\Controllers\SuperAdmin\ExoInstanceController::class, 'updateLicenseKey'])->name('exo.license-key');
    Route::put('/exo/{exoInstance}/db-creds', [\App\Http\Controllers\SuperAdmin\ExoInstanceController::class, 'updateDbCreds'])->name('exo.db-creds');
    Route::post('/exo/{exoInstance}/test-connection', [\App\Http\Controllers\SuperAdmin\ExoInstanceController::class, 'testConnection'])->name('exo.test-connection');
    Route::post('/exo/{exoInstance}/run', [\App\Http\Controllers\SuperAdmin\ExoInstanceController::class, 'run'])->name('exo.run');
    Route::post('/exo/{exoInstance}/stop', [\App\Http\Controllers\SuperAdmin\ExoInstanceController::class, 'stop'])->name('exo.stop');
    Route::put('/exo/{exoInstance}/sekolah', [\App\Http\Controllers\SuperAdmin\ExoInstanceController::class, 'hubungkanSekolah'])->name('exo.sekolah');
    Route::post('/exo/{exoInstance}/sinkron-siswa', [\App\Http\Controllers\SuperAdmin\ExoInstanceController::class, 'sinkronSiswa'])->name('exo.sinkron-siswa');
    Route::delete('/exo/{exoInstance}', [\App\Http\Controllers\SuperAdmin\ExoInstanceController::class, 'destroy'])->name('exo.destroy');
});
