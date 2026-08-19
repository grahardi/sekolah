<?php

use App\Http\Controllers\PengajuanPerubahanController;
use App\Http\Controllers\PengajuanPerubahanPublicController;
use Illuminate\Support\Facades\Route;

// ==== PUBLIK (tanpa login) - 1 URL utk seluruh sekolah, siswa diverifikasi
// via No.Induk + Tgl Lahir + Token (token per KELAS, bukan per siswa) ====
// URL: sekolah.co.id/{npsn}/pengajuan
Route::middleware('web')->prefix('{npsn}/pengajuan')->name('pengajuan-publik.')->group(function () {
    Route::get('/', [PengajuanPerubahanPublicController::class, 'verifikasi'])->name('verifikasi');
    Route::post('/', [PengajuanPerubahanPublicController::class, 'prosesVerifikasi'])->name('verifikasi.proses');
    Route::get('/form', [PengajuanPerubahanPublicController::class, 'form'])->name('form');
    Route::post('/form', [PengajuanPerubahanPublicController::class, 'simpan'])->name('simpan');
    Route::post('/keluar', [PengajuanPerubahanPublicController::class, 'keluar'])->name('keluar');
});

Route::middleware(['web', 'auth', 'admin'])->prefix('pengajuan-perubahan')->group(function () {
    Route::get('/manajemen-token', [PengajuanPerubahanController::class, 'manajemenToken'])->name('pengajuan-perubahan.manajemen-token');
    Route::post('/manajemen-token/{waliKelas}/token-baru', [PengajuanPerubahanController::class, 'generateUlangTokenAdmin'])->name('pengajuan-perubahan.manajemen-token.baru');
});

// ==== WALI KELAS / ADMIN (login) ====
Route::middleware(['web', 'auth'])->prefix('pengajuan-perubahan')->name('pengajuan-perubahan.')->group(function () {
    Route::get('/', [PengajuanPerubahanController::class, 'index'])->name('index');
    Route::get('/{siswa}', [PengajuanPerubahanController::class, 'show'])->name('show');
    Route::post('/{siswa}', [PengajuanPerubahanController::class, 'proses'])->name('proses');
    Route::post('/token-baru', [PengajuanPerubahanController::class, 'generateUlangToken'])->name('token-baru');
});
