<?php

use App\Http\Controllers\PegawaiController;
use Illuminate\Support\Facades\Route;

// Modul Kepegawaian. Sama seperti Buku Induk: login pakai Breeze yang sudah
// ada, bukan sistem terpisah. Multi-sekolah otomatis lewat global scope di
// model Pegawai (pola sama persis dengan Siswa).
Route::middleware(['web', 'auth'])->prefix('kepegawaian')->name('pegawai.')->group(function () {
    Route::get('/', [PegawaiController::class, 'index'])->name('index');

    Route::middleware('admin')->group(function () {
        Route::get('/create', [PegawaiController::class, 'create'])->name('create');
        Route::post('/', [PegawaiController::class, 'store'])->name('store');
        Route::get('/{pegawai}/edit', [PegawaiController::class, 'edit'])->name('edit');
        Route::put('/{pegawai}', [PegawaiController::class, 'update'])->name('update');
        Route::delete('/{pegawai}', [PegawaiController::class, 'destroy'])->name('destroy');
    });
});
