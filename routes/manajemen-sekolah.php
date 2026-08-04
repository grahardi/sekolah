<?php

use App\Http\Controllers\ManajemenSekolahController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->prefix('manajemen-sekolah')->name('manajemen-sekolah.')->group(function () {
    // Login khusus Manajemen Sekolah - pakai akun yg sama (email/password),
    // cuma tampilannya dibranding sendiri terpisah dari login utama sekolah.co.id
    Route::get('/login', [ManajemenSekolahController::class, 'showLogin'])->name('login');
    Route::post('/login', [ManajemenSekolahController::class, 'login'])->name('login.submit');
    Route::post('/logout', [ManajemenSekolahController::class, 'logout'])->name('logout');

    Route::middleware(['web', 'auth'])->group(function () {
        Route::get('/', [ManajemenSekolahController::class, 'dashboard'])->name('dashboard');
        Route::get('/menu-piket', [ManajemenSekolahController::class, 'menuPiket'])->name('menu-piket');
        Route::get('/data-siswa', [ManajemenSekolahController::class, 'dataSiswa'])->name('data-siswa');
        Route::get('/data-guru', [ManajemenSekolahController::class, 'dataGuru'])->name('data-guru');
        Route::put('/data-guru/{guru}/role', [ManajemenSekolahController::class, 'updateRoleGuru'])->name('data-guru.update-role');

        Route::get('/absensi', [ManajemenSekolahController::class, 'absensiIndex'])->name('absensi.index');
        Route::post('/absensi', [ManajemenSekolahController::class, 'absensiStore'])->name('absensi.store');
        Route::get('/absensi/rekap', [ManajemenSekolahController::class, 'absensiRekap'])->name('absensi.rekap');
    });
});
