<?php

use App\Http\Controllers\AlumniPublicController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->prefix('{npsn}/alumni')->name('alumni-publik.')->group(function () {
    Route::get('/', [AlumniPublicController::class, 'verifikasi'])->name('verifikasi');
    Route::post('/', [AlumniPublicController::class, 'prosesVerifikasi'])->name('verifikasi.proses');
    Route::get('/form', [AlumniPublicController::class, 'form'])->name('form');
    Route::post('/form', [AlumniPublicController::class, 'simpan'])->name('simpan');
    Route::get('/ajuan-ulang', [AlumniPublicController::class, 'ajukanUlangForm'])->name('ajuan-ulang.form');
    Route::post('/ajuan-ulang', [AlumniPublicController::class, 'ajukanUlangSimpan'])->name('ajuan-ulang.simpan');
    Route::post('/keluar', [AlumniPublicController::class, 'keluar'])->name('keluar');
});
