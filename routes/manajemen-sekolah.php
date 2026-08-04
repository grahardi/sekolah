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
        Route::post('/absensi/{siswa}/tandai', [ManajemenSekolahController::class, 'absensiTandaiSatu'])->name('absensi.tandai-satu');
        Route::get('/absensi/rekap', [ManajemenSekolahController::class, 'absensiRekap'])->name('absensi.rekap');
        Route::get('/absensi/hari-ini', [ManajemenSekolahController::class, 'absensiSiswaHariIni'])->name('absensi.hari-ini');

        Route::get('/tatib', [ManajemenSekolahController::class, 'tatibIndex'])->name('tatib.index');
        Route::post('/tatib', [ManajemenSekolahController::class, 'tatibStore'])->name('tatib.store');
        Route::post('/tatib/notif-wali-kelas', [ManajemenSekolahController::class, 'tatibNotifWaliKelas'])->name('tatib.notif-wali-kelas');
        Route::post('/tatib/ajuan-bk', [ManajemenSekolahController::class, 'tatibAjuanBk'])->name('tatib.ajuan-bk');
        Route::put('/tatib/{pelanggaran}/tindak-lanjut', [ManajemenSekolahController::class, 'tatibTindakLanjut'])->name('tatib.tindak-lanjut');
        Route::delete('/tatib/{pelanggaran}', [ManajemenSekolahController::class, 'tatibDestroy'])->name('tatib.destroy');

        Route::get('/keterlambatan', [ManajemenSekolahController::class, 'keterlambatanIndex'])->name('keterlambatan.index');
        Route::post('/keterlambatan/{siswa}', [ManajemenSekolahController::class, 'keterlambatanTandai'])->name('keterlambatan.tandai');
        Route::get('/keterlambatan-list', [ManajemenSekolahController::class, 'keterlambatanList'])->name('keterlambatan.list');

        Route::get('/arsip-surat', [ManajemenSekolahController::class, 'arsipSuratIndex'])->name('arsip-surat.index');

        Route::get('/absensi-guru', [ManajemenSekolahController::class, 'absensiGuruIndex'])->name('absensi-guru.index');
        Route::post('/absensi-guru/{guru}', [ManajemenSekolahController::class, 'absensiGuruStore'])->name('absensi-guru.store');
        Route::get('/absensi-guru-rekap', [ManajemenSekolahController::class, 'absensiGuruRekap'])->name('absensi-guru.rekap');
        Route::get('/absensi-guru-hari-ini', [ManajemenSekolahController::class, 'absensiGuruHariIni'])->name('absensi-guru.hari-ini');
    });
});
