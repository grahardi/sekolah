<?php

use App\Http\Controllers\ArsipController;
use App\Http\Controllers\KenaikanKelasController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\PrestasiController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Modul Buku Induk Siswa. Login/logout PAKAI Breeze yang sudah ada di portal
// (bukan AuthController milik modul ini) supaya satu akun berlaku untuk
// seluruh sekolah.co.id, bukan sistem login terpisah.
Route::middleware(['web', 'auth'])->prefix('buku-induk')->group(function () {

    Route::get('/', fn () => redirect()->route('siswa.index'));

    Route::prefix('siswa')->name('siswa.')->group(function () {
        // Bisa diakses admin & induk (VIEW ONLY)
        Route::get('/', [SiswaController::class, 'index'])->name('index');
        Route::get('/{siswa}', [SiswaController::class, 'show'])->name('show');
        Route::get('/{siswa}/buku-induk-pdf', [SiswaController::class, 'cetakBukuInduk'])->name('buku-induk.pdf');
        Route::get('/{siswa}/kartu-pdf', [SiswaController::class, 'cetakKartu'])->name('kartu.pdf');
        Route::get('/{siswa}/arsip', [ArsipController::class, 'show'])->name('arsip.show');
        Route::get('/{siswa}/prestasi', [PrestasiController::class, 'index'])->name('prestasi.index');
        Route::get('/{siswa}/nilai', [NilaiController::class, 'index'])->name('nilai.index');

        // HANYA ADMIN (tambah / edit / hapus / import / export)
        Route::middleware('admin')->group(function () {
            Route::get('/create', [SiswaController::class, 'create'])->name('create');
            Route::post('/', [SiswaController::class, 'store'])->name('store');
            Route::get('/{siswa}/edit', [SiswaController::class, 'edit'])->name('edit');
            Route::put('/{siswa}', [SiswaController::class, 'update'])->name('update');
            Route::delete('/{siswa}', [SiswaController::class, 'destroy'])->name('destroy');

            Route::get('/export/excel', [SiswaController::class, 'exportExcel'])->name('export.excel');
            Route::get('/export/pdf', [SiswaController::class, 'exportPdfAll'])->name('export.pdf');

            Route::get('/import/form', [SiswaController::class, 'showImport'])->name('import.form');
            Route::post('/import/process', [SiswaController::class, 'import'])->name('import.process');
            Route::post('/import/dapodik', [SiswaController::class, 'importDapodik'])->name('import.dapodik');
            Route::get('/import/template', [SiswaController::class, 'downloadTemplate'])->name('import.template');

            Route::post('/{siswa}/arsip', [ArsipController::class, 'update'])->name('arsip.update');
            Route::post('/{siswa}/arsip/hapus', [ArsipController::class, 'hapusBerkas'])->name('arsip.hapus');

            Route::post('/{siswa}/prestasi', [PrestasiController::class, 'store'])->name('prestasi.store');
            Route::delete('/{siswa}/prestasi/{prestasi}', [PrestasiController::class, 'destroy'])->name('prestasi.destroy');
            Route::post('/{siswa}/prestasi/{prestasi}/sertifikat', [PrestasiController::class, 'updateSertifikat'])->name('prestasi.sertifikat');

            Route::prefix('/{siswa}/nilai')->name('nilai.')->group(function () {
                Route::post('/rapor', [NilaiController::class, 'storeRapor'])->name('rapor.store');
                Route::delete('/rapor/{nilaiRapor}', [NilaiController::class, 'destroyRapor'])->name('rapor.destroy');
                Route::post('/p5', [NilaiController::class, 'storeP5'])->name('p5.store');
                Route::delete('/p5/{nilaiP5}', [NilaiController::class, 'destroyP5'])->name('p5.destroy');
                Route::post('/ekskul', [NilaiController::class, 'storeEkskul'])->name('ekskul.store');
                Route::delete('/ekskul/{nilaiEkskul}', [NilaiController::class, 'destroyEkskul'])->name('ekskul.destroy');
                Route::post('/kehadiran', [NilaiController::class, 'storeKehadiran'])->name('kehadiran.store');
                Route::post('/riwayat', [NilaiController::class, 'storeRiwayat'])->name('riwayat.store');
            });
        });
    });

    // HANYA ADMIN
    Route::middleware('admin')->group(function () {
        Route::prefix('nilai-massal')->name('nilai.')->group(function () {
            Route::get('/', [NilaiController::class, 'showImportMassal'])->name('import-massal');
            Route::post('/import', [NilaiController::class, 'importMassal'])->name('import-massal.process');
            Route::get('/template', [NilaiController::class, 'templateMassal'])->name('import-massal.template');
        });

        Route::prefix('kenaikan')->name('kenaikan.')->group(function () {
            Route::get('/', [KenaikanKelasController::class, 'index'])->name('index');
            Route::get('/preview', [KenaikanKelasController::class, 'preview'])->name('preview');
            Route::post('/proses', [KenaikanKelasController::class, 'proses'])->name('proses');
        });

        // Manajemen User internal Buku Induk (peran admin/induk) - beda dari
        // pengguna umum portal, khusus untuk siapa yang boleh akses modul ini.
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        });
    });

    // Ganti Password Sendiri (semua role yang sudah login)
    Route::get('/ganti-password', [UserController::class, 'showChangePassword'])->name('user.change-password');
    Route::post('/ganti-password', [UserController::class, 'changePassword'])->name('user.change-password.update');
});
