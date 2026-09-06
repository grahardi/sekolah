<?php

use App\Http\Controllers\AlumniController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'not_guru'])->prefix('alumni')->name('alumni.')->group(function () {
    Route::get('/', [AlumniController::class, 'index'])->name('index');
    Route::get('/history', [AlumniController::class, 'historyIndex'])->name('history.index');
    Route::put('/history/{siswa}', [AlumniController::class, 'historyEdit'])->name('history.edit');

    // Arsip berkas per-alumni - HARUS di atas kalau ada wildcard lain,
    // tapi di sini {siswa} cuma dipakai di 1 grup jadi aman.
    Route::get('/{siswa}/arsip', [AlumniController::class, 'arsip'])->name('arsip.show');

    Route::middleware('admin')->group(function () {
        Route::get('/create', [AlumniController::class, 'create'])->name('create');
        Route::post('/', [AlumniController::class, 'store'])->name('store');

        Route::get('/import-dapodik', [AlumniController::class, 'showImportDapodik'])->name('import-dapodik.form');
        Route::post('/import-dapodik', [AlumniController::class, 'importDapodik'])->name('import-dapodik.process');

        Route::get('/import-berkas', [AlumniController::class, 'showImportBerkas'])->name('import-berkas.form');
        Route::post('/import-berkas', [AlumniController::class, 'importBerkas'])->name('import-berkas.process');

        Route::get('/import-nomor-ijazah', [AlumniController::class, 'showImportNomorIjazah'])->name('import-nomor-ijazah.form');
        Route::post('/import-nomor-ijazah', [AlumniController::class, 'importNomorIjazah'])->name('import-nomor-ijazah.process');

        Route::get('/sekolah-tujuan', [AlumniController::class, 'sekolahTujuanIndex'])->name('sekolah-tujuan.index');
        Route::post('/sekolah-tujuan', [AlumniController::class, 'sekolahTujuanStore'])->name('sekolah-tujuan.store');
        Route::put('/sekolah-tujuan/{sekolahTujuan}', [AlumniController::class, 'sekolahTujuanUpdate'])->name('sekolah-tujuan.update');
        Route::delete('/sekolah-tujuan/{sekolahTujuan}', [AlumniController::class, 'sekolahTujuanDestroy'])->name('sekolah-tujuan.destroy');

        Route::get('/ajuan-ulang', [AlumniController::class, 'ajuanUlangIndex'])->name('ajuan-ulang.index');
        Route::post('/ajuan-ulang/{ajuan}/proses', [AlumniController::class, 'ajuanUlangProses'])->name('ajuan-ulang.proses');

        Route::post('/{siswa}/arsip', [AlumniController::class, 'arsipUpdate'])->name('arsip.update');
        Route::post('/{siswa}/arsip/hapus', [AlumniController::class, 'arsipHapus'])->name('arsip.hapus');
    });
});
