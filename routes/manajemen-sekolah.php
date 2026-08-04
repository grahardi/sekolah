<?php

use App\Http\Controllers\ManajemenSekolahController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->prefix('manajemen-sekolah')->name('manajemen-sekolah.')->group(function () {
    Route::get('/', [ManajemenSekolahController::class, 'dashboard'])->name('dashboard');
    Route::get('/data-siswa', [ManajemenSekolahController::class, 'dataSiswa'])->name('data-siswa');
    Route::get('/data-guru', [ManajemenSekolahController::class, 'dataGuru'])->name('data-guru');

    Route::get('/absensi', [ManajemenSekolahController::class, 'absensiIndex'])->name('absensi.index');
    Route::post('/absensi', [ManajemenSekolahController::class, 'absensiStore'])->name('absensi.store');
    Route::get('/absensi/rekap', [ManajemenSekolahController::class, 'absensiRekap'])->name('absensi.rekap');
});
