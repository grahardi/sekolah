<?php

use App\Http\Controllers\PengajuanPerubahanController;
use App\Http\Controllers\PengajuanPerubahanPublicController;
use Illuminate\Support\Facades\Route;

// ==== PUBLIK (tanpa login) - diakses siswa/orang tua via NPSN + kode_akses ====
// URL: sekolah.co.id/{npsn}/siswa/{kodeAkses}/pengajuan
Route::middleware('web')->prefix('{npsn}/siswa/{kodeAkses}/pengajuan')->name('pengajuan-publik.')->group(function () {
    Route::get('/', [PengajuanPerubahanPublicController::class, 'verifikasi'])->name('verifikasi');
    Route::post('/', [PengajuanPerubahanPublicController::class, 'prosesVerifikasi'])->name('verifikasi.proses');
    Route::get('/form', [PengajuanPerubahanPublicController::class, 'form'])->name('form');
    Route::post('/form', [PengajuanPerubahanPublicController::class, 'simpan'])->name('simpan');
});

// ==== WALI KELAS / ADMIN (login) ====
Route::middleware(['web', 'auth'])->prefix('pengajuan-perubahan')->name('pengajuan-perubahan.')->group(function () {
    Route::get('/', [PengajuanPerubahanController::class, 'index'])->name('index');
    Route::get('/{siswa}', [PengajuanPerubahanController::class, 'show'])->name('show');
    Route::post('/{siswa}', [PengajuanPerubahanController::class, 'proses'])->name('proses');
    Route::post('/{siswa}/token-baru', [PengajuanPerubahanController::class, 'generateUlangToken'])->name('token-baru');
});
